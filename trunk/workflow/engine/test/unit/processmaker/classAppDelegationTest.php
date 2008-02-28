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
  //G::LoadClass ( 'appDelegation');
  require_once(PATH_CORE.'/classes/model/AppDelegation.php');


  $dbc = new DBConnection(); 
  $ses = new DBSession( $dbc);
 
  $obj = new AppDelegation ($dbc); 
  $t   = new lime_test( 1, new lime_output_color() );
 
  $t->diag('class AppDelegation' );
  $t->isa_ok( $obj  , 'AppDelegation',  'class AppDelegation created');

  class AppDel extends unitTest
  {
    function CreateEmptyAppDelegation($data,$fields)
    {
      $obj=new AppDelegation();
      $res=$obj->createAppDelegation($fields);
      return $res;
    }
    function CreateDuplicated($data,$fields)
    {
      $obj1=new AppDelegation();
      $res=$obj1->createAppDelegation($fields);
      $this->domain->addDomainValue('createdAppDel',serialize($fields));
      $obj2=new AppDelegation();
      $res=$obj2->createAppDelegation($fields);
      $this->domain->addDomainValue(serialize($fields));
      return $res;
    }
    function CreateNewAppDelegation($data,$fields)
    {
      $obj=new AppDelegation();
      $res=$obj->createAppDelegation($fields);
      $this->domain->addDomainValue('createdAppDel',serialize($fields));
      return $res;
    }
    function DeleteAppDelegation($data,$fields)
    {
      $obj=new AppDelegation();
      /* Using delete inherited function */
      $fields=unserialize($fields['Fields']);
      $obj->setAppUid($fields['APP_UID']);
      $obj->setDelIndex($fields['DEL_INDEX']);
      $res=$obj->delete();
      return $res;
    }
  }
  $tt=new AppDel('appDelegation.yml',$t,$domain);
  $domain->addDomain("createdAppDel");
  $tt->load('CreateDelApplication');
  $tt->runAll();
  $tt->load('DeleteCretedAppDelegations');
  $tt->runAll();

?>