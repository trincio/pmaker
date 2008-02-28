<?php

  //G::genericForceLogin( 'WF_MYINFO' , 'login/noViewPage', $urlLogin = 'login/login' );

  G::LoadClass('{$pack->classFile}');
  
  $dbc = new DBConnection();
  $ses = new DBSession($dbc);

  ${$pack->name} = new {$pack->class}( $dbc );

  if (!isset($_POST['{$pack->key}'])) return;

  ${$pack->name}->Delete( $_POST['{$pack->key}'] );
  
?>