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

G::LoadThirdParty('smarty/libs','Smarty.class');
G::LoadSystem ( 'xmlform');
G::LoadSystem ( 'xmlDocument');
G::LoadSystem ( 'form');
G::LoadSystem ( 'dbconnection');
G::LoadSystem ( 'dbsession');
G::LoadSystem ( 'dbrecordset');
G::LoadSystem ( 'dbtable');


//$dbc = new DBConnection(); 
//$ses = new DBSession( $dbc);
//$obj = new DBTable ( $dbc, "APPLICATION" , array ( 'APP_UID' ) );
 
$obj = new Form ( 'login/login'); 
$t = new lime_test(9, new lime_output_color());
 
$t->diag('class Form' );
$t->isa_ok( $obj  , 'Form',  'class Form created');

$t->can_ok( $obj,      'setDefaultValues',   'setDefaultValues()');
$t->can_ok( $obj,      'printTemplate',   'printTemplate()');
$t->can_ok( $obj,      'render',   'render()');
$t->can_ok( $obj,      'setValues',   'setValues()');
$t->can_ok( $obj,      'getFields',   'getFields()');
$t->can_ok( $obj,      'validatePost',   'validatePost()');
$t->can_ok( $obj,      'validateArray',   'validateArray()');

$t->todo(  'review all pendings in this class');
