<?php
/**
 * class.Installer.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */
//
// It works with the table CONFIGURATION in a WF dataBase
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * Processmaker Installer
 * @package ProcessMaker
 * @author maborak
 * @copyright 2008 COLOSA
 */

class Installer
{
	public 	$options=Array();
	public 	$result	=Array();
	public 	$error	=Array();
	public 	$report	=Array();
	private	$connection_database;
	function __construct()
	{
	}
	public function create_site($config=Array(),$confirmed=false)
	{
		$this->options=G::array_concat(Array(
			'password'	=>G::generate_password(12),
			'path_data'	=>@PATH_DATA,
			'path_compiled'	=>@PATH_C,
			'database'=>Array()
		),$config);
		$a=@explode(SYSTEM_HASH,G::decrypt(HASH_INSTALLATION,SYSTEM_HASH));
		$this->options['database']=G::array_concat(Array(
			'username'=>@$a[1],
			'password'=>@$a[2],
			'hostname'=>@$a[0]
		),$this->options['database']);
		return ($confirmed===true)?$this->make_site():$this->create_site_test();
	}
	public function isset_site($name="workflow")
	{
		return file_exists(PATH_DATA."sites/".$name);
	}
	private function create_site_test()
	{
		$result=Array(
			'path_data'	=>$this->is_dir_writable($this->options['path_data']),
			'path_compiled'	=>$this->is_dir_writable($this->options['path_compiled']),
			'database'	=>$this->check_connection()
		);
		return Array(
			'created'=>G::var_compare(
				true,
				$result['path_data'],
				$result['database']['connection'],
				$result['database']['grant'],
				$result['database']['version']),
			'result'=>$result
		);
	}
	private function make_site()
	{
		$test=$this->create_site_test();
		if($test['created']===true)
		{
			$local=Array('localhost','127.0.0.1');

			$wf = "wf_".$this->options['name'];
			$rb = "rbac_".$this->options['name'];
			$rp = "rp_".$this->options['name'];
			$schema	="schema.sql";
			$values	="insert.sql";   //noe existe
			/* Create databases & users  */
			$q = "DROP DATABASE IF EXISTS ".$wf;
			$ac = @mysql_query($q,$this->connection_database);
			$this->log($q.": => ".((!$ac)?mysql_error():"OK")."\n");

			$q = "DROP DATABASE IF EXISTS ".$rb;
			$ac = @mysql_query("DROP DATABASE IF EXISTS ".$rb,$this->connection_database);
			$this->log($q.": => ".((!$ac)?mysql_error():"OK")."\n");

			$q = "DROP DATABASE IF EXISTS ".$rp;
			$ac = @mysql_query("DROP DATABASE IF EXISTS ".$rp,$this->connection_database);
			$this->log($q.": => ".((!$ac)?mysql_error():"OK")."\n");

			$q	= "CREATE DATABASE ".$wf." DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
			$ac = @mysql_query($q,$this->connection_database);
			$this->log($q.": => ".((!$ac)?mysql_error():"OK")."\n");

			$q	= "CREATE DATABASE ".$rb." DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
			$ac = @mysql_query($q,$this->connection_database);
			$this->log($q.": => ".((!$ac)?mysql_error():"OK")."\n");

			/* report DB begin */

			$q	= "CREATE DATABASE ".$rp." DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
			$ac = @mysql_query($q,$this->connection_database);
			$this->log($q.": => ".((!$ac)?mysql_error():"OK")."\n");

			/* report DB end */

			//$priv_wf = "GRANT ALL PRIVILEGES ON `".$wf.".* TO ".$wf."@`".$this->options['database']['hostname']."` IDENTIFIED BY '".$this->options['password']."' WITH GRANT OPTION";
			if(in_array($this->options['database']['hostname'],$local))
			{
				$priv_wf = "GRANT ALL PRIVILEGES ON `".$wf."`.* TO ".$wf."@'localhost' IDENTIFIED BY '".$this->options['password']."' WITH GRANT OPTION";
			}
			else
			{
				$priv_wf = "GRANT ALL PRIVILEGES ON `".$wf."`.* TO ".$wf."@'%' IDENTIFIED BY '".$this->options['password']."' WITH GRANT OPTION";
			}
			$ac = @mysql_query($priv_wf,$this->connection_database);
			$this->log($priv_wf.": => ".((!$ac)?mysql_error():"OK")."\n");


			if(in_array($this->options['database']['hostname'],$local))
			{
				$priv_rb = "GRANT ALL PRIVILEGES ON `".$rb."`.* TO ".$rb."@'localhost' IDENTIFIED BY '".$this->options['password']."' WITH GRANT OPTION";
			}
			else
			{
				$priv_rb = "GRANT ALL PRIVILEGES ON `".$rb."`.* TO ".$rb."@'%' IDENTIFIED BY '".$this->options['password']."' WITH GRANT OPTION";
			}
			$ac = @mysql_query($priv_rb,$this->connection_database);
			$this->log($priv_rb.": => ".((!$ac)?mysql_error():"OK")."\n");


			/* report DB begin */

			if(in_array($this->options['database']['hostname'],$local))
			{
				$priv_rp = "GRANT ALL PRIVILEGES ON `".$rp."`.* TO ".$rp."@'localhost' IDENTIFIED BY '".$this->options['password']."' WITH GRANT OPTION";
				//$priv_rp = "GRANT ALL PRIVILEGES ON `".$rp."`.* TO ".$wf."@'localhost' IDENTIFIED BY '".$this->options['password']."' WITH GRANT OPTION";
			}
			else
			{
				$priv_rp = "GRANT ALL PRIVILEGES ON `".$rp."`.* TO ".$rp."@'%' IDENTIFIED BY '".$this->options['password']."' WITH GRANT OPTION";
				//$priv_rp = "GRANT ALL PRIVILEGES ON `".$rp."`.* TO ".$wf."@'%' IDENTIFIED BY '".$this->options['password']."' WITH GRANT OPTION";
			}
			$ac = @mysql_query($priv_rp,$this->connection_database);
			$this->log($priv_rp.": => ".((!$ac)?mysql_error():"OK")."\n");


			/* report DB end */

			/* Dump schema workflow && data  */

			$this->log("Dump schema workflow/rbac && data\n====================================\n");
			$myPortA = explode(":",$this->options['database']['hostname']);
			if(count($myPortA)<2)
			{
				$myPortA[1]="3306";
			}
			$myPort=$myPortA[1];
			$this->options['database']['hostname']=$myPortA[0];

			$this->log("Mysql port: ".$myPort."\n");

			mysql_select_db($wf,$this->connection_database);
			$pws = PATH_WORKFLOW_MYSQL_DATA.$schema;
			$qws = $this->query_sql_file(PATH_WORKFLOW_MYSQL_DATA.$schema,$this->connection_database);
			$this->log($qws);
			$qwv = $this->query_sql_file(PATH_WORKFLOW_MYSQL_DATA.$values,$this->connection_database);
			$this->log($qwv);
			/*$pws = (PHP_OS=="WINNT")?'"'.$pws.'"':$pws;

		$sh_sc = $this->options['database']['cli']." ".$wf." < ".$pws." -h ".$this->options['database']['hostname']." --port=".$myPort." --user=".$wf." --password=".$this->options['password'];
		$result_shell = exec($sh_sc);
		$this->log($sh_sc."  => ".(($result_shell)?$result_shell:"OK")."\n");


		$pws = PATH_WORKFLOW_MYSQL_DATA.$values;
		$pws = (PHP_OS=="WINNT")?'"'.$pws.'"':$pws;
		$sh_in = $this->options['database']['cli']." ".$wf." < ".$pws." -h ".$this->options['database']['hostname']." --port=".$myPort." --user=".$wf." --password=".$this->options['password'];
			$result_shell = exec($sh_in);
			$this->log($result_shell."\n");
		$this->log($sh_in."  => ".(($result_shell)?$result_shell:"OK")."\n");*/


		/* Dump schema rbac && data  */
		$pws = PATH_RBAC_MYSQL_DATA.$schema;
		mysql_select_db($rb,$this->connection_database);
		$qrs = $this->query_sql_file(PATH_RBAC_MYSQL_DATA.$schema,$this->connection_database);
		$this->log($qrs);
		$qrv = $this->query_sql_file(PATH_RBAC_MYSQL_DATA.$values,$this->connection_database);
		$this->log($qrv);
		/*$pws = (PHP_OS=="WINNT")?'"'.$pws.'"':$pws;

		$sh_rbsc = $this->options['database']['cli']." ".$rb." < ".$pws." -h ".$this->options['database']['hostname']." --port=".$myPort." --user=".$rb." --password=".$this->options['password'];
		$result_shell = exec($sh_rbsc,$err_sh);
		$this->log($result_shell."\n");
		$this->log($sh_rbsc."  => ".(($result_shell)?$result_shell:"OK")."\n");


		$pws = PATH_RBAC_MYSQL_DATA.$values;
		$pws = (PHP_OS=="WINNT")?'"'.$pws.'"':$pws;

		$sh_in = $this->options['database']['cli']." ".$rb." < ".$pws." -h ".$this->options['database']['hostname']." --port=".$myPort." --user=".$rb." --password=".$this->options['password'];
		$result_shell = exec($sh_in);
		$this->log($sh_in."  => ".(($result_shell)?$result_shell:"OK")."\n");*/

		$path_site 	= $this->options['path_data']."/sites/".$this->options['name']."/";
		$db_file	= $path_site."db.php";
		@mkdir($path_site,0777,true);
		@mkdir($path_site."customFunctions",0777,true);
		@mkdir($path_site."rtfs/",0777,true);
		@mkdir($path_site."xmlForms",0777,true);
		@mkdir($path_site."processesImages/",0777,true);
		@mkdir($path_site."files/",0777,true);

		$db_text = "<?php\n" .
		"// Processmaker configuration\n" .
		"define ('DB_ADAPTER', 'mysql' );\n" .
		"define ('DB_HOST', '" . $this->options['database']['hostname'] . ":".$myPort."' );\n" .
		"define ('DB_NAME', '" . $wf. "' );\n" .
		"define ('DB_USER', '" . $wf . "' );\n" .
		"define ('DB_PASS', '" . $this->options['password'] . "' );\n" .
		"define ('DB_RBAC_HOST', '". $this->options['database']['hostname'] .":".$myPort."' );\n" .
		"define ('DB_RBAC_NAME', '". $rb . "' );\n" .
		"define ('DB_RBAC_USER', '".$rb . "' );\n" .
		"define ('DB_RBAC_PASS', '". $this->options['password'] . "' );\n" .
		"define ('DB_REPORT_HOST', '". $this->options['database']['hostname'] .":".$myPort."' );\n" .
		"define ('DB_REPORT_NAME', '". $rp . "' );\n" .
		"define ('DB_REPORT_USER', '".$rp . "' );\n" .
		"define ('DB_REPORT_PASS', '". $this->options['password'] . "' );\n" .
		"?>";
		$fp =  @fopen($db_file, "w");
		$this->log("Creating: ".$db_file."  => ".((!$fp)?$fp:"OK")."\n");
		$ff =  @fputs( $fp, $db_text, strlen($db_text));
		$this->log("Write: ".$db_file."  => ".((!$ff)?$ff:"OK")."\n");

		fclose( $fp );
		}
		return $test;
	}
	public function query_sql_file($file,$connection)
	{
		$report = array(
			'SQL_FILE'=>$file,
			'errors'=>array(),
			'querys'=>0
		);
		$content = @fread(@fopen($file,"rt"),@filesize($file));
		if(!$content)
		{
			$report['errors']="Error reading SQL";
			return $report;
		}
		$ret = array();
		for ($i=0 ; $i < strlen($content)-1; $i++)
		{
			if ( $content[$i] == ";" )
			{
            	if ( $content[$i+1] == "\n" )
            	{
					$ret[] = substr($content, 0, $i);
					$content = substr($content, $i + 1);
					$i = 0;
            	}
        	}
    	}
    	$report['querys']=count($ret);
		foreach($ret as $qr)
		{
			$re = @mysql_query($qr,$connection);
			if(!$re)
			{
				$report['errors'][]="Query error: ".mysql_error();
			}
		}
		return $report;
	}
	private function check_path()
	{

	}
	private function find_root_path($path)
	{
		$i =0 ; //prevent loop inifinity
		while(!is_dir($path) && ($path = dirname($path)) && ((strlen($path)>1) && $i<10))
		{
			$i++;
		}
		return $path;
	}
	public function file_permisions($file,$def=777)
	{
		if ( PHP_OS == 'WINNT' )
		  return $def;
		else
		  return (int)substr(sprintf('%o',@fileperms($file)),-4);
	}
	public function is_dir_writable($dir='')
	{
		if (PHP_OS=='WINNT')
		{
	  		$dir = $this->find_root_path($dir);
  			return file_exists ( $dir );
		}
		else {
			$dir = $this->find_root_path($dir);
	  		return (is_writable($dir) && ($this->file_permisions($dir)==777 || $this->file_permisions($dir)==755));
		}
	}
	private function check_connection()
	{
		if(!function_exists("mysql_connect"))
		{
			return Array(
				'connection'=>false,
				'grant'=>false,
				'version'=>false,
				'message'=>"php-mysql is Not Installed"
			);
		}
		$this->connection_database = @mysql_connect($this->options['database']['hostname'],$this->options['database']['username'],$this->options['database']['password']);
		$rt = Array(
				'version'=>false
			);
		if(!$this->connection_database)
		{
			$rt['connection']	=false;
			$rt['grant']		=false;
			$rt['message']		="Mysql error: ".mysql_error();
		}
		else
		{
			preg_match('@[0-9]+\.[0-9]+\.[0-9]+@',mysql_get_server_info($this->connection_database),$version);
			$rt['version']=version_compare(@$version[0],"4.1.0",">=");
			$rt['connection']=true;
			$dbNameTest = "PROCESSMAKERTESTDC";
			$db = @mysql_query("CREATE DATABASE ".$dbNameTest,$this->connection_database);
			if(!$db)
			{
				$rt['grant']=false;
				$rt['message']="Db GRANTS error:  ".mysql_error();
			}
			else
			{
				//@mysql_drop_db("processmaker_testGA");
				$usrTest = "wfrbtest";
				$chkG = "GRANT ALL PRIVILEGES ON `".$dbNameTest."`.* TO ".$usrTest."@'%' IDENTIFIED BY 'sample' WITH GRANT OPTION";
				$ch   = @mysql_query($chkG,$this->connection_database);
				if(!$ch)
				{
					$rt['grant']=false;
					$rt['message']="USER PRIVILEGES ERROR";
				}
				else
				{
					@mysql_query("DROP USER ".$usrTest."@'%'",$this->connection_database);
					$rt['grant']=true;
					$rt['message']="Successful connection";
				}
				@mysql_query("DROP DATABASE ".$dbNameTest,$this->connection_database);
			}
		}
		return $rt;
	}
	public function log($text)
	{
		array_push($this->report,$text);
	}
}
/*
global $RBAC;
$aData['USR_UID']      = $_POST['form']['USR_UID'];
$aData['USR_USERNAME'] = $_POST['form']['USR_USERNAME'];
$aData['USR_PASSWORD'] = $_POST['form']['USR_PASSWORD'];
$RBAC->updateUser($aData);
require_once 'classes/model/Users.php';
$oUser = new Users();
$oUser->update($aData);
*/
?>