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
 
require_once ( "classes/model/AppSpool.php" );

	class insert
	{
		private $db_spool;
		private $mode;
	
		function __construct($db_spool=array(),$mode='pending')
		{
			if(count($db_spool)>0)
				$db_spool  = $this->db_insert($db_spool,$mode);

		}
		
		private function db_insert($db_spool,$mode)
		{
			$string = '';
			$sender = strtolower(trim($db_spool['from_email']));
		
			foreach($db_spool as $key => $val)
			{
				$b1 = 'A480fa0ba807b4A';
				$b2 = 'B480fa0ba70db4B';
				$string .= "$key$b1$val$b2";

			}
			
			$time = time();

			$spool = new AppSpool();
			$spool->setSender($sender);
			$spool->setFile($string);
			$spool->setNow($time);
			$spool->setStatus($mode);

			if(!$spool->validate())
			{
			        $errors = $spool->getValidationFailures();

				foreach($errors as $key => $value)
				{
					echo "Validation error - " . $value->getMessage($key) . "\n";
				}
			}
			else
			{
			        echo "Saving - validation ok\n";
			        $spool->save();
			}
			
		
		}
		
		
		
		
	} // end of class
 
 
 
?>
