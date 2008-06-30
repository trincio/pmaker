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

/**
* @Last Modify: 26.06.2008 10:05:00
* @Last modify by: Erik Amaru Ortiz <erik@colosa.com>
* @Last Modify comment(26.06.2008): the session expired verification was removed from here to soap class
*/

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
require_once ( "classes/model/Content.php" );
G::LoadClass('pmScript');
G::LoadClass('wsResponse');

class wsBase
{
	function __construct() {
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
	
	public function caseList( $userId ) {
		try {
			
			$result  = array();
			$oCriteria = new Criteria('workflow');
			$del = DBAdapter::getStringDelimiter();
			$oCriteria->addSelectColumn(ApplicationPeer::APP_UID);
			$oCriteria->addAsColumn('CASE_TITLE', 'C1.CON_VALUE' );
			$oCriteria->addAlias("C1",  'CONTENT');
			$caseTitleConds = array();
			$caseTitleConds[] = array( ApplicationPeer::APP_UID ,  'C1.CON_ID'  );
			$caseTitleConds[] = array( 'C1.CON_CATEGORY' , $del . 'APP_TITLE' . $del );
			$caseTitleConds[] = array( 'C1.CON_LANG' ,    $del . SYS_LANG . $del );
			$oCriteria->addJoinMC($caseTitleConds ,    Criteria::LEFT_JOIN);

			$oCriteria->addJoin(ApplicationPeer::APP_UID, AppDelegationPeer::APP_UID, Criteria::LEFT_JOIN);

			$oCriteria->add(ApplicationPeer::APP_STATUS ,  array('TO_DO','DRAFT'), Criteria::IN);
			$oCriteria->add(AppDelegationPeer::USR_UID, $userId );
			$oCriteria->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
			$oDataset = ApplicationPeer::doSelectRS($oCriteria);
			$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
			$oDataset->next();

			while ($aRow = $oDataset->getRow()) {
				$result[] = array ( 'guid' => $aRow['APP_UID'], 'name' => $aRow['CASE_TITLE'] );
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

	public function taskList( $userId ) {
		try {			
			$result  = array();
			$oCriteria = new Criteria('workflow');
			$del = DBAdapter::getStringDelimiter();
			$oCriteria->addSelectColumn(TaskPeer::TAS_UID);
			$oCriteria->addAsColumn('TAS_TITLE', 'C1.CON_VALUE' );
			$oCriteria->addAlias("C1",  'CONTENT');
			$tasTitleConds = array();
			$tasTitleConds[] = array( TaskPeer::TAS_UID ,  'C1.CON_ID'  );
			$tasTitleConds[] = array( 'C1.CON_CATEGORY' , $del . 'TAS_TITLE' . $del );
			$tasTitleConds[] = array( 'C1.CON_LANG' ,    $del . SYS_LANG . $del );
			$oCriteria->addJoinMC($tasTitleConds ,    Criteria::LEFT_JOIN);
			
			$oCriteria->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);
			
			$oCriteria->add(TaskUserPeer::USR_UID, $userId );
			$oDataset = TaskPeer::doSelectRS($oCriteria);
			$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
			$oDataset->next();
			
			while ($aRow = $oDataset->getRow()) {
				$result[] = array ( 'guid' => $aRow['TAS_UID'], 'name' => $aRow['TAS_TITLE'] );
				$oDataset->next();
			}
			return $result;
		}
		catch ( Exception $e ) {
			$result[] = array ( 'guid' => $e->getMessage(), 'name' => $e->getMessage() );
			return $result;
		}
	}

	public function sendMessage($caseId, $message) {
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

	public function createUser( $userId, $firstname, $lastname, $email, $role, $password) {
		try {	
			global $RBAC;
			$RBAC->initRBAC();
			$user=$RBAC->verifyUser($userId);
			if($user==1){
				$result = new wsResponse (7, "User ID: $userId already exist!!!");
				return $result;
			}
															
			$rol=$RBAC->loadById($role);
			if(!is_array($rol)){
				$very_rol=$RBAC->verifyByCode($role);
				if($very_rol==0){
					$result = new wsResponse (6, "Invalid role: $role");
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

	public function assignUserToGroup( $userId, $groupId) {
		try {
			G::LoadClass('groups');
			$groups = new Groups;
			
			$very_user=$groups->verifyUsertoGroup( $groupId, $userId);
			if($very_user==1){
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

	public function sendVariables($caseId, $variables) { 
		try {
			if(is_array($variables)) {
				$cant = count ( $variables );
				if($cant > 0) {
					G::LoadClass('case');
					$oCase = new Cases();

					$oldFields = $oCase->loadCase( $caseId );
					$oldFields['APP_DATA'] = array_merge( $oldFields['APP_DATA'], $variables );
					$up_case = $oCase->updateCase($caseId, $oldFields);
					$result = new wsResponse (0, "$cant variables received.");
					return $result;
				} else {
					$result = new wsResponse (100, "The variables param lenght is zero");
					return $result;
				}
			} else {
				$result = new wsResponse (100, "The variables param is not a array!");
				return $result;
			}
		}
		catch ( Exception $e ) {
			$result = new wsResponse (100, $e->getMessage());
			return $result;
		}
	}

	public function newCase($processId, $userId, $taskId, $variables) {
		try {
			if(is_array($variables)) {
				if(count($variables)>0){
					$c=count($variables);
					
					$Fields = $variables;
					if($c == 0) { //Si no tenenmos ninguna variables en el array variables.
						$result = new wsResponse (10, "Array of variables is empty");
						return $result;
					}
				}
			} else {
				$result = new wsResponse (100, "The variables param is not a array!");
				return $result;
			}

			G::LoadClass('processes');
			$oProcesses = new Processes();
			$pro = $oProcesses->processExists($processId);

			if(!$pro)
			{  $result = new wsResponse (11, "Invalid process $processId!!");
			return $result;
			}

			G::LoadClass('case');
			$oCase = new Cases();

			if($taskId=='')	{
				$tasks  = $oProcesses->getStartingTaskForUser($processId, $userId);
				$numTasks=count($tasks);

				if($numTasks==1){
					$case   = $oCase->startCase($tasks[0]['TAS_UID'], $userId);
					$caseId = $case['APPLICATION'];

					$oldFields = $oCase->loadCase( $caseId );

					$oldFields['APP_DATA'] = array_merge( $oldFields['APP_DATA'], $Fields);

					$up_case = $oCase->updateCase($caseId, $oldFields);
					$result = new wsResponse (0, "Sucessful");
					return $result;
				} else {
					if($numTasks==0){
						$result = new wsResponse (12, "No staring task defined");
						return $result;
					}
					if($numTasks > 1){
						$result = new wsResponse (13, "Multiple starting task");
						return $result;
					}
				}
			} else {
				require_once 'classes/model/Task.php';
				$oTask = new Task();
				$task  = $oTask->taskExists( $taskId );
				if(!$task){
					$result = new wsResponse (14, "Task invalid");
					return $result;
				}

				$tasks  = $oProcesses->getStartingTaskForUser($processId,$userId);
				$numTasks=count($tasks);
				if($numTasks==1) {
					$case   = $oCase->startCase($taskId, $userId);
					$caseId = $case['APPLICATION'];
					$oldFields = $oCase->loadCase( $caseId );

					$oldFields['APP_DATA'] = array_merge( $oldFields['APP_DATA'], $Fields);
						$up_case = $oCase->updateCase($caseId, $oldFields);
					$result = new wsResponse (0, "Sucessful");
					return $result;
				} else {
					if($numTasks==0) {
						$result = new wsResponse (12, "No staring task defined");
						return $result;
					}
					if($numTasks > 1) {
						$result = new wsResponse (13, "Multiple starting task");
						return $result;
					}
				}
			}
		}
		catch ( Exception $e ) {
			$result = new wsResponse (100, $e->getMessage());
			return $result;
		}
	}

	public function newCaseImpersonate($processId, $userId, $variables) {
		try {
			if(is_array($variables)) {
				if(count($variables)>0) {
					$c=count($variables);
					$Fields = $variables;
					if($c == 0) { //Si no tenenmos ninguna variables en el array variables.
						$result = new wsResponse (10, "Array of variables is empty");
						return $result;
					}
				}
			} else {
				$result = new wsResponse (100, "The variables param is not a array!");
				return $result;
			}
			
			G::LoadClass('processes');
			$oProcesses = new Processes();
			$pro = $oProcesses->processExists($processId);

			if(!$pro) {
				$result = new wsResponse (11, "Invalid process $processId!!");
				return $result;
			}

			G::LoadClass('case');
			$oCase = new Cases();

			$tasks  = $oProcesses->getStartingTaskForUser($processId, $userId);
			$numTasks=count($tasks);

			if($numTasks==1) {
				$case   = $oCase->startCase($tasks[0]['TAS_UID'], $userId);
				$caseId = $case['APPLICATION'];

				$oldFields = $oCase->loadCase( $caseId );
				
				$oldFields['APP_DATA'] = array_merge( $oldFields['APP_DATA'], $Fields);
			
				$up_case = $oCase->updateCase($caseId, $oldFields);
				$result = new wsResponse (0, "Sucessful");
				return $result;
			}
			else {
				if($numTasks==0) {
					$result = new wsResponse (12, "No staring task defined");
					return $result;
				}
				if($numTasks > 1){
					$result = new wsResponse (13, "Multiple starting task");
					return $result;
				}
			}
		}
		catch ( Exception $e ) {
			$result = new wsResponse (100, $e->getMessage());
			return $result;
		}
	}

	public function derivateCase($caseId, $delIndex) {
		try { $sStatus = 'TO_DO';
			/*
			$result = new wsResponse (0, print_r($appFields['STATUS'],1));
				return $result;
			*/
			require_once ("classes/model/AppDelegation.php");
			require_once ("classes/model/Route.php");
			G::LoadClass('case');
			G::LoadClass('derivation');
			
			$oAppDel = new AppDelegation();
			$appdel  = $oAppDel->Load($caseId, $delIndex);
			
			$aData['APP_UID']   = $caseId;
			$aData['DEL_INDEX'] = $delIndex;
			
			$oDerivation = new Derivation();
			$derive  = $oDerivation->prepareInformation($aData);
		
			foreach ( $derive as $key=>$val ) {
				if($val['NEXT_TASK']['TAS_ASSIGN_TYPE']=='MANUAL')
				{
					$result = new wsResponse (15, "The task is a Manual assined");
					return $result;
				}
				$nextDelegations[] = array(
					'TAS_UID' => $val['NEXT_TASK']['TAS_UID'],
					'USR_UID' => $val['NEXT_TASK']['USER_ASSIGNED']['USR_UID'],
					'TAS_ASSIGN_TYPE' =>	$val['NEXT_TASK']['TAS_ASSIGN_TYPE'],
					'TAS_DEF_PROC_CODE' => $val['NEXT_TASK']['TAS_DEF_PROC_CODE'],
					'DEL_PRIORITY'	=>	$appdel['DEL_PRIORITY']
				);
			}
		
			//load data
			$oCase     = new Cases ();
			$appFields = $oCase->loadCase( $caseId );
					
			//Execute triggers before derivation
			$appFields['APP_DATA']  = $oCase->ExecuteTriggers ( $derive['TAS_UID'], 'ASSIGN_TASK', -2, 'BEFORE', $appFields['APP_DATA'] );
			$appFields['DEL_INDEX'] = $delIndex;
			$appFields['TAS_UID']   = $derive['TAS_UID'];
			//Save data - Start
			$oCase->updateCase ( $caseId, $appFields );
			//Save data - End
						
					$row  = array();
			$oCriteria = new Criteria('workflow');
			$del = DBAdapter::getStringDelimiter();
			$oCriteria->addSelectColumn(RoutePeer::ROU_TYPE);
			$oCriteria->addSelectColumn(RoutePeer::ROU_NEXT_TASK);
			$oCriteria->add(RoutePeer::TAS_UID, $appdel['TAS_UID']);
			$oDataset = TaskPeer::doSelectRS($oCriteria);
			$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
			$oDataset->next();
			while ($aRow = $oDataset->getRow()) {
				$row[] = array ( 'ROU_TYPE' => $aRow['ROU_TYPE'], 'ROU_NEXT_TASK' => $aRow['ROU_NEXT_TASK'] );
				$oDataset->next();
			}
		
			//verificar cant row != cant derive
				
			//derivate case
			$aCurrentDerivation = array(
				'APP_UID'    => $caseId,
				'DEL_INDEX'  => $delIndex,
				'APP_STATUS' => $sStatus,
				'TAS_UID'    => $appdel['TAS_UID'],
				'ROU_TYPE'   => $row[0]['ROU_TYPE']
			);
				
			$oDerivation->derivate( $aCurrentDerivation, $nextDelegations );
		
			$result = new wsResponse (0, "Sucessful");
			return $result;
		}
		catch ( Exception $e ) {
			$result = new wsResponse (100, $e->getMessage());
			return $result;
		}
	}

	public function taskCase( $caseId ) {
		try {
			$result  = array();
			$oCriteria = new Criteria('workflow');
			$del = DBAdapter::getStringDelimiter();
			$oCriteria->addSelectColumn(AppDelegationPeer::DEL_INDEX);
			
			$oCriteria->addAsColumn('TAS_TITLE', 'C1.CON_VALUE' );
			$oCriteria->addAlias("C1",  'CONTENT');
			$tasTitleConds = array();
			$tasTitleConds[] = array( AppDelegationPeer::TAS_UID ,  'C1.CON_ID'  );
			$tasTitleConds[] = array( 'C1.CON_CATEGORY' , $del . 'TAS_TITLE' . $del );
			$tasTitleConds[] = array( 'C1.CON_LANG' ,    $del . SYS_LANG . $del );
			$oCriteria->addJoinMC($tasTitleConds ,    Criteria::LEFT_JOIN);

			$oCriteria->add(AppDelegationPeer::APP_UID, $caseId );
			$oCriteria->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');
			$oCriteria->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL );
			$oDataset = AppDelegationPeer::doSelectRS($oCriteria);
			$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
			$oDataset->next();
			
			while ($aRow = $oDataset->getRow()) {
				$result[] = array ( 'guid' => $aRow['DEL_INDEX'], 'name' => $aRow['TAS_TITLE'] );
				$oDataset->next();
			}
			return $result;
		}
		catch ( Exception $e ) {
			$result[] = array ( 'guid' => $e->getMessage(), 'name' => $e->getMessage() );
			return $result;
		}
	}
	
}