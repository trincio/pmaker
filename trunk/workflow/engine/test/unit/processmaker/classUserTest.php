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
  require_once(PATH_CORE.'/classes/model/Users.php');

  require_once (  PATH_CORE . "config/databases.php");  

  $dbc = new DBConnection(); 
  $ses = new DBSession( $dbc);
 
  $obj = new Users ($dbc); 
  $t   = new lime_test( 12, new lime_output_color() );
 
  $t->diag('class User' );
  $t->isa_ok( $obj  , 'Users',  'class User created');

  //method load
  $t->can_ok( $obj,      'load',   'load() is callable' );
  //method save
  $t->can_ok( $obj,      'update',   'update() is callable' );
  //method delete
  $t->can_ok( $obj,      'delete',   'delete() is callable' );
  //method create
  $t->can_ok( $obj,      'create',   'create() is callable' );

  
  class UserTest extends UnitTest
  {
  	function CreateUser($data,$fields)
  	{
  	  try
  	  {
  	    $User=new Users();
  	    $result=$User->create($fields);
  	    $this->domain->addDomainValue('CREATED',$User->getUsrUid());
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
  	function UpdateUser($data,$fields)
  	{
  	  try
  	  {
  	    $User=new Users();
  	    $result=$User->update($fields);
  	    return $result;
  	  }
  	  catch(Exception $e)
  	  {
  	    return array('Exception!! '=> $e->getMessage());
  	  }
  	}
  	function LoadUser($data,$fields)
  	{
  	  try
  	  {
  	    $User=new Users();
  	    $result=$User->load($fields['USR_UID']);
  	    return $result;
  	  }
  	  catch(Exception $e)
  	  {
  	    return array('Exception!! '=> $e->getMessage());
  	  }
  	}
  	function RemoveUser($data,$fields)
  	{
  	  try
  	  {
  	    $User=new Users();
  	    $result=$User->remove($fields['USR_UID']);
  	    return $result;
  	  }
  	  catch(Exception $e)
  	  {
  	    return array('Exception!! '=> $e->getMessage());
  	  }
  	}
  }
  $test=new UserTest('user.yml',$t);
  $test->domain->addDomain('CREATED');
  $test->load('CreateTestUsers');
  $test->runAll();
  $test->load('UserUnitTest');
  $test->runAll();
