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

	
	class spoolRun
	{
	
		private $fileData;
		private $fileField;
		private $spool_id;
		private $message;
	
		function __construct() 
		{
			Propel::init("projects/spool/build/conf/APP_SPOOL-conf.php");

			$this->fileData  = array();
			$this->fileField = '';
			$this->spool_id  = '';
			$this->message = array();
			
			$this->getSpoolFilesList();
		
		}

		public function returnMessages()
		{
			return $this->message;

		}
	
		private function getSpoolFilesList() 
		{		
			$sql = "SELECT id,sender,file FROM APP_SPOOL WHERE status='pending'";

			$con = Propel::getConnection("APP_SPOOL");

			$stmt = $con->prepare($sql);
			$stmt->execute();

			$result = AppSpoolPeer::populateObjects($stmt);

			for($i = 0, $j = count($result); $i < $j; $i++)
			{
				$this->spool_id = $result[$i]->getId();
				$this->fileField = $result[$i]->getFile();
				$this->base64Decode();
				$this->handleEnvelopeTo();
				$this->handleMail();

			}

		}
		
		private function base64Decode() 
		{	
			$fields = array('envelope_to','subject','body','from_name','domain');

			$b1 = 'A480fa0ba807b4A';
			$b2 = 'B480fa0ba70db4B';

			$split = explode($b2,$this->fileField);
			
			foreach($split as $val) 
			{
				list($k,$v) = explode($b1,$val);

				(in_array($k,$fields)) 
					? $this->fileData[$k] = base64_decode($v)
					: $this->fileData[$k] = $v;
				 
			}	
		
		}
		
		private function deleteFromSpool() 
		{		
			if(trim($this->spool_id)!='') 
			{				
				$spool = AppSpoolPeer::retrieveByPK($this->spool_id);
				$spool->delete();
				
			}
		
		}
		
		private function updateSpoolStatus() 
		{		
			if(trim($this->spool_id)!='') 
			{				
				//$sql = "UPDATE spool SET status='sent' WHERE id='$this->spool_id'";
				
				$spool = AppSpoolPeer::retrieveByPK($this->spool_id);
				$spool ->setStatus('sent');
				if($spool->validate())
				{
					$spool>save();
				}
				else
				{
					$this->message[] = 'spool status not updated';
				}
				
			}
			else
			{
				$this->message[] = 'no spool_id';
			}
		
		}
		
		private function handleEnvelopeTo() 
		{
			$text = trim($this->fileData['envelope_to']);
			$this->fileData['envelope_to'] = array();
			$hold = array();
			
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

				$status = $send->returnStatus();
				$this->message[] = $send->returnErrors();
				unset($send);

				if($status) 
				{
					$this->updateSpoolStatus();
					$this->message[] = 'mail sent';

				}

			}

		}

			
	
	
	} // end of class



?>
