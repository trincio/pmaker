<?php
/**
 * class.case.php
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

require_once ("classes/model/Application.php");
require_once ("classes/model/AppDelay.php");
require_once ("classes/model/AppDelegation.php");
require_once ("classes/model/AppDocument.php");
require_once ("classes/model/AppMessage.php");
require_once ("classes/model/AppThread.php");
require_once ("classes/model/Content.php");
require_once ("classes/model/DbSource.php");
require_once ("classes/model/Dynaform.php");
require_once ("classes/model/InputDocument.php");
require_once ("classes/model/Language.php");
require_once ("classes/model/ObjectPermission.php");
require_once ("classes/model/OutputDocument.php");
require_once ("classes/model/Process.php");
require_once ("classes/model/ProcessUser.php");
require_once ("classes/model/ReportTable.php");
require_once ("classes/model/ReportVar.php");
require_once ("classes/model/Step.php");
require_once ("classes/model/StepSupervisor.php");
require_once ("classes/model/StepTrigger.php");
require_once ("classes/model/SubApplication.php");
require_once ("classes/model/Task.php");
require_once ("classes/model/TaskUser.php");
require_once ("classes/model/Triggers.php");
require_once ("classes/model/Users.php");

G::LoadClass('pmScript');

class Cases
{

    /*
    * Ask if an user can start a case
    * @param string $sUIDUser
    * @return boolean
    */
    function canStartCase($sUIDUser = '')
    {
        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn('COUNT(*)');

        $c->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);
        $c->add(TaskPeer::TAS_START, 'TRUE');
        $c->add(TaskUserPeer::USR_UID, $sUIDUser);

        $rs = TaskPeer::doSelectRS($c);
        $rs->next();
        $row = $rs->getRow();
        $count = $row[0];
        if ($count > 0)
            return true;

        //check groups
        G::LoadClass('groups');
        $group = new Groups();
        $aGroups = $group->getActiveGroupsForAnUser($sUIDUser);

        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn('COUNT(*)');

        $c->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);
        $c->add(TaskPeer::TAS_START, 'TRUE');
        $c->add(TaskUserPeer::USR_UID, $aGroups, Criteria::IN);

        $rs = TaskPeer::doSelectRS($c);
        $rs->next();
        $row = $rs->getRow();
        $count = $row[0];
        return ($count > 0);

    }

    /*
    * get user starting tasks
    * @param string $sUIDUser
    * @return $rows
    */
    function getStartCases($sUIDUser = '')
    {
        $rows[] = array('uid' => 'char', 'value' => 'char');
        $tasks = array();

        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn(TaskPeer::TAS_UID);
        $c->addSelectColumn(TaskPeer::PRO_UID);
        $c->addJoin(TaskPeer::PRO_UID, ProcessPeer::PRO_UID, Criteria::LEFT_JOIN);
        $c->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);
        $c->add(ProcessPeer::PRO_STATUS, 'ACTIVE');
        $c->add(TaskPeer::TAS_START, 'TRUE');
        $c->add(TaskUserPeer::USR_UID, $sUIDUser);

        $rs = TaskPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();

        while (is_array($row)) {
            $tasks[] = array('TAS_UID' => $row['TAS_UID'], 'PRO_UID' => $row['PRO_UID']);
            $rs->next();
            $row = $rs->getRow();
        }

        //check groups
        G::LoadClass('groups');
        $group = new Groups();
        $aGroups = $group->getActiveGroupsForAnUser($sUIDUser);

        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn(TaskPeer::TAS_UID);
        $c->addSelectColumn(TaskPeer::PRO_UID);
        $c->addJoin(TaskPeer::PRO_UID, ProcessPeer::PRO_UID, Criteria::LEFT_JOIN);
        $c->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);
        $c->add(ProcessPeer::PRO_STATUS, 'ACTIVE');
        $c->add(TaskPeer::TAS_START, 'TRUE');
        $c->add(TaskUserPeer::USR_UID, $aGroups, Criteria::IN);

        $rs = TaskPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();

        while (is_array($row)) {
            $tasks[] = array('TAS_UID' => $row['TAS_UID'], 'PRO_UID' => $row['PRO_UID']);
            $rs->next();
            $row = $rs->getRow();
        }

        //get content process title
        foreach ($tasks as $key => $val) {
            $tasTitle = Content::load('TAS_TITLE', '', $val['TAS_UID'], SYS_LANG);
            $proTitle = Content::load('PRO_TITLE', '', $val['PRO_UID'], SYS_LANG);
            $title = " $proTitle ($tasTitle)";
            $rows[] = array('uid' => $val['TAS_UID'], 'value' => $title, 'pro_uid' => $val['PRO_UID']);
        }
        return $rows;
    }

    /*
    * Load an user existing case, this info is used in CaseResume
    * @param string  $sAppUid
    * @param integer $iDelIndex > 0 //get the Delegation fields
    * @return Fields
    */
    function loadCase($sAppUid, $iDelIndex = 0)
    {
        try {
            $oApp = new Application;
            $oApp->Load($sAppUid);
            $aFields = $oApp->toArray(BasePeer::TYPE_FIELDNAME);
            $aFields['APP_DATA'] = G::array_merges(G::getSystemConstants(), unserialize($aFields['APP_DATA']));
            switch ($oApp->getAppStatus()) {
                case 'COMPLETED':
                    $aFields['STATUS'] = G::LoadTranslation('ID_COMPLETED');
                    break;
                case 'CANCELLED':
                    $aFields['STATUS'] = G::LoadTranslation('ID_CANCELLED');
                    break;
                case 'PAUSED':
                    $aFields['STATUS'] = G::LoadTranslation('ID_PAUSED');
                    break;
                case 'DRAFT':
                    $aFields['STATUS'] = G::LoadTranslation('ID_DRAFT');
                    break;
                case 'TO_DO':
                    $aFields['STATUS'] = G::LoadTranslation('ID_TO_DO');
                    break;
            }
            $oUser = new Users();
            $oUser->load($oApp->getAppInitUser());
            $uFields = $oUser->toArray(BasePeer::TYPE_FIELDNAME);
            $aFields['TITLE'] = $oApp->getAppTitle();
            $aFields['CREATOR'] = $oUser->getUsrFirstname() . ' ' . $oUser->getUsrLastname();
            $aFields['CREATE_DATE'] = $oApp->getAppCreateDate();
            $aFields['UPDATE_DATE'] = $oApp->getAppUpdateDate();

            if ($iDelIndex > 0) { //get the Delegation fields,
                $oAppDel = new AppDelegation();
                $oAppDel->Load($sAppUid, $iDelIndex);
                $aAppDel = $oAppDel->toArray(BasePeer::TYPE_FIELDNAME);
                $aFields['TAS_UID'] = $aAppDel['TAS_UID'];
                $aFields['DEL_INDEX'] = $aAppDel['DEL_INDEX'];
                $aFields['DEL_PREVIOUS'] = $aAppDel['DEL_PREVIOUS'];
                $aFields['DEL_TYPE'] = $aAppDel['DEL_TYPE'];
                $aFields['DEL_PRIORITY'] = $aAppDel['DEL_PRIORITY'];
                $aFields['DEL_THREAD_STATUS'] = $aAppDel['DEL_THREAD_STATUS'];
                $aFields['DEL_THREAD'] = $aAppDel['DEL_THREAD'];
                $aFields['DEL_DELEGATE_DATE'] = $aAppDel['DEL_DELEGATE_DATE'];
                $aFields['DEL_INIT_DATE'] = $aAppDel['DEL_INIT_DATE'];
                $aFields['DEL_TASK_DUE_DATE'] = $aAppDel['DEL_TASK_DUE_DATE'];
                $aFields['DEL_FINISH_DATE'] = $aAppDel['DEL_FINISH_DATE'];
            }

            return $aFields;
        }
        catch (exception $e) {
            throw ($e);
        }
    }

   /*
    * LoadCaseByNumber
    * @param string $caseNumber
    * @return
    */
    function loadCaseByNumber($sCaseNumber)
    {
        //('SELECT * FROM APP_DELEGATION WHERE APP_PROC_CODE="'.$sCaseNumber.'" ');
        try {
        	  $aCases = array();
            $c = new Criteria();
            $c->add(ApplicationPeer::APP_PROC_CODE, $sCaseNumber);
            $rs = ApplicationPeer::doSelectRs($c);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $row = $rs->getRow();
            while (is_array($row)) {
            	  $case['APP_UID'] = $row['APP_UID'];
            	  $case['APP_NUMBER'] = $row['APP_NUMBER'];
            	  $case['APP_STATUS'] = $row['APP_STATUS'];
            	  $case['PRO_UID'] = $row['PRO_UID'];
            	  $case['APP_PARALLEL'] = $row['APP_PARALLEL'];
            	  $case['APP_CUR_USER'] = $row['APP_CUR_USER'];
                $aCases[] = $case;
                $rs->next();
                $row = $rs->getRow();
            }
            return $aCases;
        }
        catch (exception $e) {
            throw ($e);
        }
    }


    /*
    * Actualiza el case label
    * PROCESO:
    *    Carga el label actual si existe
    *    Obtener APP_DELEGATIONS que esten abiertos en el CASO
    *    Filtrar los APP_DELEGATIONS cuyos TASK asociados tengan el label definido (CASE_TITLE)
    *    Leer Ultimo APP_DELEGATION->TASK
    */
    function refreshCaseLabel($sAppUid, $aAppData, $sLabel)
    {
        $getAppLabel = "getApp$sLabel";
        $getTasDef = "getTasDef$sLabel";
        $oApplication = new Application;
        if (!$oApplication->exists($sAppUid)) {
            return null;
        } else {
            $oApplication->load($sAppUid);
            $appLabel = $oApplication->$getAppLabel();
        }
        $cri = new Criteria;
        $cri->add(AppDelegationPeer::APP_UID, $sAppUid);
        $cri->add(AppDelegationPeer::DEL_THREAD_STATUS, "OPEN");
        $currentDelegations = AppDelegationPeer::doSelect($cri);
        for ($r = count($currentDelegations) - 1; $r >= 0; $r--) {
            $task = TaskPeer::retrieveByPk($currentDelegations[$r]->getTasUid());
            $caseLabel = $task->$getTasDef();
            if ($caseLabel != '') {
                $appLabel = G::replaceDataField($caseLabel, $aAppData);
                break;
            }
        }
        return $appLabel;
    }

    function refreshCaseTitle($sAppUid, $aAppData)
    {
        return $this->refreshCaseLabel($sAppUid, $aAppData, "Title");
    }

    function refreshCaseDescription($sAppUid, $aAppData)
    {
        return $this->refreshCaseLabel($sAppUid, $aAppData, "Description");
    }

    function refreshCaseStatusCode($sAppUid, $aAppData)
    {
        return $this->refreshCaseLabel($sAppUid, $aAppData, "ProcCode");
    }

    /*
    * Update an existing case, this info is used in CaseResume
    * @param string  $sAppUid
    * @param integer $iDelIndex > 0 //get the Delegation fields
    * @return Fields
    */
    function updateCase($sAppUid, $Fields = array())
    {
        try {
            $aApplicationFields = $Fields['APP_DATA'];
            $oApp = new Application;
            $Fields['APP_UID'] = $sAppUid;
            $Fields['APP_UPDATE_DATE'] = 'now';
            $Fields['APP_DATA'] = serialize($Fields['APP_DATA']);
            $Fields['APP_TITLE'] = self::refreshCaseTitle($sAppUid, $aApplicationFields);
            $Fields['APP_DESCRIPTION'] = self::refreshCaseDescription($sAppUid, $aApplicationFields);
            //$Fields['APP_PROC_CODE'] = self::refreshCaseStatusCode($sAppUid, $aApplicationFields);
            $oApp->update($Fields);

            $DEL_INDEX = isset($Fields['DEL_INDEX']) ? $Fields['DEL_INDEX'] : '';
            $TAS_UID = isset($Fields['TAS_UID']) ? $Fields['TAS_UID'] : '';

            $aFields = $oApp->load($sAppUid);
            G::LoadClass('reportTables');
            $oReportTables = new ReportTables();
            $oReportTables->updateTables($aFields['PRO_UID'], $sAppUid, $Fields['APP_NUMBER'], $aApplicationFields);

            if ($DEL_INDEX != '' && $TAS_UID != '') {
                $oTask = new Task;
                $array = $oTask->load($TAS_UID);

                $VAR_PRI = substr($array['TAS_PRIORITY_VARIABLE'], 2);

                $x = unserialize($Fields['APP_DATA']);
                if (isset($x[$VAR_PRI])) {
                	if ($x[$VAR_PRI] != '') {
                    $oDel = new AppDelegation;
                    $array['APP_UID'] = $sAppUid;
                    $array['DEL_INDEX'] = $DEL_INDEX;
                    $array['TAS_UID'] = $TAS_UID;
                    $array['DEL_PRIORITY'] = $x[$VAR_PRI];
                    $oDel->update($array);
                  }
                }
            }
            return $Fields;
        }
        catch (exception $e) {
            throw ($e);
        }
    }

    /*
    * Remove an existing case,
    * @param string  $sAppUid
    * @return Fields
    */
    function removeCase($sAppUid)
    {
        try {
            $oApplication     = new Application();
  		      $oAppDelegation   = new AppDelegation();
  		      $oAppDocument     = new AppDocument();
            //Delete the delegations of a application
      	    $oCriteria2 = new Criteria('workflow');
  	        $oCriteria2->add(AppDelegationPeer::APP_UID, $sAppUid);
  	        $oDataset2 = AppDelegationPeer::doSelectRS($oCriteria2);
            $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset2->next();
            while ($aRow2 = $oDataset2->getRow()) {
            	$oAppDelegation->remove($sAppUid, $aRow2['DEL_INDEX']);
            	$oDataset2->next();
            }
      	    //Delete the documents assigned to a application
      	    $oCriteria2 = new Criteria('workflow');
  	        $oCriteria2->add(AppDocumentPeer::APP_UID, $sAppUid);
  	        $oDataset2 = AppDocumentPeer::doSelectRS($oCriteria2);
            $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset2->next();
            while ($aRow2 = $oDataset2->getRow()) {
            	$oAppDocument->remove($aRow2['APP_DOC_UID']);
            	$oDataset2->next();
            }
            //Delete the actions from a application
      	    $oCriteria2 = new Criteria('workflow');
  	        $oCriteria2->add(AppDelayPeer::APP_UID, $sAppUid);
            AppDelayPeer::doDelete($oCriteria2);
            //Delete the messages from a application
      	    $oCriteria2 = new Criteria('workflow');
  	        $oCriteria2->add(AppMessagePeer::APP_UID, $sAppUid);
            AppMessagePeer::doDelete($oCriteria2);
            //Delete the threads from a application
      	    $oCriteria2 = new Criteria('workflow');
  	        $oCriteria2->add(AppThreadPeer::APP_UID, $sAppUid);
            AppThreadPeer::doDelete($oCriteria2);
            //Before delete verify if is a child case
            $oCriteria2 = new Criteria('workflow');
  	        $oCriteria2->add(SubApplicationPeer::APP_UID, $sAppUid);
  	        $oCriteria2->add(SubApplicationPeer::SA_STATUS, 'ACTIVE');
  	        if (SubApplicationPeer::doCount($oCriteria2) > 0) {
  	          G::LoadClass('derivation');
  	          $oDerivation = new Derivation();
  	          $oDerivation->verifyIsCaseChild($sAppUid);
  	        }
            //Delete the registries in the table SUB_APPLICATION
            $oCriteria2 = new Criteria('workflow');
  	        $oCriteria2->add(SubApplicationPeer::APP_UID, $sAppUid);
            SubApplicationPeer::doDelete($oCriteria2);
            $oCriteria2 = new Criteria('workflow');
  	        $oCriteria2->add(SubApplicationPeer::APP_PARENT, $sAppUid);
            SubApplicationPeer::doDelete($oCriteria2);
            $oApp = new Application;
            return $oApp->remove($sAppUid);
        }
        catch (exception $e) {
            throw ($e);
        }
    }

    /*
    * Set the DEL_INIT_DATE
    * @param string $sAppUid
    * @param string $iDelIndex
    * @return Fields
    */
    function setDelInitDate($sAppUid, $iDelIndex)
    {
        try {
            $oAppDel = AppDelegationPeer::retrieveByPk($sAppUid, $iDelIndex);
            $oAppDel->setDelInitDate("now");
            $oAppDel->save();
        }
        catch (exception $e) {
            throw ($e);
        }
    }

    /*
    * GetOpenThreads
    * @param string $sAppUid
    * @return
    */
    function GetOpenThreads($sAppUid)
    {
        //('SELECT * FROM APP_DELEGATION WHERE APP_UID="'.$currentDelegation['APP_UID'].'" AND DEL_THREAD_STATUS="OPEN"');
        try {
            $c = new Criteria();
            $c->clearSelectColumns();
            $c->addSelectColumn('COUNT(*)');
            $c->add(AppDelegationPeer::APP_UID, $sAppUid);
            $c->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');
            $rs = AppDelegationPeer::doSelectRs($c);
            $rs->next();
            $row = $rs->getRow();
            return intval($row[0]);
        }
        catch (exception $e) {
            throw ($e);
        }
    }

    /*
    * getSiblingThreads
    * @param string $sAppUid
    * @return
    */
    function getSiblingThreads($sAppUid, $iDelIndex)
    {
        try {
            //get the parent thread
            $c = new Criteria();
            $c->add(AppThreadPeer::APP_UID, $sAppUid);
            $c->add(AppThreadPeer::DEL_INDEX, $iDelIndex);
            $rs = AppThreadPeer::doSelectRs($c);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $row = $rs->getRow();
            $iParent = $row['APP_THREAD_PARENT'];

            //get the sibling
            $aThreads = array();
            $c = new Criteria();
            $c->add(AppThreadPeer::APP_UID, $sAppUid);
            $c->add(AppThreadPeer::APP_THREAD_PARENT, $iParent);
            $c->add(AppThreadPeer::DEL_INDEX, $iDelIndex, Criteria::NOT_EQUAL);
            $rs = AppThreadPeer::doSelectRs($c);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $row = $rs->getRow();
            while (is_array($row)) {
                $aThreads[] = $row;
                $rs->next();
                $row = $rs->getRow();
            }
            return $aThreads;
        }
        catch (exception $e) {
            throw ($e);
        }
    }

    /*
    * getSiblingThreads
    * @param string $sAppUid
    * @return
    */
    function getOpenSiblingThreads($sNextTask, $sAppUid, $iDelIndex, $sCurrentTask) {
      try {
        require_once 'classes/model/Route.php';
        $aPreviousTask = array();
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(RoutePeer::ROU_NEXT_TASK, $sNextTask);
        $oDataset = RoutePeer::doSelectRs($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        while ($aRow = $oDataset->getRow()) {
          $aPreviousTask[] = $aRow['TAS_UID'];
          $oDataset->next();
        }
        $oCriteria = new Criteria('workflow');
        $aConditions   = array();
        $aConditions[] = array(AppThreadPeer::APP_UID, AppDelegationPeer::APP_UID);
        $aConditions[] = array(AppThreadPeer::DEL_INDEX, AppDelegationPeer::DEL_INDEX);
        $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
        $oCriteria->add(AppDelegationPeer::TAS_UID, $aPreviousTask, Criteria::IN);
        $oCriteria->add(AppDelegationPeer::APP_UID, $sAppUid);
        if (AppThreadPeer::doCount($oCriteria) == 1) {
          $iCounter  = 0;
          $bContinue = true;
          $aTaskReviewed = array();
          do {
            $aAux = $aPreviousTask;
            foreach ($aAux as $sTaskUid) {
              if (!in_array($sTaskUid, $aTaskReviewed)) {
                $aTaskReviewed[] = $sTaskUid;
                $oCriteria = new Criteria('workflow');
                $oCriteria->add(RoutePeer::ROU_NEXT_TASK, $sTaskUid);
                $oDataset = RoutePeer::doSelectRs($oCriteria);
                $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $oDataset->next();
                while (($aRow = $oDataset->getRow()) && ($bContinue)) {
                  if (!in_array($aRow['TAS_UID'], $aPreviousTask)) {
                    $aPreviousTask[] = $aRow['TAS_UID'];
                  }
                  $oCriteria = new Criteria('workflow');
                  $oCriteria->add(AppDelegationPeer::APP_UID, $sAppUid);
                  $oCriteria->add(AppDelegationPeer::TAS_UID, $aRow['TAS_UID']);
                  $oCriteria->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
                  if (AppDelegationPeer::doCount($oCriteria) == 1) {
                    if ($aRow['TAS_UID'] != $sCurrentTask) {
                      $bContinue = false;
                    }
                    else {
                      $bContinue = true;
                    }
                  }
                  else {
                    $bContinue = true;
                  }
                  $oDataset->next();
                }
              }
            }
            $iCounter++;
          } while (($bContinue) && ($iCounter < 100));
        }
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(AppThreadPeer::APP_UID, $sAppUid);
        $oCriteria->add(AppThreadPeer::DEL_INDEX, $iDelIndex);
        $oDataset = AppThreadPeer::doSelectRs($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRow          = $oDataset->getRow();
        $iParent       = $aRow['APP_THREAD_PARENT'];
        $aThreads      = array();
        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn('*');
        $aConditions   = array();
        $aConditions[] = array(AppThreadPeer::APP_UID, AppDelegationPeer::APP_UID);
        $aConditions[] = array(AppThreadPeer::DEL_INDEX, AppDelegationPeer::DEL_INDEX);
        $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
        $oCriteria->add(AppThreadPeer::APP_UID, $sAppUid);
        $oCriteria->add(AppThreadPeer::APP_THREAD_PARENT, $iParent);
        $oCriteria->add(AppThreadPeer::APP_THREAD_STATUS, 'OPEN');
        $oCriteria->add(AppThreadPeer::DEL_INDEX, $iDelIndex, Criteria::NOT_EQUAL);
        $oCriteria->add(AppDelegationPeer::TAS_UID, $aPreviousTask, Criteria::IN);
        $oDataset = AppThreadPeer::doSelectRs($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRow = $oDataset->getRow();
        while ($aRow = $oDataset->getRow()) {
            $aThreads[] = $aRow;
            $oDataset->next();
        }
        return $aThreads;
      }
      catch (exception $e) {
        throw ($e);
      }
    }

    /*
    * CountTotalPreviousTasks
    * @param string $sTasUid $nextDel['TAS_UID']
    * @return
    */
    function CountTotalPreviousTasks($sTasUid)
    {
        //SELECT * FROM ROUTE WHERE ROU_NEXT_TASK="44756CDAC1BF4F";
        try {
            $c = new Criteria();
            $c->clearSelectColumns();
            $c->addSelectColumn('COUNT(*)');
            $c->add(RoutePeer::ROU_NEXT_TASK, $sTasUid);
            $rs = RoutePeer::doSelectRs($c);
            $rs->next();
            $row = $rs->getRow();
            return intval($row[0]);
        }
        catch (exception $e) {
            throw ($e);
        }
    }

    /*
    * getOpenNullDelegations
    * @param string $sTasUid $nextDel['TAS_UID']
    * @return
    */
    function getOpenNullDelegations($sAppUid, $sTasUid)
    {
        $pendingDel = array();
        //PRINT "getOpenNullDelegations ( $sAppUid, $sTasUid ) ";
        //SELECT D.*,R.* FROM ROUTE R LEFT JOIN APP_DELEGATION D ON (R.TAS_UID=D.TAS_UID)
        //WHERE ((D.DEL_THREAD_STATUS="OPEN" AND D.APP_UID="'.$nextDel['APP_UID'].'") OR ISNULL(D.DEL_THREAD_STATUS)) AND R.ROU_NEXT_TASK="'.$nextDel['TAS_UID'].'"";
        //SELECT D.*,R.* FROM ROUTE R LEFT JOIN APP_DELEGATION D ON (R.TAS_UID=D.TAS_UID)
        //where ROU_NEXT_TASK = '8479670B93B749'  AND APP_UID = ''
        try {
            //first query
            $c = new Criteria();
            $c->clearSelectColumns();
            $c->addSelectColumn(AppDelegationPeer::APP_UID);
            $c->addSelectColumn(AppDelegationPeer::DEL_INDEX);
            $c->addSelectColumn(AppDelegationPeer::DEL_PREVIOUS);
            $c->addSelectColumn(AppDelegationPeer::PRO_UID);
            $c->addSelectColumn(AppDelegationPeer::TAS_UID);
            $c->addSelectColumn(AppDelegationPeer::USR_UID);
            $c->addSelectColumn(AppDelegationPeer::DEL_TYPE);
            $c->addSelectColumn(AppDelegationPeer::DEL_PRIORITY);
            $c->addSelectColumn(AppDelegationPeer::DEL_THREAD);
            $c->addSelectColumn(AppDelegationPeer::DEL_THREAD_STATUS);
            $c->addSelectColumn(AppDelegationPeer::DEL_DELEGATE_DATE);
            $c->addSelectColumn(AppDelegationPeer::DEL_INIT_DATE);
            $c->addSelectColumn(AppDelegationPeer::DEL_TASK_DUE_DATE);
            $c->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE);
            $c->addSelectColumn(RoutePeer::ROU_UID);
            $c->addSelectColumn(RoutePeer::ROU_PARENT);
            $c->addSelectColumn(RoutePeer::ROU_NEXT_TASK);
            $c->addSelectColumn(RoutePeer::ROU_CASE);
            $c->addSelectColumn(RoutePeer::ROU_TYPE);
            $c->addSelectColumn(RoutePeer::ROU_CONDITION);
            $c->addSelectColumn(RoutePeer::ROU_TO_LAST_USER);
            $c->addSelectColumn(RoutePeer::ROU_OPTIONAL);
            $c->addSelectColumn(RoutePeer::ROU_SEND_EMAIL);

            $c->addJoin(AppDelegationPeer::TAS_UID, RoutePeer::TAS_UID);
            $c->add(RoutePeer::ROU_NEXT_TASK, $sTasUid);
            $c->add(AppDelegationPeer::APP_UID, $sAppUid);
            $rs = RoutePeer::doSelectRs($c);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $row = $rs->getRow();
            while (is_array($row)) {
                if ($row['DEL_THREAD_STATUS'] == 'OPEN' && $row['APP_UID'] = $sAppUid)
                    $pendingDel[] = $row;
                else
                    krumo($row['DEL_THREAD_STATUS']);

                $rs->next();
                $row = $rs->getRow();
            }
            return $pendingDel;
        }
        catch (exception $e) {
            throw ($e);
        }
    }


    /*
    * isRouteOpen      Busca en la ruta de una tarea algun delegation abierta.
    * @param string $sAppUid $nextDel['APP_UID']
    * @param string $sTasUid $nextDel['TAS_UID']
    * @return
    */
    function isRouteOpen($sAppUid, $sTasUid)
    {
        try {
            $c = new Criteria();
            $c->clearSelectColumns();
            $c->addSelectColumn('COUNT(*)');
            $c->add(AppDelegationPeer::APP_UID, $sAppUid);
            $c->add(AppDelegationPeer::TAS_UID, $sTasUid);
            $c->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');
            $rs = RoutePeer::doSelectRs($c);
            $rs->next();
            $row = $rs->getRow();
            $open = ($row[0] >= 1);
            if ($open)
                return true;

            $c->clearSelectColumns();
            $c->addSelectColumn(AppDelegationPeer::DEL_INDEX);
            $c->addSelectColumn(AppDelegationPeer::USR_UID);
            $c->addSelectColumn(AppDelegationPeer::DEL_TYPE);
            $c->addSelectColumn(AppDelegationPeer::DEL_THREAD);
            $c->addSelectColumn(AppDelegationPeer::DEL_THREAD_STATUS);
            $c->addSelectColumn(RoutePeer::ROU_UID);
            $c->addSelectColumn(RoutePeer::ROU_NEXT_TASK);
            $c->addSelectColumn(RoutePeer::ROU_CASE);
            $c->addSelectColumn(RoutePeer::ROU_TYPE);

            $c->addJoin(AppDelegationPeer::TAS_UID, RoutePeer::TAS_UID);
            $c->add(AppDelegationPeer::APP_UID, $sAppUid);
            $c->add(RoutePeer::ROU_NEXT_TASK, $sTasUid);
            $rs = RoutePeer::doSelectRs($c);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $row = $rs->getRow();
            $sql = 'SELECT D.*,R.* FROM ROUTE R LEFT JOIN APP_DELEGATION D ON (R.TAS_UID=D.TAS_UID) WHERE APP_UID="' . $sAppUid . '" AND ROU_NEXT_TASK="' . $sTasUid . '"';
            print $sql;

            while (is_array($row)) {
                switch ($row['DEL_THREAD_STATUS']) {
                    case 'OPEN':
                        //case 'NONE':
                        $open = true;
                        break;
                    case 'CLOSED':
                        //case 'DONE':
                        //case 'NOTDONE':
                        break;
                    case '':
                    case null:
                    default:
                        $open = $this->isRouteOpen($sAppUid, $row['TAS_UID']);
                        break;
                }
                if ($open)
                    return true;
                $rs->next();
                $row = $rs->getRow();
            }
            return false;
        }
        catch (exception $e) {
            throw ($e);
        }

    }

    /*
    * newAppDelegation
    * @param string $sProUid,
    * @param string $sAppUid,
    * @param string $sTasUid,
    * @param string $sUsrUid
    * @param string $iAppThreadIndex
    * @return
    */
    function newAppDelegation($sProUid, $sAppUid, $sTasUid, $sUsrUid, $sPrevious, $sPriority, $sDelType, $iAppThreadIndex = 1)
    {
        try {
            $appDel = new AppDelegation();
            $delIndex = $appDel->createAppDelegation($sProUid, $sAppUid, $sTasUid, $sUsrUid, $iAppThreadIndex);
            $aData = array();
            $aData['APP_UID'] = $sAppUid;
            $aData['DEL_INDEX'] = $delIndex;
            $aData['DEL_PREVIOUS'] = $sPrevious;

            //according schema posible values are NORMAL/ADHOC, but the logic in cases_Steps brings a PARALLEL
            //$aData['DEL_TYPE'] = $sDelType;
            if ($appDel->validate()) {
                $appDel->update($aData);
                return $delIndex;
            } else {
                $msg = '';
                foreach ($appDel->getValidationFailures() as $objValidationFailure)
                    $msg .= $objValidationFailure->getMessage() . "<br/>";
                throw (new Exception('Failed Data validation. ' . $msg));
            }

        }
        catch (exception $e) {
            throw ($e);
        }

    }

    /*
    * updateAppDelegation
    * @param string $sAppUid,
    * @param string $iDelIndex
    * @param string $iAppThreadIndex,
    * @return
    */
    function updateAppDelegation($sAppUid, $iDelIndex, $iAppThreadIndex)
    {
        try {
            $appDelegation = new AppDelegation();
            $aData = array();
            $aData['APP_UID'] = $sAppUid;
            $aData['DEL_INDEX'] = $iDelIndex;
            $aData['DEL_THREAD'] = $iAppThreadIndex;

            $appDelegation->update($aData);
            return true;

        }
        catch (exception $e) {
            throw ($e);
        }

    }

    /*
    * GetAllDelegations
    * @param string $sAppUid
    * @return
    */
    function GetAllDelegations($sAppUid)
    {
        //('SELECT * FROM APP_DELEGATION WHERE APP_UID="'.$currentDelegation['APP_UID'].'" ');
        try {
            $aDelegations = array();
            $c = new Criteria();
            $c->add(AppDelegationPeer::APP_UID, $sAppUid);
            $rs = AppDelegationPeer::doSelectRs($c);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $row = $rs->getRow();
            while (is_array($row)) {
                $aDelegations[] = $row;
                $rs->next();
                $row = $rs->getRow();
            }
            return $aDelegations;
        }
        catch (exception $e) {
            throw ($e);
        }
    }

    /*
    * GetAllDelegations
    * @param string $sAppUid
    * @return
    */
    function GetAllThreads($sAppUid)
    {
        //('SELECT * FROM APP_DELEGATION WHERE APP_UID="'.$currentDelegation['APP_UID'].'" ');
        try {
            $aThreads = array();
            $c = new Criteria();
            $c->add(AppThreadPeer::APP_UID, $sAppUid);
            $rs = AppThreadPeer::doSelectRs($c);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $row = $rs->getRow();
            while (is_array($row)) {
                $aThreads[] = $row;
                $rs->next();
                $row = $rs->getRow();
            }
            return $aThreads;
        }
        catch (exception $e) {
            throw ($e);
        }
    }


    /*
    * updateAppThread
    * @param string $sAppUid,
    * @param string $iAppThreadIndex,
    * @param string $iNewDelIndex
    * @return
    */
    function updateAppThread($sAppUid, $iAppThreadIndex, $iNewDelIndex)
    {
        try {
            $appThread = new AppThread();
            $aData = array();
            $aData['APP_UID'] = $sAppUid;
            $aData['APP_THREAD_INDEX'] = $iAppThreadIndex;
            $aData['DEL_INDEX'] = $iNewDelIndex;

            $appThread->update($aData);
            return $iNewDelIndex;

        }
        catch (exception $e) {
            throw ($e);
        }

    }

    /*
    * closeAppThread
    * @param string $sAppUid,
    * @param string $iAppThreadIndex,
    * @return
    */
    function closeAppThread($sAppUid, $iAppThreadIndex)
    {
        try {
            $appThread = new AppThread();
            $aData = array();
            $aData['APP_UID'] = $sAppUid;
            $aData['APP_THREAD_INDEX'] = $iAppThreadIndex;
            $aData['APP_THREAD_STATUS'] = 'CLOSED';

            $appThread->update($aData);
            return true;

        }
        catch (exception $e) {
            throw ($e);
        }

    }

    /*
    * closeAllDelegations
    * @param string $sAppUid
    * @return
    */
    function closeAllThreads($sAppUid)
    {
        try {
            //Execute('UPDATE APP_DELEGATION SET DEL_THREAD_STATUS="CLOSED" WHERE APP_UID="$sAppUid" AND DEL_THREAD_STATUS="OPEN"');
            $c = new Criteria();
            $c->add(AppThreadPeer::APP_UID, $sAppUid);
            $c->add(AppThreadPeer::APP_THREAD_STATUS, 'OPEN');
            $rowObj = AppThreadPeer::doSelect($c);
            foreach ($rowObj as $appThread) {
                $appThread->setAppThreadStatus('CLOSED');
                if ($appThread->Validate()) {
                    $appThread->Save();
                } else {
                    $msg = '';
                    foreach ($this->getValidationFailures() as $objValidationFailure)
                        $msg .= $objValidationFailure->getMessage() . "<br/>";
                    throw (new PropelException('The row cannot be created!', new PropelException($msg)));
                }
            }
        }
        catch (exception $e) {
            throw ($e);
        }
    }


    /*
    * newAppThread
    * @param string $sAppUid,
    * @param string $iNewDelIndex
    * @param string $iAppParent
    * @return $iAppThreadIndex $iNewDelIndex, $iAppThreadIndex );

    */
    function newAppThread($sAppUid, $iNewDelIndex, $iAppParent)
    {
        try {
            $appThread = new AppThread();
            return $appThread->createAppThread($sAppUid, $iNewDelIndex, $iAppParent);

        }
        catch (exception $e) {
            throw ($e);
        }

    }


    /*
    * closeAllDelegations
    * @param string $sAppUid
    * @return
    */
    function closeAllDelegations($sAppUid)
    {
        try {
            //Execute('UPDATE APP_DELEGATION SET DEL_THREAD_STATUS="CLOSED" WHERE APP_UID="$sAppUid" AND DEL_THREAD_STATUS="OPEN"');
            $c = new Criteria();
            $c->add(AppDelegationPeer::APP_UID, $sAppUid);
            $c->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');
            $rowObj = AppDelegationPeer::doSelect($c);
            foreach ($rowObj as $appDel) {
                $appDel->setDelThreadStatus('CLOSED');
                if ($appDel->Validate()) {
                    $appDel->Save();
                } else {
                    $msg = '';
                    foreach ($this->getValidationFailures() as $objValidationFailure)
                        $msg .= $objValidationFailure->getMessage() . "<br/>";
                    throw (new PropelException('The row cannot be created!', new PropelException($msg)));
                }
            }
        }
        catch (exception $e) {
            throw ($e);
        }
    }

    /*
    * CloseCurrentDelegation
    * @param string $sAppUid
    * @param string $iDelIndex
    * @return Fields
    */
    function CloseCurrentDelegation($sAppUid, $iDelIndex)
    {
        try {
            //Execute('UPDATE APP_DELEGATION SET DEL_THREAD_STATUS="CLOSED" WHERE APP_UID="$sAppUid" AND DEL_THREAD_STATUS="OPEN"');
            $c = new Criteria();
            $c->add(AppDelegationPeer::APP_UID, $sAppUid);
            $c->add(AppDelegationPeer::DEL_INDEX, $iDelIndex);
            $rowObj = AppDelegationPeer::doSelect($c);
            G::LoadClass('dates');
            $oDates = new dates();
            foreach ($rowObj as $appDel) {
                $appDel->setDelThreadStatus('CLOSED');
                $appDel->setDelFinishDate('now');
                $appDel->setDelDuration($oDates->calculateDuration($appDel->getDelInitDate(), $appDel->getDelFinishDate(), null, null, $appDel->getTasUid()));
                if ($appDel->Validate()) {
                    $appDel->Save();
                } else {
                    $msg = '';
                    foreach ($this->getValidationFailures() as $objValidationFailure)
                        $msg .= $objValidationFailure->getMessage() . "<br/>";
                    throw (new PropelException('The row cannot be created!', new PropelException($msg)));
                }
            }
        }
        catch (exception $e) {
            throw ($e);
        }
    }



    /*
    * ReactivateCurrentDelegation
    * @Description:  This function reativate the case previously cancelled from to do
    * @param string $sAppUid
    * @param string $iDelIndex
    * @return Fields
    */
    function ReactivateCurrentDelegation($sAppUid, $iDelegation)
    {
        try {
            $c = new Criteria();
            $c->add(AppDelegationPeer::APP_UID, $sAppUid);
            $c->add(AppDelegationPeer::DEL_INDEX, $iDelegation);

            $rowObj = AppDelegationPeer::doSelect($c);
            foreach ($rowObj as $appDel) {
                $appDel->setDelThreadStatus('OPEN');
                $appDel->setDelFinishDate(null);
                if ($appDel->Validate()) {
                    $appDel->Save();
                } else {
                    $msg = '';
                    foreach ($this->getValidationFailures() as $objValidationFailure)
                        $msg .= $objValidationFailure->getMessage() . "<br/>";
                    throw (new PropelException('The row cannot be created!', new PropelException($msg)));
                }
            }
        }
        catch (exception $e) {
            throw ($e);
        }
    }


    /*
    * Start a case
    * in the array is fundamental send two elements
    * one of them is TAS_UID and the other element is USR_UID
    * @param string $aData
    * @return variant
    */
    function startCase($sTasUid, $sUsrUid)
    {
        if ($sTasUid != '' && $sUsrUid != '') {
            try {
                $this->Task = new Task;
                $this->Task->Load($sTasUid);

                //Process
                $sProUid = $this->Task->getProUid();
                $this->Process = new Process;
                $proFields = $this->Process->Load($sProUid);

                //application
                $Application = new Application;
                $sAppUid = $Application->create($sProUid, $sUsrUid);

                //appDelegation
                $AppDelegation = new AppDelegation;
                $iAppThreadIndex = 1; //start thread
                $iDelIndex = $AppDelegation->createAppDelegation($sProUid, $sAppUid, $sTasUid, $sUsrUid, $iAppThreadIndex);

                //appThread
                $AppThread = new AppThread;
                $iAppThreadIndex = $AppThread->createAppThread($sAppUid, $iDelIndex, 0);
                //DONE: Al ya existir un delegation, se puede "calcular" el caseTitle.
                $Fields = $Application->toArray(BasePeer::TYPE_FIELDNAME);
                $Fields['APP_TITLE'] = self::refreshCaseTitle($sAppUid, G::array_merges(G::getSystemConstants(), unserialize($Fields['APP_DATA'])));
                $Fields['APP_DESCRIPTION'] = self::refreshCaseDescription($sAppUid, G::array_merges(G::getSystemConstants(), unserialize($Fields['APP_DATA'])));
                //$Fields['APP_PROC_CODE'] = self::refreshCaseStatusCode($sAppUid, G::array_merges(G::getSystemConstants(), unserialize($Fields['APP_DATA'])));
                $caseNumber = $Fields['APP_NUMBER'];
                $Application->update($Fields);
                //Update the task last assigned (for web entry an web services)
                G::LoadClass('derivation');
                $oDerivation = new Derivation();
                $oDerivation->setTasLastAssigned($sTasUid, $sUsrUid);

            }
            catch (exception $e) {
                throw ($e);
            }
        } else {
            throw (new Exception('You tried to start a new case without send the USER UID or TASK UID!'));
        }

        //call plugin
        if (class_exists('folderData')) {
            $folderData = new folderData($sProUid, $proFields['PRO_TITLE'], $sAppUid, $Fields['APP_TITLE'], $sUsrUid);
            $oPluginRegistry = &PMPluginRegistry::getSingleton();
            $oPluginRegistry->executeTriggers(PM_CREATE_CASE, $folderData);
        }
        //end plugin

        return array('APPLICATION' => $sAppUid, 'INDEX' => $iDelIndex, 'PROCESS' => $sProUid, 'CASE_NUMBER' => $caseNumber);
    }

    /*
    * Get the next step
    * @param string $sProUid
    * @param string $sAppUid
    * @param integer $iDelIndex
    * @param integer $iPosition
    * @return array
    */
    function getNextStep($sProUid = '', $sAppUid = '', $iDelIndex = 0, $iPosition = 0)
    {
        G::LoadClass('pmScript');
        $oPMScript = new PMScript();
        $oApplication = new Application();
        $aFields = $oApplication->load($sAppUid);
        if (!is_array($aFields['APP_DATA'])) {
            $aFields['APP_DATA'] = G::array_merges(G::getSystemConstants(), unserialize($aFields['APP_DATA']));
        }
        $oPMScript->setFields($aFields['APP_DATA']);

        try {
            //get the current Delegation, and TaskUID
            $c = new Criteria('workflow');
            $c->add(AppDelegationPeer::PRO_UID, $sProUid);
            $c->add(AppDelegationPeer::APP_UID, $sAppUid);
            $c->add(AppDelegationPeer::DEL_INDEX, $iDelIndex);
            $aRow = AppDelegationPeer::doSelect($c);

            if (!isset($aRow[0]))
                return false;

            $sTaskUid = $aRow[0]->getTasUid();

            //get max step for this task
            $c = new Criteria();
            $c->clearSelectColumns();
            $c->addSelectColumn('MAX(' . StepPeer::STEP_POSITION . ')');
            $c->add(StepPeer::PRO_UID, $sProUid);
            $c->add(StepPeer::TAS_UID, $sTaskUid);
            $rs = StepPeer::doSelectRS($c);
            $rs->next();
            $row = $rs->getRow();
            $iLastStep = intval($row[0]);

            $iPosition += 1;
            $aNextStep = null;
            if ($iPosition <= $iLastStep) {
                //to do:  		$oApplication = new Application($this->_dbc);
                //to do:  		$oApplication->load($sApplicationUID);
                //to do:  		G::LoadClass('pmScript');
                //to do:  		$oPMScript = new PMScript();
                //to do:  		$oPMScript->setFields($oApplication->Fields['APP_DATA']);
                while ($iPosition <= $iLastStep) {
                    $bAccessStep = false;
                    //step
                    $oStep = new Step;
                    $oStep = $oStep->loadByProcessTaskPosition($sProUid, $sTaskUid, $iPosition);
                    if ($oStep) {
                      if ($oStep->getStepCondition() !== '') {
                          $oPMScript->setScript($oStep->getStepCondition());
                          $bAccessStep = $oPMScript->evaluate();
                      } else {
                          $bAccessStep = true;
                      }

                      if ($bAccessStep) {
                          switch ($oStep->getStepTypeObj()) {
                              case 'DYNAFORM':
                                  $sAction = 'EDIT';
                                  break;
                              case 'OUTPUT_DOCUMENT':
                                  $sAction = 'GENERATE';
                                  break;
                              case 'INPUT_DOCUMENT':
                                  $sAction = 'ATTACH';
                                  break;
                              case 'EXTERNAL':
                                  $sAction = 'EDIT';
                                  break;
                              case 'MESSAGE':
                                  $sAction = '';
                                  break;
                          }
                          $aNextStep = array('TYPE' => $oStep->getStepTypeObj(), 'UID' => $oStep->getStepUidObj(), 'POSITION' => $oStep->getStepPosition(), 'PAGE' => 'cases_Step?TYPE=' . $oStep->getStepTypeObj() . '&UID=' . $oStep->
                              getStepUidObj() . '&POSITION=' . $oStep->getStepPosition() . '&ACTION=' . $sAction);
                          $iPosition = $iLastStep;
                      }
                    }
                    $iPosition += 1;
                }
            }
            if (!$aNextStep) {
                $aNextStep = array('TYPE' => 'DERIVATION', 'UID' => -1, 'POSITION' => ($iLastStep + 1), 'PAGE' => 'cases_Step?TYPE=ASSIGN_TASK&UID=-1&POSITION=10000&ACTION=ASSIGN');
            }
            return $aNextStep;
        }
        catch (exception $e) {
            throw ($e);
        }
    }

    /*
    * Get the previous step
    * @param string $sProUid
    * @param string $sAppUid
    * @param integer $iDelIndex
    * @param integer $iPosition
    * @return array
    */
    function getPreviousStep($sProUid = '', $sAppUid = '', $iDelIndex = 0, $iPosition = 0)
    {
        //Note: Depreciated, delete in the future
        G::LoadClass('pmScript');
        $oPMScript = new PMScript();

        try {
            //get the current Delegation, and TaskUID
            $c = new Criteria();
            $c->add(AppDelegationPeer::PRO_UID, $sProUid);
            $c->add(AppDelegationPeer::APP_UID, $sAppUid);
            $c->add(AppDelegationPeer::DEL_INDEX, $iDelIndex);
            $aRow = AppDelegationPeer::doSelect($c);

            $sTaskUid = $aRow[0]->getTasUid();
            $iFirstStep = 1;

            if ($iPosition == 10000) {
                //get max step for this task
                $c = new Criteria();
                $c->clearSelectColumns();
                $c->addSelectColumn('MAX(' . StepPeer::STEP_POSITION . ')');
                $c->add(StepPeer::PRO_UID, $sProUid);
                $c->add(StepPeer::TAS_UID, $sTaskUid);
                $rs = StepPeer::doSelectRS($c);
                $rs->next();
                $row = $rs->getRow();
                $iPosition = intval($row[0]);
            } else {
                $iPosition -= 1;
            }

            $aPreviousStep = null;
            if ($iPosition >= 1) {
                //to do:  		G::LoadClass('application');
                //to do:  		$oApplication = new Application($this->_dbc);
                //to do:  		$oApplication->load($sApplicationUID);
                //to do:  		G::LoadClass('pmScript');
                //to do:  		$oPMScript = new PMScript();
                //to do:  		$oPMScript->setFields($oApplication->Fields['APP_DATA']);

                while ($iPosition >= $iFirstStep) {
                    $bAccessStep = false;
                    //step
                    $oStep = new Step;
                    $oStep = $oStep->loadByProcessTaskPosition($sProUid, $sTaskUid, $iPosition);
                    if ($oStep) {
                      if ($oStep->getStepCondition() !== '') {
                          $oPMScript->setScript($oStep->getStepCondition());
                          $bAccessStep = $oPMScript->evaluate();
                      } else {
                          $bAccessStep = true;
                      }
                      if ($bAccessStep) {
                          switch ($oStep->getStepTypeObj()) {
                              case 'DYNAFORM':
                                  $sAction = 'EDIT';
                                  break;
                              case 'OUTPUT_DOCUMENT':
                                  $sAction = 'GENERATE';
                                  break;
                              case 'INPUT_DOCUMENT':
                                  $sAction = 'ATTACH';
                                  break;
                              case 'EXTERNAL':
                                  $sAction = 'EDIT';
                                  break;
                              case 'MESSAGE':
                                  $sAction = '';
                                  break;
                          }
                          $aPreviousStep = array('TYPE' => $oStep->getStepTypeObj(), 'UID' => $oStep->getStepUidObj(), 'POSITION' => $oStep->getStepPosition(), 'PAGE' => 'cases_Step?TYPE=' . $oStep->getStepTypeObj() . '&UID=' .
                              $oStep->getStepUidObj() . '&POSITION=' . $oStep->getStepPosition() . '&ACTION=' . $sAction);
                          $iPosition = $iFirstStep;
                      }
                    }
                    $iPosition -= 1;
                }
            }

            if (!$aPreviousStep) {
                $aPreviousStep = false;
            }
            return $aPreviousStep;
        }
        catch (exception $e) {
            throw ($e);
        }
    }

    function getNextSupervisorStep($sProcessUID, $iPosition)
    {
        $iPosition += 1;
        $oCriteria = new Criteria();
        $oCriteria->add(StepSupervisorPeer::PRO_UID, $sProcessUID);
        $oCriteria->add(StepSupervisorPeer::STEP_TYPE_OBJ, 'DYNAFORM');
        $oCriteria->add(StepSupervisorPeer::STEP_POSITION, $iPosition);
        $oDataset = StepSupervisorPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRow = $oDataset->getRow();
        if (!$aRow) {
            $oCriteria = new Criteria();
            $oCriteria->add(StepSupervisorPeer::PRO_UID, $sProcessUID);
            $oCriteria->add(StepSupervisorPeer::STEP_TYPE_OBJ, 'DYNAFORM');
            $oCriteria->add(StepSupervisorPeer::STEP_POSITION, 1);
            $oDataset = StepSupervisorPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aRow = $oDataset->getRow();
        }
        $aNextStep = array('UID' => $aRow['STEP_UID_OBJ'], 'POSITION' => $aRow['STEP_POSITION']);
        return $aNextStep;
    }

    function getPreviousSupervisorStep($sProcessUID, $iPosition)
    {
        $iPosition -= 1;
        if ($iPosition > 0) {
            $oCriteria = new Criteria();
            $oCriteria->add(StepSupervisorPeer::PRO_UID, $sProcessUID);
            $oCriteria->add(StepSupervisorPeer::STEP_TYPE_OBJ, 'DYNAFORM');
            $oCriteria->add(StepSupervisorPeer::STEP_POSITION, $iPosition);
            $oDataset = StepSupervisorPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aRow = $oDataset->getRow();
            if (!$aRow) {
                $oCriteria = new Criteria();
                $oCriteria->add(StepSupervisorPeer::PRO_UID, $sProcessUID);
                $oCriteria->add(StepSupervisorPeer::STEP_TYPE_OBJ, 'DYNAFORM');
                $oCriteria->add(StepSupervisorPeer::STEP_POSITION, 1);
                $oDataset = StepSupervisorPeer::doSelectRS($oCriteria);
                $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $oDataset->next();
                $aRow = $oDataset->getRow();
            }
            $aNextStep = array('UID' => $aRow['STEP_UID_OBJ'], 'POSITION' => $aRow['STEP_POSITION']);
            return $aNextStep;
        }
        else
        {
					return false;
		    }
    }

    function getTransferHistoryCriteria($sAppUid)
    {
        $c = new Criteria('workflow');
        $c->addAsColumn('TAS_TITLE', 'TAS_TITLE.CON_VALUE');
        $c->addSelectColumn(UsersPeer::USR_FIRSTNAME);
        $c->addSelectColumn(UsersPeer::USR_LASTNAME);
        $c->addSelectColumn(AppDelegationPeer::DEL_DELEGATE_DATE);
        $c->addAsColumn('USR_NAME', "CONCAT(USR_LASTNAME, ' ', USR_FIRSTNAME)");
        $c->addSelectColumn(AppDelegationPeer::DEL_INIT_DATE);
        //$c->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE);
        $c->addAsColumn('DEL_FINISH_DATE', "IF (DEL_FINISH_DATE IS NULL, '-', " . AppDelegationPeer::DEL_FINISH_DATE . ") ");

        //$c->addSelectColumn(AppDelayPeer::APP_TYPE);
        $c->addAsColumn('APP_TYPE', "IF (DEL_FINISH_DATE IS NULL, 'IN_PROGRESS', " . AppDelayPeer::APP_TYPE . ") ");
        $c->addSelectColumn(AppDelayPeer::APP_ENABLE_ACTION_DATE);
        $c->addSelectColumn(AppDelayPeer::APP_DISABLE_ACTION_DATE);
        //APP_DELEGATION LEFT JOIN USERS
        $c->addJoin(AppDelegationPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);

        //APP_DELAY FOR MORE DESCRIPTION
        //$c->addJoin(AppDelegationPeer::DEL_INDEX, AppDelayPeer::APP_DEL_INDEX, Criteria::LEFT_JOIN);
        //$c->addJoin(AppDelegationPeer::APP_UID, AppDelayPeer::APP_UID, Criteria::LEFT_JOIN);
        $del = DBAdapter::getStringDelimiter();
        $app = array();
        $app[] = array(AppDelegationPeer::DEL_INDEX, AppDelayPeer::APP_DEL_INDEX);
        $app[] = array(AppDelegationPeer::APP_UID, AppDelayPeer::APP_UID);
        $c->addJoinMC($app, Criteria::LEFT_JOIN);

        //  LEFT JOIN CONTENT TAS_TITLE
        $c->addAlias("TAS_TITLE", 'CONTENT');
        $del = DBAdapter::getStringDelimiter();
        $appTitleConds = array();
        $appTitleConds[] = array(AppDelegationPeer::TAS_UID, 'TAS_TITLE.CON_ID');
        $appTitleConds[] = array('TAS_TITLE.CON_CATEGORY', $del . 'TAS_TITLE' . $del);
        $appTitleConds[] = array('TAS_TITLE.CON_LANG', $del . SYS_LANG . $del);
        $c->addJoinMC($appTitleConds, Criteria::LEFT_JOIN);

        //WHERE
        $c->add(AppDelegationPeer::APP_UID, $sAppUid);

        //ORDER BY
        $c->clearOrderByColumns();
        $c->addAscendingOrderByColumn(AppDelegationPeer::DEL_DELEGATE_DATE);

        return $c;
    }
    /*
    * Get the condition for Cases List
    * @param string $sTypeList
    * @param string $sUIDUserLogged
    * @return array
    */

    function getConditionCasesList($sTypeList = 'all', $sUIDUserLogged = '')
    {
        $c = new Criteria('workflow');
        $c->clearSelectColumns();
        $c->addSelectColumn(ApplicationPeer::APP_UID);
        $c->addSelectColumn(ApplicationPeer::APP_NUMBER);
        $c->addSelectColumn(ApplicationPeer::APP_UPDATE_DATE);
        $c->addSelectColumn(AppDelegationPeer::DEL_PRIORITY);
        //$c->addSelectColumn(AppDelegationPeer::DEL_TASK_DUE_DATE);
		$c->addAsColumn('DEL_TASK_DUE_DATE', " IF (" . AppDelegationPeer::DEL_TASK_DUE_DATE . " <= NOW(), CONCAT('<span style=\'color:red\';>', " . AppDelegationPeer::DEL_TASK_DUE_DATE . ", '</span>'), " . AppDelegationPeer::DEL_TASK_DUE_DATE . ") ");
        $c->addSelectColumn(AppDelegationPeer::DEL_INDEX);
        $c->addSelectColumn(AppDelegationPeer::TAS_UID);
        $c->addSelectColumn(AppDelegationPeer::DEL_INIT_DATE);
        $c->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE);
        $c->addSelectColumn(UsersPeer::USR_UID);
        $c->addAsColumn('APP_CURRENT_USER', "CONCAT(USERS.USR_LASTNAME, ' ', USERS.USR_FIRSTNAME)");
        $c->addSelectColumn(ApplicationPeer::APP_STATUS);
        $c->addAsColumn('APP_TITLE', 'APP_TITLE.CON_VALUE');
        $c->addAsColumn('APP_PRO_TITLE', 'PRO_TITLE.CON_VALUE');
        $c->addAsColumn('APP_TAS_TITLE', 'TAS_TITLE.CON_VALUE');
        //$c->addAsColumn('APP_DEL_PREVIOUS_USER', 'APP_LAST_USER.USR_USERNAME');
		    $c->addAsColumn('APP_DEL_PREVIOUS_USER', "CONCAT(APP_LAST_USER.USR_LASTNAME, ' ', APP_LAST_USER.USR_FIRSTNAME)");

        $c->addAlias("APP_TITLE", 'CONTENT');
        $c->addAlias("PRO_TITLE", 'CONTENT');
        $c->addAlias("TAS_TITLE", 'CONTENT');
        $c->addAlias("APP_PREV_DEL", 'APP_DELEGATION');
        $c->addAlias("APP_LAST_USER", 'USERS');

        $c->addJoin(ApplicationPeer::APP_UID, AppDelegationPeer::APP_UID, Criteria::LEFT_JOIN);
        $c->addJoin(AppDelegationPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN);
        $appThreadConds[] = array(ApplicationPeer::APP_UID, AppThreadPeer::APP_UID);
        $appThreadConds[] = array(AppDelegationPeer::DEL_INDEX, AppThreadPeer::DEL_INDEX);
        $c->addJoinMC($appThreadConds, Criteria::LEFT_JOIN);
        $c->addJoin(AppDelegationPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);

        $del = DBAdapter::getStringDelimiter();
        $appTitleConds = array();
        $appTitleConds[] = array(ApplicationPeer::APP_UID, 'APP_TITLE.CON_ID');
        $appTitleConds[] = array('APP_TITLE.CON_CATEGORY', $del . 'APP_TITLE' . $del);
        $appTitleConds[] = array('APP_TITLE.CON_LANG', $del . SYS_LANG . $del);
        $c->addJoinMC($appTitleConds, Criteria::LEFT_JOIN);

        $proTitleConds = array();
        $proTitleConds[] = array(ApplicationPeer::PRO_UID, 'PRO_TITLE.CON_ID');
        $proTitleConds[] = array('PRO_TITLE.CON_CATEGORY', $del . 'PRO_TITLE' . $del);
        $proTitleConds[] = array('PRO_TITLE.CON_LANG', $del . SYS_LANG . $del);
        $c->addJoinMC($proTitleConds, Criteria::LEFT_JOIN);

        $tasTitleConds = array();
        $tasTitleConds[] = array(AppDelegationPeer::TAS_UID, 'TAS_TITLE.CON_ID');
        $tasTitleConds[] = array('TAS_TITLE.CON_CATEGORY', $del . 'TAS_TITLE' . $del);
        $tasTitleConds[] = array('TAS_TITLE.CON_LANG', $del . SYS_LANG . $del);
        $c->addJoinMC($tasTitleConds, Criteria::LEFT_JOIN);

        $prevConds = array();
        $prevConds[] = array(ApplicationPeer::APP_UID, 'APP_PREV_DEL.APP_UID');
        $prevConds[] = array('APP_PREV_DEL.DEL_INDEX', AppDelegationPeer::DEL_PREVIOUS);
        $c->addJoinMC($prevConds, Criteria::LEFT_JOIN);

        $usrConds = array();
        $usrConds[] = array('APP_PREV_DEL.USR_UID', 'APP_LAST_USER.USR_UID');
        $c->addJoinMC($usrConds, Criteria::LEFT_JOIN);

        $c->add(TaskPeer::TAS_TYPE, 'SUBPROCESS', Criteria::NOT_EQUAL);

        if ($sTypeList != 'gral' && $sTypeList != 'to_revise') {
            $c->add(UsersPeer::USR_UID, $sUIDUserLogged);
        }

        $filesList = array('cases/cases_ListAll', 'cases/cases_ListTodo', 'cases/cases_ListDraft', 'cases/cases_ListOnHold', 'cases/cases_ListCancelled', 'cases/cases_ListCompleted',
            'cases/cases_ListToRevise');
        switch ($sTypeList) {
            case 'all':
                $c->add($c->getNewCriterion(AppThreadPeer::APP_THREAD_STATUS, 'OPEN')->addOr($c->getNewCriterion(ApplicationPeer::APP_STATUS, 'COMPLETED')->addAnd($c->getNewCriterion(AppDelegationPeer::DEL_PREVIOUS,
                    0))));
                $c->addDescendingOrderByColumn(ApplicationPeer::APP_NUMBER);
                $xmlfile = $filesList[0];
                break;
            case 'to_do':
                $c->add(ApplicationPeer::APP_STATUS, 'TO_DO');
                $c->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
                $c->add(AppThreadPeer::APP_THREAD_STATUS, 'OPEN');
                $c->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');
                $c->addDescendingOrderByColumn(ApplicationPeer::APP_NUMBER);
                $xmlfile = $filesList[1];
                break;
            case 'draft':
                $c->add(ApplicationPeer::APP_STATUS, 'DRAFT');
                $c->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
                $c->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');
                $c->addDescendingOrderByColumn(ApplicationPeer::APP_NUMBER);
                $xmlfile = $filesList[2];
                break;
            case 'paused':
                $appDelayConds[] = array(ApplicationPeer::APP_UID, AppDelayPeer::APP_UID);
                $appDelayConds[] = array(AppDelegationPeer::DEL_INDEX, AppDelayPeer::APP_DEL_INDEX);
                $c->addJoinMC($appDelayConds, Criteria::LEFT_JOIN);
                $c->add(AppDelayPeer::APP_DELAY_UID, null, Criteria::ISNOTNULL);
                $c->add($c->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_USER, null, Criteria::ISNULL)->addOr($c->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_USER, 0)));
                $c->addDescendingOrderByColumn(ApplicationPeer::APP_NUMBER);
                $xmlfile = $filesList[3];
                break;
            case 'cancelled':
                $c->add($c->getNewCriterion(AppThreadPeer::APP_THREAD_STATUS, 'OPEN')->addAnd($c->getNewCriterion(ApplicationPeer::APP_STATUS, 'CANCELLED')));
                $c->addDescendingOrderByColumn(ApplicationPeer::APP_NUMBER);
                $xmlfile = $filesList[4];
                break;
            case 'completed':
                $c->add(ApplicationPeer::APP_STATUS, 'COMPLETED');
                $c->add(AppDelegationPeer::DEL_PREVIOUS, 0);
                $c->addDescendingOrderByColumn(ApplicationPeer::APP_NUMBER);
                $xmlfile = $filesList[5];
                break;
            case 'gral':
                $c->add($c->getNewCriterion(AppThreadPeer::APP_THREAD_STATUS, 'OPEN')->addOr($c->getNewCriterion(ApplicationPeer::APP_STATUS, 'COMPLETED')->addAnd($c->getNewCriterion(AppDelegationPeer::DEL_PREVIOUS,
                    0))));
                $c->addDescendingOrderByColumn(ApplicationPeer::APP_NUMBER);
                $xmlfile = $filesList[0];
                break;
            case 'to_revise':
                require_once 'classes/model/ProcessUser.php';
                $oCriteria = new Criteria('workflow');
                $oCriteria->add(ProcessUserPeer::USR_UID, $sUIDUserLogged);
                $oCriteria->add(ProcessUserPeer::PU_TYPE, 'SUPERVISOR');
                $oDataset = ProcessUserPeer::doSelectRS($oCriteria);
                $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $oDataset->next();
                $aProcesses = array();
                while ($aRow = $oDataset->getRow()) {
                    $aProcesses[] = $aRow['PRO_UID'];
                    $oDataset->next();
                }
                $c->add(ApplicationPeer::PRO_UID, $aProcesses, Criteria::IN);
                $c->add(ApplicationPeer::APP_STATUS, 'TO_DO');
                $c->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
                $c->add(AppThreadPeer::APP_THREAD_STATUS, 'OPEN');
                $c->add(AppDelegationPeer::DEL_THREAD_STATUS, 'OPEN');
                $c->addDescendingOrderByColumn(ApplicationPeer::APP_NUMBER);
                $xmlfile = $filesList[6];
                break;
        }
        /*
        * TODO: Revisar y decidir como se eliminaran variables de session xmlfors
        */
        //OPCION_1: Limpia de $_SESSION los listados no utilizados (solo listado de casos)
        foreach ($filesList as $file) {
            $id = G::createUID('', $file . '.xml');
            unset($_SESSION['pagedTable[' . $id . ']']);
            unset($_SESSION[$id]);
        }
        //OPCION_2: Limpia de $_SESSION todos los listados y xmlforms
        $cur = array_keys($_SESSION);
        foreach ($cur as $key) {
            if (substr($key, 0, 11) === "pagedTable[") {
                unset($_SESSION[$key]);
            } else {
                $xml = G::getUIDName($key, '');
                if (strpos($xml, '.xml') !== false)
                    unset($_SESSION[$key]);
            }
        }
        return array($c, $xmlfile);
    }

	/**
	*  @Author: erik@colosa.com
    *  @Description: This method set all cases with the APP_DISABLE_ACTION_DATE for today
	*/

    function ThrowUnpauseDaemon()
	{
		$today = date('Y-m-d');
		$c = new Criteria('workflow');
		$c->clearSelectColumns();
		$c->add($c->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_USER, null, Criteria::ISNULL)->addOr($c->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_USER, 0)));
		$c->add($c->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_DATE, $today.' 23:59:59', Criteria::LESS_EQUAL)->addAnd($c->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_DATE, null, Criteria::ISNOTNULL)));
		$d = AppDelayPeer::doSelectRS($c);
		$d->setFetchmode(ResultSet::FETCHMODE_ASSOC);
		$d->next();
		while ($aRow = $d->getRow()) {
			$this->unpauseCase($aRow['APP_UID'], $aRow['APP_DEL_INDEX'], 'System Daemon');
			$d->next();
		}
	}

    /*
    * Get the application UID by case number
    * @param integer $iApplicationNumber
    * @return string
    */
    function getApplicationUIDByNumber($iApplicationNumber)
    {
        $oCriteria = new Criteria();
        $oCriteria->add(ApplicationPeer::APP_NUMBER, $iApplicationNumber);
        $oApplication = ApplicationPeer::doSelectOne($oCriteria);
        if (!is_null($oApplication)) {
            return $oApplication->getAppUid();
        } else {
            return null;
        }
    }

    /*
    * Get the current delegation of a user
    * @param string $sApplicationUID
    * @param string $sUserUID
    * @return integer
    */
    function getCurrentDelegation($sApplicationUID = '', $sUserUID = '')
    {
        $oSession = new DBSession(new DBConnection());
        $oDataset = $oSession->Execute('SELECT
  	                                DEL_INDEX
  	                              FROM
  	                                APP_DELEGATION
  	                              WHERE
  	                                APP_UID           = "' . $sApplicationUID . '" AND
  	                                USR_UID           = "' . $sUserUID . '" AND
  	                                DEL_THREAD_STATUS = "OPEN"
  	                              ORDER BY
  	                                DEL_DELEGATE_DATE
  	                              DESC');
        $aRow = $oDataset->Read();
        return $aRow['DEL_INDEX'];
    }

    function loadTriggers($sTasUid, $sStepType, $sStepUidObj, $sTriggerType)
    {
        $aTriggers = array();
        if (($sStepUidObj != -1) && ($sStepUidObj != -2)) {
            $c = new Criteria();
            $c->clearSelectColumns();
            $c->addSelectColumn(StepPeer::STEP_UID);
            $c->add(StepPeer::TAS_UID, $sTasUid);
            $c->add(StepPeer::STEP_TYPE_OBJ, $sStepType);
            $c->add(StepPeer::STEP_UID_OBJ, $sStepUidObj);
            $rs = StepPeer::doSelectRS($c);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $rs->next();
            $row = $rs->getRow();
            $sStepUid = $row['STEP_UID'];
        } else {
            $sStepUid = $sStepUidObj;
        }
        $c = new Criteria();
        $c->clearSelectColumns();
		$c->addSelectColumn(TriggersPeer::TRI_UID);
        $c->addSelectColumn(StepTriggerPeer::ST_CONDITION);
        $c->addSelectColumn(TriggersPeer::TRI_TYPE);
        $c->addSelectColumn(TriggersPeer::TRI_WEBBOT);
        $c->add(StepTriggerPeer::STEP_UID, $sStepUid);
        $c->add(StepTriggerPeer::TAS_UID, $sTasUid);
        $c->add(StepTriggerPeer::ST_TYPE, $sTriggerType);
        $c->addJoin(StepTriggerPeer::TRI_UID, TriggersPeer::TRI_UID, Criteria::LEFT_JOIN);
        $c->addAscendingOrderByColumn(StepTriggerPeer::ST_POSITION);
        $rs = TriggersPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();
        while (is_array($row)) {
            $aTriggers[] = $row;
            $rs->next();
            $row = $rs->getRow();
        }
        return $aTriggers;
    }

    function executeTriggers($sTasUid, $sStepType, $sStepUidObj, $sTriggerType, $aFields = array())
    {
        $aTriggers = $this->loadTriggers($sTasUid, $sStepType, $sStepUidObj, $sTriggerType);

        if (count($aTriggers) > 0) {
            $oPMScript = new PMScript();
            $oPMScript->setFields($aFields);
            foreach ($aTriggers as $aTrigger) {
                $bExecute = true;
                if ($aTrigger['ST_CONDITION'] !== '') {
                    $oPMScript->setScript($aTrigger['ST_CONDITION']);
                    $bExecute = $oPMScript->evaluate();
                }
                if ($bExecute) {
                    $oPMScript->setScript($aTrigger['TRI_WEBBOT']);
                    $oPMScript->execute();
                }
            }
            return $oPMScript->aFields;
        } else {
            return $aFields;
        }
    }

	function getTriggerNames($triggers)
	{
		for($i=0; $i<count($triggers); $i++) {
			$c = new Criteria();
			$c->clearSelectColumns();
			$c->addSelectColumn(ContentPeer::CON_VALUE);
			$c->add(ContentPeer::CON_ID, $triggers[$i]['TRI_UID']);
			$c->add(ContentPeer::CON_VALUE, "", Criteria::NOT_EQUAL);
			$c->add(ContentPeer::CON_LANG, SYS_LANG);
			$rs = TriggersPeer::doSelectRS($c);
			$rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
			$rs->next();
			$row = $rs->getRow();

			$triggers_info[] = $row['CON_VALUE'];
		}
        return $triggers_info;
	}

    /*
    * Return the input documents list criteria object
    * @param string $sProcessUID
    * @return object
    */
    function getInputDocumentsCriteria($sApplicationUID, $iDelegation, $sDocumentUID)
    {
        try {
            require_once 'classes/model/AppDocument.php';
            $oAppDocument = new AppDocument();
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(AppDocumentPeer::APP_UID, $sApplicationUID);
            $oCriteria->add(AppDocumentPeer::DEL_INDEX, $iDelegation);
            $oCriteria->add(AppDocumentPeer::DOC_UID, $sDocumentUID);
            $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, 'INPUT');
            $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
            $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aInputDocuments = array();
            $aInputDocuments[] = array('APP_DOC_UID' => 'char', 'DOC_UID' => 'char', 'APP_DOC_COMMENT' => 'char', 'APP_DOC_FILENAME' => 'char', 'APP_DOC_INDEX' => 'integer');
            while ($aRow = $oDataset->getRow()) {
                $aAux = $oAppDocument->load($aRow['APP_DOC_UID']);
                $aFields = array('APP_DOC_UID' => $aAux['APP_DOC_UID'], 'DOC_UID' => $aAux['DOC_UID'], 'APP_DOC_COMMENT' => $aAux['APP_DOC_COMMENT'], 'APP_DOC_FILENAME' => $aAux['APP_DOC_FILENAME'], 'APP_DOC_INDEX' =>
                    $aAux['APP_DOC_INDEX']);
                if ($aFields['APP_DOC_FILENAME'] != '') {
                    $aFields['TITLE'] = $aFields['APP_DOC_FILENAME'];
                } else {
                    $aFields['TITLE'] = $aFields['APP_DOC_COMMENT'];
                }
                $aFields['POSITION'] = $_SESSION['STEP_POSITION'];
                $aFields['CONFIRM'] = G::LoadTranslation('ID_CONFIRM_DELETE_ELEMENT');
                $aInputDocuments[] = $aFields;
                $oDataset->next();
            }
            global $_DBArray;
            $_DBArray['inputDocuments'] = $aInputDocuments;
            $_SESSION['_DBArray'] = $_DBArray;
            G::LoadClass('ArrayPeer');
            $oCriteria = new Criteria('dbarray');
            $oCriteria->setDBArrayTable('inputDocuments');
            $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
            return $oCriteria;
        }
        catch (exception $oException) {
            throw $oException;
        }
    }

    function getInputDocumentsCriteriaToRevise($sApplicationUID)
    {
        try {
            require_once 'classes/model/AppDocument.php';
            $oAppDocument = new AppDocument();
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(AppDocumentPeer::APP_UID, $sApplicationUID);
            $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, 'INPUT');
            $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
            $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aInputDocuments = array();
            $aInputDocuments[] = array('APP_DOC_UID' => 'char', 'DOC_UID' => 'char', 'APP_DOC_COMMENT' => 'char', 'APP_DOC_FILENAME' => 'char', 'APP_DOC_INDEX' => 'integer');
            while ($aRow = $oDataset->getRow()) {

                $aAux = $oAppDocument->load($aRow['APP_DOC_UID']);
                $aFields = array('APP_DOC_UID' => $aAux['APP_DOC_UID'], 'DOC_UID' => $aAux['DOC_UID'], 'APP_DOC_COMMENT' => $aAux['APP_DOC_COMMENT'], 'APP_DOC_FILENAME' => $aAux['APP_DOC_FILENAME'], 'APP_DOC_INDEX' =>
                    $aAux['APP_DOC_INDEX']);

                if ($aFields['APP_DOC_FILENAME'] != '') {
                    $aFields['TITLE'] = $aFields['APP_DOC_FILENAME'];
                } else {
                    $aFields['TITLE'] = $aFields['APP_DOC_COMMENT'];
                }
                $aFields['CREATE_DATE'] = $aRow['APP_DOC_CREATE_DATE'];
                $aFields['TYPE'] = $aRow['APP_DOC_TYPE'];

                $aFields['POSITION'] = $_SESSION['STEP_POSITION'];
                $aFields['CONFIRM'] = G::LoadTranslation('ID_CONFIRM_DELETE_ELEMENT');
                $aInputDocuments[] = $aFields;
                $oDataset->next();
            }
            global $_DBArray;
            $_DBArray['inputDocuments'] = $aInputDocuments;
            $_SESSION['_DBArray'] = $_DBArray;
            G::LoadClass('ArrayPeer');
            $oCriteria = new Criteria('dbarray');
            $oCriteria->setDBArrayTable('inputDocuments');
            $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
            return $oCriteria;
        }
        catch (exception $oException) {
            throw $oException;
        }
    }

    function getOutputDocumentsCriteriaToRevise($sApplicationUID)
    {
        try {
            require_once 'classes/model/AppDocument.php';
            $oAppDocument = new AppDocument();
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(AppDocumentPeer::APP_UID, $sApplicationUID);
            $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, 'OUTPUT');
            $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
            $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aOutputDocuments = array();
            $aOutputDocuments[] = array('APP_DOC_UID' => 'char', 'DOC_UID' => 'char', 'APP_DOC_COMMENT' => 'char', 'APP_DOC_FILENAME' => 'char', 'APP_DOC_INDEX' => 'integer', 'APP_DOC_CREATE_DATE' => 'char');
            while ($aRow = $oDataset->getRow()) {
                $aAux = $oAppDocument->load($aRow['APP_DOC_UID']);
                $aFields = array('APP_DOC_UID' => $aAux['APP_DOC_UID'], 'DOC_UID' => $aAux['DOC_UID'], 'APP_DOC_COMMENT' => $aAux['APP_DOC_COMMENT'], 'APP_DOC_FILENAME' => $aAux['APP_DOC_FILENAME'], 'APP_DOC_INDEX' =>
                    $aAux['APP_DOC_INDEX'], 'APP_DOC_CREATE_DATE' => $aRow['APP_DOC_CREATE_DATE']);
                if ($aFields['APP_DOC_FILENAME'] != '') {
                    $aFields['TITLE'] = $aFields['APP_DOC_FILENAME'];
                } else {
                    $aFields['TITLE'] = $aFields['APP_DOC_COMMENT'];
                }
                $aOutputDocuments[] = $aFields;
                $oDataset->next();
            }
            global $_DBArray;
            $_DBArray['outputDocuments'] = $aOutputDocuments;
            $_SESSION['_DBArray'] = $_DBArray;
            G::LoadClass('ArrayPeer');
            $oCriteria = new Criteria('dbarray');
            $oCriteria->setDBArrayTable('outputDocuments');
            $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
            return $oCriteria;
        }
        catch (exception $oException) {
            throw $oException;
        }
    }

    function getCriteriaProcessCases($status, $PRO_UID)
    {
        $c = new Criteria('workflow');

        $c->add(ApplicationPeer::APP_STATUS, $status);
        $c->add(ApplicationPeer::PRO_UID, $PRO_UID);
        return $c;
    }

    function pauseCase($sApplicationUID, $iDelegation, $sUserUID, $sUnpauseDate = null)
    {
        $this->CloseCurrentDelegation($sApplicationUID, $iDelegation);
        $oApplication = new Application();
        $aFields = $oApplication->Load($sApplicationUID);
        $oCriteria = new Criteria('workflow');
        $oCriteria->clearSelectColumns();
        $oCriteria->addSelectColumn(AppThreadPeer::APP_THREAD_INDEX);
        $oCriteria->add(AppThreadPeer::APP_UID, $sApplicationUID);
        $oCriteria->add(AppThreadPeer::DEL_INDEX, $iDelegation);
        $oDataset = AppThreadPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRow = $oDataset->getRow();
        $aData['PRO_UID'] = $aFields['PRO_UID'];
        $aData['APP_UID'] = $sApplicationUID;
        $aData['APP_THREAD_INDEX'] = $aRow['APP_THREAD_INDEX'];
        $aData['APP_DEL_INDEX'] = $iDelegation;
        $aData['APP_TYPE'] = 'PAUSE';
        $aData['APP_STATUS'] = $aFields['APP_STATUS'];
        $aData['APP_DELEGATION_USER'] = $sUserUID;
        $aData['APP_ENABLE_ACTION_USER'] = $sUserUID;
        $aData['APP_ENABLE_ACTION_DATE'] = date('Y-m-d H:i:s');
        $aData['APP_DISABLE_ACTION_DATE'] = $sUnpauseDate;
        $oAppDelay = new AppDelay();
        $oAppDelay->create($aData);
    }

    function unpauseCase($sApplicationUID, $iDelegation, $sUserUID)
    {
        $oAppDelegation = new AppDelegation();
        $aFieldsDel = $oAppDelegation->Load($sApplicationUID, $iDelegation);
        $iIndex = $oAppDelegation->createAppDelegation($aFieldsDel['PRO_UID'], $aFieldsDel['APP_UID'], $aFieldsDel['TAS_UID'], $aFieldsDel['USR_UID'], $aFieldsDel['DEL_THREAD']);
        $aData = array();
        $aData['APP_UID'] = $aFieldsDel['APP_UID'];
        $aData['DEL_INDEX'] = $iIndex;
        $aData['DEL_PREVIOUS'] = $aFieldsDel['DEL_PREVIOUS'];
        $aData['DEL_TYPE'] = $aFieldsDel['DEL_TYPE'];
        $aData['DEL_PRIORITY'] = $aFieldsDel['DEL_PRIORITY'];
        $aData['DEL_DELEGATE_DATE'] = $aFieldsDel['DEL_DELEGATE_DATE'];
        $aData['DEL_INIT_DATE'] = date('Y-m-d H:i:s');
        $aData['DEL_FINISH_DATE'] = null;
        $oAppDelegation->update($aData);
        $oCriteria = new Criteria('workflow');
        $oCriteria->clearSelectColumns();
        $oCriteria->addSelectColumn(AppDelayPeer::APP_DELAY_UID);
        $oCriteria->addSelectColumn(AppDelayPeer::APP_THREAD_INDEX);
        $oCriteria->add(AppDelayPeer::APP_UID, $sApplicationUID);
        $oCriteria->add(AppDelayPeer::APP_DEL_INDEX, $iDelegation);
        $oCriteria->add(AppDelayPeer::APP_TYPE, 'PAUSE');
        $oCriteria->add($oCriteria->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_USER, null, Criteria::ISNULL)->addOr($oCriteria->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_USER, 0)));
        $oDataset = AppDelayPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRow = $oDataset->getRow();
        $oAppThread = new AppThread();
        $oAppThread->update(array('APP_UID' => $sApplicationUID, 'APP_THREAD_INDEX' => $aRow['APP_THREAD_INDEX'], 'DEL_INDEX' => $iIndex));
        $aData['APP_DELAY_UID'] = $aRow['APP_DELAY_UID'];
        $aData['APP_DISABLE_ACTION_USER'] = $sUserUID;
        $aData['APP_DISABLE_ACTION_DATE'] = date('Y-m-d H:i:s');
        $oAppDelay = new AppDelay();
        $aFieldsDelay = $oAppDelay->update($aData);
    }

    function cancelCase($sApplicationUID, $iIndex, $user_logged)
    {
        require_once 'classes/model/Application.php';
        require_once 'classes/model/AppDelay.php';
        require_once 'classes/model/AppThread.php';
        $oApplication = new Application();
        $aFields = $oApplication->load($sApplicationUID);
        $aFields['APP_STATUS'] = 'CANCELLED';
        $oApplication->update($aFields);
        $this->CloseCurrentDelegation($sApplicationUID, $iIndex);

        $delay = new AppDelay();
        $array['PRO_UID'] = $aFields['PRO_UID'];
        $array['APP_UID'] = $sApplicationUID;

        $c = new Criteria('workflow');
        $c->clearSelectColumns();
        $c->addSelectColumn(AppThreadPeer::APP_THREAD_INDEX);
        $c->add(AppThreadPeer::APP_UID, $sApplicationUID);
        $c->add(AppThreadPeer::DEL_INDEX, $iIndex);
        $oDataset = AppThreadPeer::doSelectRS($c);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRow = $oDataset->getRow();
        $array['APP_THREAD_INDEX'] = $aRow['APP_THREAD_INDEX'];
        $array['APP_DEL_INDEX'] = $iIndex;
        $array['APP_TYPE'] = 'CANCEL';

        $c = new Criteria('workflow');
        $c->clearSelectColumns();
        $c->addSelectColumn(ApplicationPeer::APP_STATUS);
        $c->add(ApplicationPeer::APP_UID, $sApplicationUID);
        $oDataset = ApplicationPeer::doSelectRS($c);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRow1 = $oDataset->getRow();
        $array['APP_STATUS'] = $aRow1['APP_STATUS'];

        $array['APP_DELEGATION_USER'] = $user_logged;
        $array['APP_ENABLE_ACTION_USER'] = $user_logged;
        $array['APP_ENABLE_ACTION_DATE'] = date('Y-m-d H:i:s');
        $delay->create($array);

        //Before cancel a case verify if is a child case
        $oCriteria2 = new Criteria('workflow');
  	    $oCriteria2->add(SubApplicationPeer::APP_UID, $sApplicationUID);
  	    $oCriteria2->add(SubApplicationPeer::SA_STATUS, 'ACTIVE');
  	    if (SubApplicationPeer::doCount($oCriteria2) > 0) {
  	      G::LoadClass('derivation');
  	      $oDerivation = new Derivation();
  	      $oDerivation->verifyIsCaseChild($sApplicationUID);
  	    }
    }

    function reactivateCase($sApplicationUID, $iIndex, $user_logged)
    {
        require_once 'classes/model/Application.php';
        require_once 'classes/model/AppDelay.php';
        $oApplication = new Application();
        $aFields = $oApplication->load((isset($_POST['sApplicationUID']) ? $_POST['sApplicationUID'] : $_SESSION['APPLICATION']));
        $aFields['APP_STATUS'] = 'TO_DO';
        $oApplication->update($aFields);
        $this->ReactivateCurrentDelegation($sApplicationUID, $iIndex);
        $c = new Criteria('workflow');
        $c->clearSelectColumns();
        $c->addSelectColumn(AppDelayPeer::APP_DELAY_UID);

        $c->add(AppDelayPeer::APP_UID, $sApplicationUID);
        $c->add(AppDelayPeer::PRO_UID, $aFields['PRO_UID']);
        $c->add(AppDelayPeer::APP_DEL_INDEX, $iIndex);
        $c->add(AppDelayPeer::APP_TYPE, 'CANCEL');
        $c->add(AppDelayPeer::APP_DISABLE_ACTION_USER, null);
        $c->add(AppDelayPeer::APP_DISABLE_ACTION_DATE, null);

        $oDataset = AppDelayPeer::doSelectRS($c);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRow = $oDataset->getRow();
        //var_dump($aRow);
        $aFields = array();
        $aFields['APP_DELAY_UID'] = $aRow['APP_DELAY_UID'];
        $aFields['APP_DISABLE_ACTION_USER'] = $user_logged;
        $aFields['APP_DISABLE_ACTION_DATE'] = date('Y-m-d H:i:s');
        ;

        $delay = new AppDelay();
        $delay->update($aFields);
        //$this->ReactivateCurrentDelegation($sApplicationUID);
    }

    function reassignCase($sApplicationUID, $iDelegation, $sUserUID, $newUserUID, $sType = 'REASSIGN')
    {
        $this->CloseCurrentDelegation($sApplicationUID, $iDelegation);
        $oAppDelegation = new AppDelegation();
        $aFieldsDel = $oAppDelegation->Load($sApplicationUID, $iDelegation);
        $iIndex = $oAppDelegation->createAppDelegation($aFieldsDel['PRO_UID'], $aFieldsDel['APP_UID'], $aFieldsDel['TAS_UID'], $aFieldsDel['USR_UID'], $aFieldsDel['DEL_THREAD']);
        $aData = array();
        $aData['APP_UID'] = $aFieldsDel['APP_UID'];
        $aData['DEL_INDEX'] = $iIndex;
        $aData['DEL_PREVIOUS'] = $aFieldsDel['DEL_PREVIOUS'];
        $aData['DEL_TYPE'] = $aFieldsDel['DEL_TYPE'];
        $aData['DEL_PRIORITY'] = $aFieldsDel['DEL_PRIORITY'];
        $aData['DEL_DELEGATE_DATE'] = $aFieldsDel['DEL_DELEGATE_DATE'];
        $aData['USR_UID'] = $newUserUID;
        $aData['DEL_INIT_DATE'] = null;
        $aData['DEL_FINISH_DATE'] = null;
        $oAppDelegation->update($aData);
        $oAppThread = new AppThread();
        $oAppThread->update(array('APP_UID' => $sApplicationUID, 'APP_THREAD_INDEX' => $aFieldsDel['DEL_THREAD'], 'DEL_INDEX' => $iIndex));
        //Save in APP_DELAY
        $oApplication = new Application();
        $aFields = $oApplication->Load($sApplicationUID);
        $aData['PRO_UID'] = $aFieldsDel['PRO_UID'];
        $aData['APP_UID'] = $sApplicationUID;
        $aData['APP_THREAD_INDEX'] = $aFieldsDel['DEL_THREAD'];
        $aData['APP_DEL_INDEX'] = $iDelegation;
        $aData['APP_TYPE'] = ($sType != '' ? $sType : 'REASSIGN');
        $aData['APP_STATUS'] = $aFields['APP_STATUS'];
        $aData['APP_DELEGATION_USER'] = $sUserUID;
        $aData['APP_ENABLE_ACTION_USER'] = $sUserUID;
        $aData['APP_ENABLE_ACTION_DATE'] = date('Y-m-d H:i:s');
        $oAppDelay = new AppDelay();
        $oAppDelay->create($aData);
    }

    function getAllStepsToRevise($APP_UID, $DEL_INDEX)
    {

        $oCriteria = new Criteria('workflow');

        $oCriteria->addSelectColumn(StepSupervisorPeer::STEP_UID);
        $oCriteria->addSelectColumn(StepSupervisorPeer::PRO_UID);
        $oCriteria->addSelectColumn(StepSupervisorPeer::STEP_TYPE_OBJ);
        $oCriteria->addSelectColumn(StepSupervisorPeer::STEP_UID_OBJ);
        $oCriteria->addSelectColumn(StepSupervisorPeer::STEP_POSITION);

        $oCriteria->add(AppDelegationPeer::APP_UID, $APP_UID);
        $oCriteria->add(AppDelegationPeer::DEL_INDEX, $DEL_INDEX);

        $oCriteria->addJoin(AppDelegationPeer::PRO_UID, StepSupervisorPeer::PRO_UID);
        $oCriteria->addAscendingOrderByColumn(StepSupervisorPeer::STEP_POSITION);

        $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        return $oDataset;
    }

    function getAllUploadedDocumentsCriteria($sProcessUID, $sApplicationUID, $sTasKUID, $sUserUID) {
    	//verifica si la tabla OBJECT_PERMISSION
    	$this->verifyTable();

      $aObjectPermissions = $this->getAllObjects($sProcessUID, $sApplicationUID, $sTasKUID, $sUserUID);
      if (!is_array($aObjectPermissions)) {
        $aObjectPermissions = array('DYNAFORMS' => array(-1), 'INPUT_DOCUMENTS' => array(-1), 'OUTPUT_DOCUMENTS' => array(-1));
      }
      if (!isset($aObjectPermissions['DYNAFORMS'])) {
        $aObjectPermissions['DYNAFORMS'] = array(-1);
      }
      else {
        if (!is_array($aObjectPermissions['DYNAFORMS'])) {
          $aObjectPermissions['DYNAFORMS'] = array(-1);
        }
      }
      if (!isset($aObjectPermissions['INPUT_DOCUMENTS'])) {
        $aObjectPermissions['INPUT_DOCUMENTS'] = array(-1);
      }
      else {
        if (!is_array($aObjectPermissions['INPUT_DOCUMENTS'])) {
          $aObjectPermissions['INPUT_DOCUMENTS'] = array(-1);
        }
      }
      if (!isset($aObjectPermissions['OUTPUT_DOCUMENTS'])) {
        $aObjectPermissions['OUTPUT_DOCUMENTS'] = array(-1);
      }
      else {
        if (!is_array($aObjectPermissions['OUTPUT_DOCUMENTS'])) {
          $aObjectPermissions['OUTPUT_DOCUMENTS'] = array(-1);
        }
      }
      $aDelete = $this->getAllObjectsFrom($sProcessUID, $sApplicationUID, $sTasKUID, $sUserUID, 'DELETE');
      require_once 'classes/model/AppDocument.php';
      $oAppDocument = new AppDocument();
      $oCriteria = new Criteria('workflow');
      $oCriteria->add(AppDocumentPeer::APP_UID, $sApplicationUID);
      $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, array('INPUT'), Criteria::IN);
      $oCriteria->add(AppDocumentPeer::APP_DOC_UID, $aObjectPermissions['INPUT_DOCUMENTS'], Criteria::IN);
      $aConditions   = array();
      $aConditions[] = array(AppDocumentPeer::APP_UID, AppDelegationPeer::APP_UID);
      $aConditions[] = array(AppDocumentPeer::DEL_INDEX, AppDelegationPeer::DEL_INDEX);
      $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
      $oCriteria->add(AppDelegationPeer::PRO_UID, $sProcessUID);
      $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
      $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $aInputDocuments = array();
      $aInputDocuments[] = array('APP_DOC_UID' => 'char', 'DOC_UID' => 'char', 'APP_DOC_COMMENT' => 'char', 'APP_DOC_FILENAME' => 'char', 'APP_DOC_INDEX' => 'integer');
      while ($aRow = $oDataset->getRow()) {
          $oCriteria2 = new Criteria('workflow');
          $oCriteria2->add(AppDelegationPeer::APP_UID, $sApplicationUID);
          $oCriteria2->add(AppDelegationPeer::DEL_INDEX, $aRow['DEL_INDEX']);
          $oDataset2 = AppDelegationPeer::doSelectRS($oCriteria2);
          $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
          $oDataset2->next();
          $aRow2 = $oDataset2->getRow();
          $oTask = new Task();
          if ($oTask->taskExists($aRow2['TAS_UID'])) {
            $aTask = $oTask->load($aRow2['TAS_UID']);
          }
          else {
            $aTask = array('TAS_TITLE' => '(TASK DELETED)');
          }
          $aAux = $oAppDocument->load($aRow['APP_DOC_UID']);
          $aFields = array('APP_DOC_UID' => $aAux['APP_DOC_UID'], 'DOC_UID' => $aAux['DOC_UID'], 'APP_DOC_COMMENT' => $aAux['APP_DOC_COMMENT'], 'APP_DOC_FILENAME' => $aAux['APP_DOC_FILENAME'], 'APP_DOC_INDEX' => $aAux['APP_DOC_INDEX'], 'TYPE' => $aAux['APP_DOC_TYPE'], 'ORIGIN' => $aTask['TAS_TITLE']);
          if ($aFields['APP_DOC_FILENAME'] != '') {
              $aFields['TITLE'] = $aFields['APP_DOC_FILENAME'];
          } else {
              $aFields['TITLE'] = $aFields['APP_DOC_COMMENT'];
          }
          $aFields['POSITION'] = $_SESSION['STEP_POSITION'];
          $aFields['CONFIRM'] = G::LoadTranslation('ID_CONFIRM_DELETE_ELEMENT');
          if (in_array($aRow['APP_DOC_UID'], $aDelete['INPUT_DOCUMENTS'])) {
            $aFields['ID_DELETE'] = G::LoadTranslation('ID_DELETE');
          }
          $aInputDocuments[] = $aFields;
          $oDataset->next();
      }
      $oAppDocument = new AppDocument();
      $oCriteria = new Criteria('workflow');
      $oCriteria->add(AppDocumentPeer::APP_UID, $sApplicationUID);
      $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, array('ATTACHED'), Criteria::IN);
      $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
      $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
          $oCriteria2 = new Criteria('workflow');
          $oCriteria2->add(AppDelegationPeer::DEL_INDEX, $aRow['DEL_INDEX']);
          $oDataset2 = AppDelegationPeer::doSelectRS($oCriteria2);
          $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
          $oDataset2->next();
          $aRow2 = $oDataset2->getRow();
          $oTask = new Task();
          if ($oTask->taskExists($aRow2['TAS_UID'])) {
            $aTask = $oTask->load($aRow2['TAS_UID']);
          }
          else {
            $aTask = array('TAS_TITLE' => '(TASK DELETED)');
          }
          $aAux = $oAppDocument->load($aRow['APP_DOC_UID']);
          $aFields = array('APP_DOC_UID' => $aAux['APP_DOC_UID'], 'DOC_UID' => $aAux['DOC_UID'], 'APP_DOC_COMMENT' => $aAux['APP_DOC_COMMENT'], 'APP_DOC_FILENAME' => $aAux['APP_DOC_FILENAME'], 'APP_DOC_INDEX' => $aAux['APP_DOC_INDEX'], 'TYPE' => $aAux['APP_DOC_TYPE'], 'ORIGIN' => $aTask['TAS_TITLE']);
          if ($aFields['APP_DOC_FILENAME'] != '') {
              $aFields['TITLE'] = $aFields['APP_DOC_FILENAME'];
          } else {
              $aFields['TITLE'] = $aFields['APP_DOC_COMMENT'];
          }
          $aFields['POSITION'] = $_SESSION['STEP_POSITION'];
          $aFields['CONFIRM'] = G::LoadTranslation('ID_CONFIRM_DELETE_ELEMENT');
          $aFields['ID_DELETE'] = G::LoadTranslation('ID_DELETE');
          $aInputDocuments[] = $aFields;
          $oDataset->next();
      }
      global $_DBArray;
      $_DBArray['inputDocuments'] = $aInputDocuments;
      $_SESSION['_DBArray'] = $_DBArray;
      G::LoadClass('ArrayPeer');
      $oCriteria = new Criteria('dbarray');
      $oCriteria->setDBArrayTable('inputDocuments');
      $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_CREATE_DATE);
      return $oCriteria;
    }

    function getAllGeneratedDocumentsCriteria($sProcessUID, $sApplicationUID, $sTasKUID, $sUserUID) {
    	//verifica si la tabla OBJECT_PERMISSION
    	$this->verifyTable();

      $aObjectPermissions = $this->getAllObjects($sProcessUID, $sApplicationUID, $sTasKUID, $sUserUID);
      if (!is_array($aObjectPermissions)) {
        $aObjectPermissions = array('DYNAFORMS' => array(-1), 'INPUT_DOCUMENTS' => array(-1), 'OUTPUT_DOCUMENTS' => array(-1));
      }
      if (!isset($aObjectPermissions['DYNAFORMS'])) {
        $aObjectPermissions['DYNAFORMS'] = array(-1);
      }
      else {
        if (!is_array($aObjectPermissions['DYNAFORMS'])) {
          $aObjectPermissions['DYNAFORMS'] = array(-1);
        }
      }
      if (!isset($aObjectPermissions['INPUT_DOCUMENTS'])) {
        $aObjectPermissions['INPUT_DOCUMENTS'] = array(-1);
      }
      else {
        if (!is_array($aObjectPermissions['INPUT_DOCUMENTS'])) {
          $aObjectPermissions['INPUT_DOCUMENTS'] = array(-1);
        }
      }
      if (!isset($aObjectPermissions['OUTPUT_DOCUMENTS'])) {
        $aObjectPermissions['OUTPUT_DOCUMENTS'] = array(-1);
      }
      else {
        if (!is_array($aObjectPermissions['OUTPUT_DOCUMENTS'])) {
          $aObjectPermissions['OUTPUT_DOCUMENTS'] = array(-1);
        }
      }
      $aDelete = $this->getAllObjectsFrom($sProcessUID, $sApplicationUID, $sTasKUID, $sUserUID, 'DELETE');
      require_once 'classes/model/AppDocument.php';
      $oAppDocument = new AppDocument();
      $oCriteria = new Criteria('workflow');
      $oCriteria->add(AppDocumentPeer::APP_UID, $sApplicationUID);
      $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, 'OUTPUT');
      $oCriteria->add(AppDocumentPeer::APP_DOC_UID, $aObjectPermissions['OUTPUT_DOCUMENTS'], Criteria::IN);
      $aConditions   = array();
      $aConditions[] = array(AppDocumentPeer::APP_UID, AppDelegationPeer::APP_UID);
      $aConditions[] = array(AppDocumentPeer::DEL_INDEX, AppDelegationPeer::DEL_INDEX);
      $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
      $oCriteria->add(AppDelegationPeer::PRO_UID, $sProcessUID);
      $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
      $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $aOutputDocuments = array();
      $aOutputDocuments[] = array('APP_DOC_UID' => 'char', 'DOC_UID' => 'char', 'APP_DOC_COMMENT' => 'char', 'APP_DOC_FILENAME' => 'char', 'APP_DOC_INDEX' => 'integer');
      while ($aRow = $oDataset->getRow()) {
          $oCriteria2 = new Criteria('workflow');
          $oCriteria2->add(AppDelegationPeer::APP_UID, $sApplicationUID);
          $oCriteria2->add(AppDelegationPeer::DEL_INDEX, $aRow['DEL_INDEX']);
          $oDataset2 = AppDelegationPeer::doSelectRS($oCriteria2);
          $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
          $oDataset2->next();
          $aRow2 = $oDataset2->getRow();
          $oTask = new Task();
          if ($oTask->taskExists($aRow2['TAS_UID'])) {
            $aTask = $oTask->load($aRow2['TAS_UID']);
          }
          else {
            $aTask = array('TAS_TITLE' => '(TASK DELETED)');
          }
          $aAux = $oAppDocument->load($aRow['APP_DOC_UID']);
          $aFields = array('APP_DOC_UID' => $aAux['APP_DOC_UID'], 'DOC_UID' => $aAux['DOC_UID'], 'APP_DOC_COMMENT' => $aAux['APP_DOC_COMMENT'], 'APP_DOC_FILENAME' => $aAux['APP_DOC_FILENAME'], 'APP_DOC_INDEX' => $aAux['APP_DOC_INDEX'], 'ORIGIN' => $aTask['TAS_TITLE']);
          if ($aFields['APP_DOC_FILENAME'] != '') {
              $aFields['TITLE'] = $aFields['APP_DOC_FILENAME'];
          } else {
              $aFields['TITLE'] = $aFields['APP_DOC_COMMENT'];
          }
          $aFields['POSITION'] = $_SESSION['STEP_POSITION'];
          $aFields['CONFIRM'] = G::LoadTranslation('ID_CONFIRM_DELETE_ELEMENT');
          if (in_array($aRow['APP_DOC_UID'], $aDelete['OUTPUT_DOCUMENTS'])) {
            $aFields['ID_DELETE'] = G::LoadTranslation('ID_DELETE');
          }
          $aOutputDocuments[] = $aFields;
          $oDataset->next();
      }
      global $_DBArray;
      $_DBArray['outputDocuments'] = $aOutputDocuments;
      $_SESSION['_DBArray'] = $_DBArray;
      G::LoadClass('ArrayPeer');
      $oCriteria = new Criteria('dbarray');
      $oCriteria->setDBArrayTable('outputDocuments');
      $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_CREATE_DATE);
      return $oCriteria;
    }

    function getallDynaformsCriteria($sProcessUID, $sApplicationUID, $sTasKUID, $sUserUID)
    {
    	//verifica si la tabla OBJECT_PERMISSION
    	$this->verifyTable();

      $aObjectPermissions = $this->getAllObjects($sProcessUID, $sApplicationUID, $sTasKUID, $sUserUID);
      if (!is_array($aObjectPermissions)) {
        $aObjectPermissions = array('DYNAFORMS' => array(-1), 'INPUT_DOCUMENTS' => array(-1), 'OUTPUT_DOCUMENTS' => array(-1));
      }
      if (!isset($aObjectPermissions['DYNAFORMS'])) {
        $aObjectPermissions['DYNAFORMS'] = array(-1);
      }
      else {
        if (!is_array($aObjectPermissions['DYNAFORMS'])) {
          $aObjectPermissions['DYNAFORMS'] = array(-1);
        }
      }
      if (!isset($aObjectPermissions['INPUT_DOCUMENTS'])) {
        $aObjectPermissions['INPUT_DOCUMENTS'] = array(-1);
      }
      else {
        if (!is_array($aObjectPermissions['INPUT_DOCUMENTS'])) {
          $aObjectPermissions['INPUT_DOCUMENTS'] = array(-1);
        }
      }
      if (!isset($aObjectPermissions['OUTPUT_DOCUMENTS'])) {
        $aObjectPermissions['OUTPUT_DOCUMENTS'] = array(-1);
      }
      else {
        if (!is_array($aObjectPermissions['OUTPUT_DOCUMENTS'])) {
          $aObjectPermissions['OUTPUT_DOCUMENTS'] = array(-1);
        }
      }
      $oCriteria = new Criteria('workflow');
      $oCriteria->add(ApplicationPeer::APP_UID, $sApplicationUID);
      $oCriteria->addJoin(ApplicationPeer::PRO_UID, StepPeer::PRO_UID);
      $oCriteria->addJoin(StepPeer::STEP_UID_OBJ, DynaformPeer::DYN_UID);
      $oCriteria->add(StepPeer::STEP_TYPE_OBJ, 'DYNAFORM');
      $oCriteria->add(StepPeer::STEP_UID_OBJ, $aObjectPermissions['DYNAFORMS'], Criteria::IN);
      $oCriteria->addAscendingOrderByColumn(StepPeer::STEP_POSITION);
      $oCriteria->setDistinct();
      $oDataset = DynaformPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $aInputDocuments = array();
      $aInputDocuments[] = array(
        'DYN_TITLE' => 'char'
      );

      while ($aRow = $oDataset->getRow()) {
          $o = new Dynaform();
          $o->setDynUid($aRow['DYN_UID']);
          $aFields['DYN_TITLE'] = $o->getDynTitle();
          $aFields['DYN_UID'] = $aRow['DYN_UID'];
          $aFields['EDIT'] = G::LoadTranslation('ID_EDIT');
          $aInputDocuments[] = $aFields;
          $oDataset->next();
      }
      global $_DBArray;
      $_DBArray['Dynaforms'] = $aInputDocuments;
      $_SESSION['_DBArray'] = $_DBArray;
      G::LoadClass('ArrayPeer');
      $oCriteria = new Criteria('dbarray');
      $oCriteria->setDBArrayTable('Dynaforms');
      //$oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_CREATE_DATE);
      return $oCriteria;
    }

    function sendNotifications($sCurrentTask, $aTasks, $aFields, $sApplicationUID, $iDelegation, $sFrom = '') {
      try {
        require_once 'classes/model/Configuration.php';
        $oConfiguration = new Configuration();
        $sDelimiter     = DBAdapter::getStringDelimiter();
        $oCriteria      = new Criteria('workflow');
        $oCriteria->add(ConfigurationPeer::CFG_UID, 'Emails');
        $oCriteria->add(ConfigurationPeer::OBJ_UID, '');
        $oCriteria->add(ConfigurationPeer::PRO_UID, '');
        $oCriteria->add(ConfigurationPeer::USR_UID, '');
        $oCriteria->add(ConfigurationPeer::APP_UID, '');
        if (ConfigurationPeer::doCount($oCriteria) == 0) {
          $oConfiguration->create(array('CFG_UID' => 'Emails', 'OBJ_UID' => '', 'CFG_VALUE' => '', 'PRO_UID' => '', 'USR_UID' => '', 'APP_UID' => ''));
          $aConfiguration = array();
        }
        else {
          $aConfiguration = $oConfiguration->load('Emails', '', '', '', '');
          if ($aConfiguration['CFG_VALUE'] != '') {
            $aConfiguration = unserialize($aConfiguration['CFG_VALUE']);
          }
          else {
            $aConfiguration = array();
          }
        }
        if ($aConfiguration['MESS_ENABLED'] == '1') {
          //Send derivation notification - Start
          $oTask     = new Task();
          $aTaskInfo = $oTask->load($sCurrentTask);
          if ($aTaskInfo['TAS_SEND_LAST_EMAIL'] == 'TRUE') {
            if ($sFrom == '') {
              $sFrom = '"ProcessMaker"';
            }
            if (($aConfiguration['MESS_ENGINE'] != 'MAIL') && ($aConfiguration['MESS_ACCOUNT'] != '')) {
              $sFrom .= ' <' . $aConfiguration['MESS_ACCOUNT'] . '>';
            }
            else {
              if (($aConfiguration['MESS_ENGINE'] == 'MAIL')) {
                $sFrom .= ' <info@' . gethostbyaddr('127.0.0.1') . '>';
              }
              else {
                if ($aConfiguration['MESS_SERVER'] != '') {
                  if (($sAux = @gethostbyaddr($aConfiguration['MESS_SERVER']))) {
                    $sFrom .= ' <info@' . $sAux . '>';
                  }
                  else {
                    $sFrom .= ' <info@' . $aConfiguration['MESS_SERVER'] . '>';
                  }
                }
                else {
                  $sFrom .= ' <info@processmaker.com>';
                }
              }
            }
            $sSubject = G::LoadTranslation('ID_MESSAGE_SUBJECT_DERIVATION');
            $sBody    = G::replaceDataField($aTaskInfo['TAS_DEF_MESSAGE'], $aFields);
            G::LoadClass('spool');
            $oUser = new Users();
            foreach ($aTasks as $aTask) {
              if (isset($aTask['USR_UID'])) {
                $aUser = $oUser->load($aTask['USR_UID']);
                $sTo   = ((($aUser['USR_FIRSTNAME'] != '') || ($aUser['USR_LASTNAME'] != '')) ? $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'] . ' ' : '') . '<' . $aUser['USR_EMAIL'] . '>';
                $oSpool = new spoolRun();
                $oSpool->setConfig(array('MESS_ENGINE'   => $aConfiguration['MESS_ENGINE'],
                                         'MESS_SERVER'   => $aConfiguration['MESS_SERVER'],
                                         'MESS_PORT'     => $aConfiguration['MESS_PORT'],
                                         'MESS_ACCOUNT'  => $aConfiguration['MESS_ACCOUNT'],
                                         'MESS_PASSWORD' => $aConfiguration['MESS_PASSWORD'],
                                         'SMTPAuth'      => $aConfiguration['MESS_RAUTH'] == '1' ? true : false));
                $oSpool->create(array('msg_uid'          => '',
                                      'app_uid'          => $sApplicationUID,
                                      'del_index'        => $iDelegation,
                                      'app_msg_type'     => 'DERIVATION',
                                      'app_msg_subject'  => $sSubject,
                                      'app_msg_from'     => $sFrom,
                                      'app_msg_to'       => $sTo,
                                      'app_msg_body'     => $sBody,
                                      'app_msg_cc'       => '',
                                      'app_msg_bcc'      => '',
                                      'app_msg_attach'   => '',
                                      'app_msg_template' => '',
                                      'app_msg_status'   => 'pending'));
                if (($aConfiguration['MESS_BACKGROUND'] == '') || ($aConfiguration['MESS_TRY_SEND_INMEDIATLY'] == '1')) {
                  $oSpool->sendMail();
                }
              }
            }
          }
          //Send derivation notification - End
        }
      }
      catch (Exception $oException) {
        throw $oException;
      }
    }

	/**
	* Obtain all user permits for Dynaforms, Input and output documents
	*
	* @function getAllObjectsFrom ($PRO_UID, $APP_UID, $TAS_UID, $USR_UID)
	* @author Erik Amaru Ortiz <erik@colosa.com>
	* @access public
	* @param  Process ID, Application ID, Task ID and User ID
	* @return Array within all user permitions all objects' types
	*/

	function getAllObjects($PRO_UID, $APP_UID, $TAS_UID = '', $USR_UID)
	{
		$ACTIONS = Array('VIEW', 'BLOCK'); //TO COMPLETE
		$MAIN_OBJECTS = Array();
		$RESULT_OBJECTS = Array();

		foreach($ACTIONS as $action) {
			$MAIN_OBJECTS[$action] = $this->getAllObjectsFrom($PRO_UID, $APP_UID, $TAS_UID, $USR_UID, $action);
		}
		/* ADDITIONAL OPERATIONS*/
		/*** BETWEN VIEW AND BLOCK***/
		$RESULT_OBJECTS['DYNAFORMS']		= G::arrayDiff($MAIN_OBJECTS['VIEW']['DYNAFORMS'], $MAIN_OBJECTS['BLOCK']['DYNAFORMS']);
		$RESULT_OBJECTS['INPUT_DOCUMENTS']	= G::arrayDiff($MAIN_OBJECTS['VIEW']['INPUT_DOCUMENTS'], $MAIN_OBJECTS['BLOCK']['INPUT_DOCUMENTS']);
		$RESULT_OBJECTS['OUTPUT_DOCUMENTS']	= G::arrayDiff($MAIN_OBJECTS['VIEW']['OUTPUT_DOCUMENTS'], $MAIN_OBJECTS['BLOCK']['OUTPUT_DOCUMENTS']);
		array_push($RESULT_OBJECTS['DYNAFORMS'], -1);
		array_push($RESULT_OBJECTS['INPUT_DOCUMENTS'], -1);
		array_push($RESULT_OBJECTS['OUTPUT_DOCUMENTS'], -1);

		return $RESULT_OBJECTS;
	}

	/**
	* Obtain all user permits for Dynaforms, Input and output documents from some action [VIEW, BLOCK, etc...]
	*
	* @function getAllObjectsFrom ($PRO_UID, $APP_UID, $TAS_UID, $USR_UID, $ACTION)
	* @author Erik Amaru Ortiz <erik@colosa.com>
	* @access public
	* @param  Process ID, Application ID, Task ID, User ID, Action
	* @return Array within all user permitions all objects' types
	*/

	function getAllObjectsFrom($PRO_UID, $APP_UID, $TAS_UID = '', $USR_UID, $ACTION='')
	{
		$USER_PERMISSIONS = Array();
		$GROUP_PERMISSIONS = Array();
		$RESULT = Array("DYNAFORM"=>Array(), "INPUT"=>Array(), "OUTPUT"=>Array());

		//permissions per user
		$oCriteria = new Criteria('workflow');
		$oCriteria->add(ObjectPermissionPeer::USR_UID, $USR_UID);
		$oCriteria->add(ObjectPermissionPeer::PRO_UID, $PRO_UID);
		$oCriteria->add(ObjectPermissionPeer::OP_ACTION, $ACTION);
		$oCriteria->add( $oCriteria->getNewCriterion(ObjectPermissionPeer::TAS_UID, $TAS_UID)->addOr($oCriteria->getNewCriterion(ObjectPermissionPeer::TAS_UID, '')) );
		$rs = ObjectPermissionPeer::doSelectRS($oCriteria);
		$rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);

		while ($rs->next()) {
			array_push($USER_PERMISSIONS, $rs->getRow());
		}

		//permissions per group
		G::loadClass('groups');
		$gr = new Groups();
		$records = $gr->getActiveGroupsForAnUser($USR_UID);
		foreach($records as $group) {
			$oCriteria = new Criteria('workflow');
			$oCriteria->add(ObjectPermissionPeer::USR_UID, $group);
			$oCriteria->add(ObjectPermissionPeer::PRO_UID, $PRO_UID);
			$oCriteria->add(ObjectPermissionPeer::OP_ACTION, $ACTION);
			$oCriteria->add( $oCriteria->getNewCriterion(ObjectPermissionPeer::TAS_UID, $TAS_UID)->addOr($oCriteria->getNewCriterion(ObjectPermissionPeer::TAS_UID, '')) );
			$rs = ObjectPermissionPeer::doSelectRS($oCriteria);
			$rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
			while ($rs->next()) {
				array_push($GROUP_PERMISSIONS, $rs->getRow());
			}
		}

		$PERMISSIONS = array_merge($USER_PERMISSIONS, $GROUP_PERMISSIONS);

		foreach ($PERMISSIONS as $row) {

			$USER 			= $row['USR_UID'];
			$USER_RELATION 	= $row['OP_USER_RELATION'];
			$TASK_SOURCE	= $row['OP_TASK_SOURCE'];
			$PARTICIPATE	= $row['OP_PARTICIPATE'];
			$O_TYPE			= $row['OP_OBJ_TYPE'];
			$O_UID			= $row['OP_OBJ_UID'];
			$ACTION			= $row['OP_ACTION'];

			// here!,. we should verify $PARTICIPATE
			$sw_participate = false; // must be false for default
			if($PARTICIPATE == 1){
				$oCriteriax = new Criteria('workflow');
				$oCriteriax->add(AppDelegationPeer::USR_UID, $USR_UID);
				$oCriteriax->add(AppDelegationPeer::APP_UID, $APP_UID);

				if( AppDelegationPeer::doCount($oCriteriax) == 0 ) {
					$sw_participate = true;
				}
			}

			if( !$sw_participate ) {

				switch( $O_TYPE ) {
					case 'ANY':
						//for dynaforms
						$oCriteria = new Criteria('workflow');
						$oCriteria->add(ApplicationPeer::APP_UID, $APP_UID);
						$oCriteria->addJoin(ApplicationPeer::PRO_UID, StepPeer::PRO_UID);
						$oCriteria->addJoin(StepPeer::STEP_UID_OBJ, DynaformPeer::DYN_UID);
						if($TASK_SOURCE != '') {
							$oCriteria->add(StepPeer::TAS_UID, $TASK_SOURCE);
						}
						$oCriteria->add(StepPeer::STEP_TYPE_OBJ, 'DYNAFORM');
						$oCriteria->addAscendingOrderByColumn(StepPeer::STEP_POSITION);
						$oCriteria->setDistinct();

						$oDataset = DynaformPeer::doSelectRS($oCriteria);
						$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
						$oDataset->next();

						while ($aRow = $oDataset->getRow()) {
							if( !in_array($aRow['DYN_UID'], $RESULT['DYNAFORM']) ) {
								array_push($RESULT['DYNAFORM'], $aRow['DYN_UID']);
							}
							$oDataset->next();
						}

						//inputs
						$oCriteria = new Criteria('workflow');
						$oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_UID);
						$oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_TYPE);
						$oCriteria->add(AppDelegationPeer::APP_UID, $APP_UID);
						$oCriteria->add(AppDelegationPeer::PRO_UID, $PRO_UID);
						if($TASK_SOURCE != '') {
							$oCriteria->add(AppDelegationPeer::TAS_UID, $TASK_SOURCE);
						}
						$oCriteria->add( $oCriteria->getNewCriterion(AppDocumentPeer::APP_DOC_TYPE, 'INPUT')->addOr($oCriteria->getNewCriterion(AppDocumentPeer::APP_DOC_TYPE, 'OUTPUT')) );
						$aConditions = Array();
						$aConditions[] = array(AppDelegationPeer::APP_UID, AppDocumentPeer::APP_UID);
						$aConditions[] = array(AppDelegationPeer::DEL_INDEX, AppDocumentPeer::DEL_INDEX);
						$oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);

						$oDataset = DynaformPeer::doSelectRS($oCriteria);
						$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
						$oDataset->next();
						while ($aRow = $oDataset->getRow()) {
							if( !in_array($aRow['APP_DOC_UID'], $RESULT[$aRow['APP_DOC_TYPE']]) ) {
								array_push($RESULT[$aRow['APP_DOC_TYPE']], $aRow['APP_DOC_UID']);
							}
							$oDataset->next();
						}

						break;

					case 'DYNAFORM':
						$oCriteria = new Criteria('workflow');
						$oCriteria->add(ApplicationPeer::APP_UID, $APP_UID);
						if($TASK_SOURCE != '') {
							$oCriteria->add(StepPeer::TAS_UID, $TASK_SOURCE);
						}
						if($O_UID != '') {
							$oCriteria->add(ApplicationPeer::DYN_UID, $O_UID);
						}
						$oCriteria->addJoin(ApplicationPeer::PRO_UID, StepPeer::PRO_UID);
						$oCriteria->addJoin(StepPeer::STEP_UID_OBJ, DynaformPeer::DYN_UID);
						$oCriteria->add(StepPeer::STEP_TYPE_OBJ, 'DYNAFORM');
						$oCriteria->addAscendingOrderByColumn(StepPeer::STEP_POSITION);
						$oCriteria->setDistinct();

						$oDataset = DynaformPeer::doSelectRS($oCriteria);
						$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
						$oDataset->next();

						while ($aRow = $oDataset->getRow()) {
							if( !in_array($aRow['DYN_UID'], $RESULT['DYNAFORM']) ) {
								array_push($RESULT['DYNAFORM'], $aRow['DYN_UID']);
							}
							$oDataset->next();
						}

					break;

					case 'INPUT' :
					case 'OUTPUT':

						if($row['OP_OBJ_TYPE'] == 'INPUT') {
							$obj_type = 'INPUT';
						} else {
							$obj_type = 'OUTPUT';
						}
						$oCriteria = new Criteria('workflow');
						$oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_UID);
						$oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_TYPE);
						$oCriteria->add(AppDelegationPeer::APP_UID, $APP_UID);
						$oCriteria->add(AppDelegationPeer::PRO_UID, $PRO_UID);
						if($TASK_SOURCE != '') {
							$oCriteria->add(AppDelegationPeer::TAS_UID, $TASK_SOURCE);
						}
						if($O_UID != '') {
							$oCriteria->add(AppDocumentPeer::DOC_UID, $O_UID);
						}
						$oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, $obj_type);

						$aConditions = Array();
						$aConditions[] = array(AppDelegationPeer::APP_UID, AppDocumentPeer::APP_UID);
						$aConditions[] = array(AppDelegationPeer::DEL_INDEX, AppDocumentPeer::DEL_INDEX);
						$oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);

						$oDataset = DynaformPeer::doSelectRS($oCriteria);
						$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
						$oDataset->next();
						while ($aRow = $oDataset->getRow()) {
							if( !in_array($aRow['APP_DOC_UID'], $RESULT[$obj_type]) ) {
								array_push($RESULT[$obj_type], $aRow['APP_DOC_UID']);
							}
							$oDataset->next();
						}
					break;
				}
			}
		}

		return Array("DYNAFORMS"=>$RESULT['DYNAFORM'], "INPUT_DOCUMENTS"=>$RESULT['INPUT'], "OUTPUT_DOCUMENTS"=>$RESULT['OUTPUT']);
	}

