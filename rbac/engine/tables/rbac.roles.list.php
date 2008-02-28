<?php

global $G_TMP_TABLE;
global $G_TMP_TARGET;
global $HTTP_SESSION_VARS;

$lang = SYS_LANG;

$stQry = "SELECT * " .
         "FROM ROLE AS R left JOIN APPLICATION AS A ON (ROL_APPLICATION = A.UID ) ";

$G_TMP_TABLE->SetSource( $stQry, "" );
$G_TMP_TABLE->WhereClause = "";

$G_TMP_TABLE->AddRawColumn( "text", "UID", "center"   , 60 );
$G_TMP_TABLE->AddRawColumn( "text", "APP_CODE", "left", 150 );
$G_TMP_TABLE->AddRawColumn( "text", "ROL_CODE",        "left", 200  );
$G_TMP_TABLE->AddRawColumn( "text", "ROL_DESCRIPTION", "left", 200 );

switch( $lang )
{
case 'po':
  $G_TMP_TABLE->Labels = array(
    "ID",
    "Application",
    "Código",
    "Descripcion"
  );
  break;
case 'es':
  $G_TMP_TABLE->Labels = array(
    "ID",
    "Applicación",
    "Código",
    "Descripción"
  );
  break;
default:
  $G_TMP_TABLE->Labels = array(
    "ID",
    "Application",
    "Code",
    "Description"
  );
  break;
}

?>