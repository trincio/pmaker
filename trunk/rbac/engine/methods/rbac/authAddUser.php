<?php
$G_MAIN_MENU         = 'rbac';
$G_SUB_MENU          = 'rbac.authSource';
$G_BACK_PAGE         = 'rbac/authenticationList.html';
$G_MENU_SELECTED     = 2;
$G_SUB_MENU_SELECTED = 2;

if (!isset($_GET['UID']))
{
	$_GET['UID'] = '';
}
$appid = $_GET['UID'];
if ($appid == '' && $_SESSION['CURRENT_AUTH_SOURCE'] != '')
{
  $appid = $_SESSION['CURRENT_AUTH_SOURCE'];
}
$_SESSION['CURRENT_AUTH_SOURCE'] = $appid;
$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );
G::LoadClassRBAC('authentication');
$Fields['authId'] = $appid;

$G_PUBLISH = new Publisher;
$G_PUBLISH->SetTo ($dbc);
$G_PUBLISH->AddContent('xmlform', 'xmlform', 'rbac/authAddUser', '', $Fields);
G::RenderPage( 'publish');
?>