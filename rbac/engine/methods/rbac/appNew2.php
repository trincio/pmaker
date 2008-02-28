<?php
header( "location: appList.html" );die;
/*Falta revisar la clase RBAC_Application*/
$frm = $_POST['form'];

$code        = strtoupper ( $frm['APP_CODE']);
$description = $frm['APP_DESCRIPTION'];
$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );

//crear nueva aplicacion
G::LoadClassRBAC ('applications');
$obj = new RBAC_Application;
$obj->SetTo( $dbc );
$res = $obj->applicationCodeRepetido ( $code );

if ($res != 0 ) {
  G::SendMessage ( 15, "error");
  header ("location: appNew.php");
  die;
}

$appid = $obj->createApplication ($code, $description );
$_SESSION['CURRENT_APPLICATION'] = $appid;

header( "location: appList.html" );
?>