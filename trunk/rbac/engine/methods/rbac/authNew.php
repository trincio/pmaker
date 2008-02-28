<?php
  G::GenericForceLogin ('RBAC_LOGIN','login/noViewPage','login/login');

  $G_MAIN_MENU = "rbac";
  $G_BACK_PAGE = "rbac/authenticationList";
  $G_SUB_MENU  = "cancel";
  $G_MENU_SELECTED = 1;
  
  $Fields['AUT_PROVIDER'] = 'adprovider';
  $Fields['AUT_ENABLED_TLS'] = '0';
  $Fields['AUT_PORT'] = '389';
  $Fields['AUT_SEARCH_ATTRIBUTES'] = "cn\nmail\nmsAMAccountName";
  $Fields['AUT_OBJECT_CLASSES']    = "user\ninetOrgPerson\nposixAccount";
  
  
  $dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->SetTo ($dbc);
  $G_PUBLISH->AddContent ( "xmlform", "xmlform", "rbac/authNew", "", $Fields, "authNew2");
  G::RenderPage( "publish" );

?>