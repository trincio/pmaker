<?php
/**
 * pluginsList.php
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
/*  switch ($RBAC->userCanAccess('PM_CASES'))
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
*/
  
// lets display the items

$items[] = array ( 'id' => 'char', 'title' => 'char', 'type' => 'char', 'creator' => 'char' ,
                   'modifiedBy' => 'char', 'filename' => 'char', 'size' => 'char', 'mime' => 'char');
//***************** Plugins **************************
	G::LoadClass('plugin');
  //here we are loading all plugins registered

  $oPluginRegistry =& PMPluginRegistry::getSingleton();
  if ($handle = opendir( PATH_PLUGINS  )) {
    while ( false !== ($file = readdir($handle))) {
    	 if ( strpos($file, '.php',1) && is_file(PATH_PLUGINS . $file) ) {
         include_once ( PATH_PLUGINS . $file );
         $pluginDetail = $oPluginRegistry->getPluginDetails ( $file );
         //$status = $pluginDetail->enabled ? 'Enabled' : 'Disabled';
         $status = $pluginDetail->enabled ? G::LoadTranslation('ID_ENABLED') : G::LoadTranslation('ID_DISABLED');
         if ( isset ($pluginDetail->aWorkspaces ) ) {
           if ( ! in_array ( SYS_SYS, $pluginDetail->aWorkspaces) )
             continue;
         }
         $linkEditValue = $pluginDetail->sSetupPage != '' && $pluginDetail->enabled ?  G::LoadTranslation('ID_SETUP') : ' '; 

         $link = 'pluginsChange?id=' . $file . '&status=' . $pluginDetail->enabled;
         $linkEdit = 'pluginsSetup?id=' . $file ;
         if ( isset ($pluginDetail) )
           $items[] = array ( 
             'id'   => count( $items ),
             //'title'=>$pluginDetail->sFriendlyName, 
             'title'=>$pluginDetail->sFriendlyName . "\n(" . $pluginDetail->sNamespace . '.php)', 
             'className' => $pluginDetail->sNamespace, 
             'description' => $pluginDetail->sDescription, 
             'setupPage' => $pluginDetail->sSetupPage, 
             'enabled'=> $status, 
             'url' => $link,
             'urlEdit' => $linkEdit,
             'linkEditValue' => $linkEditValue );
         
       }
    }
    closedir($handle);
  }

$folders['items'] = $items;

$_DBArray['plugins'] = $items;
$_SESSION['_DBArray'] = $_DBArray;

    G::LoadClass( 'ArrayPeer');
    $c = new Criteria ('dbarray');
    $c->setDBArrayTable('plugins');
    //$c->addAscendingOrderByColumn ('id');
    
  $G_MAIN_MENU            = 'processmaker';
  $G_ID_MENU_SELECTED     = 'SETUP';
  $G_SUB_MENU             = 'setup';
  $G_ID_SUB_MENU_SELECTED = 'PLUGINS';
  
  $G_PUBLISH = new Publisher;

  $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'setup/pluginList', $c );  
  G::RenderPage('publish');
