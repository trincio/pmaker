<?php

  global $G_ENVIRONMENTS;
//var_dump($G_ENVIRONMENTS[G_ENVIRONMENT]);die;
  if ( isset ( $G_ENVIRONMENTS ) ) {
    $dbfile = $G_ENVIRONMENTS[ G_ENVIRONMENT ][ 'dbfile'];
    if ( !file_exists ( $dbfile ) ) {
      printf("%s \n", pakeColor::colorize( "dbfile $dbfile doesn't exist for environment " . G_ENVIRONMENT  , 'ERROR'));
      die();
    }
    require_once ( $dbfile );
  }
  else {
    //when this file is called from sysGeneric, the $G_ENVIRONMENTS DOES NOT EXIST, BUT DB_HOST is defined
    if ( !defined ( 'DB_HOST' ) ) {
      printf("%s \n", pakeColor::colorize( "dbfile $dbfile doesn't exist for environment " . G_ENVIRONMENT  , 'ERROR'));
      die();
    }
      
  }

    //to do: enable for other databases
  $dbType = 'mysql';

  $dsn     = 'mysql://' .  DB_USER . ':' . DB_PASS . '@' . DB_HOST . '/' . DB_NAME ;

  //to do: enable a mechanism to select RBAC Database
  $dsnRbac = 'mysql://' .  DB_RBAC_USER . ':' . DB_RBAC_PASS . '@' . DB_RBAC_HOST . '/' . DB_RBAC_NAME ;
  
  $pro ['datasources']['workflow']['connection'] = $dsn;
  $pro ['datasources']['workflow']['adapter'] = DB_ADAPTER;

  $pro ['datasources']['rbac']['connection'] = $dsnRbac;
  $pro ['datasources']['rbac']['adapter'] = DB_ADAPTER;
  //krumo ( $pro);

  return $pro;
?>
