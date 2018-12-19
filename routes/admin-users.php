<?php
use \Hcode\PageAdmin;
use \Hcode\Model\User;

// -- ADMIN - USUÁRIOS ---------------------------------------------------------
// Tela de alteração da senha do usuário
$app->get( "/admin/users/:iduser/password", function( $iduser ) {
   User::verifyLogin();

   $user = new User();
   $page = new PageAdmin();

   $user->get( ( int )$iduser );

   $page->setTpl( "users-password", array(
      "user"       => $user->getValues(),
      "msgError"   => User::getError(),
      "msgSuccess" => User::getSuccess()
   ) );
} );

// Altera a senha do usuário
$app->post( "/admin/users/:iduser/password", function( $iduser ) {
   User::verifyLogin();

   if ( !isset( $_POST[ 'despassword' ] ) || $_POST[ 'despassword' ] === '' ) {
      User::setError( 'Preencha a nova senha!' );

      header( "Location: /admin/users/{$iduser}/password" );

      exit;
   }

   if ( !isset( $_POST[ 'despassword-confirm' ] ) || $_POST[ 'despassword-confirm' ] === '' ) {
      User::setError( 'Preencha com a confirmação da nova senha!' );

      header( "Location: /admin/users/{$iduser}/password" );

      exit;
   }

   if ( $_POST[ 'despassword' ] !== $_POST[ 'despassword-confirm' ] ) {
      User::setError( 'As senhas devem ser iguais!' );

      header( "Location: /admin/users/{$iduser}/password" );

      exit;
   }

   $user = new User();

   $user->get( ( int )$iduser );
   $user->setPassword( User::getPasswordHash( $_POST[ 'despassword' ] ) );

   User::setSuccess( 'Senha alterada com sucesso!' );

   header( "Location: /admin/users/{$iduser}/password" );

   exit;
} );

// Rota para exclusão dos dados de usuário
$app->get( "/admin/users/:iduser/delete", function( $iduser ) {
   User::verifyLogin();

   $user = new User();

   $user->get( ( int )$iduser );
   $user->delete();

   header( "Location: /admin/users" );

   exit;
} );

// Rota da tela para atualização de dados de usuário
$app->get( '/admin/users/:iduser', function( $iduser ) {
   User::verifyLogin();

   $user = new User();
   $page = new PageAdmin();

   $user->get( ( int )$iduser );

   $page->setTpl( "users-update", array(
      "user" => $user->getValues()
   ) );
} );

// Rota da tela de criação de usuário
$app->get( '/admin/users/create', function() {
   User::verifyLogin();

   $page = new PageAdmin();

   $page->setTpl( "users-create" );
} );

// Rota para salvar os dados de criação de usuário
$app->post( "/admin/users/create", function() {
   User::verifyLogin();

   $user = new User();

   $_POST[ 'inadmin' ]     = ( isset( $_POST[ 'inadmin' ] ) ) ? 1 : 0;
   $_POST[ 'despassword' ] = password_hash( $_POST[ "despassword" ], PASSWORD_DEFAULT, [ "cost" => 12 ] );  // Temporário

   $user->setData( $_POST );
   $user->save();

   header( "Location: /admin/users" );

   exit;
} );

// Rota para salvar os dados de atualização de usuário
$app->post( "/admin/users/:iduser", function( $iduser ) {
   User::verifyLogin();

   $user = new User();

   $_POST[ 'inadmin' ] = ( isset( $_POST[ 'inadmin' ] ) ) ? 1 : 0;

   $user->get( ( int )$iduser );
   $user->setData( $_POST );
   $user->update();

   header( "Location: /admin/users" );

   exit;
} );

// Rota da tela para listar todos os usuários
$app->get( '/admin/users', function() {
   User::verifyLogin();

   $search     = ( isset( $_GET[ 'search' ] ) ) ? $_GET[ 'search' ]      : "";
   $page       = ( isset( $_GET[ 'page' ] ) )   ? ( int )$_GET[ 'page' ] : 1;
   $pagination = User::getPage( $page, $search );
   $pages      = [];

   for ( $i = 0; $i < $pagination[ 'pages' ]; $i++ ) {
      array_push( $pages, array(
         'text' => $i + 1,
         'href' => '/admin/users?' . http_build_query( array(
            'page'   => $i + 1,
            'search' => $search
         ) )
      ) );
   }

   $page = new PageAdmin();

   $page->setTpl( "users", array(
      "users"  => $pagination[ 'data' ],
      "search" => $search,
      "pages"  => $pages
   ) );
} );
?>