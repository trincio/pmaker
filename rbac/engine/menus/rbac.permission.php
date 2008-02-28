<?php

global $G_TMP_MENU;

$G_TMP_MENU->AddRawOption( "rbac/permNew.html" );

switch( SYS_LANG )
{
case 'es':
  $G_TMP_MENU->Labels = array(
    "Añadir Nuevo Permiso"
  );
  break;
case 'po':
  $G_TMP_MENU->Labels = array(
    "Inserir Novo Permisio"
  );
  break;
default:
  $G_TMP_MENU->Labels = array(
    "Add New Permission"
  );
  break;
}
?>