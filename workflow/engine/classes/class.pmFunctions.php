<?php
/**
 * class.pmFunctions.php
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
////////////////////////////////////////////////////
// PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/*
   */

function getCurrentDate()
{
	return G::CurDate('Y-m-d');
}

function getCurrentTime()
{
	return G::CurDate('H:i:s');
}

function userInfo($user_uid)
{
	try {
		require_once 'classes/model/Users.php';
    	$oUser = new Users();
		return $oUser->getAllInformation($user_uid);
	}
	catch (Exception $oException) {
		throw $oException;
	}
}

function upperCase($sText)
{
	return G::toUpper($sText);
}

function lowerCase($sText)
{
	return G::toLower($sText);
}

function capitalize($sText)
{
	return G::capitalizeWords($sText);
}

function formatDate($date, $format='', $lang='en')
{
	if( !isset($date) or $date == '') {
		throw new Exception('function:formatDate::Bad param');
	}
	try{
    	return G::getformatedDate($date, $format, $lang);
	} catch (Exception $oException) {
    	throw $oException;
	}
}

function literalDate($date, $lang = 'en')
{
    if( !isset($date) or $date == '' ) {
		throw new Exception('function:formatDate::Bad param');
	}
	try{
		switch($lang)
		{
			case 'en': $ret = G::getformatedDate($date, 'M d,yyyy', $lang); break;
			case 'es': $ret = G::getformatedDate($date, 'd de M de yyyy', $lang); break;
		}
    	return $ret;
	} catch (Exception $oException) {
    	throw $oException;
  	}
}

function pauseCase($sApplicationUID = '', $iDelegation = 0, $sUserUID = '', $sUnpauseDate = null) {//var_dump($sApplicationUID, $iDelegation, $sUserUID, $sUnpauseDate);die(':|');
  try {
    if ($sApplicationUID == '') {
      throw new Exception('The application UID cannot be empty!');
    }
    if ($iDelegation == 0) {
      throw new Exception('The delegation index cannot be 0!');
    }
    if ($sUserUID == '') {
      throw new Exception('The user UID cannot be empty!');
    }
    G::LoadClass('case');
    $oCase = new Cases();
    $oCase->pauseCase($sApplicationUID, $iDelegation, $sUserUID, $sUnpauseDate);
  }
  catch (Exception $oException) {
    throw $oException;
  }
}

function executeQuery($SqlStatement, $DBConnectionUID = 'workflow')
{
	try {
		$con = Propel::getConnection($DBConnectionUID);
		$con->begin();
		$rs = $con->executeQuery($SqlStatement);
		$con->commit();

		switch(true) {
			case eregi("SELECT", $SqlStatement):
			case eregi("EXEC*", $SqlStatement):
				$result = Array();
				$i=1;
				while ($rs->next()) {
					$result[$i++] = $rs->getRow();
				}
			break;
			case eregi("INSERT*", $SqlStatement):
				//$result = $lastId->getId();
				$result = 1;
			break;
			case eregi("UPDATE*", $SqlStatement):
				 $result =  $con->getUpdateCount();
			break;
			case eregi("DELETE*", $SqlStatement):
				$result =  $con->getUpdateCount();
			break;
		}
		return $result;
	} catch (SQLException $sqle) {
		$con->rollback();
		throw $sqle;
	}
}

function orderGrid($dataM, $field, $ord = 'ASC')
{
	if(!is_array($dataM) or !isset($field) or $field==''){
		throw new Exception('function:orderGrid Error!, bad parameters found!');
	}
	for($i=1; $i <= count($dataM)-1; $i++){
		for($j=$i+1; $j <= count($dataM); $j++){
			if(strtoupper($ord) == 'ASC'){
				if(strtolower($dataM[$j][$field]) < strtolower($dataM[$i][$field])){
					$swap  = $dataM[$i];
					$dataM[$i] = $dataM[$j];
					$dataM[$j] = $swap;
				}
			} else {
				if($dataM[$j][$field] > $dataM[$i][$field]){
					$swap  = $dataM[$i];
					$dataM[$i] = $dataM[$j];
					$dataM[$j] = $swap;
				}
			}
		}
	}
	return $dataM;
}

