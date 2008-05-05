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
	
		private $fileData;
		private $fileField;
		private $spool_id;
	
		function __construct() 
		{
			$this->fileData  = array();
			$this->spool_id  = '';
			
			$this->getSpoolFilesList();

		}

		private function getSpoolFilesList() 
		{		
			$sql = "SELECT * FROM APP_MESSAGE WHERE APP_MSG_STATUS ='pending'";

			$con = Propel::getConnection("workflow");
			$stmt = $con->prepareStatement($sql);
			$rs = $stmt->executeQuery();

			while($rs->next())
			{
				$this->spool_id 		= $rs->getInt('APP_MSG_UID');
				$this->fileData['subject'] 	= $rs->getString('APP_MSG_SUBJECT');
				$this->fileData['from'] 	= $rs->getString('APP_MSG_FROM');
				$this->fileData['to'] 		= $rs->getString('APP_MSG_TO');
				$this->fileData['body'] 	= $rs->getString('APP_MSG_BODY');
				$this->fileData['date'] 	= $rs->getString('APP_MSG_DATE');
				$this->fileData['cc'] 		= $rs->getString('APP_MSG_CC');
				$this->fileData['bcc'] 		= $rs->getString('APP_MSG_BCC');
				$this->fileData['template'] 	= $rs->getString('APP_MSG_TEMPLATE');
				$this->fileData['attachments'] 	= array(); //$rs->getString('APP_MSG_ATTACH');
				$this->fileData['domain'] 	= 'processmaker.com';

				$this->handleFrom();
				$this->handleEnvelopeTo();
				$this->handleMail();
				$this->updateSpoolStatus();

			}

		}
		
		private function updateSpoolStatus() 
		{		
			(false === $this->status)
				? $s = 'failed'
				: $s = 'sent';

			$spool = AppMessagePeer::retrieveByPK($this->spool_id);
			$spool->setappMsgstatus($s);
			$spool->save();
		
		}

		private function handleFrom()
		{
			if(false !== ($pos = strpos($this->fileData['from'],'<')))
			{
				$this->fileData['from_name']  = trim(substr($this->fileData['from'],0,$pos));
				$this->fileData['from_email'] = trim(substr($this->fileData['from'],$pos));

			}

		}

		private function handleEnvelopeTo() 
		{
			$hold = array();
			$this->fileData['envelope_to'] = array();

			$text  = '';
			$text .= trim($this->fileData['to']);
			$text .= ',' . trim($this->fileData['cc']);
			$text .= ',' . trim($this->fileData['bcc']);

			if(false !== (strpos($text,',')))
			{
				$hold = explode(',',$text);
			
				for($i = 0; $i < count($hold); $i++)
					if(strlen($hold[$i])>0) $this->fileData['envelope_to'][$i] = "{$hold[$i]}";
					
			} 
			else 
			{
				$this->fileData['envelope_to']['0'] = "$text";
			}
		
		}

		private function handleMail() 
		{
			if(count($this->fileData['envelope_to'])>0) 
			{
				$pack = new package($this->fileData);
				$header = $pack->returnHeader();
				$body   = $pack->returnBody();
				unset($pack);

				$return_path = '<'."{$this->fileData['from_email']}".'>';

				$send = new smtp($return_path, $this->fileData['envelope_to'], $header, $body);

				$this->status = $send->returnStatus();

				unset($send);

			}

		}

			
	
	
	} // end of class



?>
