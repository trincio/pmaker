<?php

  if ( !defined ('PATH_THIRDPARTY') ) {
    require_once(  $_SERVER['PWD']. '/test/bootstrap/unit.php');
  }

  require_once( PATH_THIRDPARTY . 'lime/lime.php');
  define ( 'G_ENVIRONMENT', G_TEST_ENV);
  require_once( PATH_CORE . 'config' . PATH_SEP . 'environments.php');

  global $G_ENVIRONMENTS;
  if ( isset ( $G_ENVIRONMENTS ) ) {
    $dbfile = $G_ENVIRONMENTS[ G_TEST_ENV ][ 'dbfile'];
    if ( !file_exists ( $dbfile ) ) {
      printf("%s \n", pakeColor::colorize( "dbfile $dbfile doesn't exist for environment " . G_ENVIRONMENT  , 'ERROR'));
      exit (200);
    }
    else
     include ( $dbfile );
  }
  else
   exit (201);

  require_once( PATH_GULLIVER . 'class.dbconnection.php');
  require_once( PATH_GULLIVER . 'class.dbsession.php');
  require_once( PATH_GULLIVER . 'class.dbrecordset.php');

G::LoadThirdParty('smarty/libs','Smarty.class');
G::LoadSystem ( 'xmlform');
G::LoadSystem ( 'xmlDocument');
G::LoadSystem ( 'form');
G::LoadSystem ( 'rbac');
G::LoadSystem ( 'dbconnection');
G::LoadSystem ( 'dbsession');
G::LoadSystem ( 'dbrecordset');
G::LoadSystem ( 'dbtable');


//$dbc = new DBConnection(); 
//$ses = new DBSession( $dbc);
//$obj = new DBTable ( $dbc, "APPLICATION" , array ( 'APP_UID' ) );
 
$t = new lime_test(26, new lime_output_color());
 
$obj = new RBAC ( 'field' ); 
$t->diag('class RBAC' );
$t->isa_ok( $obj  , 'RBAC',  'class RBAC created');
$t->can_ok( $obj,      'initDB',   'initDB()');
$t->can_ok( $obj,      'VerifyLogin',   'VerifyLogin()');
$t->can_ok( $obj,      'userCanAccess',   'userCanAccess()');
$t->can_ok( $obj,      'userCanAccessApp',   'userCanAccessApp()');
$t->can_ok( $obj,      'getPermissionsArray',   'getPermissionsArray()');
$t->can_ok( $obj,      'load',   'load()');
$t->can_ok( $obj,      'editUser',   'editUser()');
$t->can_ok( $obj,      'changePassword',   'changePassword()');
$t->can_ok( $obj,      'changePasswordEncrypted',   'changePasswordEncrypted()');
$t->can_ok( $obj,      'UserNameRepetido',   'UserNameRepetido()');
$t->can_ok( $obj,      'changePasswordEncrypted',   'changePasswordEncrypted()');
$t->can_ok( $obj,      'createUser',   'createUser()');
$t->can_ok( $obj,      'createUser_old',   'createUser_old()');
$t->can_ok( $obj,      'createUserName',   'createUserName()');
$t->can_ok( $obj,      'assignUserRole',   'assignUserRole()');
$t->can_ok( $obj,      'setUserRole',   'setUserRole()');
$t->can_ok( $obj,      'listAllUsers',   'listAllUsers()');
$t->can_ok( $obj,      'listAllUsersByRole',   'listAllUsersByRole()');
$t->can_ok( $obj,      'listAllRoles',   'listAllRoles()');
$t->can_ok( $obj,      'getUserName',   'getUserName()');
$t->can_ok( $obj,      'deleteUser',   'deleteUser()');
$t->can_ok( $obj,      'loadroleinfo',   'loadroleinfo()');
$t->can_ok( $obj,      'roleCodeRepetido',   'roleCodeRepetido()');
$t->can_ok( $obj,      'editRole',   'editRole()');

$t->todo(  'review all pendings in this class');
