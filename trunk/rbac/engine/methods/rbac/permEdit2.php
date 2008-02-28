<?php

$frm = $_POST['form'];

$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );


$permid  = $_SESSION['CURRENT_PERM_PARENT'];
$appid   = $_SESSION['CURRENT_APPLICATION'];
$code    = strtoupper( $frm['PRM_CODE']);
$descrip = $frm['PRM_DESCRIPTION'];

//crear nuevo permiso
G::LoadClassRBAC ( "permissions");
$obj = new RBAC_Permission;
$obj->SetTo( $dbc );
$res = $obj->permissionCodeRepetido ( $code );
if ($res != 0 && $res != $permid) {
  G::SendMessage ( 16, "error");
  header ("location: permList.php");
  die;
}
$uid = $obj->editPermission( $permid, $appid, $code , $descrip );
header( "location: permList.html" );
?>