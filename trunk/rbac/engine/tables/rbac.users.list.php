<?php

global $G_TMP_TABLE;
global $G_TMP_TARGET;
global $HTTP_SESSION_VARS;

$lang = SYS_LANG;


$stQry = "SELECT UID, USR_USERNAME, USR_USE_LDAP, " .
           "CONCAT(USR_LASTNAME,' ',USR_MIDNAME,' ',USR_FIRSTNAME) AS USR_NAME, " .
  	       "USR_EMAIL, USR_CREATE_DATE, USR_DUE_DATE, USR_STATUS " .
           "FROM USERS AS U ORDER BY UID";


$G_TMP_TABLE->SetSource( $stQry, "" );
$G_TMP_TABLE->WhereClause = "";

$G_TMP_TABLE->AddRawColumn( "link", "UID",            "center", 45, "loadUser", "&UID" );
$G_TMP_TABLE->AddRawColumn( "link", "USR_USERNAME",   "left",  110, "loadUserRole", "&UID" );
$G_TMP_TABLE->AddRawColumn( "text", "USR_NAME"    ,   "left",  220 );
$G_TMP_TABLE->AddRawColumn( "text", "USR_EMAIL",      "left",   80 );
$G_TMP_TABLE->AddRawColumn( "text", "USR_STATUS",     "center", 80 );
//$G_TMP_TABLE->AddRawColumn( "text", "USR_CREATE_DATE","center",100 );
$G_TMP_TABLE->AddRawColumn( "text", "USR_USE_LDAP","center",60 );
$G_TMP_TABLE->AddRawColumn( "text", "USR_DUE_DATE","center",100 );
//  $G_TMP_TABLE->AddRawColumn("jslink", "DELETEBID",       "center", 50,"DeleteDraftBid","&JOBID",'','',false);

switch( $lang )
{
case 'es':
  $G_TMP_TABLE->Labels = array(
    "ID",
    "Nombre de Usuario",
    "Nombre completo",
    "Email",
    "Estado",
    'LDAP/AD', 
    "Fecha Venc."
  );
  break;
default:
  $G_TMP_TABLE->Labels = array(
    "ID",
    "User Name",
    "Full Name",
    "Email",
    "Status",
    'LDAP/AD', //"Creation Date",
    "Due Date"
  );
  break;
}

?>