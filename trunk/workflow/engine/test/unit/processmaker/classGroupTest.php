<?php
  $unitFilename = $_SERVER['PWD'] . '/test/bootstrap/unit.php' ;
  require_once( $unitFilename );

  require_once( PATH_THIRDPARTY . '/lime/lime.php');
  require_once( PATH_THIRDPARTY.'lime/yaml.class.php');
  require_once( 'propel/Propel.php' );
  Propel::init(  PATH_CORE . "config/databases.php");

 
  G::LoadThirdParty('smarty/libs','Smarty.class');
  G::LoadSystem ( 'error');
  G::LoadSystem ( 'xmlform');
  G::LoadSystem ( 'xmlDocument');
  G::LoadSystem ( 'form');
  G::LoadSystem ( 'testTools');

  require_once( PATH_CORE.'/classes/model/Groupwf.php');

  $obj = new Groupwf (); 
  $t   = new lime_test( 25, new lime_output_color() );

  $t->diag('class Groupwf' );
  $t->isa_ok( $obj  , 'Groupwf',  'class Groupwf created');

  //method load
  //#2
  $t->can_ok( $obj,      'getGrpTitle',   'getGrpTitle() is callable' );
  //#3
  $t->can_ok( $obj,      'setGrpTitle',   'setGrpTitle() is callable' );
  //#4
  $t->can_ok( $obj,      'create',   'create() is callable' );
  //#5
  $t->can_ok( $obj,      'update',   'update() is callable' );
  //#6
  $t->can_ok( $obj,      'load',   'load() is callable' );
  //#7
  $t->can_ok( $obj,      'remove',   'remove() is callable' );

  //getGrpUid
  //#8 
  $t->is( $obj->getGrpUid(),      '',   'getGrpUid() return empty, when the instance doesnt have any row' );
  
  //getGrpTitle
  try {
    $obj = new Groupwf (); 
    $res = $obj->getGrpTitle();
  } 
  catch ( Exception $e ) {
  //#9
    $t->isa_ok( $e,      'Exception',   'getGrpTitle() return error when GRP_UID is not defined' );
  //#10
    $t->is ( $e->getMessage(),      "Error in getGrpTitle, the GRP_UID can't be blank",   'getGrpTitle() return Error in getGrpTitle, the GRP_UID cant be blank' );
  }

  
  //create new row
  try {
    $obj = new Groupwf (); 
    $res = $obj->create();
  } 
  catch ( Exception $e ) {
  //#xx
    $t->isa_ok( $e,      'PropelException',   'create() return error when GRP_UID is not defined' );
  //#xx
    $t->like ( $e->getMessage(),      "%The groupwf cannot be created. The USR_UID is empty.%",   'create() return The groupwf cannot be created. The USR_UID is empty.' );
  }

  //create
  try {
    //$Fields['USR_UID'] = '1';  // we need a valid user
    $Fields['GRP_TITLE'] = 'My Group Title';
    $obj = new Groupwf (); 
    $proUid = $obj->create( $Fields );
  //#11
    $t->isa_ok( $proUid,      'string',   'create(), creates a new Groupwf' );
  //#12
    $t->is ( strlen($proUid),      14,   'create(), creates a new Groupwf, Guid lenth=14 chars' );

    $res = $obj->load( $proUid ); 
  //#13
    $t->isa_ok( $res,      'array',   'load(), loads a new Groupwf' );
  //#14
    $t->is ( $res['GRP_UID'],      $proUid,   'load(), loads a new Groupwf, valid GRP_UID' );
  //#15
    $t->is ( $res['GRP_TITLE'],    'My Group Title',   'load(), loads a new Groupwf, valid PRO_DESCRIPTION' );
    
  } 
  catch ( Exception $e ) {
    $t->like ( $e->getMessage(),      "%Unable to execute INSERT statement%",   'create() return Error in getAppTitle, the APP_UID cant be blank' );
  }

  //update with empty
  try {
    $obj = new Groupwf (); 
    $res = $obj->update( NULL );
  } 
  catch ( Exception $e ) {
  //#16
    $t->isa_ok( $e,      'Exception',   'update() returns error when GRP_UID is not defined' );
  //#17
    $t->is ( $e->getMessage(),   "This row doesn't exists!",   "update() This row doesn't exists!" );
  }


  //update with $fields
  $newTitle = 'new title ' . rand( 1000, 5000);
  $newDescription = 'new Description '. rand( 1000, 5000);
  $Fields['GRP_UID'] = $proUid;
  $Fields['GRP_TITLE'] = $newTitle;
  $Fields['GRP_STATUS'] = 'INACTIVE';
  try {
    $obj = new Groupwf (); 
    $res = $obj->update( $Fields);
  //#18
    $t->is ( $res,   1,   "update() update 1 row" );
    $Fields = $obj->Load ( $proUid );
  //#19
    $t->is ( $obj->getgrpUid(),   $proUid,   "update() APP_UID = ". $proUid );
  //#20
    $t->is ( $obj->getGrpTitle(),   $newTitle,   "update() getAppTitle" );
  //#21
    $t->is ( $Fields['GRP_TITLE'],   $newTitle,   "update() PRO_TITLE= ". $newTitle );
  } 
  catch ( Exception $e ) {
  //#14
    $t->isa_ok( $e,      'PropelException',   'update() return error ' . $e->getMessage() );
    print $e->getMessage();
  }

//remove with empty
  try {
    $obj = new Groupwf (); 
    $res = $obj->remove( NULL );
  } 
  catch ( Exception $e ) {
  //#30
    $t->isa_ok( $e,      'Exception',   'remove() returns error when UID is not defined' );
  //#31
    $t->is ( $e->getMessage(),   "This row doesn't exists!",   "remove() This row doesn't exists!" );
  }

  //remove with $fields
  $Fields['GRP_UID'] = $proUid;
  try {
    $obj = new Groupwf (); 
    $res = $obj->remove( $Fields );
  //#32
    $t->is ( $res,   NULL,   "remove() remove row $proUid" );
  } 
  catch ( Exception $e ) {
  //#14
    $t->isa_ok( $e,      'PropelException',   'remove() return error ' . $e->getMessage() );
  }

  //remove with $proUid
  $obj = new Groupwf (); 
  $proUid = $obj->create( '1' );
  try {
    $obj = new Groupwf (); 
    $res = $obj->remove ($proUid );
  //#33
    $t->is ( $res,   NULL,   "remove() remove row $proUid" );
  } 
  catch ( Exception $e ) {
  //#14
    $t->isa_ok( $e,      'PropelException',   'remove() return error ' . $e->getMessage() );
  }


?>
