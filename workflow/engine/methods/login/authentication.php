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
	$frm  = $_POST['form'];
	if ( isset ( $frm['USR_USERNAME'] ) ) 	{
	  $usr = strtolower( trim( $frm['USR_USERNAME']));
	  $pwd = trim( $frm['USR_PASSWORD']);
	}
  else
	{
		/*$usr = $_SESSION['USER_TEMP'];
	  $pwd = $_SESSION['PASS_TEMP'];
	  unset( $_SESSION['PASS_TEMP']);
		unset( $_SESSION['USER_TEMP']);*/
	}
	$uid  = $RBAC->VerifyLogin( $usr , $pwd);
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

	$_SESSION['USER_LOGGED'] = $uid;
	$_SESSION['USR_USERNAME'] = $usr;

  // Asign the uid of user to userloggedobj
  $RBAC->loadUserRolePermission( $RBAC->sSystem, $uid );
	$res = $RBAC->userCanAccess("PM_LOGIN");


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

  //TODO:
  /****************** THIS LINE IS NEEDED UNTIL THERE WERE FACOTORY ,...*/
  /*
  $_SESSION['USER_ROLE']  = 'ADMIN';
  $frm['USER_LANG'] = (isset($frm['USER_LANG']) && $frm['USER_LANG'])?$frm['USER_LANG']:
    defined('SYS_LANG')?SYS_LANG:'en';
  $_SESSION['USR_USERNAME'] = $frm['USR_USERNAME'];
  G::header('location: /sys' .  SYS_TEMP . '/' . $frm['USER_LANG'] . '/' . SYS_SKIN . '/' . 'cases/cases_List'); die;
  */
  /****************** THIS LINE IS NEEDED UNTIL THERE WERE FACOTORY ,...*/

	//G::LoadClass('log');
	//$log = new Log;
	//$log->SaveLogin ( $uid, $usr );

	//if ( file_exists ( PATH_METHODS . 'login/'. SYS_SYS . ".php" ) ) {
	//  include ( SYS_SYS . ".php" );
	//}

	$accessPMProcess   = $RBAC->userCanAccess("PM_FACTORY");
	$accessPMCases     = $RBAC->userCanAccess("PM_CASES");

	//if ( $accessWfArchitect == 1 )
	//  $_SESSION['USER_ROLE']  = 'ARCHITECT';
	//else if ( $accessWfProcess == 1 )
	//    $_SESSION['USER_ROLE']  = 'ADMIN';
	//  else
	//    $_SESSION['USER_ROLE']  = 'USER';

	//administrator
	if ( $accessPMProcess == 1) {
    G::header('location: /sys' .  SYS_TEMP . '/' . $lang . '/' . SYS_SKIN . '/' . 'processes/processes_List');
	  die;
	}

	//Operador
	if ( $accessPMCases == 1) {
    G::header('location: /sys' .  SYS_TEMP . '/' . $lang . '/' . SYS_SKIN . '/' . 'cases/cases_List');
	  die;
	}


	throw ( new Exception ( "this $usr has no role assigned  ($res, $uid)" ) );

}
catch ( Exception $e ) {
  $aMessage['MESSAGE'] = $e->getMessage();
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
  G::RenderPage( 'publish' );
  die;
}