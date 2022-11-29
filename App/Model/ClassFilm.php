<?php

namespace App\Model;
use App\Model\ClassConnection;
use Dompdf\Options;

class ClassFilm {
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

    public function getSessionFilmId($cine_id, $film_id){
        //$sql = "SELECT * FROM sessao s, sala WHERE s.id_filme='".$id."'";
        $sql = "SELECT se.id_sessao, se.preco, se.estado, se.data_sessao, se.hora, 
        s.id_sala, s.id_sala, s.num_sala,
        cin.nome as nome_cin 
        FROM cinema cin INNER JOIN sala s ON cin.id_cinema = s.id_cinema
        INNER JOIN sessao se ON se.id_sala = s.id_sala WHERE cin.id_cinema = '$cine_id' AND se.id_filme = '$film_id'";
        //$sql = "SELECT f.nome, s.preco FROM filme f, sessao s WHERE f.id_filme = s.id_filme";
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

    public function getShowFilms($date){   
        $sql = 'BEGIN pkg_filmes.filmes_exibicao(:DATA,:E_DADOS); END;';
			
        $curs = oci_new_cursor($this->con);
        $stmt = oci_parse($this->con, $sql);	
        oci_bind_by_name($stmt,":DATA",$date);
        oci_bind_by_name($stmt,":E_DADOS",$curs,-1,OCI_B_CURSOR);
        oci_execute($stmt);
        oci_execute($curs);
        $result=oci_fetch_all($curs,$data,null,null,OCI_FETCHSTATEMENT_BY_ROW);
        if(count($data) > 0):
            return  $data;
        else:
            return [];
        endif;

        /*$sql = "SELECT * FROM filme";
        //$sql = "SELECT f.nome, s.preco FROM filme f, sessao s WHERE f.id_filme = s.id_filme";
        $stmt = oci_parse($this->con, $sql);
        oci_execute($stmt);
        
        //$result=oci_fetch_array($stmt, OCI_ASSOC);
        $result=oci_fetch_all($stmt,$data,null,null,OCI_FETCHSTATEMENT_BY_ROW);
        
        if(count($data) > 0):
            return  $data;
        else:
            return [];
        endif;*/
    }

    public function getFilms(){   
        $sql = 'BEGIN pkg_filmes.filmes(:DADOS); END;';
			
        $curs = oci_new_cursor($this->con);
        $stmt = oci_parse($this->con, $sql);		
        oci_bind_by_name($stmt,":DADOS",$curs,-1,OCI_B_CURSOR);
        oci_execute($stmt);
        oci_execute($curs);
        $result=oci_fetch_all($curs,$data,null,null,OCI_FETCHSTATEMENT_BY_ROW);

        if(count($data) > 0):
            return  $data;
        else:
            return [];
        endif;

        /*$sql = "SELECT * FROM filme";
        //$sql = "SELECT f.nome, s.preco FROM filme f, sessao s WHERE f.id_filme = s.id_filme";
        $stmt = oci_parse($this->con, $sql);
        oci_execute($stmt);
        
        //$result=oci_fetch_array($stmt, OCI_ASSOC);
        $result=oci_fetch_all($stmt,$data,null,null,OCI_FETCHSTATEMENT_BY_ROW);
        
        if(count($data) > 0):
            return  $data;
        else:
            return [];
        endif;*/
    }

    public function getFilmsCartaz(){   
        $sql = 'BEGIN :DADOS_FILME := pkg_filmes.filmes_cartaz; END;';
			
        $dados_filme = oci_new_cursor($this->con);
        $stmt = oci_parse($this->con, $sql);		
        oci_bind_by_name($stmt,":DADOS_FILME",$dados_filme,-1,OCI_B_CURSOR);
        oci_execute($stmt);
        oci_execute($dados_filme);
        $result=oci_fetch_all($dados_filme,$data,null,null,OCI_FETCHSTATEMENT_BY_ROW);
        //return oci_fetch_array($dados_filme);
        if(count($data) > 0):
            return  $data;
        else:
            return [];
        endif;

    }

    public function getFilmsExib($v_data_sessao){   
        $sql = 'BEGIN :DADOS_FILME := pkg_filmes.filmes_exibicao(:V_DATA_SESSAO); END;';
			
        $dados_filme = oci_new_cursor($this->con);
        $stmt = oci_parse($this->con, $sql);

        oci_bind_by_name($stmt,':V_DATA_SESSAO', $v_data_sessao);		
        oci_bind_by_name($stmt,":DADOS_FILME",$dados_filme,-1,OCI_B_CURSOR);
        oci_execute($stmt);
        oci_execute($dados_filme);
        $result=oci_fetch_all($dados_filme,$data,null,null,OCI_FETCHSTATEMENT_BY_ROW);
        //return oci_fetch_array($dados_filme);
        if(count($data) > 0):
            return  $data;
        else:
            return [];
        endif;

    }

    public function create($request){

        $sql="INSERT INTO filme(id_filme, nome, capa, genero, duracao, descrisao, triller, id_admini) VALUES(SEQ_FILME.nextval, '".$request['name']."', '".$request['capa']."', '".$request['gender']."', '".$request['time']."', '".$request['description']."', '".$request['triller']."', '".$request['id_admin']."')";
        $stmt = oci_parse($this->con, $sql);
        oci_execute($stmt);
        
        
        $sql="SELECT MAX(id_filme) FROM filme";

        $stmt = oci_parse($this->con, $sql);
        oci_execute($stmt);
        $result=oci_fetch_array($stmt, OCI_ASSOC);

        if(count($result) > 0):
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