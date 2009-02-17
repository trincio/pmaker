<?php
require_once 'classes/model/Users.php';
$oUser = new Users();
$aUser = $oUser->load($_SESSION['USER_LOGGED']);
global $RBAC;
$aData['USR_UID']      = $aUser['USR_UID'];
$aData['USR_USERNAME'] = $aUser['USR_USERNAME'];
$aData['USR_PASSWORD'] = md5($_POST['form']['USR_PASSWORD']);
$aData['USR_FIRSTNAME']   = $aUser['USR_FIRSTNAME'];
$aData['USR_LASTNAME']    = $aUser['USR_LASTNAME'];
$aData['USR_EMAIL']       = $aUser['USR_EMAIL'];
$aData['USR_DUE_DATE']    = $aUser['USR_DUE_DATE'];
$aData['USR_UPDATE_DATE'] = date('Y-m-d H:i:s');
$RBAC->updateUser($aData, $aUser['USR_ROLE']);
$aData['USR_COUNTRY']     = $aUser['USR_COUNTRY'];
$aData['USR_CITY']        = $aUser['USR_CITY'];
$aData['USR_LOCATION']    = $aUser['USR_LOCATION'];
$aData['USR_ADDRESS']     = $aUser['USR_ADDRESS'];
$aData['USR_PHONE']       = $aUser['USR_PHONE'];
$aData['USR_ZIP_CODE']    = $aUser['USR_ZIP_CODE'];
$aData['USR_POSITION']    = $aUser['USR_POSITION'];
$oUser->update($aData);
require_once 'classes/model/UsersProperties.php';
$oUserProperty = new UsersProperties();
$aUserProperty = $oUserProperty->load($_SESSION['USER_LOGGED']);
$aHistory      = unserialize($aUserProperty['USR_PASSWORD_HISTORY']);
if (!is_array($aHistory)) {
  $aHistory = array();
}
if (!defined('PPU_PASSWORD_HISTORY')) {
  define('PPU_PASSWORD_HISTORY', 0);
}
if (PPU_PASSWORD_HISTORY > 0) {
  if (count($aHistory) > PPU_PASSWORD_HISTORY) {
    array_shift($aHistory);
  }
  $aHistory[] = $_POST['form']['USR_PASSWORD'];
}
$aUserProperty['USR_LAST_UPDATE_DATE']  = date('Y-m-d H:i:s');
$aUserProperty['USR_LOGGED_FIRST_TIME'] = 0;
$aUserProperty['USR_PASSWORD_HISTORY']  = serialize($aHistory);
$oUserProperty->update($aUserProperty);
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
			       	  G::header('location: /sys' .  SYS_TEMP . '/' . SYS_LANG . '/' . SYS_SKIN . '/' . $detail->sPathMethod );
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
  G::header('location: /sys' .  SYS_TEMP . '/' . SYS_LANG . '/' . SYS_SKIN . '/' . 'processes/processes_List');
  die;
}

$res = $RBAC->userCanAccess('PM_CASES');
if ($res == 1) {
  G::header('location: /sys' .  SYS_TEMP . '/' . SYS_LANG . '/' . SYS_SKIN . '/' . 'cases/cases_List');
  die;
}

$res = $RBAC->userCanAccess('PM_REPORTS');
if ($res == 1) {
  G::header('location: /sys' .  SYS_TEMP . '/' . SYS_LANG . '/' . SYS_SKIN . '/' . 'reports/reportsList');
  die;
}

$res = $RBAC->userCanAccess('PM_USERS');
if ($res == 1) {
  G::header('location: /sys' .  SYS_TEMP . '/' . SYS_LANG . '/' . SYS_SKIN . '/' . 'users/users_List');
  die;
}

$res = $RBAC->userCanAccess('PM_SETUP');
if ($res == 1) {
  G::header('location: /sys' .  SYS_TEMP . '/' . SYS_LANG . '/' . SYS_SKIN . '/' . 'setup/pluginsList');
  die;
}

G::header('location: /sys' .  SYS_TEMP . '/' . SYS_LANG . '/' . SYS_SKIN . '/' . 'users/myInfo');