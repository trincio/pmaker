<?php

$roleid = isset($_GET[0])?$_GET[0]:'';//$URI_VARS[0];

$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );


//crear Objeto Roles
G::LoadClassRBAC ("roles");
$obj = new RBAC_Role;
$obj->SetTo ($dbc);
$obj->removeRole ( $roleid );

header( "location: roleList.html" );
?>
