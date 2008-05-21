<?php

/**
 * @brief smtp class to send emails. Requires an email server.
 *
 * @package Tomahawk_Mail
 * @author Ian K Armstrong <ika@[REMOVE_THESE_CAPITALS]openmail.cc>
 * @copyright Copyright (c) 2007, Ian K Armstrong
 * @license http://www.opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link http://www.openmail.cc
 *
 * @category web_mail
 * @subpackage mail
 * @filesource
 * @version
 *
 * @file class.smtp.php
 *
 */


	class smtp
	{
		private $mail_server;
		private $port = 25;
		private $return_path;
		private $envelope_to;
		private $status;
		private $headers;
		private $body;
		private $log;

		function __construct($return_path='',$env_to=array(),$headers='',$body='')
		{
			$this->status  = false;
			$this->mail_server = gethostbyaddr('127.0.0.1');

			$this->return_path = "$return_path";

			$this->getEnvelopeTo($env_to);

			$this->headers = "$headers";
			$this->body = "$body";
			$this->log = array();

			/*if(count($this->envelope_to)>0)
				$this->status = $this->sendMessage();*/
		}

		function setServer($sServer) {
		  $this->mail_server = ($sServer != '' ? $sServer : gethostbyaddr('127.0.0.1'));
		}

		function setPort($iPort) {
		  $this->port = ($iPort != '' ? (int)$iPort : 25);
		}

		function setReturnPath($sReturnPath) {
		  $this->return_path = $sReturnPath;
		}

		function setHeaders($sHeaders) {
		  $this->headers = $sHeaders;
		}

		function setBody($sBody) {
		  $this->body = $sBody;
		}

		public function returnErrors()
		{
			return $this->log;

		}

		public function returnStatus()
		{
			return $this->status;

		}

		public function getEnvelopeTo($env_to)
		{
			if(count($env_to)>0)
			{
				foreach($env_to as $val)
				{
					if(false !== ($p = strpos($val,'<')))
						$this->envelope_to[] = trim(substr($val,$p));
				  else
				    $this->envelope_to[] = trim($val);
				}
			}
			else
			{
				$this->envelope_to = array();
			}

		}

		public function sendMessage()
		{
			// connect
			$cp = fsockopen("$this->mail_server", $this->port, $errno, $errstr, 1);

			if(!$cp)
			{
				$this->log[] = 'Failed to make a connection';
				return false;
			}

			$res = fgets($cp,256);
			if(substr($res,0,3) != '220')
			{
				$this->log[] = $res.' Failed to connect';
				return false;
			}

			// say HELO
			fputs($cp, 'HELO '."$this->mail_server\r\n");

			$res = fgets($cp,256);
			if(substr($res,0,3) != '250')
			{
				$this->log[] = $res.' Failed to say HELO';
				return false;
			}

			// mail from
			fputs($cp, 'MAIL FROM: '."$this->return_path\r\n");

			$res = fgets($cp,256);
			if(substr($res,0,3) != '250')
			{
				$this->log[] = $res.' MAIL FROM failed';
				return false;
			}

			// mail to
			foreach($this->envelope_to as $val)
			{
				fputs($cp, 'RCPT TO: '."$val\r\n");

				$res = fgets($cp,256);
				if(substr($res,0,3) != '250')
				{
					$this->log[] = $res.' RCPT TO failed';
					return false;
				}

			}

			// data
			fputs($cp, 'DATA'."\r\n");

			$res = fgets($cp,256);
			if(substr($res,0,3) != '354')
			{
				$this->log[] = $res.' DATA failed';
				return false;
			}

			// send headers
			fputs($cp, "$this->headers\r\n");

			// send body
			fputs($cp, "$this->body\r\n");

			// end of message
			fputs($cp, "\r\n.\r\n");

			$res = fgets($cp,256);
			if(substr($res,0,3) != '250')
			{
				$this->log[] = $res. ' Message failed';
				return false;
			}

			// quit
			fputs($cp, 'QUIT'."\r\n");

			$res = fgets($cp,256);
			if(substr($res,0,3) != '221')
			{
				$this->log[] = $res.' QUIT failed';
				return false;
			}

			return true;

		}



	} // end of class


?>
