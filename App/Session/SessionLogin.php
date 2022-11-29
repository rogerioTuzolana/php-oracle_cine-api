<?php

namespace App\Session;

class SessionLogin{

    private static function init_session(){
        if(session_status()!= PHP_SESSION_ACTIVE):
            session_start();
        endif;
    }
    public static function session_login($dataUser){
        
        //Iniciar sessao login
        self::init_session();
        
        $_SESSION['user']=[
            'id' => $dataUser['ID_ADMIN'],
            'name' => $dataUser['NOME'],
            'email' => $dataUser['EMAIL'],
            'type' => "admin"
        ];
        return $_SESSION['user'];//$_SESSION['user'];
    }


    public static function isStudentLogged(){
        //Cria sessao login
        self::init_session();
        
        return isset($_SESSION['course']['student']['id']);
    }
    
    public static function isLogged(){
        //Cria sessao login
        self::init_session();
        
        return isset($_SESSION['user']['id']);
    }

    public static function session_logout(){
        //Destroi sessao do usuario
        self::init_session();
        unset($_SESSION['user']);
        return true;
    }

    public static function session_student_logout(){
        //Destroi sessao do usuario
        self::init_session();
        unset($_SESSION['course']['student']);
        return true;
    }
}