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

//$x = formatDate('2008-06-07','dd/mm/yy');
/*$x = executeQuery("insert into USERS (USR_UID,USR_USERNAME) values ('erik','erik')");
//$x = executeQuery("delete from USERS where USR_UID='erik'");
echo '<pre>-->';
print_r($x);
echo '<pre>';*/
//print_r(userInfo($_SESSION['USER_LOGGED']));
	
	
?>