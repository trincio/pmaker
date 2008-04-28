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
		private $connect;
		private $status;
		private $mail_server;

		private $smtpserver;
		private $port;
		private $timeout;
		private $username;
		private $password;
		private $with_auth;

		private $return_path;
		private $envelope_to;
		private $headers;
		private $body;
		private $log;
		
		function __construct($return_path='',$env_to=array(),$headers='',$body='')
		{
			$this->connect = false;
			$this->status  = true;
			$this->mail_server = gethostbyaddr('127.0.0.1');

			$this->configure();
				
			$this->return_path = "$return_path";
			
			$this->getEnvelopeTo($env_to);
			
			$this->headers = "$headers";
			$this->body = "$body";
			$this->log = array();
			
			$this->openConnection();

			$this->sendMessage();

			$this->closeConnection();

		}
		
		public function returnErrors()
		{
			return $this->log;

		}
		
		public function returnStatus()
		{
			return $this->status;

		}

		private function configure()
		{
			require_once('config_smtp.php');

			$this->smtpserver = "{$config['smtpserver']}";
			$this->port       = "{$config['port']}";
			$this->timeout    = "{$config['timeout']}";
			$this->with_auth  = "{$config['with_auth']}";
			$this->username	  = "{$config['username']}";
			$this->password   = "{$config['password']}";

		}

		private function getEnvelopeTo($env_to)
		{
			if(count($env_to)>0)
			{
				foreach($env_to as $val)
				{
					if(false !== ($p = strpos($val,'<')))
						$this->envelope_to[] = trim(substr($val,$p));
				}
			}
			else
			{
				$this->envelope_to = array();
			}
			
		}
		
		private function openConnection()
		{
			$errno 	= '';
			$errstr = '';
			
			if($this->connect = fsockopen($this->smtpserver, $this->port, $errno, $errstr, $this->timeout))
				$this->serverResponse('220','Failed to connect');
		
		}
		
		private function closeConnection()
		{
			if($this->connect)
				fclose($this->connect);
			exit();

		}
		
		private function serverResponse($code='',$error='')
		{
			if($this->connect)
			{	
				if($rcv = fgets($this->connect, 1024))
				{ 
					if(substr($rcv,0,3) != "$code") 
					{
						$this->status = false;
						$this->log[] = $rcv . " $error";
						$this->closeConnection();
					}

				}

			}

		}
		
		private function put_line($line='')
		{
			fputs($this->connect, "$line\r\n");

		}
	
		private function sendMessage()
		{
			// say HELO
			if($this->connect) {
				$this->put_line('HELO '."$this->mail_server");
				$this->serverResponse('250','Failed to say helo');
			}

			// if we authenticate
			if(false !== strpos($this->with_auth, 'yes'))
			{
				if($this->connect)
				{
					$this->put_line('AUTH LOGIN ');
					$this->serverResponse('334','Failed to initiate authentication');
				}

				if($this->connect)
				{
					$this->put_line(base64_encode($this->username));
					$this->serverResponse('334','Failed username');
				}

				if($this->connect)
				{
					$this->put_line(base64_encode($this->password));
					$this->serverResponse('235','Failed password');
				}

			}

			// return_path
			if($this->connect) {
				$this->put_line('MAIL FROM: '."$this->return_path");
				$this->serverResponse('250','MAIL FROM failed');
			}

			// envelope_to
			if($this->connect) {
				foreach($this->envelope_to as $val) {
					$this->put_line('RCPT TO: '."$val");
					$this->serverResponse('250','RCPT TO failed');
				}
			}

			// data
			if($this->connect) {
				$this->put_line('DATA');
				$this->serverResponse('354','DATA failed');
			}

			// send headers
			$this->put_line("$this->headers");
				
			// send body
			$this->put_line("$this->body");
				
			// end of message
			$this->put_line("\r\n.");
			$this->serverResponse('250','Message failed');

			// quit
			$this->put_line('QUIT');
			$this->serverResponse('221','QUIT failed');
			
			
		}
		
		
	} // end of class
	
	
?>
