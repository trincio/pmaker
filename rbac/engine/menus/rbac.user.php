<?php

global $G_TMP_MENU;
global $canCreateUsers;

$G_TMP_MENU->AddRawOption( "rbac/userNew.html" );

switch( SYS_LANG )
{
case 'es':
  $G_TMP_MENU->Labels = array(
    "Añadir Nuevo Usuario"
  );
  break;
case 'po':
  $G_TMP_MENU->Labels = array(
    "Inserir Novo Usuario"
  );
  break;
default:
  $G_TMP_MENU->Labels = array(
    "Add New User"
  );
  break;
}

if ($canCreateUsers != 1 ) 
  $G_TMP_MENU->DisableOptionPos(0);
?>