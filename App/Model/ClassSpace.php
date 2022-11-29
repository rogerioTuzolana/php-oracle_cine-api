<?php

namespace App\Model;
use App\Model\ClassConnection;
use Dompdf\Options;

class ClassSpace {
    private $sql;
    private $con;

    public function __construct(){
        $this->sql=new ClassConnection;
        $this->con = $this->sql->connect_db();
    }

    public function getFilmById($id){
        $sql = 'BEGIN pkg_filmes.buscar_filme(:ID_FILME, :B_DADOS); END;';
			
        $curs = oci_new_cursor($this->con);
        $stmt = oci_parse($this->con, $sql);												
        oci_bind_by_name($stmt,":ID_FILME",$id);
        oci_bind_by_name($stmt,":B_DADOS",$curs,-1,OCI_B_CURSOR);
        oci_execute($stmt);
        oci_execute($curs);
        //$result=oci_fetch_all($curs,$data,null,null,OCI_FETCHSTATEMENT_BY_ROW);
        $data = oci_fetch_array($curs);

        if(count($data) > 0):
            return  $data;
        else:
            return [];
        endif;
        /*$sql = "SELECT * FROM filme WHERE id_filme='".$id."'";
        
        $stmt = oci_parse($this->con, $sql);
        oci_execute($stmt);

        $result=oci_fetch_all($stmt,$data,null,null,OCI_FETCHSTATEMENT_BY_ROW);
        return $data;
        if(count($data) > 0):
            return  $data;
        else:
            return [];
        endif;*/
    }

    public function getSpaceExist($id_cinema){
        $sql = 'BEGIN :DADOS_LUGAR := pkg_filmes.tem_lugar(:V_ID_CINEMA); END;';

        
        $stmt = oci_parse($this->con, $sql);
        $dados_lugar = oci_new_cursor($this->con);
        oci_bind_by_name($stmt,':V_ID_CINEMA', $id_cinema);		
        oci_bind_by_name($stmt,':DADOS_LUGAR',$dados_lugar,-1,OCI_B_CURSOR);
        oci_execute($stmt);
        oci_execute($dados_lugar);
        $result=oci_fetch_all($dados_lugar,$data,null,null,OCI_FETCHSTATEMENT_BY_ROW);
        //return oci_fetch_array($dados_filme);
        if(count($data) > 0):
            return  $data;
        else:
            return [];
        endif;

    }
    public function getSpace($id_sessao){
        $sql = "SELECT se.id_sessao, 
        s.id_sala, s.num_sala,
        lg.id_lugar, lg.posicao, lg.estado 
        FROM sessao se INNER JOIN sala s ON se.id_sala = s.id_sala
        INNER JOIN lugar lg ON lg.id_sala = s.id_sala WHERE se.id_sessao = '$id_sessao'";
			
        $stmt = oci_parse($this->con, $sql);												
        oci_execute($stmt);
        $result=oci_fetch_all($stmt,$data,null,null,OCI_FETCHSTATEMENT_BY_ROW);

        if(count($data) > 0):
            return  $data;
        else:
            return [];
        endif;
    }

    public function updateStatusSpace($id_sala, $ids_lugares){
        $lugares = json_decode($ids_lugares);
        /*if (count($lugares)) {
            # code...
        }
        return count($lugares);*/
        //return $lugares;
        $result = false;
        foreach ($lugares as $key => $id_lugar) {
            $id = intval($id_lugar);
            $sql = "UPDATE lugar SET estado = 1 WHERE id_sala = '$id_sala' and id_lugar = '$id'";
			
            $stmt = oci_parse($this->con, $sql);												
            $result = oci_execute($stmt);

        }											     
        return $result;
        /*if(oci_execute($stmt)):
            return  true;
        else:
            return [];
        endif;*/
    }

    public function create($request){

        $sql="INSERT INTO lugar(id_lugar, posicao, estado, id_sala) VALUES(SEQ_LUGAR.nextval, '".$request['position']."', '".$request['status']."', '".$request['id_sala']."'";
        $stmt = oci_parse($this->con, $sql);
        $result = oci_execute($stmt);
        

        if($result):
            return  $result;
        else:
            return [];
        endif;
    }

    public function update($request, $id_film){

        $sql="UPDATE filme SET nome='".$request['name']."', capa='".$request['capa']."' , genero ='".$request['gender']."', duracao='".$request['time']."', descrisao='".$request['description']."', triller='".$request['triller']."', id_admini='".$request['id_admin']."' WHERE id_filme = '$id_film'";
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