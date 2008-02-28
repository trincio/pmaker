<?php
$G_MAIN_MENU = "rbac";
$G_SUB_MENU  = "rbac.role";
$G_MENU_SELECTED = 1;

$canCreateRole = $RBAC->userCanAccess("RBAC_CREATE_ROLE" );
$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );

$G_PUBLISH = new Publisher;
$G_PUBLISH->SetTo ($dbc);
$G_PUBLISH->AddContent ( "view", "treeRole");
$content = '';//G::LoadContent( "rbac/myApp" );
G::RenderPage( "publish" );

?>