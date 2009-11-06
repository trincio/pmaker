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

require_once ('classes/model/AppMessage.php');

class spoolRun {
	private $config;
	private $fileData;
	private $spool_id;
	public $status;
	public $error;

	function __construct() {
		$this->config = array();
		$this->fileData = array();
		$this->spool_id = '';
		$this->status = 'pending';
		$this->error = '';

		//$this->getSpoolFilesList();
	}

	public function getSpoolFilesList() {
		$sql = "SELECT * FROM APP_MESSAGE WHERE APP_MSG_STATUS ='pending'";

		$con = Propel::getConnection("workflow");
		$stmt = $con->prepareStatement($sql);
		$rs = $stmt->executeQuery();

		while($rs->next()) {
			$this->spool_id = $rs->getString('APP_MSG_UID');
			$this->fileData['subject'] = $rs->getString('APP_MSG_SUBJECT');
			$this->fileData['from'] = $rs->getString('APP_MSG_FROM');
			$this->fileData['to'] = $rs->getString('APP_MSG_TO');
			$this->fileData['body'] = $rs->getString('APP_MSG_BODY');
			$this->fileData['date'] = $rs->getString('APP_MSG_DATE');
			$this->fileData['cc'] = $rs->getString('APP_MSG_CC');
			$this->fileData['bcc'] = $rs->getString('APP_MSG_BCC');
			$this->fileData['template'] = $rs->getString('APP_MSG_TEMPLATE');
			$this->fileData['attachments'] = array(); //$rs->getString('APP_MSG_ATTACH');
			if($this->config['MESS_ENGINE'] == 'OPENMAIL') {
				if($this->config['MESS_SERVER'] != '') {
					if(($sAux = @gethostbyaddr($this->config['MESS_SERVER']))) {
						$this->fileData['domain'] = $sAux;
					} else {
						$this->fileData['domain'] = $this->config['MESS_SERVER'];
					}
				} else {
					$this->fileData['domain'] = gethostbyaddr('127.0.0.1');
				}
			}
			$this->sendMail();
		}
	}

	public function create($aData) {
		G::LoadClass('insert');
		$oInsert = new insert();
		$sUID = $oInsert->db_insert($aData);

		$aData['app_msg_date'] = isset($aData['app_msg_date']) ? $aData['app_msg_date'] : '';

		if(isset($aData['app_msg_status'])) {
			$this->status = strtolower($aData['app_msg_status']);
		}

		$this->setData($sUID, $aData['app_msg_subject'], $aData['app_msg_from'], $aData['app_msg_to'], $aData['app_msg_body'], $aData['app_msg_date'], $aData['app_msg_cc'], $aData['app_msg_bcc'], $aData['app_msg_template']);
	}

	public function setConfig($aConfig) {
		$this->config = $aConfig;
	}

