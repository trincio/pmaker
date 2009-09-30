<?php
/**
 * pluginsList.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
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
switch ($RBAC->userCanAccess('PM_SETUP'))
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

  // lets display the items
  $items[] = array ( 'id' => 'char', 'title' => 'char', 'type' => 'char', 'creator' => 'char' ,
                   'modifiedBy' => 'char', 'filename' => 'char', 'size' => 'char', 'mime' => 'char');

  //***************** Skins **************************
  $aFiles = array ();
  if ($handle = opendir( PATH_SKINS  )) {
    while ( false !== ($file = readdir($handle))) {
      $filename = substr ( $file,0, strrpos($file, '.'));
      if ( $filename != 'blank' && substr($file,0,1) != '.' && $filename != 'green' && $filename != 'raw' && $filename != 'tracker' && $filename != 'iphone') {
        if ( !isset($aFiles[ $filename ]) ) $aFiles[$filename] = 0; 
        if ( strpos($file, '.php', 1) ) $aFiles[ $filename ] += 1;
        if ( strpos($file, '.html',1) ) $aFiles[ $filename ] += 2;
      }
    }

    closedir($handle);

    foreach ( $aFiles as $key => $val ) {
      $linkPackValue = G::LoadTranslation('ID_EXPORT') ;
      $link = 'skinsExport?id=' . $filename ;
      $items[] = array ( 
                 'id' => count($items), 
                 'name' => $key, 
                 'filename' => $key,
                 'url' => $link,
                 'linkPackValue' => $linkPackValue 
                 );
    }
    $folders['items'] = $items;
  }
  
  $_DBArray['plugins'] = $items;
  $_SESSION['_DBArray'] = $_DBArray;

  G::LoadClass( 'ArrayPeer');
  $c = new Criteria ('dbarray');
  $c->setDBArrayTable('plugins');
    //$c->addAscendingOrderByColumn ('id');

  $G_MAIN_MENU            = 'processmaker';
  $G_ID_MENU_SELECTED     = 'SETUP';
  $G_SUB_MENU             = 'setup';
  $G_ID_SUB_MENU_SELECTED = 'SKINS';

  $G_PUBLISH = new Publisher;

  $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'setup/skinsList', $c );
  G::RenderPage('publish');

