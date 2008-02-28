<?php
$_SESSION['CURRENT_ROLE'] = $_GET['ROL_UID'];

$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME);
$ses = new DBSession;
$ses->SetTo ($dbc);
$dset = $ses->Execute('SELECT ROL_APPLICATION FROM USER_ROLE LEFT JOIN ROLE ON (ROL_UID = UID) WHERE ROL_UID = ' . $_SESSION['CURRENT_ROLE']);
$row  = $dset->Read();
$_SESSION['CURRENT_APPLICATION'] = $row['ROL_APPLICATION'];

header('location: userRoleProp.htm');
?>