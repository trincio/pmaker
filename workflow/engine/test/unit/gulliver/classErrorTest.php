<?php
  if ( !defined ('PATH_THIRDPARTY') ) {
    require_once(  $_SERVER['PWD']. '/test/bootstrap/unit.php');
  }

  require_once( PATH_THIRDPARTY . 'lime/lime.php');
  define ( 'G_ENVIRONMENT', G_TEST_ENV);
  require_once( PATH_CORE . 'config' . PATH_SEP . 'environments.php');

  global $G_ENVIRONMENTS;
  if ( isset ( $G_ENVIRONMENTS ) ) {
    $dbfile = $G_ENVIRONMENTS[ G_TEST_ENV ][ 'dbfile'];
    if ( !file_exists ( $dbfile ) ) {
      printf("%s \n", pakeColor::colorize( "dbfile $dbfile doesn't exist for environment " . G_ENVIRONMENT  , 'ERROR'));
      exit (200);
    }
    else
     include ( $dbfile );
  }
  else
   exit (201);

  require_once( PATH_GULLIVER . 'class.dbconnection.php');
  require_once( PATH_GULLIVER . 'class.error.php');

$obj = new G_Error(); 

$t = new lime_test(10, new lime_output_color());
 
$t->diag('class error' );
$t->isa_ok( $obj  , 'G_Error',  'class G_Error created');
$t->is( G_ERROR , -100,         'G_ERROR constant defined');
$t->is( G_ERROR_ALREADY_ASSIGNED , -118,         'G_ERROR_ALREADY_ASSIGNED defined');

$obj = new G_Error( "string" ); 
$t->is( $obj->code, -1,    'default code error');
$t->is( $obj->message, "G Error: string",    'default message error');
$t->is( $obj->level, E_USER_NOTICE,    'default level error');

$obj = new G_Error( G_ERROR_SYSTEM_UID ); 
$t->is( $obj->code, -105,    'code error');
$t->is( $obj->message, "G Error: ",    'message error');

$t->can_ok( $obj, "errorMessage",  "exists method errorMessage");
$msg = $obj->errorMessage ( G_ERROR );
//$t->is( $msg->code, -100,    'fail in method errorMessage');
$t->todo(  'fail in method errorMessage');
