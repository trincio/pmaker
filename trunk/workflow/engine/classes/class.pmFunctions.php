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
		$stmt = $con->prepareStatement($SqlStatement);
		$rs = $stmt->executeQuery(ResultSet::FETCHMODE_ASSOC);
 		$lastId = $con->getIdGenerator();
 		$con->commit();
		switch(true) {
			case eregi("SELECT", $SqlStatement):
				$result = Array();
				$i=1;
				while ($rs->next()) {
					$result[$i++] = $rs->getRow();
				}
			break;
			case eregi("INSERT*", $SqlStatement):
				$result = $lastId->getId();
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
	if(isset($_SESSION['WS_SESSION_ID']) or $force){
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

function WSgetParam()
{
	if(isset($_SESSION['WS_SESSION_ID'])){
		$sessionId = $_SESSION['WS_SESSION_ID'];
		$params = array('sessionId'=>$sessionId);
		return $params;
	} else {
		throw new Exception('WS session is not open');
	}
}

function WSCreateUser($sessionId, $userId, $password, $firstname, $lastname, $email, $role)
{	
	$client = WSOpen();
	$sessionId = $_SESSION['WS_SESSION_ID'];
	$params = array('sessionId'=>$sessionId, 'userId'=>$userId, 'firstname'=>$firstname, 'lastname'=>$lastname, 'email'=>$email, 'role'=>$role, 'password'=>$password);
	$result = $client->__SoapCall('CreateUser', array($params));

	$result->status_code;
	if($result->status_code == 0){
		return true;
	} else {
		throw new Exception('WS[Create user]:failed!, '.$result->message);
	}
}

function WSTaskCase($caseId)
{
	$client = WSOpen();
	
	$sessionId = $_SESSION['WS_SESSION_ID'];

	$params = array('sessionId'=>$sessionId, 'caseId'=>$caseId);
	echo '<pre>';
	echo print_r($params);
	echo '</pre>';
	$result = $client->__SoapCall('TaskCase', array($params));

	//$rows[] = array ( 'guid' => 'char', 'name' => 'char' );

	//$i = 1;
	
	//if ( isset ( $result->taskCases ) )
	//echo '<pre>';
	//echo ($result->taskCases);
	//echo '</pre>';
	
	/*foreach ( $result->taskCases as $key=>$item) {
		if ( isset ($item->item) )
		foreach ( $item->item as $index=> $val ) {
			if ( $val->key == 'guid' ) $guid = $val->value;
			if ( $val->key == 'name' ) $name = $val->value;
		}
		else
		foreach ( $item as $index=> $val ) {
			if ( $val->key == 'guid' ) $guid = $val->value;
			if ( $val->key == 'name' ) $name = $val->value;
		}
		$rows[] = array ( 'guid' => $guid, 'name' => $name );
	}*/
}

	//echo 'session : '.$_SESSION['WS_SESSION_ID'];
	//WSTaskCase('355657143484d5e75a54bf7076495068');

?>