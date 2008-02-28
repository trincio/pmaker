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
  require_once( PATH_GULLIVER . 'class.dbsession.php');
  require_once( PATH_GULLIVER . 'class.dbrecordset.php');

$dbc = new DBConnection(); 
$ses = new DBSession( $dbc);
$dset = $ses->Execute ( "SELECT * from APPLICATION" );
 
$t = new lime_test(6, new lime_output_color());
 
$t->diag('class DBSession' );
$t->isa_ok( $ses  , 'DBSession',  'class DBSession created');

$t->can_ok( $ses,      'SetTo',   'SetTo()');
$t->can_ok( $ses,      'Free',   'Free()');
$t->can_ok( $ses,      'UseDB',   'UseDB()');
$t->can_ok( $ses,      'Execute',   'Execute()');

$t->todo(  'review all pendings in this class');
