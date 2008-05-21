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

switch ($_POST['action']) {
  case 'testEmailConfiguration':
    global $G_PUBLISH;
  	$G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'setup/emails_TestForm');
    G::RenderPage('publish', 'raw');
  break;
  case 'sendTestMail':
    $sFrom    = ($_POST['FROM_NAME'] != '' ? '"' . $_POST['FROM_NAME'] . '" ' : '"') . '<' . $_POST['FROM_EMAIL'] . '>';
    $sSubject = G::LoadTranslation('ID_MESS_TEST_SUBJECT');
    $sBody    = G::LoadTranslation('ID_MESS_TEST_BODY') . ' "';
    switch ($_POST['MESS_ENGINE']) {
      case 'MAIL':
        $sBody .= G::LoadTranslation('ID_MESS_ENGINE_TYPE_1');
      break;
      case 'PHPMAILER':
        $sBody .= G::LoadTranslation('ID_MESS_ENGINE_TYPE_2');
      break;
      case 'OPENMAIL':
        $sBody .= G::LoadTranslation('ID_MESS_ENGINE_TYPE_3');
      break;
    }
    $sBody .= '" (' . date('H:i:s') . ').';
    G::LoadClass('insert');
    $oInsert = new insert();
    $sUID    = $oInsert->db_insert(array('msg_uid'          => '',
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
                                         'app_msg_status'   => 'pending'));
    switch ($_POST['MESS_ENGINE']) {
      case 'MAIL':
        G::LoadThirdParty('phpmailer', 'class.phpmailer');
        $oPHPMailer = new PHPMailer();
        $oPHPMailer->Mailer   = 'mail';
        $oPHPMailer->From     = $_POST['FROM_EMAIL'];
        $oPHPMailer->FromName = $_POST['FROM_NAME'];
        $oPHPMailer->Subject  = $sSubject;
        $oPHPMailer->Body     = $sBody;
        $oPHPMailer->AddAddress($_POST['TO']);
        global $G_PUBLISH;
  	    $G_PUBLISH = new Publisher();
        if ($oPHPMailer->Send()) {
          $G_PUBLISH->AddContent('xmlform', 'xmlform', 'setup/emails_Sended', '', array('MESSAGE_VALUE' => G::LoadTranslation('ID_MESS_TEST_MESSAGE_SENDED')));
          $sStatus = 'sent';
        }
        else {
          $G_PUBLISH->AddContent('xmlform', 'xmlform', 'setup/emails_Sended', '', array('MESSAGE_VALUE' => G::LoadTranslation('ID_MESS_TEST_MESSAGE_ERROR_PHP_MAIL') . $oPHPMailer->ErrorInfo));
          $sStatus = 'failed';
        }
        G::RenderPage('publish', 'raw');
      break;
      case 'PHPMAILER':
        G::LoadThirdParty('phpmailer', 'class.phpmailer');
        $oPHPMailer = new PHPMailer();
        $oPHPMailer->Mailer   = 'smtp';
        $oPHPMailer->SMTPAuth = true;
        $oPHPMailer->Host     = $_POST['MESS_SERVER'];
        $oPHPMailer->Port     = $_POST['MESS_PORT'];
        $oPHPMailer->Username = $_POST['MESS_ACCOUNT'];
        $oPHPMailer->Password = $_POST['MESS_PASSWORD'];
        $oPHPMailer->From     = $_POST['FROM_EMAIL'];
        $oPHPMailer->FromName = $_POST['FROM_NAME'];
        $oPHPMailer->Subject  = $sSubject;
        $oPHPMailer->Body     = $sBody;
        $oPHPMailer->AddAddress($_POST['TO']);
        $oPHPMailer->IsHTML(true);
        global $G_PUBLISH;
  	    $G_PUBLISH = new Publisher();
        if ($oPHPMailer->Send()) {
          $G_PUBLISH->AddContent('xmlform', 'xmlform', 'setup/emails_Sended', '', array('MESSAGE_VALUE' => G::LoadTranslation('ID_MESS_TEST_MESSAGE_SENDED')));
          $sStatus = 'sent';
        }
        else {
          $G_PUBLISH->AddContent('xmlform', 'xmlform', 'setup/emails_Sended', '', array('MESSAGE_VALUE' => G::LoadTranslation('ID_MESS_TEST_MESSAGE_ERROR_PHP_MAIL') . $oPHPMailer->ErrorInfo));
          $sStatus = 'failed';
        }
        G::RenderPage('publish', 'raw');
      break;
      case 'OPENMAIL':
        G::LoadClass('spool');
        $oSpool = new spoolRun();
        $oSpool->setConfig($_POST['MESS_SERVER'], $_POST['MESS_PORT']);
        $oSpool->setData($sUID, $sSubject, $sFrom, $_POST['TO'], $sBody);
        $oSpool->sendMail();
        global $G_PUBLISH;
  	    $G_PUBLISH = new Publisher();
        if ($oSpool->status == 'sent') {
          $G_PUBLISH->AddContent('xmlform', 'xmlform', 'setup/emails_Sended', '', array('MESSAGE_VALUE' => G::LoadTranslation('ID_MESS_TEST_MESSAGE_SENDED')));
          $sStatus = 'sent';
        }
        else {
          $G_PUBLISH->AddContent('xmlform', 'xmlform', 'setup/emails_Sended', '', array('MESSAGE_VALUE' => G::LoadTranslation('ID_MESS_TEST_MESSAGE_ERROR_PHP_MAIL') . $oSpool->error));
          $sStatus = 'failed';
        }
        G::RenderPage('publish', 'raw');
      break;
    }
    $oAppMessage = AppMessagePeer::retrieveByPK($sUID);
		$oAppMessage->setappMsgstatus($sStatus);
		$oAppMessage->save();
  break;
}