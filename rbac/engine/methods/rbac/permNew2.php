<?php

$frm = $_POST['form'];

$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );


$parent  = $_SESSION['CURRENT_PERM_PARENT'];
$appid   = $_SESSION['CURRENT_APPLICATION'];
$code    = strtoupper( $frm['PRM_CODE']);
$descrip = $frm['PRM_DESCRIPTION'];

//crear nuevo permiso
G::LoadClassRBAC ( "permissions");
$obj = new RBAC_Permission;
$obj->SetTo( $dbc );
$res = $obj->permissionCodeRepetido ( $code );
if ($res != 0 ) {
  G::SendMessage ( 16, "error");
  header ("location: permList.php");
  die;
}
$uid = $obj->createPermission( $parent, $appid, $code , $descrip );
header( "location: permList.html" );
?>
