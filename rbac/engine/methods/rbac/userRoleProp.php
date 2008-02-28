<?php

$G_MAIN_MENU         = 'rbac';
$G_SUB_MENU          = 'rbac.userView';
$G_MENU_SELECTED     = 0;
$G_SUB_MENU_SELECTED = 2;

//$permid = $URI_VARS[0];
$rolid  = $_SESSION['CURRENT_ROLE'];

G::LoadClassRBAC ('roles');
G::LoadClassRBAC ('user');
$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME);

$obj = new RBAC_user;
$obj->SetTo ($dbc);
$access = $RBAC->userCanAccess('RBAC_CREATE_USERS');
//$accessChangeRole = 0;

$obj = New RBAC_role;
$obj->SetTo ($dbc);
$parents = $obj->GetAllParents($rolid);
$_SESSION['CURRENT_ROLE_PARENTS'] = $parents;

/*if ( $permid != '' ) {
  $obj->flipFlopRole($rolid, $permid);
}*/

$G_PUBLISH = new Publisher;
//$G_PUBLISH->SetTo ($dbc);
$G_PUBLISH->AddContent ( 'view', 'treePermRole');
//$content = G::LoadContent( 'rbac/myApp' );
G::RenderPage( 'publish' );
?>