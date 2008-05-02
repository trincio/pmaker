<?php
/**
 * class.wsBase.php
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
//
// It works with the table CONFIGURATION in a WF dataBase
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

require_once ( "classes/model/Application.php" );
require_once ( "classes/model/AppDelegation.php" );
require_once ( "classes/model/AppThread.php" );
require_once ( "classes/model/Dynaform.php" );
require_once ( "classes/model/InputDocument.php" );
require_once ( "classes/model/Language.php" );
require_once ( "classes/model/OutputDocument.php" );
require_once ( "classes/model/Process.php" );
require_once ( "classes/model/ReportTable.php");
require_once ( "classes/model/ReportVar.php");
require_once ( "classes/model/Step.php" );
require_once ( "classes/model/StepTrigger.php" );
require_once ( "classes/model/Task.php" );
require_once ( "classes/model/TaskUser.php" );
require_once ( "classes/model/Triggers.php" );
require_once ( "classes/model/Users.php" );
G::LoadClass('pmScript');

class wsResponse
{
	public $status_code = 0;
	public $message = '';
	public $timestamp = '';
	
	function __construct( $status, $message )
	{
  	$this->status_code = $status;
	  $this->message     = $message;
	  $this->timestamp   = date('Y-m-d H:i:s');
	}
	
	function getPayloadString ( $operation ) {
    $res = "<$operation>\n";
    $res .= "<status_code>" . $this->status_code . "</status_code>";
    $res .= "<message>" . $this->message . " $operation </message>";
    $res .= "<timestamp>" . $this->timestamp . "</timestamp>";
    $res .= "<$operation>";
    return $res;
	}
}

class wsBase
{
	function __construct()
	{
	}
	
	public function login( $username, $password  ) {
		global $RBAC;

    try {	
    	$uid  = $RBAC->VerifyLogin( $username , $password);
	    switch ($uid) {
    		case -1: //The user not exists
        	$wsResponse = new wsResponse (3, G::loadTranslation ('ID_USER_NOT_REGISTERED'));
    	    break;
    	  
    	  case -2://The password is incorrect
        	$wsResponse = new wsResponse (4, G::loadTranslation ('ID_WRONG_PASS'));
    	    break;
    	  
    	  case -3: //The user is inactive
        	$wsResponse = new wsResponse (5, G::loadTranslation ('ID_USER_INACTIVE'));
    	  
    	  case -4: //The Due date is finished
        	$wsResponse = new wsResponse (5, G::loadTranslation ('ID_USER_INACTIVE'));
    	    break;
    	}
    
    	if ($uid < 0 ) {
      	throw ( new Exception ( serialize ( $wsResponse ) ));
    	}
    
      // Asign the uid of user to userloggedobj
      $RBAC->loadUserRolePermission( $RBAC->sSystem, $uid );
	    $res = $RBAC->userCanAccess("PM_LOGIN");
      
	    if ($res != 1 ) {
	      if ($res == -2)
        	$wsResponse = new wsResponse (1, G::loadTranslation ('ID_USER_HAVENT_RIGHTS_SYSTEM'));
	      else
        	$wsResponse = new wsResponse (2, G::loadTranslation ('ID_USER_HAVENT_RIGHTS_SYSTEM'));
      	throw ( new Exception ( serialize ( $wsResponse ) ));
	    }
      
	  	$wsResponse = new wsResponse ('0', $uid );
	  	return $wsResponse;
    }
    catch ( Exception $e ) {
    	$wsResponse = unserialize ( $e->getMessage() );
	  	return $wsResponse;
    }		
	}
	
}