<?
/**
 * cases_SaveData.php
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
  //validate the data post
  $oForm = new Form($_SESSION['PROCESS']. '/' . $_GET['UID'], PATH_DYNAFORM);
  $oForm->validatePost();

  /* Includes */
  G::LoadClass('case');

  //load the variables
  $oCase = new Cases();
  $Fields = $oCase->loadCase( $_SESSION['APPLICATION'] );
  $Fields['APP_DATA'] = array_merge($Fields['APP_DATA'], G::getSystemConstants());
  $Fields['APP_DATA'] = array_merge( $Fields['APP_DATA'], (array)$_POST['form']);

  #here we must verify if is a debug session
  $trigger_debug_session = $_SESSION['TRIGGER_DEBUG']['ISSET']; #here we must verify if is a debugg session

  #trigger debug routines...
  
  //cleaning debug variables
  $_SESSION['TRIGGER_DEBUG']['ERRORS'] = Array();
  $_SESSION['TRIGGER_DEBUG']['DATA'] = Array();
  $_SESSION['TRIGGER_DEBUG']['TRIGGERS_NAMES'] = '';
  
  $triggers = $oCase->loadTriggers( $_SESSION['TASK'], 'DYNAFORM', $_GET['UID'], 'AFTER');
  
  $_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] = count($triggers);
  $_SESSION['TRIGGER_DEBUG']['TIME'] = 'AFTER';
  if($_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] != 0){
	$_SESSION['TRIGGER_DEBUG']['TRIGGERS_NAMES'] = $oCase->getTriggerNames($triggers);
  }
  
  if( $_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] != 0 ) {
	//Execute after triggers - Start
	$Fields['APP_DATA'] = $oCase->ExecuteTriggers ( $_SESSION['TASK'], 'DYNAFORM', $_GET['UID'], 'AFTER', $Fields['APP_DATA'] );
	//Execute after triggers - End
  } 
  //save data
  $aData = array();
  $aData['APP_NUMBER']      = $Fields['APP_NUMBER'];
  $aData['APP_PROC_STATUS'] = $Fields['APP_PROC_STATUS'];
  $aData['APP_DATA']        = $Fields['APP_DATA'];
  $aData['DEL_INDEX']       = $_SESSION['INDEX'];
  $aData['TAS_UID']         = $_SESSION['TASK'];
  $oCase->updateCase( $_SESSION['APPLICATION'], $aData );

  //go to the next step
  $aNextStep = $oCase->getNextStep($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION']);
  $_SESSION['STEP_POSITION'] = $aNextStep['POSITION'];

  if($trigger_debug_session){
  	$_SESSION['TRIGGER_DEBUG']['BREAKPAGE'] = $aNextStep['PAGE'];
	G::header('location: ' . $aNextStep['PAGE'].'&breakpoint=triggerdebug');
  }
  else {	
    G::header('location: ' . $aNextStep['PAGE']);
  }











