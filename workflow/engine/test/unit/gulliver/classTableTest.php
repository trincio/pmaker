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
G::LoadSystem ( 'table');
G::LoadSystem ( 'dbconnection');
G::LoadSystem ( 'dbsession');
G::LoadSystem ( 'dbrecordset');
G::LoadSystem ( 'dbtable');


//$dbc = new DBConnection(); 
//$ses = new DBSession( $dbc);
//$obj = new DBTable ( $dbc, "APPLICATION" , array ( 'APP_UID' ) );
 
$t = new lime_test(23, new lime_output_color());
 
$obj = new Table(  ); 
$t->diag('class Table' );
$t->isa_ok( $obj  , 'Table',  'class Table created');
$t->can_ok( $obj,      'SetTo',   'SetTo()');
$t->can_ok( $obj,      'SetSource',   'SetSource()');
$t->can_ok( $obj,      'GetSource',   'GetSource()');
$t->can_ok( $obj,      'TotalCount',   'TotalCount()');
$t->can_ok( $obj,      'Count',   'Count()');
$t->can_ok( $obj,      'CurRow',   'CurRow()');
$t->can_ok( $obj,      'ColumnCount',   'ColumnCount()');
$t->can_ok( $obj,      'Read',   'Read()');
$t->can_ok( $obj,      'Seek',   'Seek()');
$t->can_ok( $obj,      'MoveFirst',   'MoveFirst()');
$t->can_ok( $obj,      'EOF',   'EOF()');
$t->can_ok( $obj,      'AddColumn',   'AddColumn()');
$t->can_ok( $obj,      'AddRawColumn',   'AddRawColumn()');
$t->can_ok( $obj,      'RenderTitle_ajax',   'RenderTitle_ajax()');
$t->can_ok( $obj,      'RenderTitle2',   'RenderTitle2()');
$t->can_ok( $obj,      'RenderColumn',   'RenderColumn()');
$t->can_ok( $obj,      'SetAction',   'SetAction()');
$t->can_ok( $obj,      'setTranslate',   'setTranslate()');
$t->can_ok( $obj,      'translateValue',   'translateValue()');
$t->can_ok( $obj,      'setContext',   'setContext()');
$t->can_ok( $obj,      'ParsingFromHtml',   'ParsingFromHtml()');

$t->todo(  'review all pendings in this class');
