<?php

namespace App\Model;
use App\Model\ClassConnection;
use Dompdf\Options;

class ClassCine {
    private $sql;
    private $con;

    public function __construct(){
        $this->sql=new ClassConnection;
        $this->con = $this->sql->connect_db();
    }
    public function getCines(){     
        $sql = "SELECT * FROM cinema";
        $stmt = oci_parse($this->con, $sql);
        oci_execute($stmt);
        
        //$result=oci_fetch_array($stmt, OCI_ASSOC);
        $result=oci_fetch_all($stmt,$data,null,null,OCI_FETCHSTATEMENT_BY_ROW);
        return $data;
        if(count($data) > 0):
            return  $data;
        else:
            return [];
        endif;
    }

    public function create($request){

        $sql="INSERT INTO cinema(id_cinema, nome, localizacao, id_admini) VALUES(SEQ_CINEM.nextval, '".$request['name']."', '".$request['location']."', '".$request['id_admin']."')";
        $stmt = oci_parse($this->con, $sql);
        oci_execute($stmt);
        
        $sql="SELECT MAX(id_cinema) FROM cinema";

        $stmt = oci_parse($this->con, $sql);
        oci_execute($stmt);
        $result=oci_fetch_array($stmt, OCI_ASSOC);

        if(count($result) > 0):
            return  $result;
        else:
            return [];
        endif;
    }

    public function update($request, $id_cinema){
        $sql="UPDATE cinema SET nome='".$request['name']."' , localizacao='".$request['location']."' , id_admini='".$request['id_admin']."' WHERE id_cinema='$id_cinema'";
        $stmt = oci_parse($this->con, $sql);
        $result = oci_execute($stmt);
        if ($result) {
            return $result;
        }else {
            return false;
        }
        
    }

    public function delete($id){

        $sql="DELETE FROM cinema WHERE cinema='".$id."'";
        $stmt = oci_parse($this->con, $sql);
        $result = oci_execute($stmt);
        if ($result) {
            return $result;
        }else {
            return false;
        }
    }

}