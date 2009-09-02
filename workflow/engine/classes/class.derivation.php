<?php
/**
 * class.derivation.php
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
  require_once ( "classes/model/Task.php" );
  require_once ( "classes/model/Process.php" );
  require_once ( "classes/model/Step.php" );
  require_once ( "classes/model/Application.php" );
  require_once ( 'classes/model/Groupwf.php' );
  require_once ( "classes/model/GroupUser.php" );
  require_once ( "classes/model/AppDelegation.php" );
  require_once ( "classes/model/Route.php" );
  require_once ( 'classes/model/SubApplication.php');
  require_once ( 'classes/model/SubProcess.php' );
  require_once ( "classes/model/Users.php" );

  G::LoadClass( "plugin" );

/**
 * derivation - derivation class
 * @package ProcessMaker

 */


class Derivation
{
  var $case;

  function prepareInformation($aData){
    $oTask    = new Task();
    //SELECT *
    //FROM APP_DELEGATION AS A
    //LEFT JOIN TASK AS T ON(T.TAS_UID = A.TAS_UID)
    //LEFT JOIN ROUTE AS R ON(R.TAS_UID = A.TAS_UID)
    //WHERE
    //APP_UID = '$aData['APP_UID']'
    //AND DEL_INDEX = '$aData['DEL_INDEX']'
    $c = new Criteria ( 'workflow' );
    $c->clearSelectColumns();
    $c->addSelectColumn ( AppDelegationPeer::TAS_UID );
    $c->addSelectColumn ( RoutePeer::ROU_CONDITION );
    $c->addSelectColumn ( RoutePeer::ROU_NEXT_TASK );
    $c->addSelectColumn ( RoutePeer::ROU_TYPE );
    $c->addJoin ( AppDelegationPeer::TAS_UID, TaskPeer::TAS_UID,  Criteria::LEFT_JOIN );
    $c->addJoin ( AppDelegationPeer::TAS_UID, RoutePeer::TAS_UID, Criteria::LEFT_JOIN );
    $c->add ( AppDelegationPeer::APP_UID, $aData['APP_UID'] );
    $c->add ( AppDelegationPeer::DEL_INDEX, $aData['DEL_INDEX'] );
    $c->addAscendingOrderByColumn ( RoutePeer::ROU_CASE  );
    $rs = AppDelegationPeer::doSelectRs ( $c );
    $rs->setFetchmode (ResultSet::FETCHMODE_ASSOC);
    $rs->next();
    $aDerivation = $rs->getRow();
    $i=0;
    $taskInfo = array();

    $oUser    = new Users();
    $this->case = new Cases();
    // 1. there is no rule
    if ( is_null ( $aDerivation['ROU_NEXT_TASK'] ) ) {
      throw ( new Exception ( G::LoadTranslation ( 'ID_NO_DERIVATION_RULE')  ) );
    }

    while( is_array( $aDerivation) ) {
      $aDerivation = G::array_merges( $aDerivation, $aData );
      $bContinue   = true;

      //evaluate the condition if there are conditions defined.
      if( isset( $aDerivation['ROU_CONDITION'] ) && $aDerivation['ROU_CONDITION'] != ''
         && ( $aDerivation['ROU_TYPE'] != 'SELECT' ||  $aDerivation['ROU_TYPE'] == 'PARALLEL-BY-EVALUATION') ) {
        $AppFields = $this->case->loadCase( $aData['APP_UID'] );
        G::LoadClass('pmScript');
        $oPMScript = new PMScript();
        $oPMScript->setFields( $AppFields['APP_DATA'] );
        $oPMScript->setScript( $aDerivation['ROU_CONDITION'] );
        $bContinue = $oPMScript->evaluate();
      }

      if ( $aDerivation['ROU_TYPE'] == 'EVALUATE' ) {
        if ( count (  $taskInfo) >= 1) {
          $bContinue = false;
        }
      }

      if ($bContinue) {
        $i++;
        $TaskFields = $oTask->load( $aDerivation['TAS_UID'] );

        $aDerivation = G::array_merges( $aDerivation, $TaskFields );

        //2. if next case is an special case
        if ( (int)$aDerivation['ROU_NEXT_TASK'] < 0) {
          $aDerivation['NEXT_TASK']['TAS_UID']               = (int)$aDerivation['ROU_NEXT_TASK'];
          $aDerivation['NEXT_TASK']['TAS_ASSIGN_TYPE']       = '';
          $aDerivation['NEXT_TASK']['TAS_PRIORITY_VARIABLE'] = '';
          $aDerivation['NEXT_TASK']['TAS_DEF_PROC_CODE']     = '';
          $aDerivation['NEXT_TASK']['TAS_PARENT'] = '';
          switch ($aDerivation['ROU_NEXT_TASK']) {
            case -1: $aDerivation['NEXT_TASK']['TAS_TITLE'] = G::LoadTranslation('ID_END_OF_PROCESS');
                     break;
            case -2: $aDerivation['NEXT_TASK']['TAS_TITLE'] = G::LoadTranslation('ID_TAREA_COLGANTE');
                     break;
          }
          $aDerivation['NEXT_TASK']['USR_UID']     = 'asdf';
        }
        else {
          //3. load the task information of normal NEXT_TASK
          $aDerivation['NEXT_TASK'] = $oTask->load( $aDerivation['ROU_NEXT_TASK'] );
          if ($aDerivation['NEXT_TASK']['TAS_TYPE'] === 'SUBPROCESS') {
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(SubProcessPeer::PRO_PARENT, $aDerivation['PRO_UID']);
            $oCriteria->add(SubProcessPeer::TAS_PARENT, $aDerivation['NEXT_TASK']['TAS_UID']);
            $oDataset = SubProcessPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aRow = $oDataset->getRow();
            $sTaskParent = $aDerivation['NEXT_TASK']['TAS_UID'];
            $aDerivation['ROU_NEXT_TASK'] = $aRow['TAS_UID'];
            $aDerivation['NEXT_TASK'] = $oTask->load( $aDerivation['ROU_NEXT_TASK'] );
            $oProcess = new Process();
            $aRow = $oProcess->load($aRow['PRO_UID']);
            $aDerivation['NEXT_TASK']['TAS_TITLE']     .= ' (' . $aRow['PRO_TITLE'] . ')';
            $aDerivation['NEXT_TASK']['TAS_PARENT']     = $sTaskParent;
            unset($oTask, $oProcess, $aRow, $sTaskParent);
          }
          else {
            $aDerivation['NEXT_TASK']['TAS_PARENT'] = '';
          }
          $aDerivation['NEXT_TASK']['USER_ASSIGNED'] = $this->getNextAssignedUser($aDerivation);
        }

        $taskInfo[$i] = $aDerivation;
      }
      $rs->next();
      $aDerivation = $rs->getRow();
    }
    return $taskInfo;
  }

