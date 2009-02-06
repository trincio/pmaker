<?php
/**
 * languages.php
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
global $RBAC;
switch ($RBAC->userCanAccess('PM_SETUP_ADVANCE'))
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

$G_MAIN_MENU            = 'processmaker';
$G_SUB_MENU             = 'setup';
$G_ID_MENU_SELECTED     = 'SETUP';
$G_ID_SUB_MENU_SELECTED = 'LANGUAGES';

require_once 'classes/model/Configuration.php';
require_once 'classes/model/Language.php';
$oConfiguration = new Configuration();
$sDelimiter     = DBAdapter::getStringDelimiter();
$oCriteria      = new Criteria('workflow');
$oCriteria->add(ConfigurationPeer::CFG_UID, 'Language');		  
$oCriteria->add(ConfigurationPeer::OBJ_UID, '');
$oCriteria->add(ConfigurationPeer::PRO_UID, '');
$oCriteria->add(ConfigurationPeer::USR_UID, '');
$oCriteria->add(ConfigurationPeer::APP_UID, '');

if(ConfigurationPeer::doCount($oCriteria)==0)
 {
 		$aData['CFG_UID']   = 'Language';
		$aData['OBJ_UID']   = ''; 
		$aData['CFG_VALUE'] = 'en';
		$aData['PRO_UID']   = ''; 
		$aData['USR_UID']   = ''; 
		$aData['APP_UID']   = ''; 
		
		$oConfig = new Configuration();		  	  
		$oConfig->create($aData);  		
 }		
 

$oCriteria = new Criteria('workflow');
$oCriteria->addSelectColumn('LAN_ID');
$oCriteria->addSelectColumn('LAN_NAME');
$oCriteria->add(LanguagePeer::LAN_ENABLED, '1');

$oDataset = LanguagePeer::doSelectRS($oCriteria);
$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
$oDataset->next();
$aFields[] = array('LAN_ID'     => 'char',
      	           'LAN_NAME'   => 'char',
      	           'LAN_SELECT' => 'char',
      	           'LAN_EXPORT' => 'char',
      	           'LAN_DELETE' => 'char'      	            
      	           );

while ($aRow = $oDataset->getRow()) 
 { 
	 $oConfiguration = new Configuration();																																																		
	 $oCriteria      = new Criteria('workflow');
	 $oCriteria->addSelectColumn(ConfigurationPeer::CFG_VALUE);																											
	 $oCriteria->add(ConfigurationPeer::CFG_UID, 'Language');		  
	 $oCriteria->add(ConfigurationPeer::OBJ_UID, '');																																																						
   $oCriteria->add(ConfigurationPeer::PRO_UID, '');																											
   $oCriteria->add(ConfigurationPeer::USR_UID, '');																											
   $oCriteria->add(ConfigurationPeer::APP_UID, ''); 	 
   $oDataset1 = ConfigurationPeer::doSelectRS($oCriteria);
   $oDataset1->setFetchmode(ResultSet::FETCHMODE_ASSOC);
   $oDataset1->next();   		    																											
   $aRow1 = $oDataset1->getRow();
   if($aRow1['CFG_VALUE']==$aRow['LAN_ID'])
 	 		$radio='<input type="radio" name="language" value="'.$aRow['LAN_ID'].'" onclick="languageSelect(\''.$aRow['LAN_ID'].'\');" checked>';   	      	
 	 else		
 			$radio='<input type="radio" name="language" value="'.$aRow['LAN_ID'].'" onclick="languageSelect(\''.$aRow['LAN_ID'].'\');">';   	      		 
 	  	 
   $aFields[] = array('LAN_ID'     => $aRow['LAN_ID'],
      	           		'LAN_NAME'   => $aRow['LAN_NAME'],
      	           		'LAN_SELECT' => $radio,
      	           		'LAN_EXPORT' => G::LoadTranslation('ID_EXPORT'),
      	           		'LAN_DELETE' => G::LoadTranslation('ID_DELETE')      	            
      	           	  );
   $oDataset->next();
 }

global $_DBArray;
$_DBArray['languages']  = $aFields;
$_SESSION['_DBArray'] = $_DBArray;
G::LoadClass('ArrayPeer');
$oCriteria = new Criteria('dbarray');
$oCriteria->setDBArrayTable('languages');      	           

/*
$aFields['LAN_EXPORT'] = G::LoadTranslation('ID_EXPORT');
$aFields['LAN_DELETE'] = G::LoadTranslation('ID_DELETE');
*/
$aFields['CONFIRM']    = G::LoadTranslation('ID_MSG_CONFIRM_REMOVE_LANGUAGE');
$aFields['CANNOT']     = G::LoadTranslation('ID_MSG_CANNOT_REMOVE_LANGUAGE');
$aFields['RAND']       = rand();

$G_PUBLISH = new Publisher;
$G_PUBLISH->AddContent('propeltable', 'paged-table', 'setup/languages', $oCriteria , $aFields);
G::RenderPage('publish');