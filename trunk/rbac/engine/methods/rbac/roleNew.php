<?php

$G_MAIN_MENU = "rbac";
$G_BACK_PAGE = "rbac/roleList";
$G_SUB_MENU  = "cancel";
$G_MENU_SELECTED = 1;

if (!isset($_GET[0])) {
  $parent = "0";
  $postFile = "roleNew2";
}
else {
  $parent = isset($_GET[0])?$_GET[0]:'';//$URI_VARS[0];
  $postFile = "roleNew2";
}

$_SESSION ['CURRENT_ROLE_PARENT'] = $parent;
$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );

$G_PUBLISH = new Publisher;
$G_PUBLISH->SetTo ($dbc);

  if ( PEAR_DATABASE == 'pgsql' ) {
    $concat1 = 'APP_CODE || " - " || APP_DESCRIPTION';
    $concat2 = 'ROL_CODE || " - " || ROL_DESCRIPTION';
  }
  else {
    $concat1 = 'CONCAT(APP_CODE," - ",APP_DESCRIPTION)';
    $concat2 = 'CONCAT(ROL_CODE," - ",ROL_DESCRIPTION)';
  }

$fields['APPID'] = $_SESSION['CURRENT_APPLICATION'];
$fields['ROLID'] = $_SESSION['CURRENT_ROLE_PARENT'];
$fields['CONCAT1'] = $concat1;
$fields['CONCAT2'] = $concat2;
$G_PUBLISH->AddContent ( "xmlform", "xmlform", "rbac/roleNew", "", $fields, $postFile );
$content = '';//G::LoadContent( "rbac/myApp" );
G::RenderPage( "publish" );

?>