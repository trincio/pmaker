<?php
/*--------------------------------------------------
| net.class.php
| By Erik Amaru Ortiz
| CopyLeft (f) 2008 
| Email: erik@colosa.com
+--------------------------------------------------
| Email bugs/suggestions to erik@colosa.com erik.260mb.com
+--------------------------------------------------
| This script has been created and released under
| the GNU GPL and is free to use and redistribute
| only if this copyright statement is not removed
+--------------------------------------------------*/
/**
* @LastModification 30/05/2008
*/

class NET
{
    public $hostname;
    public $ip;

    private $db_user;
    private $db_passwd;
    private $db_sourcename;
    private $db_port;

    /*errors handle*/
	public $error;
	public $errno;
	public $errstr;

    function __construct($pHost)
    {
        $this->errno = 0;
        $this->errstr = "";

        unset($this->db_user);
        unset($this->db_passwd);
        unset($this->db_sourcename);

        #verifing valid param
        if ($pHost == "") {
            $this->errno = 1000;
            $this->errstr = "NET::You must specify a host";
            //$this->showMsg();
        }
        $this->resolv($pHost);
    }

    function resolv($pHost)
    {
        if ($this->is_ipaddress($pHost)) {
            $this->ip = $pHost;
            if (!$this->hostname = @gethostbyaddr($pHost)) {
                $this->errno = 2000;
                $this->errstr = "NET::Host down";
				$this->error = "Destination Host Unreachable";
            }
        } else {
			$ip   = @gethostbyname($pHost);
			$long = ip2long($ip);
			if ( $long == -1 || $long === FALSE)  {
				$this->errno = 2000;
				$this->errstr = "NET::Host down";
				$this->error = "Destination Host Unreachable";
			} else {
				$this->ip = @gethostbyname($pHost);
				$this->hostname = $pHost;
			}
        }
    }

    function scannPort($pPort)
    {
        define('TIMEOUT', 5);
        $hostip = @gethostbyname($host); // resloves IP from Hostname returns hostname on failure
        // attempt to connect
        if (@fsockopen($this->ip, $pPort, $this->errno, $this->errstr, TIMEOUT)) {
            return true;
            @fclose($x); //close connection (i dont know if this is needed or not).
        } else {
			$this->errno = 9999;
			$this->errstr = "NET::Port Host Unreachable";
			$this->error = "Destination Port Unreachable";
            return false;
        }

    }

    function is_ipaddress($pHost)
    {
        $key = true;
        #verifing if is a ip address
        $tmp = explode(".", $pHost);
        #if have a ip address format
        if (count($tmp) == 4) {
            #if a correct ip address
            for ($i = 0; $i < count($tmp); $i++) {
                if (!is_int($tmp[$i])) {
                    $key = false;
                    break;
                }
            }
        } else {
            $key = false;
        }
        return $key;
    }

    function ping($pTTL = 3000)
    {
        $cmd = "ping -w $pTTL $this->ip";
        $output = exec($cmd, $a, $a1);
        $this->errstr = "";
        for ($i = 0; $i < count($a); $i++)
            $this->errstr += $a[$i];
        $this->errno = $a1;
    }

    function loginDbServer($pUser, $pPasswd)
    {
        $this->db_user = $pUser;
        $this->db_passwd = $pPasswd;
    }

    function setDataBase($pDb, $pPort='')
    {
        $this->db_sourcename = $pDb;
        $this->db_port = $pPort;
    }

