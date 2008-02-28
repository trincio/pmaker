<?php

$G_MAIN_MENU = "rbac";
$G_SUB_MENU  = "rbac.appEdit";
$G_MENU_SELECTED = 1;

$appid = isset($_GET[0])?$_GET[0]:'';//$URI_VARS[0];
$HTTP_SESSION_VARS['CURRENT_APPLICATION'] = $appid;
$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );
G::LoadClassRBAC ("applications");
$obj = new RBAC_Application;
$obj->SetTo ($dbc);
$obj->Load($appid);

$obj->Fields['EDIT_ROLES'] = G::LoadMessageXml ('ID_ROLES');
$obj->Fields['EDIT_PERMISSIONS'] = G::LoadMessageXml ('ID_PERMISSIONS');
$G_PUBLISH = new Publisher;
$G_PUBLISH->SetTo ($dbc);
$G_PUBLISH->AddContent ( "xmlform", "xmlform", "rbac/appEdit", "", $obj->Fields, "../appEdit2");
$content = '';//G::LoadContent( "rbac/myApp" );
G::RenderPage( "publish" );

?>