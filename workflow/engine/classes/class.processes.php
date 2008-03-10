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
require_once 'classes/model/SwimlanesElements.php';
require_once 'classes/model/InputDocument.php';
require_once 'classes/model/OutputDocument.php';
require_once 'classes/model/Step.php';
require_once 'classes/model/StepTrigger.php';
require_once 'classes/model/Dynaform.php';
require_once 'classes/model/Triggers.php';
G::LoadClass('tasks');
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
  }

  function getProcessRow ($sProUid ){
    $oProcess = new Process( );
    return $oProcess->Load( $sProUid );
  }
  
  function createProcessRow ($row ){
    $oProcess = new Process( );
    return $oProcess->createRow($row);
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
  
  function getRouteRows ($sProUid ){
    $oTask = new Tasks( );
    return $oTask->getAllRoutes( $sProUid );
  }
  
  function createRouteRows ($aRoutes ){
    $oTask = new Tasks( );
    return $oTask->createRouteRows( $aRoutes );
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
  	    $newGuid = $map[ $val['STEP_UID'] ];
	      $oData->steptriggers[$key]['STEP_UID'] = $newGuid;
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

  function createDynaformRows ($aDynaform ){  
  	foreach ( $aDynaform as $key => $row ) {
      $oDynaform = new Dynaform();
      //unset ($row['TAS_UID']);
      $res = $oDynaform->create($row);
  	}
  	return;
  }


  function createStepTriggerRows ($aStepTrigger ){  
  	foreach ( $aStepTrigger as $key => $row ) {
      $oStepTrigger = new StepTrigger();
      //unset ($row['TAS_UID']);
      $res = $oStepTrigger->create($row);
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
    $oData->steptriggers = $this->getStepTriggerRows( $oData->tasks );
    //krumo ($oData);
    //$oJSON = new Services_JSON();
    //krumo ( $oJSON->encode($oData) );
    //return $oJSON->encode($oData);
    return serialize($oData);
  }
  
  function saveSerializedProcess ( $proFields ) {
    //$oJSON = new Services_JSON();
    //$data = $oJSON->decode($proFields);
    //$sProUid = $data->process->PRO_UID;
    $data = unserialize ($proFields);
    $sProUid = $data->process['PRO_UID'];
    $path = PATH_DOCUMENT . 'output' . PATH_SEP;
    if ( !is_dir($path) ) {
      	G::verifyPath($path, true);
    }
    $filename = $path . $sProUid . '.pm';
    $filenameOnly = $sProUid . '.pm';
    $bytesSaved = file_put_contents  ( $filename  , $proFields  );
    $filenameLink = 'processes_DownloadFile?p=' . $sProUid . '&r=' . rand(100,1000);
    
    $result['PRO_UID']         = $data->process['PRO_UID'];
    $result['PRO_TITLE']       = $data->process['PRO_TITLE'];
    $result['PRO_DESCRIPTION'] = $data->process['PRO_DESCRIPTION'];
    $result['SIZE']            = $bytesSaved;
    $result['FILENAME']        = $filenameOnly;
    $result['FILENAME_LINK']   = $filenameLink;
    return $result;
  }
  
  /*
  * this function creates a new Process, defined in the object $oData
  * @param string $sProUid
  * @return boolean
  */
  function createProcessFromData ($oData) {
    $this->createProcessRow ($oData->process );
    $this->createTaskRows ($oData->tasks );
    $this->createRouteRows ($oData->routes );
    $this->createLaneRows ($oData->lanes );
    $this->createDynaformRows ($oData->dynaforms );
    $this->createInputRows ($oData->inputs );
    $this->createOutputRows ($oData->outputs );
    $this->createStepRows ($oData->steps );
    $this->createTriggerRows ($oData->triggers);
    $this->createStepTriggerRows ($oData->steptriggers);
 }
  

}