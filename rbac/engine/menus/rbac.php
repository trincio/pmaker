<?php

global $G_TMP_MENU;

$G_TMP_MENU->AddRawOption( "rbac/userList.html" );
$G_TMP_MENU->AddRawOption( "rbac/appList.html"  );
$G_TMP_MENU->AddRawOption( "rbac/authenticationList.html" );
$G_TMP_MENU->AddRawOption( "rbac/dbInfo.html"  );
$G_TMP_MENU->AddRawOption( "login/login.html"  );


switch( SYS_LANG )
{
case 'po':
  $G_TMP_MENU->Labels = array(
    "Usuarios",
    "Applicaciones",
    "Authentication Sources",
    "Info Database",
    "Saír do Sistema"
  );
  break;
case 'es':
  $G_TMP_MENU->Labels = array(
    "Usuarios",
    "Applicaciones",
    "Fuentes de Autenticación",
    "Información de BD",
    "Salir del Sistema"
  );
  break;
default:
  $G_TMP_MENU->Labels = array(
    "Users",
    "Applications",
    "Authentication Sources",
    "DB Info",
    "Logout"
  );
  break;
}

?>