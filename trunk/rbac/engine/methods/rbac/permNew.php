<?php

$G_MAIN_MENU = "rbac";
$G_BACK_PAGE = "rbac/permList";
$G_SUB_MENU  = "cancel";
$G_MENU_SELECTED = 1;

$permid = isset($_GET['UID'])?$_GET['UID']:'';//$URI_VARS[0];
$HTTP_SESSION_VARS['CURRENT_PERM_PARENT'] = $permid;

$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );

$G_PUBLISH = new Publisher;
$G_PUBLISH->SetTo ($dbc);

$fields['APPID'] = $_SESSION['CURRENT_APPLICATION'];
if ( PEAR_DATABASE == 'pgsql' ) {
  $CONCAT1 = "APP_CODE || \" - \" || APP_DESCRIPTION ";
  $CONCAT2 = "PRM_CODE || \" - \" || PRM_DESCRIPTION ";
}
else {
  $CONCAT1 = "CONCAT(APP_CODE,\" - \",APP_DESCRIPTION) ";
  $CONCAT2 = "CONCAT(PRM_CODE,\" - \",PRM_DESCRIPTION) ";
}
$fields['CONCAT1'] = $CONCAT1;
$fields['CONCAT2'] = $CONCAT2;
$fields['PERMID'] = $permid;

$G_PUBLISH->AddContent ( "xmlform", "xmlform", "rbac/permNew", "", $fields, "permNew2");
$content = '';//G::LoadContent( "rbac/myApp" );
G::RenderPage( "publish" );

?>