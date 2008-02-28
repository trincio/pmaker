<?php
$rolid = isset($_GET['ROL_UID']) ? $_GET['ROL_UID']:'';
if ($rolid != "")
  $_SESSION['CURRENT_ROLE'] = $rolid;

$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );

$ses = new DBSession;
$ses->SetTo ($dbc);

$sql = "select ROL_APPLICATION FROM USER_ROLE LEFT JOIN ROLE ON (ROL_UID = UID) WHERE ROL_UID = $rolid ";
$dset = $ses->Execute ($sql);
$row = $dset->Read();
if (is_array($row) )
  $_SESSION['CURRENT_APPLICATION'] = $row['ROL_APPLICATION'];
header ("location: roleProp.htm");
?>