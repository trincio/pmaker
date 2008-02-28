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
  G::LoadSystem ( 'form');
  G::LoadSystem ( 'dbconnection');
  G::LoadSystem ( 'dbsession');
  G::LoadSystem ( 'dbrecordset');
  G::LoadSystem ( 'dbtable');
  G::LoadSystem ( 'testTools');
  G::LoadClass ( 'derivation');
  //G::LoadClass('task');
  //G::LoadClass('application');

  global $dbc;
  $dbc = new DBConnection();
  $ses = new DBSession( $dbc);

  $obj = new Derivation ($dbc);
  $t   = new lime_test( 14, new lime_output_color() );

  $t->diag('class Derivation' );
  $t->isa_ok( $obj  , 'Derivation',  'class Derivation created');


  //method startCase
  $t->can_ok( $obj,      'startCase',   'startCase() is callable' );

//  $result = $obj->startCase ( $aData);
//  $t->isa_ok( $result,      'NULL',   'call to method startCase ');


  //method prepareInformation
  $t->can_ok( $obj,      'prepareInformation',   'prepareInformation() is callable' );

//  $result = $obj->prepareInformation ( $aData);
//  $t->isa_ok( $result,      'NULL',   'call to method prepareInformation ');


  //method getNextAssignedUser
  $t->can_ok( $obj,      'getNextAssignedUser',   'getNextAssignedUser() is callable' );

//  $result = $obj->getNextAssignedUser ( $tasInfo);
//  $t->isa_ok( $result,      'NULL',   'call to method getNextAssignedUser ');


  //method derivate
  $t->can_ok( $obj,      'derivate',   'derivate() is callable' );

//  $result = $obj->derivate ( $currentDelegation, $nextDelegations);
//  $t->isa_ok( $result,      'NULL',   'call to method derivate ');


  //method isOpen
  $t->can_ok( $obj,      'isOpen',   'isOpen() is callable' );

//  $result = $obj->isOpen ( $appUID, $tasUID);
//  $t->isa_ok( $result,      'NULL',   'call to method isOpen ');




  //$t->fail(  'review all pendings methods in this class');


/*****   TEST CLASS DERIVATION   *****/
///////// INITIAL VALUES /////////
define('SYS_LANG','en');
//Test Class
class derivationTest extends unitTest
{
  function StartCaseTest( $testCase, $Fields )
  {
    global $dbc;
    $der = new Derivation( $dbc );
    $result = $der->startCase( $Fields );
    return $result;
  }
  function DeleteCase( $testCase, $Fields )
  {
    global $dbc;
    $app= new Application( $dbc );
    return $app->delete( $Fields['APP_UID'] );
  }
  function derivate( $testCase, &$testDomain, &$t )
  {

  }
}

/***************************/
die;
/***************************/

//Initialize the global domain (It is optional)
$testDomain = new ymlDomain();
//Initialize the testClass ( ymlTestDefinitionFile, limeTestObject, testDomain )
$test = new derivationTest('derivation.yml', $t, $testDomain );
$test->load('StartCase1');
$vAux = $test->runSingle();//var_dump($vAux);die;
$t->isa_ok( $vAux['APPLICATION'], 'string', 'Verify if APPLICATION is a string' );
$t->is( $vAux['INDEX'], 1, 'Verify if DEL_INDEX is 1' );
$t->isa_ok( $vAux['PROCESS'], 'string', 'Verify if PROCESS is a string' );
/*$test->load('StartCase2');
$test->runSingle();
$test->load('StartCase3');
$test->runSingle();
$test->load('StartCase4');
$test->runSingle();
$test->load('StartCase5');
$test->runSingle();*/
//$test->load('DeleteCreatedApplications');
//$test->runAll();
?>
