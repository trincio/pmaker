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
  //G::LoadClass ( 'user');
  G::LoadSystem ( 'testTools');
  require_once(PATH_CORE.'/classes/model/StepTrigger.php');

  require_once (  PATH_CORE . "config/databases.php");  

  $dbc = new DBConnection(); 
  $ses = new DBSession( $dbc);
 
  $obj = new StepTrigger ($dbc); 
  $t   = new lime_test( 5, new lime_output_color() );
 
  $t->diag('class StepTrigger' );
  $t->isa_ok( $obj  , 'StepTrigger',  'class StepTrigger created');

  //method load
  $t->can_ok( $obj,      'load',   'load() is callable' );
  //method save
  $t->can_ok( $obj,      'update',   'update() is callable' );
  //method delete
  $t->can_ok( $obj,      'delete',   'delete() is callable' );
  //method create
  $t->can_ok( $obj,      'create',   'create() is callable' );

  
  class StepTriggerTest extends UnitTest
  {
  	function CreateStepTrigger($data,$fields)
  	{
  	  try
  	  {
  	    $StepTrigger=new StepTrigger();
  	    $result=$StepTrigger->create($fields);
  	    $this->domain->addDomainValue('CREATED',$StepTrigger->getStepUid());
  	    $this->domain->addDomainValue('CREATED_TAS',$StepTrigger->getTasUid());
  	    $this->domain->addDomainValue('CREATED_TRI',$StepTrigger->getTriUid());
  	    $this->domain->addDomainValue('CREATED_TYPE',$StepTrigger->getStType());
  	    return $result;
  	  }
  	  catch(Exception $e)
  	  {
  	    return array('Exception!! '=> $e->getMessage());
  	  }
  	}
  	function UpdateStepTrigger($data,$fields)
  	{
  	  try
  	  {
  	    $StepTrigger=new StepTrigger();
  	    $result=$StepTrigger->update($fields);
  	    return $result;
  	  }
  	  catch(Exception $e)
  	  {
  	    return array('Exception!! '=> $e->getMessage());
  	  }
  	}
  	function LoadStepTrigger($data,$fields)
  	{
  	  try
  	  {
  	    $StepTrigger=new StepTrigger();
  	    $result=$StepTrigger->load($fields['STEP_UID'],$fields['TAS_UID'],$fields['TRI_UID'],$fields['ST_TYPE']);
  	    return $result;
  	  }
  	  catch(Exception $e)
  	  {
  	    return array('Exception!! '=> $e->getMessage());
  	  }
  	}
  	function RemoveStepTrigger($data,$fields)
  	{
  	  try
  	  {
  	    $StepTrigger=new StepTrigger();
  	    $result=$StepTrigger->remove($fields['STEP_UID'],$fields['TAS_UID'],$fields['TRI_UID'],$fields['ST_TYPE']);
  	    return $result;
  	  }
  	  catch(Exception $e)
  	  {
  	    return array('Exception!! '=> $e->getMessage());
  	  }
  	}
  }
  $test=new StepTriggerTest('StepTrigger.yml',$t);
  /*
  $this->domain->addDomainValue('CREATED');
  $this->domain->addDomainValue('CREATED_TAS');
  $this->domain->addDomainValue('CREATED_TRI');
  $this->domain->addDomainValue('CREATED_TYPE');
  */
  $test->load('CreateTestStepTriggers');
  $test->runAll();
  $test->load('StepTriggerUnitTest');
  $test->runAll();