/*
funcion de verificacion para la autenticacion del External User by Everth The Answer
*/
 function verifyCaseTracker($case, $pin){

 	  $pin=md5($pin);

 	  $oCriteria = new Criteria('workflow');
		$oCriteria->addSelectColumn(ApplicationPeer::APP_UID);
		$oCriteria->addSelectColumn(ApplicationPeer::APP_PIN);
		$oCriteria->addSelectColumn(ApplicationPeer::PRO_UID);
		$oCriteria->addSelectColumn(ApplicationPeer::APP_NUMBER);
		$oCriteria->addSelectColumn(ApplicationPeer::APP_PROC_CODE);
		//$oCriteria->add(ApplicationPeer::APP_NUMBER, $case);
		$oCriteria->add(ApplicationPeer::APP_PROC_CODE, $case);

 	  $oDataset = DynaformPeer::doSelectRS($oCriteria);
		$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
		$oDataset->next();
		$aRow = $oDataset->getRow();

		$sw=0;
	if(is_array($aRow))
	{
	  $PRO_UID=$aRow['PRO_UID'];
	  $APP_UID=$aRow['APP_UID'];
	  $PIN    =$aRow['APP_PIN'];
	}
	else
	{
		$oCriteria = new Criteria('workflow');
		$oCriteria->addSelectColumn(ApplicationPeer::APP_UID);
		$oCriteria->addSelectColumn(ApplicationPeer::APP_PIN);
		$oCriteria->addSelectColumn(ApplicationPeer::PRO_UID);
		$oCriteria->addSelectColumn(ApplicationPeer::APP_NUMBER);
		$oCriteria->addSelectColumn(ApplicationPeer::APP_PROC_CODE);
		$oCriteria->add(ApplicationPeer::APP_NUMBER, $case);

 	  $oDataseti = DynaformPeer::doSelectRS($oCriteria);
		$oDataseti->setFetchmode(ResultSet::FETCHMODE_ASSOC);
		$oDataseti->next();
		$aRowi = $oDataseti->getRow();

		if(is_array($aRowi))
		{	$PRO_UID=$aRowi['PRO_UID'];
	  	$APP_UID=$aRowi['APP_UID'];
	  	$PIN    =$aRowi['APP_PIN'];
	  }
	  else
	  {
	  		$sw=1;
	  }
	}


 	  $s=0;
 	  if($sw==1) //no existe el caso
 	  	{
 	  		return -1;
 	    }
		else
			{
		  	 $s++;
		  }

 	  if($PIN!=$pin) //el pin no es valido
 	  		return -2;
 	  else
 	  		$s++;

 	  $res=array();
 	  $res['PRO_UID']=$PRO_UID;
 	  $res['APP_UID']=$APP_UID;

 	  if($s==2)
 	  	return $res;

	}

