<?php
/**
 * cases_List.php
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

  /* Permissions */
  if (($RBAC_Response = $RBAC->userCanAccess("PM_CASES"))!=1) return $RBAC_Response;
  //if (($RBAC_Response = $RBAC->userCanAccess("PM_CASES"))!=1) return $RBAC_Response;
  /* Includes */
  G::LoadClass('case');
  G::LoadClass('configuration');

  /* GET , POST & $_SESSION Vars */

  // $_GET['l'] has the type of cases list like todo,pause,cancel, all

  $conf = new Configurations();  
  if (!isset($_GET['l']))
  {
    $confCasesList = $conf->loadObject('ProcessMaker','cases_List','',$_SESSION['USER_LOGGED'],'');    
    if (is_array($confCasesList))
    {
      $sTypeList = $confCasesList['sTypeList'];
    }
    else
    {
      $sTypeList = 'to_do';
    }
  }
  else
  {
    $sTypeList = $_GET['l'];
    $confCasesList=array('sTypeList'=>$sTypeList);
    $conf->saveObject($confCasesList,'ProcessMaker','cases_List','',$_SESSION['USER_LOGGED'],'');
  }

  $sUIDUserLogged = $_SESSION['USER_LOGGED'];

  /* Menues */
  $G_MAIN_MENU            = 'processmaker';
  $G_SUB_MENU             = 'cases';
  $G_ID_MENU_SELECTED     = 'CASES';
  $G_ID_SUB_MENU_SELECTED = 'CASES_' . strtoupper($sTypeList);

  /* Prepare page before to show */
  $oCases = new Cases();
  list($Criteria,$xmlfile) = $oCases->getConditionCasesList( $sTypeList, $sUIDUserLogged);
  
  /* Render page */
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent( 'propeltable', 'paged-table', $xmlfile, $Criteria );
  G::RenderPage( "publish" );
