<?php
namespace App\Utils;

class Render{
    private $var;
    private $filedir;
    public function __construct()
    {

    }

    public function responseJson($data){
        /*$this->var = $arr;
        $this->filedir = $filedir;

        if(file_exists("../Src/views".$layout.".phtml")){
            include_once "../Src/views".$layout.".phtml";
        }else{
            $this->content(); 
        }*/
        header('Content-type: application/json');
        echo json_encode($data);
    }

    public function content(){
        include_once "../Src/views".$this->filedir.".phtml";
    }

}