/*
funcion permisos, by Everth The Answer
*/
 function Permisos($PRO_UID){
 	  require_once ("classes/model/CaseTracker.php");
		require_once ("classes/model/CaseTrackerObject.php");
		$a=0;
		$b=0;
		$c=0;
		$d=0;
		$oCaseTracker = new CaseTracker();
		$aCaseTracker = $oCaseTracker->load($PRO_UID);
		//print_r($aCaseTracker); die;
		if(is_array($aCaseTracker))
		{	if($aCaseTracker['CT_MAP_TYPE']!='NONE')
			 	 $a=1;

			$oCriteria = new Criteria();
      $oCriteria->add(CaseTrackerObjectPeer::PRO_UID, $PRO_UID);
      if (CaseTrackerObjectPeer::doCount($oCriteria) > 0)
     	  	$b=1;

			if($aCaseTracker['CT_DERIVATION_HISTORY']==1)
				 	$c=1;

			if($aCaseTracker['CT_MESSAGE_HISTORY']==1)
				 	$d=1;

	  }
  return $a.'-'.$b.'-'.$c.'-'.$d;
}


/*
funcion momentanea by Everth The Answer
*/
 function verifyTable(){
 	  $oCriteria = new Criteria('workflow');
		$del = DBAdapter::getStringDelimiter();
    $sql = "CREATE TABLE IF NOT EXISTS `OBJECT_PERMISSION` (
 									 `OP_UID` varchar(32) NOT NULL,
 									 `PRO_UID` varchar(32) NOT NULL,
 									 `TAS_UID` varchar(32) NOT NULL,
 									 `USR_UID` varchar(32) NOT NULL,
 									 `OP_USER_RELATION` int(1) NOT NULL default '1',
 									 `OP_TASK_SOURCE` varchar(32) NOT NULL,
 									 `OP_PARTICIPATE` int(1) NOT NULL default '1',
 									 `OP_OBJ_TYPE` varchar(15) NOT NULL default 'ANY',
 									 `OP_OBJ_UID` varchar(32) NOT NULL,
 									 `OP_ACTION` varchar(10) NOT NULL default 'VIEW',
 									 KEY `PRO_UID` (`PRO_UID`,`TAS_UID`,`USR_UID`,`OP_TASK_SOURCE`,`OP_OBJ_UID`)
      						 )ENGINE=MyISAM DEFAULT CHARSET=latin1;";

		$con = Propel::getConnection("workflow");
		$stmt = $con->prepareStatement($sql);
		$rs = $stmt->executeQuery();
	}

