<?php

  //G::genericForceLogin( 'WF_MYINFO' , 'login/noViewPage', $urlLogin = 'login/login' );

  G::LoadClass('{$pack->classFile}');
  
  $dbc = new DBConnection();
  $ses = new DBSession($dbc);

  ${$pack->name} = new {$pack->class}( $dbc );

  if ($_POST['form']['{$pack->key}']==='') unset($_POST['form']['{$pack->key}']);
  ${$pack->name}->Save( $_POST['form'] );

?>