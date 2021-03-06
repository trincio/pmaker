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

  $oCases = new Cases();
  //echo $_SESSION['PROCESS'].'<br>';
  /** here we verify if there is a any case with a unpause on this day*/
  if( $sTypeList === 'to_do' or $sTypeList === 'draft' or $sTypeList === 'paused') {
	$oCases->ThrowUnpauseDaemon();
  }
      
  /* Prepare page before to show */
  switch ( $sTypeList ) {
  	case 'to_do' :  
  	     if ( defined(  'ENABLE_CASE_LIST_OPTIMIZATION' ) ) {
  	       $aCriteria = $oCases->prepareCriteriaForToDo($sUIDUserLogged);
  	       $xmlfile  = 'cases/cases_ListTodoNew';
  	     }
  	     else
           list($aCriteria,$xmlfile) = $oCases->getConditionCasesList( $sTypeList, $sUIDUserLogged);      	     
  	     break;
    default : 
      list($aCriteria,$xmlfile) = $oCases->getConditionCasesList( $sTypeList, $sUIDUserLogged);    
  }

  /* Render page */
  $G_PUBLISH = new Publisher;
  if ($sTypeList == 'to_reassign') {
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_ReassignBy', '', array('REASSIGN_BY' => 1));
  }
  $G_PUBLISH->AddContent('propeltable', 'paged-table', $xmlfile, $aCriteria, null);
  G::RenderPage('publish');

  
  

