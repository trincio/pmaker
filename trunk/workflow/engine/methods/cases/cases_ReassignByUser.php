<?php
/**
 * cases_ReassignByUser.php
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

/**
 * Reassign ByUser routines
 * Author Erik Amaru Ortiz <erik@colosa.com> 
 */

try {
	global $RBAC;
	switch ($RBAC->userCanAccess('PM_REASSIGNCASE')) {
		case -2:
			G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels');
			G::header('location: ../login/login');
			die;
			break;
		case -1:
			G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
			G::header('location: ../login/login');
			die;
			break;
	}

	if (!isset($_GET['REASSIGN_USER'])) {
		$_GET['REASSIGN_USER'] = '';
	}
	$_GET['REASSIGN_BY']    = 2;
	$G_MAIN_MENU            = 'processmaker';
	$G_SUB_MENU             = 'cases';
	$G_ID_MENU_SELECTED     = 'CASES';
	$G_ID_SUB_MENU_SELECTED = 'CASES_TO_REASSIGN';
	$G_PUBLISH = new Publisher;
	$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_ReassignBy', '', $_GET);

	$sUserToReassign = trim($_GET['REASSIGN_USER']);
	
	if ($_GET['REASSIGN_USER'] != '') {

		G::LoadClass('tasks');
		G::LoadClass('groups');
		$oTasks  = new Tasks();
		$oGroups = new Groups();
		$oUser   = new Users();
		G::LoadClass('case');
		$oCases = new Cases();

		list($oCriteriaToDo,$sXMLFile)  = $oCases->getConditionCasesList('to_do', $sUserToReassign);
		list($oCriteriaDraft,$sXMLFile) = $oCases->getConditionCasesList('draft', $sUserToReassign);

		$aCasesList = Array();
		
		$oDataset = ApplicationPeer::doSelectRS($oCriteriaToDo);
		$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

		while ( $oDataset->next() ) {
			array_push($aCasesList, $oDataset->getRow());
		}

		$oDataset = ApplicationPeer::doSelectRS($oCriteriaDraft);
		$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
		
		while ( $oDataset->next() ) {
			array_push($aCasesList, $oDataset->getRow());
		}
        
		$filedNames = Array (
	        "APP_UID",
	        "APP_NUMBER",
	        "APP_UPDATE_DATE",
	        "DEL_PRIORITY",
	        "DEL_INDEX",
	        "TAS_UID",
	        "DEL_INIT_DATE", 
	        "DEL_FINISH_DATE", 
	        "USR_UID",
	        "APP_STATUS",
	        "DEL_TASK_DUE_DATE",
	        "APP_CURRENT_USER",
	        "APP_TITLE",
	        "APP_PRO_TITLE",
	        "APP_TAS_TITLE",
	        "APP_DEL_PREVIOUS_USER",
	    );
	    
	    $aCasesList = array_merge(Array($filedNames), $aCasesList);
	
	   // G::pr($aCasesList); die;
	        
	    
	    require_once ( 'classes/class.xmlfield_InputPM.php' );
	    
	    global $_DBArray;
	    $_DBArray['reassign_byuser'] = $aCasesList;
	    $_SESSION['_DBArray'] = $_DBArray;
	    G::LoadClass('ArrayPeer');
	    $oCriteria = new Criteria('dbarray');
	    $oCriteria->setDBArrayTable('reassign_byuser');
	
	    $oHeadPublisher =& headPublisher::getSingleton();
        $oHeadPublisher->addScriptFile('/jscore/cases/reassignByUser.js');
        
	    $G_PUBLISH->AddContent('propeltable', 'cases/paged-table-reassigByUser', 'cases/cases_ToReassignByUserList', $oCriteria, Array('FROM_USR_UID'=>$sUserToReassign));
    
	}
	
	G::RenderPage('publish');
}
catch (Exception $oException) {
	die($oException->getMessage());
}
















