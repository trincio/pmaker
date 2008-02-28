<?php

global $G_TMP_MENU;

$G_TMP_MENU->AddIdRawOption("OP2b", "rbac/userChangeLdap.html" );
$G_TMP_MENU->AddIdRawOption("OP2c", "rbac/userTestLdap.html" );
$G_TMP_MENU->AddIdRawOption("OP4",  "Javascript:go();", 'absolute' );

switch( SYS_LANG )
{
case 'es':
  $G_TMP_MENU->Labels = array(
    "LDAP/AD", 
    'Test Login',
    "Asignar Roles"
  );
  break;
case 'po':
  $G_TMP_MENU->Labels = array(
    "LDAP/AD", 
    'Test Login',
    "Asignar Roles"
  );
  break;
default:
  $G_TMP_MENU->Labels = array(
    "LDAP/AD", 
    'Test Login',
    "Assign Role"
  );
  break;
}

global $access;
global $useLdap;

if ($access != 1) {
  $G_TMP_MENU->DisableOptionId ("OP4");
}  

if ( ! $useLdap ) {
  $G_TMP_MENU->DisableOptionId ("OP2b");
  $G_TMP_MENU->DisableOptionId ("OP2c");
}

?>