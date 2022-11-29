<?php

namespace App\Core;
use \App\ajax\TimeUpdate;
use App\ajax\Uploads;


class RoutesCore {
    private $prefix;
    private $route;
    private $method;
    private $getArray=[];
    private $postArray=[];
    private $putArray=[];
    private $deleteArray=[];
    public function __construct(){
        header('Access-Control-Allow-Origin: *');
        /*header('Access-Control-Allow-Methods: GET, POST');
        header('Access-Control-Allow-Headers: X-Requested-With');*/

        $this->initRoute();
        require_once '../Config/routes.php'; 
        $this->executeRoute();
    }

    private function initRoute(){
        
        /*header('Access-Control-Allow-Methods: GET, POST');
        header('Access-Control-Allow-Headers: X-Requested-With');*/

        $this->method=$_SERVER['REQUEST_METHOD'];
        $uri=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
        $this->route=$this->config_route($uri);;
        //print_r($this->route);
    }
    private function config_route($str){
        $parseUrl=parse_url(DIRPAGE);
        $this->prefix = $parseUrl['path'] ?? '';
        $route=explode($this->prefix,$str);
        $route=end($route);
        return $route;
    }
    private function get($route,$callback){
        $this->getArray[]=[
            'route'=>$route,
            'callback'=>$callback
        ];
    }
    private function post($route,$callback){
        $this->postArray[]=[
            'route'=>$route,
            'callback'=>$callback
        ];
    }

    private function put($route,$callback){
        $this->putArray[]=[
            'route'=>$route,
            'callback'=>$callback
        ];
    }
    private function delete($route,$callback){
        $this->deleteArray[]=[
            'route'=>$route,
            'callback'=>$callback
        ];
    }
    private function executeRoute(){
        switch($this->method){
            case 'GET':
                $this->executeGet();
                break;
            case 'POST':
                $this->executePost();
                break;
            case 'PUT':
                $this->executePut();
                break;
            case 'DELETE':
                $this->executeDelete();
                break;
        }
    }
    private function executeGet(){
        $pre='';$suf='';$param_r=[];$param_uri=[];
        foreach($this->getArray as $get):
            $_route=$get['route'];
            if(substr($_route,-1)=='/' && strlen($_route)>1):
                $_route=substr($get['route'],0,-1);
                //echo $_route;
            endif;

            //explode para encontrar paremetro
            $param=explode('{', $_route);
            //verifica a rota definida com a rota uri
            if($_route==$this->route):
                if(is_callable($get['callback'])){
                    $get['callback']();
                    break;
                }else{
                    $this->config_controller($get['callback'],NULL);
                    break;
                }
            //se nao encontrou a rota verifica se tem parametro
            elseif (isset($param[1])):
                $pre=$param[0];
                $tam=strlen($pre);
                if(substr($this->route,0,$tam)==$pre){
                    //extrair parametros na rota
                    unset($param[0]);
                    $param=array_values(array_filter($param));
                    foreach($param as $p):
                        $param_r[]=explode('}', $p)[0];
                    endforeach;

                    //extrair os parametros na uri
                    $param_uri=substr($this->route,$tam);
                    $param_uri=explode('/',$param_uri);
                    if(empty($param_uri[0])){
                        unset($param_uri[0]);
                        unset($param_uri[count($param_uri)-1]);

                    }else if(empty($param_uri[count($param_uri)-1])){
                        unset($param_uri[count($param_uri)-1]);
                        $param_uri=array_values(array_filter($param_uri));
                    }
                    //verifica se rota definida e rota da uri tem o mesmo tam.
                    if(count($param_r)==count($param_uri)):
                        if(is_callable($get['callback'])){
                            call_user_func_array($get['callback'],$param_uri);
                            break;
                        }else{
                            $this->config_controller($get['callback'],$param_uri);
                            break;
                        }
                    endif;
                }
            endif;
        endforeach;
    }

    private function executeDelete(){
        $pre='';$suf='';$param_r=[];$param_uri=[];
        foreach($this->deleteArray as $delete):
            $_route=$delete['route'];
            if(substr($_route,-1)=='/' && strlen($_route)>1):
                $_route=substr($delete['route'],0,-1);
                //echo $_route;
            endif;

            //explode para encontrar paremetro
            $param=explode('{', $_route);
            //verifica a rota definida com a rota uri
            if($_route==$this->route):
                if(is_callable($delete['callback'])){
                    $delete['callback']();
                    break;
                }else{
                    $this->config_controller($delete['callback'],NULL);
                    break;
                }
            //se nao encontrou a rota verifica se tem parametro
            elseif (isset($param[1])):
                $pre=$param[0];
                $tam=strlen($pre);
                if(substr($this->route,0,$tam)==$pre){
                    //extrair parametros na rota
                    unset($param[0]);
                    $param=array_values(array_filter($param));
                    foreach($param as $p):
                        $param_r[]=explode('}', $p)[0];
                    endforeach;

                    //extrair os parametros na uri
                    $param_uri=substr($this->route,$tam);
                    $param_uri=explode('/',$param_uri);
                    if(empty($param_uri[0])){
                        unset($param_uri[0]);
                        unset($param_uri[count($param_uri)-1]);

                    }else if(empty($param_uri[count($param_uri)-1])){
                        unset($param_uri[count($param_uri)-1]);
                        $param_uri=array_values(array_filter($param_uri));
                    }
                    //verifica se rota definida e rota da uri tem o mesmo tam.
                    if(count($param_r)==count($param_uri)):
                        if(is_callable($delete['callback'])){
                            call_user_func_array($delete['callback'],$param_uri);
                            break;
                        }else{
                            $this->config_controller($delete['callback'],$param_uri);
                            break;
                        }
                    endif;
                }
            endif;
        endforeach;
    }

