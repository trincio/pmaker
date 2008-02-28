<?php

  $G_MAIN_MENU = "rbac";
  $G_SUB_MENU  = "rbac.userEdit";
  $G_MENU_SELECTED = 0;
  
  $uid = $HTTP_SESSION_VARS['CURRENT_USER'];
  
  $dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );
  $ses = new DBSession ( $dbc );
  
  $stQry = "SELECT UID, USR_USE_LDAP FROM USERS where UID = $uid ";
  $dset = $ses->Execute ( $stQry );
  $row  = $dset->Read();
  $useLdap = $row['USR_USE_LDAP'] == 'Y';
  
  $access = $RBAC->userCanAccess ("RBAC_CREATE_USERS");
  
  G::LoadClassRBAC ("user");
  $obj = new RBAC_User;
  $obj->SetTo ($dbc);
  $obj->Load ($uid);

  $G_PUBLISH = new Publisher;
  $G_PUBLISH->SetTo ($dbc);
  $G_PUBLISH->AddContent ( "xmlform", "xmlform", "rbac/userChangeLdap", "", $obj->Fields, "userChangeLdap2");
  G::RenderPage( "publish" );
?>