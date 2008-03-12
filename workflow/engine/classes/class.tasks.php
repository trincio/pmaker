<?php
/**
 * class.tasks.php
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
require_once 'classes/model/GroupUser.php';
require_once 'classes/model/Groupwf.php';
require_once 'classes/model/Process.php';
require_once 'classes/model/Route.php';
require_once 'classes/model/Step.php';
require_once 'classes/model/StepTrigger.php';
require_once 'classes/model/Task.php';
require_once 'classes/model/TaskUser.php';
require_once 'classes/model/Users.php';
/**
 * Tasks - Tasks class
 * @package ProcessMaker
 * @author Julio Cesar Laura Avendaño
 * @copyright 2007 COLOSA
 */

class Tasks {

  /*
	* Get the assigned groups of a task
	* @param string $sTaskUID
	* @param integer $iType
	* @return array
	*/
  public function getGroupsOfTask($sTaskUID, $iType) {
  	try {
  	  $aGroups   = array();
  	  $oCriteria = new Criteria('workflow');
  	  $oCriteria->addJoin(GroupwfPeer::GRP_UID, TaskUserPeer::USR_UID, Criteria::LEFT_JOIN);
      $oCriteria->add(TaskUserPeer::TAS_UID,     $sTaskUID);
      $oCriteria->add(TaskUserPeer::TU_TYPE,     $iType);
      $oCriteria->add(TaskUserPeer::TU_RELATION, 2);
      $oCriteria->add(GroupwfPeer::GRP_STATUS,   'ACTIVE');
      $oDataset = GroupwfPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
      	$aGroups[] = $aRow;
      	$oDataset->next();
      }
      return $aGroups;
    }
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

/*
	* Get all tasks for any Process
	* @param string $sProUid
	* @return array
	*/
  public function getAllTasks($sProUid) {
  	try {
  	  $aTasks   = array();
  	  $oCriteria = new Criteria('workflow');
      $oCriteria->add(TaskPeer::PRO_UID,     $sProUid);
      $oDataset = TaskPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
      	$oTask = new Task();
      	$aTasks[] = $oTask->Load($aRow['TAS_UID']);
      	$oDataset->next();
      }
      return $aTasks;
    }
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

/*
	* creates row tasks from an Task Array
	* @param string $aTasks
	* @return array
	*/
  public function createTaskRows( $aTask ) {
  	foreach ( $aTask as $key => $row ) {
      $oTask = new Task();
      $res = $oTask->create($row);
  	}
  	return;
  }

/*
	* updates row tasks from an Task Array
	* @param string $aTasks
	* @return array
	*/
  public function updateTaskRows( $aTask ) {
  	foreach ( $aTask as $key => $row ) {
      $oTask = new Task();
      $res = $oTask->update($row);
  	}
  	return;
  }

/*
	* Get all Routes for any Process
	* @param string $sProUid
	* @return array
	*/
  public function getAllRoutes($sProUid) {
  	try {
  	  $aRoutes   = array();
  	  $oCriteria = new Criteria('workflow');
      $oCriteria->add(RoutePeer::PRO_UID,     $sProUid);
      $oDataset = RoutePeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
      	$aRoutes[] = $aRow;
      	$oDataset->next();
      }
      return $aRoutes;
    }
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

  /*
	* creates row tasks from an Route Array
	* @param string $aTasks
	* @return array
	*/
  public function createRouteRows( $aRoutes ) {
  	foreach ( $aRoutes as $key => $row ) {
      $oRoute = new Route();
      unset ($row['ROU_UID']);
      $res = $oRoute->create($row);
  	}
  	return;
  }

  /*
	* updates row tasks from an Route Array
	* @param string $aTasks
	* @return array
	*/
  public function updateRouteRows( $aRoutes ) {
  	foreach ( $aRoutes as $key => $row ) {
      $oRoute = new Route();
      krumo ($row);
      $res = $oRoute->update($row);
  	}
  	return;
  }

  /*
	* Get the assigned users of a task
	* @param string $sTaskUID
	* @param integer $iType
	* @return array
	*/
  public function getUsersOfTask($sTaskUID, $iType) {
  	try {
  	  $aUsers    = array();
  	  $oCriteria = new Criteria('workflow');
  	  $oCriteria->addJoin(UsersPeer::USR_UID, TaskUserPeer::USR_UID, Criteria::LEFT_JOIN);
      $oCriteria->add(TaskUserPeer::TAS_UID,     $sTaskUID);
      $oCriteria->add(TaskUserPeer::TU_TYPE,     $iType);
      $oCriteria->add(TaskUserPeer::TU_RELATION, 1);
      $oDataset = UsersPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
      	$aUsers[] = $aRow;
      	$oDataset->next();
      }
      return $aUsers;
    }
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

