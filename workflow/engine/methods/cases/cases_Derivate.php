<?
/**
 * cases_Derivate.php
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
/* Permissions */
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

/* Includes */
G::LoadClass('pmScript');
G::LoadClass('case');
G::LoadClass('derivation');

/* GET , POST & $_SESSION Vars */
/* Process the info */
$sStatus = 'TO_DO';
foreach ($_POST['form']['TASKS'] as $aValues)
{
	if ($aValues['TAS_ASSIGN_TYPE'] == 'SELFSERVICE')
	{
		$sStatus = 'SELFSERVICE';
	}
}

try {
  //load data
  $oCase = new Cases ();
  $oCase->thisIsTheCurrentUser($_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['USER_LOGGED'], 'REDIRECT', 'cases_List');
  $appFields = $oCase->loadCase( $_SESSION['APPLICATION'] );
  $appFields['APP_DATA'] = array_merge($appFields['APP_DATA'], G::getSystemConstants());


  #here we must verify if is a debug session
  $trigger_debug_session = $_SESSION['TRIGGER_DEBUG']['ISSET']; #here we must verify if is a debugg session

  #trigger debug routines...

  //cleaning debug variables
  $_SESSION['TRIGGER_DEBUG']['ERRORS'] = Array();
  $_SESSION['TRIGGER_DEBUG']['DATA'] = Array();
  $_SESSION['TRIGGER_DEBUG']['TRIGGERS_NAMES'] = Array();
  $_SESSION['TRIGGER_DEBUG']['TRIGGERS_VALUES'] = Array();

  $triggers = $oCase->loadTriggers( $_SESSION['TASK'], 'ASSIGN_TASK', -2, 'BEFORE');

  $_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] = count($triggers);
  $_SESSION['TRIGGER_DEBUG']['TIME'] = 'BEFORE';
  if($_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] != 0){
	$_SESSION['TRIGGER_DEBUG']['TRIGGERS_NAMES'] = $oCase->getTriggerNames($triggers);
	$_SESSION['TRIGGER_DEBUG']['TRIGGERS_VALUES'] = $triggers;
  }

  if( $_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] != 0 ) {
    //Execute triggers before derivation
  	$appFields['APP_DATA'] = $oCase->ExecuteTriggers ( $_SESSION['TASK'], 'ASSIGN_TASK', -2, 'BEFORE', $appFields['APP_DATA'] );
	//Execute after triggers - End
  }


  $appFields['DEL_INDEX']       = $_SESSION['INDEX'];
  $appFields['TAS_UID']         = $_SESSION['TASK'];

  //Save data - Start
  //$appFields = $oCase->loadCase( $_SESSION['APPLICATION'] );
  $oCase->updateCase ( $_SESSION['APPLICATION'], $appFields);
  //Save data - End

  //derivate case
  $oDerivation = new Derivation();
  $aCurrentDerivation = array(
    'APP_UID'    => $_SESSION['APPLICATION'],
    'DEL_INDEX'  => $_SESSION['INDEX'],
    'APP_STATUS' => $sStatus,
    'TAS_UID'    => $_SESSION['TASK'],
    'ROU_TYPE'   => $_POST['form']['ROU_TYPE']
  );

  $oDerivation->derivate( $aCurrentDerivation, $_POST['form']['TASKS'] );
  //Execute triggers after derivation
  $appFields = $oCase->loadCase( $_SESSION['APPLICATION'] ); //refresh appFields, because in derivations should change some values

  $triggers = $oCase->loadTriggers( $_SESSION['TASK'], 'ASSIGN_TASK', -2, 'AFTER');
  $cnt2 = count($triggers);
  $_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] = $_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] + $cnt2;

  if( $cnt2 != 0) {
    //Execute triggers after derivation
  	$appFields['APP_DATA'] = $oCase->ExecuteTriggers ( $_SESSION['TASK'], 'ASSIGN_TASK', -2, 'AFTER', $appFields['APP_DATA'] );
	  //Execute after triggers - End
  }
  //$appFields['DEL_INDEX'] = $_SESSION['INDEX'];
  //$appFields['TAS_UID']   = $_SESSION['TASK'];

  //Save data - Start
  //$appFields = $oCase->loadCase( $_SESSION['APPLICATION'] );
  $oCase->updateCase ( $_SESSION['APPLICATION'], $appFields);
  //Save data - End
  //Send notifications - Start
  $oUser     = new Users();
  $aUser     = $oUser->load($_SESSION['USER_LOGGED']);
  $sFromName = '"' . $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'] . '"';
  $oCase->sendNotifications($_SESSION['TASK'], $_POST['form']['TASKS'], $appFields['APP_DATA'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $sFromName);
  //Send notifications - End
  /* Redirect */

  #trigger debug routines...

  //cleaning debug variables
  $_SESSION['TRIGGER_DEBUG']['ERRORS'] = Array();
  $_SESSION['TRIGGER_DEBUG']['DATA'] = Array();
  $_SESSION['TRIGGER_DEBUG']['TRIGGERS_NAMES'] = Array();
  $_SESSION['TRIGGER_DEBUG']['TRIGGERS_VALUES'] = Array();

  $triggers = $oCase->loadTriggers( $_SESSION['TASK'], 'ASSIGN_TASK', -2, 'AFTER');

  $_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] = count($triggers);
  $_SESSION['TRIGGER_DEBUG']['TIME'] = 'AFTER';
  if($_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] != 0){
	$_SESSION['TRIGGER_DEBUG']['TRIGGERS_NAMES'] = $oCase->getTriggerNames($triggers);
	$_SESSION['TRIGGER_DEBUG']['TRIGGERS_VALUES'] = $triggers;
  }

  if( $_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] != 0 && $trigger_debug_session) {
	//Execute after triggers - Start
	$Fields['APP_DATA'] = $oCase->ExecuteTriggers ( $_SESSION['TASK'], 'ASSIGN_TASK', -2, 'AFTER', $appFields['APP_DATA'] );
	//Execute after triggers - End
  }


  $aNextStep['PAGE'] = 'cases_List';
  if($trigger_debug_session){
  	$_SESSION['TRIGGER_DEBUG']['BREAKPAGE'] = $aNextStep['PAGE'];
	G::header('location: ' . 'cases_Step?' .'breakpoint=triggerdebug');
  }
  else {
    G::header('location: cases_List');
  }
}
catch ( Exception $e ){
  /* Render Error Page */
  $G_MAIN_MENU        = 'processmaker';
  $G_SUB_MENU         = 'cases';
  $G_ID_MENU_SELECTED = 'CASES';

  $aMessage = array();
  $aMessage['MESSAGE'] = $e->getMessage();
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
  G::RenderPage( 'publish' );
}
