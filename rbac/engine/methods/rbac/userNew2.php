<?php
$frm   = $_POST['form'];
if (!isset($frm['USR_FIRSTNAME']))
{
	$frm['USR_FIRSTNAME'] = '';
}
if (!isset($frm['USR_MIDNAME']))
{
	$frm['USR_MIDNAME'] = '';
}
if (!isset($frm['USR_NAMES']))
{
	$frm['USR_NAMES'] = '';
}
if (!isset($frm['USR_EMAIL']))
{
	$frm['USR_EMAIL'] = '';
}
if (!isset($frm['USR_PHONE']))
{
	$frm['USR_PHONE'] = '';
}
if (!isset($frm['USR_CELLULAR']))
{
	$frm['USR_CELLULAR'] = '';
}
if (!isset($frm['USR_FAX']))
{
	$frm['USR_FAX'] = '';
}
if (!isset($frm['USR_POBOX']))
{
	$frm['USR_POBOX'] = '';
}
$first = strtoupper ($frm['USR_FIRSTNAME']);
$mid   = strtoupper ($frm['USR_MIDNAME']);
$names = strtoupper ($frm['USR_NAMES']);
$email = $frm['USR_EMAIL'];
$phone = $frm['USR_PHONE'];
$cell  = $frm['USR_CELLULAR'];
$fax   = $frm['USR_FAX'];
$pobox = $frm['USR_POBOX'];
$dbc   = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME);

G::LoadClassRBAC ('user');
$obj = new RBAC_User;
$obj->SetTo($dbc);
$uid = $obj->createUser ($first, $mid, $names, $email, $phone, $cell, $fax, $pobox);
$_SESSION['CURRENT_USER'] = $uid;

header('location: userNew3.html');
?>