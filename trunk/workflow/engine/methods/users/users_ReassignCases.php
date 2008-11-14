<?php
/**
 * users_ReassignCases.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */
try {
  global $RBAC;
  switch ($RBAC->userCanAccess('PM_REASSIGNCASE')) {
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
  /*$sUsers = '<select name="USERS[]" id="USERS[]"><option value=""> - ' . G::LoadTranslation('ID_NO_REASSIGN') . ' - </option>';
  require_once 'classes/model/Users.php';
  $oCriteria = new Criteria('workflow');
  $oCriteria->addSelectColumn(UsersPeer::USR_UID);
  $oCriteria->addAsColumn('USR_COMPLETENAME', "CONCAT(USR_LASTNAME, ' ', USR_FIRSTNAME, ' (', USR_USERNAME, ')')");
  $oCriteria->add(UsersPeer::USR_UID, $_GET['sUser'], Criteria::NOT_EQUAL);
  $oDataset = UsersPeer::doSelectRS($oCriteria);
  $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
  $oDataset->next();
  while ($aRow = $oDataset->getRow()) {
    $sUsers .= '<option value="' . $aRow['USR_UID'] . '">' . $aRow['USR_COMPLETENAME'] . '</option>';
    $oDataset->next();
  }
  $sUsers .= '</select>';*/
  $oTemplatePower = new TemplatePower(PATH_TPL . 'users/users_ReassignCases.html');
  $oTemplatePower->prepare();
  G::LoadClass('tasks');
  G::LoadClass('groups');
  $oTasks  = new Tasks();
  $oGroups = new Groups();
  $oUser   = new Users();
  G::LoadClass('case');
  $oCases = new Cases();
  list($oCriteriaToDo,$sXMLFile)  = $oCases->getConditionCasesList('to_do', $_GET['sUser']);
  list($oCriteriaDraft,$sXMLFile) = $oCases->getConditionCasesList('draft', $_GET['sUser']);
  $oDataset = ApplicationPeer::doSelectRS($oCriteriaToDo);
  $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
  $oDataset->next();
  while ($aRow = $oDataset->getRow()) {
    $oTemplatePower->newBlock('cases');
    $aKeys = array_keys($aRow);
    foreach ($aKeys as $sKey) {
      $oTemplatePower->assign($sKey, $aRow[$sKey]);
    }
    $sUsers  = '<input type="hidden" name="APPLICATIONS[]" id="APPLICATIONS[]" value="' . $aRow['APP_UID'] . '" />';
    $sUsers .= '<input type="hidden" name="INDEXES[]" id="INDEXES[]" value="' . $aRow['DEL_INDEX'] . '" />';
    $sUsers .= '<select name="USERS[]" id="USERS[]"><option value=""> - ' . G::LoadTranslation('ID_NO_REASSIGN') . ' - </option>';
    $aUsers  = array($_GET['sUser']);
    $aAux1   = $oTasks->getGroupsOfTask($aRow['TAS_UID'], 1);
  	foreach ($aAux1 as $aGroup) {
  		$aAux2 = $oGroups->getUsersOfGroup($aGroup['GRP_UID']);
  	  foreach ($aAux2 as $aUser) {
        if (!in_array($aUser['USR_UID'], $aUsers)) {
          $aUsers[] = $aUser['USR_UID'];
          $aData    = $oUser->load($aUser['USR_UID']);
          $sUsers .= '<option value="' . $aUser['USR_UID'] . '">' . $aData['USR_FIRSTNAME'] . ' ' . $aData['USR_LASTNAME'] . ' (' . $aData['USR_USERNAME'] . ')</option>';
        }
  	  }
  	}
  	$aAux1  = $oTasks->getUsersOfTask($aRow['TAS_UID'], 1);
  	foreach ($aAux1 as $aUser) {
      if (!in_array($aUser['USR_UID'], $aUsers)) {
        $aUsers[] = $aUser['USR_UID'];
        $aData    = $oUser->load($aUser['USR_UID']);
        $sUsers .= '<option value="' . $aUser['USR_UID'] . '">' . $aData['USR_FIRSTNAME'] . ' ' . $aData['USR_LASTNAME'] . ' (' . $aData['USR_USERNAME'] . ')</option>';
      }
  	}
    $sUsers .= '</select>';
    $oTemplatePower->assign('USERS', str_replace('USERS[]', 'USERS[]', $sUsers));
    $oDataset->next();
  }
  $oDataset = ApplicationPeer::doSelectRS($oCriteriaDraft);
  $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
  $oDataset->next();
  while ($aRow = $oDataset->getRow()) {
    $oTemplatePower->newBlock('cases');
    $aKeys = array_keys($aRow);
    foreach ($aKeys as $sKey) {
      $oTemplatePower->assign($sKey, $aRow[$sKey]);
    }
    $oTemplatePower->assign('USERS', str_replace('USERS[]', 'USERS[]', $sUsers));
    $oDataset->next();
  }
  $oTemplatePower->gotoBlock('_ROOT');
  $oTemplatePower->assign('ID_NUMBER',      '#');
  $oTemplatePower->assign('ID_CASE',        G::LoadTranslation('ID_CASE'));
  $oTemplatePower->assign('ID_TASK',        G::LoadTranslation('ID_TASK'));
  $oTemplatePower->assign('ID_PROCESS',     G::LoadTranslation('ID_PROCESS'));
  $oTemplatePower->assign('ID_REASSIGN_TO', G::LoadTranslation('ID_REASSIGN_TO'));
  $oTemplatePower->assign('ID_REASSIGN',    G::LoadTranslation('ID_REASSIGN'));
  $oTemplatePower->assign('USR_UID',        $_GET['sUser']);

  $G_MAIN_MENU            = 'processmaker';
  $G_SUB_MENU             = 'users';
  $G_ID_MENU_SELECTED     = 'USERS';
  $G_ID_SUB_MENU_SELECTED = '-';
  $G_PUBLISH              = new Publisher;
  $G_PUBLISH->AddContent('template', '', '', '', $oTemplatePower);
  G::RenderPage('publish');
}
catch (Exception $oException) {
	die($oException->getMessage());
}
?>