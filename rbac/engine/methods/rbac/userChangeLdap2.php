<?php

$frm = $_POST['form'];
//$frm = G::PrepareFormArray( $frm );

$use    = $frm['USR_USE_LDAP'];
$source = $frm['USR_LDAP_SOURCE'];
$dn     = $frm['USR_LDAP_DN'];

$uid   = $HTTP_SESSION_VARS['CURRENT_USER'];
$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );


G::LoadClassRBAC ('user');
$obj = new RBAC_User;
$obj->SetTo( $dbc );

$obj->updateLDAP( $uid, $source, $dn, $use );

header( "location: userEdit.html" );
?>
