<?php

namespace App\Controllers;

use App\Model\ClassValidation;
use App\Session\SessionLogin;
use App\Model\ClassUser;
use App\Model\ClassReservation;
use App\Model\ClassFilm;
use App\Model\ClassSession;
use App\Model\ClassClient;
use App\Model\ClassCine;
use App\Model\ClassRoom;

use App\Http\Middleware\RequireAdminLogout;
use App\Utils\Render;

class AuthController extends Render{
    private $user;
    private $reservation;
    private $film;
    private $cine;
    private $session;
    private $client;
    private $room;
    private $middleware;
    private $is_logged;

    public function __construct(){
        
        $this->is_logged = RequireAdminLogout::middleware_logged();
        $this->user = new ClassUser;
        $this->reservation = new ClassReservation;
        $this->film=new ClassFilm;
        $this->cine=new ClassCine;
        $this->session=new ClassSession;
        $this->room=new ClassRoom;
        $this->client=new ClassClient;
        
    }

    public function verify_logged(){
        if ($this->is_logged == false) {
            $this->responseJson(["data"=>"Acesso negado"]);
            return;
        }else{
            $this->responseJson(["data"=>"Acesso permitido"]);
            
        }
    }
    public function login(){
        /*unset($_SESSION['user']);
        return;*/
        if ($this->is_logged) {
            $this->responseJson(["data"=>$_SESSION['user']]);
            return;
        }
        
        //Verifica senha do usuario
        if(empty($_POST['email']) || empty($_POST['pass'])){
            $this->responseJson(["data"=>"Senha ou email inválido"]);
            return;
        }

        $email=filter_input(INPUT_POST,'email',FILTER_VALIDATE_EMAIL)??'';

        $pass=filter_input(INPUT_POST,'pass',FILTER_SANITIZE_SPECIAL_CHARS);

        $dataUser=$this->user->getUser($email);
        if(empty($dataUser)){

            $this->responseJson(["data"=>"Usuário ou Senha inválida"]);
            return;
        }else{
            if($pass != $dataUser['SENHA']):
                $this->responseJson(["data"=>"Usuário ou Senha inválida"]);
                return;
            endif;
        }
        
        /*$this->responseJson(["data"=>$_SESSION]);
        unset($_SESSION['user']);
        return;*/

        //cria sessao de login para o usuario
        $session = SessionLogin::session_login($dataUser);
        //header('Location:'.DIRPAGE.'/admin/dash');
        $this->responseJson(["data"=>$session]);
        return;
        //print_r($_SESSION);
        
    }

    public function logout(){
        //destroi sessao de login
        $logout = SessionLogin::session_logout();
    
        $this->responseJson(["data"=>$logout]);
        return;
    }

    
    public function list_reservations(){
        /*if ($this->is_logged == false) {
            $this->responseJson(["data"=>"Acesso negado"]);
            return; 
        }*/

        $reservations = $this->reservation->getReservations();
        
        $this->responseJson(["data"=>$reservations]);
    }

    public function list_films(){
        /*if ($this->is_logged == false) {
            $this->responseJson(["data"=>"Acesso negado"]);
            return; 
        }*/
        $films = $this->film->getFilms();
        
        $this->responseJson(["data"=>$films]);
    }
    
    public function list_sessions(){
        /*if ($this->is_logged == false) {
            $this->responseJson(["data"=>"Acesso negado"]);
            return; 
        }*/
        $sessions = $this->session->getSessions();
        
        $this->responseJson(["data"=>$sessions]);
    }
    
    public function list_clients(){
        /*if ($this->is_logged == false) {
            $this->responseJson(["data"=>"Acesso negado"]);
            return; 
        }*/
        $sessions = $this->client->getClients();
        
        $this->responseJson(["data"=>$sessions]);
    }
    
    public function create_cine(){
        /*if ($this->is_logged == false) {
            $this->responseJson(["data"=>"Acesso negado"]);
            return; 
        }*/
        $request=[
            "name"=>$_POST["name"],
            "location"=>$_POST["location"],
            "id_admin"=>$_POST["id_admin"],
        ];
        $cine = $this->cine->create($request);
        //$this->responseJson(["data"=>$cines]);
        $this->responseJson(["data"=>$cine]);
    }

