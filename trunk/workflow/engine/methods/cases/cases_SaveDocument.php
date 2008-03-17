<?
/**
 * cases_SaveDocument.php
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
  //load the variables
  G::LoadClass('case');
  $oCase  = new Cases();
  $Fields = $oCase->loadCase($_SESSION['APPLICATION']);
  //Execute after triggers - Start
  $Fields['APP_DATA'] = $oCase->ExecuteTriggers ( $_SESSION['TASK'], 'INPUT_DOCUMENT', $_GET['UID'], 'AFTER', $Fields['APP_DATA'] );
  //Execute after triggers - End

  //save data
  $aData = array();
  $aData['APP_NUMBER']      = $Fields['APP_NUMBER'];
  $aData['APP_PROC_STATUS'] = $Fields['APP_PROC_STATUS'];
  $aData['APP_DATA']        = $Fields['APP_DATA'];
  $oCase->updateCase( $_SESSION['APPLICATION'], $aData );

  //save info
  require_once ( "classes/model/AppDocument.php" );

  $oAppDocument = new AppDocument();
  $aFields = array('APP_UID'             => $_SESSION['APPLICATION'],
                   'DEL_INDEX'           => $_SESSION['INDEX'],
                   'DEL_INDEX'           => $_SESSION['INDEX'],
                   'USR_UID'             => $_SESSION['USER_LOGGED'],
                   'DOC_UID'             => $_GET['UID'],
                   'APP_DOC_TYPE'        => $_POST['form']['APP_DOC_TYPE'],
                   'APP_DOC_CREATE_DATE' => date('Y-m-d H:i:s'),
                   'APP_DOC_COMMENT'     => isset($_POST['form']['APP_DOC_COMMENT']) ? $_POST['form']['APP_DOC_COMMENT'] : '',
                   'APP_DOC_TITLE'       => '',
                   'APP_DOC_FILENAME'    => isset($_FILES['form']['name']['APP_DOC_FILENAME']) ? $_FILES['form']['name']['APP_DOC_FILENAME'] : '');
  $oAppDocument->create($aFields);
  $sAppDocUid = $oAppDocument->getAppDocUid();
  $info = pathinfo( $oAppDocument->getAppDocFilename() );
  $ext = (isset($info['extension']) ? $info['extension'] : '');
/*
  $path = PATH_PLUGINS . 'knowledgeTree' . PATH_SEP . 'data' . PATH_SEP . 'mysql'. PATH_SEP . 'schema.sql';
  
  $contents = file_get_contents ( $path );
  print $contents;
  $con = Propel::getConnection( 'workflow');
  $stmt = $con->createStatement();
  $rs = $stmt->executeQuery($contents, ResultSet::FETCHMODE_NUM);
  die;
  */
  //save the file
  if (!empty($_FILES['form'])) {
  	if ($_FILES['form']['error']['APP_DOC_FILENAME'] == 0) {
      G::uploadFile($_FILES['form']['tmp_name']['APP_DOC_FILENAME'], PATH_DOCUMENT . $_SESSION['APPLICATION'] . '/', $sAppDocUid . '.' . $ext );
      
      //Hook PM_UPLOAD_DOCUMENT for upload document
      //to do: process_id undefined
      $oData['PRO_UID']	  = $_SESSION['APPLICATION'];
      $oData['APP_UID']	  = $_SESSION['APPLICATION'];
      $oData['FILENAME']	= PATH_UPLOAD . $_SESSION['APPLICATION'] . '/' . $_FILES['form']['name']['APP_DOC_FILENAME'] ;
    	$oPluginRegistry =& PMPluginRegistry::getSingleton();
	    $oPluginRegistry->executeTriggers ( PM_UPLOAD_DOCUMENT , $oData );
      
    }
  }

  //go to the next step
  if (!isset($_POST['form']['MORE'])) {
    $aNextStep = $oCase->getNextStep($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION']);
    $_SESSION['STEP_POSITION'] = $aNextStep['POSITION'];
    G::header('location: ' . $aNextStep['PAGE']);
    die;
  }
  else {
  	G::header('location: ' . $_SERVER['HTTP_REFERER']);
  	die;
  }