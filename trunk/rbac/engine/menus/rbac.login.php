<?php

global $G_TMP_MENU;

$G_TMP_MENU->AddRawOption( "login/login.html" );
$G_TMP_MENU->AddRawOption( "login/dbInfo.html" );


switch( SYS_LANG )
{
case 'po':
  $G_TMP_MENU->Labels = array(
    "login",
    "about"
  );
  break;
case 'es':
  $G_TMP_MENU->Labels = array(
    "Iniciar Sesión",
    "acerca de"
  );
  break;
default:
  $G_TMP_MENU->Labels = array(
    "login",
    "about"
  );
  break;
}

?>