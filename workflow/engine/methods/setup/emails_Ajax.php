<?php
/**
 * emails.php
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

$request = (isset($_POST['action']))?$_POST['action']:$_POST['request'];

switch ($request) {
	case 'testEmailConfiguration':
		global $G_PUBLISH;
		$G_PUBLISH = new Publisher();
		$aFields['FROM_EMAIL'] = $_POST['usermail'];
		$G_PUBLISH->AddContent('xmlform', 'xmlform', 'setup/emails_TestForm','', $aFields);
		G::RenderPage('publish', 'raw');
	break;
	case 'sendTestMail':
		$sFrom    = ($_POST['FROM_NAME'] != '' ? $_POST['FROM_NAME'] . ' ' : '') . '<' . $_POST['FROM_EMAIL'] . '>';
		$sSubject = G::LoadTranslation('ID_MESS_TEST_SUBJECT');
		$msg = G::LoadTranslation('ID_MESS_TEST_BODY');

		switch ($_POST['MESS_ENGINE']) {
			case 'MAIL':
				$engine = G::LoadTranslation('ID_MESS_ENGINE_TYPE_1');
			break;
			case 'PHPMAILER':
				$engine = G::LoadTranslation('ID_MESS_ENGINE_TYPE_2');
			break;
			case 'OPENMAIL':
				$engine = G::LoadTranslation('ID_MESS_ENGINE_TYPE_3');
			break;
		}

		$colosa_msg = "This Business Process is powered by <b>ProcessMaker</b>.";
		$sBody = "
		<table style=\"background-color: white; font-family: Arial,Helvetica,sans-serif; color: black; font-size: 11px; text-align: left;\" cellpadding='10' cellspacing='0' width='100%'>
		<tbody><tr><td><img id='logo' src='http://".$_SERVER['SERVER_NAME']."/images/processmaker.logo.jpg' /></td></tr>
		<tr><td style='font-size: 14px;'>$msg $engine [".date('H:i:s')."]</td></tr>
		<tr><td style='vertical-align:middel;'>
		<br /><hr><b>This Business Process is powered by ProcessMaker.<b><br />
		<a href='http://www.processmaker.com' style='color:#c40000;'>www.processmaker.com</a><br /></td>
		</tr></tbody></table>";

		G::LoadClass('spool');
		$oSpool = new spoolRun();
		$oSpool->setConfig( array(
			'MESS_ENGINE'   => $_POST['MESS_ENGINE'],
			'MESS_SERVER'   => $_POST['MESS_SERVER'],
			'MESS_PORT'     => $_POST['MESS_PORT'],
			'MESS_ACCOUNT'  => $_POST['MESS_ACCOUNT'],
			'MESS_PASSWORD' => $_POST['MESS_PASSWORD']
		));
		$oSpool->create(array(
			'msg_uid'          => '',
			'app_uid'          => '',
			'del_index'        => 0,
			'app_msg_type'     => 'TEST',
			'app_msg_subject'  => $sSubject,
			'app_msg_from'     => $sFrom,
			'app_msg_to'       => $_POST['TO'],
			'app_msg_body'     => $sBody,
			'app_msg_cc'       => '',
			'app_msg_bcc'      => '',
			'app_msg_attach'   => '',
			'app_msg_template' => '',
			'app_msg_status'   => 'pending'
		));
		$oSpool->sendMail();
		global $G_PUBLISH;
		$G_PUBLISH = new Publisher();
		if ($oSpool->status == 'sent') {
			$G_PUBLISH->AddContent('xmlform', 'xmlform', 'setup/emails_Sended', '', array('MESSAGE_VALUE' => G::LoadTranslation('ID_MESS_TEST_MESSAGE_SENDED')));
		}
		else {
			$G_PUBLISH->AddContent('xmlform', 'xmlform', 'setup/emails_Sended', '', array('MESSAGE_VALUE' => G::LoadTranslation('ID_MESS_TEST_MESSAGE_ERROR_PHP_MAIL') . $oSpool->error));
		}
		G::RenderPage('publish', 'raw');
	break;

	/**********************************************************************************/

	case 'mailTest_Show':
		$srv = $_POST['srv'];
		$port =  $_POST['port'];
		$account = $_POST['account'];
		$passwd = $_POST['passwd'];
		$auth_required	= $_POST['auth_required'];
		$send_test_mail = $_POST['send_test_mail'];
		$mail_to		= $_POST['mail_to'];
		$G_PUBLISH = new Publisher;
		$G_PUBLISH->AddContent('view', 'setup/mailConnectiontest');
		G::RenderPage('publish', 'raw');
	break;

	case 'testConnection':

		G::LoadClass('net');
		require_once('classes/class.smtp.rfc-821.php');

		define("SUCCESSFUL", 'SUCCESSFUL');
		define("FAILED", 'FAILED');

		//$host = 'smtp.bizmail.yahoo.com';
		$tld = ereg("([^//]*$)", $_POST['srv'], $regs);
    $srv1 = $regs[1];

		$srv	= $_POST['srv'];

		$port	= ($_POST['port'] == 'default')? 25: $_POST['port'];
		$user	= $_POST['account'];
		$passwd = $_POST['passwd'];
		$step	= $_POST['step'];
		$auth_required	= $_POST['auth_required'];
		$send_test_mail = $_POST['send_test_mail'];
		$mail_to		= $_POST['mail_to'];

		$Server = new NET($srv1);
		$oSMTP = new ESMTP;

		switch ($step) {
			case 1:
				if ($Server->getErrno() == 0) {
					print(SUCCESSFUL.',');
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
					print(SUCCESSFUL.',');
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
					print(SUCCESSFUL.','.$oSMTP->status);
				}
			break;

			#try login to host

			case 4:
				if($auth_required == 'yes'){
					if($port == 0){
						$resp = $oSMTP->Connect($srv);
					} else {
						$resp = $oSMTP->Connect($srv, $port);
					}
					if($resp) {
					  $oSMTP->do_debug = false;
						$oSMTP->Hello($srv);
						if( !$oSMTP->Authenticate($user, $passwd) ) {
							print(FAILED.','.$oSMTP->error['error']);
						} else {
							print(SUCCESSFUL.','.$oSMTP->status);
						}
					} else {
						print(FAILED.','.$oSMTP->error['error']);
					}
				} else {
					print(SUCCESSFUL.', No authentication required!');
				}
			break;

			case 5:
				if($send_test_mail == 'yes'){
					//print(SUCCESSFUL.',ok');
					$_POST['FROM_NAME'] = 'Process Maker O.S. [Test mail]';
					$_POST['FROM_EMAIL'] = $user;

					$_POST['MESS_ENGINE'] = 'PHPMAILER';
					$_POST['MESS_SERVER'] = $srv;
					$_POST['MESS_PORT']   = $port;
					$_POST['MESS_ACCOUNT'] = $user;
					$_POST['MESS_PASSWORD'] = $passwd;
					$_POST['TO'] = $mail_to;
					if($auth_required == 'yes'){
						$_POST['SMTPAuth'] = true;
					} else {
						$_POST['SMTPAuth'] = false;
					}
					$resp = sendTestMail();

					if($resp->status){
						print(SUCCESSFUL.','.$resp->msg);
					} else {
						print(FAILED.','.$resp->msg);
					}
				} else {
					print('jump this step');
				}
			break;

			default:
				print('test finished!');
		}
	break;
}

