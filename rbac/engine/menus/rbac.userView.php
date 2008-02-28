<?php

global $G_TMP_MENU;

$G_TMP_MENU->AddIdRawOption("OP1",  "rbac/userEdit.html" );
$G_TMP_MENU->AddIdRawOption("OP2",  "rbac/userChangePwd.html" );
$G_TMP_MENU->AddIdRawOption("OP2b", "rbac/userChangeLdap.html" );
$G_TMP_MENU->AddIdRawOption("OP2c", "rbac/userTestLdap.html" );
$G_TMP_MENU->AddIdRawOption("OP3",  "rbac/userViewRole.html" );
$G_TMP_MENU->AddIdRawOption("OP4",  "rbac/userAssignRole.html" );

switch( SYS_LANG )
{
case 'es':
  $G_TMP_MENU->Labels = array(
    "Editar Usuario",
    "Reiniciar Password",
    "LDAP/AD", 
    'Test Login',
    "Ver Roles",
    "Asignar Roles"
  );
  break;
case 'po':
  $G_TMP_MENU->Labels = array(
    "Editar Usuario",
    "Reiniciar Password",
    "LDAP/AD", 
    'Test Login',
    "Ver Roles",
    "Asignar Roles"
  );
  break;
default:
  $G_TMP_MENU->Labels = array(
    "Edit User",
    "Reset Password",
    "LDAP/AD", 
    'Test Login',
    "View Roles",
    "Assign Role"
  );
  break;
}

global $access;
global $useLdap;

if ($access != 1) {
  $G_TMP_MENU->DisableOptionId ("OP1");
  $G_TMP_MENU->DisableOptionId ("OP2");
  $G_TMP_MENU->DisableOptionId ("OP4");
}  

if ( $useLdap ) 
  $G_TMP_MENU->DisableOptionId ("OP2");
else  {
  $G_TMP_MENU->DisableOptionId ("OP2b");
  $G_TMP_MENU->DisableOptionId ("OP2c");
}

?>