/*
funcion input documents for case tracker by Everth The Answer
*/
function getAllUploadedDocumentsCriteriaTracker($sProcessUID, $sApplicationUID, $sDocUID) {

      require_once 'classes/model/AppDocument.php';
      $oAppDocument = new AppDocument();
      $oCriteria = new Criteria('workflow');
      $oCriteria->add(AppDocumentPeer::APP_UID, $sApplicationUID);
      //$oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, array('INPUT'), Criteria::IN);
      $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, 'INPUT');
      $oCriteria->add(AppDocumentPeer::DOC_UID, $sDocUID);
      $aConditions   = array();
      $aConditions[] = array(AppDocumentPeer::APP_UID, AppDelegationPeer::APP_UID);
      $aConditions[] = array(AppDocumentPeer::DEL_INDEX, AppDelegationPeer::DEL_INDEX);
      $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
      $oCriteria->add(AppDelegationPeer::PRO_UID, $sProcessUID);
      $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
      $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $aInputDocuments = array();
      $aInputDocuments[] = array('APP_DOC_UID' => 'char', 'DOC_UID' => 'char', 'APP_DOC_COMMENT' => 'char', 'APP_DOC_FILENAME' => 'char', 'APP_DOC_INDEX' => 'integer');
      while ($aRow = $oDataset->getRow()) {
          $oCriteria2 = new Criteria('workflow');
          $oCriteria2->add(AppDelegationPeer::APP_UID, $sApplicationUID);
          $oCriteria2->add(AppDelegationPeer::DEL_INDEX, $aRow['DEL_INDEX']);
          $oDataset2 = AppDelegationPeer::doSelectRS($oCriteria2);
          $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
          $oDataset2->next();
          $aRow2 = $oDataset2->getRow();
          $oTask = new Task();
          if ($oTask->taskExists($aRow2['TAS_UID'])) {
            $aTask = $oTask->load($aRow2['TAS_UID']);
          }
          else {
            $aTask = array('TAS_TITLE' => '(TASK DELETED)');
          }
          $aAux = $oAppDocument->load($aRow['APP_DOC_UID']);
          $aFields = array('APP_DOC_UID' => $aAux['APP_DOC_UID'], 'DOC_UID' => $aAux['DOC_UID'], 'APP_DOC_COMMENT' => $aAux['APP_DOC_COMMENT'], 'APP_DOC_FILENAME' => $aAux['APP_DOC_FILENAME'], 'APP_DOC_INDEX' => $aAux['APP_DOC_INDEX'], 'TYPE' => $aAux['APP_DOC_TYPE'], 'ORIGIN' => $aTask['TAS_TITLE']);
          if ($aFields['APP_DOC_FILENAME'] != '') {
              $aFields['TITLE'] = $aFields['APP_DOC_FILENAME'];
          } else {
              $aFields['TITLE'] = $aFields['APP_DOC_COMMENT'];
          }
          //$aFields['POSITION'] = $_SESSION['STEP_POSITION'];
          $aFields['CONFIRM'] = G::LoadTranslation('ID_CONFIRM_DELETE_ELEMENT');
          $aInputDocuments[] = $aFields;
          $oDataset->next();
      }
      $oAppDocument = new AppDocument();
      $oCriteria = new Criteria('workflow');
      $oCriteria->add(AppDocumentPeer::APP_UID, $sApplicationUID);
      //$oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, array('ATTACHED'), Criteria::IN);
      $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, 'ATTACHED');
      $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
      $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
          $oCriteria2 = new Criteria('workflow');
          $oCriteria2->add(AppDelegationPeer::DEL_INDEX, $aRow['DEL_INDEX']);
          $oDataset2 = AppDelegationPeer::doSelectRS($oCriteria2);
          $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
          $oDataset2->next();
          $aRow2 = $oDataset2->getRow();
          $oTask = new Task();
          if ($oTask->taskExists($aRow2['TAS_UID'])) {
            $aTask = $oTask->load($aRow2['TAS_UID']);
          }
          else {
            $aTask = array('TAS_TITLE' => '(TASK DELETED)');
          }
          $aAux = $oAppDocument->load($aRow['APP_DOC_UID']);
          $aFields = array('APP_DOC_UID' => $aAux['APP_DOC_UID'], 'DOC_UID' => $aAux['DOC_UID'], 'APP_DOC_COMMENT' => $aAux['APP_DOC_COMMENT'], 'APP_DOC_FILENAME' => $aAux['APP_DOC_FILENAME'], 'APP_DOC_INDEX' => $aAux['APP_DOC_INDEX'], 'TYPE' => $aAux['APP_DOC_TYPE'], 'ORIGIN' => $aTask['TAS_TITLE']);
          if ($aFields['APP_DOC_FILENAME'] != '') {
              $aFields['TITLE'] = $aFields['APP_DOC_FILENAME'];
          } else {
              $aFields['TITLE'] = $aFields['APP_DOC_COMMENT'];
          }
          //$aFields['POSITION'] = $_SESSION['STEP_POSITION'];
          $aFields['CONFIRM'] = G::LoadTranslation('ID_CONFIRM_DELETE_ELEMENT');
          $aInputDocuments[] = $aFields;
          $oDataset->next();
      }
      global $_DBArray;
      $_DBArray['inputDocuments'] = $aInputDocuments;
      $_SESSION['_DBArray'] = $_DBArray;
      G::LoadClass('ArrayPeer');
      $oCriteria = new Criteria('dbarray');
      $oCriteria->setDBArrayTable('inputDocuments');
      $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_CREATE_DATE);
      return $oCriteria;
    }

