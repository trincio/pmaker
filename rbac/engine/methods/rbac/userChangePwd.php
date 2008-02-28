<?php
$G_MAIN_MENU         = 'rbac';
$G_SUB_MENU          = 'rbac.userView';
$G_MENU_SELECTED     = 0;
$G_SUB_MENU_SELECTED = 1;

$dbc    = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME);
$access = $RBAC->userCanAccess('RBAC_CREATE_USERS');

G::LoadClassRBAC('user');
$obj = new RBAC_User;
$obj->SetTo($dbc);
$obj->Load($_SESSION['CURRENT_USER']);
$G_PUBLISH = new Publisher;
$G_PUBLISH->SetTo($dbc);
$G_PUBLISH->AddContent('xmlform', 'xmlform', 'rbac/userChangePwd', '', $obj->Fields, 'userChangePwd2');
G::RenderPage( 'publish');
?>