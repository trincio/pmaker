<?php
/**
 * myInfo.php
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
	switch ($RBAC->userCanAccess('PM_LOGIN'))
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
	G::LoadClass('xmlfield_Image');
	require_once 'classes/model/Users.php';
	unset($_SESSION['CURRENT_USER']);
	$oUser                    = new Users();
	$aFields                  = $oUser->load($_SESSION['USER_LOGGED']);
	$aFields['USR_PASSWORD']  = '********';
	$aFields['MESSAGE0']      = G::LoadTranslation('ID_USER_REGISTERED') . '!';
	$aFields['MESSAGE1']      = G::LoadTranslation('ID_MSG_ERROR_USR_USERNAME');
	$aFields['MESSAGE2']      = G::LoadTranslation('ID_MSG_ERROR_DUE_DATE');
	$aFields['MESSAGE3']      = G::LoadTranslation('ID_NEW_PASS_SAME_OLD_PASS');
	$aFields['MESSAGE4']      = G::LoadTranslation('ID_MSG_ERROR_USR_FIRSTNAME');
	$aFields['MESSAGE5']      = G::LoadTranslation('ID_MSG_ERROR_USR_LASTNAME');
	$aFields['NO_RESUME']     = G::LoadTranslation('ID_NO_RESUME');
	$aFields['START_DATE']    = date('Y-m-d');
	$aFields['END_DATE']      = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y') + 5));
	$aFields['RANDOM']        = rand();
	$G_MAIN_MENU              = 'processmaker';
	$G_ID_MENU_SELECTED       = 'MY_ACCOUNT';
	$G_PUBLISH                = new Publisher;
	


	//$RBAC->systemObj->loadByCode('PROCESSMAKER');//('PROCESSMAKER', $_SESSION['USER_LOGGED']);
	
	#verifying if it has any preferences on the configurations table
	G::loadClass('configuration');
	$oConf = new Configurations; 
	$oConf->loadConfig($x, 'USER_PREFERENCES','','',$_SESSION['USER_LOGGED'],'');
	
	//echo $RBAC->aUserInfo['PROCESSMAKER']['ROLE']['ROL_CODE'];
	//G::pr($RBAC->userObj->load($_SESSION['USER_LOGGED']));
	if( sizeof($oConf->Fields) > 0){ #this user has a configuration record
		$aFields['PREF_DEFAULT_LANG'] = $oConf->aConfig['DEFAULT_LANG'];
		$aFields['PREF_DEFAULT_MENUSELECTED'] = $oConf->aConfig['DEFAULT_MENU'];
	} else {
		switch($RBAC->aUserInfo['PROCESSMAKER']['ROLE']['ROL_CODE']){
			case 'PROCESSMAKER_ADMIN':
				$aFields['PREF_DEFAULT_MENUSELECTED'] = 'PM_USERS';
				break;

			case 'PROCESSMAKER_OPERATOR':
				$aFields['PREF_DEFAULT_MENUSELECTED'] = 'PM_CASES';
				break;
			
		}
		$aFields['PREF_DEFAULT_LANG'] = SYS_LANG;
	}
	//G::pr($RBAC->aUserInfo);
	$rows[] = Array('id'=>'char', 'name'=>'char');


	foreach($RBAC->aUserInfo['PROCESSMAKER']['PERMISSIONS'] as $permission){
		
		switch($permission['PER_CODE']){
			case 'PM_USERS':	
				 $rows[] = Array('id'=>'PM_USERS', 'name'=>strtoupper(G::LoadTranslation('ID_USERS'))); 
			break;
			case 'PM_CASES':	
				 $rows[] = Array('id'=>'PM_CASES', 'name'=>strtoupper(G::LoadTranslation('ID_CASES'))); 
			break;
			case 'PM_FACTORY':	
				 $rows[] = Array('id'=>'PM_FACTORY', 'name'=>strtoupper(G::LoadTranslation('ID_APPLICATIONS'))); 
			break;
			case 'PM_DASHBOARD':	
				 $rows[] = Array('id'=>'PM_DASHBOARD', 'name'=>strtoupper(G::LoadTranslation('ID_DASHBOARD'))); 
			break;
			case 'PM_SETUP':	
				 $rows[] = Array('id'=>'PM_SETUP', 'name'=>strtoupper(G::LoadTranslation('ID_SETUP'))); 
			break;
		}
	}
	//G::pr($rows); die;
	global $_DBArray;
	$_DBArray['menutab']   = $rows;
	$_SESSION['_DBArray'] = $_DBArray;
	G::LoadClass('ArrayPeer');
	$oCriteria = new Criteria('dbarray');
	$oCriteria->setDBArrayTable('menutab');

	
	$G_PUBLISH->AddContent('xmlform', 'xmlform', 'users/myInfoView.xml', '', $aFields);

	if ($RBAC->userCanAccess('PM_EDITPERSONALINFO') != 1) {
		$G_PUBLISH->AddContent('xmlform', 'xmlform', 'users/myInfo2.xml', 'display:none', $aFields, 'myInfo_Save');
	}
	else {
		$G_PUBLISH->AddContent('xmlform', 'xmlform', 'users/myInfo.xml', 'display:none', $aFields, 'myInfo_Save');
	}
	G::RenderPage('publish');
}
catch (Exception $oException) {
	die($oException->getMessage());
}
?>