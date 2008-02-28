<?php

$G_MAIN_MENU = "rbac";
$G_SUB_MENU  = "rbac.userView";
$G_MENU_SELECTED = 0;

$uid = $HTTP_SESSION_VARS['CURRENT_USER'];
G::LoadClassRBAC ("user");
$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );
$obj = new RBAC_user;

$obj->SetTo ($dbc);
$access = $RBAC->userCanAccess ("RBAC_CREATE_USERS" );

$obj->SetTo ($dbc);
$obj->Load ($uid);

$G_PUBLISH = new Publisher;
$G_PUBLISH->SetTo ($dbc);
$G_PUBLISH->AddContent ( "xmlform", "view", "rbac/userView", "", $obj->Fields, "userNew2");
$content = '';//G::LoadContent( "rbac/myApp" );
G::RenderPage( "publish" );

?>