function evaluateFunction($aGrid, $sExpresion)
{
	$sExpresion = str_replace('Array','$this->aFields', $sExpresion);
	$sExpresion .= ';';
	G::LoadClass('pmScript');
	$pmScript = new PMScript();
	$pmScript->setScript($sExpresion);

	for($i=1; $i<=count($aGrid); $i++){
		$aFields = $aGrid[$i];

		$pmScript->setFields($aFields);

		$pmScript->execute();

		$aGrid[$i] = $pmScript->aFields;
	}
	return $aGrid;
}

/** Web Services Functions **/

function WSLogin($user, $pass, $endpoint='')
{
	$client = wSOpen(true);
	$params = array('userid'=>$user, 'password'=>$pass);
	$result = $client->__SoapCall('login', array($params));

	if($result->status_code == 0){
		if($endpoint != ''){
			$_SESSION['WS_END_POINT'] = $endpoint;
		}
		return $_SESSION['WS_SESSION_ID'] = $result->message;
	} else {
		unset($_SESSION['WS_SESSION_ID']);
		$wp = (trim($pass) != "")?'YES':'NO';
		throw new Exception("WSAccess denied! for user $user with password $wp");
	}
}

function WSOpen($force=false)
{
	if(isset($_SESSION['WS_SESSION_ID']) || $force){
		if( !isset ($_SESSION['WS_END_POINT']) ){
			$defaultEndpoint = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/sys'.SYS_SYS.'/en/green/services/wsdl';
		}
		$endpoint = isset( $_SESSION['WS_END_POINT'] ) ? $_SESSION['WS_END_POINT'] : $defaultEndpoint;
		$client = new SoapClient( $endpoint );
		return $client;
	} else {
		throw new Exception('WS session is not open');
	}
}

function WSTaskCase($caseId)
{
	$client = WSOpen();

	$sessionId = $_SESSION['WS_SESSION_ID'];

	$params = array('sessionId'=>$sessionId, 'caseId'=>$caseId);
	$result = $client->__soapCall('taskCase', array($params));

	$i = 1;
	if(isset ($result->taskCases)){
		foreach ( $result->taskCases as $key=> $item) {
			if ( isset ($item->item) ){
				foreach ( $item->item as $index=> $val ) {
					if ( $val->key == 'guid' ) $guid = $val->value;
					if ( $val->key == 'name' ) $name = $val->value;
				}
			} else {
				foreach ( $item as $index=> $val ) {
					if ( $val->key == 'guid' ) $guid = $val->value;
					if ( $val->key == 'name' ) $name = $val->value;
				}
			}

			$rows[$i++] = array ( 'guid' => $guid, 'name' => $name );
		}
	}
	return $rows;
}

function WSTaskList()
{
	$client = WSOpen();

	$sessionId = $_SESSION['WS_SESSION_ID'];
	$params = array('sessionId'=>$sessionId );
	$result = $client->__SoapCall('TaskList', array($params));

	$i = 1;
	if(isset ($result->tasks)){
		foreach ( $result->tasks as $key=> $item) {
			if ( isset ($item->item) ){
				foreach ( $item->item as $index=> $val ) {
					if ( $val->key == 'guid' ) $guid = $val->value;
					if ( $val->key == 'name' ) $name = $val->value;
				}
			} else {
				foreach ( $item as $index=> $val ) {
					if ( $val->key == 'guid' ) $guid = $val->value;
					if ( $val->key == 'name' ) $name = $val->value;
				}
			}

			$rows[$i++] = array ( 'guid' => $guid, 'name' => $name );
		}
	}
	return $rows;
}

