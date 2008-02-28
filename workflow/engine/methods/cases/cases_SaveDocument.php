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
                   'APP_DOC_COMMENT'     => isset($_POST['form']['APP_DOC_COMMENT'])?$_POST['form']['APP_DOC_COMMENT']:'',
                   'APP_DOC_TITLE'       => '',
                   'APP_DOC_FILENAME'    => $_FILES['form']['name']['APP_DOC_FILENAME']);
  $oAppDocument->create($aFields);
  $sAppDocUid = $oAppDocument->getAppDocUid();
  $info = pathinfo( $oAppDocument->getAppDocFilename() );
  $ext = $info['extension'];

  //save the file
  G::uploadFile($_FILES['form']['tmp_name']['APP_DOC_FILENAME'], PATH_DOCUMENT . $_SESSION['APPLICATION'] . '/', $sAppDocUid . '.' . $ext );

  //to do: process_id undefined
  $oData['PRO_UID']	  = $_SESSION['APPLICATION'];
  $oData['APP_UID']	  = $_SESSION['APPLICATION'];
  $oData['FILENAME']	= PATH_UPLOAD . $_SESSION['APPLICATION'] . '/' . $_FILES['form']['name']['APP_DOC_FILENAME'] ;
/*
  if($_FILES['form']['name']['APP_DOC_FILENAME'] != ''){
  	$oPluginRegistry =& PMPluginRegistry::getSingleton();
	  $oPluginRegistry->executeTriggers ( PM_UPLOAD_DOCUMENT , $oData );
  }*/

  //go to the next step
  $aNextStep = $oCase->getNextStep($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION'] - 1);
  $_SESSION['STEP_POSITION'] = $aNextStep['POSITION'];
  G::header('location: ' . $aNextStep['PAGE']);
