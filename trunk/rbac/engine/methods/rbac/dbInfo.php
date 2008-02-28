<?php
  
  $G_MAIN_MENU = "rbac";
  $G_BACK_PAGE = "rbac/appList";
  $G_MENU_SELECTED = 3;
  
  if ( file_exists ( PATH_METHODS . 'login/version-rbac.php') ) {
    include ( PATH_METHODS . 'login/version-rbac.php' );
  }
  else {
    define ( 'RBAC_VERSION', "Development Version" );
  }
  
  function lookup($target){
    global $ntarget;
    $msg = "$target => ";
    if( eregi("[a-zA-Z]", $target) )
      $ntarget = gethostbyname($target);
    else
      $ntarget = gethostbyaddr($target);
    $msg .= $ntarget;
    return($msg);
  }
  $dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );
  $ses = new DBSession ($dbc);
  $dset = $ses->execute ("SELECT VERSION() AS VERSION ");
  $row  = $dset->Read();
  
  if (getenv('HTTP_CLIENT_IP')) {
    $ip = getenv('HTTP_CLIENT_IP');
  }
  elseif(getenv('HTTP_X_FORWARDED_FOR')) {
    $ip = getenv('HTTP_X_FORWARDED_FOR');
  } else {
    $ip = getenv('REMOTE_ADDR');
  }
  
    if ( file_exists ( "/etc/redhat-release" ) ) {
      $fnewsize = filesize( "/etc/redhat-release"  );
      $fp = fopen( "/etc/redhat-release" , "r" );
      $redhat = fread( $fp, $fnewsize );
      fclose( $fp );
    }
  
  $Fields = $dbc->db->dsn;
  $Fields['SYSTEM'] = $redhat;
  $Fields['MYSQL']  = $row['VERSION'];
  $Fields['PHP']    = phpversion();
  $Fields['FLUID']  = RBAC_VERSION;
  $Fields['IP']     = lookup ($ip);
  $Fields['ENVIRONMENT']     = SYS_SYS;
  $Fields['SERVER_SOFTWARE'] = getenv('SERVER_SOFTWARE');
  $Fields['SERVER_NAME']     = getenv('SERVER_NAME');
  $Fields['SERVER_PROTOCOL'] = getenv('SERVER_PROTOCOL');
  $Fields['SERVER_PORT']     = getenv('SERVER_PORT' );
  $Fields['REMOTE_HOST']     = getenv('REMOTE_HOST');
  $Fields['SERVER_ADDR']     = getenv('SERVER_ADDR');
  $Fields['HTTP_USER_AGENT'] = getenv('HTTP_USER_AGENT');
  
  
  $Fields['a'] = $dbc;
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->SetTo ($dbc);
  $G_PUBLISH->AddContent ( "xmlform", "xmlform", "rbac/dbInfo", "", $Fields, "appNew2");
  $content = '';//'';//G::LoadContent( "rbac/myApp" );
  G::RenderPage( "publish" );

?>