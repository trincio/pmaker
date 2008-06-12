<?
/**
 * processes_ImportFile.php
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

 try {
  //load the variables
  G::LoadClass('processes');
  $oProcess = new Processes();

//  if ( isset ($_POST) ) {
//  	krumo ( $_POST );
//  }

  //save the file
  if ($_FILES['form']['error']['PROCESS_FILENAME'] == 0) {
    $filename = $_FILES['form']['name']['PROCESS_FILENAME'];
    $path     = PATH_DOCUMENT . 'input' . PATH_SEP ;
    $tempName = $_FILES['form']['tmp_name']['PROCESS_FILENAME'];
    G::uploadFile($tempName, $path, $filename );
  }
  $oData = $oProcess->getProcessData ( $path . $filename  );

  $Fields['PRO_FILENAME']  = $filename;
  $Fields['IMPORT_OPTION'] = 2;

  $sProUid = $oData->process['PRO_UID'];

  if ( $oProcess->processExists ( $sProUid ) ) {
    $G_MAIN_MENU            = 'processmaker';
    $G_ID_MENU_SELECTED     = 'PROCESSES';
    $G_PUBLISH = new Publisher;
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'processes/processes_ImportExisting', '', $Fields, 'processes_ImportExisting'  );
    G::RenderPage('publish');
    die;
  }

  if ( $oProcess->processExists ( $sProUid ) ) {
  //krumo ($oData);
    $sNewProUid = $oProcess->getUnusedProcessGUID() ;
    $oProcess->setProcessGuid ( $oData, $sNewProUid );
    $oProcess->setProcessParent( $oData, $sProUid );
    $oData->process['PRO_TITLE'] = 'Copy of ' . $oData->process['PRO_TITLE'] . date ( 'H:i:s' );
    $oProcess->renewAllTaskGuid ( $oData );
    $oProcess->renewAllDynaformGuid ( $oData );
    $oProcess->renewAllInputGuid ( $oData );
    $oProcess->renewAllOutputGuid ( $oData );
    $oProcess->renewAllStepGuid ( $oData );
    $oProcess->renewAllTriggerGuid ( $oData );
  }

  $oProcess->createProcessFromData ($oData, $path . $filename );
  G::header ( 'Location: processes_List');

}
catch ( Exception $e ){
  $G_PUBLISH = new Publisher;
	$aMessage['MESSAGE'] = $e->getMessage();
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
  G::RenderPage('publish');
}