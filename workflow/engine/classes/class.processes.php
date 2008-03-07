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

  function getStepTriggerRows ($sProUid ){  //SwimlanesElements
  	try {
  	  $aStepTrigger   = array();
  	  $oCriteria = new Criteria('workflow');
      $oCriteria->add(StepTriggerPeer::PRO_UID,  $sProUid);
      $oDataset = StepTriggerPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
      	$oStepTrigger = new StepTrigger();
      	$aStepTrigger[] = $oStepTrigger->Load( $aRow['StepTrigger_UID'] );
      	$oDataset->next();
      }
      return $aStepTrigger;
    }
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

  function createStepTriggerRows ($aStepTrigger ){  
  	foreach ( $aStepTrigger as $key => $row ) {
      $oStepTrigger = new StepTriggerdocument();
      //unset ($row['TAS_UID']);
      $res = $oStepTrigger->create($row);
  	}
  	return;
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
    //$oData->steptriggers = $this->getStepTriggerRows( $sProUid );
    
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
    //$this->createTriggerRows ($oData->triggers);
    
 }
  

}