/*
funcion output documents for case tracker by Everth The Answer
*/
function getAllGeneratedDocumentsCriteriaTracker($sProcessUID, $sApplicationUID, $sDocUID) {

      require_once 'classes/model/AppDocument.php';
      $oAppDocument = new AppDocument();
      $oCriteria = new Criteria('workflow');
      $oCriteria->add(AppDocumentPeer::APP_UID, $sApplicationUID);
      $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, 'OUTPUT');
      $oCriteria->add(AppDocumentPeer::DOC_UID, $sDocUID);
      $aConditions   = array();
      $aConditions[] = array(AppDocumentPeer::APP_UID, AppDelegationPeer::APP_UID);
      $aConditions[] = array(AppDocumentPeer::DEL_INDEX, AppDelegationPeer::DEL_INDEX);
      $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
      $oCriteria->add(AppDelegationPeer::PRO_UID, $sProcessUID);
      $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
      $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $aOutputDocuments = array();
      $aOutputDocuments[] = array('APP_DOC_UID' => 'char', 'DOC_UID' => 'char', 'APP_DOC_COMMENT' => 'char', 'APP_DOC_FILENAME' => 'char', 'APP_DOC_INDEX' => 'integer');
      while ($aRow = $oDataset->getRow()) {
          $oCriteria2 = new Criteria('workflow');
          $oCriteria2->add(AppDelegationPeer::APP_UID, $sApplicationUID);
          $oCriteria2->add(AppDelegationPeer::DEL_INDEX, $aRow['DEL_INDEX']);
          $oDataset2 = AppDelegationPeer::doSelectRS($oCriteria2);
          $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
          $oDataset2->next();
          $aRow2 = $oDataset2->getRow();
          $oTask = new Task();
          if ($oTask->taskExists($aRow2['TAS_UID'])) {
            $aTask = $oTask->load($aRow2['TAS_UID']);
          }
          else {
            $aTask = array('TAS_TITLE' => '(TASK DELETED)');
          }
          $aAux = $oAppDocument->load($aRow['APP_DOC_UID']);
          $aFields = array('APP_DOC_UID' => $aAux['APP_DOC_UID'], 'DOC_UID' => $aAux['DOC_UID'], 'APP_DOC_COMMENT' => $aAux['APP_DOC_COMMENT'], 'APP_DOC_FILENAME' => $aAux['APP_DOC_FILENAME'], 'APP_DOC_INDEX' => $aAux['APP_DOC_INDEX'], 'ORIGIN' => $aTask['TAS_TITLE']);
          if ($aFields['APP_DOC_FILENAME'] != '') {
              $aFields['TITLE'] = $aFields['APP_DOC_FILENAME'];
          } else {
              $aFields['TITLE'] = $aFields['APP_DOC_COMMENT'];
          }
          //$aFields['POSITION'] = $_SESSION['STEP_POSITION'];
          $aFields['CONFIRM'] = G::LoadTranslation('ID_CONFIRM_DELETE_ELEMENT');
          $aOutputDocuments[] = $aFields;
          $oDataset->next();
      }
      global $_DBArray;
      $_DBArray['outputDocuments'] = $aOutputDocuments;
      $_SESSION['_DBArray'] = $_DBArray;
      G::LoadClass('ArrayPeer');
      $oCriteria = new Criteria('dbarray');
      $oCriteria->setDBArrayTable('outputDocuments');
      $oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_CREATE_DATE);
      return $oCriteria;
    }

