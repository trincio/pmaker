<?php

$G_MAIN_MENU = "rbac";
$G_SUB_MENU  = "rbac.appView";
$G_MENU_SELECTED = 1;

$canCreatePerm = $RBAC->userCanAccess("RBAC_CREATE_PERMISSION" );

$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );

$G_PUBLISH = new Publisher;
$G_PUBLISH->SetTo ($dbc);
$G_PUBLISH->AddContent ( "view", "treePerm");
$content = '';//G::LoadContent( "rbac/myApp" );
G::RenderPage( "publish" );

?>