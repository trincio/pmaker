<?php

$G_MAIN_MENU = "rbac";
$G_BACK_PAGE = "rbac/roleList";
$G_SUB_MENU  = "cancel";
$G_MENU_SELECTED = 1;

$uid = isset($_GET[0])?$_GET[0]:'';//$URI_VARS[0];
$_SESSION['CURRENT_ROLE_PARENT'] = $uid;
$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );


G::LoadClassRBAC ("roles");
$obj = new RBAC_Role;
$obj->SetTo ($dbc);
$obj->Load($uid);

$obj->Fields['EDIT_ROLES'] = G::LoadMessageXml ('ID_ROLES');
$obj->Fields['EDIT_PERMISSIONS'] = G::LoadMessageXml ('ID_PERMISSIONS');

$G_PUBLISH = new Publisher;
$G_PUBLISH->SetTo ($dbc);
$fields = $obj->Fields;
$fields['CURRENT_APPLICATION'] = $_SESSION['CURRENT_APPLICATION'];
$G_PUBLISH->AddContent ( "xmlform", "xmlform", "rbac/roleEdit", "", $fields, "roleEdit2");
$content = '';//G::LoadContent( "rbac/myApp" );
G::RenderPage( "publish" );

?>