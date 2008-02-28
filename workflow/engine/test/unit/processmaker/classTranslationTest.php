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
  require_once ( 'classes/model/Translation.php');

  require_once (  PATH_CORE . "config/databases.php");

  $obj = new Translation ();
  $t   = new lime_test( 5, new lime_output_color() );

  $t->diag('class Translation' );
  $t->isa_ok( $obj  , 'Translation',  'class Translation created');

  //method load
  $t->can_ok( $obj,      'load',   'load() is callable' );

//  $result = $obj->load ( $sUID);
//  $t->isa_ok( $result,      'NULL',   'call to method load ');


  //method save
  $t->can_ok( $obj,      'save',   'save() is callable' );

//  $result = $obj->save ( );
//  $t->isa_ok( $result,      'NULL',   'call to method save ');


  //method saveContent
  $t->can_ok( $obj,      'saveContent',   'saveContent() is callable' );

//  $result = $obj->saveContent ( $sConCategory, $fields, $sysLang);
//  $t->isa_ok( $result,      'NULL',   'call to method saveContent ');



  $t->fail(  'review all pendings methods in this class');
