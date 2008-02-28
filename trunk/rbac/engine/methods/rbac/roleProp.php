<?php

$G_MAIN_MENU = "rbac";
$G_SUB_MENU  = "rbac.appView";
$G_MENU_SELECTED = 1;

$permid = isset($_GET[0])?$_GET[0]:'';//$URI_VARS[0];
$rolid  = $_SESSION['CURRENT_ROLE'];

G::LoadClassRBAC ( "roles" );
G::LoadClassRBAC ( "user" );
$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );


$canCreateRole = $RBAC->userCanAccess("RBAC_CREATE_PERMISSION" );
$obj = New RBAC_role;
$obj->SetTo ($dbc);
$parents = $obj->GetAllParents($rolid);
$_SESSION['CURRENT_ROLE_PARENTS'] = $parents;

if ( $permid != "" ) {
  $obj->flipFlopRole($rolid, $permid);
}

$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );

$G_PUBLISH = new Publisher;
$G_PUBLISH->SetTo ($dbc);
$G_PUBLISH->AddContent ( "view", "treePermRole");
$content = '';//G::LoadContent( "rbac/myApp" );
G::RenderPage( "publish" );

?>