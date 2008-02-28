<?php
$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );
G::LoadClassRBAC ('authentication');
$obj = new authenticationSource;
$obj->SetTo($dbc);
$obj->removeSource($_GET['UID']);
header('location: authenticationList.html');
?>