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

	//-------------------------------------------------------------
	// smtp authentication
	//-------------------------------------------------------------
	// setSmtpAuthentication($sAuth)
	// setUsername($sName)
	// setPassword($sPass)
	//-------------------------------------------------------------

	class smtp
	{
		private $mail_server;
		private $port;
		private $return_path;
		private $envelope_to;
		private $status;
		private $headers;
		private $body;
		private $log;
		private $with_auth;
		private $username;
		private $password;

		function __construct()
		{
			$this->status  = false;

			$this->with_auth = false; 	// change to 'true' to use smtp authentication
			$this->username = '';     	// needed for smtp authentication
			$this->password = '';     	// needed for smtp authentication

			$this->mail_server = @gethostbyaddr('127.0.0.1');
			$this->port = 25;

			$this->return_path = '';
			$this->envelope_to = array();
			$this->headers = '';
			$this->body = '';

			$this->log = array();


		}

		public function setServer($sServer)
		{
			if(($sAux = @gethostbyaddr($sServer)))
				$sServer = $sAux;

			$this->mail_server = $sServer;
		}

		public function setPort($iPort) {
		  $this->port = ($iPort != '' ? (int)$iPort : 25);
		}

		public function setReturnPath($sReturnPath) {
		  $this->return_path = $sReturnPath;
		}

		public function setHeaders($sHeaders) {
		  $this->headers = $sHeaders;
		}

		public function setBody($sBody) {
		  $this->body = $sBody;
		}

		public function setSmtpAuthentication($sAuth) {
		 	$this->with_auth = $sAuth;
		}

		public function setUsername($sName) {
		 	$this->username = $sName;
  		}

		public function setPassword($sPass) {
			$this->password = $sPass;
		}


		public function returnErrors() {
			return $this->log;
		}

		public function returnStatus() {
			return $this->status;
		}

		public function setEnvelopeTo($env_to)
		{
			if(count($env_to)>0)
			{
				foreach($env_to as $val)
				{
					(false !== ($p = strpos($val,'<')))
						? $this->envelope_to[] = trim(substr($val,$p))
				    		: $this->envelope_to[] = trim($val);
				}

			}

		}

		public function sendMessage()
		{
			// connect
			$errno = $errstr = '';
			$cp = @fsockopen("$this->mail_server", $this->port, $errno, $errstr, 1);

			if(!$cp)
			{
				$this->log[] = 'Failed to make a connection';
				return false;
			}

			$res = fgets($cp,256);
			if(substr($res,0,3) != '220')
			{
				$this->log[] = $res.' Failed to connect';
				fclose($cp);
				return false;
			}

			if(false !== $this->with_auth)
			{
				// say EHLO - works with SMTP and ESMTP servers
				fputs($cp, 'EHLO '."$this->mail_server\r\n");

				$res = fgets($cp,256);
				if(substr($res,0,3) != '250')
				{
					$this->log[] = $res.' Failed to say EHLO';
					fclose($cp);
					return false;
				}

				// Request Authentication
				fputs($cp, 'AUTH LOGIN'."\r\n");

				$res = fgets($cp,256);
				if(substr($res,0,3) != '334')
				{
					$this->log[] = $res.' Auth Login Failed';
					fclose($cp);
					return false;
				}

				// Send Username
				fputs($cp, base64_encode($this->username)."\r\n");

				$res = fgets($cp,256);
				if(substr($res,0,3) != '334')
				{
					$this->log[] = $res.' Username failed';
					fclose($cp);
					return false;
				}

				// Send Password
				fputs($cp, base64_encode($this->password)."\r\n");

				$res = fgets($cp,256);
				if(substr($res,0,3) != '235')
				{
					$this->log[] = $res.' Password failed';
					fclose($cp);
					return false;
				}


			}
			else // without smtp authentication
			{

				// say HELO
				fputs($cp, 'HELO '."$this->mail_server\r\n");

				$res = fgets($cp,256);
				if(substr($res,0,3) != '250')
				{
					$this->log[] = $res.' Failed to say HELO';
					fclose($cp);
					return false;
				}

			}

			// mail from
			fputs($cp, 'MAIL FROM: '."$this->return_path\r\n");

			$res = fgets($cp,256);
			if(substr($res,0,3) != '250')
			{
				$this->log[] = $res.' MAIL FROM failed';
				fclose($cp);
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
					fclose($cp);
					return false;
				}

			}

			// data
			fputs($cp, 'DATA'."\r\n");

			$res = fgets($cp,256);
			if(substr($res,0,3) != '354')
			{
				$this->log[] = $res.' DATA failed';
				fclose($cp);
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
				fclose($cp);
				return false;
			}

			// quit
			fputs($cp, 'QUIT'."\r\n");

			$res = fgets($cp,256);
			if(substr($res,0,3) != '221')
			{
				$this->log[] = $res.' QUIT failed';
				fclose($cp);
				return false;
			}

			fclose($cp);

			$this->status  = true;

		}




	} // end of class


?>
