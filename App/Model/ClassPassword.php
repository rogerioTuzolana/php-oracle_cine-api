<?php

namespace App\Model;
use App\Model\ClassUser;

class ClassPassword {
    private $user;

    public function __construct(){

        $this->user=new ClassUser;
    }
    public function passwordHash($password){

        return password_hash($password, PASSWORD_DEFAULT);
    }

}