function WSUserList()
{
	$client = WSOpen();

	$sessionId = $_SESSION['WS_SESSION_ID'];
	$params = array('sessionId'=>$sessionId );
    $result = $client->__SoapCall('UserList', array($params));

	$i = 1;
	if(isset ($result->users)){
		foreach ( $result->users as $key=> $item) {
			if ( isset ($item->item) ){
				foreach ( $item->item as $index=> $val ) {
					if ( $val->key == 'guid' ) $guid = $val->value;
					if ( $val->key == 'name' ) $name = $val->value;
				}
			} else {
				foreach ( $item as $index=> $val ) {
					if ( $val->key == 'guid' ) $guid = $val->value;
					if ( $val->key == 'name' ) $name = $val->value;
				}
			}

			$rows[$i++] = array ( 'guid' => $guid, 'name' => $name );
		}
	}
	return $rows;
}

function WSGroupList()
{
	$client = WSOpen();

	$sessionId = $_SESSION['WS_SESSION_ID'];
	$params = array('sessionId'=>$sessionId );
    $result = $client->__SoapCall('GroupList', array($params));

	$i = 1;
	if(isset ($result->groups)){
		foreach ( $result->groups as $key=> $item) {
			if ( isset ($item->item) ){
				foreach ( $item->item as $index=> $val ) {
					if ( $val->key == 'guid' ) $guid = $val->value;
					if ( $val->key == 'name' ) $name = $val->value;
				}
			} else {
				foreach ( $item as $index=> $val ) {
					if ( $val->key == 'guid' ) $guid = $val->value;
					if ( $val->key == 'name' ) $name = $val->value;
				}
			}

			$rows[$i++] = array ( 'guid' => $guid, 'name' => $name );
		}
	}
	return $rows;
}


function WSRoleList()
{
	$client = WSOpen();

	$sessionId = $_SESSION['WS_SESSION_ID'];
	$params = array('sessionId'=>$sessionId );
	$result = $client->__SoapCall('RoleList', array($params));
	$i = 1;
	if(isset ($result->roles)){
		foreach ( $result->roles as $key=> $item) {
			if ( isset ($item->item) ){
				foreach ( $item->item as $index=> $val ) {
					if ( $val->key == 'guid' ) $guid = $val->value;
					if ( $val->key == 'name' ) $name = $val->value;
				}
			} else {
				foreach ( $item as $index=> $val ) {
					if ( $val->key == 'guid' ) $guid = $val->value;
					if ( $val->key == 'name' ) $name = $val->value;
				}
			}

			$rows[$i++] = array ( 'guid' => $guid, 'name' => $name );
		}
	}
	return $rows;
}

function WSCaseList()
{
	$client = WSOpen();

	$sessionId = $_SESSION['WS_SESSION_ID'];
	$params = array('sessionId'=>$sessionId );
	$result = $client->__SoapCall('CaseList', array($params));

	$i = 1;
	if(isset ($result->cases)){
		foreach ( $result->cases as $key=> $item) {
			if ( isset ($item->item) ){
				foreach ( $item->item as $index=> $val ) {
					if ( $val->key == 'guid' ) $guid = $val->value;
					if ( $val->key == 'name' ) $name = $val->value;
				}
			} else {
				foreach ( $item as $index=> $val ) {
					if ( $val->key == 'guid' ) $guid = $val->value;
					if ( $val->key == 'name' ) $name = $val->value;
				}
			}

			$rows[$i++] = array ( 'guid' => $guid, 'name' => $name );
		}
	}

	return $rows;
}

function WSProcessList()
{
	$client = WSOpen();

	$sessionId = $_SESSION['WS_SESSION_ID'];
	$params = array('sessionId'=>$sessionId );
	$result = $client->__SoapCall('ProcessList', array($params));

	$i = 1;
	if(isset ($result->processes)){
		foreach ( $result->processes as $key=> $item) {
			if ( isset ($item->item) ){
				foreach ( $item->item as $index=> $val ) {
					if ( $val->key == 'guid' ) $guid = $val->value;
					if ( $val->key == 'name' ) $name = $val->value;
				}
			} else {
				foreach ( $item as $index=> $val ) {
					if ( $val->key == 'guid' ) $guid = $val->value;
					if ( $val->key == 'name' ) $name = $val->value;
				}
			}

			$rows[$i++] = array ( 'guid' => $guid, 'name' => $name );
		}
	}
	return $rows;
}

