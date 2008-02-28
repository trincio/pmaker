<?php

$G_MAIN_MENU = "rbac";
$G_BACK_PAGE = "rbac/appList";
$G_SUB_MENU  = "cancel";
$G_MENU_SELECTED = 1;

$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );
$G_PUBLISH = new Publisher;
$G_PUBLISH->SetTo ($dbc);
$G_HEADER->addScriptCode('msgEmptyField = "msgEmptyField";');
$G_PUBLISH->AddContent ( "xmlform", "xmlform", "rbac/appNew", "", '', "appNew2");
$content = '';//'';//G::LoadContent( "rbac/myApp" );
G::RenderPage( "publish" );

?>