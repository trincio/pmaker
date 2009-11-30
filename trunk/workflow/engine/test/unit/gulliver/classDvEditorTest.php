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
  G::LoadSystem ( 'dvEditor');

  $t = new lime_test( 3, new lime_output_color());
  $obj = "XmlForm_Field_HTML";
  $method = array ( );
  $testItems = 0;
  $class_methods = get_class_methods('XmlForm_Field_HTML');
  foreach ($class_methods as $method_name) {
    $methods[ $testItems ] = $method_name;
    $testItems++;
  }
  $t->diag('class XmlForm_Field_HTML' );
  $t->is(  $testItems , 18,  "class XmlForm_Field_HTML " . $testItems . " methods." );
  $t->is( $obj  , 'XmlForm_Field_HTML',  'class XmlForm_Field_HTML created');
  $t->todo(  'review all pendings in this class');
