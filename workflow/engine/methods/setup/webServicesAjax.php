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
ini_set ( "soap.wsdl_cache_enabled", "0" ); // enabling WSDL cache


G::LoadClass ( 'ArrayPeer' );

if (($RBAC_Response = $RBAC->userCanAccess ( "PM_FACTORY" )) != 1)
	return $RBAC_Response;
G::LoadInclude ( 'ajax' );
$_POST ['action'] = get_ajax_value ( 'action' );

switch ($_POST ['action']) {
	case 'showForm' :
		global $G_PUBLISH;
		$xmlform = isset ( $_POST ['wsID'] ) ? 'setup/ws' . $_POST ['wsID'] : '';
		if (file_exists ( PATH_XMLFORM . $xmlform . '.xml' )) {
			//print_r ($_SESSION['_DBArray']);
			global $_DBArray;
			$_DBArray = (isset ( $_SESSION ['_DBArray'] ) ? $_SESSION ['_DBArray'] : '');
			$G_PUBLISH = new Publisher ( );
			$fields ['SESSION_ID'] = isset ( $_SESSION ['WS_SESSION_ID'] ) ? $_SESSION ['WS_SESSION_ID'] : '';
			$fields ['ACTION'] = $_POST ['wsID'];
			$G_PUBLISH->AddContent ( 'xmlform', 'xmlform', $xmlform, '', $fields, '../setup/webServicesAjax' );
			G::RenderPage ( 'publish', 'raw' );
		}
		break;
	
	case 'showDetails' :
		G::LoadClass ( 'groups' );
		
		$dbc = new DBConnection ( );
		$ses = new DBSession ( $dbc );
		
		if (! isset ( $_SESSION ['END_POINT'] )) {
			$aFields ['WS_HOST'] = $_SERVER ['HTTP_HOST'];
			$aFields ['WS_WORKSPACE'] = SYS_SYS;
		} else {
			if (strpos ( $_SESSION ['END_POINT'], 'https' ) !== false) {
				preg_match ( '@^(?:https://)?([^/]+)@i', $_SESSION ['END_POINT'], $coincidencias );
			} else {
				preg_match ( '@^(?:http://)?([^/]+)@i', $_SESSION ['END_POINT'], $coincidencias );
			}
			$aAux = explode ( ':', $coincidencias [1] );
			$aFields ['WS_HOST'] = $aAux [0];
			$aFields ['WS_PORT'] = (isset ( $aAux [1] ) ? $aAux [1] : '');
			$aAux = explode ( $aAux [0] . (isset ( $aAux [1] ) ? ':' . $aAux [1] : ''), $_SESSION ['END_POINT'] );
			$aAux = explode ( '/', $aAux [1] );
			$aFields ['WS_WORKSPACE'] = substr ( $aAux [1], 3 );
		}
		
		$rows [] = array ('uid' => 'char', 'name' => 'char', 'age' => 'integer', 'balance' => 'float' );
		$rows [] = array ('uid' => 'http', 'name' => 'http' );
		$rows [] = array ('uid' => 'https', 'name' => 'https' );
		
		$_DBArray ['protocol'] = $rows;
		$_SESSION ['_DBArray'] = $_DBArray;
		
		if (! isset ( $_SESSION ['END_POINT'] )) {
			//$wsdl = 'http://'.$_SERVER['HTTP_HOST'].'/sys'.SYS_SYS.'/en/green/services/wsdl';
			$wsdl = 'http://' . $_SERVER ['HTTP_HOST'];
			$workspace = SYS_SYS;
		} else {
			$wsdl = $_SESSION ['END_POINT'];
			$workspace = $_SESSION ['WS_WORKSPACE'];
		}
		
		$defaultEndpoint = 'http://' . $_SERVER ['SERVER_NAME'] . ':' . $_SERVER ['SERVER_PORT'] . '/sys' . SYS_SYS . '/en/green/services/wsdl2';
		
		$wsdl = isset ( $_SESSION ['END_POINT'] ) ? $_SESSION ['END_POINT'] : $defaultEndpoint;
		
		$wsSessionId = '';
		if (isset ( $_SESSION ['WS_SESSION_ID'] )) {
			$wsSessionId = $_SESSION ['WS_SESSION_ID'];
		}
		
		$aFields ['WSDL'] = $wsdl;
		$aFields ['OS'] = $workspace;
		$aFields ['WSID'] = $wsSessionId;
		
		$G_PUBLISH = new Publisher ( );
		$G_PUBLISH->AddContent ( 'xmlform', 'xmlform', 'setup/webServicesDetails', '', $aFields, 'webServicesSetupSave' );
		
		G::RenderPage ( "publish", "raw" );
		
		break;
}

