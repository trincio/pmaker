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
  require_once( PATH_GULLIVER . 'class.dbtable.php');

$dbc = new DBConnection(); 
$ses = new DBSession( $dbc);
$obj = new DBTable ( $dbc, "APPLICATION" , array ( 'APP_UID' ) );
 
$t = new lime_test(12, new lime_output_color());
 
$t->diag('class DBTable' );
$t->isa_ok( $obj  , 'DBTable',  'class DBTable created');

$t->can_ok( $obj,      'SetTo',   'SetTo()');
$t->can_ok( $obj,      'loadEmpty',   'loadEmpty()');
$t->can_ok( $obj,      'loadWhere',   'loadWhere()');
$t->can_ok( $obj,      'load',   'load()');
$t->can_ok( $obj,      'nextvalPGSql',   'nextvalPGSql()');
$t->can_ok( $obj,      'insert',   'insert()');
$t->can_ok( $obj,      'update',   'update()');
$t->can_ok( $obj,      'save',   'save()');
$t->can_ok( $obj,      'delete',   'delete()');
$t->can_ok( $obj,      'next',   'next()');

$t->todo(  'review all pendings in this class');
