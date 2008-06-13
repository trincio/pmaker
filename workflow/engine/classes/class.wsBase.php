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
require_once ( "classes/model/Groupwf.php" );
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
require_once ( "classes/model/Session.php" );
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
    $res .= "<message>" . $this->message . "</message>";
    $res .= "<timestamp>" . $this->timestamp . "</timestamp>";
    $res .= "<array>" . $this->timestamp . "</array>";
    $res .= "<$operation>";
    return $res;
	}

	function getPayloadArray (  ) {
    return array("status_code" => $this->status_code , 'message'=> $this->message, 'timestamp' => $this->timestamp);
	}
}

class wsBase
{
	function __construct()
	{
	}
	
	public function login( $userid, $password  ) {
		global $RBAC;

    try {	
    	$uid  = $RBAC->VerifyLogin( $userid , $password);
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
    
      // check access to PM
      $RBAC->loadUserRolePermission( $RBAC->sSystem, $uid );
	    $res = $RBAC->userCanAccess("PM_LOGIN");
      
	    if ($res != 1 ) {
	      if ($res == -2)
        	$wsResponse = new wsResponse (1, G::loadTranslation ('ID_USER_HAVENT_RIGHTS_SYSTEM'));
	      else
        	$wsResponse = new wsResponse (2, G::loadTranslation ('ID_USER_HAVENT_RIGHTS_SYSTEM'));
      	throw ( new Exception ( serialize ( $wsResponse ) ));
	    }

      $sessionId = G::generateUniqueID();
	  	$wsResponse = new wsResponse ('0', $sessionId );
	  	
	  	
	  	$session = new Session ();
	  	$session->setSesUid ( $sessionId );
	  	$session->setSesStatus ( 'ACTIVE');
	  	$session->setUsrUid ( $uid );
	  	$session->setSesRemoteIp ( $_SERVER['REMOTE_ADDR'] );
	  	$session->setSesInitDate ( date ('Y-m-d H:i:s') );
	  	$session->setSesDueDate  ( date ('Y-m-d H:i:s', mktime(date('H'),date('i')+5, date('s'), date('m'),date('d'),date('Y') ) ) );
	  	$session->setSesEndDate ( '' );
	  	$session->Save();
		  	  
	  	//save the session in DataBase
	  	return $wsResponse;
    }
    catch ( Exception $e ) {
    	$wsResponse = unserialize ( $e->getMessage() );
	  	return $wsResponse;
    }		

	}

	public function processList( ) {
   try {	
  	  $result  = array();
  	  $oCriteria = new Criteria('workflow');
      $oCriteria->add(ProcessPeer::PRO_STATUS ,  'ACTIVE' );
      $oDataset = ProcessPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      
      while ($aRow = $oDataset->getRow()) {
      	$oProcess = new Process();
      	$arrayProcess = $oProcess->Load( $aRow['PRO_UID'] );
      	$result[] = array ( 'guid' => $aRow['PRO_UID'], 'name' => $arrayProcess['PRO_TITLE'] );
      	$oDataset->next();
      }
      return $result;
    }
    catch ( Exception $e ) {
    	$result[] = array ( 'guid' => $e->getMessage(), 'name' => $e->getMessage() );
      return $result;
    }		
	}

	public function roleList( ) {
   try {	
  	  $result  = array();
      G::LoadClass("BasePeer" );
      G::LoadClass("ArrayPeer" );

      $RBAC =& RBAC::getSingleton();
      $RBAC->initRBAC();
      $oCriteria = $RBAC->listAllRoles ();
      $oDataset = GulliverBasePeer::doSelectRs ( $oCriteria);;
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      
      while ($aRow = $oDataset->getRow()) {
      	$result[] = array ( 'guid' => $aRow['ROL_UID'], 'name' => $aRow['ROL_CODE'] );
      	$oDataset->next();
      }
      
      return $result;
    }
    catch ( Exception $e ) {
    	$result[] = array ( 'guid' => $e->getMessage(), 'name' => $e->getMessage() );
      return $result;
    }		
	}

	public function groupList( ) {
   try {	
  	  $result  = array();
  	  $oCriteria = new Criteria('workflow');
      $oCriteria->add(GroupwfPeer::GRP_STATUS ,  'ACTIVE' );
      $oDataset = GroupwfPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      
      while ($aRow = $oDataset->getRow()) {
      	$oGroupwf = new Groupwf();
      	$arrayGroupwf = $oGroupwf->Load( $aRow['GRP_UID'] );
      	$result[] = array ( 'guid' => $aRow['GRP_UID'], 'name' => $arrayGroupwf['GRP_TITLE'] );
      	//$result[] = array ( 'guid' => $aRow['GRP_UID'], 'name' => $aRow['GRP_UID'] );
      	$oDataset->next();
      }
      return $result;
    }
    catch ( Exception $e ) {
    	$result[] = array ( 'guid' => $e->getMessage(), 'name' => $e->getMessage() );
      return $result;
    }		
	}
	
