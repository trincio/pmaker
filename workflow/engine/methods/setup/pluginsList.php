<?php
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
    	 if ( strpos($file, '.php',1) ) {
         require_once ( PATH_PLUGINS . $file );
         $pluginDetail = $oPluginRegistry->getPluginDetails ( $file );
         $status = $pluginDetail->enabled ? 'Enabled' : 'Disabled';
         $linkEditValue = $pluginDetail->sSetupPage == '' ? ' ' : G::LoadTranslation('ID_SETUP'); 

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
