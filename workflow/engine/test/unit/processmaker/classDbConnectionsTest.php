<?php
  $unitFilename = $_SERVER['PWD'] . '/test/bootstrap/unit.php' ;
  require_once( $unitFilename );

  require_once( PATH_THIRDPARTY . '/lime/lime.php');
  require_once( PATH_THIRDPARTY.'lime/yaml.class.php');
 
  G::LoadThirdParty('smarty/libs','Smarty.class');
  G::LoadSystem ( 'xmlform');
  G::LoadSystem ( 'xmlDocument');
  G::LoadSystem ( 'form');
  require_once( 'propel/Propel.php' );
  require_once ( "creole/Creole.php" );
  require_once (  PATH_CORE . "config/databases.php");  

  G::LoadClass ( 'dbConnections');


  $obj = new DbConnections ($dbc); 
  $t   = new lime_test( 23, new lime_output_color() );

  $className = DbConnections;
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
  //To change the case only the first letter of each word, TIA
  //$className = ucwords($className);
  $t->diag("class $className" );

  $t->isa_ok( $obj  , $className,  "class $className created");

  $t->is( count($methods) , 10,  "class $className have " . 10 . ' methods.' );

   //checking method '__construct'
  $t->can_ok( $obj,      '__construct',   '__construct() is callable' );

  //$result = $obj->__construct ( $pPRO_UID);
  //$t->isa_ok( $result,      'NULL',   'call to method __construct ');
  $t->todo( "call to method __construct using $pPRO_UID ");


  //checking method 'getAllConnections'
  $t->can_ok( $obj,      'getAllConnections',   'getAllConnections() is callable' );

  //$result = $obj->getAllConnections ( );
  //$t->isa_ok( $result,      'NULL',   'call to method getAllConnections ');
  $t->todo( "call to method getAllConnections using  ");


  //checking method 'getConnections'
  $t->can_ok( $obj,      'getConnections',   'getConnections() is callable' );

  //$result = $obj->getConnections ( $pType);
  //$t->isa_ok( $result,      'NULL',   'call to method getConnections ');
  $t->todo( "call to method getConnections using $pType ");


  //checking method 'loadAdditionalConnections'
  $t->can_ok( $obj,      'loadAdditionalConnections',   'loadAdditionalConnections() is callable' );

  //$result = $obj->loadAdditionalConnections ( );
  //$t->isa_ok( $result,      'NULL',   'call to method loadAdditionalConnections ');
  $t->todo( "call to method loadAdditionalConnections using  ");


  //checking method 'getDbServicesAvailables'
  $t->can_ok( $obj,      'getDbServicesAvailables',   'getDbServicesAvailables() is callable' );

  //$result = $obj->getDbServicesAvailables ( );
  //$t->isa_ok( $result,      'NULL',   'call to method getDbServicesAvailables ');
  $t->todo( "call to method getDbServicesAvailables using  ");


  //checking method 'showMsg'
  $t->can_ok( $obj,      'showMsg',   'showMsg() is callable' );

  //$result = $obj->showMsg ( );
  //$t->isa_ok( $result,      'NULL',   'call to method showMsg ');
  $t->todo( "call to method showMsg using  ");


  //checking method 'getEncondeList'
  $t->can_ok( $obj,      'getEncondeList',   'getEncondeList() is callable' );

  //$result = $obj->getEncondeList ( $engine);
  //$t->isa_ok( $result,      'NULL',   'call to method getEncondeList ');
  $t->todo( "call to method getEncondeList using $engine ");


  //checking method 'getErrno'
  $t->can_ok( $obj,      'getErrno',   'getErrno() is callable' );

  //$result = $obj->getErrno ( );
  //$t->isa_ok( $result,      'NULL',   'call to method getErrno ');
  $t->todo( "call to method getErrno using  ");


  //checking method 'getErrmsg'
  $t->can_ok( $obj,      'getErrmsg',   'getErrmsg() is callable' );

  //$result = $obj->getErrmsg ( );
  //$t->isa_ok( $result,      'NULL',   'call to method getErrmsg ');
  $t->todo( "call to method getErrmsg using  ");


  //checking method 'ordx'
  $t->can_ok( $obj,      'ordx',   'ordx() is callable' );

  //$result = $obj->ordx ( $m);
  //$t->isa_ok( $result,      'NULL',   'call to method ordx ');
  $t->todo( "call to method ordx using $m ");



  $t->todo (  'review all pendings methods in this class');