	public function caseList( ) {
   try {	
  	  $result  = array();
  	  $oCriteria = new Criteria('workflow');
      $oCriteria->add(ApplicationPeer::APP_STATUS ,  array('TO_DO','DRAFT'), Criteria::IN);      
      $oDataset = ApplicationPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      
      while ($aRow = $oDataset->getRow()) {      	
      	$result[] = array ( 'guid' => $aRow['APP_UID'], 'name' => $aRow['APP_UID'] );
      	$oDataset->next();
      }
      return $result;
    }
    catch ( Exception $e ) {
    	$result[] = array ( 'guid' => $e->getMessage(), 'name' => $e->getMessage() );
      return $result;
    }		
	}

	public function userList( ) {
   try {	
  	  $result  = array();
  	  $oCriteria = new Criteria('workflow');
      $oCriteria->add(UsersPeer::USR_STATUS ,  'ACTIVE' );
      $oDataset = UsersPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      
      while ($aRow = $oDataset->getRow()) {
      	//$oProcess = new User();
      	//$arrayProcess = $oUser->Load( $aRow['PRO_UID'] );
      	$result[] = array ( 'guid' => $aRow['USR_UID'], 'name' => $aRow['USR_USERNAME'] );
      	$oDataset->next();
      }
      return $result;
    }
    catch ( Exception $e ) {
    	$result[] = array ( 'guid' => $e->getMessage(), 'name' => $e->getMessage() );
      return $result;
    }		
	}
	
	public function sendMessage( $sessionId, $caseId, $message) {
   try {   			
				G::LoadClass('case'); 						
		    $oCase = new Cases();
		    
		    $Fields['1']='xUNO';
		    $Fields['2']='xDOS';
		    $Fields['3']='xTRES';
		    $Fields['4']='xCUATRO';
		    $Fields['5']='xCINCO';
		  
        $oldFields = $oCase->loadCase( $caseId );
        $oldFields['APP_DATA'] = array_merge( $oldFields['APP_DATA'], $Fields);
        
		    $up_case = $oCase->updateCase($caseId, $oldFields);				            
	      $result = new wsResponse (0, "Sucessful");	      
	      return $result;
    }
    catch ( Exception $e ) {
      $result = new wsResponse (100, $e->getMessage());
      return $result;
    }    
	}
	
	
	public function createUser($sessionId, $userId, $firstname, $lastname, $email, $role, $password) {
   try {
   			global $RBAC;  
   			$RBAC->initRBAC();

				$user=$RBAC->verifyUser($userId);	        
        if($user==1)
        {  $result = new wsResponse (7, "User ID: $userId already exist!!!");
           return $result;
        }
        	  	  	  						  	  	  	
	  	  $rol=$RBAC->loadById($role);
	  	  if(!is_array($rol))
	  	  {		$very_rol=$RBAC->verifyByCode($role);
	  	  	  if($very_rol==0)
	  	  	  {		$result = new wsResponse (6, "Invalid role: $role");
	  	  	  		return $result;
	  	  	  }		
	  	  }	  	  	  	  
	  	  
	  	  $aData['USR_USERNAME']    = $userId;
				$aData['USR_PASSWORD']    = md5($password);
				$aData['USR_FIRSTNAME']   = $firstname;
				$aData['USR_LASTNAME']    = $lastname;
				$aData['USR_EMAIL']       = $email;
				$aData['USR_DUE_DATE']    = mktime(0, 0, 0, date("m"), date("d"), date("Y")+1);
				$aData['USR_CREATE_DATE'] = date('Y-m-d H:i:s');
				$aData['USR_UPDATE_DATE'] = date('Y-m-d H:i:s');  	  
	  	  $aData['USR_STATUS']      = 1;	  	
	  	  
				$sUserUID                 = $RBAC->createUser($aData,  $rol['ROL_CODE']);		
				
				$aData['USR_UID']         = $sUserUID;
				$aData['USR_PASSWORD']    = md5($sUserUID);					
				$aData['USR_STATUS']      = 'ACTIVE';			
				$aData['USR_COUNTRY']     = 'US';
				$aData['USR_CITY']        = 'FL';
				$aData['USR_LOCATION']    = 'MIA';
				$aData['USR_ADDRESS']     = ''; 
				$aData['USR_PHONE']       = '';							
				$aData['USR_ZIP_CODE']    = '33314';				
				$aData['USR_POSITION']    = '';
				$aData['USR_RESUME']      = '';
				$aData['USR_BIRTHDAY']    = date('Y-m-d');	
				$aData['USR_ROLE']        = $rol['ROL_CODE'];
	    	  
	  	  $oUser = new Users();
			  $oUser->create($aData);
			  
	      $result = new wsResponse (0, "User $firstname $lastname [$userId] created sucessful.");
	      
	      return $result;
    }
    catch ( Exception $e ) {
      $result = new wsResponse (100, $e->getMessage());
      return $result;
    }    
	}
	
