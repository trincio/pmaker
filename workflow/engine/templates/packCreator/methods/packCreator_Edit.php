<?php

  //G::genericForceLogin( 'WF_MYINFO' , 'login/noViewPage', $urlLogin = 'login/login' );

  G::LoadClass('{$pack->classFile}');
  
  $dbc = new DBConnection();
  $ses = new DBSession($dbc);

  ${$pack->name} = new {$pack->class}( $dbc );
  ${$pack->name}->Fields['{$pack->key}']=(isset($_GET['{$pack->key}'])) ? urldecode($_GET['{$pack->key}']):'0';
  ${$pack->name}->Load( ${$pack->name}->Fields['{$pack->key}'] );
  ${$pack->name}->Fields['PRO_UID'] = isset(${$pack->name}->Fields['PRO_UID'])?${$pack->name}->Fields['PRO_UID']:$_GET['PRO_UID'];

  $G_PUBLISH = new Publisher();
  $G_PUBLISH->AddContent('xmlform', 'xmlform', '{$pack->name}/{$pack->name}_Edit', '', ${$pack->name}->Fields , SYS_URI.'{$pack->name}/{$pack->name}_Save');
  
  G::RenderPage( "publish" , "blank" );
?>