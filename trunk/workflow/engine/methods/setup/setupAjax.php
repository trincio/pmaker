<?php
/**
 * setupAjax.php
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
if (($RBAC_Response=$RBAC->userCanAccess("PM_SETUP"))!=1) return $RBAC_Response;

//$oSMTPJSON   = new Services_JSON();
//$oSMTPData   = $oSMTPJSON->decode(stripslashes($_POST['data']));
//$sOutput = '';

	if(isset($_GET['action'])) {
		G::LoadClass('setup');

		$oSMTPSetup = new Setup(new DBConnection);

		$action = strtolower ( $_GET['action'] );
		$data   = $_GET;

		$arr = get_class_methods( get_class($oSMTPSetup) );
		foreach ($arr as $method) {
		if ( $method == $action )
			$oSMTPSetup->{$action} ( $_GET );
		}
	}

	if(isset($_POST['request'])) {
		$request = $_POST['request'];

		switch($request) {
			case 'mailTest_Show':
				$srv = $_POST['srv'];
				$port =  $_POST['port'];
				$account = $_POST['account'];
				$passwd = $_POST['passwd'];
				$G_PUBLISH = new Publisher;
				$G_PUBLISH->AddContent('view', 'setup/mailConnectiontest');
				G::RenderPage('publish', 'raw');
			break;
			
			case 'testConnection':

			G::LoadClass('net');
			require_once('classes/class.smtp.rfc-821.php');

			define("SUCCESSFULL", 'SUCCESSFULL');
			define("FAILED", 'FAILED');
			
			
			//$host = 'smtp.bizmail.yahoo.com';
			$srv = $_POST['srv'];
			$port = ($_POST['port'] == 'default')? 0: $_POST['port'];
			$user = $_POST['account'];
			$passwd = $_POST['passwd'];
			$step = $_POST['step']; 

			$Server = new NET($srv);
			$oSMTP = new SMTP;
			
			switch ($step) {
				case 1:
					if ($Server->getErrno() == 0) {	
						print(SUCCESSFULL.',');
					} else {
						print(FAILED.','.$Server->error);
					}
				break;

				case 2:
					if($port == 0){
						$port = $oSMTP->SMTP_PORT;
					}
					$Server->scannPort($port);
					if ($Server->getErrno() == 0) {
						print(SUCCESSFULL.',');
					} else {
						print(FAILED.','.$Server->error);
					}
				break;
					
				#try to connect to host
				case 3:
					if($port == 0){
						$resp = $oSMTP->Connect($srv);
					} else {
						$resp = $oSMTP->Connect($srv, $port);
					}
					if( !$resp) {
						print(FAILED.','.$oSMTP->error['error']);
					} else {
						print(SUCCESSFULL.','.$oSMTP->status);
					}
				break;

				#try login to host
				case 4:
					if($port == 0){
						$resp = $oSMTP->Connect($srv);
					} else {
						$resp = $oSMTP->Connect($srv, $port);
					}
					if($resp) {
						if( !$oSMTP->Authenticate($user, $passwd) ) {
							print(FAILED.','.$oSMTP->error['error']);
						} else {
							print(SUCCESSFULL.',');
						}
					} else {
						print(FAILED.','.$oSMTP->error['error']);
					}
				break;

				default:
					print('test finished!');
			}
		}
	}
  
?>