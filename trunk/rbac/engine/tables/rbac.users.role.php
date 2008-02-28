<?php

global $G_TMP_TABLE;
global $G_TMP_TARGET;
global $HTTP_SESSION_VARS;
global $access;

$lang = SYS_LANG;
$uid = $HTTP_SESSION_VARS['CURRENT_USER'];

$stQry = "SELECT  " .
        "USR_USERNAME FROM USERS WHERE UID = $uid";


$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );
$ses = new DBSession ( $dbc );
$dset = $ses->Execute ( $stQry );
$row  = $dset->Read( );

$stQry = "SELECT  " .
        "USR_USERNAME, APP_CODE, ROL_UID, USR_UID, " .
  	 "ROL_CODE, ROL_DESCRIPTION, ROL_APPLICATION " .
         " FROM USER_ROLE AS A LEFT JOIN USERS AS U ON (USR_UID = U.UID) " .
	 " LEFT JOIN ROLE AS R ON (ROL_UID = R.UID) " .
	 " LEFT JOIN APPLICATION AS P ON (ROL_APPLICATION = P.UID) " .
	 " WHERE USR_UID = $uid";

$G_TMP_TABLE->SetSource( $stQry, "" );
$G_TMP_TABLE->WhereClause = "";
$G_TMP_TABLE->title = "user: " . $row['USR_USERNAME'];

$G_TMP_TABLE->AddRawColumn( "text", "APP_CODE",       "left",  120 );
$G_TMP_TABLE->AddRawColumn( "link", "ROL_CODE",       "left",  120, "loadRoleProp", "&ROL_UID" );
$G_TMP_TABLE->AddRawColumn( "text", "ROL_DESCRIPTION","left",  180 );
if ($access == 1)
  $G_TMP_TABLE->AddRawColumn( "image", "/images/trash.gif","center",  90, "userRoleDel", "&ROL_UID" );

switch( $lang )
{
case 'po':
  $G_TMP_TABLE->Labels = array(
    "Application",
    "Rol Code",
    "Rol Descripcion",
    "Quitar Rol"
  );
  break;
case 'es':
  $G_TMP_TABLE->Labels = array(
    "Aplicación",
    "Código de Rol",
    "Descripcion de Rol",
    "Quitar Rol"
  );
  break;
default:
  $G_TMP_TABLE->Labels = array(
    "Application",
    "Role Code",
    "Role Description",
    "Remove Rol"
  );
  break;
}

?>