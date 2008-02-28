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
  require_once(PATH_CORE.'/classes/model/Triggers.php');

  require_once (  PATH_CORE . "config/databases.php");

  $dbc = new DBConnection();
  $ses = new DBSession( $dbc);

  $obj = new Triggers ($dbc);
  $t   = new lime_test( 25, new lime_output_color() );

  $t->diag('class Trigger' );
  $t->isa_ok( $obj  , 'Triggers',  'class Trigger created');

  //method load
  $t->can_ok( $obj,      'load',   'load() is callable' );
  //method save
  $t->can_ok( $obj,      'update',   'update() is callable' );
  //method delete
  $t->can_ok( $obj,      'delete',   'delete() is callable' );
  //method create
  $t->can_ok( $obj,      'create',   'create() is callable' );


  class TriggerTest extends UnitTest
  {
  	function CreateTrigger($data,$fields)
  	{
  	  try
  	  {
  	    $Trigger=new Triggers();
  	    $result=$Trigger->create($fields);
  	    $this->domain->addDomainValue('CREATED',$Trigger->getTriUid());
  	    return $result;
  	  }
  	  catch(Exception $e)
  	  {
  	    return array('Exception!! '=> $e->getMessage());
  	  }
  	}
  	function UpdateTrigger($data,$fields)
  	{
  	  try
  	  {
  	    $Trigger=new Triggers();
  	    $result=$Trigger->update($fields);
  	    return $result;
  	  }
  	  catch(Exception $e)
  	  {
  	    return array('Exception!! '=> $e->getMessage());
  	  }
  	}
  	function LoadTrigger($data,$fields)
  	{
  	  try
  	  {
  	    $Trigger=new Triggers();
  	    $result=$Trigger->load($fields['TRI_UID']);
  	    return $result;
  	  }
  	  catch(Exception $e)
  	  {
  	    return array('Exception!! '=> $e->getMessage());
  	  }
  	}
  	function RemoveTrigger($data,$fields)
  	{
  	  try
  	  {
  	    $Trigger=new Triggers();
  	    $result=$Trigger->remove($fields['TRI_UID']);
  	    return $result;
  	  }
  	  catch(Exception $e)
  	  {
  	    return array('Exception!! '=> $e->getMessage());
  	  }
  	}
  }
  $test=new TriggerTest('trigger.yml',$t);
  $test->domain->addDomain('CREATED');
  $test->load('CreateTestTriggers');
  $test->runAll();
  $test->load('TriggerUnitTest');
  $test->runAll();
?>