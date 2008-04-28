<?php
/**
 * users_Ajax.php
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
  global $RBAC;
  switch ($RBAC->userCanAccess('PM_LOGIN'))
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
  G::LoadInclude('ajax');
  if (isset($_POST['form']))
  {
  	$_POST = $_POST['form'];
  }
  $_POST['function'] = get_ajax_value('function');
  switch ($_POST['function'])
  {
  	case 'verifyUsername':
  	  $_POST['sOriginalUsername'] = get_ajax_value('sOriginalUsername');
  	  $_POST['sUsername']         = get_ajax_value('sUsername');
  	  if ($_POST['sOriginalUsername'] == $_POST['sUsername'])
  	  {
  	  	echo '0';
  	  }
  	  else
  	  {/*
  	  	G::LoadClassRBAC('user');
  	  	$oUser = new RBAC_User(new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME, null, null ,DB_ERROR_SHOWALL_AND_CONTINUE));
  	  	if (!$oUser->loadByUsername($_POST['sUsername']))
  	  	{
  	  		echo '0';
  	  	}
  	  	else
  	  	{
  	  		echo '1';
  	  	}*/
  	  	require_once 'classes/model/Users.php';
  	  	G::LoadClass('Users');
	      $oUser = new Users();
	      $oCriteria=$oUser->loadByUsername($_POST['sUsername']);	        	    
  	  	$oDataset = UsersPeer::doSelectRS($oCriteria);
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
  	case 'availableUsers':
  	  G::LoadClass('processMap');
      $oProcessMap = new ProcessMap();
  	  global $G_PUBLISH;
    	global $G_HEADER;
    	$G_PUBLISH = new Publisher();
      $G_PUBLISH->AddContent('propeltable', 'paged-table', 'users/users_AvailableUsers', $oProcessMap->getAvailableUsersCriteria($_GET['sTask'], $_GET['iType']));
      $G_HEADER->clearScripts();
      G::RenderPage('publish', 'raw');
  	break;
  	case 'assign':
  	  G::LoadClass('tasks');
  	  $oTasks = new Tasks();
  	  switch ((int)$_POST['TU_RELATION']) {
  	  	case 1:
  	  	  echo $oTasks->assignUser($_POST['TAS_UID'], $_POST['USR_UID'], $_POST['TU_TYPE']);
  	  	break;
  	  	case 2:
  	  	  echo $oTasks->assignGroup($_POST['TAS_UID'], $_POST['USR_UID'], $_POST['TU_TYPE']);
  	  	break;
  	  }
  	  G::LoadClass('processMap');
      $oProcessMap = new ProcessMap();
      $oProcessMap->getAvailableUsersCriteria($_POST['TAS_UID'], $_POST['TU_TYPE']);
  	break;
  	case 'ofToAssign':
  	  G::LoadClass('tasks');
  	  $oTasks = new Tasks();
  	  switch ((int)$_POST['TU_RELATION']) {
  	  	case 1:
  	  	  echo $oTasks->ofToAssignUser($_POST['TAS_UID'], $_POST['USR_UID'], $_POST['TU_TYPE']);
  	  	break;
  	  	case 2:
  	  	  echo $oTasks->ofToAssignGroup($_POST['TAS_UID'], $_POST['USR_UID'], $_POST['TU_TYPE']);
  	  	break;
  	  }
  	  G::LoadClass('processMap');
      $oProcessMap = new ProcessMap();
      $oProcessMap->getAvailableUsersCriteria($_POST['TAS_UID'], $_POST['TU_TYPE']);
  	break;
  	case 'changeView':
  	  $_SESSION['iType'] = $_POST['TU_TYPE'];
  	break;
  	
  	case 'deleteGroup':
  	  G::LoadClass('groups');
	  $oGroup = new Groups();
	  $oGroup->removeUserOfGroup($_POST['GRP_UID'], $_POST['USR_UID']);
	  $_GET['sUserUID'] = $_POST['USR_UID'];
	  $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent('view', 'users/users_Tree' );
      G::RenderPage('publish', 'raw');
  	break;
  	
  	case 'showUserGroupInterface':
  	  $_GET['sUserUID'] = $_POST['sUserUID'];
	  $G_PUBLISH = new Publisher;
	  $G_PUBLISH->AddContent('view', 'users/users_AssignGroup' );
	  G::RenderPage('publish', 'raw');
  	break;
  	
  	case 'showUserGroups':
  	  $_GET['sUserUID'] = $_POST['sUserUID'];
	  $G_PUBLISH = new Publisher;
	  $G_PUBLISH->AddContent('view', 'users/users_Tree' );
	  G::RenderPage('publish', 'raw');
  	break;
  	
  	case 'assignUserToGroup':
	  G::LoadClass('groups');
	  $oGroup = new Groups();
	  $oGroup->addUserToGroup($_POST['GRP_UID'], $_POST['USR_UID']);
	  echo '<div align="center"><h2><font color="blue">'.G::LoadTranslation('ID_MSG_ASSIGN_DONE').'</font></h2></div>';
	break;
  }
}
catch (Exception $oException) {
	die($oException->getMessage());
}
?>