    function tryConnectServer($pDbDriver)
    {
		if($this->errno != 0) {
			return 0;
		}
		$stat = new Stat();
			
        if(isset($this->db_user) && (isset($this->db_passwd) || ('' == $this->db_passwd)) && isset($this->db_sourcename))
        {
            switch($pDbDriver)
            {
                case 'mysql':
                    if ($this->db_passwd == '') {
						$link = @mysql_connect($this->ip.':'.$this->db_port, $this->db_user);
                    } else {
						$link = @mysql_connect($this->ip.':'.$this->db_port, $this->db_user, $this->db_passwd);
                    }
                    if ($link) {
                        if (@mysql_ping($link)) {
                            $stat->status = 'SUCCESS';
                            $this->errstr = "";
                            $this->errno = 0;
                        } else {
							$this->error = "Lost MySql Connection";
                            $this->errstr = "NET::MYSQL->Lost Connection";
                            $this->errno = 10010;
                        }
                    } else {
						$this->error = "MySql connection refused!";
                        $this->errstr = "NET::MYSQL->The connection was refused";
                        $this->errno = 10001;
                    }
                    break;

                case 'pgsql':
                    $this->db_port = ($this->db_port == "") ? "5432" : $this->db_port;
                    $link = @pg_connect("host='$this->ip' port='$this->db_port' user='$this->db_user' password='$this->db_passwd' dbname='$this->db_sourcename'");
                    if ($link) {
                        $stat->status = 'SUCCESS';
                        $this->errstr = "";
                        $this->errno = 0;
                    } else {
						$this->error = "PostgreSql connection refused!";
						$this->errstr = "NET::POSTGRES->The connection was refused";
                        $this->errno = 20001;
                    }
                    break;

                case 'mssql':
                    $str_port = ($this->db_port == "") ? "" : ",".$this->db_port;
                    $link = @mssql_connect($this->ip . $str_port, $this->db_user, $this->db_passwd);

                    if ($link) {
                        $stat->status = 'SUCCESS';
                        $this->errstr = "";
                        $this->errno = 0;
                    } else {
						$this->error = "MS-SQL Server connection refused";
                        $this->errstr = "NET::MSSQL->The connection was refused";
                        $this->errno = 30001;
                    }
                    break;

                case 'oracle':
                    $this->db_port = ($this->db_port == "") ? "1521" : $this->db_port;
                    try{
                        $link = $conn = @oci_connect($this->db_user,$this->db_passwd, "(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP) (HOST=$this->ip) (PORT=$this->db_port) )) (CONNECT_DATA=(SERVICE_NAME=)))");
                        if ($link) {
                            $stat->status = 'SUCCESS';
                            $this->errstr = "";
                            $this->errno = 0;
                        } else {
							$this->error = "Oracle connection refused";
                            $this->errstr = "NET::ORACLE->The connection was refused";
                            $this->errno = 30001;
                        }
                    } catch (Exception $e){
                        throw new Exception("[erik] Couldn't connect to Oracle Server! - ".$e->getMessage());
                    }
                    break;

                case 'informix':
                    break;
                case 'sqlite':
                    break;

            }
        } else {
            throw new Exception("CLASS::NET::ERROR: No connections param.");
        }

        return $stat;
    }

