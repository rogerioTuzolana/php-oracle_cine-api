<?php

namespace App\Model;
use App\Model\ClassPassword;


class ClassValidation {
    private $password;

    public function __construct(){

    }

    /*public function validation_login($email,$user_password){
        $pass_user_exist=$this->password_valid->validate_password($email,$user_passord);
        if($pass_user_exist==true):
            $token=bin2hex(random_bytes(64));
            $_SESSION['user']=$login->getEmail();
            $_SESSION['start']=time();
            $_SESSION['expire']=$_SESSION['start']+(120*20);
            echo"<br/><h1>{$token}</h1>";
            echo "<br/>User:<h1>{$_SESSION['user']}</h1>";
            echo "<br/>Tempo:<h1>{$_SESSION['start']}</h1>";
            header('Location:'.DIRPAGE.'dashboard-admin');
        else:
            //echo "<br/><h1>{$_SESSION['user']}</h1>";
            echo "<h5><br/>Usuária ou senha inválida<h5/>";
        endif;
    }*/
}