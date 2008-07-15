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
	  G::header  ("location: login.html");
	  die;
	}

	$_SESSION['USER_LOGGED']  = $uid;
	$_SESSION['USR_USERNAME'] = $usr;

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