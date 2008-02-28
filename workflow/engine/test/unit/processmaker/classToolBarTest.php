<?php
  $unitFilename = $_SERVER['PWD'] . '/test/bootstrap/unit.php' ;
  require_once( $unitFilename );
  require_once( PATH_THIRDPARTY . '/lime/lime.php');
  require_once( PATH_THIRDPARTY.'lime/yaml.class.php');

  G::LoadThirdParty('smarty/libs','Smarty.class');
  G::LoadSystem ( 'error');
  G::LoadSystem ( 'xmlform');
  G::LoadSystem ( 'xmlDocument');
  G::LoadSystem ( 'form');
  G::LoadSystem ( 'dbconnection');
  G::LoadSystem ( 'dbsession');
  G::LoadSystem ( 'dbrecordset');
  G::LoadSystem ( 'dbtable');
  G::LoadClass ( 'toolBar');

  require_once (  PATH_CORE . "config/databases.php");

  $dbc = new DBConnection();
  $ses = new DBSession( $dbc);
  $file = 'login/login';
  $arg1 = '';
  $arg2 = '';
  $arg3 = '';

  $obj = new ToolBar ( $file );
  $t   = new lime_test( 1, new lime_output_color() );

  $t->diag('class ToolBar' );
  $t->isa_ok( $obj  , 'ToolBar',  'class ToolBar created');

  $t->diag(' XmlForm_Field_toolbar' );

  //$t->fail(  'review all pendings methods and CLASSES in this class');
  
?>