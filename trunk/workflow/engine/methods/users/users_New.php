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
try {
  global $RBAC;
  switch ($RBAC->userCanAccess('PM_FACTORY'))
  {
  	case -2:
  	  G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels');
  	  G::header('location: ../login/login');
  	  die;
  	break;
  	case -1:
  	  G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
  	  G::header('location: ../login/login');
  	  die;
  	break;
  }
  $aFields['MESSAGE0']   = G::LoadTranslation('ID_USER_REGISTERED') . '!';
  $aFields['MESSAGE1']   = G::LoadTranslation('ID_MSG_ERROR_USR_USERNAME');
  $aFields['MESSAGE2']   = G::LoadTranslation('ID_MSG_ERROR_DUE_DATE');
  $aFields['MESSAGE3']   = G::LoadTranslation('ID_NEW_PASS_SAME_OLD_PASS');
  $aFields['START_DATE'] = date('Y-m-d');
  $aFields['END_DATE']   = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y') + 5));
  $G_MAIN_MENU           = 'processmaker';
  $G_ID_MENU_SELECTED    = 'USERS';
  $G_PUBLISH             = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'users/users_Edit.xml', '', $aFields, 'users_Save');
  G::RenderPage('publish');
}
catch (Exception $oException) {
	die($oException->getMessage());
}
?>