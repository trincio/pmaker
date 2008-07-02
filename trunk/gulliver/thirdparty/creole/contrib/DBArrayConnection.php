<?php

require_once 'creole/Connection.php';
require_once 'creole/common/ConnectionCommon.php';
require_once 'creole/PreparedStatement.php';
require_once 'creole/common/PreparedStatementCommon.php';
require_once 'creole/ResultSet.php';
require_once 'creole/common/ResultSetCommon.php';

class DBArrayPreparedStatement extends PreparedStatementCommon implements PreparedStatement {

    /**
     * Quotes string using native mysql function (mysql_real_escape_string()).
     * @param string $str
     * @return string
     */
    protected function escape($str)
    {
        return $str;
    }

    private function prepareStatement($sql)
    {
        krumo ( $sql );
        return $str;
    }

}



class DBArrayResultSet extends ResultSetCommon implements ResultSet {

  public 	$dbArray = null;

    public function seek($rownum)
    {
        $this->cursorPos = $rownum;
        return true;
    }

    /**
     * @see ResultSet::next()
     */
    public function next()
    {
      if ( $this->cursorPos == 0 ) $this->cursorPos++;

      if ( $this->cursorPos >= count ($this->dbArray) ) {
        $this->fields = NULL;
        return false;
      }
      else {
        $this->fields = $this->dbArray[$this->cursorPos];
        $this->cursorPos++;
        return true;
      }
/*        $this->fields = mysql_fetch_array($this->result, $this->fetchmode);

           if (!$this->fields) {
            $errno = mysql_errno($this->conn->getResource());
            if (!$errno) {
                // We've advanced beyond end of recordset.
                $this->afterLast();
                return false;
            } else {
                throw new SQLException("Error fetching result", mysql_error($this->conn->getResource()));
            }
        }

        if ($this->fetchmode === ResultSet::FETCHMODE_ASSOC && $this->lowerAssocCase) {
            $this->fields = array_change_key_case($this->fields, CASE_LOWER);
        }

        // Advance cursor position
  */
        return true;
    }

    /**
     * @see ResultSet::getRecordCount()
     */
    function getRecordCount()
    {
      return (int) count($this->dbArray) - 1;
    }

    /**
     * @see ResultSet::close()
     */
    function close()
    {
        if(is_resource($this->result))
            @mysql_free_result($this->result);
        $this->fields = array();
    }

    /**
     * Get string version of column.
     * No rtrim() necessary for MySQL, as this happens natively.
     * @see ResultSet::getString()
     */
    public function getString($column)
    {
        $idx = (is_int($column) ? $column - 1 : $column);
        if (!array_key_exists($idx, $this->fields)) { throw new SQLException("Invalid resultset column: " . $column); }
        if ($this->fields[$idx] === null) { return null; }
        return (string) $this->fields[$idx];
    }


}

/**
 * DBArray implementation of Connection.
 *
 */
class DBArrayConnection implements Connection {

	/** @var Connection */
	private $childConnection = null;

	/** @var int */
	private $numQueriesExecuted = 0;

	/** @var string */
	private $lastExecutedQuery = '';

	/**
	 * @var object Instance of PEAR Log (or other class with log() method).
	 */
	private $logger;

  private $_DBArray;
  public $dataSql;


	/**
	 * Sets a Logger class (e.g. PEAR Log) to use for logging.
	 * The logger class must have a log() method.  All messages are logged at default log level.
	 * @param object $logger
	 */
	public function setLogger($logger)
	{
krumo ( 'DBArrayConnection setLogger '); die;
		$this->logger = $logger;
	}

  /**
	 * connect()
	 */
	public function connect($dsninfo, $flags = 0)
	{
		if (!($driver = Creole::getDriver($dsninfo['phptype']))) {
			throw new SQLException("No driver has been registered to handle connection type: $type");
		}
    global $_DBArray;
    if ( !isset ($_DBArray) ) {
			throw new SQLException("No Database Array defined for this connection");
    }
    $this->_DBArray = $_DBArray;
    return true;
	}

	/**
	 * @see Connection::getDatabaseInfo()
	 */
	public function getDatabaseInfo()
	{
krumo ( 'DBArrayConnection getDatabaseInfo '); die;
		return $this->childConnection->getDatabaseInfo();
	}