//private function to get current email configuration
function getEmailConfiguration () {
  require_once 'classes/model/Configuration.php';
  $oConfiguration = new Configuration();
  $sDelimiter     = DBAdapter::getStringDelimiter();
  $oCriteria      = new Criteria('workflow');
  $oCriteria->add(ConfigurationPeer::CFG_UID, 'Emails');
  $oCriteria->add(ConfigurationPeer::OBJ_UID, '');
  $oCriteria->add(ConfigurationPeer::PRO_UID, '');
  $oCriteria->add(ConfigurationPeer::USR_UID, '');
  $oCriteria->add(ConfigurationPeer::APP_UID, '');

  if (ConfigurationPeer::doCount($oCriteria) == 0) {
    $oConfiguration->create(array('CFG_UID' => 'Emails', 'OBJ_UID' => '', 'CFG_VALUE' => '', 'PRO_UID' => '', 'USR_UID' => '', 'APP_UID' => ''));
    $aFields = array();
  }
  else {
    $aFields = $oConfiguration->load('Emails', '', '', '', '');
    if ($aFields['CFG_VALUE'] != '') {
      $aFields = unserialize($aFields['CFG_VALUE']);
    }
    else {
      $aFields = array();
    }
  }

  return $aFields;
}

function PMFSendMessage($caseId, $sFrom, $sTo, $sCc, $sBcc, $sSubject, $sTemplate, $aFields = array()) {
	G::LoadClass('wsBase');
	$ws = new wsBase ();
	$result = $ws->sendMessage($caseId, $sFrom, $sTo, $sCc, $sBcc, $sSubject, $sTemplate, $aFields);

	if ( $result->status_code == 0){
		return 1;
	} else {
		return 0;
	}
}


function WSSendVariables($caseId, $name1, $value1, $name2, $value2)
{
	$client = WSOpen();
	$sessionId = $_SESSION['WS_SESSION_ID'];

	$variables[1]->name  = $name1;
	$variables[1]->value = $value1;
	$variables[2]->name  = $name2;
	$variables[2]->value = $value2;
	$params = array('sessionId'=>$sessionId, 'caseId'=>$caseId, 'variables'=>$variables);
	$result = $client->__SoapCall('SendVariables', array($params));

	$fields['status_code'] = $result->status_code;
	$fields['message']     = $result->message;
	$fields['time_stamp']  = $result->timestamp;
	return $fields;
}

function WSDerivateCase($caseId, $delIndex)
{
	$client = WSOpen();
	$sessionId = $_SESSION['WS_SESSION_ID'];

	$params = array('sessionId'=>$sessionId, 'caseId'=>$caseId, 'delIndex'=>$delIndex );
	$result = $client->__SoapCall('DerivateCase', array($params));

	$fields['status_code'] = $result->status_code;
	$fields['message']     = $result->message;
	$fields['time_stamp']  = $result->timestamp;
	return $fields;
}

function WSNewCaseImpersonate($processId, $userId, $name1, $value1, $name2, $value2)
{
	$client = WSOpen();
	$sessionId = $_SESSION['WS_SESSION_ID'];

	$variables[1]->name  = $name1;
	$variables[1]->value = $value1;
	$variables[2]->name  = $name2;
	$variables[2]->value = $value2;

	$params = array('sessionId'=>$sessionId, 'processId'=>$processId, 'userId'=>$userId, 'variables'=>$variables );
	$result = $client->__SoapCall('NewCaseImpersonate', array($params));

	$fields['status_code'] = $result->status_code;
	$fields['message']     = $result->message;
	$fields['time_stamp']  = $result->timestamp;
	return $fields;
}

function WSNewCase($processId, $taskId, $name1, $value1, $name2, $value2)
{
	$client = WSOpen();
	$sessionId = $_SESSION['WS_SESSION_ID'];

	$variables[1]->name  = $name1;
	$variables[1]->value = $value1;
	$variables[2]->name  = $name2;
	$variables[2]->value = $value2;

	$params = array('sessionId'=>$sessionId, 'processId'=>$processId, 'taskId'=>$taskId, 'variables'=>$variables );
	$result = $client->__SoapCall('NewCase', array($params));

	$fields['status_code'] = $result->status_code;
	$fields['message']     = $result->message;
	$fields['time_stamp']  = $result->timestamp;
	return $fields;
}