    private function executePut(){
        $pre='';$suf='';$param_r=[];$param_uri=[];
        foreach($this->putArray as $put):
            $_route=$put['route'];
            if(substr($_route,-1)=='/' && strlen($_route)>1):
                $_route=substr($put['route'],0,-1);
                //echo $_route;
            endif;

            //explode para encontrar paremetro
            $param=explode('{', $_route);
            //verifica a rota definida com a rota uri
            if($_route==$this->route):
                if(is_callable($put['callback'])){
                    $put['callback']();
                    break;
                }else{
                    $this->config_controller($put['callback'],NULL);
                    break;
                }
            //se nao encontrou a rota verifica se tem parametro
            elseif (isset($param[1])):
                $pre=$param[0];
                $tam=strlen($pre);
                if(substr($this->route,0,$tam)==$pre){
                    //extrair parametros na rota
                    unset($param[0]);
                    $param=array_values(array_filter($param));
                    foreach($param as $p):
                        $param_r[]=explode('}', $p)[0];
                    endforeach;

                    //extrair os parametros na uri
                    $param_uri=substr($this->route,$tam);
                    $param_uri=explode('/',$param_uri);
                    if(empty($param_uri[0])){
                        unset($param_uri[0]);
                        unset($param_uri[count($param_uri)-1]);

                    }else if(empty($param_uri[count($param_uri)-1])){
                        unset($param_uri[count($param_uri)-1]);
                        $param_uri=array_values(array_filter($param_uri));
                    }
                    //verifica se rota definida e rota da uri tem o mesmo tam.
                    if(count($param_r)==count($param_uri)):
                        if(is_callable($put['callback'])){
                            call_user_func_array($put['callback'],$param_uri);
                            break;
                        }else{
                            $this->config_controller($put['callback'],$param_uri);
                            break;
                        }
                    endif;
                }
            endif;
        endforeach;
    }

    private function executePost(){
        foreach($this->postArray as $post):
            $_route=$post['route'];
            if(substr($_route,-1)=='/'):
                $_route=substr($post['route'],0,-1);
            endif;

            //explode para encontrar paremetro
            $param=explode('{', $_route);
            //verifica a rota definida com a rota uri
            if($_route==$this->route):
                if(is_callable($post['callback'])){
                    $post['callback']();
                    break;
                }else{
                    $this->config_controller($post['callback'],NULL);
                    break;
                }
            //se nao encontrou a rota verifica se tem parametro
            elseif (isset($param[1])):
                $pre=$param[0];
                $tam=strlen($pre);
                if(substr($this->route,0,$tam)==$pre){
                    //extrair parametros na rota
                    unset($param[0]);
                    $param=array_values(array_filter($param));
                    foreach($param as $p):
                        $param_r[]=explode('}', $p)[0];
                    endforeach;

                    //extrair os parametros na uri
                    $param_uri=substr($this->route,$tam);
                    $param_uri=explode('/',$param_uri);
                    if(empty($param_uri[0])){
                        unset($param_uri[0]);
                        unset($param_uri[count($param_uri)-1]);

                    }else if(empty($param_uri[count($param_uri)-1])){
                        unset($param_uri[count($param_uri)-1]);
                        $param_uri=array_values(array_filter($param_uri));
                    }
                    //verifica se rota definida e rota da uri tem o mesmo tam.
                    if(count($param_r)==count($param_uri)):
                        if(is_callable($post['callback'])){
                            call_user_func_array($post['callback'],$param_uri);
                            break;
                        }else{
                            $this->config_controller($post['callback'],$param_uri);
                            break;
                        }
                    endif;
                }
            endif;
            
        endforeach;
    }
    private function config_controller($str,$param=[]){ 

        $str=explode('@',$str);
        if(isset($str[0]) && isset($str[1])){ 
            $name_space="App\\Controllers\\{$str[0]}";
            if (!class_exists($name_space)) {
                echo "<h4>Controlador nao encontrado!</h4>";
                return;
            }
            if(!method_exists($name_space,$str[1])){
                echo "<h4>Metodo nao encontrado!</h4>";
                return;
            }
            if (empty($param)) {
                call_user_func_array([new $name_space,$str[1]],[]);
            }else{
                call_user_func_array([new $name_space,$str[1]],$param);
            }
        }else if (isset($str[0])){
            $name_space="App\\Controllers\\{$str[0]}";
            if (!class_exists($name_space)) {
                echo "<h4>Controlador nao encontrado!</h4>";
                return;
            }
            return (new $name_space);
        }else{

            echo "<h4>Metodo e controlador nao encontrado!</h4>";
        }
    }
}