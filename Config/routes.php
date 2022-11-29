<?php


//Rotas do tipo Get

//$this->get("/",'HomeController@index');
//$this->get("/home",'HomeController@index');
$this->get("/register",'TestController@register');

$this->get("/test",'TestController@index');
$this->get("/",'HomeController@index');
$this->get("/home",'HomeController@index');

$this->post("/post",'HomeController@postTest');
$this->post("/put",'HomeController@putTest');
$this->delete("/delete/{id}",'HomeController@deleteTest');

//Rotas do projecto

//Rotas do cliente
$this->get("/filmes",'HomeController@list_films');
$this->get("/filmes-cartaz",'HomeController@list_films_cartaz');

$this->get("/filmes-exibicao/{date}",'HomeController@films_exibition');
$this->get("/sessoes",'HomeController@list_sessions');
$this->get("/filmes-cartaz-sessoes",'HomeController@list_film_sessions');
$this->get("/cines",'HomeController@list_cines');
$this->get("/filme/{id}",'HomeController@film');
$this->get("/filme-sessoes/{cine_id}/{film_id}",'HomeController@film_sessions');
$this->post("/criar-reserva",'HomeController@create_reservation');
$this->post("/eliminar-reserva",'HomeController@delete_reservation');
$this->get("/historico-reserva/{email}",'HomeController@historic_reservation');
$this->post("/atualizar-reserva",'HomeController@update_reservation');
$this->get("/consultar-reserva/{data}",'HomeController@research_reservation');
$this->get("/lugar-sala/{id_sessao}",'HomeController@list_space');
$this->get("/tem-lugar/{id_cinema}",'HomeController@have_space');
$this->post("/estado-lugar/{id_sala}",'HomeController@update_status_space');