  /* get all users, from any task, if the task have Groups, the function expand the group
  // param $sTasUid  the task uidUser
  // return an array with userID order by USR_UID
  */
  function getAllUsersFromAnyTask ( $sTasUid ) {
    $users = array();
    $c = new Criteria ( 'workflow' );
    $c->clearSelectColumns();
    $c->addSelectColumn( TaskUserPeer::USR_UID);
    $c->addSelectColumn( TaskUserPeer::TU_RELATION);
    $c->add ( TaskUserPeer::TAS_UID, $sTasUid);
    $c->add ( TaskUserPeer::TU_TYPE, 1);
    $rs = TaskUserPeer::DoSelectRs ($c);
    $rs->setFetchmode (ResultSet::FETCHMODE_ASSOC);
    $rs->next();
    $row = $rs->getRow();
    while ( is_array ( $row ) ) {
      if ( $row['TU_RELATION'] == '2' ) {
        $cGrp = new Criteria ('workflow');
        $cGrp->add(GroupwfPeer::GRP_STATUS,  'ACTIVE');
        $cGrp->add(GroupUserPeer::GRP_UID, $row['USR_UID']);
        $cGrp->addJoin(GroupUserPeer::GRP_UID, GroupwfPeer::GRP_UID, Criteria::LEFT_JOIN);
        $cGrp->addJoin(GroupUserPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);
        $cGrp->add(UsersPeer::USR_STATUS, 'ACTIVE');
        $rsGrp = GroupUserPeer::DoSelectRs ($cGrp);
        $rsGrp->setFetchmode (ResultSet::FETCHMODE_ASSOC);
        $rsGrp->next();
        $rowGrp = $rsGrp->getRow();
        while ( is_array ( $rowGrp ) ) {
          $users[$rowGrp['USR_UID']] = $rowGrp['USR_UID'];
          $rsGrp->next();
          $rowGrp = $rsGrp->getRow();
        }
      }
      else {
        $users[$row['USR_UID']] = $row['USR_UID'];
      }
      $rs->next();
      $row = $rs->getRow();
    }
    //to do: different types of sort
    sort($users);

    return $users;
  }

