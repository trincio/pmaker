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
require_once(PATH_CORE . 'classes/model/TaskUser.php');

$obj = new TaskUser();
$t   = new lime_test(3, new lime_output_color());

$t->diag('Class TaskUser');

//class TaskUser
$t->isa_ok($obj, 'TaskUser', 'Class TaskUser created!');

//method create
$t->can_ok($obj, 'create', 'create() is callable!');

//method remove
$t->can_ok($obj, 'remove', 'remove() is callable!');

/*****   TEST CLASS TASKUSER   *****/
///////// INITIAL VALUES /////////
define('SYS_LANG', 'en');
//Test class
class TaskUserTest extends unitTest
{
  function createTest($aTestData, $aFields)
  {
    $oTaskUser = new TaskUser();
    try {
      return $oTaskUser->create($aFields);
    }
    catch (Exception $oError) {
    	return $oError;
    }
  }
  function removeTest($aTestData, $aFields)
  {
    $oTaskUser = new TaskUser();
    try {
      return $oTaskUser->remove($aFields['TAS_UID'], $aFields['USR_UID'], $aFields['TU_TYPE'], $aFields['TU_RELATION']);
    }
    catch (Exception $oError) {
    	return $oError;
    }
  }
}
//Initialize the test class (ymlTestDefinitionFile, limeTestObject, testDomain)
$oTaskUserTest = new TaskUserTest('taskUser.yml', $t, new ymlDomain());
$oTaskUserTest->load('create1');
$vAux = $oTaskUserTest->runSingle();
//var_dump($vAux);echo "\n\n";
$oTaskUserTest->load('create2');
$vAux = $oTaskUserTest->runSingle();
//var_dump($vAux);echo "\n\n";
$oTaskUserTest->load('remove1');
$vAux = $oTaskUserTest->runSingle();
//var_dump($vAux);echo "\n\n";
$oTaskUserTest->load('remove2');
$vAux = $oTaskUserTest->runSingle();
//var_dump($vAux);echo "\n\n";
?>