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

class NET
{
    public $hostname;
    public $ip;

    /*errors handle*/
    private $errno;
    private $errstr;

    function __construct($pHost)
    {
        $this->errno = 0;
        $this->errstr = "";

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
            }
        } else {
            if (!$this->ip = @gethostbyname($pHost)) {
                $this->errno = 2000;
                $this->errstr = "NET::Host down";
            }
            $this->hostname = $pHost;
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

    function mysql_ping($pUser, $pPasswd)
    {
        if ($pPasswd != "") {
            $link = @mysql_connect($this->ip, $pUser, $pPasswd);
        } else {
            $link = @mysql_connect($this->ip, $pUser, "");
        }
        if ($link) {
            if (@mysql_ping($link)) {
                $this->errstr = "";
                $this->errno = 0;
            } else {
                $this->errstr = "NET::MYSQL->Lost Connection";
                $this->errno = 10010;
            }
        } else {
            $this->errstr = "NET::MYSQL->The connection was refused";
            $this->errno = 10001;
        }
        return;
    }

    function mysql_testDataBase($pUser, $pPasswd, $pDb)
    {
        set_time_limit(0);
        $link = @mysql_connect($this->ip, $pUser, $pPasswd);
        $db = @mysql_select_db($pDb);
        if ($link) {
            if ($db) {
                $result = @mysql_query("show tables;");
                if ($result) {
                    $this->errstr = "";
                    $this->errno = 0;
                    @mysql_free_result($result);
                } else {
                    $this->errstr = "NET::MYSQL->Test query failed";
                    $this->errno = 10100;
                }
            } else {
                $this->errstr = "NET::MYSQL->Select data base failed";
                $this->errno = 10011;
            }
        } else {
            $this->errstr = "NET::MYSQL->The connection was refused";
            $this->errno = 10001;
        }
    }
    
    function mssql_connect($pUser, $pPasswd)
    {
        if ($pPasswd != "") {
            $link = @mssql_connect($this->ip, $pUser, $pPasswd);
        } else {
            $link = @mssql_connect($this->ip, $pUser, "");
        }
        if ($link) {
            $this->errstr = "";
            $this->errno = 0;
        } else {
            $this->errstr = "NET::MSSQL->The connection was refused";
            $this->errno = 30001;
        }
    }

    function mssql_testDataBase($pUser, $pPasswd, $pDb)
    {
        set_time_limit(0);
        $link = @mssql_connect($this->ip, $pUser, $pPasswd);
        if ($link) {
        	$db = @mysql_select_db($pDb, $link);	
            if ($db) {
                $this->errstr = "";
                $this->errno = 0;
            } else {
                $this->errstr = "NET::MSSQL->Select data base failed";
                $this->errno = 30010;
            }
        } else {
            $this->errstr = "NET::MSSQL->The connection was refused";
            $this->errno = 30001;
        }
    }

    function pg_ping($pUser, $pPasswd, $pDb, $pPort)
    {
        $pPort = ($pPort == "") ? "5432" : $pPort;
        $link = pg_connect("host='192.168.1.23' port='5432' user='processmaker' password='processmaker' dbname='workflow'"); //pg_connect("host='$this->ip' port='$pPort' user='$pUser' password='$pPasswd' dbname='$pDb' ");
        if ($link) {
        	$this->errno = 0;
        	return 0;
            if (pg_ping($link)) {
                $this->errstr = "";
                $this->errno = 0;
            } else {
                $this->errstr = "NET::POSTGRES->Lost Connection";
                $this->errno = 20010;
            }
        } else {
            $this->errstr = "NET::POSTGRES->The connection was refused";
            $this->errno = 20001;
        }
    }
    
    function pg_connect($pUser, $pPasswd, $pDb, $pPort)
    {
        $pPort = ($pPort == "") ? "5432" : $pPort;
        $link = pg_connect("host='192.168.1.23' port='5432' user='processmaker' password='processmaker' dbname='workflow'"); //pg_connect("host='$this->ip' port='$pPort' user='$pUser' password='$pPasswd' dbname='$pDb' ");
        if ($link) {
        	$this->errstr = "";
            $this->errno = 0;
        } else {
            $this->errstr = "NET::POSTGRES->The connection was refused";
            $this->errno = 20011;
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

?>