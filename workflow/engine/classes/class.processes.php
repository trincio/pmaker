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
//require_once 'classes/model/Task.php';
require_once 'classes/model/Route.php';
require_once 'classes/model/SwimlanesElements.php';
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
      	$aLanes[] = $aRow;
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
  
  function getRouteRows ($sProUid ){
    $oTask = new Tasks( );
    return $oTask->getAllRoutes( $sProUid );
  }
  
  function getLaneRows ($sProUid ){  //SwimlanesElements
    return $this->getAllLanes( $sProUid );
  }
  
  /*
  * change Status of any Process
  * @param string $sProUid
  * @return boolean
  */
  function serializeProcess ( $sProUid = '') {
    $oProcess = new Process( );
    $oData->process = $this->getProcessRow( $sProUid );
    $oData->tasks   = $this->getTaskRows( $sProUid );
    $oData->routes  = $this->getRouteRows( $sProUid );
    $oData->lanes   = $this->getLaneRows( $sProUid );
    
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
 
}