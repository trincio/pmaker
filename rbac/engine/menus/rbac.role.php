<?php

global $G_TMP_MENU;

$G_TMP_MENU->AddIdRawOption( "OP1", "rbac/roleNew.html" );
$G_TMP_MENU->AddIdRawOption( "OP2", "rbac/roleList.html" );
$G_TMP_MENU->AddIdRawOption( "OP3", "rbac/permList.html" );
$G_TMP_MENU->AddIdRawOption( "OP4", "rbac/appList.html" );

switch( SYS_LANG )
{
case 'es':
  $G_TMP_MENU->Labels = array(
    "Añadir Nuevo Rol",
    "Ver Roles",
    "Ver Permisos",
    "Lista de Aplicaciones"
  );
  break;
case 'po':
  $G_TMP_MENU->Labels = array(
    "Inserir Novo Rol",
    "Ver Roles",
    "Ver Permisos",
    "Lista de Aplicaciones"
  );
  break;
default:
  $G_TMP_MENU->Labels = array(
    "Add New Role",
    "View Roles",
    "View Permissions",
    "Applications List"
  );
  break;
}

global $canCreateRole;
if ($canCreateRole != 1)
 $G_TMP_MENU->DisableOptionID ("OP1");

?>