<?php
/**
 * class.processes.php
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

require_once 'classes/model/Content.php';
require_once 'classes/model/Process.php';
require_once 'classes/model/Task.php';
require_once 'classes/model/Route.php';
require_once 'classes/model/Route.php';
require_once 'classes/model/SwimlanesElements.php';
require_once 'classes/model/InputDocument.php';
require_once 'classes/model/ObjectPermission.php';
require_once 'classes/model/OutputDocument.php';
require_once 'classes/model/Step.php';
require_once 'classes/model/StepTrigger.php';
require_once 'classes/model/Dynaform.php';
require_once 'classes/model/Triggers.php';
require_once 'classes/model/Groupwf.php';
require_once 'classes/model/ReportTable.php';
require_once 'classes/model/ReportVar.php';
require_once 'classes/model/DbSource.php';
require_once 'classes/model/StepSupervisor.php';

G::LoadClass('tasks');
G::LoadClass('reportTables');
G::LoadThirdParty('pear/json','class.json');

class Processes {

  /*
  * change Status of any Process
  * @param string $sProUid
  * @return boolean
  */
  function changeStatus ( $sProUid = '') {
    $oProcess = new Process();
    $Fields = $oProcess->Load( $sProUid );
    $proFields['PRO_UID'] = $sProUid;
    if ( $Fields['PRO_STATUS'] == 'ACTIVE' )
      $proFields['PRO_STATUS'] = 'INACTIVE';
    else
      $proFields['PRO_STATUS'] = 'ACTIVE';

    $oProcess->Update( $proFields );
  }

  /*
  * changes in DB the parent GUID
  * @return $sProUid
  */
  function changeProcessParent( $sProUid, $sParentUid) {
    $oProcess = new Process();
    $Fields = $oProcess->Load( $sProUid );
    $proFields['PRO_UID'] = $sProUid;
    $Fields['PRO_PARENT'] == $sParentUid;
    $oProcess->Update( $proFields );
  }

  /*
  * verify if the process $sProUid exists
  * @param string $sProUid
  * @return boolean
  */
  function processExists ( $sProUid = '') {
    $oProcess = new Process();
    return $oProcess->processExists( $sProUid );
  }

  /*
  * get an unused process GUID
  * @return $sProUid
  */
  function getUnusedProcessGUID( ) {
    do {
     $sNewProUid = G::generateUniqueID() ;
    } while ( $this->processExists ( $sNewProUid ) );
    return $sNewProUid;
  }

  /*
  * verify if the task  $sTasUid exists
  * @param string $sTasUid
  * @return boolean
  */
  function taskExists ( $sTasUid = '') {
    $oTask = new Task();
    return $oTask->taskExists( $sTasUid );
  }

  /*
  * get an unused task GUID
  * @return $sTasUid
  */
  function getUnusedTaskGUID( ) {
    do {
     $sNewTasUid = G::generateUniqueID() ;
    } while ( $this->taskExists ( $sNewTasUid ) );
    return $sNewTasUid;
  }

  /*
  * verify if the dynaform $sDynUid exists
  * @param string $sDynUid
  * @return boolean
  */
  function dynaformExists ( $sDynUid = '') {
    $oDynaform = new Dynaform();
    return $oDynaform->dynaformExists( $sDynUid );
  }

  /*
  * verify if the object exists
  * @param string $sUid
  * @return boolean
  */
  function inputExists ( $sUid = '') {
    $oInput = new InputDocument();
    return $oInput->inputExists( $sUid );
  }


  /*
  * verify if the object exists
  * @param string $sUid
  * @return boolean
  */
  function outputExists ( $sUid = '') {
    $oOutput = new OutputDocument();
    return $oOutput->outputExists( $sUid );
  }

  /*
  * verify if the object exists
  * @param string $sUid
  * @return boolean
  */
  function triggerExists ( $sUid = '') {
    $oTrigger = new Triggers();
    return $oTrigger->triggerExists( $sUid );
  }

  /*
  * get an unused input GUID
  * @return $sProUid
  */
  function getUnusedInputGUID( ) {
    do {
     $sNewUid = G::generateUniqueID() ;
    } while ( $this->inputExists ( $sNewUid ) );
    return $sNewUid;
  }

  /*
  * get an unused output GUID
  * @return $sProUid
  */
  function getUnusedOutputGUID( ) {
    do {
     $sNewUid = G::generateUniqueID() ;
    } while ( $this->outputExists ( $sNewUid ) );
    return $sNewUid;
  }

  /*
  * get an unused trigger GUID
  * @return $sProUid
  */
  function getUnusedTriggerGUID( ) {
    do {
     $sNewUid = G::generateUniqueID() ;
    } while ( $this->triggerExists ( $sNewUid ) );
    return $sNewUid;
  }

  /*
  * verify if the object exists
  * @param string $sUid
  * @return boolean
  */
  function stepExists ( $sUid = '') {
    $oStep = new Step();
    return $oStep->stepExists( $sUid );
  }
  /*
  * get an unused step GUID
  * @return $sUid
  */
  function getUnusedStepGUID( ) {
    do {
     $sNewUid = G::generateUniqueID() ;
    } while ( $this->stepExists ( $sNewUid ) );
    return $sNewUid;
  }


  /*
  * get an unused Dynaform GUID
  * @return $sDynUid
  */
  function getUnusedDynaformGUID( ) {
    do {
     $sNewUid = G::generateUniqueID() ;
    } while ( $this->dynaformExists ( $sNewUid ) );
    return $sNewUid;
  }

  /*
  * change the GUID for a serialized process
  * @param string $sProUid
  * @return boolean
  */
  function setProcessGUID( &$oData, $sNewProUid ) {
  	$sProUid = $oData->process['PRO_UID'];
  	$oData->process['PRO_UID'] = $sNewProUid;

  	foreach ($oData->tasks as $key => $val ) {
  		$oData->tasks[$key]['PRO_UID'] = $sNewProUid;
  	}
  	foreach ($oData->routes as $key => $val ) {
  		$oData->routes[$key]['PRO_UID'] = $sNewProUid;
  	}
  	foreach ($oData->lanes as $key => $val ) {
  		$oData->lanes[$key]['PRO_UID'] = $sNewProUid;
  	}
  	foreach ($oData->inputs as $key => $val ) {
  		$oData->inputs[$key]['PRO_UID'] = $sNewProUid;
  	}
  	foreach ($oData->outputs as $key => $val ) {
  		$oData->outputs[$key]['PRO_UID'] = $sNewProUid;
  	}
  	foreach ($oData->steps as $key => $val ) {
  		$oData->steps[$key]['PRO_UID'] = $sNewProUid;
  	}
  	foreach ($oData->dynaforms as $key => $val ) {
  		$oData->dynaforms[$key]['PRO_UID'] = $sNewProUid;
  	}
  	foreach ($oData->triggers as $key => $val ) {
  		$oData->triggers[$key]['PRO_UID'] = $sNewProUid;
  	}
  	foreach ($oData->dbconnections as $key => $val ) {
  		$oData->dbconnections[$key]['PRO_UID'] = $sNewProUid;
  	}
	foreach ($oData->objectPermissions as $key => $val ) {
		$oData->objectPermissions[$key]['PRO_UID'] = $sNewProUid;
	}
	
  	return true;
  }

  /*
  * change the GUID Parent for a serialized process, only in serialized data
  * @param string $sProUid
  * @return boolean
  */
  function setProcessParent( &$oData, $sParentUid ) {
  	$oData->process['PRO_PARENT'] = $sParentUid;
  	$oData->process['PRO_CREATE_DATE'] = date ('Y-m-d H:i:s');
  	$oData->process['PRO_UPDATE_DATE'] = date ('Y-m-d H:i:s');
  	return true;
  }

  /*
  * change and Renew all Task GUID, because the process needs to have a new set of tasks
  * @param string $oData
  * @return boolean
  */
  function renewAllTaskGuid ( &$oData ) {
  	$map = array ();
  	foreach ( $oData->tasks as $key => $val ) {
  	  $newGuid = $this->getUnusedTaskGUID();
  	  $map[ $val['TAS_UID'] ] = $newGuid;
  	  $oData->tasks[$key]['TAS_UID'] = $newGuid;
  	}
  	foreach ( $oData->routes as $key => $val ) {
  	  $newGuid = $map[ $val['TAS_UID'] ];
  	  $oData->routes[$key]['TAS_UID'] = $newGuid;
  	  if ( strlen ( $val['ROU_NEXT_TASK'] ) > 0 && $val['ROU_NEXT_TASK'] >0 ) {
  	    $newGuid = $map[ $val['ROU_NEXT_TASK'] ];
  	    $oData->routes[$key]['ROU_NEXT_TASK'] = $newGuid;
  	  }
  	}
  	foreach ( $oData->steps as $key => $val ) {
  	  $newGuid = $map[ $val['TAS_UID'] ];
  	  $oData->steps[$key]['TAS_UID'] = $newGuid;
  	}

  	foreach ( $oData->steptriggers as $key => $val ) {
  	  $newGuid = $map[ $val['TAS_UID'] ];
  	  $oData->steptriggers[$key]['TAS_UID'] = $newGuid;
  	}
  	foreach ( $oData->taskusers as $key => $val ) {
  	  $newGuid = $map[ $val['TAS_UID'] ];
  	  $oData->taskusers[$key]['TAS_UID'] = $newGuid;
  	}
  }

  /*
  * change and Renew all Dynaform GUID, because the process needs to have a new set of dynaforms
  * @param string $oData
  * @return boolean
  */
  function renewAllDynaformGuid ( &$oData ) {
  	$map = array ();
  	foreach ( $oData->dynaforms as $key => $val ) {
  	  $newGuid = $this->getUnusedDynaformGUID();
  	  $map[ $val['DYN_UID'] ] = $newGuid;
  	  $oData->dynaforms[$key]['DYN_UID'] = $newGuid;
  	}
  	foreach ( $oData->steps as $key => $val ) {
  	  if ( $val['STEP_TYPE_OBJ'] == 'DYNAFORM' ) {
    	  $newGuid = $map[ $val['STEP_UID_OBJ'] ];
  	    $oData->steps[$key]['STEP_UID_OBJ'] = $newGuid;
  	  }
  	}
  	foreach ( $oData->dynaformFiles as $key => $val ) {
  	  $newGuid = $map[ $key ];
	    $oData->dynaformFiles[$key] = $newGuid;
  	}
  }

  function getProcessRow ($sProUid ){
    $oProcess = new Process( );
    return $oProcess->Load( $sProUid );
  }

  function createProcessRow ($row ){
    $oProcess = new Process( );
    return $oProcess->createRow($row);
  }

  function updateProcessRow ($row ){
    $oProcess = new Process( );
    return $oProcess->update($row);
  }


