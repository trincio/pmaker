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
  $oCase     = new Cases ();
  $appFields = $oCase->loadCase( $_SESSION['APPLICATION'] );
  $appFields['APP_DATA'] = array_merge($appFields['APP_DATA'], G::getSystemConstants());

  //Execute triggers before derivation
  $appFields['APP_DATA'] = $oCase->ExecuteTriggers ( $_SESSION['TASK'], 'ASSIGN_TASK', -2, 'BEFORE', $appFields['APP_DATA'] );
  $appFields['DEL_INDEX']       = $_SESSION['INDEX'];
  $appFields['TAS_UID']         = $_SESSION['TASK'];
  //Save data - Start
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
  $appFields['APP_DATA'] = $oCase->ExecuteTriggers ( $_SESSION['TASK'], 'ASSIGN_TASK', -2, 'AFTER', $appFields['APP_DATA'] );
  //$appFields['DEL_INDEX'] = $_SESSION['INDEX'];
  //$appFields['TAS_UID']   = $_SESSION['TASK'];
  //Save data - Start
  $oCase->updateCase ( $_SESSION['APPLICATION'], $appFields);
  //Save data - End
  //Send notifications - Start
  $oCase->sendNotifications($_SESSION['TASK'], $_POST['form']['TASKS'], $appFields['APP_DATA'], $_SESSION['APPLICATION'], $_SESSION['INDEX']);
  //Send notifications - End
  /* Redirect */
  G::header('location: cases_List');
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