try {
	global $G_PUBLISH;
	if (isset ( $_POST ['form'] ['ACTION'] )) {
		$frm = $_POST ['form'];
		$action = $frm ['ACTION'];
		if (isset ( $_POST ["epr"] )) {
			$_SESSION ['END_POINT'] = $_POST ["epr"];
		}
		$defaultEndpoint = 'http://' . $_SERVER ['SERVER_NAME'] . ':' . $_SERVER ['SERVER_PORT'] . '/sys' . SYS_SYS . '/en/green/services/wsdl2';
		
		$endpoint = isset ( $_SESSION ['END_POINT'] ) ? $_SESSION ['END_POINT'] : $defaultEndpoint;
		
		$sessionId = isset ( $_SESSION ['SESSION_ID'] ) ? $_SESSION ['SESSION_ID'] : '';
		@$client = new SoapClient ( $endpoint );
		
		switch ($action) {
			case "Login" :
				$user = $frm ["USER_ID"];
				$pass = $frm ["PASSWORD"];
				$params = array ('userid' => $user, 'password' => $pass );
				$result = $client->__SoapCall ( 'login', array ($params ) );
				$_SESSION ['WS_SESSION_ID'] = '';
				if ($result->status_code == 0) {
					$_SESSION ['WS_SESSION_ID'] = $result->message;
				}
				$G_PUBLISH = new Publisher ( );
				$fields ['status_code'] = $result->status_code;
				$fields ['message'] = 'ProcessMaker WebService version: ' . $result->version . "\n" . $result->message;
				$fields ['version'] = $result->version;
				$fields ['time_stamp'] = $result->timestamp;
				$G_PUBLISH->AddContent ( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
				G::RenderPage ( 'publish', 'raw' );
				break;
			case "ProcessList" :
				$sessionId = $frm ["SESSION_ID"];
				$params = array ('sessionId' => $sessionId );
				$result = $client->__SoapCall ( 'ProcessList', array ($params ) );
				$G_PUBLISH = new Publisher ( );
				$rows [] = array ('guid' => 'char', 'name' => 'char' );
				if (isset ( $result->processes ))
					if (is_object ( $result->processes )) {
						if ($result->processes->guid)
							$guid = $result->processes->guid;
						if ($result->processes->name)
							$name = $result->processes->name;
						$rows [] = array ('guid' => $guid, 'name' => $name );
					} else
						foreach ( $result->processes as $key => $item ) {
							if (isset ( $item->item ))
								foreach ( $item->item as $index => $val ) {
									if ($val->key == 'guid')
										$guid = $val->value;
									if ($val->key == 'name')
										$name = $val->value;
								}
							else if (is_array ( $item ))
								foreach ( $item as $index => $val ) {
									if ($val->key == 'guid')
										$guid = $val->value;
									if ($val->key == 'name')
										$name = $val->value;
								}
							else {
								if (isset ( $item->guid ))
									$guid = $item->guid;
								if (isset ( $item->name ))
									$name = $item->name;
							}
							
							$rows [] = array ('guid' => $guid, 'name' => $name );
						}
				if (isset ( $_SESSION ['_DBArray'] ))
					$_DBArray = $_SESSION ['_DBArray'];
				$_DBArray ['process'] = $rows;
				$_SESSION ['_DBArray'] = $_DBArray;
				
				$c = new Criteria ( 'dbarray' );
				$c->setDBArrayTable ( 'process' );
				$c->addAscendingOrderByColumn ( 'name' );
				$G_PUBLISH->AddContent ( 'propeltable', 'paged-table', 'setup/wsrProcessList', $c );
				G::RenderPage ( 'publish', 'raw' );
				break;
			case "RoleList" :
				$sessionId = $frm ["SESSION_ID"];
				$params = array ('sessionId' => $sessionId );
				$result = $client->__SoapCall ( 'RoleList', array ($params ) );
				
				$G_PUBLISH = new Publisher ( );
				$rows [] = array ('guid' => 'char', 'name' => 'char' );
				if (isset ( $result->roles ))
					if (is_object ( $result->roles )) {
						if ($result->roles->guid)
							$guid = $result->roles->guid;
						if ($result->roles->name)
							$name = $result->roles->name;
						$rows [] = array ('guid' => $guid, 'name' => $name );
					} else
						foreach ( $result->roles as $key => $item ) {
							if (isset ( $item->item ))
								foreach ( $item->item as $index => $val ) {
									if ($val->key == 'guid')
										$guid = $val->value;
									if ($val->key == 'name')
										$name = $val->value;
								}
							else if (is_array ( $item ))
								foreach ( $item as $index => $val ) {
									if ($val->key == 'guid')
										$guid = $val->value;
									if ($val->key == 'name')
										$name = $val->value;
								}
							else {
								if (isset ( $item->guid ))
									$guid = $item->guid;
								if (isset ( $item->name ))
									$name = $item->name;
							}
							
							$rows [] = array ('guid' => $guid, 'name' => $name );
						}
				if (isset ( $_SESSION ['_DBArray'] ))
					$_DBArray = $_SESSION ['_DBArray'];
				$_DBArray ['role'] = $rows;
				$_SESSION ['_DBArray'] = $_DBArray;
				
				G::LoadClass ( 'ArrayPeer' );
				$c = new Criteria ( 'dbarray' );
				$c->setDBArrayTable ( 'role' );
				$c->addAscendingOrderByColumn ( 'name' );
				$G_PUBLISH->AddContent ( 'propeltable', 'paged-table', 'setup/wsrRoleList', $c );
				G::RenderPage ( 'publish', 'raw' );
				break;
			case "GroupList" :
				$sessionId = $frm ["SESSION_ID"];
				$params = array ('sessionId' => $sessionId );
				$result = $client->__SoapCall ( 'GroupList', array ($params ) );
				
				$G_PUBLISH = new Publisher ( );
				$rows [] = array ('guid' => 'char', 'name' => 'char' );
				if (isset ( $result->groups ))
					if (is_object ( $result->groups )) {
						if ($result->groups->guid)
							$guid = $result->groups->guid;
						if ($result->groups->name)
							$name = $result->groups->name;
						$rows [] = array ('guid' => $guid, 'name' => $name );
					} else
						foreach ( $result->groups as $key => $item ) {
							if (isset ( $item->item ))
								foreach ( $item->item as $index => $val ) {
									if ($val->key == 'guid')
										$guid = $val->value;
									if ($val->key == 'name')
										$name = $val->value;
								}
							else if (is_array ( $item ))
								foreach ( $item as $index => $val ) {
									if ($val->key == 'guid')
										$guid = $val->value;
									if ($val->key == 'name')
										$name = $val->value;
								}
							else {
								if (isset ( $item->guid ))
									$guid = $item->guid;
								if (isset ( $item->name ))
									$name = $item->name;
							}
							
							$rows [] = array ('guid' => $guid, 'name' => $name );
						}
				if (isset ( $_SESSION ['_DBArray'] ))
					$_DBArray = $_SESSION ['_DBArray'];
				$_DBArray ['group'] = $rows;
				$_SESSION ['_DBArray'] = $_DBArray;
				
				G::LoadClass ( 'ArrayPeer' );
				$c = new Criteria ( 'dbarray' );
				$c->setDBArrayTable ( 'group' );
				$c->addAscendingOrderByColumn ( 'name' );
				$G_PUBLISH->AddContent ( 'propeltable', 'paged-table', 'setup/wsrGroupList', $c );
				G::RenderPage ( 'publish', 'raw' );
				break;
			case "CaseList" :
				$sessionId = $frm ["SESSION_ID"];
				$params = array ('sessionId' => $sessionId );
				$result = $client->__SoapCall ( 'CaseList', array ($params ) );
				$G_PUBLISH = new Publisher ( );
				$rows [] = array ('guid' => 'char', 'name' => 'char', 'status' => 'char', 'delIndex' => 'char' );
				
				if (isset ( $result->cases ))
					if (is_object ( $result->cases )) {
						$name = '';
						if ($result->cases->guid)
							$guid = $result->cases->guid;
						if ($result->cases->name)
							$name = $result->cases->name;
						if ($result->cases->status)
							$status = $result->cases->status;
						if ($result->cases->delIndex)
							$delIndex = $result->cases->delIndex;
						$rows [] = array ('guid' => $guid, 'name' => $name, 'status' => $status, 'delIndex' => $delIndex );
					} else
						foreach ( $result->cases as $key => $item ) {
							if (isset ( $item->item ))
								foreach ( $item->item as $index => $val ) {
									if ($val->key == 'guid')
										$guid = $val->value;
									if ($val->key == 'name')
										$name = $val->value;
									if ($val->key == 'status')
										$status = $val->value;
									if ($val->key == 'delIndex')
										$delIndex = $val->value;
								}
							else if (is_array ( $item ))
								foreach ( $item as $index => $val ) {
									if ($val->key == 'guid')
										$guid = $val->value;
									if ($val->key == 'name')
										$name = $val->value;
									if ($val->key == 'status')
										$status = $val->value;
									if ($val->key == 'delIndex')
										$delIndex = $val->value;
								}
							else {
								if (isset ( $item->guid ))
									$guid = $item->guid;
								if (isset ( $item->name ))
									$name = $item->name;
								if (isset ( $item->status ))
									$status = $item->status;
								if (isset ( $item->delIndex ))
									$delIndex = $item->delIndex;
							}
							
							$rows [] = array ('guid' => $guid, 'name' => $name, 'status' => $status, 'delIndex' => $delIndex );
						
						}
				if (isset ( $_SESSION ['_DBArray'] ))
					$_DBArray = $_SESSION ['_DBArray'];
				$_DBArray ['case'] = $rows;
				$_SESSION ['_DBArray'] = $_DBArray;
				
				G::LoadClass ( 'ArrayPeer' );
				$c = new Criteria ( 'dbarray' );
				$c->setDBArrayTable ( 'case' );
				$c->addAscendingOrderByColumn ( 'name' );
				$G_PUBLISH->AddContent ( 'propeltable', 'paged-table', 'setup/wsrCaseList', $c );
				G::RenderPage ( 'publish', 'raw' );
				break;
			case "UserList" :
				$sessionId = $frm ["SESSION_ID"];
				$params = array ('sessionId' => $sessionId );
				$result = $client->__SoapCall ( 'UserList', array ($params ) );
				$G_PUBLISH = new Publisher ( );
				$rows [] = array ('guid' => 'char', 'name' => 'char' );
				if (isset ( $result->users ))
					if (is_object ( $result->users )) {
						if ($result->users->guid)
							$guid = $result->users->guid;
						if ($result->users->name)
							$name = $result->users->name;
						$rows [] = array ('guid' => $guid, 'name' => $name );
					} else
						foreach ( $result->users as $key => $item ) {
							if (isset ( $item->item ))
								foreach ( $item->item as $index => $val ) {
									if ($val->key == 'guid')
										$guid = $val->value;
									if ($val->key == 'name')
										$name = $val->value;
								}
							else if (is_array ( $item ))
								foreach ( $item as $index => $val ) {
									if ($val->key == 'guid')
										$guid = $val->value;
									if ($val->key == 'name')
										$name = $val->value;
								}
							else {
								if (isset ( $item->guid ))
									$guid = $item->guid;
								if (isset ( $item->name ))
									$name = $item->name;
							}
							
							$rows [] = array ('guid' => $guid, 'name' => $name );
						}
				if (isset ( $_SESSION ['_DBArray'] ))
					$_DBArray = $_SESSION ['_DBArray'];
				$_DBArray ['user'] = $rows;
				$_SESSION ['_DBArray'] = $_DBArray;
				
				G::LoadClass ( 'ArrayPeer' );
				$c = new Criteria ( 'dbarray' );
				$c->setDBArrayTable ( 'user' );
				$c->addAscendingOrderByColumn ( 'name' );
				$G_PUBLISH->AddContent ( 'propeltable', 'paged-table', 'setup/wsrUserList', $c );
				G::RenderPage ( 'publish', 'raw' );
				break;
			
			case "SendMessage" :
				$sessionId = $frm ["SESSION_ID"];
				$caseId = $frm ["CASE_ID"];
				$message = $frm ["MESSAGE"];
				$params = array ('sessionId' => $sessionId, 'caseId' => $caseId, 'message' => $message );
				$result = $client->__SoapCall ( 'sendMessage', array ($params ) );
				$G_PUBLISH = new Publisher ( );
				$fields ['status_code'] = $result->status_code;
				$fields ['message'] = $result->message;
				$fields ['time_stamp'] = $result->timestamp;
				$G_PUBLISH->AddContent ( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
				G::RenderPage ( 'publish', 'raw' );
				break;
			
			case "SendVariables" :
				$sessionId = $frm ["SESSION_ID"];
				$caseId = $frm ["CASE_ID"];
				
				$variables [1]->name = $frm ["NAME1"];
				$variables [1]->value = $frm ["VALUE1"];
				$variables [2]->name = $frm ["NAME2"];
				$variables [2]->value = $frm ["VALUE2"];
				$params = array ('sessionId' => $sessionId, 'caseId' => $caseId, 'variables' => $variables );
				$result = $client->__SoapCall ( 'SendVariables', array ($params ) );
				$G_PUBLISH = new Publisher ( );
				$fields ['status_code'] = $result->status_code;
				$fields ['message'] = $result->message;
				$fields ['time_stamp'] = $result->timestamp;
				$G_PUBLISH->AddContent ( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
				G::RenderPage ( 'publish', 'blank' );
				break;
			
			case "DerivateCase" :
				$sessionId = $frm ["SESSION_ID"];
				$caseId = $frm ["CASE_ID"];
				$delIndex = $frm ["DEL_INDEX"];
				
				$params = array ('sessionId' => $sessionId, 'caseId' => $caseId, 'delIndex' => $delIndex );
				$result = $client->__SoapCall ( 'DerivateCase', array ($params ) );
				$G_PUBLISH = new Publisher ( );
				$fields ['status_code'] = $result->status_code;
				$fields ['message'] = $result->message;
				$fields ['time_stamp'] = $result->timestamp;
				$G_PUBLISH->AddContent ( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
				G::RenderPage ( 'publish', 'raw' );
				break;
			
			case "ReassignCase" :
				$sessionId = $frm ["SESSION_ID"];
				$caseId = $frm ["CASE_ID"];
				$delIndex = $frm ["DEL_INDEX"];
				$userIdSource = $frm ['USERIDSOURCE'];
				$userIdTarget = $frm ['USERIDTARGET'];
				
				$params = array ('sessionId' => $sessionId, 'caseId' => $caseId, 'delIndex' => $delIndex, 'userIdSource' => $userIdSource, 'userIdTarget' => $userIdTarget );
				$result = $client->__SoapCall ( 'reassignCase', array ($params ) );
				
				$G_PUBLISH = new Publisher ( );
				$fields ['status_code'] = $result->status_code;
				$fields ['message'] = $result->message;
				$fields ['time_stamp'] = $result->timestamp;
				$G_PUBLISH->AddContent ( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
				G::RenderPage ( 'publish', 'raw' );
				
				break;
			
			case "NewCaseImpersonate" :
				$sessionId = $frm ["SESSION_ID"];
				$processId = $frm ["PROCESS_ID"];
				$userId = $frm ["USER_ID"];
				foreach ( $frm ['VARIABLES'] as $iRow => $aRow ) {
					$variables [$iRow]->name = $aRow ['NAME'];
					$variables [$iRow]->value = $aRow ['VALUE'];
				}
				$params = array ('sessionId' => $sessionId, 'processId' => $processId, 'userId' => $userId, 'variables' => $variables );
				$result = $client->__SoapCall ( 'NewCaseImpersonate', array ($params ) );
				$G_PUBLISH = new Publisher ( );
				$fields ['status_code'] = $result->status_code;
				$fields ['message'] = $result->message;
				$fields ['time_stamp'] = $result->timestamp;
				$G_PUBLISH->AddContent ( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
				G::RenderPage ( 'publish', 'raw' );
				break;
			
			case "NewCase" :
				$sessionId = $frm ["SESSION_ID"];
				$processId = $frm ["PROCESS_ID"];
				$taskId = $frm ["TASK_ID"];
				foreach ( $frm ['VARIABLES'] as $iRow => $aRow ) {
					$variables [$iRow]->name = $aRow ['NAME'];
					$variables [$iRow]->value = $aRow ['VALUE'];
				}
				$params = array ('sessionId' => $sessionId, 'processId' => $processId, 'taskId' => $taskId, 'variables' => $variables );
				$result = $client->__SoapCall ( 'NewCase', array ($params ) );
				$G_PUBLISH = new Publisher ( );
				$fields ['status_code'] = $result->status_code;
				$fields ['message'] = $result->message;
				$fields ['time_stamp'] = $result->timestamp;
				$G_PUBLISH->AddContent ( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
				G::RenderPage ( 'publish', 'raw' );
				break;
			
			case "AssignUserToGroup" :
				$sessionId = $frm ["SESSION_ID"];
				$userId = $frm ["USER_ID"];
				$groupId = $frm ["GROUP_ID"];
				$params = array ('sessionId' => $sessionId, 'userId' => $userId, 'groupId' => $groupId );
				$result = $client->__SoapCall ( 'AssignUserToGroup', array ($params ) );
				$G_PUBLISH = new Publisher ( );
				$fields ['status_code'] = $result->status_code;
				$fields ['message'] = $result->message;
				$fields ['time_stamp'] = $result->timestamp;
				$G_PUBLISH->AddContent ( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
				G::RenderPage ( 'publish', 'raw' );
				break;
			
			case "CreateUser" :
				$sessionId = $frm ["SESSION_ID"];
				$userId = $frm ["USER_ID"];
				$firstname = $frm ["FIRST_NAME"];
				$lastname = $frm ["LAST_NAME"];
				$email = $frm ["EMAIL"];
				$role = $frm ["ROLE"];
				$password = $frm ["PASSWORD"];
				
				$params = array ('sessionId' => $sessionId, 'userId' => $userId, 'firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'role' => $role, 'password' => $password );
				$result = $client->__SoapCall ( 'CreateUser', array ($params ) );
				$G_PUBLISH = new Publisher ( );
				$fields ['status_code'] = $result->status_code;
				$fields ['message'] = $result->message;
				$fields ['time_stamp'] = $result->timestamp;
				$G_PUBLISH->AddContent ( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
				G::RenderPage ( 'publish', 'raw' );
				break;
			
			case "TaskList" :
				$sessionId = $frm ["SESSION_ID"];
				$params = array ('sessionId' => $sessionId );
				$result = $client->__SoapCall ( 'TaskList', array ($params ) );
				
				$G_PUBLISH = new Publisher ( );
				$rows [] = array ('guid' => 'char', 'name' => 'char' );
				
				if (isset ( $result->tasks ))
					if (is_object ( $result->tasks )) {
						if ($result->tasks->guid)
							$guid = $result->tasks->guid;
						if ($result->tasks->name)
							$name = $result->tasks->name;
						$rows [] = array ('guid' => $guid, 'name' => $name );
					} else
						foreach ( $result->tasks as $key => $item ) {
							if (isset ( $item->item ))
								foreach ( $item->item as $index => $val ) {
									if ($val->key == 'guid')
										$guid = $val->value;
									if ($val->key == 'name')
										$name = $val->value;
								}
							else if (is_array ( $item ))
								foreach ( $item as $index => $val ) {
									if ($val->key == 'guid')
										$guid = $val->value;
									if ($val->key == 'name')
										$name = $val->value;
								}
							else {
								if (isset ( $item->guid ))
									$guid = $item->guid;
								if (isset ( $item->name ))
									$name = $item->name;
							}
							
							$rows [] = array ('guid' => $guid, 'name' => $name );
						}
				if (isset ( $_SESSION ['_DBArray'] ))
					$_DBArray = $_SESSION ['_DBArray'];
				$_DBArray ['task'] = $rows;
				$_SESSION ['_DBArray'] = $_DBArray;
				
				G::LoadClass ( 'ArrayPeer' );
				$c = new Criteria ( 'dbarray' );
				$c->setDBArrayTable ( 'task' );
				$c->addAscendingOrderByColumn ( 'name' );
				$G_PUBLISH->AddContent ( 'propeltable', 'paged-table', 'setup/wsrTaskList', $c );
				G::RenderPage ( 'publish', 'raw' );
				break;
			
			case "TaskCase" :
				$sessionId = $frm ["SESSION_ID"];
				$caseId = $frm ["CASE_ID"];
				
				$params = array ('sessionId' => $sessionId, 'caseId' => $caseId );
				$result = $client->__SoapCall ( 'TaskCase', array ($params ) );
				
				$G_PUBLISH = new Publisher ( );
				$rows [] = array ('guid' => 'char', 'name' => 'char' );
				
				if (isset ( $result->taskCases ))
					if (is_object ( $result->taskCases )) {
						if ($result->taskCases->guid)
							$guid = $result->taskCases->guid;
						if ($result->taskCases->name)
							$name = $result->taskCases->name;
						$rows [] = array ('guid' => $guid, 'name' => $name );
					} else
						foreach ( $result->taskCases as $key => $item ) {
							if (isset ( $item->item ))
								foreach ( $item->item as $index => $val ) {
									if ($val->key == 'guid')
										$guid = $val->value;
									if ($val->key == 'name')
										$name = $val->value;
								}
							else if (is_array ( $item ))
								foreach ( $item as $index => $val ) {
									if ($val->key == 'guid')
										$guid = $val->value;
									if ($val->key == 'name')
										$name = $val->value;
								}
							else {
								print_r ( $item );
								if (isset ( $item->guid ))
									$guid = $item->guid;
								if (isset ( $item->name ))
									$name = $item->name;
							}
							
							$rows [] = array ('guid' => $guid, 'name' => $name );
						}
				
				if (isset ( $_SESSION ['_DBArray'] ))
					$_DBArray = $_SESSION ['_DBArray'];
				$_DBArray ['taskCases'] = $rows;
				$_SESSION ['_DBArray'] = $_DBArray;
				
				G::LoadClass ( 'ArrayPeer' );
				$c = new Criteria ( 'dbarray' );
				$c->setDBArrayTable ( 'taskCases' );
				$c->addAscendingOrderByColumn ( 'name' );
				$G_PUBLISH->AddContent ( 'propeltable', 'paged-table', 'setup/wsrTaskCase', $c );
				G::RenderPage ( 'publish', 'raw' );
				break;
			
			case "UploadFiles" :
				$sessionId = $frm ["SESSION_ID"];
				$caseId = $frm ["CASE_ID"];
				print_r ( $_FILES );
				die ();
				/*$name=$_FILES['file']["name"];
				 $type=$_FILES['file']["type"];
				 $size=$_FILES['file']["size"];
				 echo $name."<br />".$type."<br />".$size;*/
				break;
			default :
				print_r ( $_POST );
		}
	}
} catch ( Exception $e ) {
	$G_PUBLISH = new Publisher ( );
	$aMessage ['MESSAGE'] = $e->getMessage ();
	$G_PUBLISH->AddContent ( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
	G::RenderPage ( 'publish', 'raw' );
}