function sendTestMail() {

	$sFrom    = ($_POST['FROM_NAME'] != '' ? $_POST['FROM_NAME'] . ' ' : '') . '<' . $_POST['FROM_EMAIL'] . '>';
	$sSubject = G::LoadTranslation('ID_MESS_TEST_SUBJECT');
	$msg = G::LoadTranslation('ID_MESS_TEST_BODY');

	switch ($_POST['MESS_ENGINE']) {
		case 'MAIL':
			$engine = G::LoadTranslation('ID_MESS_ENGINE_TYPE_1');
		break;
		case 'PHPMAILER':
			$engine = G::LoadTranslation('ID_MESS_ENGINE_TYPE_2');
		break;
		case 'OPENMAIL':
			$engine = G::LoadTranslation('ID_MESS_ENGINE_TYPE_3');
		break;
	}

	$colosa_msg = "This Business Process is powered by <b>ProcessMaker</b>.";
	$sBody = "
	<table style=\"background-color: white; font-family: Arial,Helvetica,sans-serif; color: black; font-size: 11px; text-align: left;\" cellpadding='10' cellspacing='0' width='100%'>
	<tbody><tr><td><img id='logo' src='http://".$_SERVER['SERVER_NAME']."/images/processmaker.logo.jpg' /></td></tr>
	<tr><td style='font-size: 14px;'>$msg [".date('H:i:s')."] - $engine</td></tr>
	<tr><td style='vertical-align:middel;'>
	<br /><hr><b>This Business Process is powered by ProcessMaker.<b><br />
	<a href='http://www.processmaker.com' style='color:#c40000;'>www.processmaker.com</a><br /></td>
	</tr></tbody></table>";

	G::LoadClass('spool');
	$oSpool = new spoolRun();


	$oSpool->setConfig( array(
		'MESS_ENGINE'   => $_POST['MESS_ENGINE'],
		'MESS_SERVER'   => $_POST['MESS_SERVER'],
		'MESS_PORT'     => $_POST['MESS_PORT'],
		'MESS_ACCOUNT'  => $_POST['MESS_ACCOUNT'],
		'MESS_PASSWORD' => $_POST['MESS_PASSWORD'],
		'SMTPAuth'		=> $_POST['SMTPAuth']
	));

	$oSpool->create(array(
		'msg_uid'          => '',
		'app_uid'          => '',
		'del_index'        => 0,
		'app_msg_type'     => 'TEST',
		'app_msg_subject'  => $sSubject,
		'app_msg_from'     => $sFrom,
		'app_msg_to'       => $_POST['TO'],
		'app_msg_body'     => $sBody,
		'app_msg_cc'       => '',
		'app_msg_bcc'      => '',
		'app_msg_attach'   => '',
		'app_msg_template' => '',
		'app_msg_status'   => 'pending'
	));

	$oSpool->sendMail();

	global $G_PUBLISH;
	$G_PUBLISH = new Publisher();
	if ($oSpool->status == 'sent') {
		$o->status = true;
		$o->msg = G::LoadTranslation('ID_MESS_TEST_MESSAGE_SENDED');
	}
	else {
		$o->status = false;
		$o->msg = $oSpool->error;
	}
	return $o;
}


function e_utf8_encode($input) {
	$utftext = null;

	for ($n = 0; $n < strlen($input); $n++) {

		$c = ord($input[$n]);

		if ($c < 128) {
			$utftext .= chr($c);
		} else if (($c > 128) && ($c < 2048)) {
			$utftext .= chr(($c >> 6) | 192);
			$utftext .= chr(($c & 63) | 128);
		} else {
			$utftext .= chr(($c >> 12) | 224);
			$utftext .= chr((($c & 6) & 63) | 128);
			$utftext .= chr(($c & 63) | 128);
		}
	}

	return $utftext;
}