<?php

$appid = $HTTP_SESSION_VARS['CURRENT_APPLICATION'];

$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );

//crear Objeto
G::LoadClassRBAC ("applications");
$obj = new RBAC_Application;
$obj->SetTo ($dbc);
$obj->removeApplication ( $appid );
header( "location: appList.html" );
?>
