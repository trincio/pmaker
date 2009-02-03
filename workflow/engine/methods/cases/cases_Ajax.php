<?
/**
 * cases_Ajax.php
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
G::LoadClass('case');
$oCase = new Cases();
if ($RBAC->userCanAccess('PM_ALLCASES') < 0) {
  $oCase->thisIsTheCurrentUser($_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['USER_LOGGED'], 'SHOW_MESSAGE');
}

if (($RBAC_Response = $RBAC->userCanAccess("PM_CASES")) != 1)
    return $RBAC_Response;
if (isset($_POST['showWindow'])) {
    if ($_POST['showWindow'] == 'steps') {
        $fn = 'showSteps();';
    } elseif ($_POST['showWindow'] == 'information') {
        $fn = 'showInformation();';
    } elseif ($_POST['showWindow'] == 'actions') {
        $fn = 'showActions();';
    } elseif ($_POST['showWindow'] == 'false') {
        $fn = '';
    } else {
        if ($_POST['showWindow'] != '') {
            $fn = false;
        }
    }
    $_SESSION['showCasesWindow'] = $fn;
}
if (!isset($_POST['action'])) {
    $_POST['action'] = '';
}
switch ($_POST['action']) {
	case 'steps':
		global $G_PUBLISH;
		$G_PUBLISH = new Publisher();
		$G_PUBLISH->AddContent('view', 'cases/cases_StepsTree');
		G::RenderPage('publish', 'raw');
		break;
	case 'information':
		global $G_PUBLISH;
		$G_PUBLISH = new Publisher();
		$G_PUBLISH->AddContent('view', 'cases/cases_InformationTree');
		G::RenderPage('publish', 'raw');
		break;
	case 'actions':
		global $G_PUBLISH;
		$G_PUBLISH = new Publisher();
		$G_PUBLISH->AddContent('view', 'cases/cases_ActionsTree');
		G::RenderPage('publish', 'raw');
		break;
	case 'showProcessMap':
		$oTemplatePower = new TemplatePower(PATH_TPL . 'processes/processes_Map.html');
		$oTemplatePower->prepare();
		$G_PUBLISH = new Publisher;
		$G_PUBLISH->AddContent('template', '', '', '', $oTemplatePower);
$oHeadPublisher =& headPublisher::getSingleton();
$oHeadPublisher->addScriptCode('
		var pb=leimnud.dom.capture("tag.body 0");
		Pm=new processmap();
		Pm.options = {
			target    : "pm_target",
			dataServer: "../processes/processes_Ajax",
			uid       : "' . $_SESSION['PROCESS'] . '",
			lang      : "' . SYS_LANG . '",
			theme     : "processmaker",
			size      : {w:document.getElementById("panel_modal___processmaker").offsetWidth-30,h:document.getElementById("panel_modal___processmaker").offsetHeight},
			images_dir: "/jscore/processmap/core/images/",
			rw        : false,
			hideMenu  : false
		}
		Pm.make();');
		G::RenderPage('publish', 'raw');
		break;
	case 'showLeyends':
		$aFields = array();
		$aFields['sLabel1'] = G::LoadTranslation('ID_TASK_IN_PROGRESS');
		$aFields['sLabel2'] = G::LoadTranslation('ID_COMPLETED_TASK');
		$aFields['sLabel3'] = G::LoadTranslation('ID_PENDING_TASK');
		$aFields['sLabel4'] = G::LoadTranslation('ID_PARALLEL_TASK');
		$G_PUBLISH = new Publisher;
		$G_PUBLISH->AddContent('smarty', 'cases/cases_Leyends', '', '', $aFields);
		G::RenderPage('publish', 'raw');
		break;
	case 'showProcessInformation':
		require_once 'classes/model/Process.php';
		$oProcess = new Process();
		$aFields = $oProcess->load($_SESSION['PROCESS']);
		require_once 'classes/model/Users.php';
		$oUser = new Users();
		try {
		  $aUser = $oUser->load($aFields['PRO_CREATE_USER']);
		  $aFields['PRO_AUTHOR'] = $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'];
		}
		catch (Exception $oError) {
		  $aFields['PRO_AUTHOR'] = '(USER DELETED)';
		}
		$aFields['PRO_CREATE_DATE'] = date('F j, Y', strtotime($aFields['PRO_CREATE_DATE']));
		global $G_PUBLISH;
		$G_PUBLISH = new Publisher();
		$G_PUBLISH->AddContent('xmlform', 'xmlform', 'cases/cases_ProcessInformation', '', $aFields);
		G::RenderPage('publish', 'raw');
		break;
	case 'showTransferHistory':
		G::LoadClass("case");
		$c = Cases::getTransferHistoryCriteria($_SESSION['APPLICATION']);
		$G_PUBLISH = new Publisher();
		$G_PUBLISH->AddContent('propeltable', 'paged-table', 'cases/cases_TransferHistory', $c, array());
		G::RenderPage('publish', 'raw');
		break;
	case 'showTaskInformation':
		require_once 'classes/model/AppDelegation.php';
		require_once 'classes/model/Task.php';
		$oTask = new Task();
		$aFields = $oTask->load($_SESSION['TASK']);
		$oCriteria = new Criteria('workflow');
		$oCriteria->add(AppDelegationPeer::APP_UID, $_SESSION['APPLICATION']);
		$oCriteria->add(AppDelegationPeer::DEL_INDEX, $_SESSION['INDEX']);
		$oDataset = AppDelegationPeer::doSelectRS($oCriteria);
		$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
		$oDataset->next();
		$aDelegation = $oDataset->getRow();
		$iDiff = strtotime($aDelegation['DEL_FINISH_DATE']) - strtotime($aDelegation['DEL_INIT_DATE']);
		$aFields['INIT_DATE'] = ($aDelegation['DEL_INIT_DATE'] != null ? $aDelegation['DEL_INIT_DATE'] : G::LoadTranslation('ID_CASE_NOT_YET_STARTED'));
		$aFields['DUE_DATE'] = ($aDelegation['DEL_TASK_DUE_DATE'] != null ? $aDelegation['DEL_TASK_DUE_DATE'] : G::LoadTranslation('ID_NOT_FINISHED'));
		$aFields['FINISH'] = ($aDelegation['DEL_FINISH_DATE'] != null ? $aDelegation['DEL_FINISH_DATE'] : G::LoadTranslation('ID_NOT_FINISHED'));
		$aFields['DURATION'] = ($aDelegation['DEL_FINISH_DATE'] != null ? (int)($iDiff / 3600) . ' ' . ((int)($iDiff / 3600) == 1 ? G::LoadTranslation('ID_HOUR') : G::LoadTranslation('ID_HOURS')) . ' ' . (int)
			(($iDiff % 3600) / 60) . ' ' . ((int)(($iDiff % 3600) / 60) == 1 ? G::LoadTranslation('ID_MINUTE') : G::LoadTranslation('ID_MINUTES')) . ' ' . (int)(($iDiff % 3600) % 60) . ' ' . ((int)(($iDiff % 3600) %
			60) == 1 ? G::LoadTranslation('ID_SECOND') : G::LoadTranslation('ID_SECONDS')) : G::LoadTranslation('ID_NOT_FINISHED'));
		global $G_PUBLISH;
		$G_PUBLISH = new Publisher();
		$G_PUBLISH->AddContent('xmlform', 'xmlform', 'cases/cases_TaskInformation', '', $aFields);
		G::RenderPage('publish', 'raw');
		break;
	case 'showTaskDetails':
		require_once 'classes/model/AppDelegation.php';
		require_once 'classes/model/Task.php';
		require_once 'classes/model/Users.php';
		$oTask = new Task();
		$aRow = $oTask->load($_POST['sTaskUID']);
		$sTitle = $aRow['TAS_TITLE'];
		$oCriteria = new Criteria('workflow');
		$oCriteria->addSelectColumn(UsersPeer::USR_UID);
		$oCriteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
		$oCriteria->addSelectColumn(UsersPeer::USR_LASTNAME);
		$oCriteria->addSelectColumn(AppDelegationPeer::DEL_INIT_DATE);
		$oCriteria->addSelectColumn(AppDelegationPeer::DEL_TASK_DUE_DATE);
		$oCriteria->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE);
		$oCriteria->addJoin(AppDelegationPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);
		$oCriteria->add(AppDelegationPeer::APP_UID, $_SESSION['APPLICATION']);
		$oCriteria->add(AppDelegationPeer::TAS_UID, $_POST['sTaskUID']);
		$oCriteria->addDescendingOrderByColumn(AppDelegationPeer::DEL_INDEX);
		$oDataset = AppDelegationPeer::doSelectRS($oCriteria);
		$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
		$oDataset->next();
		$aRow = $oDataset->getRow();
		$iDiff = strtotime($aRow['DEL_FINISH_DATE']) - strtotime($aRow['DEL_INIT_DATE']);
		$aFields = array();
		$aFields['TASK'] = $sTitle;
		$aFields['USER'] = ($aRow['USR_UID'] != null ? $aRow['USR_FIRSTNAME'] . ' ' . $aRow['USR_LASTNAME'] : G::LoadTranslation('ID_NONE'));
		$aFields['INIT_DATE'] = ($aRow['DEL_INIT_DATE'] != null ? $aRow['DEL_INIT_DATE'] : G::LoadTranslation('ID_CASE_NOT_YET_STARTED'));
		$aFields['DUE_DATE'] = ($aRow['DEL_TASK_DUE_DATE'] != null ? $aRow['DEL_TASK_DUE_DATE'] : G::LoadTranslation('ID_CASE_NOT_YET_STARTED'));
		$aFields['FINISH'] = ($aRow['DEL_FINISH_DATE'] != null ? $aRow['DEL_FINISH_DATE'] : G::LoadTranslation('ID_NOT_FINISHED'));
		$aFields['DURATION'] = ($aRow['DEL_FINISH_DATE'] != null ? (int)($iDiff / 3600) . ' ' . ((int)($iDiff / 3600) == 1 ? G::LoadTranslation('ID_HOUR') : G::LoadTranslation('ID_HOURS')) . ' ' . (int)(($iDiff %
			3600) / 60) . ' ' . ((int)(($iDiff % 3600) / 60) == 1 ? G::LoadTranslation('ID_MINUTE') : G::LoadTranslation('ID_MINUTES')) . ' ' . (int)(($iDiff % 3600) % 60) . ' ' . ((int)(($iDiff % 3600) % 60) ==
			1 ? G::LoadTranslation('ID_SECOND') : G::LoadTranslation('ID_SECONDS')) : G::LoadTranslation('ID_NOT_FINISHED'));
		global $G_PUBLISH;
		$G_PUBLISH = new Publisher();
		$G_PUBLISH->AddContent('xmlform', 'xmlform', 'cases/cases_TaskDetails', '', $aFields);
		G::RenderPage('publish', 'raw');
		break;
	case 'showUsers':
		switch ($_POST['TAS_ASSIGN_TYPE']) {
			case 'BALANCED':
				G::LoadClass('user');
				$oUser = new User(new DBConnection());
				$oUser->load($_POST['USR_UID']);
				echo $oUser->Fields['USR_FIRSTNAME'] . ' ' . $oUser->Fields['USR_LASTNAME'] . '<input type="hidden" name="form[TASKS][1][USR_UID]" id="form[TASKS][1][USR_UID]" value="' . $_POST['USR_UID'] . '">';
				break;
			case 'MANUAL':
				$sAux = '<select name="form[TASKS][1][USR_UID]" id="form[TASKS][1][USR_UID]">';
				$oSession = new DBSession(new DBConnection());
				$oDataset = $oSession->Execute("SELECT
												TU.USR_UID AS USR_UID,
												CONCAT(U.USR_LASTNAME, ' ', U.USR_FIRSTNAME) AS USR_FULLNAME
											FROM
												TASK_USER AS TU
											LEFT JOIN
												USERS AS U
											ON (
												TU.USR_UID = U.USR_UID
											)
											WHERE
												TU.TAS_UID     = '" . $_POST['TAS_UID'] . "' AND
												TU.TU_TYPE     = 1 AND
												TU.TU_RELATION = 1 AND
												U.USR_STATUS   = 1");
				while ($aRow = $oDataset->Read()) {
					$sAux .= '<option value="' . $aRow['USR_UID'] . '">' . $aRow['USR_FULLNAME'] . '</option>';
				}
				$sAux .= '</select>';
				echo $sAux;
				break;
			case 'EVALUATE':
				G::LoadClass('application');
				$oApplication = new Application(new DBConnection());
				$oApplication->load($_SESSION['APPLICATION']);
				$sUser = '';
				if ($_POST['TAS_ASSIGN_VARIABLE'] != '') {
					if (isset($oApplication->Fields['APP_DATA'][str_replace('@@', '', $_POST['TAS_ASSIGN_VARIABLE'])])) {
						$sUser = $oApplication->Fields['APP_DATA'][str_replace('@@', '', $_POST['TAS_ASSIGN_VARIABLE'])];
					}
				}
				if ($sUser != '') {
					G::LoadClass('user');
					$oUser = new User(new DBConnection());
					$oUser->load($sUser);
					echo $oUser->Fields['USR_FIRSTNAME'] . ' ' . $oUser->Fields['USR_LASTNAME'] . '<input type="hidden" name="form[TASKS][1][USR_UID]" id="form[TASKS][1][USR_UID]" value="' . $sUser . '">';
				} else {
					echo '<strong>Error: </strong>' . $_POST['TAS_ASSIGN_VARIABLE'] . ' ' . G::LoadTranslation('ID_EMPTY');
					echo '<input type="hidden" name="_ERROR_" id="_ERROR_" value="">';
				}
				break;
			case 'SELFSERVICE':
				//Next release
				break;
		}
		break;

	case 'cancelCase':
		$sApplicationUID = (isset($_POST['sApplicationUID'])) ? $_POST['sApplicationUID']:
		$_SESSION['APPLICATION'];
		$iIndex = (isset($_POST['sApplicationUID'])) ? $_POST['iIndex']:
		$_SESSION['INDEX'];
		$oCase = new Cases();
		$oCase->cancelCase($sApplicationUID, $iIndex, $_SESSION['USER_LOGGED']);
		break;

	case 'reactivateCase':
		$sApplicationUID = isset($_POST['sApplicationUID']) ? $_POST['sApplicationUID']:
		$_SESSION['APPLICATION'];
		$iIndex = (isset($_POST['sApplicationUID'])) ? $_POST['iIndex']:
		$_SESSION['INDEX'];
		$oCase = new Cases();
		$oCase->reactivateCase($sApplicationUID, $iIndex, $_SESSION['USER_LOGGED']);
		break;
	case 'showPauseCaseInput':
		//echo '<input type=button onclick="close_pauseCase()" value="Cancel">';
		$aFields = Array();
		$G_PUBLISH = new Publisher;
		$G_PUBLISH->AddContent('xmlform', 'xmlform', 'cases/cases_UnpauseDateInput', '', $aFields);
        G::RenderPage('publish', 'raw');
		break;
	case 'pauseCase':
		$unpausedate = $_POST['unpausedate'];
		$oCase = new Cases();
		if (isset($_POST['sApplicationUID'])) {
			$oCase->pauseCase($_POST['sApplicationUID'], $_POST['iIndex'], $_SESSION['USER_LOGGED'], $unpausedate);
		} else {
			$oCase->pauseCase($_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['USER_LOGGED'], $unpausedate);
		}
		break;
	case 'unpauseCase':
		$sApplicationUID = (isset($_POST['sApplicationUID'])) ? $_POST['sApplicationUID']:
		$_SESSION['APPLICATION'];
		$iIndex = (isset($_POST['sApplicationUID'])) ? $_POST['iIndex']:
		$_SESSION['INDEX'];
		$oCase = new Cases();
		$oCase->unpauseCase($sApplicationUID, $iIndex, $_SESSION['USER_LOGGED']);
		break;
	case 'deleteCase':
		$oCase = new Cases();
		$sApplicationUID = (isset($_POST['sApplicationUID'])) ? $_POST['sApplicationUID']:
		$_SESSION['APPLICATION'];
		$oCase->removeCase($sApplicationUID);
		break;
	case 'view_reassignCase':
		G::LoadClass('groups');
		G::LoadClass('tasks');

		$oTasks = new Tasks();
		$aAux = $oTasks->getGroupsOfTask($_SESSION['TASK'], 1);
		$row = array();

		$groups = new Groups();
		foreach ($aAux as $aGroup) {
			$aUsers = $groups->getUsersOfGroup($aGroup['GRP_UID']);
			foreach ($aUsers as $aUser) {
				if ($aUser['USR_UID'] != $_SESSION['USER_LOGGED']) {
					$row[] = $aUser['USR_UID'];
				}
			}
		}

		$aAux = $oTasks->getUsersOfTask($_SESSION['TASK'], 1);
		foreach ($aAux as $aUser) {
			if ($aUser['USR_UID'] != $_SESSION['USER_LOGGED']) {
				$row[] = $aUser['USR_UID'];
			}
		}

		require_once 'classes/model/Users.php';
		$c = new Criteria('workflow');
		$c->addSelectColumn(UsersPeer::USR_UID);
		$c->addSelectColumn(UsersPeer::USR_FIRSTNAME);
		$c->addSelectColumn(UsersPeer::USR_LASTNAME);
		$c->add(UsersPeer::USR_UID, $row, Criteria::IN);

		global $G_PUBLISH;
		$G_PUBLISH = new Publisher();
		$G_PUBLISH->AddContent('propeltable', 'paged-table', 'processes/processes_viewreassignCase', $c);
		G::RenderPage('publish', 'raw');
		break;
	case 'reassignCase':
		$cases = new Cases();
		$cases->reassignCase($_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['USER_LOGGED'], $_POST['USR_UID'], $_POST['THETYPE']);
		break;
	case 'toRevisePanel':
			$_GET['APP_UID'] = $_POST['APP_UID'];
			$_GET['DEL_INDEX'] = $_POST['DEL_INDEX'];
		  $G_PUBLISH = new Publisher;
		  $G_PUBLISH->AddContent('view', 'cases/cases_toRevise');
		  $G_PUBLISH->AddContent('smarty', 'cases/cases_toReviseIn', '', '', array());
			G::RenderPage('publish', 'raw');
		break;
	case 'showUploadedDocuments':
		$oCase = new Cases();
		global $G_PUBLISH;
		$G_PUBLISH = new Publisher();
		$G_PUBLISH->AddContent('propeltable', 'paged-table', 'cases/cases_AllInputdocsList', $oCase->getAllUploadedDocumentsCriteria($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['TASK'], $_SESSION['USER_LOGGED']));
		G::RenderPage('publish', 'raw');
		break;
	case 'showUploadedDocument':
		require_once 'classes/model/AppDocument.php';
		require_once 'classes/model/AppDelegation.php';
		require_once 'classes/model/InputDocument.php';
		require_once 'classes/model/Users.php';
		$oAppDocument = new AppDocument();
		$oAppDocument->Fields = $oAppDocument->load($_POST['APP_DOC_UID']);
		$oInputDocument = new InputDocument();
		if ($oAppDocument->Fields['DOC_UID'] != -1) {
		  $Fields = $oInputDocument->load($oAppDocument->Fields['DOC_UID']);
		}
		else {
		  $Fields = array('INP_DOC_FORM_NEEDED' => '', 'FILENAME' => $oAppDocument->Fields['APP_DOC_FILENAME']);
		}
		$oCriteria = new Criteria('workflow');
    $oCriteria->add(AppDelegationPeer::DEL_INDEX, $oAppDocument->Fields['DEL_INDEX']);
    $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    $aRow = $oDataset->getRow();
    $oTask = new Task();
    try {
          $aTask = $oTask->load($aRow['TAS_UID']);
          $Fields['ORIGIN'] = $aTask['TAS_TITLE'];
          $oAppDocument->Fields['VIEW'] = G::LoadTranslation('ID_OPEN');
        }
    catch (Exception $oException) {
           $Fields['ORIGIN'] = '(TASK DELETED)';           
        }
    
    
		$oUser = new Users();
		$aUser = $oUser->load($oAppDocument->Fields['USR_UID']);
		$Fields['CREATOR'] = $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'];
		switch ($Fields['INP_DOC_FORM_NEEDED'])
		{
			case 'REAL':
			$sXmlForm = 'cases/cases_ViewAnyInputDocument2';
			break;
			case 'VIRTUAL':
			$sXmlForm = 'cases/cases_ViewAnyInputDocument1';
			break;
			case 'VREAL':
			$sXmlForm = 'cases/cases_ViewAnyInputDocument3';
			break;
			default:
			$sXmlForm = 'cases/cases_ViewAnyInputDocument';
			break;
		}
		//$oAppDocument->Fields['VIEW'] = G::LoadTranslation('ID_OPEN');
		$oAppDocument->Fields['FILE'] = 'cases_ShowDocument?a=' . $_POST['APP_DOC_UID'] . '&r=' . rand();
		$G_PUBLISH = new Publisher;
		$G_PUBLISH->AddContent('xmlform', 'xmlform', $sXmlForm, '', G::array_merges($Fields, $oAppDocument->Fields), '');
		G::RenderPage('publish', 'raw');
		break;
	case 'showGeneratedDocuments':
		$oCase = new Cases();
		global $G_PUBLISH;
		$G_PUBLISH = new Publisher();
		$G_PUBLISH->AddContent('propeltable', 'paged-table', 'cases/cases_AllOutputdocsList', $oCase->getAllGeneratedDocumentsCriteria($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['TASK'], $_SESSION['USER_LOGGED']));
		G::RenderPage('publish', 'raw');
		break;
	case 'showGeneratedDocument':
		require_once 'classes/model/AppDocument.php';
		require_once 'classes/model/AppDelegation.php';
		$oAppDocument = new AppDocument();
		$aFields = $oAppDocument->load($_POST['APP_DOC_UID']);
		require_once 'classes/model/OutputDocument.php';
		$oOutputDocument = new OutputDocument();
		$aOD = $oOutputDocument->load($aFields['DOC_UID']);
		$oCriteria = new Criteria('workflow');
    $oCriteria->add(AppDelegationPeer::DEL_INDEX, $aFields['DEL_INDEX']);
    $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    $aRow = $oDataset->getRow();
    $oTask = new Task();
    $aTask = $oTask->load($aRow['TAS_UID']);
    $aFields['ORIGIN'] = $aTask['TAS_TITLE'];
    require_once 'classes/model/Users.php';
		$oUser = new Users();
		$aUser = $oUser->load($aFields['USR_UID']);
		$aFields['CREATOR'] = $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'];
		$aFields['VIEW'] = G::LoadTranslation('ID_OPEN');
		$aFields['FILE1'] = 'cases_ShowOutputDocument?a=' . $aFields['APP_DOC_UID'] . '&ext=doc&random=' . rand();
		$aFields['FILE2'] = 'cases_ShowOutputDocument?a=' . $aFields['APP_DOC_UID'] . '&ext=pdf&random=' . rand();
		$G_PUBLISH = new Publisher();
		$G_PUBLISH->AddContent('xmlform', 'xmlform', 'cases/cases_ViewAnyOutputDocument', '', G::array_merges($aOD, $aFields), '');
		G::RenderPage('publish', 'raw');
		break;


	case 'showDynaformList':
		$oCase = new Cases();
		global $G_PUBLISH;
		$G_PUBLISH = new Publisher();
		$G_PUBLISH->AddContent('propeltable', 'paged-table', 'cases/cases_AllDynaformsList', $oCase->getallDynaformsCriteria($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['TASK'], $_SESSION['USER_LOGGED']));
		G::RenderPage('publish', 'raw');
		break;

	case 'showDynaform':
		$G_PUBLISH = new Publisher;
		$oCase = new Cases();
		$Fields = $oCase->loadCase( $_SESSION['APPLICATION'] );
		$Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = '';
		$Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP_LABEL'] = '';
		$Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP'] = '#';
		$Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_ACTION'] = 'return false;';
		$G_PUBLISH->AddContent('dynaform', 'xmlform', $_SESSION['PROCESS']. '/' . $_POST['DYN_UID'], '', $Fields['APP_DATA'],'','','view');
		G::RenderPage('publish', 'raw');
		break;

	case 'adhocAssignmentUsers':
		G::LoadClass('groups');
		G::LoadClass('tasks');
		$oTasks = new Tasks();
		$aAux = $oTasks->getGroupsOfTask($_SESSION['TASK'], 2);
		$aAdhocUsers = array();
		$oGroups = new Groups();
		foreach ($aAux as $aGroup) {
			$aUsers = $oGroups->getUsersOfGroup($aGroup['GRP_UID']);
			foreach ($aUsers as $aUser) {
			if ($aUser['USR_UID'] != $_SESSION['USER_LOGGED']) {
				$aAdhocUsers[] = $aUser['USR_UID'];
			}
			}
		}
		$aAux = $oTasks->getUsersOfTask($_SESSION['TASK'], 2);
		foreach ($aAux as $aUser) {
			if ($aUser['USR_UID'] != $_SESSION['USER_LOGGED']) {
			$aAdhocUsers[] = $aUser['USR_UID'];
			}
		}
		require_once 'classes/model/Users.php';
		$oCriteria = new Criteria('workflow');
		$oCriteria->addSelectColumn(UsersPeer::USR_UID);
		$oCriteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
		$oCriteria->addSelectColumn(UsersPeer::USR_LASTNAME);
		$oCriteria->add(UsersPeer::USR_UID, $aAdhocUsers, Criteria::IN);

		global $G_PUBLISH;
		$G_PUBLISH = new Publisher();
		$G_PUBLISH->AddContent('propeltable', 'paged-table', 'processes/processes_viewreassignCase', $oCriteria, array('THETYPE' => 'ADHOC'));
		G::RenderPage('publish', 'raw');
		break;

		case 'showHistoryMessages':
		$oCase = new Cases();
		global $G_PUBLISH;
		$G_PUBLISH = new Publisher();
		$G_PUBLISH->AddContent('propeltable', 'paged-table', 'cases/cases_Messages', $oCase->getHistoryMessagesTracker($_SESSION['APPLICATION']));
		G::RenderPage('publish', 'raw');
		break;

		case 'showHistoryMessage':
		$G_PUBLISH = new Publisher;
		$oCase = new Cases();

		$G_PUBLISH->AddContent('xmlform', 'xmlform', 'cases/cases_MessagesView', '', $oCase->getHistoryMessagesTrackerView($_POST['APP_UID'], $_POST['APP_MSG_UID']));
		G::RenderPage('publish', 'raw');
		break;

		case 'deleteUploadedDocument':
      require_once 'classes/model/AppDocument.php';
      $oAppDocument = new AppDocument();
      $oAppDocument->remove($_POST['DOC']);
      $oCase = new Cases();
      $oCase->getAllUploadedDocumentsCriteria($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['TASK'], $_SESSION['USER_LOGGED']);
		break;

		case 'deleteGeneratedDocument':
      require_once 'classes/model/AppDocument.php';
      $oAppDocument = new AppDocument();
      //$oAppDocument->remove($_POST['DOC']);
      $oCase = new Cases();
      $oCase->getAllGeneratedDocumentsCriteria($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['TASK'], $_SESSION['USER_LOGGED']);
		break;

    default: echo 'default';
}
