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
  if ($_GET['USR_UID'] == '00000000000000000000000000000001') {
  	G::SendTemporalMessage('ID_CANNOT_CHANGE_STATUS_ADMIN_USER', 'error', 'usersLabels');
  	G::header('location: ' . $_SERVER['HTTP_REFERER']);
  	die;
  }
  /*$RBAC->removeUser($_GET['USR_UID']);
  require_once 'classes/model/Users.php';
  $oUser = new Users();
  $oUser->remove($_GET['USR_UID']);*/
  G::LoadClass('tasks');
  $oTasks = new Tasks();
  $oTasks->ofToAssignUserOfAllTasks($_GET['USR_UID']);
  G::LoadClass('groups');
  $oGroups = new Groups();
  $oGroups->ofToAssignUserOfAllGroups($_GET['USR_UID']);
  $RBAC->changeUserStatus($_GET['USR_UID'], 'CLOSED');
  require_once 'classes/model/Users.php';
  $oUser                 = new Users();
  $aFields               = $oUser->load($_GET['USR_UID']);
  $aFields['USR_STATUS'] = 'CLOSED';
  $oUser->update($aFields);
  G::header('location: ' . $_SERVER['HTTP_REFERER']);
}
catch (Exception $oException) {
	die($oException->getMessage());
}
?>