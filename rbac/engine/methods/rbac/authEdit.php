<?php
$G_MAIN_MENU = 'rbac';
$G_SUB_MENU  = 'rbac.authSource';
$G_BACK_PAGE = 'rbac/authenticationList.html';
$G_MENU_SELECTED = 2;

$appid = isset($_GET[0])?$_GET[0]:'';
if ( $appid == '' && $HTTP_SESSION_VARS['CURRENT_AUTH_SOURCE'] != '' )
  $appid = $HTTP_SESSION_VARS['CURRENT_AUTH_SOURCE'];

$HTTP_SESSION_VARS['CURRENT_AUTH_SOURCE'] = $appid;
$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );

G::LoadClassRBAC('authentication');
$obj = new authenticationSource;
$obj->SetTo ($dbc);
$obj->Load($appid);

$G_PUBLISH = new Publisher;
$G_PUBLISH->SetTo ($dbc);
$G_PUBLISH->AddContent ( 'xmlform', 'xmlform', 'rbac/authNew', '', $obj->Fields, 'authEdit2');
G::RenderPage( 'publish');
?>