<?php

namespace App\Model;
use App\Model\ClassConnection;
use Dompdf\Options;

class ClassClient {
    private $sql;
    private $con;

    public function __construct(){
        $this->sql=new ClassConnection;
        $this->con = $this->sql->connect_db();
    }

    public function getClients(){  
 
        $sql = "SELECT * FROM cliente";

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
        
        $sql="INSERT INTO cliente(id_cliente, nome, num_tel, email, id_reserva) VALUES(SEQ_CLIENTE.nextval, '".$request['name']."', '".$request['num_tel']."', '".$request['email']."', '".$request['id_reservation']."')";
        $stmt = oci_parse($this->con, $sql);
        $result = oci_execute($stmt);
        if ($result) {
            return $result;
        }else {
            return false;
        }
        
    }

    public function update($request, $id_room){

        $sql="UPDATE cliente SET nome='".$request['name']."', num_tel='".$request['num_tel']."', email='".$request['email']."', id_reserva='".$request['id_reservation']."' WHERE id_cliente='".$id_cliente."'";
        $stmt = oci_parse($this->con, $sql);
        $result = oci_execute($stmt);
        if ($result) {
            return $result;
        }else {
            return false;
        }
        
    }

    public function delete($id){

        $sql="DELETE FROM sala WHERE id_sala='".$id."'";
        $stmt = oci_parse($this->con, $sql);
        $result = oci_execute($stmt);
        if ($result) {
            return $result;
        }else {
            return false;
        }
        
    }

}