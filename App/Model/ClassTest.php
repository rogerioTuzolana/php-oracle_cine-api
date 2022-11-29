<?php

namespace App\Model;
use App\Model\ClassConnection;
use Dompdf\Options;

class ClassTest {
    private $sql;
    private $con;

    public function __construct(){
        $this->sql=new ClassConnection;
        $this->con = $this->sql->connect_db();
    }

    public function getTests(){     
        //$sql = "SELECT f.nome, s.preco FROM filme f, sessao s WHERE f.id_filme = s.id_filme";
        $sql = "SELECT * FROM lugar";
        $stmt = oci_Parse($this->con, $sql);
        oci_execute($stmt);
        
        //$result=oci_fetch_array($stmt, OCI_ASSOC);
        $result=oci_fetch_all($stmt,$data,null,null,OCI_FETCHSTATEMENT_BY_ROW);

        if(count($data) > 0):
            return  $data;
        else:
            return [];
        endif;
        //oci_free_statement($stmt);
        //oci_close($this->con);
    }

    public function post($request){
        /*$id = $request["id"];
        $first_name = $request['first_name'];
        $last_name = $request['last_name'];
        $gender = $request['gender'];*/

        $sql="INSERT INTO test(customer_id, first_name, last_name, gender) VALUES(SEQ_CINEMA.nextval, '".$request['first_name']."', '".$request['last_name']."', '".$request['gender']."')";
        $stmt = oci_parse($this->con, $sql);
        oci_execute($stmt);
        
        $sql="SELECT MAX(customer_id) FROM test";

        $stmt = oci_parse($this->con, $sql);
        oci_execute($stmt);
        $result=oci_fetch_array($stmt, OCI_ASSOC);

        if(count($result) > 0):
            return  $result;
        else:
            return [];
        endif;
    }

    public function put($request){
        $first_name = $request['first_name'];
        $sql="UPDATE test SET first_name='$first_name' WHERE customer_id = '".$request['id']."'";
        $stmt = oci_parse($this->con, $sql);
        $result = oci_execute($stmt);
        if ($result) {
            return $result;
        }else {
            return false;
        }
        
    }

    public function delete($id){

        $sql="DELETE FROM test WHERE customer_id='".$id."'";
        $stmt = oci_parse($this->con, $sql);
        $result = oci_execute($stmt);
        if ($result) {
            return $result;
        }else {
            return false;
        }

    }

    public function getTestsWithTeachigs(){
        $sql="SELECT quiz.id,quiz.question,quiz.id_type,quiz.course_id,quiz.answer_id,teaching.title FROM `quiz` JOIN `teaching` on teaching.id = quiz.video_id";
        $select=parent::connect_db()->prepare($sql);
        $select->execute();
        if($select->rowCount() > 0):
            return  $select->fetchAll(\PDO::FETCH_ASSOC);
        else:
            return [];
        endif;
    }

    //metodo que adiciona dados do portfolio.
    public function create($regist){
        $sql="INSERT INTO quiz(question, id_type, course_id, video_id) VALUES(?, ?, ?, ?)";
        $insert=parent::connect_db()->prepare($sql);
        $insert->bindValue(1, $regist['question']);
        $insert->bindValue(2, $regist['id_type']);
        $insert->bindValue(3, $regist['course_id']);
        $insert->bindValue(4, $regist['video_id']);
        $insert->execute();

        $sql="SELECT MAX(id) FROM quiz";
        $select=parent::connect_db()->prepare($sql);
        $select->execute();
        return  $select->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateAnswerBool($answer, $id){
        $sql="UPDATE quiz SET answer_question = ? WHERE id = ?";
        $update=parent::connect_db()->prepare($sql);
        $update->bindValue(1, $answer);
        $update->bindValue(2, $id);
        $update->execute();
    }

    public function update($regist, $id){
        $sql="UPDATE quiz SET question=?, id_type = ?, course_id = ?, video_id = ? WHERE id = ?";
        $update=parent::connect_db()->prepare($sql);
        $update->bindValue(1, $regist['question']);
        $update->bindValue(2, $regist['id_type']);
        $update->bindValue(3, $regist['course_id']);
        $update->bindValue(4, $regist['video_id']);
        $update->bindValue(5, $id);
        $update->execute();
    }
    //metodo que elimina dados do .
    public function deletei($id){
        $sql="DELETE FROM quiz WHERE id = ?";
        $delete=parent::connect_db()->prepare($sql);;
        $delete->bindValue(1, $id);
        $delete->execute();
    }

    public function deleteAnswerOption($id){
        
        $sql="SET FOREIGN_KEY_CHECKS=0;DELETE FROM answer_question WHERE id = ?;SET FOREIGN_KEY_CHECKS=1;";
        $delete=parent::connect_db()->prepare($sql);
        $delete->bindValue(1, $id);
        $delete->execute();
        
    }

    public function deleteT($id){
        $course = $this->getCourseById($id);
        $file = '';
        if ($course[0]["image"]!='') {
            $file = DIRRZ.'Public'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$course[0]["image"];
        }
        
        $sql="SET FOREIGN_KEY_CHECKS=0;DELETE FROM courses WHERE id = ?;SET FOREIGN_KEY_CHECKS=1;";
        $delete=parent::connect_db()->prepare($sql);;
        $delete->bindValue(1, $id);
        $delete->execute();

        if (file_exists($file)) {
            unlink($file);
            echo "Passou image";
        }
        
    }
}