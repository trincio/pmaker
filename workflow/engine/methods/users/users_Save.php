<?php
/**
 * users_Save.php
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
  global $RBAC;
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
  }
  if (empty($_POST)) {
  	die('The information sended is empty!');
  }
  if (isset($_GET['USR_UID']))
  {
	  $_POST['form']['USR_UID'] = $_GET['USR_UID'];
  }
  else {
  	$_POST['form']['USR_UID'] = '';
  }
  if (isset($_FILES['form']['name']['USR_RESUME'])) {
  	if ($_FILES['form']['tmp_name']['USR_RESUME'] != '') {
  	  $_POST['form']['USR_RESUME'] = $_FILES['form']['name']['USR_RESUME'];
    }
    else {
    	$_POST['form']['USR_RESUME'] = '';
    }
  }
  /*if ($_POST['form']['USR_EMAIL'] != '') {
		if (!ereg("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*$", $_POST['form']['USR_EMAIL'])) {
			G::SendTemporalMessage ('ID_INCORRECT_EMAIL', 'error');
		}
	}*/
	if (!isset($_POST['form']['USR_NEW_PASS'])) {
		$_POST['form']['USR_NEW_PASS'] = '';
	}
	if ($_POST['form']['USR_NEW_PASS'] != '') {
	  $_POST['form']['USR_PASSWORD'] = md5($_POST['form']['USR_NEW_PASS']);
	}
	if (!isset($_POST['form']['USR_CITY'])) {
		$_POST['form']['USR_CITY'] = '';
	}
	if (!isset($_POST['form']['USR_LOCATION'])) {
		$_POST['form']['USR_LOCATION'] = '';
	}
	if ($_POST['form']['USR_UID'] == '') {
		$aData['USR_USERNAME']    = $_POST['form']['USR_USERNAME'];
		$aData['USR_PASSWORD']    = $_POST['form']['USR_PASSWORD'];
		$aData['USR_FIRSTNAME']   = $_POST['form']['USR_FIRSTNAME'];
		$aData['USR_LASTNAME']    = $_POST['form']['USR_LASTNAME'];
		$aData['USR_EMAIL']       = $_POST['form']['USR_EMAIL'];
		$aData['USR_DUE_DATE']    = $_POST['form']['USR_DUE_DATE'];
		$aData['USR_CREATE_DATE'] = date('Y-m-d H:i:s');
		$aData['USR_UPDATE_DATE'] = date('Y-m-d H:i:s');
		$aData['USR_BIRTHDAY']    = date('Y-m-d');
		//fixing bug in inactive user when the admin create a new user.
		$statusWF = $_POST['form']['USR_STATUS'];
		$aData['USR_STATUS']      = $_POST['form']['USR_STATUS'] == 'ACTIVE' ? 1 : 0;
		$sUserUID                 = $RBAC->createUser($aData, $_POST['form']['USR_ROLE']);
		$aData['USR_STATUS']      = $statusWF;
		$aData['USR_UID']         = $sUserUID;
		$aData['USR_PASSWORD']    = md5($sUserUID);//fake :p
		$aData['USR_COUNTRY']     = $_POST['form']['USR_COUNTRY'];
		$aData['USR_CITY']        = $_POST['form']['USR_CITY'];
		$aData['USR_LOCATION']    = $_POST['form']['USR_LOCATION'];
		$aData['USR_ADDRESS']     = $_POST['form']['USR_ADDRESS'];
		$aData['USR_PHONE']       = $_POST['form']['USR_PHONE'];
		$aData['USR_ZIP_CODE']    = $_POST['form']['USR_ZIP_CODE'];
		$aData['USR_POSITION']    = $_POST['form']['USR_POSITION'];
		$aData['USR_RESUME']      = $_POST['form']['USR_RESUME'];
		$aData['USR_ROLE']        = $_POST['form']['USR_ROLE'];
		require_once 'classes/model/Users.php';
		$oUser = new Users();
		$oUser->create($aData);
		if ($_FILES['form']['error']['USR_PHOTO'] != 1) {
		  if ($_FILES['form']['tmp_name']['USR_PHOTO'] != '') {
			  G::uploadFile($_FILES['form']['tmp_name']['USR_PHOTO'], PATH_IMAGES_ENVIRONMENT_USERS, $sUserUID . '.gif');
		  }
	  }
	  else {
	  	G::SendTemporalMessage ('ID_FILE_TOO_BIG', 'error');
	  }
	  if ($_FILES['form']['error']['USR_RESUME'] != 1) {
		  if ($_FILES['form']['tmp_name']['USR_RESUME'] != '') {
			  G::uploadFile($_FILES['form']['tmp_name']['USR_RESUME'], PATH_IMAGES_ENVIRONMENT_FILES . $sUserUID . '/', $_FILES['form']['name']['USR_RESUME']);
		  }
		}
		else {
	  	G::SendTemporalMessage ('ID_FILE_TOO_BIG', 'error');
	  }
	}
	else {
		$aData['USR_UID']         = $_POST['form']['USR_UID'];
		$aData['USR_USERNAME']    = $_POST['form']['USR_USERNAME'];
		if (isset($_POST['form']['USR_PASSWORD'])) {
		  if ($_POST['form']['USR_PASSWORD'] != '') {
		    $aData['USR_PASSWORD'] = $_POST['form']['USR_PASSWORD'];
		    require_once 'classes/model/UsersProperties.php';
        $oUserProperty = new UsersProperties();
        $aUserProperty = $oUserProperty->loadOrCreateIfNotExists($_POST['form']['USR_UID'], array('USR_PASSWORD_HISTORY' => serialize(array($_POST['form']['USR_NEW_PASS']))));
        $aErrors       = $oUserProperty->validatePassword($_POST['form']['USR_NEW_PASS'], $aUserProperty['USR_LAST_UPDATE_DATE'], 0);
        if (count($aErrors) > 0) {
          $sDescription = G::LoadTranslation('ID_POLICY_ALERT').':<br /><br />';
          foreach ($aErrors as $sError)  {
            switch ($sError) {
              case 'ID_PPP_MINIMUN_LENGTH':
                $sDescription .= ' - ' . G::LoadTranslation($sError).': ' . PPP_MINIMUN_LENGTH . '<br />';
              break;
              case 'ID_PPP_MAXIMUN_LENGTH':
                $sDescription .= ' - ' . G::LoadTranslation($sError).': ' . PPP_MAXIMUN_LENGTH . '<br />';
              break;
              case 'ID_PPP_EXPIRATION_IN':
                $sDescription .= ' - ' . G::LoadTranslation($sError).' ' . PPP_EXPIRATION_IN . ' ' . G::LoadTranslation('ID_DAYS') . '<br />';
              break;
              default:
                $sDescription .= ' - ' . G::LoadTranslation($sError).'<br />';
              break;
            }
          }
          $sDescription .= '<br />' . G::LoadTranslation('ID_PLEASE_CHANGE_PASSWORD_POLICY');
          G::SendMessageText($sDescription, 'warning');
          G::header('Location: ' . $_SERVER['HTTP_REFERER']);
          die;
        }
        $aHistory      = unserialize($aUserProperty['USR_PASSWORD_HISTORY']);
        if (!is_array($aHistory)) {
          $aHistory = array();
        }
        if (!defined('PPP_PASSWORD_HISTORY')) {
          define('PPP_PASSWORD_HISTORY', 0);
        }
        if (PPP_PASSWORD_HISTORY > 0) {
          if (count($aHistory) >= PPP_PASSWORD_HISTORY) {
            array_shift($aHistory);
          }
          $aHistory[] = $_POST['form']['USR_NEW_PASS'];
        }
        $aUserProperty['USR_LAST_UPDATE_DATE'] = date('Y-m-d H:i:s');
        $aUserProperty['USR_LOGGED_NEXT_TIME'] = 1;
        $aUserProperty['USR_PASSWORD_HISTORY'] = serialize($aHistory);
        $oUserProperty->update($aUserProperty);
	    }
	  }
		$aData['USR_FIRSTNAME']   = $_POST['form']['USR_FIRSTNAME'];
		$aData['USR_LASTNAME']    = $_POST['form']['USR_LASTNAME'];
		$aData['USR_EMAIL']       = $_POST['form']['USR_EMAIL'];
		$aData['USR_DUE_DATE']    = $_POST['form']['USR_DUE_DATE'];
		$aData['USR_UPDATE_DATE'] = date('Y-m-d H:i:s');
		if (isset($_POST['form']['USR_STATUS'])) {
		  $aData['USR_STATUS']      = $_POST['form']['USR_STATUS'];
	  }
	  if (isset($_POST['form']['USR_ROLE'])) {
		  $RBAC->updateUser($aData, $_POST['form']['USR_ROLE']);
		}
		else {
		  $RBAC->updateUser($aData);
		}
		$aData['USR_COUNTRY']     = $_POST['form']['USR_COUNTRY'];
		$aData['USR_CITY']        = $_POST['form']['USR_CITY'];
		$aData['USR_LOCATION']    = $_POST['form']['USR_LOCATION'];
		$aData['USR_ADDRESS']     = $_POST['form']['USR_ADDRESS'];
		$aData['USR_PHONE']       = $_POST['form']['USR_PHONE'];
		$aData['USR_ZIP_CODE']    = $_POST['form']['USR_ZIP_CODE'];
		$aData['USR_POSITION']    = $_POST['form']['USR_POSITION'];
		if ($_POST['form']['USR_RESUME'] != '') {
		  $aData['USR_RESUME'] = $_POST['form']['USR_RESUME'];
	  }
	  if (isset($_POST['form']['USR_ROLE'])) {
		  $aData['USR_ROLE'] = $_POST['form']['USR_ROLE'];
		}
		require_once 'classes/model/Users.php';
		$oUser = new Users();
		$oUser->update($aData);
		if ($_FILES['form']['error']['USR_PHOTO'] != 1) {
		  if ($_FILES['form']['tmp_name']['USR_PHOTO'] != '') {
		  	$aAux = explode('.', $_FILES['form']['name']['USR_PHOTO']);
		  	G::uploadFile($_FILES['form']['tmp_name']['USR_PHOTO'], PATH_IMAGES_ENVIRONMENT_USERS, $aData['USR_UID'] . '.' . $aAux[1]);
		  	G::resizeImage(PATH_IMAGES_ENVIRONMENT_USERS . $aData['USR_UID'] . '.' . $aAux[1], 96, 96, PATH_IMAGES_ENVIRONMENT_USERS . $aData['USR_UID'] . '.gif');
		  }
		}
	  else {
	  	G::SendTemporalMessage ('ID_FILE_TOO_BIG', 'error');
	  }
	  if ($_FILES['form']['error']['USR_RESUME'] != 1) {
		  if ($_FILES['form']['tmp_name']['USR_RESUME'] != '') {
		  	G::uploadFile($_FILES['form']['tmp_name']['USR_RESUME'], PATH_IMAGES_ENVIRONMENT_FILES . $aData['USR_UID'] . '/', $_FILES['form']['name']['USR_RESUME']);
		  }
		}
		else {
	  	G::SendTemporalMessage ('ID_FILE_TOO_BIG', 'error');
	  }
	}
	
	if($_SESSION['USER_LOGGED'] == $_POST['form']['USR_UID']){
		/*UPDATING SESSION VARIABLES*/
		$aUser = $RBAC->userObj->load($_SESSION['USER_LOGGED']);
		$_SESSION['USR_FULLNAME'] = $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'];
	}
	
	G::header('location: users_List');
}
catch (Exception $oException) {
	die($oException->getMessage());
}
?>