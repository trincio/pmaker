<?php

global $G_TMP_MENU;
global $canCreateApp;

$G_TMP_MENU->AddOption( G::LoadMenuXml ('ID_NEW_APPLICATION'), "rbac/appNew.html" );

if ( $canCreateApp != 1 )
  $G_TMP_MENU->DisableOptionPos (0);

?>