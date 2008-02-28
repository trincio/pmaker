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
  G::LoadClass ( 'message');

  require_once (  PATH_CORE . "config/databases.php");  

  $dbc = new DBConnection(); 
  $ses = new DBSession( $dbc);
 
  $obj = new Message ($dbc); 
  $t   = new lime_test( 6, new lime_output_color() );
 
  $t->diag('class Message' );
  $t->isa_ok( $obj  , 'Message',  'class Message created');

  //method load
  $t->can_ok( $obj,      'load',   'load() is callable' );

//  $result = $obj->load ( $sUID);
//  $t->isa_ok( $result,      'NULL',   'call to method load ');


  //method save
  $t->can_ok( $obj,      'save',   'save() is callable' );

//  $result = $obj->save ( $fields);
//  $t->isa_ok( $result,      'NULL',   'call to method save ');




  $t->fail(  'review all pendings methods in this class');