	public function setData($sAppMsgUid, $sSubject, $sFrom, $sTo, $sBody, $sDate = '', $sCC = '', $sBCC = '', $sTemplate = '') {
		$this->spool_id = $sAppMsgUid;
		$this->fileData['subject'] = $sSubject;
		$this->fileData['from'] = $sFrom;
		$this->fileData['to'] = $sTo;
		$this->fileData['body'] = $sBody;
		$this->fileData['date'] = ($sDate != '' ? $sDate : date('Y-m-d H:i:s'));
		$this->fileData['cc'] = $sCC;
		$this->fileData['bcc'] = $sBCC;
		$this->fileData['template'] = $sTemplate;
		$this->fileData['attachments'] = array();

		if($this->config['MESS_ENGINE'] == 'OPENMAIL') {
			if($this->config['MESS_SERVER'] != '') {
				if(($sAux = @gethostbyaddr($this->config['MESS_SERVER']))) {
					$this->fileData['domain'] = $sAux;
				} else {
					$this->fileData['domain'] = $this->config['MESS_SERVER'];
				}
			} else {
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

	private function updateSpoolStatus() {
		$oAppMessage = AppMessagePeer::retrieveByPK($this->spool_id);
		$oAppMessage->setappMsgstatus($this->status);
		$oAppMessage->setappMsgsenddate(date('Y-m-d H:i:s'));
		$oAppMessage->save();

	}


	/**
	 * Improved for recipients with <name> email@domain.com
	 * By Neyek
	 */
	private function handleFrom() {
		if( strpos($this->fileData['from'], '<') !== false ) {
			preg_match('/([\"\w@\.-_\s]*\s*)?(<(\w+[\.-]?\w+]*@\w+([\.-]?\w+)*\.\w{2,3})+>)/', $this->fileData['from'], $matches);
            
			$this->fileData['from_name'] = trim(str_replace('"', '', $matches[1]));
			$this->fileData['from_email'] = trim($matches[3]);
            
		} else {
			
			$this->fileData['from_name'] = 'Processmaker Web boot';
			$this->fileData['from_email'] = $this->fileData['from'];
		}

	}

	private function handleEnvelopeTo() {
		$hold = array();
		$text = trim($this->fileData['to']);
		if(isset($this->fileData['cc']) && trim($this->fileData['cc']) != '') {
			$text .= ',' . trim($this->fileData['cc']);
		}

		if(isset($this->fileData['bcc']) && trim($this->fileData['bcc']) != '') {
			$text .= ',' . trim($this->fileData['bcc']);
		}

		if(false !== (strpos($text, ','))) {
			$hold = explode(',', $text);

			foreach($hold as $val) {
				if(strlen($val) > 0) {
					$this->fileData['envelope_to'][] = "$val";
				}
			}
		} else {
			$this->fileData['envelope_to'][] = "$text";
		}
	}

	private function handleMail() {
		if(count($this->fileData['envelope_to']) > 0) {
			switch($this->config['MESS_ENGINE']) {
				case 'MAIL':
					G::LoadThirdParty('phpmailer', 'class.phpmailer');
					$oPHPMailer = new PHPMailer();
					$oPHPMailer->Mailer = 'mail';
					$oPHPMailer->From = $this->fileData['from_email'];
					$oPHPMailer->FromName = $this->fileData['from_name'];
					$oPHPMailer->Subject = $this->fileData['subject'];
					$oPHPMailer->Body = $this->fileData['body'];
						
					foreach($this->fileData['envelope_to'] as $sEmail) {
						if(strpos($this->fileData['to'], '<') !== false) {
                            preg_match('/([\"\w@\.-_\s]*\s*)?(<(\w+[\.-]?\w+]*@\w+([\.-]?\w+)*\.\w{2,3})+>)/', $sEmail, $matches);
							$sTo = trim($matches[3]);
							$sToName = trim($matches[1]);

							$oPHPMailer->AddAddress($sTo, $sToName);
						} else {
							$oPHPMailer->AddAddress($sEmail);
						}
					}
						
					$oPHPMailer->IsHTML(true);
					if($oPHPMailer->Send()) {
						$this->error = '';
						$this->status = 'sent';
					} else {
						$this->error = $oPHPMailer->ErrorInfo;
						$this->status = 'failed';
					}
					break;
				case 'PHPMAILER':
					G::LoadThirdParty('phpmailer', 'class.phpmailer');
					$oPHPMailer = new PHPMailer();
					$oPHPMailer->Mailer = 'smtp';
					$oPHPMailer->SMTPAuth = (isset($this->config['SMTPAuth']) ? $this->config['SMTPAuth'] : '');
					$oPHPMailer->Host = $this->config['MESS_SERVER'];
					$oPHPMailer->Port = $this->config['MESS_PORT'];
					$oPHPMailer->Username = $this->config['MESS_ACCOUNT'];
					$oPHPMailer->Password = $this->config['MESS_PASSWORD'];
					$oPHPMailer->From = $this->fileData['from_email'];
					$oPHPMailer->FromName = utf8_decode($this->fileData['from_name']);
					$oPHPMailer->Subject = utf8_decode($this->fileData['subject']);
					$oPHPMailer->Body = utf8_decode($this->fileData['body']);
				    
					foreach($this->fileData['envelope_to'] as $sEmail) {
						if(strpos($sEmail, '<') !== false) {
                            preg_match('/([\"\w@\.-_\s]*\s*)?(<(\w+[\.-]?\w+]*@\w+([\.-]?\w+)*\.\w{2,3})+>)/', $sEmail, $matches);
							$sTo = trim($matches[3]);
							$sToName = trim($matches[1]);
							$oPHPMailer->AddAddress($sTo, $sToName);
						} else {
							$oPHPMailer->AddAddress($sEmail);
						}
					}
						
					$oPHPMailer->IsHTML(true);
					if($oPHPMailer->Send()) {
						$this->error = '';
						$this->status = 'sent';
					} else {
						$this->error = $oPHPMailer->ErrorInfo;
						$this->status = 'failed';
					}
					break;
				case 'OPENMAIL':
					G::LoadClass('package');
					G::LoadClass('smtp');
					$pack = new package($this->fileData);
					$header = $pack->returnHeader();
					$body = $pack->returnBody();
					$send = new smtp();
					$send->setServer($this->config['MESS_SERVER']);
					$send->setPort($this->config['MESS_PORT']);
					$send->setUsername($this->config['MESS_ACCOUNT']);
					$send->setPassword($this->config['MESS_PASSWORD']);
					$send->setReturnPath($this->fileData['from_email']);
					$send->setHeaders($header);
					$send->setBody($body);
					$send->setEnvelopeTo($this->fileData['envelope_to']);
					if($send->sendMessage()) {
						$this->error = '';
						$this->status = 'sent';
					} else {
						$this->error = implode(', ', $send->returnErrors());
						$this->status = 'failed';
					}
					break;
			}
		}
	}

	function resendEmails() {
		try {
			require_once 'classes/model/Configuration.php';
			$oConfiguration = new Configuration();
			$sDelimiter = DBAdapter::getStringDelimiter();
			$oCriteria = new Criteria('workflow');
			$oCriteria->add(ConfigurationPeer::CFG_UID, 'Emails');
			$oCriteria->add(ConfigurationPeer::OBJ_UID, '');
			$oCriteria->add(ConfigurationPeer::PRO_UID, '');
			$oCriteria->add(ConfigurationPeer::USR_UID, '');
			$oCriteria->add(ConfigurationPeer::APP_UID, '');
			$aConfiguration = $oConfiguration->load('Emails', '', '', '', '');
			$aConfiguration = unserialize($aConfiguration['CFG_VALUE']);
			if($aConfiguration['MESS_ENABLED'] == '1') {
				$this->setConfig(array('MESS_ENGINE'=>$aConfiguration['MESS_ENGINE'], 'MESS_SERVER'=>$aConfiguration['MESS_SERVER'], 'MESS_PORT'=>$aConfiguration['MESS_PORT'], 'MESS_ACCOUNT'=>$aConfiguration['MESS_ACCOUNT'], 'MESS_PASSWORD'=>$aConfiguration['MESS_PASSWORD']));
				require_once 'classes/model/AppMessage.php';
				$oCriteria = new Criteria('workflow');
				$oCriteria->add(AppMessagePeer::APP_MSG_STATUS, 'sent', Criteria::NOT_EQUAL);
				$oDataset = AppMessagePeer::doSelectRS($oCriteria);
				$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
				$oDataset->next();
				while($aRow = $oDataset->getRow()) {
					$this->setData($aRow['APP_MSG_UID'], $aRow['APP_MSG_SUBJECT'], $aRow['APP_MSG_FROM'], $aRow['APP_MSG_TO'], $aRow['APP_MSG_BODY']);
					$this->sendMail();
					$oDataset->next();
				}
			}
		} catch ( Exception $oError ) {
			//CONTINUE
		}
	}
} // end of class
?>