	public function assignUserToGroup($sessionId, $userId, $groupId) {
   try {   			
				G::LoadClass('groups');  	      	      	    
  	    $groups = new Groups;
  	    
  	    $very_user=$groups->verifyUsertoGroup( $groupId, $userId);								
			  if($very_user==1)
			  { 
			  	$result = new wsResponse (8, "User exist in the group");
	      	return $result;
			  }  	      	    
  	    $groups->addUserToGroup( $groupId, $userId);								
		    		  
	      $result = new wsResponse (0, "User assigned to group sucessful");
	      
	      return $result;
    }
    catch ( Exception $e ) {
      $result = new wsResponse (100, $e->getMessage());
      return $result;
    }    
	}
	
	public function sendVariables($sessionId, $caseId, $variables) {
   try {   			
				G::LoadClass('case'); 						
		    $oCase = new Cases();

	      //$result = new wsResponse (123, print_r ( $variables,1) );	      //
	      //return $result;
		    foreach ( $variables as $key=>$val ) {
		    	$Fields[ $val->name ]= $val->value ;
		    }
        $cant = count ( $Fields );
		    
        $oldFields = $oCase->loadCase( $caseId );
        $oldFields['APP_DATA'] = array_merge( $oldFields['APP_DATA'], $Fields );
		    $up_case = $oCase->updateCase($caseId, $oldFields);				            
	      $result = new wsResponse (0, "$cant variables received.");	      
	      return $result;
    }
    catch ( Exception $e ) {
      $result = new wsResponse (100, $e->getMessage());
      return $result;
    }    
	}
	///OJASO AUMENTAR LA CFUNCTION PARA VER USERS A TRAVES DE USERS
	public function newCase($sessionId, $processId, $taskId, $variables) {
   try { 
   	
   			G::LoadClass('case'); 						
		    $oCase = new Cases();  							
				G::LoadClass('processes'); 
				$oProcesses = new Processes();
				$pro = $oProcesses->processExists($processId);
				
				if(!$pro)
				{  $result = new wsResponse (9, "Invalid process $processId!!");	      
	          return $result;
				}
				
				if($taskId=='')
				{
					 $tasks = $oProcesses->getStartingTaskForUser($processId,$pro); //POR EL MOMENTO PRO ES EL USER POR DEFAULT USAR CLASE 
					  
	          
	         $case = $oCase->startCase($tasks[0]['TAS_UID'], '12090688047fa3cfda74f91.34325182');
	         $result = new wsResponse (0, print_r($case,1));	      
	      return $result;
				}
				
				
			
        		   
		  
        $oldFields = $oCase->loadCase( $caseId );
        $oldFields['APP_DATA'] = array_merge( $oldFields['APP_DATA'], $Fields);
        
		    $up_case = $oCase->updateCase($caseId, $oldFields);				            
	      $result = new wsResponse (0, "Sucessful");	      
	      return $result;
    }
    catch ( Exception $e ) {
      $result = new wsResponse (100, $e->getMessage());
      return $result;
    }    
	}
	
	public function newCaseImpersonate($sessionId, $processId, $userId, $variables) {
   try {   			
				G::LoadClass('case'); 						
		    $oCase = new Cases();
		    
		    $Fields['1']='xUNO';
		    $Fields['2']='xDOS';
		    $Fields['3']='xTRES';
		    $Fields['4']='xCUATRO';
		    $Fields['5']='xCINCO';
		  
        $oldFields = $oCase->loadCase( $caseId );
        $oldFields['APP_DATA'] = array_merge( $oldFields['APP_DATA'], $Fields);
        
		    $up_case = $oCase->updateCase($caseId, $oldFields);				            
	      $result = new wsResponse (0, "Sucessful");	      
	      return $result;
    }
    catch ( Exception $e ) {
      $result = new wsResponse (100, $e->getMessage());
      return $result;
    }    
	}
	
	public function derivateCase($sessionId, $caseId) {
   try {   			
				G::LoadClass('case'); 						
		    $oCase = new Cases();
		    
		    $Fields['1']='xUNO';
		    $Fields['2']='xDOS';
		    $Fields['3']='xTRES';
		    $Fields['4']='xCUATRO';
		    $Fields['5']='xCINCO';
		  
        $oldFields = $oCase->loadCase( $caseId );
        $oldFields['APP_DATA'] = array_merge( $oldFields['APP_DATA'], $Fields);
        
		    $up_case = $oCase->updateCase($caseId, $oldFields);				            
	      $result = new wsResponse (0, "Sucessful");	      
	      return $result;
    }
    catch ( Exception $e ) {
      $result = new wsResponse (100, $e->getMessage());
      return $result;
    }    
	}
	
}