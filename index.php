<?php 

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app = new Slim();

$app->config('debug', true);


// home
$app->get('/', function() {
    
	$page = new Page();

	$page->setTpl("index");

});

// admin
$app->get('/admin', function() {

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("index");

});

// Login admin get
$app->get('/admin/login', function() {
	
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);
	
	$page->setTpl("login");
});

// Login admin post
$app->post('/admin/login', function() {

	User::login($_POST["login"], $_POST["password"]);

	header("Location: /admin");
	exit;
});

// Logout admin
$app->get('/admin/logout', function(){
	
	User::logout();

	header("Location: /admin/login");
	exit;
});

// Admin users
$app->get("/admin/users", function(){
	
	User::verifyLogin();

	$users = User::listAll(); 

	$page = new PageAdmin();

	$page->setTpl("users", array(
		"users"=>$users
	));
});


// CREATE USER POST
//---------------------------------------------------//
$app->post('/admin/users/create', function() {

	User::verifyLogin();

	var_dump($_POST);

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");
	exit;

});

$app->get('/admin/users/create', function() {

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("users-create");

});
// CREATE USER GET
//---------------------------------------------------//


// UPDATE POST
//---------------------------------------------------//
$app->post('/admin/users/:iduser', function($iduser) {

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();

});

// UPDATE GET
//---------------------------------------------------//
$app->get('/admin/users/:iduser', function($iduser) {

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();

	$user->save($iduser);

	$page->setTpl("users-update", array(
		"user"=>$user->getValues()
	));

});


// DELETE
//---------------------------------------------------//
$app->get('/admin/users/:iduser/delete', function($iduser) {

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");
	exit;

});


$app->run();

 ?>