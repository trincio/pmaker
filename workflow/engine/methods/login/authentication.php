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
	  if (!defined('PPU_FAILED_LOGINS')) {
      define('PPU_FAILED_LOGINS', 0);
    }
    if (PPU_FAILED_LOGINS > 0) {
      if ($_SESSION['FAILED_LOGINS'] > PPU_FAILED_LOGINS) {
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
  if (!$oUserProperty->UserPropertyExists($_SESSION['USER_LOGGED'])) {
    $aUserProperty = array('USR_UID'               => $_SESSION['USER_LOGGED'],
                           'USR_LAST_UPDATE_DATE'  => date('Y-m-d H:i:s'),
                           'USR_LOGGED_NEXT_TIME'  => 0,
                           'USR_PASSWORD_HISTORY'  => serialize(array($_POST['form']['USR_PASSWORD'])));
    $oUserProperty->create($aUserProperty);
  }
  else {
    $aUserProperty = $oUserProperty->load($_SESSION['USER_LOGGED']);
  }
  if (!defined('PPU_MINIMUN_LENGTH')) {
    define('PPU_MINIMUN_LENGTH', 5);
  }
  if (!defined('PPU_MAXIMUN_LENGTH')) {
    define('PPU_MAXIMUN_LENGTH', 20);
  }
  if (!defined('PPU_NUMERICAL_CHARACTER_REQUIRED')) {
    define('PPU_NUMERICAL_CHARACTER_REQUIRED', 0);
  }
  if (!defined('PPU_UPPERCASE_CHARACTER_REQUIRED')) {
    define('PPU_UPPERCASE_CHARACTER_REQUIRED', 0);
  }
  if (!defined('PPU_SPECIAL_CHARACTER_REQUIRED')) {
    define('PPU_SPECIAL_CHARACTER_REQUIRED', 0);
  }
  if (!defined('PPU_EXPIRATION_IN')) {
    define('PPU_EXPIRATION_IN', 0);
  }
  if (!defined('PPU_CHANGE_PASSWORD_AFTER_NEXT_LOGIN')) {
    define('PPU_CHANGE_PASSWORD_AFTER_NEXT_LOGIN', 0);
  }
  if (function_exists('mb_strlen')) {
    $iLength = mb_strlen($_POST['form']['USR_PASSWORD']);
  }
  else {
    $iLength = strlen($_POST['form']['USR_PASSWORD']);
  }
  $aErrors = array();
  if ($iLength < PPU_MINIMUN_LENGTH) {
    $aErrors[] = 'ID_PPU_MINIMUN_LENGTH';
  }
  if ($iLength > PPU_MAXIMUN_LENGTH) {
    $aErrors[] = 'ID_PPU_MAXIMUN_LENGTH';
  }
  if (PPU_NUMERICAL_CHARACTER_REQUIRED == 1) {
    if (preg_match_all('/[0-9]/', $_POST['form']['USR_PASSWORD'], $aMatch, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE) == 0) {
      $aErrors[] = 'ID_PPU_NUMERICAL_CHARACTER_REQUIRED';
    }
  }
  if (PPU_UPPERCASE_CHARACTER_REQUIRED == 1) {
    if (preg_match_all('/[A-Z]/', $_POST['form']['USR_PASSWORD'], $aMatch, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE) == 0) {
      $aErrors[] = 'ID_PPU_UPPERCASE_CHARACTER_REQUIRED';
    }
  }
  if (PPU_SPECIAL_CHARACTER_REQUIRED == 1) {
    if (preg_match_all('/[ºª\\!|"@·#$~%€&¬\/()=\'?¡¿*+\-_.:,;]/', $_POST['form']['USR_PASSWORD'], $aMatch, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE) == 0) {
      $aErrors[] = 'ID_PPU_SPECIAL_CHARACTER_REQUIRED';
    }
  }
  if (PPU_EXPIRATION_IN > 0) {
    G::LoadClass('dates');
    $oDates = new dates();
    $fDays  = $oDates->calculateDuration(date('Y-m-d H:i:s'), $aUserProperty['USR_LAST_UPDATE_DATE']);
    if ($fDays > PPU_EXPIRATION_IN) {
      $aErrors[] = 'ID_PPU_EXPIRATION_IN';
    }
  }
  if (PPU_CHANGE_PASSWORD_AFTER_NEXT_LOGIN == 1) {
    if ($aUserProperty['USR_LOGGED_NEXT_TIME'] == 1) {
      $aErrors[] = 'ID_PPU_CHANGE_PASSWORD_AFTER_NEXT_LOGIN';
    }
  }
  if (!empty($aErrors)) {
    if (!defined('NO_DISPLAY_USERNAME')) {
      define('NO_DISPLAY_USERNAME', 1);
    }
    $aFields = array();
    $aFields['DESCRIPTION']  = '<span style="font-weight:normal;">';
    $aFields['DESCRIPTION'] .= G::LoadTranslation('ID_POLICY_ALERT').':<br /><br />';
    foreach ($aErrors as $sError)  {
      switch ($sError) {
        case 'ID_PPU_MINIMUN_LENGTH':
          $aFields['DESCRIPTION'] .= ' - ' . G::LoadTranslation($sError).': ' . PPU_MINIMUN_LENGTH . '<br />';
          $aFields[substr($sError, 3)] = PPU_MINIMUN_LENGTH;
        break;
        case 'ID_PPU_MAXIMUN_LENGTH':
          $aFields['DESCRIPTION'] .= ' - ' . G::LoadTranslation($sError).': ' . PPU_MAXIMUN_LENGTH . '<br />';
          $aFields[substr($sError, 3)] = PPU_MAXIMUN_LENGTH;
        break;
        case 'ID_PPU_EXPIRATION_IN':
          $aFields['DESCRIPTION'] .= ' - ' . G::LoadTranslation($sError).' ' . PPU_EXPIRATION_IN . ' ' . G::LoadTranslation('ID_DAYS') . '<br />';
          $aFields[substr($sError, 3)] = PPU_EXPIRATION_IN;
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

  //get the plugins, and check if there is redirectLogins
  //if yes, then redirect according his Role
  if ( class_exists('redirectDetail')) {
    //falta validar...
    if(isset($RBAC->aUserInfo['PROCESSMAKER']['ROLE']['ROL_CODE']))
    		$userRole = $RBAC->aUserInfo['PROCESSMAKER']['ROLE']['ROL_CODE'];

    $oPluginRegistry = &PMPluginRegistry::getSingleton();
    //$oPluginRegistry->showArrays();
    $aRedirectLogin = $oPluginRegistry->getRedirectLogins();
    if(isset($aRedirectLogin))
		 { if(is_array($aRedirectLogin))
		 	 {
		 	 		foreach ( $aRedirectLogin as $key=>$detail ) {
			  	   if(isset($detail->sPathMethod))
				  	  {
				  	  	if ( $detail->sRoleCode == $userRole ) {
				       	  G::header('location: /sys' .  SYS_TEMP . '/' . $lang . '/' . SYS_SKIN . '/' . $detail->sPathMethod );
				       	  die;
				  	   	}
				  	  }
		      }
		   }
     }
  }
  //end plugin


	$res = $RBAC->userCanAccess('PM_FACTORY');
	if ($res == 1) {
    G::header('location: /sys' .  SYS_TEMP . '/' . $lang . '/' . SYS_SKIN . '/' . 'processes/processes_List');
	  die;
	}

	$res = $RBAC->userCanAccess('PM_CASES');
	if ($res == 1) {
    G::header('location: /sys' .  SYS_TEMP . '/' . $lang . '/' . SYS_SKIN . '/' . 'cases/cases_List');
	  die;
	}

	$res = $RBAC->userCanAccess('PM_REPORTS');
	if ($res == 1) {
    G::header('location: /sys' .  SYS_TEMP . '/' . $lang . '/' . SYS_SKIN . '/' . 'reports/reportsList');
	  die;
	}

	$res = $RBAC->userCanAccess('PM_USERS');
	if ($res == 1) {
    G::header('location: /sys' .  SYS_TEMP . '/' . $lang . '/' . SYS_SKIN . '/' . 'users/users_List');
	  die;
	}

	$res = $RBAC->userCanAccess('PM_SETUP');
	if ($res == 1) {
    G::header('location: /sys' .  SYS_TEMP . '/' . $lang . '/' . SYS_SKIN . '/' . 'setup/pluginsList');
	  die;
	}

	G::header('location: /sys' .  SYS_TEMP . '/' . $lang . '/' . SYS_SKIN . '/' . 'users/myInfo');

}
catch ( Exception $e ) {
  $aMessage['MESSAGE'] = $e->getMessage();
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
  G::RenderPage( 'publish' );
  die;
}