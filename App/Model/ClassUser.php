<?php

namespace App\Model;
use App\Model\ClassConnection;


class ClassUser extends ClassConnection {
    private $sql;
    private $con;

    public function __construct(){
        $this->sql=new ClassConnection;
        $this->con = $this->sql->connect_db();
    }
    //Dado um email verifica se existe um utilizador
    public function getUser($email){
        $sql="SELECT * FROM admini  WHERE email = '".$email."'";
        $stmt = oci_parse($this->con, $sql);
        oci_execute($stmt);

        $result=oci_fetch_array($stmt, OCI_ASSOC);

        if(count($result) > 0):
            return  $result;
        else:
            return [];
        endif;
    }
    
}