  /* get an array of users, and returns the same arrays with User's fullname and other fields
  // param $aUsers the task uidUser
  // return an array with with User's fullname
  */
  function getUsersFullNameFromArray ( $aUsers ) {
    $oUser    = new Users();
    $aUsersData = array();
    if ( is_array ( $aUsers ) ) {
      foreach ( $aUsers as $key => $val ) {
        $userFields = $oUser->load($val);
        $auxFields['USR_UID'] = $userFields['USR_UID'];
        $auxFields['USR_USERNAME']   = $userFields['USR_USERNAME'];
        $auxFields['USR_FIRSTNAME']  = $userFields['USR_FIRSTNAME'];
        $auxFields['USR_LASTNAME']   = $userFields['USR_LASTNAME'];
        $auxFields['USR_FULLNAME']   = $userFields['USR_LASTNAME'] . ($userFields['USR_LASTNAME'] != '' ? ', ' : '') . $userFields['USR_FIRSTNAME'];
        $auxFields['USR_EMAIL']      = $userFields['USR_EMAIL'];
        $auxFields['USR_STATUS']     = $userFields['USR_STATUS'];
        $auxFields['USR_COUNTRY']    = $userFields['USR_COUNTRY'];
        $auxFields['USR_CITY']       = $userFields['USR_CITY'];
        $auxFields['USR_LOCATION']   = $userFields['USR_LOCATION'];
        $auxFields['DEP_UID']        = $userFields['DEP_UID'];
        $aUsersData[] = $auxFields;
      }
    }
    else {
      $oCriteria = new Criteria();
      $oCriteria->add(UsersPeer::USR_UID, $aUsers);
      if (UsersPeer::doCount($oCriteria) < 1) {
        return null;
      }
      $userFields = $oUser->load( $aUsers );
      $auxFields['USR_UID']        = $userFields['USR_UID'];
      $auxFields['USR_USERNAME']   = $userFields['USR_USERNAME'];
      $auxFields['USR_FIRSTNAME']  = $userFields['USR_FIRSTNAME'];
      $auxFields['USR_LASTNAME']   = $userFields['USR_LASTNAME'];
      $auxFields['USR_FULLNAME']   = $userFields['USR_LASTNAME'] . ($userFields['USR_LASTNAME'] != '' ? ', ' : '') . $userFields['USR_FIRSTNAME'];
      $auxFields['USR_EMAIL']      = $userFields['USR_EMAIL'];
      $auxFields['USR_STATUS']     = $userFields['USR_STATUS'];
      $auxFields['USR_COUNTRY']    = $userFields['USR_COUNTRY'];
      $auxFields['USR_CITY']       = $userFields['USR_CITY'];
      $auxFields['USR_LOCATION']   = $userFields['USR_LOCATION'];
      $auxFields['DEP_UID']        = $userFields['DEP_UID'];
      $aUsersData = $auxFields;
    }
    return $aUsersData;
  }

