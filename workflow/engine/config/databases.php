<?php
/**
 * $Id$
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * You can contact Colosa Inc, 2655 Le Jeune Road, Suite 1112, Coral Gables,
 * FL 33134, USA or email info@colosa.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * ProcessMaker" logo and retain the original copyright notice. If the display
 * of the logo is not reasonably feasible for technical reasons, the
 * Appropriate Legal Notices must display the words "Powered by ProcessMaker"
 * and retain the original copyright notice.
 * -
 */

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
  $dbType = DB_ADAPTER;

  $dsn     = DB_ADAPTER . '://' .  DB_USER . ':' . DB_PASS . '@' . DB_HOST . '/' . DB_NAME;

  //to do: enable a mechanism to select RBAC Database
  $dsnRbac = DB_ADAPTER . '://' .  DB_RBAC_USER . ':' . DB_RBAC_PASS . '@' . DB_RBAC_HOST . '/' . DB_RBAC_NAME;

  switch (DB_ADAPTER) {
  	case 'mysql':
  	  $dsn     .= '?encoding=utf8';
  	  $dsnRbac .= '?encoding=utf8';
  	break;
  	case 'mssql':
  	  //$dsn     .= '?sendStringAsUnicode=false';
  	  //$dsnRbac .= '?sendStringAsUnicode=false';
  	break;
  	default:
  	break;
  }

  $pro ['datasources']['workflow']['connection'] = $dsn;
  $pro ['datasources']['workflow']['adapter'] = DB_ADAPTER;

  $pro ['datasources']['rbac']['connection'] = $dsnRbac;
  $pro ['datasources']['rbac']['adapter'] = DB_ADAPTER;

  $pro ['datasources']['dbarray']['connection'] = 'dbarray://user:pass@localhost/pm_os';
  $pro ['datasources']['dbarray']['adapter']    = 'dbarray';

  return $pro;
?>
