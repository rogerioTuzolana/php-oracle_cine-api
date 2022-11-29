<?php
//Diretorios raizes(interno e servidor)
$directory_into="cinema";
define('DIRPAGE',"http://{$_SERVER['HTTP_HOST']}/{$directory_into}");

if(substr($_SERVER['DOCUMENT_ROOT'],-1)=='/'){
    define('DIRRZ',"{$_SERVER['DOCUMENT_ROOT']}{$directory_into}");
}else{
    define('DIRRZ',"{$_SERVER['DOCUMENT_ROOT']}/{$directory_into}/");
}
//Estilizacao
define('DIRASSETS',"http://{$_SERVER['HTTP_HOST']}/{$directory_into}/Public/assets");
define('DIRCSS',"http://{$_SERVER['HTTP_HOST']}/{$directory_into}/Public/css/");
define('DIRPLUG',"http://{$_SERVER['HTTP_HOST']}/{$directory_into}/Public/admin/");
//Js
define('DIRJS',"http://{$_SERVER['HTTP_HOST']}/{$directory_into}/Public/js/");
//imagens
define('DIRIMG',"http://{$_SERVER['HTTP_HOST']}/{$directory_into}/Public/img/");
//video
define('DIRVID',"http://{$_SERVER['HTTP_HOST']}/{$directory_into}/Public/video/");
//config acesso ao banco de dados
define('HOST', 'localhost');
define('DB', 'localhost/xe');
define('PORT', 1521);
define('USER', 'cin');
define('PASS', 942745025);