  function getNextAssignedUser( $tasInfo ){
    $oUser            = new Users();
    $nextAssignedTask = $tasInfo['NEXT_TASK'];
    $lastAssigned     = $tasInfo['NEXT_TASK']['TAS_LAST_ASSIGNED'];
    $sTasUid          = $tasInfo['NEXT_TASK']['TAS_UID'];
    // to do: we can increase the LOCATION by COUNTRY, STATE and LOCATION
    /* Verify if the next Task is set with the option "TAS_ASSIGN_LOCATION == TRUE" */
    $assignLocation = '';
    if ($tasInfo['NEXT_TASK']['TAS_ASSIGN_LOCATION'] == 'TRUE')
    {
      $oUser->load( $tasInfo['USER_UID'] );
      krumo ($oUser->getUsrLocation() );
      //to do: assign for location
      //$assignLocation = " AND USR_LOCATION = " . $oUser->Fields['USR_LOCATION'];
    }
    /* End - Verify if the next Task is set with the option "TAS_ASSIGN_LOCATION == TRUE" */

    $uidUser = '';
    switch( $nextAssignedTask['TAS_ASSIGN_TYPE'] )
    {
      case 'BALANCED' :
           $users = $this->getAllUsersFromAnyTask ($sTasUid);
           if ( is_array( $users) && count( $users ) > 0 ) {
             //to do apply any filter like LOCATION assignment
             $uidUser = $users[ 0 ];
             $i = count($users) -1;
             while ( $i > 0  ) {
               if ( $lastAssigned < $users[$i] )
                 $uidUser = $users[ $i ];
               $i--;
             }
           }
           else {
             throw ( new Exception (G::LoadTranslation( 'ID_NO_USERS' ) ) );
           }
           $userFields = $this->getUsersFullNameFromArray ($uidUser);
           break;
      case 'MANUAL' :
           $users = $this->getAllUsersFromAnyTask ($sTasUid);
           $userFields = $this->getUsersFullNameFromArray ($users);
           break;
      case 'EVALUATE' :
           $AppFields = $this->case->loadCase( $tasInfo['APP_UID'] );
           $variable  = str_replace ( '@@', '', $nextAssignedTask['TAS_ASSIGN_VARIABLE'] );
           if ( isset ( $AppFields['APP_DATA'][$variable] ) ) {
             if ($AppFields['APP_DATA'][$variable] != '') {
               $value = $AppFields['APP_DATA'][$variable];
               $userFields = $this->getUsersFullNameFromArray ($value);
               if (is_null($userFields)) {
                 throw ( new Exception("Task doesn't have a valid user in variable $variable.") ) ;
               }
             }
             else {
               throw ( new Exception("Task doesn't have a valid user in variable $variable.") ) ;
             }
           }
           else
             throw ( new Exception("Task doesn't have a valid user in variable $variable or this variable doesn't exists.") ) ;
           break;
      default :
           throw ( new Exception('Invalid Task Assignment method for Next Task ') ) ;
    }
    return $userFields;
  }

  function setTasLastAssigned ( $tasUid, $usrUid ) {
    try {
		  $oTask = TaskPeer::retrieveByPk( $tasUid );
      $oTask->setTasLastAssigned( $usrUid );
      $oTask->save();
    }
  	catch ( Exception $e ) {
	    throw ( $e );
    }
  }

