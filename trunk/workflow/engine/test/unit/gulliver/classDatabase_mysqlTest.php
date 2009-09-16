<?php
  if ( !defined ('PATH_THIRDPARTY') ) {
    require_once(  $_SERVER['PWD']. '/test/bootstrap/unit.php');
  }
  require_once( PATH_THIRDPARTY . 'lime/lime.php');

  define ( 'G_ENVIRONMENT', G_TEST_ENV);
  G::LoadThirdParty('smarty/libs','Smarty.class');
  G::LoadSystem ( 'xmlform');
  G::LoadSystem ( 'xmlDocument');
  G::LoadSystem ( 'form');
  G::LoadSystem ( 'database_mysql');

  $t = new lime_test( 3, new lime_output_color());
  $obj = new database_base();
  $method = array ( );
  $testItems = 0;
  $class_methods = get_class_methods('database');
  foreach ($class_methods as $method_name) {
    $methods[ $testItems ] = $method_name;
    $testItems++;
  }
  $t->diag('class database' );
  $t->is(  $testItems , 19,  "class database " . $testItems . " methods." );
  $t->isa_ok( $obj  , 'database_base',  'class database_base created');
  $t->todo(  'review all pendings in this class');
