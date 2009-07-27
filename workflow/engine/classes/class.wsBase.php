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
G::LoadClass('case');

class wsBase
{
	function __construct() {
	}

	public function login( $userid, $password ) {
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
			//$oCriteria->add(ProcessPeer::PRO_STATUS ,  'ACTIVE' );
			$oCriteria->add(ProcessPeer::PRO_STATUS, 'DISABLED', Criteria::NOT_EQUAL);
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
			$oCriteria->addSelectColumn(ApplicationPeer::APP_STATUS);
			$oCriteria->addSelectColumn(AppDelegationPeer::DEL_INDEX);
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
				$result[] = array ( 'guid' => $aRow['APP_UID'], 'name' => $aRow['CASE_TITLE'], 'status' => $aRow['APP_STATUS'], 'delIndex' => $aRow['DEL_INDEX'] );
				$oDataset->next();
			}
			return $result;
		}
		catch ( Exception $e ) {
			$result[] = array ( 'guid' => $e->getMessage(), 'name' => $e->getMessage(), 'status' => $e->getMessage() , 'status' => $e->getMessage() );
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

	public function sendMessage($caseId, $sFrom, $sTo, $sCc, $sBcc, $sSubject, $sTemplate, $appFields = null ) {
		try {
			G::LoadClass('case');
      G::LoadClass('spool');

    	$aSetup = getEmailConfiguration();

      $oSpool = new spoolRun();
      $oSpool->setConfig(array('MESS_ENGINE'   => $aSetup['MESS_ENGINE'],
                               'MESS_SERVER'   => $aSetup['MESS_SERVER'],
                               'MESS_PORT'     => $aSetup['MESS_PORT'],
                               'MESS_ACCOUNT'  => $aSetup['MESS_ACCOUNT'],
                               'MESS_PASSWORD' => $aSetup['MESS_PASSWORD'],
                               'SMTPAuth'      => $aSetup['MESS_RAUTH'] ));


			$oCase = new Cases();
  	  $oldFields = $oCase->loadCase( $caseId );

      $pathEmail = PATH_DATA_SITE . 'mailTemplates' . PATH_SEP . $oldFields['PRO_UID'] . PATH_SEP;
			$fileTemplate = $pathEmail . $sTemplate;
			@mkdir( $pathEmail, 0777,true);

			if ( ! file_exists ( $fileTemplate ) ) {
			  $result = new wsResponse (100, "template file: '$fileTemplate' doesn't exists."  );
			  return $result;
			}

      if ( $appFields == null ) {
  			$Fields = $oldFields['APP_DATA'];
      }
      else
        $Fields = $appFields;

      $templateContents = file_get_contents ( $fileTemplate );

      //desde aki
      //$sContent    = G::unhtmlentities($sContent);
  		$iAux        = 0;
  	  $iOcurrences = preg_match_all('/\@(?:([\>])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*(?:[\\\\][\w\W])?)*)\))((?:\s*\[[\'"]?\w+[\'"]?\])+)?/',  $templateContents, $aMatch, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);

  	  if ($iOcurrences) {
  	    for($i = 0; $i < $iOcurrences; $i++) {
  	      preg_match_all('/@>' . $aMatch[2][$i][0] . '([\w\W]*)' . '@<' . $aMatch[2][$i][0] . '/', $templateContents, $aMatch2, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
  	      $sGridName       = $aMatch[2][$i][0];
  	      $sStringToRepeat = $aMatch2[1][0][0];
  	      if (isset($Fields[$sGridName])) {
  	        if (is_array($Fields[$sGridName])) {
  	          $sAux = '';
  	          foreach ($Fields[$sGridName] as $aRow) {
  	            $sAux .= G::replaceDataField($sStringToRepeat, $aRow);
  	          }
  	        }
  	      }
  	      $templateContents = str_replace('@>' . $sGridName . $sStringToRepeat . '@<' . $sGridName, $sAux, $templateContents);
  	    }
  	  }
      //hata aki
      $sBody = G::replaceDataField( $templateContents, $Fields);

      if ($sFrom != '') {
        $sFrom = $sFrom . ' <' . $aSetup['MESS_ACCOUNT'] . '>';
      }
      else {
        $sFrom = $aSetup['MESS_ACCOUNT'];
      }

      $messageArray = array('msg_uid'          => '',
                            'app_uid'          => $caseId,
                            'del_index'        => 0,
                            'app_msg_type'     => 'TRIGGER',
                            'app_msg_subject'  => $sSubject,
                            'app_msg_from'     => $sFrom,
                            'app_msg_to'       => $sTo,
                            'app_msg_body'     => $sBody,
                            'app_msg_cc'       => $sCc,
                            'app_msg_bcc'      => $sBcc,
                            'app_msg_attach'   => '',
                            'app_msg_template' => '',
                            'app_msg_status'   => 'pending');

      $oSpool->create( $messageArray );

      $oSpool->sendMail();

      if ( $oSpool->status == 'sent' )
			  $result = new wsResponse (0, "message sent : $sTo" );
			else
			  $result = new wsResponse (100, $oSpool->status . ' ' . $oSpool->error . print_r ($aSetup ,1 ) );
			return $result;
		}
		catch ( Exception $e ) {
			$result = new wsResponse (100, $e->getMessage());
			return $result;
		}
	}

	public function getCaseInfo($caseId, $iDelIndex ) {
		try {
			G::LoadClass('case');
			$oCase = new Cases();
  	  $aRows = $oCase->loadCase( $caseId, $iDelIndex );
  	  if ( count($aRows) == 0 ) {
    	  $result = new wsResponse (27, "Case $caseNumber doesn't exists." );
  	    return $result;
  	  }
//  	  if ( count($aRows) > 1 ) {
//    	  $result = new wsResponse (27, "There are more than one case with the same CaseNumber $caseNumber." );
//  	    return $result;
//  	  }

  	  $result = new wsResponse (0, "case found" );
  	  $result->caseId = $aRows['APP_UID'];
  	  $result->caseNumber = $aRows['APP_NUMBER'];
  	  $result->caseStatus = $aRows['APP_STATUS'];
  	  $result->caseParalell = $aRows['APP_PARALLEL'];
  	  $result->caseCurrentUser = $aRows['APP_CUR_USER'];
			return $result;
		}
		catch ( Exception $e ) {
			$result = new wsResponse (100, $e->getMessage());
			return $result;
		}
	}

	public function createUser( $userId, $firstname, $lastname, $email, $role, $password) {
		try {
			if($userId=='')
			{  $result = new wsResponse (20, "User ID is required");
				 return $result;
			}

			if($password=='')
			{  $result = new wsResponse (21, "Password is required");
				 return $result;
			}

			if($firstname=='')
			{  $result = new wsResponse (22, "Firstname is required");
				 return $result;
			}

			global $RBAC;
			$RBAC->initRBAC();

			$user = $RBAC->verifyUser($userId);
			if ( $user == 1){
				$result = new wsResponse (7, "User ID: $userId already exist!!!");
				return $result;
			}

			$rol=$RBAC->loadById($role);
			if ( is_array($rol) ){
				$strRole = $rol['ROL_CODE'];
		  }
		  else {
				$very_rol = $RBAC->verifyByCode($role);
				if ( $very_rol==0 ){
					$result = new wsResponse (6, "Invalid role: $role");
					return $result;
				}
				$strRole = $role;
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

			$sUserUID                 = $RBAC->createUser($aData,  $strRole );

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
			$aData['USR_ROLE']        = $strRole ;

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
			global $RBAC;
			$RBAC->initRBAC();
			$user=$RBAC->verifyUserId($userId);
			if($user==0){
				$result = new wsResponse (3, "User not registered in the system");
				return $result;
			}

			G::LoadClass('groups');
			$groups = new Groups;
			$very_group=$groups->verifyGroup( $groupId );
			if($very_group==0){
				$result = new wsResponse (23, "Group not registered in the system");
				return $result;
			}

      /*  this code is unnecesary because the restriccions is over the user is calling this method, not over the user we are changing group.
			$oRBAC = RBAC::getSingleton();
      $oRBAC->loadUserRolePermission($oRBAC->sSystem, $userId);
      $aPermissions = $oRBAC->aUserInfo[$oRBAC->sSystem]['PERMISSIONS'];
      foreach ($aPermissions as $aPermission) {
        if ($aPermission['PER_CODE'] == 'PM_FACTORY')
        {
          exit;
        }
        else
        {
        	$result = new wsResponse (24, "You do not have privileges");
					return $result;
        }
      }
      */

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
		//delegation where app uid (caseId) y usruid(session) ordenar delindes descendente y agaarr el primero
		//delfinishdate != null error
		try {
			require_once ("classes/model/AppDelegation.php");
			$oCriteria = new Criteria('workflow');
			$oCriteria->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE);
			$oCriteria->add(AppDelegationPeer::APP_UID, $caseId);

			$oCriteria->addDescendingOrderByColumn(AppDelegationPeer::DEL_INDEX);
			$oDataset = AppDelegationPeer::doSelectRS($oCriteria);
			$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                        $cnt = 0;
                        while($oDataset->next()) {
                          $aRow = $oDataset->getRow();
                            if($aRow['DEL_FINISH_DATE']==NULL)
                            {
                                $cnt++;
                            }
                        }

                        if($cnt == 0){
                            $result = new wsResponse (18, 'This case is already closed');
                            return $result;
                        }
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
					$result = new wsResponse (100, "The variables param length is zero");
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

	public function getVariables($caseId, $variables) {
		//delegation where app uid (caseId) y usruid(session) ordenar delindes descendente y agaarr el primero
		//delfinishdate != null error
		try {
			require_once ("classes/model/AppDelegation.php");
			$oCriteria = new Criteria('workflow');
			$oCriteria->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE);
			$oCriteria->add(AppDelegationPeer::APP_UID, $caseId);

			$oCriteria->addDescendingOrderByColumn(AppDelegationPeer::DEL_INDEX);
			$oDataset = AppDelegationPeer::doSelectRS($oCriteria);
			$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

      $cnt = 0;
      while($oDataset->next()) {
        $aRow = $oDataset->getRow();
          if($aRow['DEL_FINISH_DATE']==NULL)
          {
              $cnt++;
          }
      }

      if($cnt == 0){
          $result = new wsResponse (18, 'This case is already closed');
          return $result;
      }

			if(is_array($variables)) {
				$cant = count ( $variables );
				if($cant > 0) {
					G::LoadClass('case');
					$oCase = new Cases();

					$caseFields = $oCase->loadCase( $caseId );
					$oldFields = $caseFields['APP_DATA'];
					$resFields = array();
					foreach ( $variables as $key => $val ) {
  				  //$resFields[ $val ] =  $oldFields[ $val ] ;
						if ( isset ( $oldFields[ $val ] ) )
						  $resFields[ $val ] =  $oldFields[ $val ] ;
					}
					//$cant = count ( $resFields );
					//$result = new wsResponse (0, "$cant variables received." . print_r ($resFields, 1) );
					return $resFields;
				}
				else {
					$result = new wsResponse (100, "The variables param length is zero");
					return $result;
				}
			}
			else {
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
			$Fields = array();
			if( is_array($variables) ) {
				if( count($variables)>0 ){
					$c=count($variables);

					$Fields = $variables;
					if($c == 0) { //Si no tenenmos ninguna variables en el array variables.
						$result = new wsResponse (10, "Array of variables is empty");
						return $result;
					}
				}
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
				G::LoadClass('tasks');
				$oTask = new Tasks();
				$startingTasks = $oCase->getStartCases($userId);
				array_shift ($startingTasks); //remove the first row, the header row
				$founded = '';
				$tasksInThisProcess = 0;
				$validTaskId = $taskId;
				foreach ( $startingTasks as $key=> $val ) {
					if ( $val['pro_uid'] == $processId ) { $tasksInThisProcess ++; $validTaskId = $val['uid']; }
					if ( $val['uid'] == $taskId ) $founded = $val['value'];
				}

				if ( $taskId == '' ) {
				  if ( $tasksInThisProcess == 1 ) {
					  $founded = $validTaskId;
  					$taskId = $validTaskId;
	  			}
				  if ( $tasksInThisProcess > 1 ) {
						$result = new wsResponse (13, "Multiple starting task ");
						return $result;
				  }
	  		}

				if( $founded == '')
				{
				  $result = new wsResponse (16, "Task invalid or the user is not assigned to the task");
			    return $result;
				}

				$case   = $oCase->startCase($taskId, $userId);
				$caseId = $case['APPLICATION'];
				$caseNr = $case['CASE_NUMBER'];

				$oldFields = $oCase->loadCase( $caseId );

				$oldFields['APP_DATA'] = array_merge( $oldFields['APP_DATA'], $Fields);

				$up_case = $oCase->updateCase($caseId, $oldFields);
				//$result = new wsResponse (0, "Sucessful\ncase uid = $caseId \ncase number = $caseNr ");
				$result = new wsResponse (0, "Sucessful");
				$result->caseId = $caseId;
				$result->caseNumber = $caseNr;

				return $result;
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

			if($numTasks==1)
			{

				G::LoadClass('tasks');
				$oTask = new Tasks();
				$very = $oTask->verifyUsertoTask($userId, $tasks[0]['TAS_UID']);
				if(is_array($very))
				{
					if($very['TU_RELATION']==2)
				   {
						 $group=$groups->getUsersOfGroup( $tasks[0]['TAS_UID'] );
						 if(!is_array($group))
						 { $result = new wsResponse (16, "The user is not assigned to the task");
			    		 return $result;
						 }
				   }
				}
				else
				{ $result = new wsResponse (16, "The user is not assigned to the task");
			    return $result;
				}

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

	public function derivateCase($userId, $caseId, $delIndex, $bExecuteTriggersBeforeAssignment = false) {
		try { $sStatus = 'TO_DO';
			require_once ("classes/model/AppDelegation.php");
			require_once ("classes/model/Route.php");
			require_once ("classes/model/AppDelay.php");
			G::LoadClass('case');
			G::LoadClass('derivation');
			G::LoadClass('sessions');

			$varResponse = '';
			$varTriggers = "\n";

			if ($delIndex == '') {
			  $oCriteria = new Criteria('workflow');
			  $oCriteria->addSelectColumn(AppDelegationPeer::DEL_INDEX);
			  $oCriteria->add(AppDelegationPeer::APP_UID, $caseId);
			  $oCriteria->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
			  if (AppDelegationPeer::doCount($oCriteria) > 1) {
			    $result = new wsResponse (20, 'Please specify the delegation index!');
				  return $result;
			  }
			  $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
			  $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
			  $oDataset->next();
			  $aRow     = $oDataset->getRow();
			  $delIndex = $aRow['DEL_INDEX'];
			}

			$oAppDel = new AppDelegation();
			$appdel  = $oAppDel->Load($caseId, $delIndex);

			if($userId!=$appdel['USR_UID'])
			{
				$result = new wsResponse (17, "This case is assigned to another user");
				return $result;
			}

			if($appdel['DEL_FINISH_DATE']!=NULL)
			{
				$result = new wsResponse (18, 'This delegation already closed or not exists');
				return $result;
			}

			$oCriteria = new Criteria('workflow');
			$oCriteria->addSelectColumn(AppDelayPeer::APP_UID);
			$oCriteria->addSelectColumn(AppDelayPeer::APP_DEL_INDEX);
			$oCriteria->add(AppDelayPeer::APP_TYPE, '');
			$oCriteria->add($oCriteria->getNewCriterion(AppDelayPeer::APP_TYPE, 'PAUSE')->addOr($oCriteria->getNewCriterion(AppDelayPeer::APP_TYPE, 'CANCEL')));
			$oCriteria->addAscendingOrderByColumn(AppDelayPeer::APP_ENABLE_ACTION_DATE);
			$oDataset = AppDelayPeer::doSelectRS($oCriteria);
			$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
			$oDataset->next();
			$aRow = $oDataset->getRow();

			if(is_array($aRow))
			{
					if($aRow['APP_DISABLE_ACTION_USER']!=0 && $aRow['APP_DISABLE_ACTION_DATE']!='')
					{
							$result = new wsResponse (19, "This case is in status". $aRow['APP_TYPE']);
							return $result;
					}
			}

			$aData['APP_UID']   = $caseId;
			$aData['DEL_INDEX'] = $delIndex;

			//load data
			$oCase     = new Cases ();
			$appFields = $oCase->loadCase( $caseId );
			$appFields['APP_DATA']['APPLICATION'] = $caseId;

      if ($bExecuteTriggersBeforeAssignment) {
        //Execute triggers before assignment
        $aTriggers = $oCase->loadTriggers($appdel['TAS_UID'], 'ASSIGN_TASK', -1, 'BEFORE' );
        if (count($aTriggers) > 0) {
          $oPMScript = new PMScript();
          foreach ($aTriggers as $aTrigger) {
            //$appFields = $oCase->loadCase( $caseId );
     	  		//$appFields['APP_DATA']['APPLICATION'] = $caseId;
            $oPMScript->setFields( $appFields['APP_DATA'] );
            $bExecute = true;
            if ($aTrigger['ST_CONDITION'] !== '') {
              $oPMScript->setScript($aTrigger['ST_CONDITION']);
              $bExecute = $oPMScript->evaluate();
            }
            if ($bExecute) {
              $oPMScript->setScript($aTrigger['TRI_WEBBOT']);
              $oPMScript->execute();
              $varTriggers .= "Before Assignment ----------\n" . $aTrigger['TRI_WEBBOT'] . "\n";
              //$appFields = $oCase->loadCase( $caseId );
              $appFields['APP_DATA'] = $oPMScript->aFields;
              //$appFields['APP_DATA']['APPLICATION'] = $caseId;
//$var  Triggers .= "proccode " . $appFields['APP_PROC_CODE'] . "\n";
//$var  Triggers .= "pin " . $appFields['APP_DATA']['PIN'] . "\n";
        			$oCase->updateCase ( $caseId, $appFields );
            }
          }
        }
      }

			//Execute triggers before derivation
      $aTriggers = $oCase->loadTriggers($appdel['TAS_UID'], 'ASSIGN_TASK', -2, 'BEFORE' );
      if (count($aTriggers) > 0) {
        $oPMScript = new PMScript();
        foreach ($aTriggers as $aTrigger) {
          //$appFields = $oCase->loadCase( $caseId );
     			//$appFields['APP_DATA']['APPLICATION'] = $caseId;
          $oPMScript->setFields( $appFields['APP_DATA'] );
          $bExecute = true;
          if ($aTrigger['ST_CONDITION'] !== '') {
            $oPMScript->setScript($aTrigger['ST_CONDITION']);
            $bExecute = $oPMScript->evaluate();
          }
          if ($bExecute) {
            $oPMScript->setScript($aTrigger['TRI_WEBBOT']);
            $oPMScript->execute();
            $varTriggers .= "Before Derivation ----------\n" . $aTrigger['TRI_WEBBOT'] . "\n";
            //$appFields = $oCase->loadCase( $caseId );
            $appFields['APP_DATA'] = $oPMScript->aFields;
            //$appFields['APP_DATA']['APPLICATION'] = $caseId;
//$varTriggers .= "proccode " . $appFields['APP_PROC_CODE'] . "\n";
//$varTriggers .= "pin " . $appFields['APP_DATA']['PIN'] . "\n";
      			$oCase->updateCase ( $caseId, $appFields );
          }
        }
      }

      $oDerivation = new Derivation();
			$derive  = $oDerivation->prepareInformation($aData);
			if (isset($derive[1])) {
			  if ($derive[1]['ROU_TYPE'] == 'SELECT') {
			    $result = new wsResponse (21, 'Cannot derivate a "Manual" derivation using webservices.');
					return $result;
			  }
			}
			else {
			  $result = new wsResponse (22, 'Task does not have derivation rule, check process definition');
				return $result;
			}
			foreach ( $derive as $key=>$val ) {
				if($val['NEXT_TASK']['TAS_ASSIGN_TYPE']=='MANUAL')
				{
					$result = new wsResponse (15, "The task is defined for Manual assignment");
					return $result;
				}
				$nextDelegations[] = array(
																		'TAS_UID' => $val['NEXT_TASK']['TAS_UID'],
																		'USR_UID' => $val['NEXT_TASK']['USER_ASSIGNED']['USR_UID'],
																		'TAS_ASSIGN_TYPE' =>	$val['NEXT_TASK']['TAS_ASSIGN_TYPE'],
																		'TAS_DEF_PROC_CODE' => $val['NEXT_TASK']['TAS_DEF_PROC_CODE'],
																		'DEL_PRIORITY'	=>	$appdel['DEL_PRIORITY'],
																		'TAS_PARENT' => $val['NEXT_TASK']['TAS_PARENT']
																	);
				$varResponse = $varResponse . ($varResponse!=''?',':'') . $val['NEXT_TASK']['TAS_TITLE'].'('.$val['NEXT_TASK']['USER_ASSIGNED']['USR_USERNAME'].')';
			}

			$appFields['DEL_INDEX'] = $delIndex;
			if ( isset($derive['TAS_UID']) )
			  $appFields['TAS_UID']   = $derive['TAS_UID'];

			//Save data - Start
			//$appFields = $oCase->loadCase( $caseId );
			//$oCase->updateCase ( $caseId, $appFields );
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

			//derivate case
			$aCurrentDerivation = array(
				'APP_UID'    => $caseId,
				'DEL_INDEX'  => $delIndex,
				'APP_STATUS' => $sStatus,
				'TAS_UID'    => $appdel['TAS_UID'],
				'ROU_TYPE'   => $row[0]['ROU_TYPE']
			);

			$oDerivation->derivate( $aCurrentDerivation, $nextDelegations );
      $appFields = $oCase->loadCase($caseId);

			$aTriggers = $oCase->loadTriggers($appdel['TAS_UID'], 'ASSIGN_TASK', -2, 'AFTER' );
      if (count($aTriggers) > 0) {
        $oPMScript = new PMScript();
        //$appFields['APP_DATA']['APPLICATION'] = $caseId;
        $oPMScript->setFields( $appFields['APP_DATA'] );
        foreach ($aTriggers as $aTrigger) {
          $bExecute = true;
          if ($aTrigger['ST_CONDITION'] !== '') {
            $oPMScript->setScript($aTrigger['ST_CONDITION']);
            $bExecute = $oPMScript->evaluate();
          }
          if ($bExecute) {
            $oPMScript->setScript($aTrigger['TRI_WEBBOT']);
            $oPMScript->execute();
            $varTriggers .= "After Derivation ----------\n" . $aTrigger['TRI_WEBBOT'] . "\n";
            //$appFields = $oCase->loadCase( $caseId );
            $appFields['APP_DATA'] = $oPMScript->aFields;
            //$appFields['APP_DATA']['APPLICATION'] = $caseId;
            //$appFields = $oCase->loadCase( $caseId );
      			$oCase->updateCase ( $caseId, $appFields );
          }
        }
      }

      $oUser     = new Users();
      $aUser     = $oUser->load($userId);
      $sFromName = '"' . $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'] . '"';
      $oCase->sendNotifications($appdel['TAS_UID'], $nextDelegations, $appFields['APP_DATA'], $caseId, $delIndex, $sFromName);

			//Save data - Start
			//$appFields = $oCase->loadCase( $caseId );
			//$oCase->updateCase ( $caseId, $appFields );
			//Save data - End

			$result = new wsResponse (0, $varResponse . $varTriggers );
      $res = $result->getPayloadArray ();

      $res['derivation'] = $oDerivation->getDerivatedCases( $caseId, $delIndex);
			return $res;
		}
		catch ( Exception $e ) {
			$result = new wsResponse (100, $e->getMessage());
			return $result;
		}
	}

	public function executeTrigger($userId, $caseId, $triggerIndex, $delIndex) {
		try {
			require_once ("classes/model/AppDelegation.php");
			require_once ("classes/model/Route.php");
			require_once ("classes/model/AppDelay.php");
			G::LoadClass('case');
			G::LoadClass('sessions');

			$oAppDel = new AppDelegation();
			$appdel  = $oAppDel->Load($caseId, $delIndex);

			if($userId!=$appdel['USR_UID'])
			{
				$result = new wsResponse (17, "This case is assigned to another user");
				return $result;
			}

			if($appdel['DEL_FINISH_DATE']!=NULL)
			{
				$result = new wsResponse (18, 'This delegation already closed');
				return $result;
			}

			$oCriteria = new Criteria('workflow');
			$oCriteria->addSelectColumn(AppDelayPeer::APP_UID);
			$oCriteria->addSelectColumn(AppDelayPeer::APP_DEL_INDEX);
			$oCriteria->add(AppDelayPeer::APP_TYPE, '');
			$oCriteria->add($oCriteria->getNewCriterion(AppDelayPeer::APP_TYPE, 'PAUSE')->addOr($oCriteria->getNewCriterion(AppDelayPeer::APP_TYPE, 'CANCEL')));
			$oCriteria->addAscendingOrderByColumn(AppDelayPeer::APP_ENABLE_ACTION_DATE);
			$oDataset = AppDelayPeer::doSelectRS($oCriteria);
			$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
			$oDataset->next();
			$aRow = $oDataset->getRow();

			if(is_array($aRow))
			{
					if($aRow['APP_DISABLE_ACTION_USER']!=0 && $aRow['APP_DISABLE_ACTION_DATE']!='')
					{
							$result = new wsResponse (19, "This case is in status". $aRow['APP_TYPE']);
							return $result;
					}
			}

			//load data
			$oCase     = new Cases ();
			$appFields = $oCase->loadCase( $caseId );
			$appFields['APP_DATA']['APPLICATION'] = $caseId;

      //executeTrigger
      $aTriggers = array();
      $c = new Criteria();
      $c->add(TriggersPeer::TRI_UID, $triggerIndex );
      $rs = TriggersPeer::doSelectRS($c);
      $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $rs->next();
      $row = $rs->getRow();
      if (is_array($row) && $row['TRI_TYPE'] == 'SCRIPT' ) {
        $aTriggers[] = $row;
        $oPMScript = new PMScript();
        $oPMScript->setFields($appFields['APP_DATA']);
        $oPMScript->setScript($row['TRI_WEBBOT']);
        $oPMScript->execute();

  			//Save data - Start
    		$appFields['APP_DATA']  = $oPMScript->aFields;
    		//$appFields = $oCase->loadCase( $caseId );
	  		$oCase->updateCase ( $caseId, $appFields);
		  	//Save data - End
      }
      else {
  			$result = new wsResponse (100, "Invalid trigger '$triggerIndex'" );
	  		return $result;
      }


			$result = new wsResponse (0, 'executed: '. trim( $row['TRI_WEBBOT']) );
			//$result = new wsResponse (0, 'executed: '. print_r( $oPMScript ,1 ) );
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

	public function processListVerified( $userId ){
		try {
			$oCase = new Cases();
			$rows = $oCase->getStartCases($userId);
			$result  = array();

			foreach ( $rows as $key=>$val ) {
				if ( $key != 0 )
				  $result[] = array ( 'guid' => $val['pro_uid'], 'name' => $val['value'] );
			}
			return $result;
		}
		catch ( Exception $e ) {
			$result[] = array ( 'guid' => $e->getMessage(), 'name' => $e->getMessage() );
			return $result;
		}
	}

	public function reassignCase( $sessionId, $caseId, $delIndex, $userIdSource, $userIdTarget ){
		try {
		  G::LoadClass('derivation');
			if ( $userIdTarget == $userIdSource ) {
		  	$result = new wsResponse (30, "Target and Origin user are the same" );
	  	  return $result;
	  	}

		  /******************( 1 )******************/
			$oCriteria = new Criteria('workflow');
			$oCriteria->add(UsersPeer::USR_STATUS, 'ACTIVE' );
			$oCriteria->add(UsersPeer::USR_UID, $userIdSource);
			$oDataset = UsersPeer::doSelectRS($oCriteria);
			$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
			$oDataset->next();
			$aRow = $oDataset->getRow();
		  if(!is_array($aRow))
		  {
		  		$result = new wsResponse (31, "Invalid origin user" );
	  			return $result;
		  }

		  /******************( 2 )******************/
		  $oCase = new Cases();
			$rows = $oCase->loadCase($caseId);
			if(!is_array($aRow))
			{
		  		$result = new wsResponse (32, "This case is not open." );
	  			return $result;
		  }

		  /******************( 3 )******************/
			$oCriteria = new Criteria('workflow');
			$aConditions   = array();
//      $aConditions[] = array(AppDelegationPeer::USR_UID, TaskUserPeer::USR_UID);
//      $aConditions[] = array(AppDelegationPeer::TAS_UID, TaskUserPeer::TAS_UID);
//      $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
		  //$oCriteria->addJoin(AppDelegationPeer::USR_UID, TaskUserPeer::USR_UID, Criteria::LEFT_JOIN);
			$oCriteria->add(AppDelegationPeer::APP_UID, $caseId );
			$oCriteria->add(AppDelegationPeer::USR_UID, $userIdSource );
			$oCriteria->add(AppDelegationPeer::DEL_INDEX, $delIndex);
			$oCriteria->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
			$oDataset = AppDelegationPeer::doSelectRS($oCriteria);
			$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
			$oDataset->next();
			$aRow = $oDataset->getRow();
		  if(!is_array($aRow))
		  {
		  		$result = new wsResponse (33, "Invalid delindex for this user" );
	  			return $result;
		  }
		  $tasUid = $aRow['TAS_UID'];
		  $derivation = new Derivation ();
		  $userList = $derivation->getAllUsersFromAnyTask( $tasUid );
		  if ( ! in_array ( $userIdTarget, $userList ) ) {
		  	$result = new wsResponse (34, "The userTarget has not rights to execute the task" );
	  	  return $result;
	  	}


		  /******************( 4 )******************/
			$oCriteria = new Criteria('workflow');
			$oCriteria->add(UsersPeer::USR_STATUS, 'ACTIVE' );
			$oCriteria->add(UsersPeer::USR_UID, $userIdTarget);
			$oDataset = UsersPeer::doSelectRS($oCriteria);
			$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
			$oDataset->next();
			$aRow = $oDataset->getRow();
		  if(!is_array($aRow))
		  {
		  		$result = new wsResponse (35, "The user destination is invalid" );
	  			return $result;
		  }


		  /******************( 5 )******************/
		  $var=$oCase->reassignCase($caseId, $delIndex, $userIdSource, $userIdTarget);

		  if(!$var)
		  {
		  		$result = new wsResponse (36, "The case could not be reassigned." );
	  			return $result;
		  }

			$result = new wsResponse (1, 'Succesful......!');

			return $result;
		}
		catch ( Exception $e ) {
			$result[] = array ( 'guid' => $e->getMessage(), 'name' => $e->getMessage() );
			return $result;
		}
	}

}