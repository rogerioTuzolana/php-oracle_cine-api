<?php

namespace App\Controllers;

use App\Utils\Render;
use App\Model\VerifyAuth;
use App\Model\ClassCourse;
use App\Model\ClassTeaching;
use App\Model\ClassStudent;
use App\Model\ClassCourseStudent;
use App\Model\ClassCertification;
use App\Model\ClassTest;
use App\Session\SessionLogin;

class StudentController extends Render{
    private $course;
    private $teaching;
    private $student;
    private $certification;
    private $test;
    public function __construct(){
        $this->course=new ClassCourse;
        $this->teaching=new ClassTeaching;
        $this->student=new ClassStudent;
        $this->course_student=new ClassCourseStudent;
        $this->certification=new ClassCertification;
        $this->test=new ClassTest;
        $verify=new VerifyAuth;
        $verify->verify_student();
    }

    public function courseOnlineLogout(){
        //destroi sessao de login
        SessionLogin::session_student_logout();
        header('Location:'.DIRPAGE.'/');
    }

    public function watchCourses($user_id,$course_id){
        //var_dump($_SESSION['course']['student']["id"]);
        if($_SESSION['course']['student']["id"]!=$user_id){
            $this->view("/courseLayout","/courseonline/errorAccessVid",[
                "color"=>true,
                "access_failed"=>"Acesso Negado"
            ]);
        }
        $title = "AmaroGonçalves Aula";
        //Retorna a pagina padrao(Layout)

        $course=$this->course->getCourses();
        $teachings_course=$this->teaching->getTeachingsCourse($course_id);
        $teaching=$this->teaching->getTeachingByCourseId(null,$course_id);
        
        $course_selected=$this->course->getCourseById($course_id);
        $this->view("/courseLayout","/courseonline/watch",[
            "title"=>$title,
            "courses"=>$course, 
            "course_selected"=>$course_selected, 
            "teachings_course"=>$teachings_course,
            "teaching"=>$teaching,
            "color"=>true
        ]);
    }

    public function ajax($e){
        return ["val"=>"deu"];
    }

    public function watchTeachingCourse($user_id, $course_id, $teaching_id){
        if($_SESSION['course']['student']["id"]!=$user_id){
            $this->view("/courseLayout","/courseonline/errorAccessVid",[
                "color"=>true,
                "access_failed"=>"Acesso Negado"
            ]);
        }
        $title = "AmaroGonçalves Aula";
        //Retorna a pagina padrao(Layout)
        $regist['video_id'] = $teaching_id;
        $regist['course_id'] = $course_id;
        $student_teaching = $this->student->getStudentTeachingVid($regist);
        $first_teaching = $this->teaching->getFirstTeaching();

        if (isset($student_teaching) && $student_teaching[0]["status"] != 1 && $first_teaching[0]["id"] != $teaching_id) {   
            
            $this->view("/courseLayout","/courseonline/errorAccessVid",[
                "color"=>true,
                "access_failed"=>"Acesso Negado"
            ]);
        }else{
            $course=$this->course->getCourses();
            $teachings_course=$this->teaching->getTeachingsCourse($course_id);
            $teaching=$this->teaching->getTeachingByCourseId($teaching_id,$course_id);
            
            $course_selected=$this->course->getCourseById($course_id);
            $this->view("/courseLayout","/courseonline/watch",[
                "title"=>$title,
                "courses"=>$course, 
                "course_selected"=>$course_selected, 
                "teachings_course"=>$teachings_course,
                "teaching"=>$teaching,
                "color"=>true
            ]);
        }
    }

    public function myCertification(){
        $title = "AmaroGonçalves Mais";
        $certification=$this->certification->getCertification();
        $courses=$this->course->getCourses();
        foreach ($courses as $key => $course) {
            $course_title[$course["reference"]]=$course["title"];
        }
        /*foreach ($this->certification->getCertification() as $key => $certif) {
            $certif[$certif["id"]]=$certif["bi"];
            $students[$student["bi"]]=$student["name"];
        }*/
        $this->view("/courseLayout","/courseonline/myCertification",["title"=>$title, "certification"=>$certification,"course_title"=>$course_title, "color"=>true, "courses"=>$courses]);
        
    }