/*
funcion History messages for case tracker by Everth The Answer
*/
  function getHistoryMessagesTracker($sApplicationUID) {
	 	//die ($sApplicationUID);
	  require_once 'classes/model/AppMessage.php';
    $oAppDocument = new AppDocument();
    $oCriteria = new Criteria('workflow');
    $oCriteria->add(AppMessagePeer::APP_UID, $sApplicationUID);
    $oCriteria->addAscendingOrderByColumn(AppMessagePeer::APP_MSG_DATE);
    $oDataset = AppMessagePeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();

    $aMessages = array();
    $aMessages[] = array('APP_MSG_UID' => 'char',
    								 'APP_UID' => 'char',
    								 'DEL_INDEX' => 'char',
    								 'APP_MSG_TYPE' => 'char',
    								 'APP_MSG_SUBJECT' => 'char',
    								 'APP_MSG_FROM' => 'char',
    								 'APP_MSG_TO' => 'char',
    								 'APP_MSG_BODY' => 'char',
    								 'APP_MSG_DATE' => 'char',
    								 'APP_MSG_CC' => 'char',
    								 'APP_MSG_BCC' => 'char',
    								 'APP_MSG_TEMPLATE' => 'char',
    								 'APP_MSG_STATUS' => 'char',
    								 'APP_MSG_ATTACH' => 'char'
    								 );
    while ($aRow = $oDataset->getRow()) {
    	 $aMessages[] = array('APP_MSG_UID' => $aRow['APP_MSG_UID'],
    								 'APP_UID' => $aRow['APP_UID'],
    								 'DEL_INDEX' => $aRow['DEL_INDEX'],
    								 'APP_MSG_TYPE' => $aRow['APP_MSG_TYPE'],
    								 'APP_MSG_SUBJECT' => $aRow['APP_MSG_SUBJECT'],
    								 'APP_MSG_FROM' => $aRow['APP_MSG_FROM'],
    								 'APP_MSG_TO' => $aRow['APP_MSG_TO'],
    								 'APP_MSG_BODY' => $aRow['APP_MSG_BODY'],
    								 'APP_MSG_DATE' => $aRow['APP_MSG_DATE'],
    								 'APP_MSG_CC' => $aRow['APP_MSG_CC'],
    								 'APP_MSG_BCC' => $aRow['APP_MSG_BCC'],
    								 'APP_MSG_TEMPLATE' => $aRow['APP_MSG_TEMPLATE'],
    								 'APP_MSG_STATUS' => $aRow['APP_MSG_STATUS'],
    								 'APP_MSG_ATTACH' => $aRow['APP_MSG_ATTACH']
    								 );
    	 $oDataset->next();
    }

    global $_DBArray;
    $_DBArray['messages']  = $aMessages;
    $_SESSION['_DBArray'] = $_DBArray;
    G::LoadClass('ArrayPeer');
    $oCriteria = new Criteria('dbarray');
    $oCriteria->setDBArrayTable('messages');

    return $oCriteria;
  }

  /*
funcion History messages for case tracker by Everth The Answer
*/
  function getHistoryMessagesTrackerView($sApplicationUID, $Msg_UID) {
	  require_once 'classes/model/AppMessage.php';
    $oAppDocument = new AppDocument();
    $oCriteria = new Criteria('workflow');
    $oCriteria->add(AppMessagePeer::APP_UID, $sApplicationUID);
    $oCriteria->add(AppMessagePeer::APP_MSG_UID, $Msg_UID);
    $oCriteria->addAscendingOrderByColumn(AppMessagePeer::APP_MSG_DATE);
    $oDataset = AppMessagePeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();

    $aRow = $oDataset->getRow();

    return $aRow;
  }


  function getAllObjectsFromProcess($PRO_UID, $OBJ_TYPE='%'){

            $RESULT = Array();
            $oCriteria = new Criteria('workflow');
            $oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_UID);
            $oCriteria->addSelectColumn(AppDocumentPeer::APP_UID);
            $oCriteria->addSelectColumn(AppDocumentPeer::DEL_INDEX);
            $oCriteria->addSelectColumn(AppDocumentPeer::DOC_UID);
            $oCriteria->addSelectColumn(AppDocumentPeer::USR_UID);
            $oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_TYPE);
            $oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_CREATE_DATE);
            $oCriteria->addSelectColumn(AppDocumentPeer::APP_DOC_INDEX);


            $oCriteria->add(ApplicationPeer::PRO_UID, $PRO_UID);
            $oCriteria->addJoin(ApplicationPeer::APP_UID, AppDocumentPeer::APP_UID);

            $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, $OBJ_TYPE, Criteria::LIKE);

            $oDataset = DynaformPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            ;
            while ($oDataset->next()) {
                 $row = $oDataset->getRow();
                 $oAppDocument = new AppDocument();
                 $oAppDocument->Fields = $oAppDocument->load($row['APP_DOC_UID']);

                 $row['APP_DOC_FILENAME'] = $oAppDocument->Fields['APP_DOC_FILENAME'];
                 array_push($RESULT, $row);
            }
            return $RESULT;
        }

  function executeTriggersAfterExternal($sProcess, $sTask, $sApplication, $iIndex, $iStepPosition, $aNewData = array()) {
    //load the variables
    $Fields = $this->loadCase($sApplication);
    $Fields['APP_DATA'] = array_merge($Fields['APP_DATA'], G::getSystemConstants());
    $Fields['APP_DATA'] = array_merge( $Fields['APP_DATA'], $aNewData);
    //execute triggers
    $oCase = new Cases();
    $aNextStep = $this->getNextStep($sProcess, $sApplication, $iIndex, $iStepPosition - 1);
    $Fields['APP_DATA'] = $this->ExecuteTriggers($sTask, 'EXTERNAL', $aNextStep['UID'], 'AFTER', $Fields['APP_DATA']);
    //save data
    $aData = array();
    $aData['APP_NUMBER']      = $Fields['APP_NUMBER'];
    $aData['APP_PROC_STATUS'] = $Fields['APP_PROC_STATUS'];
    $aData['APP_DATA']        = $Fields['APP_DATA'];
    $aData['DEL_INDEX']       = $iIndex;
    $aData['TAS_UID']         = $sTask;
    $this->updateCase($sApplication, $aData);
  }

}


