<?php
use \Hcode\PageAdmin;
use \Hcode\Model\User;

// -- ADMIN --------------------------------------------------------------------
// Rota da tela de login de usuário
$app->get( '/admin/login', function() {
   $page = new PageAdmin( [
      "header" => false,
      "footer" => false
   ] );

   $page->setTpl( "login" );
} );

// Rota para o login de usuário
$app->post( '/admin/login', function() {
   User::login( $_POST[ 'login' ], $_POST[ 'password' ] );

   header( "Location: /admin" );

   exit;
} );

// Rota para o logout de usuário
$app->get( '/admin/logout', function() {
   User::logout();

   header( "Location: /admin/login" );

   exit;
} );

// -- ADMIN - RESTAURAR SENHA DE ADMINISTRADOR ---------------------------------
// Rota para o envio do link com o código para reconfiguração de senha
$app->get( "/admin/forgot/sent", function() {
   $page = new PageAdmin( [
      "header" => false,
      "footer" => false
   ] );

   $page->setTpl( "forgot-sent" );
} );

// Rota para validar o código para reconfiguração da senha
$app->get( "/admin/forgot/reset", function() {
   $user = User::validForgotDecrypt( $_GET[ 'code' ] );

   $page = new PageAdmin( [
      "header" => false,
      "footer" => false
   ] );

   $page->setTpl( "forgot-reset", array(
      "name" => $user[ 'desperson' ],
      "code" => $_GET[ 'code' ]
   ) );
} );

// Rota para salvar a nova senha
$app->post( "/admin/forgot/reset", function() {
   $forgot = User::validForgotDecrypt( $_POST[ 'code' ] );

   User::setForgotUsed( $forgot[ 'idrecovery' ] );

   $user     = new User();
   $password = password_hash( $_POST[ "password" ], PASSWORD_DEFAULT, [ "cost" => 12 ] );

   $user->get( ( int )$forgot[ 'iduser' ] );
   $user->setPassword( $password );

   $page = new PageAdmin( [
      "header" => false,
      "footer" => false
   ] );

   $page->setTpl( "forgot-reset-success" );
} );

// Rota da tela solicitando o e-mail para reconfiguração de senha
$app->get( "/admin/forgot", function() {
   $page = new PageAdmin( [
      "header" => false,
      "footer" => false
   ] );

   $page->setTpl( "forgot" );
} );

// Rota para enviar e-mail de reconfiguração de senha
$app->post( "/admin/forgot", function() {
   $user = User::getForgot( $_POST[ 'email' ] );

   header( "Location: /admin/forgot/sent" );

   exit;
} );

// Rota para o diretório admin
$app->get( '/admin', function() {
   User::verifyLogin();

   $page = new PageAdmin();

   $page->setTpl( "index" );
} );

?>