/*
	* Get all Swimlanes Elements for any Process
	* @param string $sProUid
	* @return array
	*/
  public function getAllLanes($sProUid) {
  	try {
  	  $aLanes   = array();
  	  $oCriteria = new Criteria('workflow');
      $oCriteria->add(SwimlanesElementsPeer::PRO_UID,     $sProUid);
      $oDataset = SwimlanesElementsPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
      	$oSwim = new SwimlanesElements();
      	$aLanes[] = $oSwim->Load($aRow['SWI_UID']);
      	$oDataset->next();
      }
      return $aLanes;
    }
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

  function getTaskRows ($sProUid ){
    $oTask = new Tasks( );
    return $oTask->getAllTasks( $sProUid );
  }    

  function createTaskRows ($aTasks ){
    $oTask = new Tasks( );
    return $oTask->createTaskRows( $aTasks );
  }

  function updateTaskRows ($aTasks ){
    $oTask = new Tasks( );
    return $oTask->updateTaskRows( $aTasks );
  }  

  function getRouteRows ($sProUid ){
    $oTask = new Tasks( );
    return $oTask->getAllRoutes( $sProUid );
  }

  function createRouteRows ($aRoutes ){
    $oTask = new Tasks( );
    return $oTask->createRouteRows( $aRoutes );
  }

  function updateRouteRows ($aRoutes ){
    $oTask = new Tasks( );
    return $oTask->updateRouteRows( $aRoutes );
  }

  function getLaneRows ($sProUid ){  //SwimlanesElements
    return $this->getAllLanes( $sProUid );
  }

  function createLaneRows ($aLanes ){  //SwimlanesElements
  	foreach ( $aLanes as $key => $row ) {
      $oLane = new SwimlanesElements();
      //unset ($row['TAS_UID']);
      $res = $oLane->create($row);
  	}
  	return;
  }

  function getInputRows ($sProUid ){  //SwimlanesElements
  	try {
  	  $aInput   = array();
  	  $oCriteria = new Criteria('workflow');
      $oCriteria->add(InputdocumentPeer::PRO_UID,     $sProUid);
      $oDataset = InputdocumentPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
      	$oInput = new Inputdocument();
      	$aInput[] = $oInput->Load( $aRow['INP_DOC_UID'] );
      	$oDataset->next();
      }
      return $aInput;
    }
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

  function createInputRows ($aInput ){
  	foreach ( $aInput as $key => $row ) {
      $oInput = new Inputdocument();
      //unset ($row['TAS_UID']);
      $res = $oInput->create($row);
  	}
  	return;
  }

  /*
  * change and Renew all Input GUID, because the process needs to have a new set of Inputs
  * @param string $oData
  * @return boolean
  */
  function renewAllInputGuid ( &$oData ) {
  	$map = array ();
  	foreach ( $oData->inputs as $key => $val ) {
  	  $newGuid = $this->getUnusedInputGUID();
  	  $map[ $val['INP_DOC_UID'] ] = $newGuid;
  	  $oData->inputs[$key]['INP_DOC_UID'] = $newGuid;
  	}
  	foreach ( $oData->steps as $key => $val ) {
  	  if ( $val['STEP_TYPE_OBJ'] == 'INPUT_DOCUMENT' ) {
    	  $newGuid = $map[ $val['STEP_UID_OBJ'] ];
  	    $oData->steps[$key]['STEP_UID_OBJ'] = $newGuid;
  	  }
  	}
  }

  function getOutputRows ($sProUid ){  //SwimlanesElements
  	try {
  	  $aOutput   = array();
  	  $oCriteria = new Criteria('workflow');
      $oCriteria->add(OutputdocumentPeer::PRO_UID,     $sProUid);
      $oDataset = OutputdocumentPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
      	$oOutput = new Outputdocument();
      	$aOutput[] = $oOutput->Load( $aRow['OUT_DOC_UID'] );
      	$oDataset->next();
      }
      return $aOutput;
    }
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

  function createOutputRows ($aOutput ){
  	foreach ( $aOutput as $key => $row ) {
      $oOutput = new Outputdocument();
      //unset ($row['TAS_UID']);
      $res = $oOutput->create($row);
  	}
  	return;
  }

  /*
  * change and Renew all Output GUID, because the process needs to have a new set of Outputs
  * @param string $oData
  * @return boolean
  */
  function renewAllOutputGuid ( &$oData ) {
  	$map = array ();
  	foreach ( $oData->outputs as $key => $val ) {
  	  $newGuid = $this->getUnusedOutputGUID();
  	  $map[ $val['OUT_DOC_UID'] ] = $newGuid;
  	  $oData->outputs[$key]['OUT_DOC_UID'] = $newGuid;
  	}
  	foreach ( $oData->steps as $key => $val ) {
  	  if ( $val['STEP_TYPE_OBJ'] == 'OUTPUT_DOCUMENT' ) {
    	  $newGuid = $map[ $val['STEP_UID_OBJ'] ];
  	    $oData->steps[$key]['STEP_UID_OBJ'] = $newGuid;
  	  }
  	}
  }

  /*
  * change and Renew all Trigger GUID, because the process needs to have a new set of Triggers
  * @param string $oData
  * @return boolean
  */
  function renewAllTriggerGuid ( &$oData ) {
  	$map = array ();
  	foreach ( $oData->triggers as $key => $val ) {
  	  $newGuid = $this->getUnusedTriggerGUID();
  	  $map[ $val['TRI_UID'] ] = $newGuid;
  	  $oData->triggers[$key]['TRI_UID'] = $newGuid;
  	}
  	foreach ( $oData->steptriggers as $key => $val ) {
  	  $newGuid = $map[ $val['TRI_UID'] ];
	    $oData->steptriggers[$key]['TRI_UID'] = $newGuid;
  	}
  }

  function getStepRows ($sProUid ){  //SwimlanesElements
  	try {
  	  $aStep   = array();
  	  $oCriteria = new Criteria('workflow');
      $oCriteria->add(StepPeer::PRO_UID,  $sProUid);
      $oDataset = StepPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
      	$oStep = new Step();
      	$aStep[] = $oStep->Load( $aRow['STEP_UID'] );
      	$oDataset->next();
      }
      return $aStep;
    }
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

  function createStepRows ($aStep ){
  	foreach ( $aStep as $key => $row ) {
      $oStep = new Step();
      //unset ($row['TAS_UID']);
      $res = $oStep->create($row);
  	}
  	return;
  }

  function createStepSupervisorRows($aStepSupervisor){
    foreach ($aStepSupervisor as $key => $row ) {
      $oStepSupervisor = new StepSupervisor();
	  if( $oStepSupervisor->Exists($row['STEP_UID']) ) {
		$oStepSupervisor->remove($row['STEP_UID']);
	  }
	  $oStepSupervisor->create($row);
    }
  } #@!Neyek

  /*
  * change and Renew all Step GUID, because the process needs to have a new set of Steps
  * @param string $oData
  * @return boolean
  */
  function renewAllStepGuid ( &$oData ) {
  	$map = array ();
  	foreach ( $oData->steps as $key => $val ) {
  	  $newGuid = $this->getUnusedStepGUID();
  	  $map[ $val['STEP_UID'] ] = $newGuid;
  	  $oData->steps[$key]['STEP_UID'] = $newGuid;
  	}
  	foreach ( $oData->steptriggers as $key => $val ) {
  		if ( $val['STEP_UID'] > 0 ) {
  		  if (isset($map[ $val['STEP_UID'] ])) {
  	      $newGuid = $map[ $val['STEP_UID'] ];
	        $oData->steptriggers[$key]['STEP_UID'] = $newGuid;
	      }
	      else {
	        $oData->steptriggers[$key]['STEP_UID'] = $this->getUnusedStepGUID();
	      }
	    }
  	}
  }


  function getDynaformRows ($sProUid ){
  	try {
  	  $aDynaform   = array();
  	  $oCriteria = new Criteria('workflow');
      $oCriteria->add(DynaformPeer::PRO_UID,  $sProUid);
      $oDataset = DynaformPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
      	$oDynaform = new Dynaform();
      	$aDynaform[] = $oDynaform->Load( $aRow['DYN_UID'] );
      	$oDataset->next();
      }
      return $aDynaform;
    }
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

  function getObjectPermissionRows ($sProUid ){ // by erik
	  try {
		  $oPermissions   = array();
		  $oCriteria = new Criteria('workflow');
		  $oCriteria->add(ObjectPermissionPeer::PRO_UID,  $sProUid);
		  $oDataset = ObjectPermissionPeer::doSelectRS($oCriteria);
		  $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
		  $oDataset->next();
		  while ($aRow = $oDataset->getRow()) {
			  $o = new ObjectPermission();
			  $oPermissions[] = $o->Load( $aRow['OP_UID'] );
			  $oDataset->next();
		  }
		  return $oPermissions;
	  }
	  catch (Exception $oError) {
		  throw($oError);
	  }
  }#@!neyek

  function createDynaformRows ($aDynaform ){
  	foreach ( $aDynaform as $key => $row ) {
      $oDynaform = new Dynaform();
      //unset ($row['TAS_UID']);
      $res = $oDynaform->create($row);
  	}
  	return;
  }

  function createObjectPermissions ($objectPermissions){ //by erik
	  foreach ( $objectPermissions as $key => $row ) {
		  $o = new ObjectPermission();
		  if( $o->Exists($row['OP_UID']) ) {
			  $o->remove($row['OP_UID']);
		  }
		 
		  $res = $o->create($row);
	  }
	  return;
  }#@!neyek

  function createStepTriggerRows ($aStepTrigger ){
  	foreach ( $aStepTrigger as $key => $row ) {
      $oStepTrigger = new StepTrigger();
      //unset ($row['TAS_UID']);
      $res = $oStepTrigger->createRow($row);
  	}
  	return;
  }

  function getStepTriggerRows ($aTask ){
  	try {
  		$aInTasks = array();
  		foreach ( $aTask as $key => $val ) {
  			$aInTasks[] = $val['TAS_UID'];
  		}

  	  $aTrigger   = array();
  	  $oCriteria = new Criteria('workflow');
      $oCriteria->add( StepTriggerPeer::TAS_UID,  $aInTasks, Criteria::IN );
      $oDataset = StepTriggerPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $aStepTrigger = array();
      while ($aRow = $oDataset->getRow()) {
      	$aStepTrigger[] = $aRow;
      	$oDataset->next();
      }
      return $aStepTrigger;
    }
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

  function getTriggerRows ($sProUid ){
  	try {
  	  $aTrigger   = array();
  	  $oCriteria = new Criteria('workflow');
      $oCriteria->add( TriggersPeer::PRO_UID,  $sProUid);
      $oDataset = TriggersPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
      	$oTrigger = new Triggers();
      	$aTrigger[] = $oTrigger->Load( $aRow['TRI_UID'] );
      	$oDataset->next();
      }
      return $aTrigger;
    }
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

  function createTriggerRows ($aTrigger ){
  	foreach ( $aTrigger as $key => $row ) {
      $oTrigger = new Triggers();
      //unset ($row['TAS_UID']);
      $res = $oTrigger->create($row);
  	}
  	return;
  }

  function getGroupwfRows ($aGroups ){
  	try {
  		$aInGroups = array();
  		foreach ( $aGroups as $key => $val ) {
  			$aInGroups[] = $val['USR_UID'];
  		}

  	  $aGroupwf   = array();
  	  $oCriteria = new Criteria('workflow');
      $oCriteria->add( GroupwfPeer::GRP_UID,  $aInGroups, Criteria::IN );
      $oDataset = GroupwfPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
      	$oGroupwf = new Groupwf();
      	$aGroupwf[] = $oGroupwf->Load( $aRow['GRP_UID'] );
      	$oDataset->next();
      }
      return $aGroupwf;
    }
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

  function getDBConnectionsRows($sProUid) {
    try {
      $aConnections = array();
  	  $oCriteria = new Criteria('workflow');
      $oCriteria->add(DbSourcePeer::PRO_UID, $sProUid);
      $oDataset = DbSourcePeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
      	$oConnection = new DbSource();
      	$aConnections[] = $oConnection->Load($aRow['DBS_UID']);
      	$oDataset->next();
      }
      return $aConnections;
    }
    catch (Exception $oError) {
      throw $oError;
    }
  }

  function getStepSupervisorRows($sProUid)
  {
    try {
      $aConnections = array();
      $oCriteria = new Criteria('workflow');
      $oCriteria->add(StepSupervisorPeer::PRO_UID, $sProUid);
      $oDataset = StepSupervisorPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $aStepSup = array();
      while ($aRow  = $oDataset->getRow()) {
        $aStepSup[] = $aRow;
        $oDataset->next();
      }
      return $aStepSup;
    }
    catch (Exception $oError) {
      throw $oError;
    }
  }

  function getReportTablesRows($sProUid)
  {
    try {
      $aReps = array();
      $oCriteria = new Criteria('workflow');
      $oCriteria->add(ReportTablePeer::PRO_UID, $sProUid);
      $oDataset = ReportTablePeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
        $oRep = new ReportTable();
        $aReps[] = $oRep->load($aRow['REP_TAB_UID']);
        $oDataset->next();
      }
      return $aReps;
    }
    catch (Exception $oError) {
      throw $oError;
    }
  }

  function getReportTablesVarsRows($sProUid)
  {
    try {
      $aRepVars = array();
      $oCriteria = new Criteria('workflow');
      $oCriteria->add(ReportVarPeer::PRO_UID, $sProUid);
      $oDataset = ReportVarPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
        $oRepVar = new ReportVar();
        $aRepVars[] = $oRepVar->load($aRow['REP_VAR_UID']);
        $oDataset->next();
      }
      return $aRepVars;
    }
    catch (Exception $oError) {
      throw $oError;
    }
  }

  function getTaskUserRows ($aTask ){
  	try {
  		$aInTasks = array();
  		foreach ( $aTask as $key => $val ) {
  			$aInTasks[] = $val['TAS_UID'];
  		}

  	  $aTaskUser   = array();
  	  $oCriteria = new Criteria('workflow');
      $oCriteria->add( TaskUserPeer::TAS_UID,  $aInTasks, Criteria::IN );
      $oCriteria->add( TaskUserPeer::TU_RELATION,  2 );
      $oDataset = TaskUserPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
      	$oCriteria2 = new Criteria('workflow');
      	$oCriteria2->clearSelectColumns();
      	$oCriteria2->addSelectColumn ( 'COUNT(*)' );
        $oCriteria2->add( GroupwfPeer::GRP_UID,    $aRow['USR_UID']);
        $oCriteria2->add( GroupwfPeer::GRP_STATUS, 'ACTIVE' );
        $oDataset2 = GroupwfPeer::doSelectRS($oCriteria2);
        //$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset2->next();
        $aRow2 = $oDataset2->getRow();
        $bActiveGroup = $aRow2[0];
        if ( $bActiveGroup == 1 )
      	  $aTaskUser[] = $aRow;
      	$oDataset->next();
      }
      return $aTaskUser;
    }
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

  function createTaskUserRows ($aTaskUser ){
  	foreach ( $aTaskUser as $key => $row ) {
      $oTaskUser = new TaskUser();
      $res = $oTaskUser->create($row);
  	}
  	return;
  }

  function createGroupRow ($aGroupwf ) {
  	foreach ( $aGroupwf as $key => $row ) {
      $oGroupwf = new Groupwf();
      if ( ! $oGroupwf->GroupwfExists ( $row['GRP_UID'] ) ) {
        $res = $oGroupwf->create($row);
  		}
    }
  }

  function createDBConnectionsRows ($aConnections ) {
  	foreach ( $aConnections as $sKey => $aRow ) {
      $oConnection = new DbSource();
	  if( $oConnection->Exists($aRow['DBS_UID']) ) {
		$oConnection->remove($aRow['DBS_UID']);
	  }
      $oConnection->create($aRow);
    }
  } #@!neyek

  function createReportTables($aReportTables, $aReportTablesVars)
  {
    $this->createReportTablesVars($aReportTablesVars);
    $oReportTables = new ReportTables();
    foreach ( $aReportTables as $sKey => $aRow ) {
      $oRep = new ReportTable();
      $oRep->create($aRow);
      $aFields = $oReportTables->getTableVars($aRow['REP_TAB_UID'], true);
      $oReportTables->createTable($aRow['REP_TAB_NAME'], $aRow['REP_TAB_CONNECTION'], $aRow['REP_TAB_TYPE'], $aFields);
      $oReportTables->populateTable($aRow['REP_TAB_NAME'], $aRow['REP_TAB_CONNECTION'], $aRow['REP_TAB_TYPE'], $aFields, $aRow['PRO_UID'], $aRow['REP_TAB_GRID']);
    }
  } #@!neyek

  function updateReportTables($aReportTables, $aReportTablesVars)
  {
    $this->cleanupReportTablesReferences($aReportTable);
    $this->createReportTables($aReportTables, $aReportTablesVars);
  } #@!neyek

  function createReportTablesVars($aReportTablesVars)
  {
    foreach ( $aReportTablesVars as $sKey => $aRow ) {
      $oRep = new ReportVar();
      $oRep->create($aRow);
    }
  } #@!neyek

  function cleanupReportTablesReferences($aReportTables)
  {
    foreach ( $aReportTables as $sKey => $aRow ) {
        $oReportTables = new ReportTables();
        $oReportTables->deleteReportTable($aRow['REP_TAB_UID']);
        $oReportTables->deleteAllReportVars($aRow['REP_TAB_UID']);
        $oReportTables->dropTable($aRow['REP_TAB_NAME']);
    }
  } #@!neyek

  /*
  * change Status of any Process
  * @param string $sProUid
  * @return boolean
  */
  function serializeProcess ( $sProUid = '') {
    $oProcess = new Process( );

    $oData->process  = $this->getProcessRow( $sProUid );
    $oData->tasks    = $this->getTaskRows( $sProUid );    
    $oData->routes   = $this->getRouteRows( $sProUid );
    $oData->lanes    = $this->getLaneRows( $sProUid );
    $oData->inputs   = $this->getInputRows( $sProUid );
    $oData->outputs  = $this->getOutputRows( $sProUid );
    $oData->dynaforms= $this->getDynaformRows ( $sProUid );
    $oData->steps    = $this->getStepRows( $sProUid );
    $oData->triggers = $this->getTriggerRows( $sProUid );
    $oData->taskusers= $this->getTaskUserRows( $oData->tasks );
    $oData->groupwfs = $this->getGroupwfRows( $oData->taskusers );
    $oData->steptriggers  = $this->getStepTriggerRows( $oData->tasks );
    $oData->dbconnections = $this->getDBConnectionsRows($sProUid);
    $oData->reportTables  = $this->getReportTablesRows($sProUid);
    $oData->reportTablesVars  = $this->getReportTablesVarsRows($sProUid);
    $oData->stepSupervisor    = $this->getStepSupervisorRows($sProUid);
    $oData->objectPermissions = $this->getObjectPermissionRows ($sProUid);
    //krumo ($oData);die;			
    //$oJSON = new Services_JSON();
    //krumo ( $oJSON->encode($oData) );
    //return $oJSON->encode($oData);
	return serialize($oData);
  }

  function saveSerializedProcess ( $oData ) {  	   	      
    //$oJSON = new Services_JSON();
    //$data = $oJSON->decode($oData);
    //$sProUid = $data->process->PRO_UID;
    $data = unserialize ($oData);
    /*
    echo"<textarea>";
    print_r($data); die; 
    echo"</textarea>";
    */
    $sProUid = $data->process['PRO_UID'];
    $path = PATH_DOCUMENT . 'output' . PATH_SEP;

    if ( !is_dir($path) ) {
      	G::verifyPath($path, true);
    }
    $proTitle = G::capitalizeWords($data->process['PRO_TITLE']);

    $index = '';

    $lastIndex = '';

    do {
      $filename = $path . $proTitle . $index . '.pm';
      $lastIndex = $index;

      if ( $index == '' )
        $index = 1;
      else
        $index ++;
    } while ( file_exists ( $filename )  );


    $proTitle .= $lastIndex;
    $filenameOnly = $proTitle . '.pm';

    $fp = fopen( $filename.'tpm', "wb");

    $fsData = sprintf ( "%09d", strlen ( $oData) );
    $bytesSaved = fwrite( $fp, $fsData );  //writing the size of $oData
    $bytesSaved += fwrite( $fp, $oData ); //writing the $oData
	
    foreach ($data->dynaforms as $key => $val ) {
    	$sFileName = PATH_DYNAFORM .  $val['DYN_FILENAME'] . '.xml';
    	if ( file_exists ( $sFileName ) ) {
            $xmlGuid    = $val['DYN_UID'];
            $fsXmlGuid  = sprintf ( "%09d", strlen ( $xmlGuid ) );
            $bytesSaved += fwrite( $fp, $fsXmlGuid );  //writing the size of xml file
            $bytesSaved += fwrite( $fp, $xmlGuid );    //writing the xmlfile

            $xmlContent = file_get_contents ( $sFileName );
            $fsXmlContent = sprintf ( "%09d", strlen ( $xmlContent) );
            $bytesSaved += fwrite( $fp, $fsXmlContent );  //writing the size of xml file
            $bytesSaved += fwrite( $fp, $xmlContent );    //writing the xmlfile
    	}

        $sFileName2 = PATH_DYNAFORM .  $val['DYN_FILENAME'] . '.html';
        if ( file_exists ( $sFileName2 ) ) {
            $htmlGuid    = $val['DYN_UID'];
            $fsHtmlGuid  = sprintf ( "%09d", strlen ( $htmlGuid ) );
            $bytesSaved += fwrite( $fp, $fsHtmlGuid );  //writing size dynaform id
            $bytesSaved += fwrite( $fp, $htmlGuid );    //writing dynaform id

            $htmlContent = file_get_contents ( $sFileName2 );
            $fsHtmlContent = sprintf ( "%09d", strlen ( $htmlContent ) );
            $bytesSaved += fwrite( $fp, $fsHtmlContent );  //writing the size of xml file
            $bytesSaved += fwrite( $fp, $htmlContent );    //writing the htmlfile
        }		
    }
	/**
     * By <erik@colosa.com> 
     * here we should work for the new functionalities 
	 * we have a many files for attach into this file
     * 
     * here we go with the anothers files ;)
     */
	//before to do something we write a header into pm file for to do a differentiation between document types
	
	
	//create the store object
	//$file_objects = new ObjectCellection();
		  
	// for mailtemplates files	
	$MAILS_ROOT_PATH = PATH_DATA.'sites'.PATH_SEP.SYS_SYS.PATH_SEP.'mailTemplates'.PATH_SEP.$_SESSION['PROCESS'];

  $isMailTempSent = false;
  $isPublicSent = false;	  
	//if this process have any mailfile
	if ( is_dir( $MAILS_ROOT_PATH ) ) {
	  
		//get mail files list from this directory
		$file_list = scandir($MAILS_ROOT_PATH);
		
		foreach ($file_list as $filename) {
			// verify if this filename is a valid file, because it could be . or .. on *nix systems
			if($filename != '.' && $filename != '..'){
				if (@is_readable($MAILS_ROOT_PATH.PATH_SEP.$filename)) {
          $sFileName = $MAILS_ROOT_PATH . PATH_SEP . $filename;
          if ( file_exists ( $sFileName ) ) {
            if ( ! $isMailTempSent ) {
            	$bytesSaved += fwrite( $fp, 'MAILTEMPL');  
              $isMailTempSent = true; 
            }
            $htmlGuid    = $val['DYN_UID'];
            $fsFileName  = sprintf ( "%09d", strlen ( $filename ) );
            $bytesSaved += fwrite( $fp, $fsFileName );  //writing the fileName size
            $bytesSaved += fwrite( $fp, $filename );    //writing the fileName size

            $fileContent = file_get_contents ( $sFileName );
            $fsFileContent = sprintf ( "%09d", strlen ( $fileContent ) );
            $bytesSaved += fwrite( $fp, $fsFileContent );  //writing the size of xml file
            $bytesSaved += fwrite( $fp, $fileContent );    //writing the htmlfile
          }		

				} 
			}	
		}
	}

	// for public files	
	$PUBLIC_ROOT_PATH = PATH_DATA.'sites'.PATH_SEP.SYS_SYS.PATH_SEP.'public'.PATH_SEP.$_SESSION['PROCESS'];

	//if this process have any mailfile
	if ( is_dir( $PUBLIC_ROOT_PATH ) ) {
	  
		//get mail files list from this directory
		$file_list = scandir($PUBLIC_ROOT_PATH);
		
		foreach ($file_list as $filename) {
			// verify if this filename is a valid file, because it could be . or .. on *nix systems
			if($filename != '.' && $filename != '..'){
				if (@is_readable($PUBLIC_ROOT_PATH.PATH_SEP.$filename)) {
          $sFileName = $PUBLIC_ROOT_PATH . PATH_SEP . $filename;
          if ( file_exists ( $sFileName ) ) {
            if ( ! $isPublicSent ) {
            	$bytesSaved += fwrite( $fp, 'PUBLIC   ');  
              $isPublicSent = true; 
            }
            $htmlGuid    = $val['DYN_UID'];
            $fsFileName  = sprintf ( "%09d", strlen ( $filename ) );
            $bytesSaved += fwrite( $fp, $fsFileName );  //writing the fileName size
            $bytesSaved += fwrite( $fp, $filename );    //writing the fileName size

            $fileContent = file_get_contents ( $sFileName );
            $fsFileContent = sprintf ( "%09d", strlen ( $fileContent ) );
            $bytesSaved += fwrite( $fp, $fsFileContent );  //writing the size of xml file
            $bytesSaved += fwrite( $fp, $fileContent );    //writing the htmlfile
          }		

				} 
			}	
		}
	}

/*	
	// for public files	
	$PUBLIC_ROOT_PATH = PATH_DATA.'sites'.PATH_SEP.SYS_SYS.PATH_SEP.'public'.PATH_SEP.$_SESSION['PROCESS'];
	  
	//if this process have any mailfile
	if ( is_dir( $PUBLIC_ROOT_PATH ) ) {
	  
		//get mail files list from this directory
		$files_list = scandir($PUBLIC_ROOT_PATH);
		foreach ($file_list as $filename) {
			// verify if this filename is a valid file, beacuse it could be . or .. on *nix systems
			if($filename != '.' && $filename != '..'){
				if (@is_readable($PUBLIC_ROOT_PATH.PATH_SEP.$nombre_archivo)) {
					$tmp = explode('.', $filename);
					$ext = $tmp[1];
					$ext_fp = fopen($PUBLIC_ROOT_PATH.PATH_SEP.$nombre_archivo, 'r');
					$file_data = fread($ext_fp, filesize($PUBLIC_ROOT_PATH.PATH_SEP.$nombre_archivo));
					fclose($ext_fp);
					$file_objects->add($filename, $ext, $file_data,'public');
				} 
			}	
		}
	}
	
	//So,. we write the store object into pm export file
	$extended_data = serialize($file_objects);
	$bytesSaved += fwrite( $fp, $extended_data ); 
	*/
	/* under here, I've not modified those lines */
	  
    fclose ($fp);

    //$bytesSaved = file_put_contents  ( $filename  , $oData  );
    $filenameLink = 'processes_DownloadFile?p=' . $proTitle . '&r=' . rand(100,1000);

    $result['PRO_UID']         = $data->process['PRO_UID'];
    $result['PRO_TITLE']       = $data->process['PRO_TITLE'];
    $result['PRO_DESCRIPTION'] = $data->process['PRO_DESCRIPTION'];
    $result['SIZE']            = $bytesSaved;
    $result['FILENAME']        = $filenameOnly;
    $result['FILENAME_LINK']   = $filenameLink;
    return $result;
  }

  function getProcessData ( $pmFilename  ) {
    if (! file_exists($pmFilename) )
      throw ( new Exception ( 'Unable to read uploaded file, please check permissions. '));

    if (! filesize($pmFilename) >= 9 )
      throw ( new Exception ( 'Uploaded file is corrupted, please check the file before continue. '));

    $fp = fopen( $pmFilename, "rb");
    $fsData = intval( fread ( $fp, 9)); //reading the size of $oData
    $contents  = fread( $fp, $fsData );    //reading string $oData
    $oData = unserialize ($contents);

    $oData->dynaformFiles = array();
    $sIdentifier = 0;
    while ( !feof ( $fp ) && is_numeric ( $sIdentifier ) ) {
      $sIdentifier  = fread ( $fp, 9);      //reading the block identifier
      if ( is_numeric ( $sIdentifier ) ) {
      $fsXmlGuid    = intval( $sIdentifier );      //reading the size of $filename
        if ( $fsXmlGuid > 0 )
          $XmlGuid    = fread( $fp, $fsXmlGuid );    //reading string $XmlGuid
  
        $fsXmlContent = intval( fread ( $fp, 9));      //reading the size of $XmlContent
        if ( $fsXmlContent > 0 ) {
        	$oData->dynaformFiles[$XmlGuid ] = $XmlGuid;
          $XmlContent   = fread( $fp, $fsXmlContent );    //reading string $XmlContent
          unset($XmlContent);
        }
      }
    }
    fclose ( $fp);

    return $oData;
  }

  /* disable all previous process with the parent $sProUid
  */
  function disablePreviousProcesses( $sProUid ) {
	  //change status of process
  	$oCriteria = new Criteria('workflow');
  	$oCriteria->add(ProcessPeer::PRO_PARENT, $sProUid);
  	$oDataset = ProcessPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    $oProcess = new Process();
    while ($aRow = $oDataset->getRow()) {
    	$aRow['PRO_STATUS'] = 'DISABLED';
    	$aRow['PRO_UPDATE_DATE'] = 'now';
    	$oProcess->update ( $aRow);
      $oDataset->next();
    }

  }

  function createFiles ( $oData, $pmFilename  ) {
        if (! file_exists($pmFilename))
            throw ( new Exception ( 'Unable to read uploaded .pm file, please check permissions. ') );

        if (! filesize($pmFilename) >= 9 )
            throw ( new Exception ( 'Uploaded .pm file is corrupted, please check the file before continue. '));

        $fp = fopen( $pmFilename, "rb");
        $fsData = intval( fread ( $fp, 9));    //reading the size of $oData
        $contents  = fread( $fp, $fsData );    //reading string $oData

        $path = PATH_DYNAFORM . $oData->process['PRO_UID'] . PATH_SEP;
        if ( !is_dir($path) ) {
            G::verifyPath($path, true);
        }
        
        $sIdentifier = 1;
        while ( !feof ( $fp ) && is_numeric( $sIdentifier )  ) {
          $sIdentifier = fread ( $fp, 9);      //reading the size of $filename
          if ( is_numeric( $sIdentifier ) ) {
            $fsXmlGuid    = intval( $sIdentifier );      //reading the size of $filename
            if ( $fsXmlGuid > 0 )
                $XmlGuid    = fread( $fp, $fsXmlGuid );    //reading string $XmlGuid
            $fsXmlContent = intval( fread ( $fp, 9));      //reading the size of $XmlContent
            if ( $fsXmlContent > 0 ) {
              $newXmlGuid = $oData->dynaformFiles[ $XmlGuid ];
              
              //print "$sFileName <br>";
              $XmlContent   = fread( $fp, $fsXmlContent );    //reading string $XmlContent
              
              #here we verify if is adynaform or a html
              $ext = (substr(trim($XmlContent),0,5) == '<?xml')?'.xml':'.html';
              
              $sFileName = $path . $newXmlGuid . $ext;
              $bytesSaved = @file_put_contents ( $sFileName, $XmlContent );
              if ( $bytesSaved != $fsXmlContent )
              throw ( new Exception ('Error writing dynaform file in directory : ' . $path ) );  
            }
          }
        }
 
        //now mailTemplates and public files       
        $pathPublic  = PATH_DATA_SITE . 'public' . PATH_SEP . $oData->process['PRO_UID'] . PATH_SEP;
        $pathMailTem = PATH_DATA_SITE . 'mailTemplates' . PATH_SEP . $oData->process['PRO_UID'] . PATH_SEP;
        G::mk_dir ( $pathPublic );
        G::mk_dir ( $pathMailTem );

        if ( $sIdentifier == 'MAILTEMPL' ) {
          $sIdentifier = 1;
          while ( !feof ( $fp ) && is_numeric( $sIdentifier )  ) {
            $sIdentifier = fread ( $fp, 9);      //reading the size of $filename
            if ( is_numeric( $sIdentifier ) ) {
              $fsFileName    = intval( $sIdentifier );      //reading the size of $filename
              if ( $fsFileName > 0 )
                  $sFileName    = fread( $fp, $fsFileName );    //reading filename string 
              $fsContent = intval( fread ( $fp, 9));      //reading the size of $Content
              if ( $fsContent > 0 ) {
                $fileContent   = fread( $fp, $fsContent );    //reading string $XmlContent
                $newFileName = $pathMailTem . $sFileName;
                $bytesSaved = @file_put_contents ( $newFileName, $fileContent );
                if ( $bytesSaved != $fsContent )
                throw ( new Exception ('Error writing MailTemplate file in directory : ' . $pathMailTem ) );  
              }
            }
          }
        }

        if ( $sIdentifier == 'PUBLIC   ' ) {
          $sIdentifier = 1;
          while ( !feof ( $fp ) && is_numeric( $sIdentifier )  ) {
            $sIdentifier = fread ( $fp, 9);      //reading the size of $filename
            if ( is_numeric( $sIdentifier ) ) {
              $fsFileName    = intval( $sIdentifier );      //reading the size of $filename
              if ( $fsFileName > 0 )
                  $sFileName    = fread( $fp, $fsFileName );    //reading filename string 
              $fsContent = intval( fread ( $fp, 9));      //reading the size of $Content
              if ( $fsContent > 0 ) {
                $fileContent   = fread( $fp, $fsContent );    //reading string $XmlContent
                $newFileName = $pathPublic . $sFileName;
                $bytesSaved = @file_put_contents ( $newFileName, $fileContent );
                if ( $bytesSaved != $fsContent )
                throw ( new Exception ('Error writing Public file in directory : ' . $pathPublic ) );  
              }
            }
          }
        }
        
        fclose ( $fp);

        return true;

    }


  /*
  * this function remove all Process except the PROCESS ROW
  * @param string $sProUid
  * @return boolean
  */
  function removeProcessRows ($sProUid )  {
    try {
  	  //Instance all classes necesaries
  	  $oProcess         = new Process();
  	  $oDynaform        = new Dynaform();
  	  $oInputDocument   = new InputDocument();
  	  $oOutputDocument  = new OutputDocument();
  	  $oTrigger         = new Triggers();
  	  $oStepTrigger     = new StepTrigger();
  	  $oRoute           = new Route();
  	  $oStep            = new Step();
  	  $oSwimlaneElement = new SwimlanesElements();
  	  $oConnection      = new DbSource();

  	  //Delete the tasks of process
  	  $oCriteria = new Criteria('workflow');
  	  $oCriteria->add(TaskPeer::PRO_UID, $sProUid);
  	  $oDataset = TaskPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $oTask = new Task();
      while ($aRow = $oDataset->getRow()) {
        $oTask->remove( $aRow['TAS_UID']);
      	$oDataset->next();
      }

    //Delete the dynaforms of process
    $oCriteria = new Criteria('workflow');
    $oCriteria->add(DynaformPeer::PRO_UID, $sProUid);
    $oDataset = DynaformPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    while ($aRow = $oDataset->getRow()) {
    	$sWildcard = PATH_DYNAFORM . $aRow['PRO_UID'] . PATH_SEP . $aRow['DYN_UID'] . '_tmp*';
    	foreach( glob($sWildcard) as $fn ) {
        @unlink($fn);
      }
    	$sWildcard = PATH_DYNAFORM . $aRow['PRO_UID'] . PATH_SEP . $aRow['DYN_UID'] . '.*';
    	foreach( glob($sWildcard) as $fn ) {
        @unlink($fn);
      }
    	$oDynaform->remove( $aRow['DYN_UID'] );
    	$oDataset->next();
    }

    //Delete the input documents of process
  	$oCriteria = new Criteria('workflow');
  	$oCriteria->add(InputDocumentPeer::PRO_UID, $sProUid);
  	$oDataset = InputDocumentPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    while ($aRow = $oDataset->getRow()) {
    	$oInputDocument->remove($aRow['INP_DOC_UID']);
    	$oDataset->next();
    }

    //Delete the output documents of process
		$oCriteria = new Criteria('workflow');
	  $oCriteria->add(OutputDocumentPeer::PRO_UID, $sProUid);
	  $oDataset = OutputDocumentPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    while ($aRow = $oDataset->getRow()) {
    	$oOutputDocument->remove($aRow['OUT_DOC_UID']);
    	$oDataset->next();
    }

    //Delete the steps
		$oCriteria = new Criteria('workflow');
	  $oCriteria->add(StepPeer::PRO_UID, $sProUid);
	  $oDataset = StepPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    while ($aRow = $oDataset->getRow()) {
    	//Delete the steptrigger of process
    	$oCriteria = new Criteria('workflow');
	  	$oCriteria->add(StepTriggerPeer::STEP_UID, $aRow['STEP_UID']);
	  	$oDataseti = StepTriggerPeer::doSelectRS($oCriteria);
    	$oDataseti->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    	$oDataseti->next();
    	while ($aRowi = $oDataseti->getRow()) {
    		$oStepTrigger->remove($aRowi['STEP_UID'], $aRowi['TAS_UID'], $aRowi['TRI_UID'], $aRowi['ST_TYPE']);
    		$oDataseti->next();
    	}
    	
    	$oStep->remove($aRow['STEP_UID']);
    	$oDataset->next();
    }

		//Delete the StepSupervisor
    $oCriteria = new Criteria('workflow');
    $oCriteria->add(StepSupervisorPeer::PRO_UID, $sProUid);
    $oDataset = StepSupervisorPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    while ($aRow = $oDataset->getRow()) {
        $oStep->remove($aRow['STEP_UID']);
        $oDataset->next();
    }

    //Delete the triggers of process
		$oCriteria = new Criteria('workflow');
	  $oCriteria->add(TriggersPeer::PRO_UID, $sProUid);
	  $oDataset = TriggersPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    while ($aRow = $oDataset->getRow()) {
    	$oTrigger->remove($aRow['TRI_UID']);
    	$oDataset->next();
    }

    //Delete the routes of process
		$oCriteria = new Criteria('workflow');
	  $oCriteria->add(RoutePeer::PRO_UID, $sProUid);
	  $oDataset = RoutePeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    while ($aRow = $oDataset->getRow()) {
    	$oRoute->remove($aRow['ROU_UID']);
    	$oDataset->next();
    }

    //Delete the swimlanes elements of process
		$oCriteria = new Criteria('workflow');
	  $oCriteria->add(SwimlanesElementsPeer::PRO_UID, $sProUid);
	  $oDataset = SwimlanesElementsPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    while ($aRow = $oDataset->getRow()) {
    	$oSwimlaneElement->remove($aRow['SWI_UID']);
    	$oDataset->next();
    }

    //Delete the DB connections of process
		$oCriteria = new Criteria('workflow');
	  $oCriteria->add(DbSourcePeer::PRO_UID, $sProUid);
	  $oDataset = DbSourcePeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    while ($aRow = $oDataset->getRow()) {
    	$oConnection->remove($aRow['DBS_UID']);
    	$oDataset->next();
    }
       
 		return true;
  	}
  	catch ( Exception $oError) {
    	throw($oError);
    }
  }

  /*
  * this function creates a new Process, defined in the object $oData
  * @param string $sProUid
  * @return boolean
  */
  function createProcessFromData ($oData, $pmFilename ) {
    $this->createProcessRow ($oData->process );
    $this->createTaskRows ($oData->tasks );        
    $this->createRouteRows ($oData->routes );
    $this->createLaneRows ($oData->lanes );
    $this->createDynaformRows ($oData->dynaforms );
    $this->createInputRows ($oData->inputs );
    $this->createOutputRows ($oData->outputs );
    $this->createStepRows ($oData->steps );
    $this->createStepSupervisorRows($oData->stepSupervisor);
    $this->createTriggerRows ($oData->triggers);
    $this->createStepTriggerRows ($oData->steptriggers);
    $this->createTaskUserRows ($oData->taskusers);
    $this->createGroupRow ($oData->groupwfs );
    $this->createDBConnectionsRows($oData->dbconnections);
    $this->createReportTables($oData->reportTables, $oData->reportTablesVars);
    $this->createObjectPermissions( $oData->objectPermissions );

    //and finally create the files, dynaforms (xml and html), emailTemplates and Public files    
    $this->createFiles ( $oData, $pmFilename  );
 }

  /*
  * this function creates a new Process, defined in the object $oData
  * @param string $sProUid
  * @return boolean
  */
  function updateProcessFromData ($oData, $pmFilename ) {  	
    $this->updateProcessRow ($oData->process );
    $this->removeProcessRows ($oData->process['PRO_UID'] );    
    $this->createTaskRows ($oData->tasks );        
    $this->createRouteRows ($oData->routes );
    $this->createLaneRows ($oData->lanes );
    $this->createDynaformRows ($oData->dynaforms );
    $this->createInputRows ($oData->inputs );
    $this->createOutputRows ($oData->outputs );
    $this->createStepRows ($oData->steps );
    $this->createStepSupervisorRows($oData->stepSupervisor);
    $this->createTriggerRows ($oData->triggers);
    $this->createStepTriggerRows ($oData->steptriggers);
    $this->createTaskUserRows ($oData->taskusers);
    $this->createGroupRow ($oData->groupwfs );
    $this->createDBConnectionsRows($oData->dbconnections);
    $this->updateReportTables($oData->reportTables, $oData->reportTablesVars);
    $this->createFiles ( $oData, $pmFilename  );    
    $this->createObjectPermissions( $oData->objectPermissions );
 }

 function getStartingTaskForUser ($sProUid, $sUsrUid ){
    $oTask = new Tasks( );

    return $oTask->getStartingTaskForUser( $sProUid, $sUsrUid );
  }
}


class ObjectDocument{
	public $type;
	public $name;
	public $data;
	public $origin;
	
	function __construct(){
		$this->type = '';
		$this->name = '';
		$this->data = '';
		$this->origin = '';
    }
}

class ObjectCellection{
	public $num;
	public $swapc;
	public $objects;
	
	function __construct (){
		$this->objects = Array();
		$this->num = 0;
		$this->swapc = $this->num;
		array_push($this->objects, 'void');
    }
	
	function add($name, $type, $data, $origin){
		$o = new ObjectDocument();
		$o->name = $name;
		$o->type = $type;
		$o->data = $data;
		$o->origin = $origin;
		
		$this->num++;
		array_push($this->objects, $o);
		$this->swapc = $this->num;
    }
	
	function get(){
		if($this->swapc > 0) {
			$e = $this->objects[$this->swapc];
			$this->swapc--;
			return $e;
        } else {
			$this->swapc = $this->num;
			return false;
		}
	}
}