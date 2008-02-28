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
G::LoadSystem ( 'templatePower');
G::LoadSystem ( 'dbconnection');
G::LoadSystem ( 'dbsession');
G::LoadSystem ( 'dbrecordset');
G::LoadSystem ( 'dbtable');


//$dbc = new DBConnection(); 
//$ses = new DBSession( $dbc);
//$obj = new DBTable ( $dbc, "APPLICATION" , array ( 'APP_UID' ) );
 
$t = new lime_test(15, new lime_output_color());
 
$obj = new TemplatePowerParser( 'a', 'b'  ); 
$t->diag('class TemplatePowerParser' );
$t->isa_ok( $obj  , 'TemplatePowerParser',  'class TemplatePowerParser created');
$t->can_ok( $obj,      '__prepare',   '__prepare()');
$t->can_ok( $obj,      '__prepareTemplate',   '__prepareTemplate()');
$t->can_ok( $obj,      '__parseTemplate',   '__parseTemplate()');

$obj = new TemplatePower(  ); 

$t->can_ok( $obj,      '__outputContent',   '__outputContent()');
$t->can_ok( $obj,      '__printVars',   '__printVars()');
$t->can_ok( $obj,      'prepare',   'prepare()');
$t->can_ok( $obj,      'newBlock',   'newBlock()');
$t->can_ok( $obj,      'assignGlobal',   'assignGlobal()');
$t->can_ok( $obj,      'assign',   'assign()');
$t->can_ok( $obj,      'gotoBlock',   'gotoBlock()');
$t->can_ok( $obj,      'getVarValue',   'getVarValue()');
$t->can_ok( $obj,      'printToScreen',   'printToScreen()');
$t->can_ok( $obj,      'getOutputContent',   'getOutputContent()');

$t->todo(  'review all pendings in this class');
