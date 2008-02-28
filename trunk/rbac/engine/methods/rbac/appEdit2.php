<?php

$frm = $HTTP_POST_VARS['form'];
$frm = G::PrepareFormArray( $frm );

$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );

$appid   = $HTTP_SESSION_VARS['CURRENT_APPLICATION'];
$code    = strtoupper( $frm['APP_CODE']);
$descrip = $frm['APP_DESCRIPTION'];

//crear nueva applicacion
G::LoadClassRBAC ( "applications");
$obj = new RBAC_Application;
$obj->SetTo( $dbc );

print "xx $res";
$res = $obj->applicationCodeRepetido ( $code );
if ($res != 0 && $res != $appid ) {
  G::SendMessage ( 15, "error");
  header ("location: appList.php");
  die;
}
print "xx $res";
$uid = $obj->editApplication( $appid, $code , $descrip );
header( "location: appList.html" );
?>
