<?php

namespace App\Model;
use App\Model\ClassConnection;
use Dompdf\Options;

class ClassSession {
    private $sql;
    private $con;

    public function __construct(){
        $this->sql=new ClassConnection;
        $this->con = $this->sql->connect_db();
    }

    public function getSessions(){     
        $sql = "SELECT * FROM sessao, filme,sala WHERE sessao.id_filme = filme.id_filme and sala.id_sala = sessao.id_sala";
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

    public function getFilmSessions(){     
        $sql = "SELECT s.id_sessao, s.preco, s.estado, s.data_sessao, s.hora, 
        s.id_sala, f.nome, f.capa, f.genero, f.duracao, f.descrisao, f.triller 
         FROM filme f INNER JOIN sessao s ON s.id_filme = f.id_filme";

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

        $sql="INSERT INTO sessao(id_filme, titulo, capa, duracao, descricao, triller, id_admini) VALUES(SEQ_CINEMA.nextval, '".$request['titulo']."', '".$request['capa']."', '".$request['duracao']."', '".$request['descricao']."', '".$request['triller'].", '".$request['id_admini']."'')";
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

    public function update($request, $id_filme){
        $titulo = $request['titulo'];
        $capa = $request['capa'];
        $duracao = $request['duracao'];
        $descricao = $request['descricao'];
        $triller = $request['triller'];
        $id_admini = $request['id_admini'];

        $sql="UPDATE filme SET titulo='$first_name' AND titulo='$titulo' AND capa='$capa' AND duracao='$duracao' AND triller='$triller' AND id_admini='$id_admini' WHERE id_filme = '".$id_filme."'";
        $stmt = oci_parse($this->con, $sql);
        $result = oci_execute($stmt);
        if ($result) {
            return $result;
        }else {
            return false;
        }
        
    }

    public function delete($id){

        $sql="DELETE FROM filme WHERE id_filme='".$id."'";
        $stmt = oci_parse($this->con, $sql);
        $result = oci_execute($stmt);
        if ($result) {
            return $result;
        }else {
            return false;
        }
    }

}