<?php

namespace App\Http\Middleware;
use App\Session\SessionLogin;

class RequireAdminLogout{
    public static function middleware_logged(){
        if(SessionLogin::isLogged()):
            
            return true;
        else:
            return false;
        endif;
    }
}