<?php
  $unitFilename = $_SERVER['PWD'] . '/test/bootstrap/unit.php' ;
  require_once( $unitFilename );

  require_once( PATH_THIRDPARTY . '/lime/lime.php');
  require_once( PATH_THIRDPARTY.'lime/yaml.class.php');
  require_once (  PATH_CORE . "config/databases.php");  
  require_once ( "propel/Propel.php" );
  Propel::init(  PATH_CORE . "config/databases.php");
 
  G::LoadThirdParty('smarty/libs','Smarty.class');
  G::LoadSystem ( 'error');
  G::LoadSystem ( 'xmlform');
  G::LoadSystem ( 'xmlDocument');
  G::LoadSystem ( 'form');
  G::LoadSystem ( 'dbconnection');
  G::LoadSystem ( 'dbsession');
  G::LoadSystem ( 'dbrecordset');
  G::LoadSystem ( 'dbtable');
  G::LoadClass ( 'xmlDb');
  $dbc = new DBConnection(); 
  $ses = new DBSession( $dbc);
 
  $obj = new XmlDb ($dbc); 
  $t   = new lime_test( 4, new lime_output_color() );
 
  $t->diag('class XmlDb' );
  $t->isa_ok( $obj  , 'XMLDB',  'class XmlDb created');

  //method connect
  $t->can_ok( $obj,      'connect',   'connect() is callable' );

//  $result = $obj->connect ( $dsn, $options);
//  $t->isa_ok( $result,      'NULL',   'call to method connect ');


  //method isError
  $t->can_ok( $obj,      'isError',   'isError() is callable' );

//  $result = $obj->isError ( $result);
//  $t->isa_ok( $result,      'NULL',   'call to method isError ');


  $t->todo(  'this class permits CRUD operations over an xml file');

?>