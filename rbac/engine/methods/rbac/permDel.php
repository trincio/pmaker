<?php

$permid = isset($_GET['UID'])?$_GET['UID']:'';//$URI_VARS[0];

$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );


//crear Objeto Permission
G::LoadClassRBAC ("permissions");
$obj = new RBAC_Permission;
$obj->SetTo ($dbc);
$obj->removePermission ( $permid );

header( "location: permList.html" );
?>
