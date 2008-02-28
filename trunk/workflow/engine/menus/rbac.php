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

global $G_TMP_MENU;

$G_TMP_MENU->AddRawOption( "rbac/userList.html" );
$G_TMP_MENU->AddRawOption( "rbac/appList.html"  );
$G_TMP_MENU->AddRawOption( "rbac/authenticationList.html" );
$G_TMP_MENU->AddRawOption( "rbac/dbInfo.html"  );
$G_TMP_MENU->AddRawOption( "login/login.html"  );


switch( SYS_LANG )
{
case 'po':
  $G_TMP_MENU->Labels = array(
    "Usuarios",
    "Applicaciones",
    "Authentication Sources",
    "Info Database",
    "Saír do Sistema"
  );
  break;
case 'es':
  $G_TMP_MENU->Labels = array(
    "Usuarios",
    "Applicaciones",
    "Fuentes de Autenticación",
    "Información de BD",
    "Salir del Sistema"
  );
  break;
default:
  $G_TMP_MENU->Labels = array(
    "Users",
    "Applications",
    "Authentication Sources",
    "DB Info",
    "Logout"
  );
  break;
}

?>