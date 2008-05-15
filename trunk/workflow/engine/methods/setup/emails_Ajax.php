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
    switch ($_POST['MESS_ENGINE']) {
      case 'MAIL':
        G::LoadThirdParty('phpmailer', 'class.phpmailer');
        $oPHPMailer = new PHPMailer();
        $oPHPMailer->Mailer   = 'mail';
        $oPHPMailer->From     = $_POST['FROM_EMAIL'];
        $oPHPMailer->FromName = $_POST['FROM_NAME'];
        $oPHPMailer->Subject  = G::LoadTranslation('ID_MESS_TEST_SUBJECT');
        $oPHPMailer->Body     = G::LoadTranslation('ID_MESS_TEST_BODY') . ' "' . G::LoadTranslation('ID_MESS_ENGINE_TYPE_1') . '".';
        $oPHPMailer->AddAddress($_POST['TO']);
        global $G_PUBLISH;
  	    $G_PUBLISH = new Publisher();
        if ($oPHPMailer->Send()) {
          $G_PUBLISH->AddContent('xmlform', 'xmlform', 'setup/emails_Sended', '', array('MESSAGE_VALUE' => G::LoadTranslation('ID_MESS_TEST_MESSAGE_SENDED')));
        }
        else {
          $G_PUBLISH->AddContent('xmlform', 'xmlform', 'setup/emails_Sended', '', array('MESSAGE_VALUE' => G::LoadTranslation('ID_MESS_TEST_MESSAGE_ERROR_PHP_MAIL') . $oPHPMailer->ErrorInfo));
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
        $oPHPMailer->Subject  = G::LoadTranslation('ID_MESS_TEST_SUBJECT');
        $oPHPMailer->Body     = G::LoadTranslation('ID_MESS_TEST_BODY') . ' "' . G::LoadTranslation('ID_MESS_ENGINE_TYPE_2') . '".';
        $oPHPMailer->AddAddress($_POST['TO']);
        $oPHPMailer->IsHTML(true);
        global $G_PUBLISH;
  	    $G_PUBLISH = new Publisher();
        if ($oPHPMailer->Send()) {
          $G_PUBLISH->AddContent('xmlform', 'xmlform', 'setup/emails_Sended', '', array('MESSAGE_VALUE' => G::LoadTranslation('ID_MESS_TEST_MESSAGE_SENDED')));
        }
        else {
          $G_PUBLISH->AddContent('xmlform', 'xmlform', 'setup/emails_Sended', '', array('MESSAGE_VALUE' => G::LoadTranslation('ID_MESS_TEST_MESSAGE_ERROR_PHP_MAIL') . $oPHPMailer->ErrorInfo));
        }
        G::RenderPage('publish', 'raw');
      break;
      case 'OPENMAIL':
        //
      break;
    }
  break;
}