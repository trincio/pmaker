<?php

/**
 * @brief send new mail to the database spool
 *
 * @package Tomahawk_Mail
 * @author Ian K Armstrong <ika@[REMOVE_THESE_CAPITALS]openmail.cc>
 * @copyright Copyright (c) 2007, Ian K Armstrong
 * @license http://www.opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link http://www.openmail.cc
 *
 * @category web_mail
 * @subpackage spool
 * @filesource
 * @version
 *
 * @file tom_spool.php
 *
 */

	if( isset ( $_POST['form']) ) 
	{	
    $frm = $_POST['form']; 	

		G::LoadClass('insert');

		//set_include_path("projects/spool/build/classes" . ':' . get_include_path());
		
		$db_spool			= array();
		
		$db_spool['envelope_to']	= base64_encode($frm['to_email']);
		$db_spool['subject']		= base64_encode($frm['subject']);
		$db_spool['body'] 		= base64_encode($frm['body']);
	
		$db_spool['from_name']  	= base64_encode($frm['from_name']);
		$db_spool['from_email'] 	= "{$frm['from_email']}";
		$db_spool['domain'] 	        = base64_encode($frm['domain']);

		
		if( isset($frm['attachments']) && count($frm['attachments']) >0 ) 
		{
			foreach($frm['attachments'] as $attchment) 
			{
				$db_spool['attachments'][] = "$attchment";

			}

		}
		$insert = new insert($db_spool);
		unset($insert);
		
	}

	exit();
	
	
?>
