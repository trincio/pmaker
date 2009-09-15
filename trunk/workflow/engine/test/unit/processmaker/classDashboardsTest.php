<?php
  $unitFilename = $_SERVER['PWD'] . '/test/bootstrap/unit.php' ;
  require_once( $unitFilename );

  require_once( PATH_THIRDPARTY . '/lime/lime.php');
  require_once( PATH_THIRDPARTY.'lime/yaml.class.php');
  require_once( 'propel/Propel.php' );
  require_once ( "creole/Creole.php" );
  Propel::init(  PATH_CORE . "config/databases.php");


  G::LoadThirdParty('smarty/libs','Smarty.class');
  G::LoadSystem ( 'error');
  G::LoadSystem ( 'xmlform');
  G::LoadSystem ( 'xmlDocument');
  G::LoadSystem ( 'form');

  require_once (  PATH_CORE . "config/databases.php");  

  G::LoadClass ( 'dashboards');

  $obj = new Dashboards ($dbc); 
  $t   = new lime_test( 9, new lime_output_color() );

  $className = Dashboards;
  $className = strtolower ( substr ($className, 0,1) ) . substr ($className, 1 );
  
  $reflect = new ReflectionClass( $className );
	$method = array ( );
	$testItems = 0;
 
  foreach ( $reflect->getMethods() as $reflectmethod )  {  
  	$params = '';
  	foreach ( $reflectmethod->getParameters() as $key => $row )   {  
  	  if ( $params != '' ) $params .= ', ';
  	  $params .= '$' . $row->name;  
  	}

 		$testItems++;
  	$methods[ $reflectmethod->getName() ] = $params;
  }
 
  $t->diag('class $className' );
  $t->isa_ok( $obj  , 'Dashboards',  'class $className created');

  $t->is( count($methods) , 3,  "class $className have " . 3 . ' methods.' );

  //checking method 'getConfiguration'
  $t->can_ok( $obj,      'getConfiguration',   'getConfiguration() is callable' );

  //$result = $obj->getConfiguration ( $sUserUID);
  //$t->isa_ok( $result,      'NULL',   'call to method getConfiguration ');
  $t->todo( "call to method getConfiguration using $sUserUID ");


  //checking method 'saveConfiguration'
  $t->can_ok( $obj,      'saveConfiguration',   'saveConfiguration() is callable' );

  //$result = $obj->saveConfiguration ( $sUserUID, $aConfiguration);
  //$t->isa_ok( $result,      'NULL',   'call to method saveConfiguration ');
  $t->todo( "call to method saveConfiguration using $sUserUID, $aConfiguration ");


  //checking method 'getDashboardsObject'
  $t->can_ok( $obj,      'getDashboardsObject',   'getDashboardsObject() is callable' );

  //$result = $obj->getDashboardsObject ( $sUserUID);
  //$t->isa_ok( $result,      'NULL',   'call to method getDashboardsObject ');
  $t->todo( "call to method getDashboardsObject using $sUserUID ");



  $t->todo (  'review all pendings methods in this class');
