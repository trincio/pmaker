<?php
/**
 * processes_List.php
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
switch ($RBAC->userCanAccess('PM_FACTORY'))
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

/**************************/

	function parseItemArray( $array ) {
		if (!isset ($array->item) && !is_array($array) ) {
			return null;
		}

		$result = array();
		if ( isset ( $array->item ) ) {
		  foreach ($array->item as $key => $value) {			
  			$result[$value->key] = $value->value;
	  	}
	  }
	  else {
		  foreach ($array as $key => $value) {			
  			$result[$value->key] = $value->value;
	  	}
	  }
		return $result;
	}  

      
try {   
  G::LoadClass('processes');
  $oProcess = new Processes();
  $oProcess->ws_open_public ();   
    
  $result = $oProcess->ws_ProcessList ( );
   	$processes[] = array ( 'uid' => 'char', 'name' => 'char', 'age' => 'integer', 'balance' => 'float' );

    if ( $result->status_code == 0 && isset($result->processes) ) {
    	foreach ( $result->processes as $key => $val ) {
    		$process = parseItemArray($val); 
    		$processes[] = $process;
    	}
    }
  $_DBArray['processes'] = $processes;
  $_SESSION['_DBArray'] = $_DBArray;

  G::LoadClass( 'ArrayPeer');
  $c = new Criteria ('dbarray');
  $c->setDBArrayTable('processes');

  $G_MAIN_MENU            = 'processmaker';
  $G_ID_MENU_SELECTED     = 'PROCESSES';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('propeltable', 'paged-table', 'processes/processes_ListPublic', $c);
  G::RenderPage('publish');
  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
  }      
  