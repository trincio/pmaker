<?php

$frm = $_POST['form'];

$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );


$parent  = $_SESSION['CURRENT_ROLE_PARENT'];
$appid   = $_SESSION['CURRENT_APPLICATION'];
$code    = strtoupper( $frm['ROL_CODE']);
$descrip = $frm['ROL_DESCRIPTION'];

//crear nuevo ROL
G::LoadClassRBAC ( "roles");
$obj = new RBAC_Role;
$obj->SetTo( $dbc );
$res = $obj->roleCodeRepetido( $code );
if ($res != 0 ) {
  G::SendMessage (14, "error");
  header ("location: roleList.php");
  die;
}

$uid = $obj->createRole( $parent, $appid, $code , $descrip );
header( "location: roleList.html" );
?>
