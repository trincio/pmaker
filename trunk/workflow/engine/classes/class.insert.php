<?php

/**
 * @brief insert mail into the spool database
 *
 * @package Tomahawk_Mail
 * @author Ian K Armstrong <ika@[REMOVE_THESE_CAPITALS]openmail.cc>
 * @copyright Copyright (c) 2007, Ian K Armstrong
 * @license http://www.opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link http://www.openmail.cc
 *
 * @category web_mail
 * @subpackage send
 * @filesource
 * @version
 *
 * @file class.insert.php
 *
 */
 
	require_once ( "classes/model/AppMessage.php" );

	class insert
	{
		private $db_spool;
		private $status;
	
		function __construct($db_spool=array())
		{
			if(count($db_spool)>0)
				$db_spool  = $this->db_insert($db_spool);

		}

		public function returnStatus()
		{
			return $this->status;

		}
		
		private function db_insert($db_spool)
		{
			$spool = new AppMessage();
			$spool->setmsgUid($db_spool['msg_uid']);
			$spool->setappUid($db_spool['app_uid']);
			$spool->setdelIndex($db_spool['del_index']);
			$spool->setappMsgtype($db_spool['app_msg_type']);
			$spool->setappMsgsubject($db_spool['app_msg_subject']);
			$spool->setappMsgfrom($db_spool['app_msg_from']);
			$spool->setappMsgto($db_spool['app_msg_to']);
			$spool->setappMsgbody($db_spool['app_msg_body']);
			$spool->setappMsgdate($db_spool['app_msg_date']);
			$spool->setappMsgcc($db_spool['app_msg_cc']);
			$spool->setappMsgbcc($db_spool['app_msg_bcc']);
			$spool->setappMsgtemplate($db_spool['app_msg_template']);
			$spool->setappMsgstatus($db_spool['app_msg_status']);

			$spool->setappMsgattach($db_spool['app_msg_attach']);

			if(!$spool->validate())
			{
			        $errors = $spool->getValidationFailures();
				$this->status = 'error';

				foreach($errors as $key => $value)
				{
					echo "Validation error - " . $value->getMessage($key) . "\n";
				}
			}
			else
			{
			        echo "Saving - validation ok\n";
				$this->status = 'success';
			        $spool->save();
			}

			
		
		}
		
		
		
		
	} // end of class
 
 
 
?>
