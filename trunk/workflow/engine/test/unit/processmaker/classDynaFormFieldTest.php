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
  G::LoadClass ( 'dynaFormField');

  require_once (  PATH_CORE . "config/databases.php");  

  $dbc = new DBConnection(); 
  $ses = new DBSession( $dbc);
 
  $obj = new DynaFormField ($dbc); 
  $t   = new lime_test(  6, new lime_output_color() );
 
  $t->diag('class DynaFormField' );
  $t->isa_ok( $obj  , 'DynaFormField',  'class DynaFormField created');

  //method Load
  $t->can_ok( $obj,      'Load',   'Load() is callable' );

//  $result = $obj->Load ( $sUID);
//  $t->isa_ok( $result,      'NULL',   'call to method Load ');


  //method Delete
  $t->can_ok( $obj,      'Delete',   'Delete() is callable' );

//  $result = $obj->Delete ( $uid);
//  $t->isa_ok( $result,      'NULL',   'call to method Delete ');


  //method Save
  $t->can_ok( $obj,      'Save',   'Save() is callable' );

//  $result = $obj->Save ( $Fields, $labels, $options);
//  $t->isa_ok( $result,      'NULL',   'call to method Save ');


  //method isNew
  $t->can_ok( $obj,      'isNew',   'isNew() is callable' );

//  $result = $obj->isNew ( );
//  $t->isa_ok( $result,      'NULL',   'call to method isNew ');



  $t->fail(  'review all pendings methods in this class');
