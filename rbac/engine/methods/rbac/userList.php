<?php

  G::GenericForceLogin ('RBAC_LOGIN','login/noViewPage','login/login');

  $userID = isset ( $_SESSION ['USER_LOGGED'] ) ? $_SESSION ['USER_LOGGED'] : '';
  $G_MAIN_MENU = "rbac";
  $G_SUB_MENU  = "rbac.user";
  $G_MENU_SELECTED = 0;
  
  $canCreateUsers = $RBAC->userCanAccess("RBAC_CREATE_USERS" );
  
  $dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->SetTo ($dbc);
  //$G_PUBLISH->AddContent ( "table", "paged-table", "rbac.users.list", "rbac/myApp", "", "load");
  $fields['CURRENT_USER'] = '';//"WHERE USR_UID = 1";//$HTTP_SESSION_VARS['CURRENT_USER'];
  $G_PUBLISH->AddContent ( "xmlform", "pagedTable", "rbac/usersList", "", $fields, "");
  $content = '';//'';//G::LoadContent( "rbac/myApp" );
  G::RenderPage( "publish" );

?>