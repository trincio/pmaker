<?php
$unitFilename = $_SERVER['PWD'] . '/test/bootstrap/unit.php' ;
require_once( $unitFilename );
require_once(PATH_THIRDPARTY . '/lime/lime.php');
require_once(PATH_THIRDPARTY.'lime/yaml.class.php');

require_once(PATH_CORE . 'config/databases.php');
require_once('propel/Propel.php');
Propel::init(PATH_CORE . 'config/databases.php');

G::LoadThirdParty('smarty/libs', 'Smarty.class');
G::LoadSystem('error');
G::LoadSystem('xmlform');
G::LoadSystem('xmlDocument');
G::LoadSystem('form');
G::LoadSystem('dbtable');
G::LoadSystem('testTools');
require_once(PATH_CORE . 'classes/model/Route.php');

$obj = new Route();
$t   = new lime_test(5, new lime_output_color());

$t->diag('Class Route');

//class Route
$t->isa_ok($obj, 'Route', 'Class Route created!');

//method load
$t->can_ok($obj, 'load', 'load() is callable!');

//method create
$t->can_ok($obj, 'create', 'create() is callable!');

//method update
$t->can_ok($obj, 'update', 'update() is callable!');

//method remove
$t->can_ok($obj, 'remove', 'remove() is callable!');

/*****   TEST CLASS ROUTE   *****/
///////// INITIAL VALUES /////////
define('SYS_LANG', 'en');
//Test class
class RouteTest extends unitTest
{
  function loadTest($aTestData, $aFields)
  {
    $oRoute = new Route();
    try {
      return $oRoute->load($aFields['ROU_UID']);
    }
    catch (Exception $oError) {
    	return $oError;
    }
  }
  function createTest($aTestData, $aFields)
  {
    $oRoute = new Route();
    try {
      return $oRoute->create($aFields);
    }
    catch (Exception $oError) {
    	return $oError;
    }
  }
  function updateTest($aTestData, $aFields)
  {
    $oRoute = new Route();
    try {
      return $oRoute->update($aFields);
    }
    catch (Exception $oError) {
    	return $oError;
    }
  }
  function removeTest($aTestData, $aFields)
  {
    $oRoute = new Route();
    try {
      return $oRoute->remove($aFields['ROU_UID']);
    }
    catch (Exception $oError) {
    	return $oError;
    }
  }
}
//Initialize the test class (ymlTestDefinitionFile, limeTestObject, testDomain)
$oRouteTest = new RouteTest('route.yml', $t, new ymlDomain());
$oRouteTest->load('load1');
$vAux = $oRouteTest->runSingle();
//var_dump($vAux);echo "\n\n";
$oRouteTest->load('load2');
$vAux = $oRouteTest->runSingle();
//var_dump($vAux);echo "\n\n";
$oRouteTest->load('create1');
$vAux = $oRouteTest->runSingle();
//var_dump($vAux);echo "\n\n";
$oRouteTest->load('create2');
$vAux = $oRouteTest->runSingle();
//var_dump($vAux);echo "\n\n";
$oRouteTest->load('update1');
$vAux = $oRouteTest->runSingle();
//var_dump($vAux);echo "\n\n";
$oRouteTest->load('update2');
$vAux = $oRouteTest->runSingle();
//var_dump($vAux);echo "\n\n";
$oRouteTest->load('remove1');
$vAux = $oRouteTest->runSingle();
//var_dump($vAux);echo "\n\n";
$oRouteTest->load('remove2');
$vAux = $oRouteTest->runSingle();
//var_dump($vAux);echo "\n\n";
?>