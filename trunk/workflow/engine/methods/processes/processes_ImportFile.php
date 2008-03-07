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
  G::LoadClass('processes');
  $oProcess = new Processes();
  
  //save the file
  if ($_FILES['form']['error']['PROCESS_FILENAME'] == 0) {
    $filename = $_FILES['form']['name']['PROCESS_FILENAME'];
    $path     = PATH_DOCUMENT . 'input' . PATH_SEP ;
    $tempName = $_FILES['form']['tmp_name']['PROCESS_FILENAME'];
    G::uploadFile($tempName, $path, $filename );
  }

  $contents = file_get_contents ( $path . $filename );
  
  $oData = unserialize ($contents);
  
  
  $sProUid = $oData->process['PRO_UID'];
  
  if ( $oProcess->processExists ( $sProUid ) ) {
    $sNewProUid = $oProcess->getUnusedProcessGUID() ;
    $oProcess->setProcessGuid ( $oData, $sNewProUid );
    $oProcess->setProcessParent( $oData, $sProUid );
    $oData->process['PRO_TITLE'] = 'copy of Derivations ' . date ( 'H:i:s' );  
    $oProcess->renewAllTaskGuid ( $oData );
    $oProcess->renewAllDynaformGuid ( $oData );
  krumo ($oData); 
    $oProcess->createProcessFromData ($oData);
    die;
  	
  }
  
  $oProcess->createProcessRow ($oData->process);
  
  