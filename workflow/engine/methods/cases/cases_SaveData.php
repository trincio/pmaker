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
  $oCase->thisIsTheCurrentUser($_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['USER_LOGGED'], 'REDIRECT', 'cases_List');
  $Fields = $oCase->loadCase( $_SESSION['APPLICATION'] );
  $Fields['APP_DATA'] = array_merge($Fields['APP_DATA'], G::getSystemConstants());
  $Fields['APP_DATA'] = array_merge( $Fields['APP_DATA'], (array)$_POST['form']);

  #here we must verify if is a debug session
  $trigger_debug_session = $_SESSION['TRIGGER_DEBUG']['ISSET']; #here we must verify if is a debugg session

  #trigger debug routines...

  //cleaning debug variables
  $_SESSION['TRIGGER_DEBUG']['ERRORS'] = Array();
  $_SESSION['TRIGGER_DEBUG']['DATA'] = Array();
  $_SESSION['TRIGGER_DEBUG']['TRIGGERS_NAMES'] = Array();
  $_SESSION['TRIGGER_DEBUG']['TRIGGERS_VALUES'] = Array();

  $triggers = $oCase->loadTriggers( $_SESSION['TASK'], 'DYNAFORM', $_GET['UID'], 'AFTER');

  $_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] = count($triggers);
  $_SESSION['TRIGGER_DEBUG']['TIME'] = 'AFTER';
  if($_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] != 0){
	$_SESSION['TRIGGER_DEBUG']['TRIGGERS_NAMES'] = $oCase->getTriggerNames($triggers);
	$_SESSION['TRIGGER_DEBUG']['TRIGGERS_VALUES'] = $triggers;
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
  //$Fields = $oCase->loadCase( $_SESSION['APPLICATION'] );
  $oCase->updateCase( $_SESSION['APPLICATION'], $aData );
  //save files
  require_once 'classes/model/AppDocument.php';
  foreach ($_FILES['form']['name'] as $sFieldName => $vValue) {
    if ($_FILES['form']['error'][$sFieldName] == 0) {
      $oAppDocument = new AppDocument();
      $aFields = array('APP_UID'             => $_SESSION['APPLICATION'],
                       'DEL_INDEX'           => $_SESSION['INDEX'],
                       'USR_UID'             => $_SESSION['USER_LOGGED'],
                       'DOC_UID'             => -1,
                       'APP_DOC_TYPE'        => 'ATTACHED',
                       'APP_DOC_CREATE_DATE' => date('Y-m-d H:i:s'),
                       'APP_DOC_COMMENT'     => '',
                       'APP_DOC_TITLE'       => '',
                       'APP_DOC_FILENAME'    => $_FILES['form']['name'][$sFieldName]);
      $oAppDocument->create($aFields);
      $sAppDocUid = $oAppDocument->getAppDocUid();
      $aInfo      = pathinfo($oAppDocument->getAppDocFilename());
      $sExtension = (isset($aInfo['extension']) ? $aInfo['extension'] : '');
      $sPathName  = PATH_DOCUMENT . $_SESSION['APPLICATION'] . PATH_SEP;
      $sFileName  = $sAppDocUid . '.' . $sExtension;
      G::uploadFile($_FILES['form']['tmp_name'][$sFieldName], $sPathName, $sFileName);
      //Plugin Hook PM_UPLOAD_DOCUMENT for upload document
    	$oPluginRegistry =& PMPluginRegistry::getSingleton();
      if ($oPluginRegistry->existsTrigger(PM_UPLOAD_DOCUMENT) && class_exists('uploadDocumentData')) {
        $documentData = new uploadDocumentData (
                          $_SESSION['APPLICATION'],
                          $_SESSION['USER_LOGGED'],
                          $sPathName . $sFileName,
                          $aFields['APP_DOC_FILENAME'],
                          $sAppDocUid
                          );
  	    $oPluginRegistry->executeTriggers(PM_UPLOAD_DOCUMENT, $documentData);
  	    unlink($sPathName . $sFileName);
      }
    }
  }

  //go to the next step
  $aNextStep = $oCase->getNextStep($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION']);
  $_SESSION['STEP_POSITION'] = $aNextStep['POSITION'];


  if($trigger_debug_session){
	  $_SESSION['TRIGGER_DEBUG']['BREAKPAGE'] = $aNextStep['PAGE'];
	  $aNextStep['PAGE'] = $aNextStep['PAGE'].'&breakpoint=triggerdebug';
  }

  $oForm->validatePost();
  if( $missing_req_values = $oForm->validateRequiredFields($_POST['form']) ) {
	  $_POST['next_step'] = $aNextStep;
	  $_POST['previous_step'] = $oCase->getPreviousStep($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION']);
	  $_POST['req_val'] = $missing_req_values;
	  $G_PUBLISH = new Publisher;
	  $G_PUBLISH->AddContent('view', 'cases/missRequiredFields');
	  G::RenderPage('publish');
	  exit(0);
  }

  G::header('location: ' . $aNextStep['PAGE']);












