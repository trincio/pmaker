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
require_once ("classes/model/AppThread.php");
require_once ("classes/model/DbSource.php");
require_once ("classes/model/Dynaform.php");
require_once ("classes/model/InputDocument.php");
require_once ("classes/model/Language.php");
require_once ("classes/model/OutputDocument.php");
require_once ("classes/model/Process.php");
require_once ("classes/model/ProcessUser.php");
require_once ("classes/model/ReportTable.php");
require_once ("classes/model/ReportVar.php");
require_once ("classes/model/Step.php");
require_once ("classes/model/StepSupervisor.php");
require_once ("classes/model/StepTrigger.php");
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
    * @return boolean
    */
    function getStartCases($sUIDUser = '')
    {
        $rows[] = array('uid' => 'char', 'value' => 'char');
        $tasks = array();

        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn(TaskPeer::TAS_UID);
        $c->addSelectColumn(TaskPeer::PRO_UID);

        $c->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);
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

        $c->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);
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
            $rows[] = array('uid' => $val['TAS_UID'], 'value' => $title);
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
            $Fields['APP_PROC_CODE'] = self::refreshCaseStatusCode($sAppUid, $aApplicationFields);
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
                    $oDel = new AppDelegation;
                    $array['APP_UID'] = $sAppUid;
                    $array['DEL_INDEX'] = $DEL_INDEX;
                    $array['TAS_UID'] = $TAS_UID;
                    $array['DEL_PRIORITY'] = $x[$VAR_PRI];
                    $oDel->update($array);
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
    function getOpenSiblingThreads($sAppUid, $iDelIndex)
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
            $c->add(AppThreadPeer::APP_THREAD_STATUS, 'OPEN');
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
                $Fields['APP_PROC_CODE'] = self::refreshCaseStatusCode($sAppUid, G::array_merges(G::getSystemConstants(), unserialize($Fields['APP_DATA'])));
                $Application->update($Fields);

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

        return array('APPLICATION' => $sAppUid, 'INDEX' => $iDelIndex, 'PROCESS' => $sProUid);
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
        $c->addSelectColumn(AppDelegationPeer::DEL_INIT_DATE);
        $c->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE);

        $c->addSelectColumn(AppDelayPeer::APP_TYPE);
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
        $c->addSelectColumn(AppDelegationPeer::DEL_TASK_DUE_DATE);
        $c->addSelectColumn(AppDelegationPeer::DEL_INDEX);
        $c->addSelectColumn(AppDelegationPeer::DEL_INIT_DATE);
        $c->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE);
        $c->addSelectColumn(UsersPeer::USR_UID);
        $c->addSelectColumn(ApplicationPeer::APP_STATUS);
        $c->addAsColumn('APP_TITLE', 'APP_TITLE.CON_VALUE');
        $c->addAsColumn('APP_PRO_TITLE', 'PRO_TITLE.CON_VALUE');
        $c->addAsColumn('APP_TAS_TITLE', 'TAS_TITLE.CON_VALUE');
        $c->addAsColumn('APP_DEL_PREVIOUS_USER', 'APP_LAST_USER.USR_USERNAME');

        $c->addAlias("APP_TITLE", 'CONTENT');
        $c->addAlias("PRO_TITLE", 'CONTENT');
        $c->addAlias("TAS_TITLE", 'CONTENT');
        $c->addAlias("APP_PREV_DEL", 'APP_DELEGATION');
        $c->addAlias("APP_LAST_USER", 'USERS');

        $c->addJoin(ApplicationPeer::APP_UID, AppDelegationPeer::APP_UID, Criteria::LEFT_JOIN);
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
                $c->add(AppDelayPeer::APP_DISABLE_ACTION_USER, null, Criteria::ISNULL);
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

    function pauseCase($sApplicationUID, $iDelegation, $sUserUID)
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
        $oCriteria->add(AppDelayPeer::APP_DISABLE_ACTION_USER, null);
        $oCriteria->add(AppDelayPeer::APP_DISABLE_ACTION_DATE, null);
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

    function reassignCase($sApplicationUID, $iDelegation, $newUserUID)
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

}
