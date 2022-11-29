<?php

namespace App\ajax;
use App\Model\ClassConnection;
use App\Model\ClassTest;


class Uploads extends ClassConnection{
    private $test;

    public function __construct(){
        $this->test=new ClassTest;
    }


    public function updateTimeVideo($regist){
        $sql="UPDATE student_teaching SET time_whatched =?, status_video_ended =? WHERE course_id = ? and video_id = ?";
        $update=parent::connect_db()->prepare($sql);
        $update->bindValue(1, $regist['time_whatched']);
        $update->bindValue(2, ($regist['time']==$regist['time_whatched']?1:NULL));
        $update->bindValue(3, $regist['course_id']);
        $update->bindValue(4, $regist['video_id']);
        $update->execute();
    }
    
    public function updateWithStatusTimeVideo($regist){
        $sql="UPDATE student_teaching SET time_whatched =?, status_video_ended = ? WHERE course_id = ? and video_id = ?";
        $update=parent::connect_db()->prepare($sql);
        $update->bindValue(1, $regist['time_whatched']);
        $update->bindValue(2, intval($regist['status_video_ended']));
        $update->bindValue(3, $regist['course_id']);
        $update->bindValue(4, intval($regist['video_id']));
        $update->execute();
    }
    public function updateStatusVideo(){
        $regist["student_id"] = $_POST["student_id"];
        $regist["course_id"] = $_POST["course_id"];
        $regist["video_id"] = $_POST["video_id"];
        $regist["time_whatched"] = $_POST["time_whatched"];
        $regist["status_video_ended"] = '1';

        $this->updateWithStatusTimeVideo($regist);
        echo json_encode("1");
        return;
    }
    
    
    public function getStatusVideoFinalBefored($before_id){
        $sql="SELECT * FROM student_teaching WHERE video_id = ?";
        $select=parent::connect_db()->prepare($sql);
        $select->bindValue(1, intval($before_id));
        $select->execute();
        if($select->rowCount() > 0):
            $results = $select->fetchAll(\PDO::FETCH_ASSOC);
            return $results;
        else:
            return [];
        endif;
    }
    public function firstVideoCourse(){
        $sql="SELECT * FROM teaching ORDER BY (id) ASC LIMIT 1";
        $select=parent::connect_db()->prepare($sql);
        $select->execute();
        if($select->rowCount() > 0):
            
            
            $results = $select->fetchAll(\PDO::FETCH_ASSOC);
            return $results[0]["id"];
        else:
            return null;
        endif;
    }

    public function getStatusVideoCourse($student_id, $course_id, $video_id){
        $sql="SELECT * FROM student_teaching WHERE student_id = ? and course_id = ? and video_id = ?";
        $select=parent::connect_db()->prepare($sql);
        $select->bindValue(1, intval($student_id));
        $select->bindValue(2, intval($course_id));
        $select->bindValue(3, intval($video_id));
        $select->execute();
        if($select->rowCount() > 0):
            $results = $select->fetchAll(\PDO::FETCH_ASSOC);
            return $results;
        else:
            return NULL;
        endif;
    }

