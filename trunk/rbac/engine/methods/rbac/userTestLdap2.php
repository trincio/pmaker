<?php

$frm = $HTTP_POST_VARS['form'];
$frm = G::PrepareFormArray( $frm );

$password = $frm['PASS'];

$uid   = $HTTP_SESSION_VARS['CURRENT_USER'];
$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );
$ses = new DBSession ( $dbc );


G::LoadClassRBAC ('user');
$obj = new RBAC_User;
$obj->SetTo( $dbc );

//$obj->updateLDAP( $uid, $source, $dn, $use );

$G_MAIN_MENU = "rbac";
$G_SUB_MENU  = "rbac.userView";
$G_MENU_SELECTED = 0;

$uid = $HTTP_SESSION_VARS['CURRENT_USER'];

$access = $RBAC->userCanAccess ("RBAC_CREATE_USERS");

G::LoadClassRBAC ("user");
$obj = new RBAC_User;
$obj->SetTo ($dbc);
$obj->Load ($uid);

$G_PUBLISH = new Publisher;
$G_PUBLISH->SetTo ($dbc);
$G_PUBLISH->AddContent ( "xmlform", "xmlform", "rbac/userTestLdap", "", $obj->Fields, "userTest2");
$content = '';//G::LoadContent( "rbac/myApp" );
G::RenderPage( "publish" );


?>
