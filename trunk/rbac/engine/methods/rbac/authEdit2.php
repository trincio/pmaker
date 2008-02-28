<?php
$frm = $_POST['form'];

$code        = strtoupper ( $frm['APP_CODE']);
$description = $frm['APP_DESCRIPTION'];
$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );

G::LoadClassRBAC('authentication');
$obj = new authenticationSource;
$obj->SetTo($dbc);
$res = $obj->editSource($_SESSION['CURRENT_AUTH_SOURCE'], $frm);

if ($res <= 0)
{
  G::SendMessageXml('ID_USER_HAVENT_RIGHTS_PAGE', 'error');
  header('location: authEdit');
  die;
}

header('location: authTest.html');
//header('location: authenticationList.html');
?>