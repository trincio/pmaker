<?php
    if ( PHP_OS == 'WINNT' ) 
      define('PATH_SEP', '\\');
    else
      define('PATH_SEP', '/');
      
    //***************** Defining the Home Directory *********************************
    $docuroot =  explode ( PATH_SEP , $_SERVER['PWD'] );
    array_pop($docuroot);
    $pathhome = implode( PATH_SEP, $docuroot );
    define('PATH_HOME', $pathhome . PATH_SEP );
    $gulliverConfig =  PATH_HOME . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'paths.php';  
    $definesConfig =  PATH_HOME . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'defines.php';  

    //try to find automatically the trunk directory where are placed the RBAC and Gulliver directories
    //in a normal installation you don't need to change it.
    array_pop($docuroot);
    $pathTrunk = implode( PATH_SEP, $docuroot ) . PATH_SEP ;
    array_pop($docuroot);
    $pathOutTrunk = implode( PATH_SEP, $docuroot ) . PATH_SEP ;
    // to do: check previous algorith for Windows  $pathTrunk = "c:/home/"; 

    define('PATH_TRUNK', $pathTrunk );
    define('PATH_OUTTRUNK', $pathOutTrunk );
       
    if (file_exists( $gulliverConfig ))   {
      include ( $gulliverConfig );
    }
 
    if (file_exists( $definesConfig ))   {
      include ( $definesConfig );
    }
 
  //$_test_dir = realpath(dirname(__FILE__).'/..');
  //require_once( 'lime/lime.php');

  require_once( PATH_THIRDPARTY . 'pake' . PATH_SEP . 'pakeFunction.php');
  require_once( PATH_THIRDPARTY . 'pake' . PATH_SEP . 'pakeGetopt.class.php');
  require_once( PATH_CORE . 'config' . PATH_SEP . 'environments.php');

  if ( !defined ( 'G_ENVIRONMENT') ) 
    define ( 'G_ENVIRONMENT', G_TEST_ENV );

