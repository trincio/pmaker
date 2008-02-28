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
require_once(PATH_CORE . 'classes/model/InputDocument.php');

$obj = new InputDocument();
$t   = new lime_test(9, new lime_output_color());

$t->diag('Class InputDocument');

//class InputDocument
$t->isa_ok($obj, 'InputDocument', 'Class InputDocument created!');

//method load
$t->can_ok($obj, 'load', 'load() is callable!');

//method create
$t->can_ok($obj, 'create', 'create() is callable!');

//method update
$t->can_ok($obj, 'update', 'update() is callable!');

//method remove
$t->can_ok($obj, 'remove', 'remove() is callable!');

//method getInpDocTitle
$t->can_ok($obj, 'getInpDocTitle', 'getInpDocTitle() is callable!');

//method setInpDocTitle
$t->can_ok($obj, 'setInpDocTitle', 'setInpDocTitle() is callable!');

//method getInpDocComment
$t->can_ok($obj, 'getInpDocDescription', 'getInpDocDescription() is callable!');

//method setInpDocComment
$t->can_ok($obj, 'setInpDocDescription', 'setInpDocDescription() is callable!');

/*****   TEST CLASS INPUTDOCUMENT   *****/
///////// INITIAL VALUES /////////
define('SYS_LANG', 'en');
//Test class
class InputDocumentTest extends unitTest
{
  function loadTest($aTestData, $aFields)
  {
    $oInputDocument = new InputDocument();
    try {
      return $oInputDocument->load($aFields['INP_DOC_UID']);
    }
    catch (Exception $oError) {
    	return $oError;
    }
  }
  function createTest($aTestData, $aFields)
  {
    $oInputDocument = new InputDocument();
    try {
      return $oInputDocument->create($aFields);
    }
    catch (Exception $oError) {
    	return $oError;
    }
  }
  function updateTest($aTestData, $aFields)
  {
    $oInputDocument = new InputDocument();
    try {
      return $oInputDocument->update($aFields);
    }
    catch (Exception $oError) {
    	return $oError;
    }
  }
  function removeTest($aTestData, $aFields)
  {
    $oInputDocument = new InputDocument();
    try {
      return $oInputDocument->remove($aFields['INP_DOC_UID']);
    }
    catch (Exception $oError) {
    	return $oError;
    }
  }
}
//Initialize the test class (ymlTestDefinitionFile, limeTestObject, testDomain)
$oInputDocumentTest = new InputDocumentTest('inputDocument.yml', $t, new ymlDomain());
$oInputDocumentTest->load('load1');
$vAux = $oInputDocumentTest->runSingle();
//var_dump($vAux);echo "\n\n";
$oInputDocumentTest->load('load2');
$vAux = $oInputDocumentTest->runSingle();
//var_dump($vAux);echo "\n\n";
$oInputDocumentTest->load('create1');
$vAux = $oInputDocumentTest->runSingle();
//var_dump($vAux);echo "\n\n";
$oInputDocumentTest->load('create2');
$vAux = $oInputDocumentTest->runSingle();
//var_dump($vAux);echo "\n\n";
$oInputDocumentTest->load('update1');
$vAux = $oInputDocumentTest->runSingle();
//var_dump($vAux);echo "\n\n";
$oInputDocumentTest->load('update2');
$vAux = $oInputDocumentTest->runSingle();
//var_dump($vAux);echo "\n\n";
$oInputDocumentTest->load('remove1');
$vAux = $oInputDocumentTest->runSingle();
//var_dump($vAux);echo "\n\n";
$oInputDocumentTest->load('remove2');
$vAux = $oInputDocumentTest->runSingle();
//var_dump($vAux);echo "\n\n";
?>