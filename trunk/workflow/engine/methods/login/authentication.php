<?php
/**
 * authentication.php
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


  if (!isset($_POST['form']) ) {
    G::SendTemporalMessage ('ID_USER_HAVENT_RIGHTS_SYSTEM', "error");
    G::header  ("location: login.html");die;
  }


try {
	$frm = $_POST['form'];
	$usr = '';
	$pwd = '';
	if (isset($frm['USR_USERNAME'])) {
	  $usr = strtolower(trim($frm['USR_USERNAME']));
	  $pwd = trim($frm['USR_PASSWORD']);
	}
	$uid = $RBAC->VerifyLogin($usr , $pwd);

	//krumo ($uid);
	//die;

	switch ($uid) {
		//The user not exists
	  case -1:
	    G::SendTemporalMessage ('ID_USER_NOT_REGISTERED', "warning");
	    break;
	  //The password is incorrect
	  case -2:
	    G::SendTemporalMessage ('ID_WRONG_PASS', "warning");
	    break;
	  //The user is inactive
	  case -3:
	  	G::SendTemporalMessage ('ID_USER_INACTIVE', "warning");
	  //The Due date is finished
	  case -4:
	    G::SendTemporalMessage ('ID_USER_INACTIVE', "warning");
	    break;
	}

	if ($uid < 0 ) {
	  $_SESSION['FAILED_LOGINS']++;
	  if (!defined('PPP_FAILED_LOGINS')) {
      define('PPP_FAILED_LOGINS', 0);
    }
    if (PPP_FAILED_LOGINS > 0) {
      if ($_SESSION['FAILED_LOGINS'] >= PPP_FAILED_LOGINS) {
        $oConnection = Propel::getConnection('rbac');
        $oStatement  = $oConnection->prepareStatement("SELECT USR_UID FROM USERS WHERE USR_USERNAME = '" . $usr . "'");
        $oDataset    = $oStatement->executeQuery();
        if ($oDataset->next()) {
          $sUserUID = $oDataset->getString('USR_UID');
          $oConnection = Propel::getConnection('rbac');
          $oStatement  = $oConnection->prepareStatement("UPDATE USERS SET USR_STATUS = 0 WHERE USR_UID = '" . $sUserUID . "'");
          $oStatement->executeQuery();
          $oConnection = Propel::getConnection('workflow');
          $oStatement  = $oConnection->prepareStatement("UPDATE USERS SET USR_STATUS = 'INACTIVE' WHERE USR_UID = '" . $sUserUID . "'");
          $oStatement->executeQuery();
          unset($_SESSION['FAILED_LOGINS']);
          G::SendMessageText(G::LoadTranslation('ID_ACCOUNT') . ' "' . $usr . '" ' . G::LoadTranslation('ID_ACCOUNT_DISABLED_CONTACT_ADMIN'), 'warning');
        }
        else {
          //Nothing
        }
      }
    }
	  G::header  ("location: login.html");
	  die;
	}

	$_SESSION['USER_LOGGED']  = $uid;
	$_SESSION['USR_USERNAME'] = $usr;
	unset($_SESSION['FAILED_LOGINS']);

  // Asign the uid of user to userloggedobj
  $RBAC->loadUserRolePermission($RBAC->sSystem, $uid);
	$res = $RBAC->userCanAccess('PM_LOGIN');

	if ($res != 1 ) {
	  if ($res == -2)
	    G::SendTemporalMessage ('ID_USER_HAVENT_RIGHTS_SYSTEM', "error");
	  else
	    G::SendTemporalMessage ('ID_USER_HAVENT_RIGHTS_PAGE', "error");
	  G::header  ("location: login.html");
	  die;
	}

  if (isset($frm['USER_LANG'])) {
  	if ($frm['USER_LANG'] != '') {
  		$lang = $frm['USER_LANG'];
  	}
  }
  else {
  	if (defined('SYS_LANG')) {
  		$lang = SYS_LANG;
  	}
  	else {
  		$lang = 'en';
  	}
  }

  /**log by Everth**/
  require_once 'classes/model/LoginLog.php';
  $weblog=new LoginLog();
  $aLog['LOG_UID']            = G::generateUniqueID();
  $aLog['LOG_STATUS']					= 'ACTIVE';
  $aLog['LOG_IP']             = $_SERVER['REMOTE_ADDR'];
  $aLog['LOG_SID']            = session_id();
  $aLog['LOG_INIT_DATE']			= date('Y-m-d H:i:s');
  //$aLog['LOG_END_DATE']				= '0000-00-00 00:00:00';
  $aLog['LOG_CLIENT_HOSTNAME']= $_SERVER['HTTP_HOST'];
  $aLog['USR_UID']						= $_SESSION['USER_LOGGED'];
  $weblog->create($aLog);
  /**end log**/

  /* Check password using policy - Start */
  require_once 'classes/model/UsersProperties.php';
  $oUserProperty = new UsersProperties();
  $aUserProperty = $oUserProperty->loadOrCreateIfNotExists($_SESSION['USER_LOGGED'], array('USR_PASSWORD_HISTORY' => serialize(array($_POST['form']['USR_PASSWORD']))));
  $aErrors       = $oUserProperty->validatePassword($_POST['form']['USR_PASSWORD'], $aUserProperty['USR_LAST_UPDATE_DATE'], $aUserProperty['USR_LOGGED_NEXT_TIME']);
  if (!empty($aErrors)) {
    if (!defined('NO_DISPLAY_USERNAME')) {
      define('NO_DISPLAY_USERNAME', 1);
    }
    $aFields = array();
    $aFields['DESCRIPTION']  = '<span style="font-weight:normal;">';
    $aFields['DESCRIPTION'] .= G::LoadTranslation('ID_POLICY_ALERT').':<br /><br />';
    foreach ($aErrors as $sError)  {
      switch ($sError) {
        case 'ID_PPP_MINIMUN_LENGTH':
          $aFields['DESCRIPTION'] .= ' - ' . G::LoadTranslation($sError).': ' . PPP_MINIMUN_LENGTH . '<br />';
          $aFields[substr($sError, 3)] = PPP_MINIMUN_LENGTH;
        break;
        case 'ID_PPP_MAXIMUN_LENGTH':
          $aFields['DESCRIPTION'] .= ' - ' . G::LoadTranslation($sError).': ' . PPP_MAXIMUN_LENGTH . '<br />';
          $aFields[substr($sError, 3)] = PPP_MAXIMUN_LENGTH;
        break;
        case 'ID_PPP_EXPIRATION_IN':
          $aFields['DESCRIPTION'] .= ' - ' . G::LoadTranslation($sError).' ' . PPP_EXPIRATION_IN . ' ' . G::LoadTranslation('ID_DAYS') . '<br />';
          $aFields[substr($sError, 3)] = PPP_EXPIRATION_IN;
        break;
        default:
          $aFields['DESCRIPTION'] .= ' - ' . G::LoadTranslation($sError).'<br />';
          $aFields[substr($sError, 3)] = 1;
        break;
      }
    }
    $aFields['DESCRIPTION'] .= '<br />' . G::LoadTranslation('ID_PLEASE_CHANGE_PASSWORD_POLICY') . '<br /><br /></span>';
    $G_PUBLISH = new Publisher;
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/changePassword', '', $aFields, 'changePassword');
    G::RenderPage('publish');
    die;
  }
  /* Check password using policy - End */

  if ($_POST['form']['URL'] != '') {
    $sLocation = $_POST['form']['URL'];
  }
  else {
    $sLocation = $oUserProperty->redirectTo($_SESSION['USER_LOGGED'], $lang);
  }
  G::header('Location: ' . $sLocation);
  die;

}
catch ( Exception $e ) {
  $aMessage['MESSAGE'] = $e->getMessage();
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
  G::RenderPage( 'publish' );
  die;
}