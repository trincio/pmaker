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
  G::LoadClass ( 'pmObject');
  G::LoadClass ( 'department');

  require_once (  PATH_CORE . "config/databases.php");  

  $dbc = new DBConnection(); 
  $ses = new DBSession( $dbc);
 
  $obj = new Department ($dbc); 
  $t   = new lime_test( 8, new lime_output_color() );
 
  $t->diag('class Department' );
  $t->isa_ok( $obj  , 'Department',  'class Department created');


  //method LoadParent
  $t->can_ok( $obj,      'LoadParent',   'LoadParent() is callable' );

//  $result = $obj->LoadParent ( );
//  $t->isa_ok( $result,      'NULL',   'call to method LoadParent ');


  //method LoadDependencie
  $t->can_ok( $obj,      'LoadDependencie',   'LoadDependencie() is callable' );

//  $result = $obj->LoadDependencie ( );
//  $t->isa_ok( $result,      'NULL',   'call to method LoadDependencie ');


  //method Save
  $t->can_ok( $obj,      'Save',   'Save() is callable' );

//  $result = $obj->Save ( );
//  $t->isa_ok( $result,      'NULL',   'call to method Save ');


  //method load
  $t->can_ok( $obj,      'load',   'load() is callable' );

//  $result = $obj->load ( $sUID);
//  $t->isa_ok( $result,      'NULL',   'call to method load ');


  //method delete
  $t->can_ok( $obj,      'delete',   'delete() is callable' );

//  $result = $obj->delete ( );
//  $t->isa_ok( $result,      'NULL',   'call to method delete ');





  $t->todo(  'TODO This class has "generic" load, save and delete methods. Review later...');
  $t->fail(  'review all pendings methods in this class');
?>
