<?php

namespace App\Session;

class SessionRecoverPassword{

    private static function init_session(){
        if(session_start()!= PHP_SESSION_ACTIVE):
            session_start();
        endif;

    }
    public static function session_recover($dataUser){
        //Iniciar sessao recover
        self::init_session();
        //Sessao de recuperacao de password
        $_SESSION['recover']=[
            'email' => $dataUser['data']['email'],
        ];
        return true;
    }



    public static function destroy_session_recover(){
        //Destroi sessao do usuario
        self::init_session();
        unset($_SESSION['recover']);
        return true;
    }
}