  /*
   */
  function derivate($currentDelegation=array(), $nextDelegations =array())
  {//var_dump($currentDelegation['APP_UID'], $currentDelegation['DEL_INDEX']);die;
    //define this...
    if ( !defined('TASK_FINISH_PROCESS')) define('TASK_FINISH_PROCESS',-1);
    if ( !defined('TASK_FINISH_TASK'))    define('TASK_FINISH_TASK',   -2);

    $this->case = new cases();
    //first, we close the current derivation, then we'll try to derivate to each defined route
    $appFields = $this->case->loadCase($currentDelegation['APP_UID'], $currentDelegation['DEL_INDEX'] );
//krumo ($currentDelegation);
//krumo ( $nextDelegations ); //*////*/*/*/*/quitar comentario
    $this->case->CloseCurrentDelegation ( $currentDelegation['APP_UID'], $currentDelegation['DEL_INDEX'] );
    //Count how many tasks should be derivated.
    //$countNextTask = count($nextDelegations);
    foreach($nextDelegations as $nextDel)
    {
      if ($nextDel['TAS_PARENT'] != '') {
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(SubProcessPeer::PRO_PARENT, $appFields['PRO_UID']);
        $oCriteria->add(SubProcessPeer::TAS_PARENT, $nextDel['TAS_PARENT']);
        if (SubProcessPeer::doCount($oCriteria) > 0) {
          $oDataset = SubProcessPeer::doSelectRS($oCriteria);
          $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
          $oDataset->next();
          $aSP            = $oDataset->getRow();
          $aSP['USR_UID'] = $nextDel['USR_UID'];
          $oTask = new Task();
          $aTask = $oTask->load($nextDel['TAS_PARENT']);
          $nextDel = array('TAS_UID'           => $aTask['TAS_UID'],
                           'USR_UID'           => $aSP['USR_UID'],
                           'TAS_ASSIGN_TYPE'   => $aTask['TAS_ASSIGN_TYPE'],
                           'TAS_DEF_PROC_CODE' => $aTask['TAS_DEF_PROC_CODE'],
                           'DEL_PRIORITY'      => 3,
                           'TAS_PARENT'        => '');
        }
        else {
          continue;
        }
      }
      $openThreads = $this->case->GetOpenThreads( $currentDelegation['APP_UID'] );
      if (($nextDel['TAS_UID'] == TASK_FINISH_PROCESS) && (($openThreads + 1) > 1)) {
        $nextDel['TAS_UID'] = TASK_FINISH_TASK;
      }
      switch ( $nextDel['TAS_UID'] ) {
        case TASK_FINISH_PROCESS:
          /*Close all delegations of $currentDelegation['APP_UID'] */
          $this->case->closeAllDelegations ( $currentDelegation['APP_UID'] );
          $this->case->closeAllThreads ( $currentDelegation['APP_UID']);
          break;
        case TASK_FINISH_TASK:
          $iAppThreadIndex = $appFields['DEL_THREAD'];
          $this->case->closeAppThread ( $currentDelegation['APP_UID'], $iAppThreadIndex);
          break;
        default:
          // get all siblingThreads
          if ( $currentDelegation['ROU_TYPE'] == 'SEC-JOIN' ) {
            $siblingThreads = $this->case->getOpenSiblingThreads($nextDel['TAS_UID'], $currentDelegation['APP_UID'], $currentDelegation['DEL_INDEX'], $currentDelegation['TAS_UID']);
            $canDerivate = count($siblingThreads) == 0;
          }
          else {
            $canDerivate = true;
          }

          if ( $canDerivate ) {
            if ( $nextDel['TAS_ASSIGN_TYPE'] == 'BALANCED') {
              $this->setTasLastAssigned ($nextDel['TAS_UID'], $nextDel['USR_UID']);
            }

            $iAppThreadIndex = $appFields['DEL_THREAD'];
            // the new delegation
            $delType = 'NORMAL';
            $iNewDelIndex = $this->case->newAppDelegation( $appFields['PRO_UID'],
              $currentDelegation['APP_UID'],
              $nextDel['TAS_UID'],
              (isset($nextDel['USR_UID']) ? $nextDel['USR_UID'] : ''),
              $currentDelegation['DEL_INDEX'],
              $nextDel['DEL_PRIORITY'],
              $delType,
              $iAppThreadIndex
               );

            switch ( $currentDelegation['ROU_TYPE'] ) {
            	case 'PARALLEL' :
            	case 'PARALLEL-BY-EVALUATION' :
                   $this->case->closeAppThread ( $currentDelegation['APP_UID'], $iAppThreadIndex);
                   $iNewThreadIndex = $this->case->newAppThread ( $currentDelegation['APP_UID'], $iNewDelIndex, $iAppThreadIndex );
                   $this->case->updateAppDelegation ( $currentDelegation['APP_UID'], $iNewDelIndex, $iNewThreadIndex  );
                   break;
              default :
              $this->case->updateAppThread ( $currentDelegation['APP_UID'], $iAppThreadIndex, $iNewDelIndex );
            }//switch
            if (isset($aSP)) {
              //Create the new case in the sub-process
              $aNewCase   = $this->case->startCase($aSP['TAS_UID'], $aSP['USR_UID']);
              //Copy case variables to sub-process case
              $aFields    = unserialize($aSP['SP_VARIABLES_OUT']);
              $aNewFields = array();
              $aOldFields = $this->case->loadCase($aNewCase['APPLICATION']);
              foreach ($aFields as $sOriginField => $sTargetField) {
                $sOriginField = str_replace('@', '', $sOriginField);
                $sOriginField = str_replace('#', '', $sOriginField);
                $sTargetField = str_replace('@', '', $sTargetField);
                $sTargetField = str_replace('#', '', $sTargetField);
                $aNewFields[$sTargetField] = isset($appFields['APP_DATA'][$sOriginField]) ? $appFields['APP_DATA'][$sOriginField] : '';
              }
				      $aOldFields['APP_DATA']   = array_merge($aOldFields['APP_DATA'], $aNewFields);
				      $aOldFields['APP_STATUS'] = 'TO_DO';
				      $this->case->updateCase($aNewCase['APPLICATION'], $aOldFields);
				      //Create a registry in SUB_APPLICATION table
				      $aSubApplication = array('APP_UID'           => $aNewCase['APPLICATION'],
                                       'APP_PARENT'        => $currentDelegation['APP_UID'],
                                       'DEL_INDEX_PARENT'  => $iNewDelIndex,
                                       'DEL_THREAD_PARENT' => $iAppThreadIndex,
                                       'SA_STATUS'         => 'ACTIVE',
                                       'SA_VALUES_OUT'     => serialize($aNewFields),
                                       'SA_INIT_DATE'      => date('Y-m-d H:i:s'));
              if ($aSP['SP_SYNCHRONOUS'] == 0) {
                $aSubApplication['SA_STATUS']      = 'FINISHED';
                $aSubApplication['SA_FINISH_DATE'] = $aSubApplication['SA_INIT_DATE'];
              }
              $oSubApplication = new SubApplication();
              $oSubApplication->create($aSubApplication);
              //If not is SYNCHRONOUS derivate one more time
              if ($aSP['SP_SYNCHRONOUS'] == 0) {
                $this->case->setDelInitDate($currentDelegation['APP_UID'], $iNewDelIndex);
                $aDeriveTasks = $this->prepareInformation(
                  array( 'USER_UID'  => -1,
                         'APP_UID'   => $currentDelegation['APP_UID'],
                         'DEL_INDEX' => $iNewDelIndex)
                );
                if (isset($aDeriveTasks[1])) {
			            if ($aDeriveTasks[1]['ROU_TYPE'] != 'SELECT') {
			              $nextDelegations2 = array();
			              foreach ($aDeriveTasks as $aDeriveTask) {
			                $nextDelegations2[] = array(
                        'TAS_UID'           => $aDeriveTask['NEXT_TASK']['TAS_UID'],
                        'USR_UID'           => $aDeriveTask['NEXT_TASK']['USER_ASSIGNED']['USR_UID'],
                        'TAS_ASSIGN_TYPE'   => $aDeriveTask['NEXT_TASK']['TAS_ASSIGN_TYPE'],
                        'TAS_DEF_PROC_CODE' => $aDeriveTask['NEXT_TASK']['TAS_DEF_PROC_CODE'],
                        'DEL_PRIORITY'	    => 3,
                        'TAS_PARENT'        => $aDeriveTask['NEXT_TASK']['TAS_PARENT']
                      );
			              }
			              $currentDelegation2 = array(
                        'APP_UID'    => $currentDelegation['APP_UID'],
                        'DEL_INDEX'  => $iNewDelIndex,
                        'APP_STATUS' => 'TO_DO',
                        'TAS_UID'    => $currentDelegation['TAS_UID'],
                        'ROU_TYPE'   => $aDeriveTasks[1]['ROU_TYPE']
                    );
                    $this->derivate($currentDelegation2, $nextDelegations2);
			            }
			          }
              }
            }
          }
          else {  //when the task doesnt generate a new AppDelegation
            $iAppThreadIndex = $appFields['DEL_THREAD'];
            switch ( $currentDelegation['ROU_TYPE'] ) {
            	case 'SEC-JOIN' :
                   $this->case->closeAppThread ( $currentDelegation['APP_UID'], $iAppThreadIndex);
            	     break;
              default :
            }//switch
          }
      }

      //SETS THE APP_PROC_CODE
      //if (isset($nextDel['TAS_DEF_PROC_CODE']))
        //$appFields['APP_PROC_CODE'] = $nextDel['TAS_DEF_PROC_CODE'];
      unset($aSP);
    }

    /* Start Block : UPDATES APPLICATION */

    //Set THE APP_STATUS
    $appFields['APP_STATUS'] = $currentDelegation['APP_STATUS'];

    /* Start Block : Count the open threads of $currentDelegation['APP_UID'] */
    $openThreads = $this->case->GetOpenThreads( $currentDelegation['APP_UID'] );
    if ($openThreads == 0) {//Close case
      $appFields['APP_STATUS']      = 'COMPLETED';
      $appFields['APP_FINISH_DATE'] = 'now';
      $this->verifyIsCaseChild($currentDelegation['APP_UID']);
    }
    $appFields['DEL_INDEX'] = (isset($iNewDelIndex) ? $iNewDelIndex : 0);
    $appFields['TAS_UID']   = $nextDel['TAS_UID'];
    /* Start Block : UPDATES APPLICATION */
    $this->case->updateCase ( $currentDelegation['APP_UID'], $appFields );
    /* End Block : UPDATES APPLICATION */
    //krumo ($appFields);die;
  }

