<?php
  require_once( PATH_THIRDPARTY . '/lime/lime.php');
  require_once( PATH_THIRDPARTY.'lime/yaml.class.php');
 
  G::LoadThirdParty('smarty/libs','Smarty.class');
  G::LoadSystem ( 'error');
  G::LoadSystem ( 'xmlform');
  G::LoadSystem ( 'xmlDocument');
  G::LoadSystem ( 'form');
  G::LoadSystem ( 'dbconnection');
  G::LoadSystem ( 'dbsession');
  G::LoadSystem ( 'dbrecordset');
  G::LoadSystem ( 'dbtable');
  G::LoadClass ( '{$classFile}');

  require_once (  PATH_CORE . "config/databases.php");  

  $dbc = new DBConnection(); 
  $ses = new DBSession( $dbc);
 
  $obj = new {$className} ($dbc); 
  $t   = new lime_test( {$testItems}, new lime_output_color() );
 
  $t->diag('class {$className}' );
  $t->isa_ok( $obj  , '{$className}',  'class {$className} created');

{foreach from=$methods key=methodName item=parameters}
  //method {$methodName}
  $t->can_ok( $obj,      '{$methodName}',   '{$methodName}() is callable' );

//  $result = $obj->{$methodName} ( {$parameters});
//  $t->isa_ok( $result,      'NULL',   'call to method {$methodName} ');


{/foreach}

  $t->fail(  'review all pendings methods in this class');
