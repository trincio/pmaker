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
  $obj = new DBConnection(); 
  $t = new lime_test(12, new lime_output_color());
 
$t->diag('class DBConnection' );
$t->isa_ok( $obj  , 'DBConnection',  'class DBConnection created');

$t->is( DB_ERROR_NO_SHOW_AND_CONTINUE,      'bDh5aTBaUG5vNkxwMnByWjJxT2EzNVk___',   'createUID() normal');
$t->can_ok( $obj,      'Reset',   'Reset()');
$t->can_ok( $obj,      'Free',   'Free()');
$t->can_ok( $obj,      'Close',   'Close()');
$t->can_ok( $obj,      'logError',   'logError()');
$t->can_ok( $obj,      'traceError',   'traceError()');
$t->can_ok( $obj,      'printArgs',   'printArgs()');
$t->todo(  'can we move it to G class ?');
$t->can_ok( $obj,      'GetLastID',   'GetLastID()');
$t->todo(  'GetLastID works only in mysql');

$t->todo(  'review all pendings in this class');
