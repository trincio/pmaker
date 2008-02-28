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
  G::LoadClass ( 'pmScript');

  require_once (  PATH_CORE . "config/databases.php");  

  $dbc = new DBConnection(); 
  $ses = new DBSession( $dbc);
 
  $obj = new PmScript ($dbc); 
  $t   = new lime_test( 7, new lime_output_color() );
 
  $t->diag('class PmScript' );
  $t->isa_ok( $obj  , 'PMScript',  'class PmScript created');

  //method setFields
  $t->can_ok( $obj,      'setFields',   'setFields() is callable' );

//  $result = $obj->setFields ( $aFields);
//  $t->isa_ok( $result,      'NULL',   'call to method setFields ');


  //method setScript
  $t->can_ok( $obj,      'setScript',   'setScript() is callable' );

//  $result = $obj->setScript ( $sScript);
//  $t->isa_ok( $result,      'NULL',   'call to method setScript ');


  //method validSyntax
  $t->can_ok( $obj,      'validSyntax',   'validSyntax() is callable' );

//  $result = $obj->validSyntax ( $sScript);
//  $t->isa_ok( $result,      'NULL',   'call to method validSyntax ');


  //method execute
  $t->can_ok( $obj,      'execute',   'execute() is callable' );

//  $result = $obj->execute ( );
//  $t->isa_ok( $result,      'NULL',   'call to method execute ');


  //method evaluate
  $t->can_ok( $obj,      'evaluate',   'evaluate() is callable' );

//  $result = $obj->evaluate ( );
//  $t->isa_ok( $result,      'NULL',   'call to method evaluate ');



  $t->fail(  'review all pendings methods in this class');