function WSAssignUserToGroup($userId, $groupId)
{
	$client = WSOpen();
	$sessionId = $_SESSION['WS_SESSION_ID'];

	$params = array('sessionId'=>$sessionId, 'userId'=>$userId, 'groupId'=>$groupId);
	$result = $client->__SoapCall('AssignUserToGroup', array($params));

	$fields['status_code'] = $result->status_code;
	$fields['message']     = $result->message;
	$fields['time_stamp']  = $result->timestamp;
	return $fields;
}

function WSCreateUser($userId, $password, $firstname, $lastname, $email, $role)
{
	$client = WSOpen();
	$sessionId = $_SESSION['WS_SESSION_ID'];
	$params = array('sessionId'=>$sessionId, 'userId'=>$userId, 'firstname'=>$firstname, 'lastname'=>$lastname, 'email'=>$email, 'role'=>$role, 'password'=>$password);
	$result = $client->__SoapCall('CreateUser', array($params));

	$fields['status_code'] = $result->status_code;
	$fields['message']     = $result->message;
	$fields['time_stamp']  = $result->timestamp;
	return $fields;
}

function WSGetSession()
{
	if(isset($_SESSION['WS_SESSION_ID'])){
		return $_SESSION['WS_SESSION_ID'];
	} else {
		throw new Exception("SW session is not opem!");
	}
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/** Local Services Functions **/

function PMFTaskCase($caseId) #its test was successfull
{
	G::LoadClass('wsBase');
	$ws = new wsBase ();
    $result = $ws->taskCase($caseId);
	$rows = Array();
	$i = 1;
	if(isset ($result)){
		foreach ( $result as $item) {
			$rows[$i++] = $item;
		}
	}
	return $rows;
}

function PMFTaskList($userId) #its test was successfull
{
	G::LoadClass('wsBase');
	$ws = new wsBase ();
    $result = $ws->taskList($userId);
	$rows = Array();
	$i = 1;
	if(isset ($result)){
		foreach ( $result as $item) {
			$rows[$i++] = $item;
		}
	}
	return $rows;
}

function PMFUserList() #its test was successfull
{
	G::LoadClass('wsBase');
	$ws = new wsBase ();
    $result = $ws->userList();
	$rows = Array();
	$i = 1;
	if(isset ($result)){
		foreach ( $result as $item) {
			$rows[$i++] = $item;
		}
	}
	return $rows;
}

function PMFGroupList() #its test was successfull
{
	G::LoadClass('wsBase');
	$ws = new wsBase ();
    $result = $ws->groupList();
	$rows = Array();
	$i = 1;
	if(isset ($result)){
		foreach ( $result as $item) {
			$rows[$i++] = $item;
		}
	}
	return $rows;
}


function PMFRoleList() #its test was successfull
{
	G::LoadClass('wsBase');
	$ws = new wsBase ();
    $result = $ws->roleList();
	$rows = Array();
	$i = 1;
	if(isset ($result)){
		foreach ( $result as $item) {
			$rows[$i++] = $item;
		}
	}
	return $rows;
}

function PMFCaseList($userId) #its test was successfull
{
	G::LoadClass('wsBase');
	$ws = new wsBase ();
    $result = $ws->caseList($userId);
	$rows = Array();
	$i = 1;
	if(isset ($result)){
		foreach ( $result as $item) {
			$rows[$i++] = $item;
		}
	}
	return $rows;
}

function PMFProcessList() #its test was successfull
{
	G::LoadClass('wsBase');
	$ws = new wsBase ();
    $result = $ws->processList();
	$rows = Array();
	$i = 1;
	if(isset ($result)){
		foreach ( $result as $item) {
			$rows[$i++] = $item;
		}
	}
	return $rows;
}


function PMFSendVariables($caseId, $variables)
{
	G::LoadClass('wsBase');
	$ws = new wsBase ();

    $result = $ws->sendVariables($caseId, $variables);
	if($result->status_code == 0){
		return 1;
	} else {
		return 0;
	}
}

function PMFDerivateCase($caseId, $delIndex, $bExecuteTriggersBeforeAssignment = false)
{
	G::LoadClass('wsBase');
	$ws = new wsBase ();
	$result = $ws->derivateCase($_SESSION['USER_LOGGED'], $caseId, $delIndex, $bExecuteTriggersBeforeAssignment);//var_dump($result);die;
	if (isset($result->status_code)) {
	  return $result->status_code;
	}
	else {
	  return 0;
	}
	if($result->status_code == 0){
		return 1;
	} else {
		return 0;
	}
}

function PMFNewCaseImpersonate($processId, $userId, $variables)
{
	G::LoadClass('wsBase');
	$ws = new wsBase ();
	$result = $ws->newCaseImpersonate($processId, $userId, $variables);

	if($result->status_code == 0){
		return 1;
	} else {
		return 0;
	}
}

function PMFNewCase($processId, $userId, $taskId, $variables)
{
	G::LoadClass('wsBase');
	$ws = new wsBase ();

	$result = $ws->newCase($processId, $userId,$taskId, $variables);

	if($result->status_code == 0){
		return $result->caseId;
	} else {
		return 0;
	}
}

function PMFAssignUserToGroup($userId, $groupId)
{
	G::LoadClass('wsBase');
	$ws = new wsBase ();
	$result = $ws->assignUserToGroup($userId, $groupId);

	if($result->status_code == 0){
		return 1;
	} else {
		return 0;
	}
}

function PMFCreateUser($userId, $password, $firstname, $lastname, $email, $role)
{
	G::LoadClass('wsBase');
	$ws = new wsBase ();
	$result = $ws->createUser($userId, $firstname, $lastname, $email, $role, $password);

	if($result->status_code == 0){
		return 1;
	} else {
		return 0;
	}
}

function generateCode ( $iDigits = 4, $sType = 'NUMERIC' ) {
	return G::generateCode ($iDigits, $sType );
}


function setCaseTrackerCode($sApplicationUID, $sCode, $sPIN = '') {
  if ($sCode != '') {
    G::LoadClass('case');
    $oCase   = new Cases();
    $aFields = $oCase->loadCase($sApplicationUID);
    $aFields['APP_PROC_CODE'] = $sCode;
    if ($sPIN != '') {
      $aFields['APP_DATA']['PIN'] = $sPIN;
      $aFields['APP_PIN']         = md5($sPIN);
    }
    $oCase->updateCase($sApplicationUID, $aFields);
    return 1;
  }
  else {
    return 0;
  }
}

function jumping ( $caseId, $delIndex ) {
	$x = $this->PMFDerivateCase($caseId, $delIndex);
	if($x==0)
		  G::SendTemporalMessage('ID_NOT_DERIVATED', 'error', 'labels');

	G::header('Location: cases_List');
}

function PMFgetLabelOption($PROCESS, $DYNAFORM_UID, $FIELD_NAME, $FIELD_SELECTED_ID){
	$G_FORM = new Form ("{$PROCESS}/{$DYNAFORM_UID}", PATH_DYNAFORM, SYS_LANG, false);
	if( isset($G_FORM->fields[$FIELD_NAME]->option[$FIELD_SELECTED_ID]) ){
		return $G_FORM->fields[$FIELD_NAME]->option[$FIELD_SELECTED_ID];
	} else {
		return null;
	}
}

function PMFRedirectToStep($sApplicationUID, $iDelegation, $sStepType, $sStepUid) {
  require_once 'classes/model/AppDelegation.php';
  $oCriteria = new Criteria('workflow');
  $oCriteria->addSelectColumn(AppDelegationPeer::TAS_UID);
  $oCriteria->add(AppDelegationPeer::APP_UID, $sApplicationUID);
  $oCriteria->add(AppDelegationPeer::DEL_INDEX, $iDelegation);
  $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
  $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
  $oDataset->next();
  $aRow = $oDataset->getRow();
  if ($aRow) {
    require_once 'classes/model/Step.php';
    $oStep = new Step();
    $oTheStep = $oStep->loadByType($aRow['TAS_UID'], $sStepType, $sStepUid);
    $bContinue = true;
    if ($oTheStep->getStepCondition() != '') {
      G::LoadClass('case');
      $oCase   = new Cases();
      $aFields = $oCase->loadCase($sApplicationUID);
      G::LoadClass('pmScript');
      $oPMScript = new PMScript();
      $oPMScript->setFields($aFields['APP_DATA']);
      $oPMScript->setScript($oTheStep->getStepCondition());
      $bContinue = $oPMScript->evaluate();
    }
    if ($bContinue) {
      switch ($oTheStep->getStepTypeObj()) {
        case 'DYNAFORM':
          $sAction = 'EDIT';
        break;
        case 'OUTPUT_DOCUMENT':
          $sAction = 'GENERATE';
        break;
        case 'INPUT_DOCUMENT':
          $sAction = 'ATTACH';
        break;
        case 'EXTERNAL':
          $sAction = 'EDIT';
        break;
        case 'MESSAGE':
          $sAction = '';
        break;
      }
      G::header('Location: ' . 'cases_Step?TYPE=' . $sStepType . '&UID=' . $sStepUid . '&POSITION=' . $oTheStep->getStepPosition() . '&ACTION=' . $sAction);
      die;
    }
  }
}


function PMFgetNextAssignedUsers ($application){
	
	require_once 'classes/model/AppDelegation.php';
	require_once 'classes/model/Task.php';
	require_once 'classes/model/TaskUser.php';
	require_once 'classes/model/Users.php';
	
  $oCriteria = new Criteria('workflow');
  
  $oCriteria->addSelectColumn(AppDelegationPeer::PRO_UID);
  $oCriteria->add(AppDelegationPeer::APP_UID, $application);
  $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
  $oDataset->next();
  $aRow = $oDataset->getRow();
  $PRO_UID=$aRow[0];
  
  $c = new Criteria('workflow');
  $c->addSelectColumn(TaskPeer::TAS_UID);
  $c->add(TaskPeer::PRO_UID, $PRO_UID);
  $c->add(TaskPeer::TAS_LAST_ASSIGNED, 0);
  $oDataset = TaskPeer::doSelectRS($c);
  $oDataset->next();
  $aRow = $oDataset->getRow();
  $TAS_UID=$aRow[0];
  
  
  $k=new Criteria('workflow');
  $k->addSelectColumn(TaskUserPeer::USR_UID);
  $k->add(TaskUserPeer::TAS_UID,$TAS_UID);
  $k->add(TaskUserPeer::TU_TYPE,1);
  $ods=TaskUserPeer::doSelectRS($k);
  $ods->next();
  $row=$ods->getRow();
  $USR_UID=$row[0];
  
  $kk=new Criteria();
  $kk->addSelectColumn(UsersPeer::USR_UID);
  $kk->addSelectColumn(UsersPeer::USR_USERNAME);
  $kk->addSelectColumn(UsersPeer::USR_FIRSTNAME);
  $kk->addSelectColumn(UsersPeer::USR_LASTNAME);
  $kk->addSelectColumn(UsersPeer::USR_EMAIL);    
  $kk->add(UsersPeer::USR_UID,$USR_UID);
  $oDataset=UsersPeer::doSelectRS($kk);
  $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC); 
  $oDataset->next();                                   
  
  $aRow1 = $oDataset->getRow();
  
   $array=array(
'USR_UID'      => $aRow1['USR_UID'],
'USR_USERNAME' => $aRow1['USR_USERNAME'],
'USR_FIRSTNAME'=> $aRow1['USR_FIRSTNAME'],
'USR_LASTNAME' => $aRow1['USR_LASTNAME'],
'USR_EMAIL'    => $aRow1['USR_EMAIL']
);
  
  return ($array);
  
}
	
