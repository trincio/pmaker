<?php

global $G_TMP_MENU;
global $canCreateApp;

$G_TMP_MENU->AddOption( G::LoadMenuXml ('ID_EDIT'), "rbac/authEdit.html" );
$G_TMP_MENU->AddOption( G::LoadMenuXml ('ID_TEST'), "rbac/authTest.html" );
$G_TMP_MENU->AddOption( G::LoadMenuXml ('ID_ADD_USERS'), "rbac/authAddUser.html" );

?>