    public function update_cine($id_cinema){
        /*if ($this->is_logged == false) {
            $this->responseJson(["data"=>"Acesso negado"]);
            return; 
        }*/
        $request=[
            "name"=>$_POST["name"],
            "location"=>$_POST["location"],
            "id_admin"=>$_POST["id_admin"],
        ];
        $cine = $this->cine->update($request,$id_cinema);
        //$this->responseJson(["data"=>$cines]);
        $this->responseJson(["data"=>$cine]);
    }

    public function delete_cine($id_cinema){
        if ($this->is_logged == false) {
            $this->responseJson(["data"=>"Acesso negado"]);
            return; 
        }
        $cine = $this->cine->delete($id_cinema);
        //$this->responseJson(["data"=>$cines]);
        $this->responseJson(["data"=>$cine]);
    }

    public function create_film(){
        if ($this->is_logged == false) {
            $this->responseJson(["data"=>"Acesso negado"]);
            return; 
        }
        $request=[
            "name"=>$_POST["name"],
            "capa"=>$_POST["capa"],
            "gender"=>$_POST["gender"],
            "time"=>$_POST["time"],
            "description"=>$_POST["description"],
            "triller"=>$_POST["triller"],
            "id_admin"=>$_POST["id_admin"],
        ];
        $film = $this->film->create($request);
        //$this->responseJson(["data"=>$cines]);
        $this->responseJson(["data"=>$film]);
    }

    public function update_film($id_film){
        if ($this->is_logged == false) {
            $this->responseJson(["data"=>"Acesso negado"]);
            return; 
        }
        $request=[
            "name"=>$_POST["name"],
            "capa"=>$_POST["capa"],
            "gender"=>$_POST["gender"],
            "time"=>$_POST["time"],
            "description"=>$_POST["description"],
            "triller"=>$_POST["triller"],
            "id_admin"=>$_POST["id_admin"],
        ];
        $film = $this->film->update($request,$id_film);
        $this->responseJson(["data"=>$film]);
    }

    public function delete_film($id_film){
        if ($this->is_logged == false) {
            $this->responseJson(["data"=>"Acesso negado"]);
            return; 
        }
        $film = $this->film->delete($id_film);
        $this->responseJson(["data"=>$film]);
    }

    public function list_cines(){
        /*if ($this->is_logged == false) {
            $this->responseJson(["data"=>"Acesso negado"]);
            return; 
        }*/
        $cines = $this->cine->getCines();
        
        $this->responseJson(["data"=>$cines]);
    }

    public function update_reservation($id_reservation){
        /*if ($this->is_logged == false) {
            $this->responseJson(["data"=>"Acesso negado"]);
            return; 
        }*/
        $request=[
            "status"=>$_POST["status"],
            "token"=>$_POST["token"],
            "qtd"=>$_POST["qtd"],
            "position"=>$_POST["position"],
            "date"=>$_POST["date"],
            "hour"=>$_POST["hour"],
            "id_session"=>$_POST["id_session"],
            "id_cine"=>$_POST["id_cine"],
        ];
        $reservation = $this->reservation->update($request, $id_reservation);
        $this->responseJson(["data"=>$reservation]);
    }

    public function create_room(){
        /*if ($this->is_logged == false) {
            $this->responseJson(["data"=>"Acesso negado"]);
            return; 
        }*/
        $request=[
            "num_room"=>$_POST["num_room"],
            "qtd_place"=>$_POST["qtd_place"],
            "id_cine"=>$_POST["id_cine"],
            "id_admin"=>$_POST["id_admin"],
        ];
        $room = $this->room->create($request);
        $this->responseJson(["data"=>$room]);
    }

    public function update_room($id_room){
        /*if ($this->is_logged == false) {
            $this->responseJson(["data"=>"Acesso negado"]);
            return; 
        }*/
        $request=[
            "num_room"=>$_POST["num_room"],
            "qtd_place"=>$_POST["qtd_place"],
            "id_cine"=>$_POST["id_cine"],
            "id_admin"=>$_POST["id_admin"],
        ];
        $room = $this->room->update($request, $id_room);
        $this->responseJson(["data"=>$room]);
    }

    public function create_space(){
        /*if ($this->is_logged == false) {
            $this->responseJson(["data"=>"Acesso negado"]);
            return; 
        }*/
        $request=[
            "position"=>$_POST["position"],
            "status"=>$_POST["status"],
            "id_room"=>$_POST["id_room"],
        ];
        $room = $this->space->create($request);
        $this->responseJson(["data"=>$room]);
    }
}