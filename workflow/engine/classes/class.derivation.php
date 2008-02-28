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
  require_once ( "classes/model/Task.php" );
  require_once ( "classes/model/Process.php" );
  require_once ( "classes/model/Step.php" );
  require_once ( "classes/model/Application.php" );
  require_once 'classes/model/Groupwf.php';
  require_once ( "classes/model/GroupUser.php" );
  require_once ( "classes/model/AppDelegation.php" );
  require_once ( "classes/model/Route.php" );
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
      if( isset( $aDerivation['ROU_CONDITION'] ) && $aDerivation['ROU_CONDITION'] != '' && $aDerivation['ROU_TYPE'] != 'SELECT' ) {
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
          $TaskFields = $oTask->load( $aDerivation['ROU_NEXT_TASK'] );
          $aDerivation['NEXT_TASK'] = $TaskFields;
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
    $rs = TaskUserPeer::DoSelectRs ($c);
    $rs->setFetchmode (ResultSet::FETCHMODE_ASSOC);
    $rs->next();
    $row = $rs->getRow();
    while ( is_array ( $row ) ) {
      if ( $row['TU_RELATION'] == '2' ) {
        $cGrp = new Criteria ('workflow');
        $cGrp->add(GroupwfPeer::GRP_STATUS,  'ACTIVE' );
        $cGrp->add ( GroupUserPeer::GRP_UID, $row['USR_UID']);
        $cGrp->addJoin(GroupUserPeer::GRP_UID, GroupwfPeer::GRP_UID, Criteria::LEFT_JOIN);
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
        $auxFields['USR_USERNAME  '] = $userFields['USR_USERNAME'];
        $auxFields['USR_FIRSTNAME']  = $userFields['USR_FIRSTNAME'];
        $auxFields['USR_LASTNAME']   = $userFields['USR_LASTNAME'];
        $auxFields['USR_FULLNAME']   = $userFields['USR_LASTNAME'] . ' ' . $userFields['USR_FIRSTNAME'];
        $auxFields['USR_EMAIL']      = $userFields['USR_EMAIL'];
        $auxFields['USR_STATUS']     = $userFields['USR_STATUS'];
        $auxFields['USR_COUNTRY']    = $userFields['USR_COUNTRY'];
        $auxFields['USR_CITY']       = $userFields['USR_CITY'];
        $auxFields['USR_LOCATION']   = $userFields['USR_LOCATION'];
        $auxFields['USR_DEPARTMENT'] = $userFields['USR_DEPARTMENT'];
        $aUsersData[] = $auxFields;
      }
    }
    else {
      $userFields = $oUser->load( $aUsers );
      $auxFields['USR_UID']        = $userFields['USR_UID'];
      $auxFields['USR_USERNAME  '] = $userFields['USR_USERNAME'];
      $auxFields['USR_FIRSTNAME']  = $userFields['USR_FIRSTNAME'];
      $auxFields['USR_LASTNAME']   = $userFields['USR_LASTNAME'];
      $auxFields['USR_FULLNAME']   = $userFields['USR_LASTNAME'] . ' ' . $userFields['USR_FIRSTNAME'];
      $auxFields['USR_EMAIL']      = $userFields['USR_EMAIL'];
      $auxFields['USR_STATUS']     = $userFields['USR_STATUS'];
      $auxFields['USR_COUNTRY']    = $userFields['USR_COUNTRY'];
      $auxFields['USR_CITY']       = $userFields['USR_CITY'];
      $auxFields['USR_LOCATION']   = $userFields['USR_LOCATION'];
      $auxFields['USR_DEPARTMENT'] = $userFields['USR_DEPARTMENT'];
      $aUsersData = $auxFields;
    }
    return $aUsersData;
  }

  function getNextAssignedUser( $tasInfo ){
    $oUser    = new Users();
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
    switch($nextAssignedTask['TAS_ASSIGN_TYPE'])
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
      default :
           krumo ($users); die;
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
  {
    //define this...
    if ( !defined('TASK_FINISH_PROCESS')) define('TASK_FINISH_PROCESS',-1);
    if ( !defined('TASK_FINISH_TASK'))    define('TASK_FINISH_TASK',   -2);

    $this->case = new cases();
    $appFields = $this->case->LoadCase ( $currentDelegation['APP_UID'], $currentDelegation['DEL_INDEX'] );

    //first, we close the current derivation, then we'll try to derivate to each defined route
    $appFields = $this->case->LoadCase($currentDelegation['APP_UID'], $currentDelegation['DEL_INDEX'] );
    $this->case->CloseCurrentDelegation ( $currentDelegation['APP_UID'], $currentDelegation['DEL_INDEX'] );

    //Count how many tasks should be derivated.
    $countNextTask = count($nextDelegations);
//krumo ($currentDelegation);    
//krumo ( $nextDelegations ); die;
    foreach($nextDelegations as $nextDel)
    {
      switch ( $nextDel['TAS_UID'] ) {
        case TASK_FINISH_PROCESS:
          /*Close all delegations of $currentDelegation['APP_UID'] */
          $this->case->closeAllDelegations ( $currentDelegation['APP_UID'] );
          break;

        case TASK_FINISH_TASK:
          /* close currentDelegation .. done some lines below */
          /* close the thread here */
          break;

        default:
          // verify previous derivations, if one of them is open, we must wait for it.
        /*
          // Get OPEN/NULL delegations que deberian llegar al $nextDel['TAS_UID'].
          $pendingDel = $this->case->getOpenNullDelegations( $currentDelegation['APP_UID'], $nextDel['TAS_UID'] );
          // Count the total previous tasks of $nextDel['TAS_UID']
          $countPrevTask = $this->case->CountTotalPreviousTasks( $nextDel['TAS_UID'] );
          // Start Block : Quitar de los pendientes los que no fueron iniciados
          $_pendingDel = $pendingDel;
          $pendingDel=array();
          foreach($_pendingDel as $pending)
          {
            $opened = $this->case->isRouteOpen( $pending['APP_UID'], $pending['TAS_UID']);
            if ($opened) $pendingDel[] = $pending;
          }

          $pending = count($pendingDel);
*/
          $canDerivate = true; //???
          if ( $canDerivate ) {
            if ( $nextDel['TAS_ASSIGN_TYPE'] == 'BALANCED') {
              $this->setTasLastAssigned ($nextDel['TAS_UID'], $nextDel['USR_UID']);
            }

            $iAppThreadIndex = $appFields['DEL_THREAD'];

            // the new delegation
            $delType = 'NORMAL';
            $iNewDelIndex = $this->case->newAppDelegation( $_SESSION['PROCESS'],
              $currentDelegation['APP_UID'],
              $nextDel['TAS_UID'],
              $nextDel['USR_UID'],
              $currentDelegation['DEL_INDEX'],
              $nextDel['DEL_PRIORITY'],
              $delType );

            if ( $currentDelegation['ROU_TYPE'] == 'PARALLEL'  ) {
              $this->case->closeAppThread ( $currentDelegation['APP_UID'], $iAppThreadIndex);
              $iNewThreadIndex = $this->case->newAppThread ( $currentDelegation['APP_UID'], $iNewDelIndex, $iAppThreadIndex );
              $this->case->updateAppDelegation ( $currentDelegation['APP_UID'], $iNewDelIndex, $iNewThreadIndex  );
            }
            else {
              $this->case->updateAppThread ( $currentDelegation['APP_UID'], $iAppThreadIndex, $iNewDelIndex );
            }

          }
      }

      //SETS THE APP_PROC_CODE
      if (isset($nextDel['TAS_DEF_PROC_CODE']))
        $appFields['APP_PROC_CODE'] = $nextDel['TAS_DEF_PROC_CODE'];
    }

    /* Start Block : UPDATES APPLICATION */
    //if ($countNextTask == 1) {
    //  $firstDel = $nextDelegations[1];
    //  $appFields['APP_CUR_USER'] = $firstDel['USR_UID'];
    //  $appFields['APP_PARALLEL'] = 'N';
    //}

    //Set THE APP_STATUS
    $appFields['APP_STATUS'] = $currentDelegation['APP_STATUS'];

    /* Start Block : Count the open threads of $currentDelegation['APP_UID'] */
    $openThreads = $this->case->GetOpenThreads( $currentDelegation['APP_UID'] );
    if ( $openThreads == 0) {
      /*Close case  */
      $appFields['APP_STATUS']      = 'COMPLETED';
      $appFields['APP_FINISH_DATE'] = 'now';
    }
    /*
    if ( $openThreads > 1) {
      $appFields['APP_CUR_USER'] = '';
      $appFields['APP_PARALLEL'] = 'Y';
    }
    elseif ( $openThreads == 1 )
    {
      //$openThreads = $openThreads->read();
      $appFields['APP_CUR_USER'] = $openThreads['USR_UID'];
      $appFields['APP_PARALLEL'] = 'N';
    }
    else
    {

    }
    /* End Block */

    /* Start Block : UPDATES APPLICATION */
    $this->case->updateCase ( $currentDelegation['APP_UID'], $appFields );
    /* End Block : UPDATES APPLICATION */
    //krumo ($appFields);die;
  }

}
