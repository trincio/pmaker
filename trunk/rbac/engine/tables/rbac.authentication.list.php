<?php

global $G_TMP_TABLE;
global $HTTP_SESSION_VARS;

$stQry = "SELECT *  " .
         "FROM AUTHENTICATION_SOURCES ORDER BY AUT_UID";

$G_TMP_TABLE->SetSource( $stQry, "" );
$G_TMP_TABLE->WhereClause = "";

$G_TMP_TABLE->AddColumn( G::LoadMessageXml ("ID_UID"),        "link", "AUT_UID",         "center",  35, 'loadAuthSource',  "&AUT_UID" );
$G_TMP_TABLE->AddColumn( G::LoadMessageXml ("ID_NAME"),       "link", "AUT_NAME",        "left",   200, 'loadAuthSource',  "&AUT_UID" );
$G_TMP_TABLE->AddColumn( G::LoadMessageXml ("ID_SERVER_NAME"),"text", "AUT_SERVER_NAME", "left",   100 );
$G_TMP_TABLE->AddColumn( G::LoadMessageXml ("ID_PROVIDER"),   "text", "AUT_PROVIDER",    "center",  90 );
$G_TMP_TABLE->AddRawColumn( "image", "/images/trash.gif","center",  90, "authDel", "&AUT_UID" );
//$G_TMP_TABLE->AddColumn( G::LoadMessageXml ("ID_UID"),        "link", "PRM_UID",         "center",  90 , "loadPermView", "&UID" );

?>