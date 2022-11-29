<?php

namespace App\Controllers;

use App\Utils\Render;
use App\Model\ClassCourse;
use App\Model\ClassTest;
use App\Model\ClassFilm;
use App\Model\ClassSession;
use App\Model\ClassCine;
use App\Model\ClassRecoverPassword;
use App\Session\SessionLogin;
use App\Model\ClassReservation;
use App\Model\ClassSpace;

class HomeController extends Render{
    private $test;
    private $film;
    private $cine;
    private $session;
    private $space;

    public function __construct(){
        $this->test=new ClassTest;
        $this->film=new ClassFilm;
        $this->cine=new ClassCine;
        $this->session=new ClassSession;
        $this->reservation=new ClassReservation;
        $this->space=new ClassSpace;
    }

    
    public function index(){
        
        $test = $this->test->getTests();
        //$a=["a"=>1,"r"=>"r"];
        //var_dump(count($a));
        
        $this->responseJson(["data"=>$test]);
    }

    public function film($id){
        $films = $this->film->getFilmById($id);
        
        $this->responseJson(["data"=>$films]);
    }

    public function update_reservation(){
        $request=[
            "status"=>$_POST["status"],
            "token"=>$_POST["token"],
            "qtd"=>$_POST["qtd"],
            "position"=>$_POST["position"],
        ];

        $reservation = $this->reservation->update($request);
        //$this->responseJson(["data"=>$cines]);
        $this->responseJson(["data"=>$reservation]);
    }

    public function delete_reservation(){
        $request=[
            "token"=>$_POST["token"],
            "id_admin"=>$_POST["id_admin"],
        ];

        $reservation = $this->reservation->delete($request);
        //$this->responseJson(["data"=>$cines]);
        $this->responseJson(["data"=>$reservation]);
    }
    
    public function research_reservation($v_data_reserva){
        /*$request=[
            "v_data_reserva"=>$_GET["v_data_reserva"],
        ];*/
        //$this->responseJson(["data"=>$v_data_reserva]);return;
        $reservation = $this->reservation->research($v_data_reserva);
        //$this->responseJson(["data"=>$cines]);
        $this->responseJson(["data"=>$reservation]);
    }
    public function historic_reservation($email){
        
        $reservation = $this->reservation->historic($email);
        //$this->responseJson(["data"=>$cines]);
        $this->responseJson(["data"=>$reservation]);
    }
    public function create_reservation(){
        $request=[
            //"status"=>$_POST["status"],
            "token"=>$_POST["token"],
            "qtd"=>$_POST["qtd"],
            "position"=>$_POST["position"],
            //"date"=>$_POST["date"],
            //"hour"=>$_POST["hour"],
            "id_session"=>$_POST["id_session"],
            "id_cine"=>$_POST["id_cine"],
            //"id_place"=>$_POST["id_place"],
            "v_nome_cliente"=>$_POST["v_nome_cliente"],
            "v_tel_cliente"=>$_POST["v_tel_cliente"],
            "v_email_cliente"=>$_POST["v_email_cliente"]
        ];

        $reservation = $this->reservation->create($request);
        //$this->responseJson(["data"=>$cines]);
        $this->responseJson(["data"=>$reservation]);
    }

    public function list_space($id_sessao){
        $space = $this->space->getSpace($id_sessao);
        //$this->responseJson(["data"=>$cines]);
        $this->responseJson(["data"=>$space]);
    }

    public function have_space($id_cinema){
        $space = $this->space->getSpaceExist($id_cinema);
        //$this->responseJson(["data"=>$cines]);
        $this->responseJson(["data"=>$space]);
    }
    public function update_status_space($id_sala){
        
        //$this->responseJson(["data"=>$_POST["lugares"]]);
        $space = $this->space->updateStatusSpace($id_sala, $_POST["lugares"]);
        //$this->responseJson(["data"=>$cines]);
        $this->responseJson(["data"=>$space]);
    }
    
    public function create_client(){
        $request=[
            "name"=>$_POST["name"],
            "num_tel"=>$_POST["num_tel"],
            "email"=>$_POST["email"],
            "id_reservation"=>$_POST["id_reservation"],
        ];

        $client = $this->client->create($request);
        //$this->responseJson(["data"=>$cines]);
        $this->responseJson(["data"=>$client]);
    }

    public function list_films(){
        $films = $this->film->getFilms();
        
        $this->responseJson(["data"=>$films]);
    }
    
    public function list_films_cartaz(){
        $films = $this->film->getFilmsCartaz();
        
        $this->responseJson(["data"=>$films]);
    }
    
    public function films_exibition($date){
        $films = $this->film->getFilmsExib($date);
        
        $this->responseJson(["data"=>$films]);
    }
    public function show_films($date){
        $films = $this->film->getShowFilms($date);
        
        $this->responseJson(["data"=>$films]);
    }

    public function film_sessions($cine_id, $film_id){
        $session_film = $this->film->getSessionFilmId($cine_id, $film_id);
        
        $this->responseJson(["data"=>$session_film]);
    }

    public function list_sessions(){
        $sessions = $this->session->getSessions();
        
        $this->responseJson(["data"=>$sessions]);
    }
    
    public function list_film_sessions(){
        $sessions = $this->session->getFilmSessions();
        
        $this->responseJson(["data"=>$sessions]);
    }

    public function list_cines(){
        $cines = $this->cine->getCines();
        
        $this->responseJson(["data"=>$cines]);
    }

    
    /*public function postTest(){
        $request=[
            "first_name"=>$_POST["first_name"],
            "last_name"=>$_POST["last_name"],
            "gender"=>$_POST["gender"],
        ];
        
        $test = $this->test->post($request);    
        $this->responseJson(["data"=>$test]);
    }

    public function deleteTest($id){
        $test = $this->test->delete($id);
        $this->responseJson(["data"=>$test]);
    }

    public function putTest(){

        $request=[
            "id"=>$_POST["id"],
            "first_name"=>$_POST["first_name"],
            //"last_name"=>$_POST["last_name"],
        ];
  
        $test = $this->test->put($request);    
        $this->responseJson(["data"=>$test]);
    }

    public function get_films(){

    }
*/
}