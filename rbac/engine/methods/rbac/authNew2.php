<?php

  $frm = $_POST['form'];
    
  $code        = strtoupper ( $frm['APP_CODE']);
  $description = $frm['APP_DESCRIPTION'];
  $dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );
  
  //crear nueva authentication source
  G::LoadClassRBAC ('authentication');
  $obj = new authenticationSource;
  $obj->SetTo( $dbc );
  $res = $obj->newSource ( $frm );
  
  if ($res <= 0 ) {
    //G::SendMessage ( -$res, "error");
    header ("location: authNew");
    die;
  }
  $HTTP_SESSION_VARS['CURRENT_AUTH_SOURCE'] = $res;
  
  header( "location: authenticationList.html" );

?>
