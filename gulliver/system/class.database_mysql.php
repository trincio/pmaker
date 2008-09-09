<?php
/**
 * class.database_mysql.php
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

G::LoadSystem('database_base');

class database extends database_base {
  public $iFetchType = MYSQL_ASSOC;
	public function __construct($sType = DB_ADAPTER, $sServer = DB_HOST, $sUser = DB_USER, $sPass = DB_PASS, $sDataBase = DB_NAME) {
		$this->sType           = $sType;
		$this->sServer         = $sServer;
		$this->sUser           = $sUser;
		$this->sPass           = $sPass;
		$this->sDataBase       = $sDataBase;
		$this->oConnection     = @mysql_connect($sServer, $sUser, $sPass) || null;
		$this->sQuoteCharacter = '`';
	}
	public function generateCreateTableSQL($sTable, $aColumns) {
		$sKeys = '';
	  $sSQL = 'CREATE TABLE IF NOT EXISTS ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . '(';
		foreach ($aColumns as $sColumnName => $aParameters) {
		  if ($sColumnName != 'INDEXES') {
			  $sSQL .= $this->sQuoteCharacter . $sColumnName . $this->sQuoteCharacter . ' ' . $aParameters['Type'];
			  if ($aParameters['Null'] == 'YES') {
			  	$sSQL .= ' NULL';
			  }
			  else {
			  	$sSQL .= ' NOT NULL';
			  }
			  if ($aParameters['Key'] == 'PRI') {
			  	$sKeys .= $this->sQuoteCharacter . $sColumnName . $this->sQuoteCharacter . ',';
			  }
			  if (isset($aParameters['Default'])) {
			    if ($aParameters['Default'] != '') {
			    	$sSQL .= " DEFAULT '" . $aParameters['Default'] . "'";
			    }
			  }
			  $sSQL .= ',';
			}
		}
		$sSQL = substr($sSQL, 0, -1);
		if ($sKeys != '') {
			$sSQL .= ',PRIMARY KEY(' . substr($sKeys, 0, -1) . ')';
		}
		$sSQL .= ')' . $this->sEndLine;
		return $sSQL;
	}
	public function generateDropColumnSQL($sTable, $sColumn) {
		$sSQL = 'ALTER TABLE ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter .
		        ' DROP COLUMN ' . $this->sQuoteCharacter . $sColumn . $this->sQuoteCharacter . $this->sEndLine;
		return $sSQL;
	}
	public function generateAddColumnSQL($sTable, $sColumn, $aParameters) {
		$sSQL = 'ALTER TABLE ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter .
		        ' ADD COLUMN ' . $this->sQuoteCharacter . $sColumn . $this->sQuoteCharacter .
		        ' ' . $aParameters['Type'];
		if ($aParameters['Null'] == 'YES') {
			$sSQL .= ' NULL';
		}
		else {
			$sSQL .= ' NOT NULL';
		}
		/*if ($aParameters['Key'] == 'PRI') {
			$sKeys .= 'ALTER TABLE ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter .
			          ' ADD PRIMARY KEY (' . $this->sQuoteCharacter . $sColumn . $this->sQuoteCharacter . ')' . $this->sEndLine;
		}*/
		if ($aParameters['Default'] != '') {
			$sSQL .= " DEFAULT '" . $aParameters['Default'] . "'";
		}
		$sSQL .= $this->sEndLine;
		return $sSQL;
	}
	public function generateChangeColumnSQL($sTable, $sColumn, $aParameters) {
		$sSQL = 'ALTER TABLE ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter .
		        ' CHANGE COLUMN ' . $this->sQuoteCharacter . $sColumn . $this->sQuoteCharacter .
		        ' ' . $this->sQuoteCharacter . $sColumn . $this->sQuoteCharacter;
		if (isset($aParameters['Type'])) {
			$sSQL .= ' ' . $aParameters['Type'];
		}
		if (isset($aParameters['Null'])) {
			if ($aParameters['Null'] == 'YES') {
		  	$sSQL .= ' NULL';
		  }
		  else {
		  	$sSQL .= ' NOT NULL';
		  }
		}
		if (isset($aParameters['Default'])) {
			if ($aParameters['Default'] != '') {
			  $sSQL .= " DEFAULT '" . $aParameters['Default'] . "'";
		  }
		}
		$sSQL .= $this->sEndLine;
		return $sSQL;
	}
	public function generateGetPrimaryKeysSQL($sTable) {
	  try {
	    if ($sTable == '') {
	      throw new Exception('The table name cannot be empty!');
	    }
	    return 'SHOW INDEX FROM  ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . ' WHERE Seq_in_index = 1' . $this->sEndLine;
	  }
	  catch (Exception $oException) {
	    throw $oException;
	  }
	}
	public function generateDropPrimaryKeysSQL($sTable) {
	  try {
	    if ($sTable == '') {
	      throw new Exception('The table name cannot be empty!');
	    }
	    return 'ALTER TABLE ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . ' DROP PRIMARY KEY' . $this->sEndLine;
	  }
	  catch (Exception $oException) {
	    throw $oException;
	  }
	}
	public function generateAddPrimaryKeysSQL($sTable, $aPrimaryKeys) {
	  try {
	    if ($sTable == '') {
	      throw new Exception('The table name cannot be empty!');
	    }
	    $sSQL = 'ALTER TABLE ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter .
			        ' ADD PRIMARY KEY (';
			foreach ($aPrimaryKeys as $sKey) {
			  $sSQL .= $this->sQuoteCharacter . $sKey . $this->sQuoteCharacter . ',';
			}
			$sSQL = substr($sSQL, 0, -1) . ')' . $this->sEndLine;
	    return $sSQL;
	  }
	  catch (Exception $oException) {
	    throw $oException;
	  }
	}
	public function generateDropKeySQL($sTable, $sColumn) {
	  try {
	    if ($sTable == '') {
	      throw new Exception('The table name cannot be empty!');
	    }
	    if ($sColumn == '') {
	      throw new Exception('The column name cannot be empty!');
	    }
	    return 'ALTER TABLE ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . ' DROP INDEX ' . $this->sQuoteCharacter . $sColumn . $this->sQuoteCharacter . $this->sEndLine;
	  }
	  catch (Exception $oException) {
	    throw $oException;
	  }
	}
	public function generateAddKeysSQL($sTable, $aKeys) {
	  try {
	    $sSQL = 'ALTER TABLE ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . ' ADD INDEX (';
	    foreach ($aKeys as $sKey) {
	      $sSQL .= $this->sQuoteCharacter . $sKey . $this->sQuoteCharacter . ', ';
	    }
	    $sSQL  = substr($sSQL, 0, -2);
	    $sSQL .= ')' . $this->sEndLine;
	    return $sSQL;
	  }
	  catch (Exception $oException) {
	  	throw $oException;
	  }
	}
	public function generateShowTablesSQL() {
    return 'SHOW TABLES' . $this->sEndLine;
	}
	public function generateDescTableSQL($sTable) {
	  try {
	    if ($sTable == '') {
	      throw new Exception('The table name cannot be empty!');
	    }
	    return 'DESC ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . $this->sEndLine;
	  }
	  catch (Exception $oException) {
	  	throw $oException;
	  }
	}
	public function generateTableIndexSQL($sTable) {
	  return 'SHOW INDEX FROM ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . " WHERE Key_name <> 'PRIMARY'" . $this->sEndLine;
	}
	public function executeQuery($sQuery) {
		try {
		  if ($this->oConnection) {
		  	@mysql_select_db($this->sDataBase);
		  	return @mysql_query($sQuery);
		  }
		  else {
		  	throw new Exception('Not exists a available connection!');
		  }
	  }
	  catch (Exception $oException) {
	  	throw $oException;
	  }
	}
	public function getRegistry($oDataset) {
	  return @mysql_fetch_array($oDataset, $this->iFetchType);
	}
	public function close() {
		@mysql_close($this->oConnection);
	}
}