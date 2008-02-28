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
function lookup($target)
{
  global $ntarget;
  $msg = $target . ' => ';
  if( eregi('[a-zA-Z]', $target) )
    $ntarget = gethostbyname($target);
  else
    $ntarget = gethostbyaddr($target);
  $msg .= $ntarget;
  return($msg);
}

  //$G_MAIN_MENU = 'wf.login';
  //$G_MENU_SELECTED = 1;

  if (file_exists(PATH_METHODS . 'login/version-pmos.php'))
  {
    include('version-pmos.php');
  }
  else {
    define('PM_VERSION', 'Development Version');
  }

  if (getenv('HTTP_CLIENT_IP')) {
    $ip = getenv('HTTP_CLIENT_IP');
  }
  elseif(getenv('HTTP_X_FORWARDED_FOR')) {
    $ip = getenv('HTTP_X_FORWARDED_FOR');
  } else {
    $ip = getenv('REMOTE_ADDR');
  }

    $redhat = '';
    if ( file_exists ( '/etc/redhat-release' ) ) {
      $fnewsize = filesize( '/etc/redhat-release'  );
      $fp = fopen( '/etc/redhat-release' , 'r' );
      $redhat = fread( $fp, $fnewsize );
      fclose( $fp );
    }
    
    $redhat .= " (" . PHP_OS . ")";

  $Fields['SYSTEM']          = $redhat;
  $Fields['DATABASE']        = DB_ADAPTER;
  $Fields['DATABASE_SERVER'] = DB_HOST;
  $Fields['DATABASE_NAME']   = DB_NAME;
  $Fields['PHP']             = phpversion();
  $Fields['FLUID']           = PM_VERSION;
  $Fields['IP']              = lookup ($ip);
  $Fields['ENVIRONMENT']     = SYS_SYS;
  $Fields['SERVER_SOFTWARE'] = getenv('SERVER_SOFTWARE');
  $Fields['SERVER_NAME']     = getenv('SERVER_NAME');
  $Fields['SERVER_PROTOCOL'] = getenv('SERVER_PROTOCOL');
  $Fields['SERVER_PORT']     = getenv('SERVER_PORT');
  $Fields['REMOTE_HOST']     = getenv('REMOTE_HOST');
  $Fields['SERVER_ADDR']     = getenv('SERVER_ADDR');
  $Fields['HTTP_USER_AGENT'] = getenv('HTTP_USER_AGENT');

  $G_PUBLISH = new Publisher;
  $G_PUBLISH->SetTo($dbc);
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/dbInfo', '', $Fields, 'appNew2');
  $G_HEADER->clearScripts();
  G::RenderPage('publish', 'raw');
?>
