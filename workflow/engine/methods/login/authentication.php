<?php
/**
 * $Id$
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * You can contact Colosa Inc, 2655 Le Jeune Road, Suite 1112, Coral Gables,
 * FL 33134, USA or email info@colosa.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * ProcessMaker" logo and retain the original copyright notice. If the display
 * of the logo is not reasonably feasible for technical reasons, the
 * Appropriate Legal Notices must display the words "Powered by ProcessMaker"
 * and retain the original copyright notice.
 * -
 */

try {
	//G::LoadClass ("user");

	$frm  = $_POST['form'];
	if ( isset ( $frm['USR_USERNAME'] ) )
	{
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

  $lang = (isset($frm['USER_LANG']) && $frm['USER_LANG']) ? $frm['USER_LANG'] :
    defined('SYS_LANG') ? SYS_LANG : 'en';
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