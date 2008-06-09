<?php
/**
 * webServiceAjax.php
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
if (($RBAC_Response=$RBAC->userCanAccess("PM_FACTORY"))!=1) return $RBAC_Response;
G::LoadInclude('ajax');
$_POST['action'] = get_ajax_value('action');

switch ($_POST['action'])
{
	case 'showForm':
	  global $G_PUBLISH;
  	global $G_HEADER;
  	$xmlform = isset($_POST['wsID']) ? 'setup/ws' . $_POST['wsID'] : '';
  	if ( file_exists ( PATH_XMLFORM . $xmlform . '.xml') ) {
  	  $G_PUBLISH = new Publisher();
      $G_HEADER->clearScripts();
      $G_PUBLISH->AddContent('xmlform', 'xmlform', $xmlform, '', '', '../setup/webServicesAjax');
      G::RenderPage('publish', 'raw');
    }
	break;
	case 'execWebService':
	  global $G_PUBLISH;
  	global $G_HEADER;
  	$method = isset($_POST['wsID']) ? $_POST['wsID'] : '';
  	print_r ($_POST);
  	print "execWebService <br>";
  	print $method;
	break;
	case 'showUsers':
	  G::LoadClass('groups');
	  $oGroup = new Groups();
	  global $G_PUBLISH;
  	global $G_HEADER;
  	$G_PUBLISH = new Publisher();
  	$G_PUBLISH->AddContent('propeltable', 'paged-table', 'groups/groups_UsersList', $oGroup->getUsersGroupCriteria($_POST['sGroupUID']), array('GRP_UID' => $_POST['sGroupUID']));
    $G_HEADER->clearScripts();
    G::RenderPage('publish', 'raw');
	break;
	case 'assignUser':
	  G::LoadClass('groups');
	  $oGroup = new Groups();
	  $oGroup->addUserToGroup($_POST['GRP_UID'], $_POST['USR_UID']);
	break;
	case 'ofToAssignUser':
	  G::LoadClass('groups');
	  $oGroup = new Groups();
	  $oGroup->removeUserOfGroup($_POST['GRP_UID'], $_POST['USR_UID']);
	break;	
	
	case 'verifyGroupname':  	  	    	  
  	  $_POST['sOriginalGroupname'] = get_ajax_value('sOriginalGroupname');
  	  $_POST['sGroupname']         = get_ajax_value('sGroupname');  	    	   	  	
  	  if ($_POST['sOriginalGroupname'] == $_POST['sGroupname'])
  	  {
  	    echo '0';
  	  }
  	  else
  	  {   	  	
  	  	require_once 'classes/model/Groupwf.php';
  	  	G::LoadClass('Groupswf');
	      $oGroup = new Groupwf();
	      $oCriteria=$oGroup->loadByGroupname($_POST['sGroupname']);	        	    
  	  	$oDataset = GroupwfPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRow = $oDataset->getRow();                      
        if (!$aRow)
  	  	{
  	  		echo '0';
  	  	}
  	  	else
  	  	{
  	  		echo '1';
  	  	} 	   
  	  }
  	break;
}
