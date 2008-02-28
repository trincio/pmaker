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
 
$t = new lime_test(7, new lime_output_color());
 
$t->diag('class DBRecordset' );
$t->isa_ok( $dset  , 'DBRecordSet',  'class DBRecordset created');

$t->can_ok( $dset,      'SetTo',   'SetTo()');
$t->can_ok( $dset,      'Free',   'Free()');
$t->can_ok( $dset,      'Count',   'Count()');
$t->can_ok( $dset,      'Read',   'Read()');
$t->can_ok( $dset,      'ReadAbsolute',   'ReadAbsolute()');

$t->todo(  'review all pendings in this class');
