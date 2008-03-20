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
if (($RBAC_Response=$RBAC->userCanAccess("PM_CASES"))!=1) return $RBAC_Response;

switch($_POST['action'])
{
	case 'steps':
	  global $G_PUBLISH;
  	global $G_HEADER;
  	$G_PUBLISH = new Publisher();
  	$G_PUBLISH->AddContent('view', 'cases/cases_StepsTree');
    $G_HEADER->clearScripts();
    $G_HEADER->addScriptFile('/js/common/tree/tree.js');
    G::RenderPage('publish', 'raw');
	break;
	case 'information':
	  global $G_PUBLISH;
  	global $G_HEADER;
  	$G_PUBLISH = new Publisher();
  	$G_PUBLISH->AddContent('view', 'cases/cases_InformationTree');
    $G_HEADER->clearScripts();
    $G_HEADER->addScriptFile('/js/common/tree/tree.js');
    G::RenderPage('publish', 'raw');
	break;
	case 'actions':
	  global $G_PUBLISH;
  	global $G_HEADER;
  	$G_PUBLISH = new Publisher();
  	$G_PUBLISH->AddContent('view', 'cases/cases_ActionsTree');
    $G_HEADER->clearScripts();
    $G_HEADER->addScriptFile('/js/common/tree/tree.js');
    G::RenderPage('publish', 'raw');
	break;
	case 'KT':
	  global $G_PUBLISH;
  	global $G_HEADER;
  	$G_PUBLISH = new Publisher();
  	$G_PUBLISH->AddContent('view', 'cases/cases_KTTree');
    $G_HEADER->clearScripts();
    $G_HEADER->addScriptFile('/js/common/tree/tree.js');
    G::RenderPage('publish', 'raw');
	break;
	case 'showProcessMap':
    $oTemplatePower = new TemplatePower(PATH_TPL . 'processes/processes_Map.html');
    $oTemplatePower->prepare();
    $G_PUBLISH = new Publisher;
    $G_PUBLISH->AddContent('template', '', '', '', $oTemplatePower);
    $G_HEADER->clearScripts();
    $G_HEADER->addScriptFile('/jscore/labels/en.js');
    $G_HEADER->addScriptCode('
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
	  $aFields            = array();
	  $aFields['sLabel1'] = G::LoadTranslation('ID_TASK_IN_PROGRESS');
	  $aFields['sLabel2'] = G::LoadTranslation('ID_COMPLETED_TASK');
	  $aFields['sLabel3'] = G::LoadTranslation('ID_PENDING_TASK');
	  $aFields['sLabel4'] = G::LoadTranslation('ID_PARALLEL_TASK');
	  $G_PUBLISH = new Publisher;
	  $G_PUBLISH->AddContent('smarty', 'cases/cases_Leyends', '', '', $aFields);
	  $G_HEADER->clearScripts();
	  G::RenderPage('publish', 'raw');
	break;
	case 'showProcessInformation':
	  require_once 'classes/model/Process.php';
	  $oProcess = new Process();
	  $aFields  = $oProcess->load($_SESSION['PROCESS']);
	  require_once 'classes/model/Users.php';
	  $oUser                      = new Users();
	  $aUser                      = $oUser->load($aFields['PRO_CREATE_USER']);
	  $aFields['PRO_AUTHOR']      = $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'];
	  $aFields['PRO_CREATE_DATE'] = date('F j, Y', strtotime($aFields['PRO_CREATE_DATE']));
	  global $G_PUBLISH;
  	global $G_HEADER;
  	$G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'cases/cases_ProcessInformation', '', $aFields);
    $G_HEADER->clearScripts();
    G::RenderPage('publish', 'raw');
	break;
	case 'showTransferHistory':
      G::LoadClass("case");
      $c = Cases::getTransferHistoryCriteria($_SESSION['APPLICATION']);
  	  $G_PUBLISH = new Publisher();
      $G_HEADER->clearScripts();
  	  $G_PUBLISH->AddContent('propeltable', 'paged-table', 'cases/cases_TransferHistory', $c, array());
      G::RenderPage('publish', 'raw');
	break;
	case 'showTaskInformation':
	  require_once 'classes/model/AppDelegation.php';
	  require_once 'classes/model/Task.php';
	  $oTask   = new Task();
	  $aFields = $oTask->load($_SESSION['TASK']);
	  $oCriteria = new Criteria('workflow');
	  $oCriteria->add(AppDelegationPeer::APP_UID,   $_SESSION['APPLICATION']);
	  $oCriteria->add(AppDelegationPeer::DEL_INDEX, $_SESSION['INDEX']);
	  $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    $aDelegation          = $oDataset->getRow();
    $iDiff                = strtotime($aDelegation['DEL_FINISH_DATE']) - strtotime($aDelegation['DEL_INIT_DATE']);
	  $aFields['INIT_DATE'] = ($aDelegation['DEL_INIT_DATE'] != null ? $aDelegation['DEL_INIT_DATE'] : G::LoadTranslation('ID_CASE_NOT_YET_STARTED'));
	  $aFields['DUE_DATE']  = ($aDelegation['DEL_TASK_DUE_DATE'] != null ? $aDelegation['DEL_TASK_DUE_DATE'] : G::LoadTranslation('ID_NOT_FINISHED'));
	  $aFields['FINISH']    = ($aDelegation['DEL_FINISH_DATE'] != null ? $aDelegation['DEL_FINISH_DATE'] : G::LoadTranslation('ID_NOT_FINISHED'));
    $aFields['DURATION']  = ($aDelegation['DEL_FINISH_DATE'] != null ? (int)($iDiff / 3600) . ' ' . ((int)($iDiff / 3600) == 1 ? G::LoadTranslation('ID_HOUR') : G::LoadTranslation('ID_HOURS')) . ' ' . (int)(($iDiff % 3600) / 60) . ' ' . ((int)(($iDiff % 3600) / 60) == 1 ? G::LoadTranslation('ID_MINUTE') : G::LoadTranslation('ID_MINUTES')) . ' '. (int)(($iDiff % 3600) % 60) . ' ' . ((int)(($iDiff % 3600) % 60) == 1 ? G::LoadTranslation('ID_SECOND') : G::LoadTranslation('ID_SECONDS')) : G::LoadTranslation('ID_NOT_FINISHED'));
	  global $G_PUBLISH;
  	global $G_HEADER;
  	$G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'cases/cases_TaskInformation', '', $aFields);
    $G_HEADER->clearScripts();
    G::RenderPage('publish', 'raw');
	break;
	case 'showTaskDetails':
	  require_once 'classes/model/AppDelegation.php';
	  require_once 'classes/model/Task.php';
	  require_once 'classes/model/Users.php';
	  $oTask     = new Task();
	  $aRow      = $oTask->load($_POST['sTaskUID']);
	  $sTitle    = $aRow['TAS_TITLE'];
	  $oCriteria = new Criteria();
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
    $aRow                 = $oDataset->getRow();
    $iDiff                = strtotime($aRow['DEL_FINISH_DATE']) - strtotime($aRow['DEL_INIT_DATE']);
    $aFields              = array();
    $aFields['TASK']      = $sTitle;
    $aFields['USER']      = ($aRow['USR_UID'] != null ? $aRow['USR_FIRSTNAME'] . ' ' . $aRow['USR_LASTNAME'] : G::LoadTranslation('ID_NONE'));
    $aFields['INIT_DATE'] = ($aRow['DEL_INIT_DATE'] != null ? $aRow['DEL_INIT_DATE'] : G::LoadTranslation('ID_CASE_NOT_YET_STARTED'));
    $aFields['DUE_DATE']  = ($aRow['DEL_TASK_DUE_DATE'] != null ? $aRow['DEL_TASK_DUE_DATE'] : G::LoadTranslation('ID_CASE_NOT_YET_STARTED'));
    $aFields['FINISH']    = ($aRow['DEL_FINISH_DATE'] != null ? $aRow['DEL_FINISH_DATE'] : G::LoadTranslation('ID_NOT_FINISHED'));
    $aFields['DURATION']  = ($aRow['DEL_FINISH_DATE'] != null ? (int)($iDiff / 3600) . ' ' . ((int)($iDiff / 3600) == 1 ? G::LoadTranslation('ID_HOUR') : G::LoadTranslation('ID_HOURS')) . ' '  . (int)(($iDiff % 3600) / 60) . ' ' . ((int)(($iDiff % 3600) / 60) == 1 ? G::LoadTranslation('ID_MINUTE') : G::LoadTranslation('ID_MINUTES')) . ' ' . (int)(($iDiff % 3600) % 60) . ' ' . ((int)(($iDiff % 3600) % 60) == 1 ? G::LoadTranslation('ID_SECOND') : G::LoadTranslation('ID_SECONDS')) : G::LoadTranslation('ID_NOT_FINISHED'));
    global $G_PUBLISH;
  	global $G_HEADER;
  	$G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'cases/cases_TaskDetails', '', $aFields);
    $G_HEADER->clearScripts();
    G::RenderPage('publish', 'raw');
	break;
	case 'showUsers':
	  switch ($_POST['TAS_ASSIGN_TYPE'])
    {
    	case 'BALANCED':
    	  G::LoadClass('user');
    	  $oUser = new User(new DBConnection());
    	  $oUser->load($_POST['USR_UID']);
    	  echo $oUser->Fields['USR_FIRSTNAME'] . ' ' . $oUser->Fields['USR_LASTNAME'] . '<input type="hidden" name="form[TASKS][1][USR_UID]" id="form[TASKS][1][USR_UID]" value="' . $_POST['USR_UID'] . '">';
    	break;
    	case 'MANUAL':
    	  $sAux  = '<select name="form[TASKS][1][USR_UID]" id="form[TASKS][1][USR_UID]">';
    	  $oSession  = new DBSession(new DBConnection());
    	  $oDataset  = $oSession->Execute("SELECT
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
    	  while ($aRow = $oDataset->Read())
    	  {
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
	      if ($_POST['TAS_ASSIGN_VARIABLE'] != '')
	      {
	      	if (isset($oApplication->Fields['APP_DATA'][str_replace('@@', '', $_POST['TAS_ASSIGN_VARIABLE'])]))
	      	{
	          $sUser = $oApplication->Fields['APP_DATA'][str_replace('@@', '', $_POST['TAS_ASSIGN_VARIABLE'])];
	        }
	      }
	      if ($sUser != '')
	      {
	      	G::LoadClass('user');
	      	$oUser = new User(new DBConnection());
	      	$oUser->load($sUser);
	      	echo $oUser->Fields['USR_FIRSTNAME'] . ' ' . $oUser->Fields['USR_LASTNAME'] . '<input type="hidden" name="form[TASKS][1][USR_UID]" id="form[TASKS][1][USR_UID]" value="' . $sUser . '">';
	      }
	      else
	      {
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
	  require_once 'classes/model/Application.php';
	  $oApplication          = new Application();
	  $aFields               = $oApplication->load((isset($_POST['sApplicationUID']) ? $_POST['sApplicationUID'] : $_SESSION['APPLICATION']));
	  $aFields['APP_STATUS'] = 'CANCELLED';
	  $oApplication->update($aFields);
	  G::LoadClass('case');
	  $oCase = new Cases();
	  if (isset($_POST['sApplicationUID'])) {
	  	$oCase->CloseCurrentDelegation($_POST['sApplicationUID'], $_POST['iIndex']);
	  }
	  else {
	    $oCase->CloseCurrentDelegation($_SESSION['APPLICATION'], $_SESSION['INDEX']);
	  }
	break;
}
?>