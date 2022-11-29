<?php

namespace App\Controllers;

use App\Model\ClassCertification;
use App\Model\VerifyAuth;
use App\Model\ClassCourse;
use App\Model\ClassCourseStudent;
use App\Model\ClassTeaching;
use App\Model\ClassStudent;
use App\Model\ClassTests;
use App\Model\ClassNews;
use App\Model\ClassTest;
use App\Model\SendEmail;
use App\Utils\Render;

class DashController extends Render{

    private $status;
    private $course;
    private $news;
    private $teaching;
    private $payment;
    private $student;
    private $certification;
    private $course_student;
    private $sendEmail;
    private $test;

    public function __construct(){
        $this->status = "";
        $this->course=new ClassCourse;
        $this->news=new ClassNews;
        $this->teaching=new ClassTeaching;
        $this->student=new ClassStudent;
        $this->course_student= new ClassCourseStudent;
        $this->certification = new ClassCertification;
        $this->sendEmail = new SendEmail;
        $this->test = new ClassTest;
        $verify=new VerifyAuth;
        $verify->verify_user();
    }
    
    public function index(){
        $title = "AmaroGonçalves Admin";
        $students["studentTotal"] = $this->student->getTotalStudent();
        $students["studentsAtive"] = $this->course_student->getStudentsAtive();
        $students["studentsInative"] = $this->course_student->getStudentsInative();
        $totalCourse=$this->course->getTotalCourses();
        $this->view("/admin/layoutDash","/admin/dash/index",["title"=>$title, "students"=>$students, "totalCourse"=>$totalCourse]);
    }

