<?php

/**
 * @brief send email from the spool database, and
 * see if we have all the addresses we send to.
 *
 * @package Tomahawk_Mail
 * @author Ian K Armstrong <ika@[REMOVE_THESE_CAPITALS]openmail.cc>
 * @copyright Copyright (c) 2007, Ian K Armstrong
 * @license http://www.opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link http://www.openmail.cc
 *
 * @category web_mail
 * @subpackage utilities
 * @filesource
 * @version
 *
 * @file class.spool.php
 *
 */


	require_once('classes/model/AppMessage.php');

	class spoolRun
	{
		private $config;
		private $fileData;
		private $spool_id;
		public  $status;
		public  $error;

		function __construct()
		{
			$this->config   = array();
			$this->fileData = array();
			$this->spool_id = '';
			$this->status   = 'pending';
			$this->error    = '';

			//$this->getSpoolFilesList();
		}

		public function getSpoolFilesList()
		{
			$sql = "SELECT * FROM APP_MESSAGE WHERE APP_MSG_STATUS ='pending'";

			$con = Propel::getConnection("workflow");
			$stmt = $con->prepareStatement($sql);
			$rs = $stmt->executeQuery();

			while($rs->next())
			{
				$this->spool_id 		           = $rs->getString('APP_MSG_UID');
				$this->fileData['subject']     = $rs->getString('APP_MSG_SUBJECT');
				$this->fileData['from'] 	     = $rs->getString('APP_MSG_FROM');
				$this->fileData['to'] 		     = $rs->getString('APP_MSG_TO');
				$this->fileData['body'] 	     = $rs->getString('APP_MSG_BODY');
				$this->fileData['date'] 	     = $rs->getString('APP_MSG_DATE');
				$this->fileData['cc'] 		     = $rs->getString('APP_MSG_CC');
				$this->fileData['bcc'] 		     = $rs->getString('APP_MSG_BCC');
				$this->fileData['template'] 	 = $rs->getString('APP_MSG_TEMPLATE');
				$this->fileData['attachments'] = array(); //$rs->getString('APP_MSG_ATTACH');
				if ($this->config['MESS_ENGINE'] == 'OPENMAIL') {
			    if ($this->config['MESS_SERVER'] != '') {
			      if (($sAux = @gethostbyaddr($this->config['MESS_SERVER']))) {
              $this->fileData['domain'] = $sAux;
            }
            else {
              $this->fileData['domain'] = $this->config['MESS_SERVER'];
            }
          }
          else {
            $this->fileData['domain'] = gethostbyaddr('127.0.0.1');
          }
			  }
				$this->sendMail();
			}
		}

		public function create($aData) {
		  G::LoadClass('insert');
      $oInsert = new insert();
      $sUID    = $oInsert->db_insert($aData);
      $this->setData($sUID, $aData['app_msg_subject'], $aData['app_msg_from'], $aData['app_msg_to'], $aData['app_msg_body']);
		}

		public function setConfig($aConfig) {
		  $this->config = $aConfig;
		}

		public function setData($sAppMsgUid, $sSubject, $sFrom, $sTo, $sBody, $sDate = '', $sCC = '', $sBCC = '', $sTemplate = '') {
		  $this->spool_id 		           = $sAppMsgUid;
			$this->fileData['subject']     = $sSubject;
			$this->fileData['from'] 	     = $sFrom;
			$this->fileData['to'] 		     = $sTo;
			$this->fileData['body'] 	     = $sBody;
			$this->fileData['date'] 	     = ($sDate != '' ? $sDate : date('Y-m-d H:i:s'));
			$this->fileData['cc'] 		     = $sCC;
			$this->fileData['bcc'] 		     = $sBCC;
			$this->fileData['template'] 	 = $sTemplate;
			$this->fileData['attachments'] = array();
			if ($this->config['MESS_ENGINE'] == 'OPENMAIL') {
			  if ($this->config['MESS_SERVER'] != '') {
			    if (($sAux = @gethostbyaddr($this->config['MESS_SERVER']))) {
            $this->fileData['domain'] = $sAux;
          }
          else {
            $this->fileData['domain'] = $this->config['MESS_SERVER'];
          }
        }
        else {
          $this->fileData['domain'] = gethostbyaddr('127.0.0.1');
        }
			}
		}

		public function sendMail() {
		  $this->handleFrom();
			$this->handleEnvelopeTo();
			$this->handleMail();
		  $this->updateSpoolStatus();
		}

		private function updateSpoolStatus()
		{
			$oAppMessage = AppMessagePeer::retrieveByPK($this->spool_id);
			$oAppMessage->setappMsgstatus($this->status);
			$oAppMessage->save();

		}

		private function handleFrom()
		{
			if(false !== ($pos = strpos($this->fileData['from'],'<')))
			{
				$this->fileData['from_name']  = trim(substr($this->fileData['from'],0,$pos));
				$this->fileData['from_email'] = trim(substr($this->fileData['from'],$pos));
        $this->fileData['from_email'] = str_replace('<', '', str_replace('>', '', $this->fileData['from_email']));
			}
			else {
			  $this->fileData['from']       = '<' . $this->fileData['from'] . '>';
			  $this->fileData['from_name']  = '';
				$this->fileData['from_email'] = str_replace('<', '', str_replace('>', '', $this->fileData['from']));
			}

		}

		private function handleEnvelopeTo()
		{
			$hold = array();

			$text  = '';
			$text .= trim($this->fileData['to']);
			$text .= ',' . trim($this->fileData['cc']);
			$text .= ',' . trim($this->fileData['bcc']);

			if(false !== (strpos($text,',')))
			{
				$hold = explode(',',$text);

				foreach($hold as $val)
					if(strlen($val)>0) $this->fileData['envelope_to'][] = "$val";


			}
			else
			{
				$this->fileData['envelope_to'][] = "$text";

			}

		}

		private function handleMail()
		{
			if(count($this->fileData['envelope_to'])>0)
			{
        switch ($this->config['MESS_ENGINE']) {
          case 'MAIL':
            G::LoadThirdParty('phpmailer', 'class.phpmailer');
            $oPHPMailer = new PHPMailer();
            $oPHPMailer->Mailer   = 'mail';
            $oPHPMailer->From     = $this->fileData['from_email'];
            $oPHPMailer->FromName = $this->fileData['from_name'];
            $oPHPMailer->Subject  = $this->fileData['subject'];
            $oPHPMailer->Body     = $this->fileData['body'];
            if (strpos($this->fileData['to'], '<') !== false) {
              $aTo     = explode('<', $this->fileData['to']);
              $sToName = trim($aTo[0]);
              $sTo     = trim(str_replace('>', '', $aTo[1]));
              $oPHPMailer->AddAddress($sTo, $sToName);
            }
            else {
              $oPHPMailer->AddAddress($this->fileData['to']);
            }
            if ($oPHPMailer->Send()) {
              $this->error = '';
				      $this->status = 'sent';
            }
            else {
              $this->error = $oPHPMailer->ErrorInfo;
				      $this->status = 'failed';
            }
          break;
          case 'PHPMAILER':
            G::LoadThirdParty('phpmailer', 'class.phpmailer');
            $oPHPMailer = new PHPMailer();
            $oPHPMailer->Mailer   = 'smtp';
            $oPHPMailer->SMTPAuth = (isset($this->config['SMTPAuth']) ? $this->config['SMTPAuth'] : '');
            $oPHPMailer->Host     = $this->config['MESS_SERVER'];
            $oPHPMailer->Port     = $this->config['MESS_PORT'];
            $oPHPMailer->Username = $this->config['MESS_ACCOUNT'];
            $oPHPMailer->Password = $this->config['MESS_PASSWORD'];
            $oPHPMailer->From     = $this->fileData['from_email'];
            $oPHPMailer->FromName = $this->fileData['from_name'];
            $oPHPMailer->Subject  = $this->fileData['subject'];
            $oPHPMailer->Body     = $this->fileData['body'];
            if (strpos($this->fileData['to'], '<') !== false) {
              $aTo     = explode('<', $this->fileData['to']);
              $sToName = trim($aTo[0]);
              $sTo     = trim(str_replace('>', '', $aTo[1]));
              $oPHPMailer->AddAddress($sTo, $sToName);
            }
            else {
              $oPHPMailer->AddAddress($this->fileData['to']);
            }
            $oPHPMailer->IsHTML(true);
            if ($oPHPMailer->Send()) {
              $this->error = '';
				      $this->status = 'sent';
            }
            else {
              $this->error = $oPHPMailer->ErrorInfo;
				      $this->status = 'failed';
            }
          break;
          case 'OPENMAIL':
            G::LoadClass('package');
            G::LoadClass('smtp');
				    $pack = new package($this->fileData);
				    $header = $pack->returnHeader();
				    $body   = $pack->returnBody();
				    $send = new smtp();
				    $send->setServer($this->config['MESS_SERVER']);
				    $send->setPort($this->config['MESS_PORT']);
				    $send->setUsername($this->config['MESS_ACCOUNT']);
				    $send->setPassword($this->config['MESS_PASSWORD']);
				    $send->setReturnPath($this->fileData['from_email']);
				    $send->setHeaders($header);
				    $send->setBody($body);
				    $send->setEnvelopeTo($this->fileData['envelope_to']);
				    if ($send->sendMessage()) {
				      $this->error = '';
				      $this->status = 'sent';
				    }
				    else {
				      $this->error = implode(', ', $send->returnErrors());
				      $this->status = 'failed';
				    }
          break;
        }
			}
		}
	} // end of class
?>
