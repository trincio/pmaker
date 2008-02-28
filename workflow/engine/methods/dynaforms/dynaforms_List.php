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
if (($RBAC_Response=$RBAC->userCanAccess("PM_FACTORY"))!=1) return $RBAC_Response;
  //G::genericForceLogin( 'WF_MYINFO' , 'login/noViewPage', $urlLogin = 'login/login' );

  $G_MAIN_MENU            = 'processmaker';
  $G_SUB_MENU             = 'processes';
  $G_ID_MENU_SELECTED     = 'PROCESSES';
  $G_ID_SUB_MENU_SELECTED = 'DYNAFORMS';

  $dbc = new DBConnection();
  $ses = new DBSession($dbc);

  //Hardcode: UID of the library by default
  $PRO_UID='746B734DC23311';
  $Fields['PRO_UID'] = $PRO_UID;
  $Fields['SYS_LANG'] = SYS_LANG;
  $G_PUBLISH = new Publisher;
  
  $G_PUBLISH->AddContent('pagedtable', 'paged-table', 'dynaforms/dynaforms_List', '', $Fields , 'dynaforms_Save');
  
  G::RenderPage( "publish" );

?>