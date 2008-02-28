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
global $HTTP_SESSION_VARS;
$appid = $HTTP_SESSION_VARS['CURRENT_APPLICATION'];

$G_TMP_MENU->AddIdRawOption( "OP1", "rbac/appList.html" );
$G_TMP_MENU->AddIdRawOption( "OP2", "rbac/appDel.html" );

switch( SYS_LANG )
{
case 'es':
  $G_TMP_MENU->Labels = array(
    "Cancelar",
    "Eliminar Applicación"
  );
  break;
case 'po':
  $G_TMP_MENU->Labels = array(
    "Cancelar",
    "Eliminar Application"
  );
  break;
default:
  $G_TMP_MENU->Labels = array(
    "Cancel",
    "Remove Application"
  );
  break;
}

//si no hay nada relacionado a esta aplicación se puede BORRAR!!
$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );
//G::LoadClassRBAC ("applications");
$obj = New RBAC_Application;
$obj->SetTo ($dbc);
$sw = $obj->canRemoveApplication ($appid);
if ($sw > 0)
  $G_TMP_MENU->disableOptionId ("OP2");

?>