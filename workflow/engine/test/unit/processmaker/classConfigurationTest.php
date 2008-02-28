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
  require_once(PATH_CORE.'/classes/model/Configuration.php');

  require_once (  PATH_CORE . "config/databases.php");  

  $dbc = new DBConnection(); 
  $ses = new DBSession( $dbc);
 
  $obj = new Configuration ($dbc); 
  $t   = new lime_test( 25, new lime_output_color() );
 
  $t->diag('class Configuration' );
  $t->isa_ok( $obj  , 'Configuration',  'class Configuration created');

  //method load
  $t->can_ok( $obj,      'load',   'load() is callable' );
  //method save
  $t->can_ok( $obj,      'update',   'update() is callable' );
  //method delete
  $t->can_ok( $obj,      'delete',   'delete() is callable' );
  //method create
  $t->can_ok( $obj,      'create',   'create() is callable' );

  
  class ConfigurationTest extends UnitTest
  {
  	function CreateConfiguration($data,$fields)
  	{
  	  try
  	  {
  	    $Configuration=new Configuration();
  	    $result=$Configuration->create($fields);
  	    $this->domain->addDomainValue('CREATED_UID',$Configuration->getCfgUid());
  	    $this->domain->addDomainValue('CREATED_OBJ',$Configuration->getObjUid());
  	    $this->domain->addDomainValue('CREATED_PRO',$Configuration->getProUid());
  	    $this->domain->addDomainValue('CREATED_USR',$Configuration->getUsrUid());
  	    return $result;
  	  }
  	  catch(Exception $e)
  	  {
  	    return array('Exception!! '=> $e->getMessage());
  	  }
  	}
  	function UpdateConfiguration($data,$fields)
  	{
  	  try
  	  {
  	    $Configuration=new Configuration();
  	    $result=$Configuration->update($fields);
  	    return $result;
  	  }
  	  catch(Exception $e)
  	  {
  	    return array('Exception!! '=> $e->getMessage());
  	  }
  	}
  	function LoadConfiguration($data,$fields)
  	{
  	  try
  	  {
  	    $Configuration=new Configuration();
  	    $result=$Configuration->load($fields['CFG_UID'], $fields['OBJ_UID'], $fields['PRO_UID'], $fields['USR_UID'], $fields['APP_UID']);
  	    return $result;
  	  }
  	  catch(Exception $e)
  	  {
  	    return array('Exception!! '=> $e->getMessage());
  	  }
  	}
  	function RemoveConfiguration($data,$fields)
  	{
  	  try
  	  {
  	    $Configuration=new Configuration();
  	    $result=$Configuration->remove($fields['CFG_UID'], $fields['OBJ_UID'], $fields['PRO_UID'], $fields['USR_UID'], $fields['APP_UID']);
  	    return $result;
  	  }
  	  catch(Exception $e)
  	  {
  	    return array('Exception!! '=> $e->getMessage());
  	  }
  	}
  }
  $test=new ConfigurationTest('configuration.yml',$t);
  $test->domain->addDomain('CREATED_UID');
  $test->domain->addDomain('CREATED_OBJ');
  $test->domain->addDomain('CREATED_PRO');
  $test->domain->addDomain('CREATED_USR');
  $test->load('CreateTestConfigurations');
  $test->runAll();
  $test->load('ConfigurationUnitTest');
  $test->runAll();