    public function videoWhatchedStatusTrue(){
        $regist["student_id"] = $_GET["student_id"];
        $regist["course_id"] = $_GET["course_id"];
        $regist["video_id"] = $_GET["video_id"];
        $regist["beforeVideoId"] = $_GET["beforeVideoId"];


        
        if ($regist["beforeVideoId"] !="") {
            $exist_before_video_whatched = $this->getStatusVideoCourse($regist["student_id"],$_GET["course_id"], $regist["beforeVideoId"]);
            $beforeVideo = $this->getStatusVideoFinalBefored($regist["beforeVideoId"]);


        }
        $exist_video_whatched = $this->getStatusVideoCourse($regist["student_id"],$regist["course_id"], $_GET["video_id"]);

        
        if (isset($exist_before_video_whatched) && empty($exist_video_whatched) && ($exist_before_video_whatched[0]["status"] == 1 /*|| $exist_before_video_whatched[0]["status_video_ended"] == 1*/) && $this->firstVideoCourse()!=intval($regist["video_id"])) {
            echo json_decode("0");
            return;
        }
        if (isset($exist_before_video_whatched) && $exist_before_video_whatched[0]["status"] == 1 /*&& $exist_before_video_whatched[0]["status_video_ended"] == 1*/  && isset($exist_video_whatched) && ($exist_video_whatched[0]["status"] == NULL)) {
            echo json_decode("0");
            return;
        }
        if (isset($exist_before_video_whatched) && $exist_before_video_whatched[0]["status"] == 1 /*&& $exist_before_video_whatched[0]["status_video_ended"] == 1*/  && empty($exist_video_whatched)) {
            echo json_decode("0");
            return;
        }

        if (isset($exist_video_whatched) && ($exist_video_whatched[0]["status"] == 1)) {
            echo json_decode("1");
            return;
        }
        if (isset($exist_before_video_whatched) && isset($exist_video_whatched) && ($exist_before_video_whatched[0]["status"] != 1 /*|| $exist_before_video_whatched[0]["status_video_ended"] != 1*/) && $this->firstVideoCourse()!=intval($regist["video_id"])) {
            //var_dump($exist_video_whatched);
            echo json_decode("2");
            return;
        }
        
        if ($this->firstVideoCourse()==intval($regist["video_id"])) {
            $status=$this->getVideoCourse($_GET["student_id"], $_GET["course_id"], $_GET["video_id"]);
            if(empty($status)){
                $regist["student_id"] = $_GET["student_id"];
                $regist["course_id"] = $_GET["course_id"];
                $regist["video_id"] = $_GET["video_id"];
                $regist["time_whatched"]=NULL;
                $regist["status"]=true;
                $this->addTimeCurrentVideo($regist);
            }
            echo json_decode("1");
            return;
        }

        echo json_decode("-1");
        return;
    }
    public function getVideoCourse($student_id, $course_id, $video_id){
        $sql="SELECT * FROM student_teaching WHERE student_id = ? and course_id = ? and video_id = ?";
        $select=parent::connect_db()->prepare($sql);
        $select->bindValue(1, $student_id);
        $select->bindValue(2, $course_id);
        $select->bindValue(3, $video_id);
        $select->execute();
        if($select->rowCount() > 0):
            $results = $select->fetchAll(\PDO::FETCH_ASSOC);
            return $results;
        else:
            return null;
        endif;
    }

    //metodo que adiciona dados do vidio assistido.
    public function addTimeCurrentVideo($regist){
        $sql="INSERT INTO student_teaching(student_id,course_id,video_id,time_whatched,status) VALUES(?, ?, ?, ?,?)";
        $insert=parent::connect_db()->prepare($sql);
        $insert->bindValue(1, intval($regist["student_id"]));
        $insert->bindValue(2, intval($regist["course_id"]));
        $insert->bindValue(3, intval($regist["video_id"]));
        $insert->bindValue(4, $regist["time_whatched"]);
        $insert->bindValue(5, $regist["status"]);
        $insert->execute();
    }
    
    public function createAndUpdateTimeVideo(){
        $regist["student_id"] = $_POST["student_id"];
        $regist["course_id"] = $_POST["course_id"];
        $regist["video_id"] = $_POST["video_id"];
        $regist["time_whatched"] = $_POST["time_whatched"];
        $regist["time"] = $_POST["time"];

        $exist_video_whatched = $this->getVideoCourse($regist["student_id"],$_POST["course_id"], $_POST["video_id"]);
        if ($exist_video_whatched != null) {
            if (doubleval($_POST["time_whatched"]) > doubleval($exist_video_whatched[0]["time_whatched"])) {
                $this->updateTimeVideo($regist);
                echo json_encode("Atualizou");
                return;
            }
            
        }else {
            $this->addTimeCurrentVideo($regist);
            
            echo json_encode("Atualizou");

            return;
        }
        
        echo json_encode("Nao guardou");
        return;
    }
    
    public function getTeachingsCourse($course_id){
        $sql="SELECT * FROM teaching WHERE (course_id = ?)";
        $select=parent::connect_db()->prepare($sql);
        $select->bindValue(1, $course_id);
        $select->execute();
        if($select->rowCount() > 0):
            return  $select->fetchAll(\PDO::FETCH_ASSOC);
            //return $results;
        else:
            return [];
        endif;
    }

    public function getVideosCourseForTest(){
        $regist["course_id"] = $_GET["course_id"];
        $videosCourse = $this->getTeachingsCourse($regist["course_id"]);
        if ($videosCourse != null) {
            header('Content-Type: application/json');
            echo json_encode($videosCourse);
            return;
        }
        echo json_encode(0);
        return;
    }

    public function updateSubOption(){
        //$regist["option_id"] = $_POST["option_id"];
        $this->test->deleteAnswerOption($_POST["option_id"]);
        echo json_encode(1);
    }

    
}

