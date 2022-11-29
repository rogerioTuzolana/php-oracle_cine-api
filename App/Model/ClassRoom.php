<?php

namespace App\Model;
use App\Model\ClassConnection;
use Dompdf\Options;

class ClassRoom {
    private $sql;
    private $con;

    public function __construct(){
        $this->sql=new ClassConnection;
        $this->con = $this->sql->connect_db();
    }

    /*public function getReservations(){  
 
        $sql = "SELECT r.id_reserva, r.estado as r_estado, r.token_id, r.qtd_lugar, r.posicao_lugar, r.data_reserva,
        s.id_sessao, s.preco, s.estado, s.data_sessao, s.hora, 
        s.id_sala, f.nome, f.capa, f.genero, f.duracao, f.descrisao, f.triller 
         FROM reserva r INNER JOIN sessao s ON r.id_sessao = s.id_sessao
         INNER JOIN filme f ON s.id_filme = f.id_filme";

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
    }*/

    public function create($request){
        
        $sql="INSERT INTO sala(id_sala, num_sala, qtd_lugar, id_cinema, id_admini) VALUES(SEQ_SALA.nextval, '".$request['num_room']."', '".$request['qtd_place']."', '".$request['id_cine']."', '".$request['id_admin']."')";
        $stmt = oci_parse($this->con, $sql);
        $result = oci_execute($stmt);
        if ($result) {
            return $result;
        }else {
            return false;
        }
        
    }

    public function update($request, $id_room){

        $sql="UPDATE sala SET num_sala='".$request['num_room']."', qtd_lugar='".$request['qtd_place']."', id_cinema='".$request['id_cine']."', id_admini='".$request['id_admin']."' WHERE id_sala='".$id_room."'";
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