  function verifyIsCaseChild($sApplicationUID) {
    //Obtain the related row in the table SUB_APPLICATION
    $oCriteria = new Criteria('workflow');
    $oCriteria->add(SubApplicationPeer::APP_UID, $sApplicationUID);
    $oDataset = SubApplicationPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    $aSA = $oDataset->getRow();
    if ($aSA) {
      //Obtain the related row in the table SUB_PROCESS
      $oCase = new Cases();
      $aParentCase = $oCase->loadCase($aSA['APP_PARENT'], $aSA['DEL_INDEX_PARENT']);
      $oCriteria = new Criteria('workflow');
      $oCriteria->add(SubProcessPeer::PRO_PARENT, $aParentCase['PRO_UID']);
      $oCriteria->add(SubProcessPeer::TAS_PARENT, $aParentCase['TAS_UID']);
      $oDataset = SubProcessPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $aSP = $oDataset->getRow();
      if ($aSP['SP_SYNCHRONOUS'] == 1) {
        $appFields = $oCase->loadCase($sApplicationUID);
        //Copy case variables to parent case
        $aFields    = unserialize($aSP['SP_VARIABLES_IN']);
        $aNewFields = array();
        foreach ($aFields as $sOriginField => $sTargetField) {
          $sOriginField = str_replace('@', '', $sOriginField);
          $sOriginField = str_replace('#', '', $sOriginField);
          $sTargetField = str_replace('@', '', $sTargetField);
          $sTargetField = str_replace('#', '', $sTargetField);
          $aNewFields[$sTargetField] = isset($appFields['APP_DATA'][$sOriginField]) ? $appFields['APP_DATA'][$sOriginField] : '';
        }
		    $aParentCase['APP_DATA'] = array_merge($aParentCase['APP_DATA'], $aNewFields);
		    $oCase->updateCase($aSA['APP_PARENT'], $aParentCase);
        //Update table SUB_APPLICATION
        $oSubApplication = new SubApplication();
        $oSubApplication->update(array('APP_UID'           => $sApplicationUID,
                                       'APP_PARENT'        => $aSA['APP_PARENT'],
                                       'DEL_INDEX_PARENT'  => $aSA['DEL_INDEX_PARENT'],
                                       'DEL_THREAD_PARENT' => $aSA['DEL_THREAD_PARENT'],
                                       'SA_STATUS'         => 'FINISHED',
                                       'SA_VALUES_IN'      => serialize($aNewFields),
                                       'SA_FINISH_DATE'    => date('Y-m-d H:i:s')));
        //Derive the parent case
        $aDeriveTasks = $this->prepareInformation(
          array( 'USER_UID'  => -1,
                 'APP_UID'   => $aSA['APP_PARENT'],
                 'DEL_INDEX' => $aSA['DEL_INDEX_PARENT'])
        );
        if (isset($aDeriveTasks[1])) {
		      if ($aDeriveTasks[1]['ROU_TYPE'] != 'SELECT') {
		        $nextDelegations2 = array();
		        foreach ($aDeriveTasks as $aDeriveTask) {
		          $nextDelegations2[] = array(
                'TAS_UID'           => $aDeriveTask['NEXT_TASK']['TAS_UID'],
                'USR_UID'           => $aDeriveTask['NEXT_TASK']['USER_ASSIGNED']['USR_UID'],
                'TAS_ASSIGN_TYPE'   => $aDeriveTask['NEXT_TASK']['TAS_ASSIGN_TYPE'],
                'TAS_DEF_PROC_CODE' => $aDeriveTask['NEXT_TASK']['TAS_DEF_PROC_CODE'],
                'DEL_PRIORITY'	    => 3,
                'TAS_PARENT'        => $aDeriveTask['NEXT_TASK']['TAS_PARENT']
              );
		        }
		        $currentDelegation2 = array(
                'APP_UID'    => $aSA['APP_PARENT'],
                'DEL_INDEX'  => $aSA['DEL_INDEX_PARENT'],
                'APP_STATUS' => 'TO_DO',
                'TAS_UID'    => $aParentCase['TAS_UID'],
                'ROU_TYPE'   => $aDeriveTasks[1]['ROU_TYPE']
            );
            $this->derivate($currentDelegation2, $nextDelegations2);
		      }
		    }
      }
    }
  }