  /*
	* Delete a task
	* @param string $sTaskUID
	* @return void
	*/
	function deleteTask($sTaskUID = '') {
		try {
			//Instance classes
			$oTask        = new Task();
			$oTasks       = new Tasks();
			$oTaskUser    = new TaskUser();
			$oStep        = new Step();
			$oStepTrigger = new StepTrigger();
			//Get task information
			$aFields = $oTask->load($sTaskUID);
			//Delete routes
			$oTasks->deleteAllRoutesOfTask($aFields['PRO_UID'], $sTaskUID, true);
			//Delete the users assigned to task
      $oCriteria = new Criteria('workflow');
  	  $oCriteria->add(TaskUserPeer::TAS_UID, $sTaskUID);
  	  $oDataset1 = TaskUserPeer::doSelectRS($oCriteria);
      $oDataset1->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset1->next();
      while ($aRow1 = $oDataset1->getRow()) {
      	$oTaskUser->remove($aRow1['TAS_UID'], $aRow1['USR_UID'], $aRow1['TU_TYPE'], $aRow1['TU_RELATION']);
      	$oDataset1->next();
      }
      //Delete the steps of task
      $oCriteria = new Criteria('workflow');
  	  $oCriteria->add(StepPeer::TAS_UID, $sTaskUID);
  	  $oDataset1 = StepPeer::doSelectRS($oCriteria);
      $oDataset1->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset1->next();
      while ($aRow1 = $oDataset1->getRow()) {
      	//Delete the triggers assigned to step
      	$oCriteria = new Criteria('workflow');
  	    $oCriteria->add(StepTriggerPeer::STEP_UID, $aRow1['STEP_UID']);
  	    $oDataset2 = StepTriggerPeer::doSelectRS($oCriteria);
        $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset2->next();
        while ($aRow2 = $oDataset2->getRow()) {
        	$oStepTrigger->remove($aRow2['STEP_UID'], $aRow2['TAS_UID'], $aRow2['TRI_UID'], $aRow2['ST_TYPE']);
        	$oDataset2->next();
        }
        $oStep->remove($aRow1['STEP_UID']);
      	$oDataset1->next();
      }
			//Delete task
			$oTask->remove($sTaskUID);
		}
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

  /*
	* Delete all routes from a task
	* @param string $sProcessUID
	* @param string $sTaskUID
	* @return boolean
	*/
  public function deleteAllRoutesOfTask($sProcessUID = '', $sTaskUID = '', $bAll = false) {
  	try {
  		$oProcess  = new Process();
  	  $aFields   = $oProcess->load($sProcessUID);
  	  $oTask     = new Task();
  	  $aFields   = $oTask->load($sTaskUID);
  	  $oCriteria = new Criteria('workflow');
  	  $oCriteria->add(RoutePeer::PRO_UID, $sProcessUID);
  	  $oCriteria->add(RoutePeer::TAS_UID, $sTaskUID);
  	  RoutePeer::doDelete($oCriteria);
  	  if ($bAll) {
  	    $oCriteria = new Criteria('workflow');
  	    $oCriteria->add(RoutePeer::PRO_UID,   $sProcessUID);
  	    $oCriteria->add(RoutePeer::ROU_NEXT_TASK, $sTaskUID);
  	    RoutePeer::doDelete($oCriteria);
  	  }
  	  return true;
    }
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

  /*
	* Assign a user to task
	* @param string $sTaskUID
	* @param string $sUserUID
	* @param string $iType
	* @return integer
	*/
  public function assignUser($sTaskUID = '', $sUserUID = '', $iType = '') {
  	try {
  	  $oTaskUser = new TaskUser();
  	  return $oTaskUser->create(array('TAS_UID' => $sTaskUID, 'USR_UID' => $sUserUID, 'TU_TYPE' => $iType, 'TU_RELATION' => 1));
  	}
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

  /*
	* Assign a group to task
	* @param string $sTaskUID
	* @param string $sGroupUID
	* @param string $iType
	* @return integer
	*/
  public function assignGroup($sTaskUID = '', $sGroupUID = '', $iType = '') {
  	try {
  		$oTaskUser = new TaskUser();
  	  /*$oCriteria = new Criteria('workflow');
  	  $oCriteria->add(GroupUserPeer::GRP_UID, $sGroupUID);
  	  $oDataset = GroupUserPeer::doSelectRS($oCriteria);
  	  $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aGroupUser = $oDataset->getRow()) {
      	$oCriteria = new Criteria('workflow');
  	    $oCriteria->add(TaskUserPeer::TAS_UID, $sTaskUID);
  	    $oCriteria->add(TaskUserPeer::USR_UID, $aGroupUser['USR_UID']);
  	    $oDataset2 = TaskUserPeer::doSelectRS($oCriteria);
  	    $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset2->next();
      	$aRow = $oDataset2->getRow();
      	if (!is_array($aRow)) {
      		$this->assignUser($sTaskUID, $aGroupUser['USR_UID'], $iType);
        }
      	$oDataset->next();
      }*/
  	  return $oTaskUser->create(array('TAS_UID' => $sTaskUID, 'USR_UID' => $sGroupUID, 'TU_TYPE' => $iType, 'TU_RELATION' => 2));
  	}
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

  public function ofToAssignUserOfAllTasks($sUserUID = '') {
  	try {
  		$oCriteria = new Criteria('workflow');
  	  $oCriteria->add(TaskUserPeer::USR_UID, $sUserUID);
  	  TaskUserPeer::doDelete($oCriteria);
  	}
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

  /*
	* Of to assign a user from a task
	* @param string $sTaskUID
	* @param string $sUserUID
	* @param integer $iType
	* @return boolean
	*/
  public function ofToAssignUser($sTaskUID = '', $sUserUID = '', $iType = 0) {
  	try {
  	  $oTaskUser = new TaskUser();
  	  $oTaskUser->remove($sTaskUID, $sUserUID, $iType, 1);
  	  return true;
  	}
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

  /*
	* Of to assign a group from a task
	* @param string $sTaskUID
	* @param string $sGroupUID
	* @param integer $iType
	* @return boolean
	*/
  public function ofToAssignGroup($sTaskUID = '', $sGroupUID = '', $iType = 0) {
  	try {
  		$oTaskUser = new TaskUser();
  	  $oCriteria = new Criteria('workflow');
  	  $oCriteria->add(GroupUserPeer::GRP_UID, $sGroupUID);
  	  $oDataset = GroupUserPeer::doSelectRS($oCriteria);
  	  $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aGroupUser = $oDataset->getRow()) {
      	$oCriteria = new Criteria('workflow');
  	    $oCriteria->add(TaskUserPeer::TAS_UID, $sTaskUID);
  	    $oCriteria->add(TaskUserPeer::USR_UID, $aGroupUser['USR_UID']);
  	    $oDataset2 = TaskUserPeer::doSelectRS($oCriteria);
  	    $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset2->next();
      	$aRow = $oDataset2->getRow();
      	if (is_array($aRow)) {
      		$this->ofToAssignUser($sTaskUID, $aGroupUser['USR_UID'], $iType);
        }
      	$oDataset->next();
      }
  	  return $oTaskUser->remove($sTaskUID, $sGroupUID, $iType, 2);
  	}
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

  /*
	* Get the assigned steps of a task
	* @param string $sTaskUID
	* @return array
	*/
  public function getStepsOfTask($sTaskUID) {
  	try {
  	  $aSteps    = array();
  	  $oCriteria = new Criteria('workflow');
      $oCriteria->add(StepPeer::TAS_UID, $sTaskUID);
      $oCriteria->addAscendingOrderByColumn(StepPeer::STEP_POSITION);
      $oDataset = StepPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
      	$aSteps[] = $aRow;
      	$oDataset->next();
      }
      return $aSteps;
    }
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

  /*
	* Return if exists building elements to add steps
	* @param string $sProcessUID
	* @return boolean
	*/
  public function existsBuildingElements($sProcessUID) {
  	try {
  	  $oCriteria = new Criteria('workflow');
      //$oCriteria->add(StepPeer::PRO_UID, $sProcessUID);
      //$oDataset = StepPeer::doSelectRS($oCriteria);
      //$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      //$oDataset->next();
      return true;
  	}
  	catch (Exception $oError) {
    	throw($oError);
    }
  }
}
?>