	/**
	 * @see Connection::getIdGenerator()
	 */
	public function getIdGenerator()
	{
krumo ( 'DBArrayConnection getIdGenerator '); die;
		return $this->childConnection->getIdGenerator();
	}

	/**
	 * @see Connection::isConnected()
	 */
	public function isConnected()
	{
	  return true;
    krumo ( 'DBArrayConnection isConnected '); die;
		return $this->childConnection->isConnected();
	}

	/**
	 * @see Connection::prepareStatement()
	 */
	public function prepareStatement($dataSql)
	{
    $this->dataSql = $dataSql;
     return new DBArrayPreparedStatement($this, $dataSql['sql']);
	}

	/**
	 * @see Connection::createStatement()
	 */
	public function createStatement( )
	{
     return new DBArrayPreparedStatement($this, null);
     return new DBArrayPreparedStatement($this, $dataSql['sql']);
krumo ( 'DBArrayConnection createStatement '); die;
		$obj = $this->childConnection->createStatement();
		$objClass = get_class($obj);
		return new $objClass($this);
	}

	/**
	 * @see Connection::applyLimit()
	 */
	public function applyLimit(&$sql, $offset, $limit)
	{
krumo ( 'DBArrayConnection applyLimit '); die;
		$this->log("applyLimit(): $sql, offset: $offset, limit: $limit");
		return $this->childConnection->applyLimit($sql, $offset, $limit);
	}

	/**
	 * @see Connection::close()
	 */
	public function close()
	{
krumo ( 'DBArrayConnection connect '); die;
		$this->log("close(): Closing connection.");
		return $this->childConnection->close();
	}

	private function parseSqlString ( $query ) {
	 //we need a SQL parse, for now we only search for text 'select * from'
	 $aux = str_ireplace ( 'select * from', '', trim($query) );
   $sql = array();
   $sql['fromClause'][0] = trim( $aux );
   $sql['limit'] = 0;
   $sql['offset'] = 0;
	  return $sql;
	}

	/**
	 * @see Connection::executeQuery()
	 */
	public function executeQuery($sql, $fetchmode = null)
	{
	  if ( !is_array($sql) && strlen( $sql ) > 1 ) {
	    $sql = $this->parseSqlString ( $sql );
	  }

    $resultSet = new DBArrayResultSet( $this, $sql, $fetchmode);
    $tableName = $sql['fromClause'][0];

    //the table doesnt exists
    if ( !isset($this->_DBArray[ $tableName ])  ) {
      $resultSet->dbArray = array();
      return $resultSet;
    }


    foreach ( $this->_DBArray[ $tableName ] as $key => $row ) {
      if ( $key == 0 ) continue;
      $flag = 1;
      if ( isset ($sql['whereClause'] ) )
        foreach ( $sql['whereClause'] as $keyClause => $valClause ) {
          if ( isset ( $valClause)  && $flag == 1  ) {
            $toEval =  "\$flag = ( " . $valClause . ') ?1 :0;' ;
            eval ( $toEval );
//            print " $toEval  ---- ". $row['balance'] . ' '. $row['age'] ." $flag <br>";
          }
        }

      if ( $flag ) {
        $resultRow[] = $row;
      }
    }

    if ( $this->dataSql['selectClause'][0] == 'COUNT(*)' ) {
      $rows[] = array ( '1' => 'integer' );
      if ( isset($resultRow) && is_array ($resultRow) )
        $rows[] = array ( '1' => count ( $resultRow) );
      else
        $rows[] = array ( '1' => 0 );
      $resultSet->dbArray = $rows;
      return $resultSet;
    }

    if ( !isset($resultRow)  ) {
      $resultSet->dbArray = array();
      return $resultSet;
    }

    //else
    if ( is_array ($this->dataSql['orderByClause'] ) && count($this->dataSql['orderByClause'] ) > 0 ) {
      foreach ($resultRow as $key => $row) {
        foreach ($this->dataSql['orderByClause']  as $keyOrder => $valOrder ) {
        	if (isset($row[ $valOrder['columnName'] ])) {
            $column[ $valOrder['columnName'] ][$key]  = strtoupper( $row[ $valOrder['columnName'] ]);
          }
          else {
            $column[ $valOrder['columnName'] ][$key]  = '';
          }
        }
      }
      $direction = SORT_ASC;
      if ( trim($this->dataSql['orderByClause'][0]['direction']) != 'ASC' ) {
        $direction = SORT_DESC;
      }
      array_multisort($column[ $this->dataSql['orderByClause'][0]['columnName'] ] , $direction , $resultRow );
    }

    //prepend the headers in the resultRow
    array_unshift($resultRow, $this->_DBArray[ $tableName ][0]);
    //$resultRow[0] = $this->_DBArray[ $tableName ][0];

    /* algorith to order a multiarray
    // Obtain a list of columns
    foreach ($data as $key => $row) {
      $volume[$key]  = $row['volume'];
      $edition[$key] = $row['edition'];
    }
    // Sort the data with volume descending, edition ascending
    // Add $data as the last parameter, to sort by the common key
    array_multisort($volume, SORT_DESC, $edition, SORT_ASC, $data);*/

    /*
     * Apply Limit and Offset
     */
    if ($sql['limit']>0)
    {
      $header    = $resultRow[0];
      $resultRow = array_slice( $resultRow, $sql['offset']+1, $sql['limit'] );
      array_unshift( $resultRow , $header );
    }
    else
    {
      $header    = $resultRow[0];
      $resultRow = array_slice( $resultRow, $sql['offset']+1 );
      array_unshift( $resultRow , $header );
    }
    $resultSet->dbArray = $resultRow;
    return $resultSet;
	}

