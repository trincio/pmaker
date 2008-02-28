<?php

$G_MENU_SELECTED = 0;
$G_MAIN_MENU = "empty";
$G_SUB_MENU = "empty";

$referer =  $_SERVER['HTTP_REFERER'];
$dbc = new DBConnection; 
$G_PUBLISH = new Publisher;
$G_PUBLISH->SetTo( $dbc );
$G_PUBLISH->AddContent( "xmlform", "xmlform", "login/noViewPage", "", NULL );

G::RenderPage( "publish" );
?>