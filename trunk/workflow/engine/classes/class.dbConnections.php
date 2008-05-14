<?php
/*--------------------------------------------------
| dbConnections.class.php
| By Erik Amaru Ortiz
| CopyLeft (f) 2008 
| Email: erik@colosa.com
+--------------------------------------------------
| Email bugs/suggestions to erik@colosa.com erik.260mb.com
+--------------------------------------------------
+--------------------------------------------------*/

require_once 'model/DbSource.php';
require_once 'model/Content.php';


class dbConnections
{
    private $PRO_UID;
    private $connections;

    /*errors handle*/
    private $errno;
    private $errstr;

    function __construct($pPRO_UID)
    {
        $this->errno = 0;
        $this->errstr = "";
        $this->PRO_UID = $pPRO_UID;
        
        $this->getAllConnections();
    }

    function getAllConnections()
    {
        $oDBSource = new DbSource();
        $oContent = new Content();
		$connections = Array();
        $c = new Criteria();
        
        $c->clearSelectColumns();
        $c->addSelectColumn(DbSourcePeer::DBS_UID);
        $c->addSelectColumn(DbSourcePeer::PRO_UID);
        $c->addSelectColumn(DbSourcePeer::DBS_TYPE);
        $c->addSelectColumn(DbSourcePeer::DBS_SERVER);
        $c->addSelectColumn(DbSourcePeer::DBS_DATABASE_NAME);
        $c->addSelectColumn(DbSourcePeer::DBS_USERNAME);
        $c->addSelectColumn(DbSourcePeer::DBS_PASSWORD);
        $c->addSelectColumn(DbSourcePeer::DBS_PORT);
        $c->addSelectColumn(ContentPeer::CON_VALUE);

        $c->add(DbSourcePeer::PRO_UID, $this->PRO_UID);
        $c->add(ContentPeer::CON_CATEGORY, 'DBS_DESCRIPTION');
        $c->addJoin(DbSourcePeer::DBS_UID, ContentPeer::CON_ID);

        $result = DbSourcePeer::doSelectRS($c);
        $result->next();
        $row = $result->getRow();

        while ($row = $result->getRow()) {
            $connections[] = Array(
				'DBS_UID' 		=> $row[0], 
				'DBS_TYPE' 		=> $row[2],
				'DBS_SERVER'	=> $row[3],
				'DBS_DATABASE_NAME' => $row[4],
				'DBS_USERNAME' 	=> $row[5],
				'DBS_PASSWORD' 	=> $row[6],
				'DBS_PORT' 		=> $row[7],
				'CON_VALUE' 	=> $row[8],
			);
            $result->next();
        }
        $this->connections = $connections;
        return $connections;
    }

	function getConnections($pType){
		$connections = Array();	
		foreach($this->connections as $c){
			if(trim($pType) == trim($c['DBS_TYPE'])){
				$connections[] = $c;		
			}
		}
		if(count($connections) > 0){
			return $connections;	
		}
		else {
			return false;
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

