<?php

  $roleid = isset($_GET['r'])?$_GET['r']: '';
  $uid    = $HTTP_SESSION_VARS['CURRENT_USER'];
  
  $dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );
  
  //crear Objeto Roles
  G::LoadClassRBAC ("user");
  $obj = new RBAC_User;
  $obj->SetTo ($dbc);
  $obj->removeUserRole( $uid, $roleid );
  
  header( "location: userEdit.html" );
?>