    public function validationPayment($id){

        $status=intval(filter_input(INPUT_POST,'status',FILTER_SANITIZE_SPECIAL_CHARS))??0;
        $course=filter_input(INPUT_POST,'course',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $key=filter_input(INPUT_POST,'key',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $address=filter_input(INPUT_POST,'email',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        if($status == 0){
            $status = 1;
            
            $this->sendEmail->sendEmailKeyAccessActive($address,$course,$key);
        }else {
            $status = 0;
            $this->sendEmail->sendEmailKeyAccessInative($address,$course);
        }
        $this->course_student->updateStatus($id, $status);

        header('Location:'.DIRPAGE.'/admin/pagamentos'); 
    }

    public function addTest(){
        $title = "AmaroGonçalves Adicionar Teste";
        $courses=$this->course->getCourses();
        $types=$this->test->getTypes();
        $this->view("/admin/layoutDash","/admin/dash/addTest",["title"=>$title, "types"=>$types, "courses"=>$courses]);
    }

    function editTest($id){
        $title = "AmaroGonçalves Editar Teste";
        $courses=$this->course->getCourses();
        $types=$this->test->getTypes();
        //$answer_question = $this->test->getAnswerQuestion($id);
        $answer_question = $this->test->getAnswerQuestionTest($id);
        $answer_question_bool = $this->test->getAnswerQuestionBoolTest($id);
        $count_answer = count($answer_question);
        
        $test=$this->test->getTestById($id);
        $teachings=$this->teaching->getTeachingsCourse($test[0]["course_id"]);

        $this->view("/admin/layoutDash","/admin/dash/addTest",
        [
            "title"=>$title,
            "edit"=>true, 
            "types"=>$types, 
            "test"=>$test, 
            "courses"=>$courses,
            "teachings"=>$teachings,
            "answer_question"=>$answer_question,
            "answer_question_bool"=>$answer_question_bool,
            "count_answer"=>$count_answer
        ]);
    }

    function deleteTest($id){
        $answers = $this->test->getAnswerQuestion($id);
        $this->test->deleteAnswerOptions($answers);
        $this->test->updateAnswerNull($id);
        $this->test->delete($id);
        header('Location:'.DIRPAGE.'/admin/testes');
    }

    public function tests(){
        $title = "AmaroGonçalves - Testes inscritos";

        $courses=$this->course->getCourses();
        $teachings=$this->teaching->getTeachings();
        $answers=$this->test->getAnswerQuestions();
        //$tests=$this->test->getTests();
        $tests=$this->test->getTestsWithTeachigs();

        foreach ($this->course->getCourses() as $key => $course) {
            $courses[$course["id"]]=$course["title"];
        }
        foreach ($this->test->getAnswerQuestions() as $key => $answer) {
            $answers[$answer["id"]]=$answer["answer"];
        }
        foreach ($this->teaching->getTeachings() as $key => $teaching) {
            $teachings[$teaching["id"]]=$teaching["title"];
        }

        $this->view("/admin/layoutDash","/admin/dash/tests",[
            "title"=>$title, 
            "courses"=>$courses, 
            "tests"=>$tests,
            "teachings"=>$teachings,
            "answers"=>$answers
        ]);
    }

    public function createTest(){
        $title = "AmaroGonçalves Adicionar Teste";
        $courses=$this->course->getCourses();
        $types=$this->test->getTypes();
    
        $regist['question']=filter_input(INPUT_POST,'question',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['id_type']=filter_input(INPUT_POST,'type',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['course_id']=filter_input(INPUT_POST,'course_id',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['video_id']=filter_input(INPUT_POST,'type_test',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['option_answer']=$_POST['option_answer']??NULL;
        $regist['answercheck']=$_POST['answercheck']??NULL;
        $regist['answer']=filter_input(INPUT_POST,'answer',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['id_quiz'] = '';
        
        if($regist['course_id'] != NULL && $regist['id_type'] != NULL){
            
            if ($regist['id_type'] == 2 && isset($regist['answercheck'][0])) {
                $createTest=$this->test->create($regist);
                $regist['id_quiz'] = $createTest[0]["MAX(id)"];
                if(isset($regist['option_answer']) && isset($regist['answercheck'][0])){

                    $answerOptions=$this->test->createAnswerQuestion($regist);
                    $pos_answer_right = explode("-",$regist['answercheck'][0]);
                    $answer_right = $regist['option_answer'][$pos_answer_right[1]];
                    $regist['answer'] = $answer_right;
                    $data_answer = $this->test->getAnswerRightMultpl($answer_right);
                    if (isset($data_answer)) {
                        $this->test->updateAnswerRightMultpl($data_answer[0]["id"], $regist['id_quiz']);
                    }
                    $this->status='<h5><br/>Teste adicionado com sucesso!</h5>';
                    echo "Teste adicionado com sucesso!";
                }else {
                    $this->status='<h5><br/>Teste não adicionado! Adiciona as opções e Seleciona a resposta certa.</h5>';
                    echo "Teste não adicionado! Adiciona as opções e Seleciona a resposta certa.";
                }

            }else if(isset($regist['answer'])){
                $createTest=$this->test->create($regist);
                $regist['id_quiz'] = $createTest[0]["MAX(id)"];
                
                $this->test->createAnswerTrueOrFalse($regist);
                $answer_id = $this->test->getAnswerTrueOrFalse($regist['answer'], $regist['id_quiz']);
                $this->test->updateAnswerRightTrueOrFalse($answer_id[0]['id'], $regist['id_quiz']);
                $this->status='<h5><br/>Teste adicionado com sucesso!</h5>';
                echo "Teste adicionado com sucesso!";
            }else{
                $this->status='<h5><br/>Teste não adicionado!</h5>';
                echo "Teste não adicionado! Verifique os campos.";
            }
            
        }else {
            $this->status='<h5><br/>Teste não adicionado!</h5>';
            echo "Teste não adicionado!";
        }

        $this->view("/admin/layoutDash","/admin/dash/addTest",["title"=>$title, "types"=>$types, "courses"=>$courses]);
    }

    public function updateTest($id){

        $title = "AmaroGonçalves Editar Teste";
        $courses=$this->course->getCourses();
        $types=$this->test->getTypes();
        $answer_question = $this->test->getAnswerQuestionTest($id);
        $answer_question_bool = $this->test->getAnswerQuestionBoolTest($id);

        $test=$this->test->getTestById($id);
        $teachings=$this->teaching->getTeachingsCourse($test[0]["course_id"]);

        $regist['question']=filter_input(INPUT_POST,'question',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['id_type']=filter_input(INPUT_POST,'type',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['course_id'] = filter_input(INPUT_POST,'course_id',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['video_id']=filter_input(INPUT_POST,'type_test',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['answer'] = $_POST['answer']??NULL;
        $regist['option_answer'] = $_POST['option_answer2']??NULL;
        $regist['answercheck2'] = $_POST['answercheck2']??NULL;
        $regist['option_id'] = $_POST['option_id']??NULL;
        $regist['id_quiz'] = $id;
        $type_after = '';

        if($regist['course_id'] != NULL && $regist['id_type'] != NULL){
            $verify_id_type = $this->test->getTestById($id);
            $type_after = $verify_id_type[0]["id_type"];
            $type_later = $regist['id_type'];

            if (($verify_id_type[0]["id_type"] != intval($regist['id_type']))) {

                $answers = $this->test->getAnswerQuestion($id);
                $this->test->deleteAnswerOptions($answers);

                $this->test->updateAnswerNull($id);
            }
    
            $createTest = $this->test->update($regist, $id);

            if ($regist['id_type'] == 2) {
                $test_answer = $this->test->getAnswerQuestionTest($id);

                if (isset($_POST['option_answer']) && isset($_POST['answercheck'][0])) {
                    $regist['option_answer']=$_POST['option_answer']??NULL;
                    $regist['answercheck']=$_POST['answercheck']??NULL;

                }
                if ($test_answer == NULL && isset($regist['option_answer']) && isset($regist['answercheck'][0])) {  

                    $answerOptions=$this->test->createAnswerQuestion($regist);
                    //Pegar a posicao da resposta certa
                    $pos_answer_right = explode("-",$regist['answercheck'][0]);
                    $answer_right = $regist['option_answer'][$pos_answer_right[1]];
                    $regist['answer'] = $answer_right;
                    //Procurar a posicao da resposta certa na db
                    $data_answer = $this->test->getAnswerRightMultpl($answer_right);
                    //Atualizar a resposta certa na tabela quiz
                    if (isset($data_answer)) {
                        $this->test->updateAnswerRightMultpl($data_answer[0]["id"], $regist['id_quiz']);
                        var_dump("atualiz");
                    }
                    $this->status='<h5><br/>Teste adicionado com sucesso!</h5>';
                    echo "Teste adicionado com sucesso!";

                }elseif ($test_answer != NULL && isset($regist['option_answer']) && isset($regist['answercheck2'][0])) {
                    
                    $this->test->updateAnswerQuestion($regist,$id);

                    $pos_answer_right = $regist['answercheck2'][0];
                    $answer_right = $regist['option_answer'][intval($pos_answer_right)];
                    $regist['answer_right'] = $answer_right; 
                    $data_answer = $this->test->getAnswerRightMultpl($regist['answer_right']);

                    if (isset($data_answer)) {
                        $this->test->updateAnswerRightMultpl($data_answer[0]["id"], $regist['id_quiz']);
                        
                    }

                    $this->status='<h5><br/>Teste editado com sucesso!</h5>';
                    echo "Teste editado com sucesso!";
                    
                }else{    
                    
                    $this->status='<h5><br/>Teste não adicionado! Adiciona as opções e Seleciona a resposta certa.</h5>';
                    echo "Teste não adicionado! Adiciona as opções e Seleciona a resposta certa.";
                }
                $answer_question = $this->test->getAnswerQuestionTest($id);
                $test=$this->test->getTestById($id);
            }else{

                $test_answer = $this->test->getAnswerQuestionBoolTest($id);

                if ($test_answer == null && isset($regist['answer'])) {
                    
                    $regist['id_quiz'] = $id;
                    
                    $this->test->createAnswerTrueOrFalse($regist);
                    $answer_id = $this->test->getAnswerTrueOrFalse($regist['answer'], $regist['id_quiz']);
                    $this->test->updateAnswerRightTrueOrFalse($answer_id[0]['id'], $regist['id_quiz']);
                }else{
                    $answer_id = $this->test->getAnswerTrueOrFalse($regist['answer'],$id);
                    $this->test->updateAnswerRightTrueOrFalse($answer_id[0]['id'], $regist['id_quiz']);
                    $answer_question = $this->test->getAnswerQuestionTest($id);
                }
                $answer_question_bool = $this->test->getAnswerQuestionBoolTest($id);
                $answer_question = $this->test->getAnswerQuestionTest($id);
                $test=$this->test->getTestById($id);

                $this->status='<h5><br/>Teste editado com sucesso!</h5>';
                echo "Teste editado com sucesso!";
            }
            
        }else {
            $this->status='<h5><br/>Teste não editado!</h5>';
            echo "Teste não editado!";
        }

        $this->view("/admin/layoutDash","/admin/dash/addTest",
        [
            "title"=>$title,
            "edit"=>true, 
            "types"=>$types, 
            "test"=>$test, 
            "courses"=>$courses,
            "teachings"=>$teachings,
            "answer_question"=>$answer_question,
            "answer_question_bool"=>$answer_question_bool
        ]);
    }

    public function payments(){
        $title = "AmaroGonçalves Pagamentos";
        $course_students=$this->course_student->getCourseStudents();
        foreach ($this->course->getCourses() as $key => $course) {
            $courses[$course["id"]]=$course["title"];
        }
        foreach ($this->student->getStudents() as $key => $student) {
            $students[$student["id"]]=$student["bi"];
            $students[$student["bi"]]=$student["name"];
            $students["email".$student["id"]]=$student["email"];
        }
        $this->view("/admin/layoutDash","/admin/dash/payments",["title"=>$title, "course_students"=>$course_students,"courses"=>$courses, "students"=>$students]); 
    }
    
    public function certification(){
        $title = "AmaroGonçalves Certificado";
        $certification=$this->certification->getCertification();
        foreach ($this->course->getCourses() as $key => $course) {
            $courses[$course["id"]]=$course["title"];
        }
        foreach ($this->student->getStudents() as $key => $student) {
            $students[$student["id"]]=$student["bi"];
            $students[$student["bi"]]=$student["name"];
        }
        $this->view("/admin/layoutDash","/admin/dash/certification",["title"=>$title, "certification"=>$certification,"courses"=>$courses, "students"=>$students]);  
    }

    public function addCertification(){
        $title = "AmaroGonçalves Adicionar Certificado";
        $courses=$this->course->getCourses();
        $this->view("/admin/layoutDash","/admin/dash/addCertification",["title"=>$title, "courses"=>$courses]);
    }

    public function createCertification(){
        $title = "AmaroGonçalves Adicionar Certificado";
        $courses=$this->course->getCourses();
        $regist['file']='';
        $regist['bi']=filter_input(INPUT_POST,'bi',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['course_id']=filter_input(INPUT_POST,'course_id',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $student_id = $this->student->getStudentBi($regist['bi']);
        //var_dump($student_id);
        //var_dump($this->course_student->existStudentInCourseActive($student_id, $regist['course_id']));
        if ($student_id != null && $this->course_student->existStudentInCourseActive($student_id, $regist['course_id'])==true) {
        
            $file_upload=$this->verify_file_text();
            
            if(!empty($file_upload)):

                if(move_uploaded_file($file_upload['tmp'], $file_upload['folder'].$file_upload['new_name'])):
                    $regist['file']=$file_upload['new_name'];
                    $certification=$this->certification->create($regist);
                    $this->status='<h5><br/>Certificado adicionado!</h5>';
                    echo "Certificado enviado com sucesso!";
                    $this->view("/admin/layoutDash","/admin/dash/addCertification",["title"=>$title, "status"=>$this->status]);
                else:
                    
                    $this->status='<h5><br/>Erro inesperado, o vídeo não foi adicionado!<br/>Verifique os campos.</h5>';
                    $this->view("/admin/layoutDash","/admin/dash/addCertification",["title"=>$title, "status"=>$this->status, "courses"=>$courses]);
                endif;
            else:
                $this->status='<h5><br/>Certificado não adicionado!<br/>Verifique os campos e o tamanho do vídeo.</h5>';
                $this->view("/admin/layoutDash","/admin/dash/addCertification",["title"=>$title, "status"=>$this->status, "courses"=>$courses]);
            endif;
        } else{
            $this->status='<h5><br/>Certificado não adicionado!<br/>Aluno não identificado!</h5>';
            echo '<h5><br/>Aluno não identificado no curso!</h5>';
            $this->view("/admin/layoutDash","/admin/dash/addCertification",["title"=>$title, "status"=>$this->status, "courses"=>$courses]);  
        }
        //$this->view("/admin/layoutDash","/admin/dash/addCourse",["title"=>$title]);
    }

    function deleteCertification($id){
        $this->certification->delete($id);
        header('Location:'.DIRPAGE.'/admin/certificados');
    }

    function editCertification($id){
        $title = "AmaroGonçalves Editar Certificado";
        $certification=$this->certification->getCertificationById($id);
        $courses=$this->course->getCourses();
        $this->view("/admin/layoutDash","/admin/dash/addCertification",["title"=>$title,"edit"=>true, "certification"=>$certification, "courses"=>$courses]);
    }

    public function updateCertification($id){
        $title = "AmaroGonçalves Editar Certificado";
        $certification=$this->certification->getCertificationById($id);
        $courses=$this->course->getCourses();

        $regist['file']='';
        $regist['bi']=filter_input(INPUT_POST,'bi',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['course_id']=filter_input(INPUT_POST,'course_id',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;

        $file_upload=$this->verify_file_text();
        $verify_certification=$this->certification->getCertificationById($id);
        
        if(!empty($file_upload)):     
            if(move_uploaded_file($file_upload['tmp'], $file_upload['folder'].$file_upload['new_name'])):
                $regist['file']=$file_upload['new_name'];
        
                $this->certification->update($regist, $id);
                $certification=$this->certification->getCertificationById($id);
                $this->status='<h5><br/>Certificado editada!</h5>';
                echo '<h5><br/>Certificado editado!</h5>';
                //header('location:'.DIRPAGE."/dashboard-admin/slide");
                $this->view("/admin/layoutDash","/admin/dash/addCertification",["title"=>$title,"edit"=>true, "status"=>$this->status, "certification"=>$certification, "courses"=>$courses]);

            else:
                $this->status='<h5><br/>Erro inesperado, Certificado foi adicionado!<br/>Verifique os campos.</h5>';
                $this->view("/admin/layoutDash","/admin/dash/addCertification",["title"=>$title,"edit"=>true, "status"=>$this->status, "certification"=>$certification, "courses"=>$courses]);
            endif;
        else:
            if($file_upload==[]  && $verify_certification[0]['file']!=NULL):
                $regist['file']=$verify_certification[0]['file'];
                $this->certification->update($regist, $id);
                $certification=$this->certification->getCertificationById($id);
                $this->status='<h5><br/>Certificado editado!</h5>';
                $this->view("/admin/layoutDash","/admin/dash/addCertification",["title"=>$title,"edit"=>true, "status"=>$this->status, "certification"=>$certification, "courses"=>$courses]);
            else:
                $this->status='<h5><br/>Certificado não foi editado!<br/>Verifique os campos e o tamanho do certificado.</h5>';
                $this->view("/admin/layoutDash","/admin/dash/addCertification",["title"=>$title,"edit"=>true, "status"=>$this->status, "certification"=>$certification, "courses"=>$courses]);
            endif;
        endif;

    }

    public function courses(){
        $title = "AmaroGonçalves Cursos";

        $course=$this->course->getCourses();

        $this->view("/admin/layoutDash","/admin/dash/courses",["title"=>$title, "courses"=>$course]);
    }

    public function addCourse(){
        $title = "AmaroGonçalves Adicionar Curso";

        $this->view("/admin/layoutDash","/admin/dash/addCourse",["title"=>$title]);
    }
    
    public function createCourse(){
        $title = "AmaroGonçalves Adicionar Curso";
        $regist['image']='';
        $regist['ebook']='';
        $regist['title']=filter_input(INPUT_POST,'title',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['description']=filter_input(INPUT_POST,'description',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['status']=filter_input(INPUT_POST,'status',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['teacher']=filter_input(INPUT_POST,'teacher',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;

        $file_upload=$this->verify_file();
        $file_upload_pdf=$this->verify_file_pdf();

        if(!empty($file_upload_pdf)){
            if(move_uploaded_file($file_upload_pdf['tmp'], $file_upload_pdf['folder'].$file_upload_pdf['new_name'])):
                $regist['ebook']=$file_upload_pdf['new_name'];
            endif;
        }
        if(!empty($file_upload)):
        
            if(move_uploaded_file($file_upload['tmp'], $file_upload['folder'].$file_upload['new_name'])):
                $regist['image']=$file_upload['new_name'];
                $course=$this->course->create($regist);
                $this->status='<h5><br/>Curso guardado com sucesso!</h5>';
                echo "Curso guardado com sucesso!";
                $this->view("/admin/layoutDash","/admin/dash/addCourse",["title"=>$title, "status"=>$this->status]);
            else:
                
                $this->status='<h5><br/>Erro inesperado, o slide não foi adicionado!<br/>Verifique os campos.</h5>';
                $this->view("/admin/layoutDash","/admin/dash/addCourse",["title"=>$title, "status"=>$this->status]);
            endif;
        else:
            $this->status='<h5><br/>Slide não adicionado!<br/>Verifique os campos e o tamanho da imagem.</h5>';
            $this->view("/admin/layoutDash","/admin/dash/addCourse",["title"=>$title, "status"=>$this->status]);
        endif;
        
        //$this->view("/admin/layoutDash","/admin/dash/addCourse",["title"=>$title]);
    }

    function deleteCourse($id){
        $this->teaching->deleteTeachingsCourse($id);
        $this->course->delete($id);
        header('Location:'.DIRPAGE.'/admin/cursos');
    }

    function editCourse($id){
        $title = "AmaroGonçalves Editar Curso";
        $course=$this->course->getCourseById($id);
        $this->view("/admin/layoutDash","/admin/dash/addCourse",["title"=>$title,"edit"=>true, "course"=>$course]);
    }

    public function updateCourse($id){

        $title = "AmaroGonçalves Editar Curso";
        $course=$this->course->getCourseById($id);

        $regist['image']='';
        $regist['ebook']='';
        $regist['title']=filter_input(INPUT_POST,'title',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['description']=filter_input(INPUT_POST,'description',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['status']=filter_input(INPUT_POST,'status',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['teacher']=filter_input(INPUT_POST,'teacher',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['course_id']=filter_input(INPUT_POST,'course_id',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;

        $file_upload=$this->verify_file();
        $file_upload_pdf=$this->verify_file_pdf();
        $verify_course=$this->course->getCourseById($id);

        if($file_upload_pdf==[]  && $verify_course[0]['ebook']!=NULL):
            $regist['ebook']=$verify_course[0]['ebook'];
        else:
            if(move_uploaded_file($file_upload_pdf['tmp'], $file_upload_pdf['folder'].$file_upload_pdf['new_name'])):
                $regist['ebook']=$file_upload_pdf['new_name'];
            endif;
        endif;
        
        if(!empty($file_upload)):
        
            if(move_uploaded_file($file_upload['tmp'], $file_upload['folder'].$file_upload['new_name'])):
                $regist['image']=$file_upload['new_name'];

                $this->course->update($regist, $id);
                $course=$this->course->getCourseById($id);
                $this->status='<h5><br/>Curso editado!</h5>';
                //header('location:'.DIRPAGE."/dashboard-admin/slide");
                $this->view("/admin/layoutDash","/admin/dash/addCourse",["title"=>$title,"edit"=>true, "status"=>$this->status, "course"=>$course]);

            else:
                $this->status='<h5><br/>Erro inesperado, aula não foi adicionado!<br/>Verifique os campos.</h5>';
                $this->view("/admin/layoutDash","/admin/dash/addCourse",["title"=>$title,"edit"=>true, "status"=>$this->status, "course"=>$course]);
            endif;
        else:
            if($file_upload==[]  && $verify_course[0]['image']!=NULL):
                $regist['image']=$verify_course[0]['image'];
                $this->course->update($regist, $id);
                $course=$this->course->getCourseById($id);
                $this->status='<h5><br/>Curso editado!</h5>';
                echo 'Curso editado!';
                $this->view("/admin/layoutDash","/admin/dash/addCourse",["title"=>$title,"edit"=>true, "status"=>$this->status, "course"=>$course]);
            else:
                $this->status='<h5><br/>Curso não foi editado!<br/>Verifique os campos e o tamanho da imagem.</h5>';
                $this->view("/admin/layoutDash","/admin/dash/addCourse",["title"=>$title,"edit"=>true, "status"=>$this->status, "course"=>$course]);
            endif;
        endif;

    }

    function verify_file(){
        //echo DIRRZ;
        //return;
        $format_img=array('jpeg','png','jpg','gif');
        $getPost=filter_input(INPUT_GET,'post',FILTER_VALIDATE_BOOLEAN);
        $file_upload=[];
        if($_FILES && !empty($_FILES['image']['name'])):
            $extension=pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            
            if(in_array($extension, $format_img)):
                $folder= DIRRZ.'Public'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR;
                //$folder="/assets/admin/img/courses";
                $tmp=$_FILES['image']['tmp_name'];
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
                return [];
            endif;       
        endif;
    }

    public function students(){
        $title = "AmaroGonçalves - Alunos inscritos";

        $students=$this->student->getStudents();
        foreach ($this->course->getCourses() as $key => $course) {
            $courses[$course["course_id"]]=$course["title"];
        }
        

        $this->view("/admin/layoutDash","/admin/dash/students",["title"=>$title, "students"=>$students, "courses"=>$courses]);
    }

    public function teachings(){
        $title = "AmaroGonçalves Aula";
        $courses = [];
        $teaching=$this->teaching->getTeachingsOfCourses();
        $this->view("/admin/layoutDash","/admin/dash/teachings",["title"=>$title, "teachings"=>$teaching, "courses"=>$courses]);
    }

    public function addTeaching(){
        $title = "AmaroGonçalves Adicionar Aula";
        $courses=$this->course->getCourses();
        $this->view("/admin/layoutDash","/admin/dash/addTeaching",["title"=>$title, "courses"=>$courses]);
    }
    public function createTeaching(){
        $title = "AmaroGonçalves Adicionar Aula";
        $courses=$this->course->getCourses();
        //$regist['video']='';
        $regist['helpbook']='';
        $regist['title']=filter_input(INPUT_POST,'title',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['course_id']=filter_input(INPUT_POST,'course_id',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['video']=filter_input(INPUT_POST,'video',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        
        //$file_upload=$this->verify_file_video();
        $file_upload_pdf=$this->verify_file_text();
        
        
        /*if(!empty($file_upload)):
        
            if(move_uploaded_file($file_upload['tmp'], $file_upload['folder'].$file_upload['new_name'])):
                $regist['video']=$file_upload['new_name'];*/
                if(!empty($file_upload_pdf)):
                    if(move_uploaded_file($file_upload_pdf['tmp'], $file_upload_pdf['folder'].$file_upload_pdf['new_name'])):
                        $regist['helpbook']=$file_upload_pdf['new_name'];
                    endif;
                endif;
                
                $teaching=$this->teaching->create($regist);

                $this->status='<h5><br/>Aula adicionada!</h5>';
                echo "Aula enviada com sucesso!";
                $this->view("/admin/layoutDash","/admin/dash/addTeaching",["title"=>$title, "status"=>$this->status, "courses"=>$courses]);
            /*else:
                
                $this->status='<h5><br/>Erro inesperado, o vídeo não foi adicionado!<br/>Verifique os campos.</h5>';
                $this->view("/admin/layoutDash","/admin/dash/addTeaching",["title"=>$title, "status"=>$this->status]);
            endif;
        else:
            $this->status='<h5><br/>Aula não adicionado!<br/>Verifique os campos e o tamanho do vídeo.</h5>';
            $this->view("/admin/layoutDash","/admin/dash/addTeaching",["title"=>$title, "status"=>$this->status]);
        endif;*/
        
        //$this->view("/admin/layoutDash","/admin/dash/addCourse",["title"=>$title]);
    }

    function deleteTeaching($id){
        $this->teaching->delete($id);
        header('Location:'.DIRPAGE.'/admin/aulas');
    }

    function editTeaching($id){
        $title = "AmaroGonçalves Editar Aula";
        $teaching=$this->teaching->getTeachingById($id);
        $courses=$this->course->getCourses();
        $this->view("/admin/layoutDash","/admin/dash/addTeaching",["title"=>$title,"edit"=>true, "teaching"=>$teaching, "courses"=>$courses]);
    }

    public function updateTeaching($id){
        $title = "AmaroGonçalves Editar Aula";
        $teaching=$this->teaching->getTeachingById($id);
        $courses=$this->course->getCourses();

        //$regist['video']='';
        $regist['helpbook']='';
        $regist['title']=filter_input(INPUT_POST,'title',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['course_id']=filter_input(INPUT_POST,'course_id',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['video']=filter_input(INPUT_POST,'video',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;

        //$file_upload=$this->verify_file_video();
        $file_upload_pdf=$this->verify_file_text();
        $verify_teaching=$this->teaching->getTeachingById($id);
        
        /*if(!empty($file_upload)):
        
            if(move_uploaded_file($file_upload['tmp'], $file_upload['folder'].$file_upload['new_name'])):
                $regist['video']=$file_upload['new_name'];*/
                if(!empty($file_upload_pdf)):
                    if(move_uploaded_file($file_upload_pdf['tmp'], $file_upload_pdf['folder'].$file_upload_pdf['new_name'])):
                        $regist['helpbook']=$file_upload_pdf['new_name'];
                    endif;
                else:
                    $regist['helpbook']=$verify_teaching[0]['helpbook'];
                endif;
                /*$video_exist=$verify_teaching[0]['video'];
                $folder= DIRRZ.'Public'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'video'.DIRECTORY_SEPARATOR.$video_exist;
                if (file_exists($folder)) {
                    //var_dump($folder);
                    //return;
                }*/
                $this->teaching->update($regist, $id);
                $teaching=$this->teaching->getTeachingById($id);
                $this->status='<h5><br/>Aula editada!</h5>';
                echo '<h6><br/>Aula editada!</h6>';
                //header('location:'.DIRPAGE."/dashboard-admin/slide");
                $this->view("/admin/layoutDash","/admin/dash/addTeaching",["title"=>$title,"edit"=>true, "status"=>$this->status, "teaching"=>$teaching, "courses"=>$courses]);

            /*else:
                $this->status='<h5><br/>Erro inesperado, Aula foi adicionado!<br/>Verifique os campos.</h5>';
                echo '<h5><br/>Erro inesperado, Aula foi adicionado!<br/>Verifique os campos.</h5>';
                $this->view("/admin/layoutDash","/admin/dash/addTeaching",["title"=>$title,"edit"=>true, "status"=>$this->status, "teaching"=>$teaching, "courses"=>$courses]);
            endif;
        else:
            
            if($file_upload==[]  && $verify_teaching[0]['video']!=NULL && $_FILES["video"]!=NULL):
                //var_dump($_FILES["helpbook"]);
                //return;
                $regist['video']=$verify_teaching[0]['video'];
                if(!empty($file_upload_pdf)):
                    if(move_uploaded_file($file_upload_pdf['tmp'], $file_upload_pdf['folder'].$file_upload_pdf['new_name'])):
                        $regist['helpbook']=$file_upload_pdf['new_name'];
                    endif;
                else:
                    $regist['helpbook']=$verify_teaching[0]['helpbook'];
                endif;
                //var_dump($file_upload_pdf);
                //return;
                $this->teaching->update($regist, $id);
                $teaching=$this->teaching->getTeachingById($id);
                $this->status='<h5><br/>Aula editada!</h5>';
                echo '<h6><br/>Aula editada!</h6>';
                
                $this->view("/admin/layoutDash","/admin/dash/addTeaching",["title"=>$title,"edit"=>true, "status"=>$this->status, "teaching"=>$teaching, "courses"=>$courses]);
            else:
                $this->status='<h5><br/>Aula não foi editado!<br/>Verifique os campos e o tamanho do vídeo.</h5>';
                echo '<h6>Aula não foi editada! Verifique os campos e o tamanho do vídeo.</h6>';
                $this->view("/admin/layoutDash","/admin/dash/addTeaching",["title"=>$title,"edit"=>true, "status"=>$this->status, "teaching"=>$teaching, "courses"=>$courses]);
            endif;
            
        endif;*/
    }

    public function news(){
        $title = "AmaroGonçalves Notícias";

        $news=$this->news->getNews();

        $this->view("/admin/layoutDash","/admin/dash/news",["title"=>$title, "news"=>$news]);
    }

    public function addNews(){
        $title = "AmaroGonçalves Adicionar Notícia";

        $this->view("/admin/layoutDash","/admin/dash/addNews",["title"=>$title]);
    }
    
    public function createNews(){
        $title = "AmaroGonçalves Adicionar Notícia";
        $regist['title']=filter_input(INPUT_POST,'title',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['description']=filter_input(INPUT_POST,'description',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['link']=filter_input(INPUT_POST,'link',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $news=$this->news->create($regist);
        $this->status='<h5><br/>Notícia adicionada!</h5>';
        echo "Notícia adicionada com sucesso!";
        $this->view("/admin/layoutDash","/admin/dash/addNews",["title"=>$title, "status"=>$this->status]);
    }

    function deleteNews($id){
        $this->news->delete($id);
        header('Location:'.DIRPAGE.'/admin/noticias');
    }

    function editNews($id){
        $title = "AmaroGonçalves Editar Notícia";
        $news=$this->news->getNewsById($id);
        $this->view("/admin/layoutDash","/admin/dash/addNews",["title"=>$title,"edit"=>true, "news"=>$news]);
    }

    public function updateNews($id){

        $title = "AmaroGonçalves Editar Notícia";
        $news=$this->news->getNewsById($id);

        $regist['title']=filter_input(INPUT_POST,'title',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['description']=filter_input(INPUT_POST,'description',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;
        $regist['link']=filter_input(INPUT_POST,'link',FILTER_SANITIZE_SPECIAL_CHARS)??NULL;

        $this->news->update($regist, $id);
        $news=$this->news->getNewsById($id);
        $this->status='<h5><br/>Notícia editada!</h5>';
        echo "Notícia editada com sucesso!";
        $this->view("/admin/layoutDash","/admin/dash/addNews",["title"=>$title,"edit"=>true, "status"=>$this->status, "news"=>$news]);
    }


    function verify_file_video(){
        $format_img=array('mp4','ogg','webm');
        $getPost=filter_input(INPUT_GET,'post',FILTER_VALIDATE_BOOLEAN);
        $file_upload=[];
        if($_FILES && !empty($_FILES['video']['name'])):
            $extension=pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
            
            if(in_array($extension, $format_img)):
                $folder= DIRRZ.'Public'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'video'.DIRECTORY_SEPARATOR;
                //$folder="/assets/admin/img/courses";
                $tmp=$_FILES['video']['tmp_name'];
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

    function verify_file_text(){
        $format_img=array('pdf');
        $getPost=filter_input(INPUT_GET,'post',FILTER_VALIDATE_BOOLEAN);
        $file_upload=[];
        if($_FILES && !empty($_FILES['file']['name'])):
            $extension=pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            
            if(in_array($extension, $format_img)):
                $folder= DIRRZ.'Public'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR;
                //$folder="/assets/admin/img/courses";
                $tmp=$_FILES['file']['tmp_name'];
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
                //echo "Arquivo muinto grande, o tamanho maximo do upload é de ".ini_get('upload_max_filesize');
                return [];
            endif;       
        endif;
    }
    function verify_file_pdf(){
        $format_img=array('pdf');
        $getPost=filter_input(INPUT_GET,'post',FILTER_VALIDATE_BOOLEAN);
        
        $file_upload=[];
        if($_FILES && !empty($_FILES['ebook']['name'])):
            $extension=pathinfo($_FILES['ebook']['name'], PATHINFO_EXTENSION);
            
            if(in_array($extension, $format_img)):
                $folder= DIRRZ.'Public'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR;
                //$folder="/assets/admin/img/courses";
                $tmp=$_FILES['ebook']['tmp_name'];
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
                //echo "Arquivo muinto grande, o tamanho maximo do upload é de ".ini_get('upload_max_filesize');
                return [];
            endif;       
        endif;
    }

}