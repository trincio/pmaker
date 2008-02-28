<?php

global $G_TMP_MENU;
global $HTTP_SESSION_VARS;
$appid = $HTTP_SESSION_VARS['CURRENT_APPLICATION'];

$G_TMP_MENU->AddIdRawOption( "OP1", "rbac/appList.html" );
$G_TMP_MENU->AddIdRawOption( "OP2", "rbac/appDel.html" );

switch( SYS_LANG )
{
case 'es':
  $G_TMP_MENU->Labels = array(
    "Cancelar",
    "Eliminar Applicación"
  );
  break;
case 'po':
  $G_TMP_MENU->Labels = array(
    "Cancelar",
    "Eliminar Application"
  );
  break;
default:
  $G_TMP_MENU->Labels = array(
    "Cancel",
    "Remove Application"
  );
  break;
}

//si no hay nada relacionado a esta aplicación se puede BORRAR!!
$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );
//G::LoadClassRBAC ("applications");
$obj = New RBAC_Application;
$obj->SetTo ($dbc);
$sw = $obj->canRemoveApplication ($appid);
if ($sw > 0)
  $G_TMP_MENU->disableOptionId ("OP2");

?>