  // getDerivatedCases
  // get all derivated cases and subcases from any task,
  // this function is useful to know who users have been assigned and what task they do.
  function getDerivatedCases ( $sParentUid, $sDelIndexParent ) {
    $oCriteria = new Criteria('workflow');
    $cases = array();
    $derivation = array();
    //get the child delegations , of parent delIndex
    $children = array();
    $oCriteria->clearSelectColumns();
    $oCriteria->addSelectColumn ( AppDelegationPeer::DEL_INDEX );
    $oCriteria->add(AppDelegationPeer::APP_UID, $sParentUid);
    $oCriteria->add(AppDelegationPeer::DEL_PREVIOUS, $sDelIndexParent );
    $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    $aRow = $oDataset->getRow();
    while ( is_array( $aRow) ) {
      $children[] = $aRow['DEL_INDEX'];

      $oDataset->next();
      $aRow = $oDataset->getRow();
    }

    //foreach child , get the info of their derivations and subprocesses
    foreach ( $children as $keyChild => $child ) {
      $oCriteria = new Criteria('workflow');
      $oCriteria->clearSelectColumns();
      $oCriteria->addSelectColumn ( SubApplicationPeer::APP_UID );
      $oCriteria->addSelectColumn ( AppDelegationPeer::APP_UID );
      $oCriteria->addSelectColumn ( AppDelegationPeer::DEL_INDEX );
      $oCriteria->addSelectColumn ( AppDelegationPeer::PRO_UID );
      $oCriteria->addSelectColumn ( AppDelegationPeer::TAS_UID );
      $oCriteria->addSelectColumn ( AppDelegationPeer::USR_UID );
      $oCriteria->addSelectColumn ( UsersPeer::USR_USERNAME );
      $oCriteria->addSelectColumn ( UsersPeer::USR_FIRSTNAME );
      $oCriteria->addSelectColumn ( UsersPeer::USR_LASTNAME );

      $oCriteria->add(SubApplicationPeer::APP_PARENT, $sParentUid);
      $oCriteria->add(SubApplicationPeer::DEL_INDEX_PARENT, $child );
      $oCriteria->addJoin ( SubApplicationPeer::APP_UID, AppDelegationPeer::APP_UID);
      $oCriteria->addJoin ( AppDelegationPeer::USR_UID, UsersPeer::USR_UID);
      $oDataset = SubApplicationPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $aRow = $oDataset->getRow();
      while ( is_array( $aRow) ) {
        $oProcess = new Process();
        $proFields = $oProcess->load($aRow['PRO_UID']);
        $oCase = new Application();
        $appFields = $oCase->load($aRow['APP_UID']);
        $oTask = new Task();
        $tasFields = $oTask->load($aRow['TAS_UID']);
        $derivation[] = array (
                        'processId' => $aRow['PRO_UID'],
                        'processTitle' => $proFields['PRO_TITLE'],
                        'caseId' => $aRow['APP_UID'],
                        'caseNumber' => $appFields['APP_NUMBER'],
                        'taskId' => $aRow['TAS_UID'],
                        'taskTitle' => $tasFields['TAS_TITLE'],
                        'userId' => $aRow['USR_UID'],
                        'userName' => $aRow['USR_USERNAME'],
                        'userFullname' => $aRow['USR_FIRSTNAME'] . ' ' . $aRow['USR_LASTNAME']
                     );

        $oDataset->next();
        $aRow = $oDataset->getRow();
      }
    }


    return $derivation;
  }

}
