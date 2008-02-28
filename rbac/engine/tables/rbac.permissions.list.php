<?php

global $G_TMP_TABLE;
global $G_TMP_TARGET;
global $HTTP_SESSION_VARS;

$lang = SYS_LANG;
$appid = $HTTP_SESSION_VARS['CURRENT_APPLICATION'];

$stQry = "SELECT * " .
         "FROM PERMISSION AS P WHERE PRM_APPLICATION = $appid";

$G_TMP_TABLE->SetSource( $stQry, "" );
$G_TMP_TABLE->WhereClause = "";

$G_TMP_TABLE->AddRawColumn( "link", "UID", "center", 60, $G_TMP_TARGET, "&UID" );
$G_TMP_TABLE->AddRawColumn( "link", "PRM_CODE", "left", 200, $G_TMP_TARGET, "&UID" );
$G_TMP_TABLE->AddRawColumn( "text", "PRM_DESCRIPTION", "left", 200 );

switch( $lang )
{
case 'po':
  $G_TMP_TABLE->Labels = array(
    "ID",
    "Código",
    "Descripcion"
  );
  break;
case 'es':
  $G_TMP_TABLE->Labels = array(
    "ID",
    "Código",
    "Descripción"
  );
  break;
default:
  $G_TMP_TABLE->Labels = array(
    "ID",
    "Code",
    "Description"
  );
  break;
}

?>