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
  require_once(PATH_CORE.'/classes/model/Step.php');

  require_once (  PATH_CORE . "config/databases.php");  

  $dbc = new DBConnection(); 
  $ses = new DBSession( $dbc);
 
  $obj = new Step ($dbc); 
  $t   = new lime_test( 9, new lime_output_color() );
 
  $t->diag('class Step' );
  $t->isa_ok( $obj  , 'Step',  'class Step created');

  //method load
  $t->can_ok( $obj,      'load',   'load() is callable' );
  //method save
  $t->can_ok( $obj,      'update',   'update() is callable' );
  //method delete
  $t->can_ok( $obj,      'delete',   'delete() is callable' );
  //method create
  $t->can_ok( $obj,      'create',   'create() is callable' );

  
  class StepTest extends UnitTest
  {
  	function CreateStep($data,$fields)
  	{
  	  try
  	  {
  	    $Step=new Step();
  	    $result=$Step->create($fields);
  	    $this->domain->addDomainValue('CREATED',$Step->getStepUid());
  	    return $result;
  	  }
  	  catch(Exception $e)
  	  {
  	  	$result=array('Exception!! '=> $e->getMessage());
  	  	if(isset($e->aValidationFailures))
  	  		$result['ValidationFailures'] = $e->aValidationFailures;
  	    return $result;
  	  }
  	}
  	function UpdateStep($data,$fields)
  	{
  	  try
  	  {
  	    $Step=new Step();
  	    $result=$Step->update($fields);
  	    return $result;
  	  }
  	  catch(Exception $e)
  	  {
  	    return array('Exception!! '=> $e->getMessage());
  	  }
  	}
  	function LoadStep($data,$fields)
  	{
  	  try
  	  {
  	    $Step=new Step();
  	    $result=$Step->load($fields['STEP_UID']);
  	    return $result;
  	  }
  	  catch(Exception $e)
  	  {
  	    return array('Exception!! '=> $e->getMessage());
  	  }
  	}
  	function RemoveStep($data,$fields)
  	{
  	  try
  	  {
  	    $Step=new Step();
  	    $result=$Step->remove($fields['STEP_UID']);
  	    return $result;
  	  }
  	  catch(Exception $e)
  	  {
  	    return array('Exception!! '=> $e->getMessage());
  	  }
  	}
  }
  $test=new StepTest('step.yml',$t);
  $test->domain->addDomain('CREATED');
  $test->load('CreateTestSteps');
  $test->runAll();
  $test->load('StepUnitTest');
  $test->runAll();
