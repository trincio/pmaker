<?
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
foreach ($_POST['form']['TASKS'] as $aValues)
{
	if ($aValues['TAS_UID'] == '-1')
	{
		$sStatus = 'COMPLETED';
	}
}

try {
  //load data
  $oCase     = new Cases ();
  $appFields = $oCase->loadCase( $_SESSION['APPLICATION'] );

  //Execute triggers before derivation
  $appFields['APP_DATA'] = $oCase->ExecuteTriggers ( $_SESSION['TASK'], 'ASSIGN_TASK', -2, 'BEFORE', $appFields['APP_DATA'] );
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
  //Save data - Start
  $oCase->updateCase ( $_SESSION['APPLICATION'], $appFields);
  //Save data - End

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