	/**
	 * @see Connection::executeUpdate()
	 */
	public function executeUpdate($sql)
	{
krumo ( 'DBArrayConnection executeUpdate '); die;
		$this->log("executeUpdate(): $sql");
		$this->lastExecutedQuery = $sql;
		$this->numQueriesExecuted++;
		return $this->childConnection->executeUpdate($sql);
	}

	/**
	 * @see Connection::getUpdateCount()
	 */
	public function getUpdateCount()
	{
krumo ( 'DBArrayConnection getUpdateCount '); die;
		return $this->childConnection->getUpdateCount();
	}

	/**
	 * @see Connection::prepareCall()
	 **/
	public function prepareCall($sql)
	{
krumo ( 'DBArrayConnection prepareCall '); die;
		$this->log("prepareCall(): $sql");
		return $this->childConnection->prepareCall($sql);
	}

	/**
	 * @see Connection::getResource()
	 */
	public function getResource()
	{
krumo ( 'DBArrayConnection getResource '); die;
		return $this->childConnection->getResource();
	}

	/**
	 * @see Connection::connect()
	 */
	public function getDSN()
	{
krumo ( 'DBArrayConnection getDSN '); die;
		return $this->childConnection->getDSN();
	}

	/**
	 * @see Connection::getFlags()
	 */
	public function getFlags()
	{
    return ;
	}

	/**
	 * @see Connection::begin()
	 */
	public function begin()
	{
krumo ( 'DBArrayConnection begin '); die;
		$this->log("Beginning transaction.");
		return $this->childConnection->begin();
	}

	/**
	 * @see Connection::commit()
	 */
	public function commit()
	{
krumo ( 'DBArrayConnection commit '); die;
		$this->log("Committing transaction.");
		return $this->childConnection->commit();
	}

	/**
	 * @see Connection::rollback()
	 */
	public function rollback()
	{
krumo ( 'DBArrayConnection rollback '); die;
		$this->log("Rolling back transaction.");
		return $this->childConnection->rollback();
	}

	/**
	 * @see Connection::setAutoCommit()
	 */
	public function setAutoCommit($bit)
	{
krumo ( 'DBArrayConnection connect '); die;
		$this->log("Setting autocommit to: " . var_export($bit, true));
		return $this->childConnection->setAutoCommit($bit);
	}

	/**
	 * @see Connection::getAutoCommit()
	 */
	public function getAutoCommit()
	{
krumo ( 'DBArrayConnection getAutoCommit '); die;
		return $this->childConnection->getAutoCommit();
	}

	/**
	 * Private function that logs message using specified logger (if provided).
	 * @param string $msg Message to log.
	 */
	private function log($msg)
	{
krumo ( 'DBArrayConnection  log '); die;
		if ($this->logger) {
			$this->logger->log($msg);
		}
	}

}
