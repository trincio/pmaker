<?php

global $G_TMP_MENU;
global $HTTP_SESSION_VARS;

$G_TMP_MENU->AddIdRawOption( "OP1", "rbac/roleList.html" );
$G_TMP_MENU->AddIdRawOption( "OP2", "rbac/permList.html" );
$G_TMP_MENU->AddIdRawOption( "OP3", "rbac/appList.html" );

switch( SYS_LANG )
{
case 'es':
  $G_TMP_MENU->Labels = array(
    "Ver Roles",
    "Ver Permisos",
    "Lista de Aplicaciones"
  );
  break;
case 'po':
  $G_TMP_MENU->Labels = array(
    "Ver Roles",
    "Ver Permisos",
    "Lista de Aplicaciones"
  );
  break;
default:
  $G_TMP_MENU->Labels = array(
    "View Roles",
    "View Permissions",
    "Applications List"
  );
  break;
}
?>