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
  G::LoadClass ( 'userInfo');

  require_once (  PATH_CORE . "config/databases.php");  

  $dbc = new DBConnection(); 
  $ses = new DBSession( $dbc);
 
  $obj = new UserInfo ($dbc); 
  $t   = new lime_test( 3, new lime_output_color() );
 
  $t->diag('class UserInfo' );
  $t->isa_ok( $obj  , 'UserInfo',  'class UserInfo created');

  //method Load
  $t->can_ok( $obj,      'Load',   'Load() is callable' );

//  $result = $obj->Load ( $uid);
//  $t->isa_ok( $result,      'NULL',   'call to method Load ');


  $t->todo(  'when we use this class?');
  
?>  