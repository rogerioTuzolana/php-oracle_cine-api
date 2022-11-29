<?php

namespace App\Model;
use App\Model\ClassConnection;
use Dompdf\Options;

class ClassReservation {
    private $sql;
    private $con;

    public function __construct(){
        $this->sql=new ClassConnection;
        $this->con = $this->sql->connect_db();
    }

    public function getReservations(){  
 
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
    }

    public function create($request){

        $sql = 'BEGIN pkg_reserva.efectuar_reserva(:V_TOKEN_ID, :V_QTD_LUGAR, :V_POSICAO_LUGAR, :V_ID_SESSAO, :V_ID_CINEMA, :V_NOME_CLIENTE, :V_TEL_CLIENTE, :V_EMAIL_CLIENTE); END;';
        
        $stmt = oci_parse($this->con, $sql);
        //oci_bind_by_name($stmt,':V_ESTADO',$request['status']);
        oci_bind_by_name($stmt,':V_TOKEN_ID', $request['token']);			
        oci_bind_by_name($stmt,':V_QTD_LUGAR', $request['qtd']);
        oci_bind_by_name($stmt,':V_POSICAO_LUGAR', $request['position']);
        //oci_bind_by_name($stmt,':V_DATA_RESERVA', $request['date']);
        //oci_bind_by_name($stmt,':V_HORA', $request['hour']);					
        oci_bind_by_name($stmt,':V_ID_SESSAO', $request['id_session']);			
        oci_bind_by_name($stmt,':V_ID_CINEMA', $request['id_cine']);			
        oci_bind_by_name($stmt,':V_NOME_CLIENTE', $request['v_nome_cliente']);			
        oci_bind_by_name($stmt,':V_TEL_CLIENTE', $request['v_tel_cliente']);			
        oci_bind_by_name($stmt,':V_EMAIL_CLIENTE', $request['v_email_cliente']);			
        //oci_bind_by_name($stmt,':query', $query);
        /*oci_execute($stmt);

        $sql="INSERT INTO reserva(id_reserva, estado, token_id, qtd_lugar, posicao_lugar, data_reserva, hora, id_sessao, id_cinema) VALUES(SEQ_RESERV.nextval, '".$request['status']."', '".$request['token']."', '".$request['qtd']."', '".$request['position']."', TO_DATE('".$request['date']."', 'DD-MM-YYYY'), '".$request['hour']."', '".$request['id_session']."', '".$request['id_cine']."')";
        $stmt = oci_parse($this->con, $sql);*/
        $result = oci_execute($stmt);
        if ($result) {
            return $result;
        }else {
            return false;
        }
        
    }

    public function update($request){

        $sql = 'BEGIN pkg_reserva.actualizar_reserva(:V_TOKEN_ID, :V_ESTADO, :V_QTD_LUGAR, :V_POSICAO_LUGAR); END;';
        
        $stmt = oci_parse($this->con, $sql);
        //
        oci_bind_by_name($stmt,':V_TOKEN_ID', $request['token']);
        oci_bind_by_name($stmt,':V_ESTADO',$request['status']);			
        oci_bind_by_name($stmt,':V_QTD_LUGAR', $request['qtd']);
        oci_bind_by_name($stmt,':V_POSICAO_LUGAR', $request['position']);

        $result = oci_execute($stmt);
        if ($result) {
            return $result;
        }else {
            return false;
        }
        
    }

    public function research($v_data_reserva){

        $sql = 'BEGIN :DADOS_RESERVAS := pkg_reserva.consultar_reserva(:V_DATA_RESERVA); END;';

        $dados_reservas = oci_new_cursor($this->con);
        $stmt = oci_parse($this->con, $sql);

        oci_bind_by_name($stmt,':V_DATA_RESERVA', $v_data_reserva);		
        oci_bind_by_name($stmt,":DADOS_RESERVAS",$dados_reservas,-1,OCI_B_CURSOR);
        oci_execute($stmt);
        oci_execute($dados_reservas);
        $result=oci_fetch_all($dados_reservas,$data,null,null,OCI_FETCHSTATEMENT_BY_ROW);
        //return oci_fetch_array($dados_filme);
        if(count($data) > 0):
            return  $data;
        else:
            return [];
        endif;

    }

    public function historic($email){
        $sql = 'BEGIN :DADOS_HISTORICO := f_historico(:V_EMAIL_CLIIENTE); END;';

        $dados_historico = oci_new_cursor($this->con);
        $stmt = oci_parse($this->con, $sql);

        oci_bind_by_name($stmt,':V_EMAIL_CLIIENTE', $email);		
        oci_bind_by_name($stmt,":DADOS_HISTORICO",$dados_historico,-1,OCI_B_CURSOR);
        oci_execute($stmt);
        oci_execute($dados_historico);
        $result=oci_fetch_all($dados_historico,$data,null,null,OCI_FETCHSTATEMENT_BY_ROW);
        //return oci_fetch_array($dados_filme);
        if(count($data) > 0):
            return  $data;
        else:
            return [];
        endif;

    }

    public function delete($request){
        //return $request['token'];
        $sql = 'BEGIN pkg_reserva.eliminar_reserva(:TOKEN, :V_ID_ADMINI); END;';
        $stmt = oci_parse($this->con, $sql);	
        oci_bind_by_name($stmt,":TOKEN",$request['token']);
        oci_bind_by_name($stmt,":V_ID_ADMINI",$request['id_admin']);
        //oci_bind_by_name($stmt,":E_DADOS",$curs,-1,OCI_B_CURSOR);
        
        if(oci_execute($stmt)):
            return  true;
        else:
            return [];
        endif;
    }
    /*public function delete($id){

        $sql="DELETE FROM reserva WHERE id_reserva='".$id."'";
        $stmt = oci_parse($this->con, $sql);
        $result = oci_execute($stmt);
        if ($result) {
            return $result;
        }else {
            return false;
        }
    }*/

}