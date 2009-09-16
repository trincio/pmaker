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
  G::LoadSystem ( 'fckEditor');

  $t = new lime_test( 2, new lime_output_color());
  $obj = "fckEditor";
  $method = array ( );
  $testItems = 0;
  $class_methods = get_class_methods('fckEditor');
  foreach ($class_methods as $method_name) {
    $methods[ $testItems ] = $method_name;
    $testItems++;
  }
  $t->diag('class fckEditor' );
  $t->is(  $testItems , 0,  "class fckEditor " . $testItems . " methods." );
  $t->todo(  'review all pendings in this class');
