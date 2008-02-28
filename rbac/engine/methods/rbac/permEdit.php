<?php

$G_MAIN_MENU = "rbac";
$G_BACK_PAGE = "rbac/permList";
$G_SUB_MENU  = "cancel";
$G_MENU_SELECTED = 1;

$uid = isset($_GET['UID'])?$_GET['UID']:'';//$URI_VARS[0];
$_SESSION['CURRENT_PERM_PARENT'] = $uid;
$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );


G::LoadClassRBAC ("permissions");
$obj = new RBAC_Permission;
$obj->SetTo ($dbc);
$obj->Load($uid);

$obj->Fields['UID'] = $_SESSION['CURRENT_APPLICATION'];
$G_PUBLISH = new Publisher;
$G_PUBLISH->SetTo ($dbc);
$G_PUBLISH->AddContent ( "xmlform", "xmlform", "rbac/permEdit", "", $obj->Fields, "permEdit2");
$content = '';//G::LoadContent( "rbac/myApp" );
G::RenderPage( "publish" );

?>