    public function sendTestVideoCourse(){
        $title = "AmaroGonçalves Teste enviado";
        $quizcheck = $_POST['quizcheck']??NULL;
        $route = $_POST['route']??NULL;
        $regist['video_id'] = $_POST['video_id']??NULL;
        $regist['course_id'] = $_POST['course_id']??NULL;
        $regist['percent'] = 0;
        $route_test = "teste-vidio/".$regist['course_id']."/".$regist['video_id'];
        $regist['student_id'] = filter_input(INPUT_POST,'student_id',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['status'] = NULL;

        $percent_final = 0;
        $quantity_answer_right = 0;
        $quantity_question = 0;

        $count_question = filter_input(INPUT_POST,'count_question',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        if($quizcheck != NULL){
            $quantity_question = count($quizcheck);
            $quantity_answer_right = 0;
            $keys = array_keys($quizcheck);
 
            foreach ($keys as $k => $key) {
                $quiz = $this->test->getTestById($key);

                if ($quiz[0]["answer_id"]==$quizcheck[$key]) {
                    $quantity_answer_right++;
                }
            }
            $percent_final = ($quantity_answer_right * 100) / $quantity_question;
            if (round($percent_final) >= 50) {
                
                $regist['percent'] = $percent_final;
                $regist['status'] = 1;
                
                $video_watched = $this->student->getStudentTeachingVideoCourse($regist);

                if (isset($video_watched)) {
                    $this->student->updateStatusAtiveVid($regist);
                }else{
                    $this->student->createStudentTeaching($regist);
                }

            }
            
            echo "<br/>Percentagem final: ".$percent_final."%";
        }
        //var_dump();
        $this->view("/courseLayout","/courseonline/testChecked",[
            "title"=>$title, 
            "route"=>$route, 
            "route_test"=>$route_test, 
            "color"=>true, 
            "percent"=>$percent_final,
            "answer_right"=>$quantity_answer_right,
            "answer_failed"=>$quantity_question-$quantity_answer_right,
            "count_question"=>$count_question
        ]);
    }
    
    public function testVideoCourse($course_id,$video_id){
        $title = "AmaroGonçalves Teste";
        /*$courses=$this->course->getCourses();
        $types=$this->test->getTypes();
        //$answer_question = $this->test->getAnswerQuestion($id);
        $answer_question = $this->test->getAnswerQuestionTest($video_id);
        $count_answer = count($answer_question);
        
        $test=$this->test->getTestById($video_id);*/
        $tests=$this->test->getTestByVideoId($video_id);
        //$answer_question = $this->test->getAnswerQuestionTest($video_id);
        $answerQuestions=[];
        foreach ($this->test->getAnswerQuestions() as $key => $answerQuestion) {
            $answerQuestions[$answerQuestion["id_quiz"]][]=$answerQuestion;
        }
        //var_dump($answerQuestions[9]);
        //return;
        //Retorna a pagina padrao(Layout)

        $this->view("/courseLayout","/courseonline/test",["title"=>$title,"tests"=>$tests, "answerQuestions"=>$answerQuestions, "color"=>true]);
    }

    public function myCourses(){
        $title = "AmaroGonçalves Meus Cursos";

        $course=$this->course->getCourses();
        $course_students=[];
        foreach ($this->course_student->getCourseStudents() as $key => $course_student) {
            $course_students[$course_student["course_id"]][$course_student["student_id"]]=$course_student["validation"];
        }

        $this->view("/courseLayout","/courseonline/coursesPayed",["title"=>$title,"courses"=>$course, "course_students"=>$course_students, "color"=>true]);
    }

    public function courseOnline(){
        $title = "AmaroGonçalves Cursos Online";
        //Retorna a pagina padrao(Layout)
        $color = true;
        //$courses=$this->course->getCourses();
        $courses=[];
        $student_id = $_SESSION['course']['student']["id"];
        $course_student = $this->course_student->getCourseByStudent($student_id);
        
        foreach ($this->course->getCourses() as $key => $course) {
            /*array_push($course, false);
            var_dump($course[0]);
            return;*/
            if($this->course_student->existStudentInCourseActive($student_id, $course["id"])==false){
                array_push($course, false);
                $courses[] =  $course;
            }else{
                array_push($course, true);
                $courses[] =  $course;
            }
        }
        //var_dump($courses);
        //return;
        $this->view("/courseLayout","/courseonline/coursesOnline",["title"=>$title,"courses"=>$courses,"course_student"=>$course_student,"color"=>$color]);
    }

    public function confirmAccess(){

        $key_access = filter_input(INPUT_POST,'key_access',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $course_id = filter_input(INPUT_POST,'course_id',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;

        $access = $this->course_student->getPermissionAccess($key_access);
        if ($access == true) {
            header('Location:'.DIRPAGE."/meus-cursos/{$course_id}"); 
        }
        $courses=$this->course->getCourses();
        $title = "AmaroGonçalves Meus Cursos";
        echo "<div class='bg-danger' style='height: 60px;'><h5 class='text-light' style='text-align: center;padding-top: 2px;padding-bottom: 2px'>Chave inválida!</h5></div>";
        $this->view("/courseLayout","/courseonline/coursesPayed",["title"=>$title,"courses"=>$courses, "color"=>false]);
    
    }


    public function registerStudent(){
        $title = "AmaroGonçalves Registar Aluno Online";
        $courses=[];
        $student_id = $_SESSION['course']['student']["id"];

        foreach ($this->course->getCourses() as $key => $course) {
            if($this->course_student->existStudentInCourseActive($student_id, $course["reference"])==false){
                $courses[] =  $course;
            }
        }
        
        $regist['borderom']="";
        $regist['course_id']=filter_input(INPUT_POST,'course_id',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['email']=filter_input(INPUT_POST,'email',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['status']=filter_input(INPUT_POST,'status',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;

        $student_id=$this->student->getStudentEmail($regist['email']);
        //$student_id=$this->student->getStudentBi($regist['bi']);

        $file_upload=$this->verify_file();
        if($student_id==null):
            echo "<div class='bg-danger' style='height: 60px;'><h5 class='text-light' style='text-align: center;padding-top: 2px;padding-bottom: 2px'>Aluno não matriculado!</h5></div>";
        elseif($this->course_student->existStudentInCourse($student_id,$regist['course_id'])==true):
            echo "<div class='bg-info' style='height: 60px;'><h5 class='text-light' style='text-align: center;padding-top: 2px;padding-bottom: 2px'>Aluno já está matriculado!</h5></div>";        
        else:
            if(!empty($file_upload)):

                if(move_uploaded_file($file_upload['tmp'], $file_upload['folder'].$file_upload['new_name'])):
                    $regist['borderom']=$file_upload['new_name'];
                    $color = false;
                    $this->course_student->create($regist['course_id'],$student_id,$regist['email'].uniqid(),$regist['borderom']);
                    $this->status='<h5><br/>Cadastramento enviado com sucesso!</h5>';
                    echo "<div class='bg-success' style='height: 60px;'><h5 class='text-light' style='text-align: center;padding-top: 2px;padding-bottom: 2px'>Cadastramento enviado com sucesso!</h5></div>";
                else:
                    
                    $this->status='<h5><br/>Erro inesperado, o cadastramento não foi enviado!<br/>Verifique os campos.</h5>';
                    echo "<div class='bg-danger' style='height: 60px;'><h5 class='text-light' style='text-align: center;padding-top: 2px;padding-bottom: 2px'>Erro inesperado, o cadastramento não foi enviado, verifique os campos!</h5></div>";
                endif;
            else:
                
                $this->status='<h5><br/>Cadastramento não enviado!<br/>Verifique os campos e o tamanho da comprovativo.</h5>';
                echo "<div class='bg-danger' style='height: 60px;'><h5 class='text-light' style='text-align: center;padding-top: 2px;padding-bottom: 2px'>Cadastramento não enviado! Verifique os campos e o tamanho da comprovativo.</h5></div>";
            endif;
        endif;
        
        $this->view("/courseLayout","/courseonline/coursesOnline",["title"=>$title, "status"=>$this->status, "courses"=>$courses,"color"=>false]);

    }

    function verify_file(){
        $format_img=array('jpeg','png','jpg','pdf','txt');
        $getPost=filter_input(INPUT_GET,'post',FILTER_VALIDATE_BOOLEAN);
        $file_upload=[];
        if($_FILES && !empty($_FILES['borderom']['name'])):
            $extension=pathinfo($_FILES['borderom']['name'], PATHINFO_EXTENSION);
            
            if(in_array($extension, $format_img)):
                $folder= DIRRZ.'Public'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR;
                //$folder="/assets/admin/img/courses";
                $tmp=$_FILES['borderom']['tmp_name'];
                $new_name=uniqid().'.'.$extension;
                $file_upload=[
                    'tmp'=>$tmp,
                    'folder'=>$folder,
                    'new_name'=>$new_name
                ];
                //var_dump(['filesize'=>ini_get('upload_max_filesize'),'postsize'=>ini_get('post_max_size')]);
                return $file_upload;
            endif;
        else:
            if(!$getPost):
                echo "Arquivo muinto grande, o tamanho maximo do upload é de ".ini_get('upload_max_filesize').". ";
                return [];
            endif;       
        endif;
    }

}