//Rotas do Administrador
$this->post("/admin/logout",'AuthController@logout');
$this->post("/admin/login",'AuthController@login');
$this->get("/admin/reservas",'AuthController@list_reservations');
$this->get("/admin/filmes",'AuthController@list_films');
$this->get("/admin/sessoes",'AuthController@list_sessions');
$this->get("/admin/clientes",'AuthController@list_clients');
$this->get("/admin/cines",'AuthController@list_cines');
$this->post("/admin/criar-cinema",'AuthController@create_cine');
$this->post("/admin/editar-cinema/{id}",'AuthController@update_cine');
$this->delete("/admin/apagar-cinema/{id}",'AuthController@delete_cine');
$this->post("/admin/criar-filme",'AuthController@create_film');
$this->post("/admin/editar-filme/{id}",'AuthController@update_film');
$this->delete("/admin/apagar-filme/{id}",'AuthController@delete_film');
$this->post("/admin/editar-reserva/{id}",'AuthController@update_reservation');
$this->post("/admin/criar-sala",'AuthController@create_room');
$this->post("/admin/editar-sala/{id}",'AuthController@update_room');
$this->post("/admin/apagar-sala/{id}",'AuthController@delete_room');
$this->post("/admin/criar-lugar",'AuthController@create_space');
/*
$this->get("/login",'HomeController@login');
$this->get("/pedir-senha",'HomeController@sendEmailForgotPassword');
$this->post("/sendEmailForgotPasswordStudent",'HomeController@sendEmailForgotPasswordStudent');
$this->get("/trocar-senha/{hash}",'HomeController@forgotPassword');
$this->post("/forgotPasswordStudent",'HomeController@forgotPasswordStudent');
$this->get("/cursos",'HomeController@courses');

$this->get("/curso-online",'StudentController@courseOnline');
$this->post("/course-logout",'StudentController@courseOnlineLogout');
$this->post("/accountStudent",'HomeController@accountStudent');
$this->get("/meus-cursos",'StudentController@myCourses');
$this->get("/meus-cursos/{user_id}/{referencia}",'StudentController@watchCourses');
$this->get("/mais",'StudentController@myCertification');
$this->get("/meus-cursos2/{referencia}",'HomeController@watchCourses');
$this->get("/meus_cursos/{user_id}/{referencia}/{id}",'StudentController@watchTeachingCourse');
$this->get("/teste-vidio/{referencia}/{id}",'StudentController@testVideoCourse');
$this->post("/teste-vidio/enviado",'StudentController@sendTestVideoCourse');
$this->post("/curso-online/cadastramento",'StudentController@registerStudent');
$this->post("/home/cadastramento",'HomeController@registerStudentHome');
$this->post("/curso-online/matricular",'HomeController@registerStudentInClass');
$this->post("/confirmAccess",'StudentController@confirmAccess');
$this->post("/confirmAccesshome",'HomeController@confirmAccesshome');

$this->get("/admin/registar-utilizador",'AdminRegisterController@index');
$this->get("/admin/utilizadores",'AdminRegisterController@users');
$this->get("/admin/editar-utilizador/{id}",'AdminRegisterController@editUser');
$this->post("/admin/updateUser/{id}",'AdminRegisterController@updateUser');
$this->post("/admin/createUser",'AdminRegisterController@createUser');
$this->get("/admin/deleteUser/{id}",'AdminRegisterController@deleteUser');

$this->get("/admin/login",'AdminLoginController@index');
//$this->post("/admin-logout",'AdminLoginController@logout');
//$this->post("/admin/login",'AdminLoginController@login');
$this->get("/admin/dash",'DashController@index');
$this->get("/admin/cursos",'DashController@courses');
$this->get("/admin/adicionar-curso",'DashController@addCourse');
$this->get("/admin/editar-curso/{id}",'DashController@editCourse');
$this->post("/admin/createCourse",'DashController@createCourse');
$this->get("/admin/deleteCourse/{id}",'DashController@deleteCourse');
$this->post("/admin/updateCourse/{id}",'DashController@updateCourse');

$this->get("/admin/aulas",'DashController@teachings');
$this->get("/admin/adicionar-aula",'DashController@addTeaching');
$this->get("/admin/editar-aula/{id}",'DashController@editTeaching');
$this->post("/admin/createTeaching",'DashController@createTeaching');
$this->get("/admin/deleteTeaching/{id}",'DashController@deleteTeaching');
$this->post("/admin/updateTeaching/{id}",'DashController@updateTeaching');

$this->get("/admin/testes",'DashController@tests');
$this->get("/admin/adicionar-teste",'DashController@addTest');
$this->get("/admin/editar-teste/{id}",'DashController@editTest');
$this->post("/admin/createTest",'DashController@createTest');
$this->get("/admin/deleteTest/{id}",'DashController@deleteTest');
$this->post("/admin/updateTest/{id}",'DashController@updateTest');

$this->get("/admin/inscritos",'DashController@students');
$this->post("/sendEmail",'HomeController@sendEmail');
$this->post("/sendEmailQuestion",'HomeController@sendEmailQuestion');

$this->get("/ajax/{e}",'StudentController@ajax');

$this->get("/admin/pagamentos",'DashController@payments');
$this->post("/admin/validationPayment/{id}",'DashController@validationPayment');

$this->get("/admin/certificados",'DashController@certification');
$this->get("/admin/adicionar-certificado",'DashController@addCertification');
$this->get("/admin/editar-certificado/{id}",'DashController@editCertification');
$this->post("/admin/createCertification",'DashController@createCertification');
$this->get("/admin/deleteCertification/{id}",'DashController@deleteCertification');
$this->post("/admin/updateCertification/{id}",'DashController@updateCertification');

$this->get("/admin/noticias",'DashController@news');
$this->get("/admin/adicionar-noticia",'DashController@addNews');
$this->get("/admin/editar-noticia/{id}",'DashController@editNews');
$this->post("/admin/createNews",'DashController@createNews');
$this->get("/admin/deleteNews/{id}",'DashController@deleteNews');
$this->post("/admin/updateNews/{id}",'DashController@updateNews');*/

//$this->get("/registar",'AdminRegisterController@store');
//$this->get("/registar",'AdminRegisterController@store');

$this->get("/exemplo", function(){
    echo "Estamos na home!";
});
