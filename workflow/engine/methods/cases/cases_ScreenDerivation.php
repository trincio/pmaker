<?php
/**
 * cases_ScreenDerivation.php
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
//var_dump($_SESSION['APPLICATION'],$_SESSION['PROCESS'],$_SESSION['INDEX'],$_SESSION['STEP_POSITION']);
	switch ($RBAC->userCanAccess('PM_CASES'))
	{
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

	$_SESSION['APPLICATION'] = '9470D56E79F44D';
	$_SESSION['PROCESS'] = '046F1977863C59';
	$_SESSION['USER_LOGGED'] = '446F8411F383A7';
	$_SESSION['INDEX']='1';

	G::LoadClass( "derivation" );
	G::LoadClass( "process" );

	$dbc         = new DBConnection;
	$oDerivation = new Derivation($dbc);
	$oProcess    = new Process($dbc );
	/*Get Process Information*/
  	$oProcess->load($_SESSION['PROCESS']);
	$Fields['PROCESS_INFORMATION'] = $oProcess->Fields;
/* derivando el primer caso */
	$frm['USER_UID']  = $_SESSION['USER_LOGGED'];
	$frm['APP_UID']   = $_SESSION['APPLICATION'];
	$frm['DEL_INDEX'] = $_SESSION['INDEX'];
	$Fields['TASK_INFORMATION'] = $oDerivation->prepareInformation($frm);
	//$Fields['POCESS_NAME'] = $Fields['TASK_INFORMATION'][];
	$G_MAIN_MENU        = 'processmaker';
	$G_ID_MENU_SELECTED = 'CASES';

	$G_PUBLISH = new Publisher;
	$G_PUBLISH->AddContent('smarty', 'cases/cases_ScreenDerivation', '', '', $Fields);
	G::RenderPage('publish');

	die;


?>