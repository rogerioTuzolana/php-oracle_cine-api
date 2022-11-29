<?php

namespace App\Model;

class ClassConnection {
    //private static $con;
    public static function connect_db(){
        try {
            //$con = new \PDO("mysql:host=".HOST.";port=3306;dbname=".DB."","".USER."","".PASSWORD."");
            //return $con;
            $con = oci_connect(USER, PASS, DB);
            //if ($con) {
            return $con;
            /*}else{
                
                return null;
            }*/
            
            /*\PDOException */
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
        
    }
}

/*if($conexao):
    echo "certo";*/