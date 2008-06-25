<?php
/**
 * processes_Ajax.php
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
  /*global $RBAC;
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
  }*/

  $oJSON   = new Services_JSON();
  if ( !isset ($_POST['data']) ) {
    die;
  }

  $oData   = $oJSON->decode(stripslashes($_POST['data']));
  $sOutput = '';

  G::LoadClass('processMap');
  $oProcessMap = new processMap(new DBConnection);

  switch($_POST['action'])
  {
  	case 'load':
  	  if ($oData->mode) {
  	    $sOutput = $oProcessMap->load($oData->uid);
  	  }
  	  else {
  	  	$sOutput = $oProcessMap->load($oData->uid, true, $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['TASK']);
  	  }
  	break;
  	case 'process_Edit':
  	  $oProcessMap->editProcess($oData->pro_uid);
  	break;
  	case 'process_Export':
  	  include(PATH_METHODS . 'processes/processes_Export.php');
  	break;
  	case 'process_User':
  	  include(PATH_METHODS . 'processes/processes_User.php');
  	break;
  	case 'availableProcessesUser':  	  	  	  
  	  include(PATH_METHODS . 'processes/processes_availableProcessesUser.php');
  	break; 
  	case 'webEntry_generate':   	 	  	  	  
  	  include(PATH_METHODS . 'processes/processes_webEntryGenerate.php');
  	  //$oProcessMap->webEntryGenerate($oData->PRO_UID, $oData->TASKS, $oData->DYNAFORM);
  	break;  	
  	case 'assignProcessUser':  	  	  	    	  
  	  $oProcessMap->assignProcessUser($oData->PRO_UID, $oData->USR_UID);
  	break;  
  	case 'removeProcessUser':  	  	  	    	  
  	  $oProcessMap->removeProcessUser($oData->PU_UID);
  	break;  	
  	case 'supervisorDynaforms':
  	  $oProcessMap->supervisorDynaforms($oData->pro_uid);
  	break;
  	case 'webEntry':
  	  $oProcessMap->webEntry($oData->pro_uid);
  	break;
  	case 'saveTitlePosition':
  	  $sOutput = $oProcessMap->saveTitlePosition($oData->pro_uid, $oData->position->x, $oData->position->y);
  	break;
  	case 'steps':
  	  switch ($oData->option)
  	  {
  	  	case 1:
  	  	  $oProcessMap->steps($oData->proUid, $oData->tasUid);
  	  	break;
  	  	case 2:
  	  	  $oProcessMap->stepsConditions($oData->proUid, $oData->tasUid);
  	  	break;
  	  	case 3:
  	  	  $oProcessMap->stepsTriggers($oData->proUid, $oData->tasUid);
  	  	break;
  	  }
  	break;
  	case 'users':
  	  $oProcessMap->users($oData->pro_uid, $oData->tas_uid);
  	break;
  	
  	case 'users_adhoc':
  	  $oProcessMap->users_adhoc($oData->pro_uid, $oData->tas_uid);
  	break;
  	
  	case 'addTask':
  	  $sOutput = $oProcessMap->addTask($oData->uid, $oData->position->x, $oData->position->y);
  	break;
  	case 'editTaskProperties':
  	  $oProcessMap->editTaskProperties($oData->uid, (isset($oData->iForm) ? $oData->iForm : 1), $oData->index);
  	break;
  	case 'saveTaskPosition':
  	  $sOutput = $oProcessMap->saveTaskPosition($oData->uid, $oData->position->x, $oData->position->y);
  	break;
  	case 'deleteTask':
  	  $sOutput = $oProcessMap->deleteTask($oData->tas_uid);
  	break;
  	case 'addGuide':
  	  $sOutput = $oProcessMap->addGuide($oData->uid, $oData->position, $oData->direction);
  	break;
  	case 'saveGuidePosition':
  	  $sOutput = $oProcessMap->saveGuidePosition($oData->uid, $oData->position, $oData->direction);
  	break;
  	case 'deleteGuide':
  	  $sOutput = $oProcessMap->deleteGuide($oData->uid);
  	break;
  	case 'deleteGuides':
  	  $sOutput = $oProcessMap->deleteGuides($oData->pro_uid);
  	break;
  	case 'addText':
  	  $sOutput = $oProcessMap->addText($oData->uid, $oData->label, $oData->position->x, $oData->position->y);
  	break;
  	case 'updateText':
  	  $sOutput = $oProcessMap->updateText($oData->uid, $oData->label);
  	break;
  	case 'saveTextPosition':
  	  $sOutput = $oProcessMap->saveTextPosition($oData->uid, $oData->position->x, $oData->position->y);
  	break;
  	case 'deleteText':
  	  $sOutput = $oProcessMap->deleteText($oData->uid);
  	break;
  	case 'dynaforms':
  	  $oProcessMap->dynaformsList($oData->pro_uid);
  	break;
  	case 'inputs':
  	  $oProcessMap->inputdocsList($oData->pro_uid);
  	break;
  	case 'outputs':
  	  $oProcessMap->outputdocsList($oData->pro_uid);
  	break;
  	case 'triggers':
  	  $oProcessMap->triggersList($oData->pro_uid);
  	break;
  	case 'messages':
  	  $oProcessMap->messagesList($oData->pro_uid);
  	break;
  	case 'reportTables':
  	  $oProcessMap->reportTablesList($oData->pro_uid);
  	break;
  	case 'derivations':
  	  if (!isset($oData->type)) {
  	    $oProcessMap->currentPattern($oData->pro_uid, $oData->tas_uid);
  	  }
  	  else {
  	  	switch ($oData->type) {
  	    	case 0:
  	    	  $oData->type = 'SEQUENTIAL';
  	    	break;
  	    	case 1:
  	    	  $oData->type = 'SELECT';
  	    	break;
  	    	case 2:
  	    	  $oData->type = 'EVALUATE';
  	    	break;
  	    	case 3:
  	    	  $oData->type = 'PARALLEL';
  	    	break;
  	    	case 4:
  	    	  $oData->type = 'PARALLEL-BY-EVALUATION';
  	    	break;
  	    	case 5:
  	    	  $oData->type = 'SEC-JOIN';
  	    	break;
  	    }
  	    $oProcessMap->newPattern($oData->pro_uid, $oData->tas_uid, $oData->next_task, $oData->type);
  	  }
  	break;
  	case 'saveNewPattern':
  	  switch ($oData->type)
  	  {
  	  	case 0:
  	  	  $sType = 'SEQUENTIAL';
  	  	break;
  	  	case 1:
  	  	  $sType = 'SELECT';
  	  	break;
  	  	case 2:
  	  	  $sType = 'EVALUATE';
  	  	break;
  	  	case 3:
  	  	  $sType = 'PARALLEL';
  	  	break;
  	  	case 4:
  	  	  $sType = 'PARALLEL-BY-EVALUATION';
  	  	break;
  	  	case 5:
  	  	  $sType = 'SEC-JOIN';
  	  	break;
  	  }
  	  if (($oData->type != 0) && ($oData->type != 5)) {
  	    if ($oProcessMap->getNumberOfRoutes($oData->pro_uid, $oData->tas_uid, $oData->next_task, $sType) > 0) {
  	    	die;
  	    }
  	    unset($aRow);
  	  }
  	  if (($oData->delete) || ($oData->type == 0) || ($oData->type == 5)) {
  	  	G::LoadClass('tasks');
  	  	$oTasks = new Tasks();
  	    $oTasks->deleteAllRoutesOfTask($oData->pro_uid, $oData->tas_uid);
  	  }
  	  $oProcessMap->saveNewPattern($oData->pro_uid, $oData->tas_uid, $oData->next_task, $sType);
  	break;
  	case 'deleteAllRoutes':
  	  G::LoadClass('tasks');
	  	$oTasks = new Tasks();
	    $oTasks->deleteAllRoutesOfTask($oData->pro_uid, $oData->tas_uid);
  	break;
  }
  die($sOutput);
}
catch (Exception $oException) {
	die($oException->getMessage());
}
?>