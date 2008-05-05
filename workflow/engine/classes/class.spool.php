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

	
	require_once('classes/model/AppSpool.php');

	class spoolRun
	{
	
		private $fileData;
		private $fileField;
		private $spool_id;
	
		function __construct() 
		{
			$this->fileData  = array();
			$this->fileField = '';
			$this->spool_id  = '';
			
			$this->getSpoolFilesList();

		}

		private function getSpoolFilesList() 
		{		
			$sql = "SELECT * FROM APP_MESSAGE WHERE status='pending'";

			$con = Propel::getConnection("workflow");
			$stmt = $con->prepareStatement($sql);
			$rs = $stmt->executeQuery();

			while($rs->next())
			{
				$this->spool_id = $rs->getInt('id');
				$this->fileField = $rs->getString('file');
				//$this->base64Decode();
				$this->handleEnvelopeTo();
				$this->handleMail();
				//$this->updateSpoolStatus();

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
		
		private function updateSpoolStatus() 
		{		
			(false === $this->status)
				? $s = 'failed'
				: $s = 'sent';

			$spool = AppSpoolPeer::retrieveByPK($this->spool_id);
			$spool->setStatus($s);
			$spool->save();
		
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

				$this->status = $send->returnStatus();

				unset($send);

			}

		}

			
	
	
	} // end of class



?>