    function tryOpenDataBase($pDbDriver)
    {
		if($this->errno != 0) {
			return 0;
		}
		
        set_time_limit(0);
        $stat = new Stat();

        if(isset($this->db_user) && (isset($this->db_passwd) || ('' == $this->db_passwd)) && isset($this->db_sourcename))
        {
            switch($pDbDriver)
            {
                case 'mysql':
					$link = @mysql_connect($this->ip.':'.$this->db_port, $this->db_user, $this->db_passwd);
                    $db = @mysql_select_db($this->db_sourcename);
                    if ($link) {
                        if ($db) {
                            $result = @mysql_query("show tables;");
                            if ($result) {
                                $stat->status = 'SUCCESS';
                                $this->errstr = "";
                                $this->errno = 0;
                                @mysql_free_result($result);
                            } else {
								$this->error = "the user $this->db_user don't has privileges to run queries!";
                                $this->errstr = "NET::MYSQL->Test query failed";
                                $this->errno = 10100;
                            }
                        } else {
							$this->error = "The $this->db_sourcename data base does'n exist!";
                            $this->errstr = "NET::MYSQL->Select data base failed";
                            $this->errno = 10011;
                        }
                    } else {
						$this->error = "MySql connection refused!";
                        $this->errstr = "NET::MYSQL->The connection was refused";
                        $this->errno = 10001;
                    }
                    break;

                case 'pgsql':
                    $this->db_port = ($this->db_port == "") ? "5432" : $this->db_port;
                    $link = @pg_connect("host='$this->ip' port='$this->db_port' user='$this->db_user' password='$this->db_passwd' dbname='$this->db_sourcename'");
                    if ($link) {
                        if (@pg_ping($link)) {
                            $stat->status = 'SUCCESS';
                            $this->errstr = "";
                            $this->errno = 0;
                        } else {
							$this->error = "PostgreSql Connection to $this->ip is  unreachable!";
                            $this->errstr = "NET::POSTGRES->Lost Connection";
                            $this->errno = 20010;
                        }
                    } else {
						$this->error = "PostgrSql connection refused";
                        $this->errstr = "NET::POSTGRES->The connection was refused";
                        $this->errno = 20001;
                    }
                    break;

                case 'mssql':
                    $str_port = ($this->db_port == "") ? "" : ",".$this->db_port;
                    $link = @mssql_connect($this->ip . $str_port, $this->db_user, $this->db_passwd);
                    if ($link) {
                        $db = @mssql_select_db($this->db_sourcename, $link);
                        if ($db) {
                            $stat->status = 'SUCCESS';
                            $this->errstr = "";
                            $this->errno = 0;
                        } else {
							$this->error = "The $this->db_sourcename data base does'n exist!";
                            $this->errstr = "NET::MSSQL->Select data base failed";
                            $this->errno = 30010;
                        }
                    } else {
						$this->error = "MS-SQL Server connection refused!";
                        $this->errstr = "NET::MSSQL->The connection was refused";
                        $this->errno = 30001;
                    }
                    break;

                case 'oracle':
                    $this->db_port = ($this->db_port == "") ? "1521" : $this->db_port;
                    $link = @oci_connect($this->db_user,$this->db_passwd, "(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP) (HOST=$this->ip) (PORT=$this->db_port) )))");
                    if ($link) {
                        $stid = @oci_parse($link, 'select AUTHENTICATION_TYPE from v$session_connect_info');
                        $result = @oci_execute($stid, OCI_DEFAULT);
                        if($result){
                            $stat->status = 'SUCCESS';
                            $this->errstr = "";
                            $this->errno = 0;
                            @oci_close($link);
                        } else {
							$this->error = "the user $this->db_user don't has privileges to run queries!";
                            $this->errstr = "NET::ORACLE->Couldn't execute any query on this server!";
                            $this->errno = 40010;
                        }
                    } else {
						$this->error = "Oracle connection refused!";
                        $this->errstr = "NET::ORACLE->The connection was refused";
                        $this->errno = 40001;
                    }
                    break;
                case 'informix':
                    break;
                case 'sqlite':
                    break;

            }
        } else {
            throw new Exception("CLASS::NET::ERROR: No connections param.");
        }
        return $stat;
    }

    function getDbServerVersion($driver)
    {
        if(isset($this->ip) && isset($this->db_user) && isset($this->db_passwd)) {
            try{
                switch($driver)
                {
                    case 'mysql':
                        if($link = @mysql_connect($this->ip, $this->db_user, $this->db_passwd)){
                            $v = @mysql_get_server_info();
                        } else {
                            throw new Exception(@mysql_error($link));
                        }
                        break;

                    case 'pgsql':
                        $this->db_port = ($this->db_port == "") ? "5432" : $this->db_port;
                        $link = @pg_connect("host='$this->ip' port='$this->db_port' user='$this->db_user' password='$this->db_passwd' dbname='$this->db_sourcename'");
                        if($link){
                            $v = @pg_version($link);
                        } else {
                            throw new Exception(@pg_last_error($link));
                        }
                        break;
                }
                return (isset($v))?$v:'none';
            } catch (Exception $e){
                throw new Exception($e->getMessage());
            }
        }
        else{
            throw new Exception('NET::Error->No params for Data Base Server!');
        }
    }
    
    function dbName($pAdapter)
    {
		switch($pAdapter)
		{
			case 'mysql': return 'MySql'; break;
			case 'pgsql': return 'PostgreSQL'; break;
			case 'mssql': return 'Microsoft SQL Server'; break;
			case 'oracle': return 'Oracle'; break;
			case 'informix': return  'Informix'; break;
			case 'sqlite': return 'SQLite'; break;
		}
	}

    function showMsg()
    {
        if ($this->errno != 0) {
            $msg = "
			<center>
			<fieldset style='width:90%'><legend>Class NET</legend>
			  <div align=left>	
				<font color='red'>
					<b>NET::ERROR NO -> $this->errno<br/>
					NET::ERROR MSG -> $this->errstr</b>
				</font>
			  </div>	
			</fieldset>
			<center>";
            print ($msg);
        }
    }

    function getErrno()
    {
        return $this->errno;
    }

    function getErrmsg()
    {
        return $this->errstr;
    }

}

class Stat
{
    public $stutus;

    function __construct()
    {
        $this->status = false;
    }
}

?>