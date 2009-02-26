<?php
/**
 * class.reportTables.php
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

G::LoadClass('case');

/**
 * ReportTables - Report tables class
 * @package ProcessMaker
 * @author Julio Cesar Laura Avendaño
 * @copyright 2007 COLOSA
 */

class ReportTables {
	private $aDef = array('mysql' => array('number' => 'DOUBLE',
	                                       'char'   => 'VARCHAR(255)',
	                                       'text'   => 'TEXT',
	                                       'date'   => 'DATETIME'),
                        'pgsql' => array('number' => 'DOUBLE',
	                                       'char'   => 'VARCHAR(255)',
	                                       'text'   => 'TEXT',
	                                       'date'   => 'DATETIME'));
  //private $sPrefix = 'REP_';
  private $sPrefix = '';
  public function deleteAllReportVars($sRepTabUid = '') {
  	try {
  		$oCriteria = new Criteria('workflow');
  	  $oCriteria->add(ReportVarPeer::REP_TAB_UID, $sRepTabUid);
  	  ReportVarPeer::doDelete($oCriteria);
  	}
  	catch (Exception $oError) {
    	throw($oError);
    }
  }
  public function dropTable($sTableName, $sConnection = 'report') {
  	$sTableName = $this->sPrefix . $sTableName;
  	if ($sConnection == '') {
  		$sConnection = 'report';
  	}
  	$sDBName = 'DB' . ($sConnection != 'wf' ? '_' . strtoupper($sConnection) : '') . '_NAME';
  	$sDBHost = 'DB' . ($sConnection != 'wf' ? '_' . strtoupper($sConnection) : '') . '_HOST';
  	$sDBUser = 'DB' . ($sConnection != 'wf' ? '_' . strtoupper($sConnection) : '') . '_USER';
  	$sDBPass = 'DB' . ($sConnection != 'wf' ? '_' . strtoupper($sConnection) : '') . '_PASS';
  	try {
  	  switch (DB_ADAPTER) {
  	  	case 'mysql':
  	  	case 'pgsql':
  	  	  eval('$oConnection = @mysql_connect(' . $sDBHost . ', ' . $sDBUser . ', ' . $sDBPass . ');');
  	  	  if (!$oConnection) {
  	  	  	throw new Exception('Cannot connect to the server!');
  	  	  }
  	  	  eval("if (!@mysql_select_db($sDBName)) {
  	  	  	throw new Exception('Cannot connect to the database ' . $sDBName . '!');
  	  	  }");
  	  	  if (!@mysql_query('DROP TABLE IF EXISTS `' . $sTableName . '`')) {
  	  	  	throw new Exception('Cannot delete the table "' . $sTableName . '"!');
  	  	  }
  	  	break;
  	  }
  	}
  	catch (Exception $oError) {
    	throw($oError);
    }
  }
  public function createTable($sTableName, $sConnection = 'report', $sType = 'NORMAL', $aFields = array()) {
  	$sTableName = $this->sPrefix . $sTableName;
  	if ($sConnection == '') {
  		$sConnection = 'report';
  	}
  	$sDBName = 'DB' . ($sConnection != 'wf' ? '_' . strtoupper($sConnection) : '') . '_NAME';
  	$sDBHost = 'DB' . ($sConnection != 'wf' ? '_' . strtoupper($sConnection) : '') . '_HOST';
  	$sDBUser = 'DB' . ($sConnection != 'wf' ? '_' . strtoupper($sConnection) : '') . '_USER';
  	$sDBPass = 'DB' . ($sConnection != 'wf' ? '_' . strtoupper($sConnection) : '') . '_PASS';
  	try {
  	  switch (DB_ADAPTER) {
  	  	case 'mysql':
  	  	case 'pgsql':
  	  	  eval('$oConnection = @mysql_connect(' . $sDBHost . ', ' . $sDBUser . ', ' . $sDBPass . ');');
  	  	  if (!$oConnection) {
  	  	  	throw new Exception('Cannot connect to the server!');
  	  	  }
  	  	  eval("if (!@mysql_select_db($sDBName)) {
  	  	  	throw new Exception('Cannot connect to the database ' . $sDBName . '!');
  	  	  }");
  	  	  $sQuery  = 'CREATE TABLE IF NOT EXISTS `' . $sTableName . '` (';
  	  	  $sQuery .= "`APP_UID` VARCHAR(32) NOT NULL DEFAULT '',`APP_NUMBER` INT NOT NULL,";
  	  	  if ($sType == 'GRID') {
  	  	  	$sQuery .= "`ROW` INT NOT NULL,";
  	  	  }
  	  	  foreach ($aFields as $aField) {
  	  	  	switch ($aField['sType']) {
  	  	  		case 'number':
  	  	  		  $sQuery .= '`' . $aField['sFieldName'] . '` ' . $this->aDef['mysql'][$aField['sType']] . " NOT NULL DEFAULT '0',";
  	  	  		break;
  	  	  		case 'char':
  	  	  		case 'text':
  	  	  		  $sQuery .= '`' . $aField['sFieldName'] . '` ' . $this->aDef['mysql'][$aField['sType']] . " NOT NULL DEFAULT '',";
  	  	  		break;
  	  	  		case 'date':
  	  	  		  $sQuery .= '`' . $aField['sFieldName'] . '` ' . $this->aDef['mysql'][$aField['sType']] . " NOT NULL DEFAULT '0000-00-00 00:00:00',";
  	  	  		break;
  	  	  	}
  	  	  }
  	  	  $sQuery .= 'PRIMARY KEY (APP_UID' . ($sType == 'GRID' ? ',ROW' : '') . ')) DEFAULT CHARSET=utf8;';
  	  	  if (!@mysql_query($sQuery)) {
  	  	  	throw new Exception('Cannot create the table "' . $sTableName . '"!');
  	  	  }
  	  	break;
  	  }
  	}
  	catch (Exception $oError) {
    	throw($oError);
    }
  }
  public function populateTable($sTableName, $sConnection = 'report', $sType = 'NORMAL', $aFields = array(), $sProcessUid = '', $sGrid = '') {
  	$sTableName = $this->sPrefix . $sTableName;
  	if ($sConnection == '') {
  		$sConnection = 'report';
  	}
  	$sDBName = 'DB' . ($sConnection != 'wf' ? '_' . strtoupper($sConnection) : '') . '_NAME';
  	$sDBHost = 'DB' . ($sConnection != 'wf' ? '_' . strtoupper($sConnection) : '') . '_HOST';
  	$sDBUser = 'DB' . ($sConnection != 'wf' ? '_' . strtoupper($sConnection) : '') . '_USER';
  	$sDBPass = 'DB' . ($sConnection != 'wf' ? '_' . strtoupper($sConnection) : '') . '_PASS';
  	if ($sType == 'GRID') {
  	  $aAux  = explode('-', $sGrid);
  	  $sGrid = $aAux[0];
  	}
  	try {
  	  switch (DB_ADAPTER) {
  	  	case 'mysql':
  	  	case 'pgsql':
  	  	  eval('$oConnection = @mysql_connect(' . $sDBHost . ', ' . $sDBUser . ', ' . $sDBPass . ');');
  	  	  if (!$oConnection) {
  	  	  	throw new Exception('Cannot connect to the server!');
  	  	  }
  	  	  $oCriteria = new Criteria('workflow');
  	      $oCriteria->add(ApplicationPeer::PRO_UID, $sProcessUid);
  	      $oCriteria->addAscendingOrderByColumn(ApplicationPeer::APP_NUMBER);
  	      $oDataset = ApplicationPeer::doSelectRS($oCriteria);
          $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
          $oDataset->next();
          eval("if (!@mysql_select_db($sDBName)) {
  	  	  	throw new Exception('Cannot connect to the database ' . $sDBName . '!');
  	  	  }");
          while ($aRow = $oDataset->getRow()) {
          	$aData = unserialize($aRow['APP_DATA']);
          	mysql_query('DELETE FROM `' . $sTableName . "` WHERE APP_UID = '" . $aRow['APP_UID'] . "'");
          	if ($sType == 'NORMAL') {
  	  	      $sQuery  = 'INSERT INTO `' . $sTableName . '` (';
  	  	      $sQuery .= '`APP_UID`,`APP_NUMBER`';
  	  	      foreach ($aFields as $aField) {
  	  	      	$sQuery .= ',`' . $aField['sFieldName'] . '`';
  	  	      }
  	  	      $sQuery .= ") VALUES ('" . $aRow['APP_UID'] . "'," . (int)$aRow['APP_NUMBER'];
  	  	      foreach ($aFields as $aField) {
  	  	      	switch ($aField['sType']) {
  	  	      		case 'number':
  	  	      		  $sQuery .= ',' . (isset($aData[$aField['sFieldName']]) ? (float)str_replace(',', '', $aData[$aField['sFieldName']]) : '0');
  	  	      		break;
  	  	      		case 'char':
  	  	      		case 'text':
  	  	      		  if (!isset($aData[$aField['sFieldName']])) {
  	  	      		    $aData[$aField['sFieldName']] = '';
  	  	      		  }
  	  	      		  if (function_exists('mb_detect_encoding')) {
  	  	      		    if (strtoupper(mb_detect_encoding($aData[$aField['sFieldName']])) == 'UTF-8') {
                        $aData[$aField['sFieldName']] = utf8_decode($aData[$aField['sFieldName']]);
  	  	      		    }
  	  	      		  }
  	  	      		  $sQuery .= ",'" . (isset($aData[$aField['sFieldName']]) ? mysql_real_escape_string($aData[$aField['sFieldName']]) : '') . "'";
  	  	      		break;
  	  	      		case 'date':
  	  	      		  $sQuery .= ",'" . (isset($aData[$aField['sFieldName']]) ? $aData[$aField['sFieldName']] : '') . "'";
  	  	      		break;
  	  	      	}
  	  	      }
  	  	      $sQuery .= ')';
  	  	  	  if (!@mysql_query($sQuery, $oConnection)) {
  	  	  	    throw new Exception('Error in insert clause!');
  	  	      }
  	  	    }
  	  	    else {
  	  	    	if (isset($aData[$sGrid])) {
  	  	    	  foreach ($aData[$sGrid] as $iRow => $aGridRow) {
  	  	    	    $sQuery  = 'INSERT INTO `' . $sTableName . '` (';
  	  	          $sQuery .= '`APP_UID`,`APP_NUMBER`,`ROW`';
  	  	          foreach ($aFields as $aField) {
  	  	          	$sQuery .= ',`' . $aField['sFieldName'] . '`';
  	  	          }
  	  	          $sQuery .= ") VALUES ('" . $aRow['APP_UID'] . "'," . (int)$aRow['APP_NUMBER'] . ',' . $iRow;
  	  	          foreach ($aFields as $aField) {
  	  	          	switch ($aField['sType']) {
  	  	          		case 'number':
  	  	          		  $sQuery .= ',' . (isset($aGridRow[$aField['sFieldName']]) ? (float)str_replace(',', '', $aGridRow[$aField['sFieldName']]) : '0');
  	  	          		break;
  	  	          		case 'char':
  	  	          		case 'text':
  	  	          		  if (!isset($aGridRow[$aField['sFieldName']])) {
  	  	      		        $aGridRow[$aField['sFieldName']] = '';
  	  	      		      }
  	  	          		  if (function_exists('mb_detect_encoding')) {
  	  	      		        if (strtoupper(mb_detect_encoding($aGridRow[$aField['sFieldName']])) == 'UTF-8') {
                            $aGridRow[$aField['sFieldName']] = utf8_decode($aGridRow[$aField['sFieldName']]);
  	  	      		        }
  	  	      		      }
  	  	          		  $sQuery .= ",'" . (isset($aGridRow[$aField['sFieldName']]) ? mysql_real_escape_string($aGridRow[$aField['sFieldName']]) : '') . "'";
  	  	          		break;
  	  	          		case 'date':
  	  	          		  $sQuery .= ",'" . (isset($aGridRow[$aField['sFieldName']]) ? $aGridRow[$aField['sFieldName']] : '') . "'";
  	  	          		break;
  	  	          	}
  	  	          }
  	  	          $sQuery .= ')';
  	  	  	      if (!@mysql_query($sQuery, $oConnection)) {
  	  	  	        throw new Exception('Error in insert clause!');
  	  	          }
  	  	    	  }
  	  	    	}
  	  	    }
            $oDataset->next();
          }
  	  	break;
  	  }
  	}
  	catch (Exception $oError) {
    	throw($oError);
    }
  }
  public function getTableVars($sRepTabUid, $bWhitType = false) {
  	try {
  		$oCriteria = new Criteria('workflow');
  	  $oCriteria->add(ReportVarPeer::REP_TAB_UID, $sRepTabUid);
  	  $oDataset = ReportVarPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $aVars = array();
      while ($aRow = $oDataset->getRow()) {
      	if ($bWhitType) {
      		$aVars[] = array('sFieldName' => $aRow['REP_VAR_NAME'], 'sType' => $aRow['REP_VAR_TYPE']);
      	}
      	else {
      	  $aVars[] = $aRow['REP_VAR_NAME'];
        }
      	$oDataset->next();
      }
      return $aVars;
  	}
  	catch (Exception $oError) {
    	throw($oError);
    }
  }
  public function deleteReportTable($sRepTabUid) {
  	try {
  		$oReportTable = new ReportTable();
  		$aFields = $oReportTable->load($sRepTabUid);
  	  $this->dropTable($aFields['REP_TAB_NAME'], $aFields['REP_TAB_CONNECTION']);
  	  $oCriteria = new Criteria('workflow');
  	  $oCriteria->add(ReportVarPeer::REP_TAB_UID, $sRepTabUid);
  	  $oDataset = ReportVarPeer::doDelete($oCriteria);
  	  $oReportTable->remove($sRepTabUid);
  	}
  	catch (Exception $oError) {
    	throw($oError);
    }
  }
  public function updateTables($sProcessUid, $sApplicationUid, $iApplicationNumber, $aFields) {
  	if (!$this->tableExist()) {
  		return;
  	}
  	try {
  		$oCriteria = new Criteria('workflow');
  	  $oCriteria->add(ReportTablePeer::PRO_UID, $sProcessUid);
  	  $oCriteria->add(ReportTablePeer::REP_TAB_STATUS, 'ACTIVE');
  	  $oDataset = ReportTablePeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $aVars = array();
      while ($aRow = $oDataset->getRow()) {
      	$aRow['REP_TAB_NAME'] = $this->sPrefix . $aRow['REP_TAB_NAME'];
      	if ($aRow['REP_TAB_CONNECTION'] == '') {
  	    	$aRow['REP_TAB_CONNECTION'] = 'report';
  	    }
  	    $sDBName = 'DB' . ($aRow['REP_TAB_CONNECTION'] != 'wf' ? '_' . strtoupper($aRow['REP_TAB_CONNECTION']) : '') . '_NAME';
  	    $sDBHost = 'DB' . ($aRow['REP_TAB_CONNECTION'] != 'wf' ? '_' . strtoupper($aRow['REP_TAB_CONNECTION']) : '') . '_HOST';
  	    $sDBUser = 'DB' . ($aRow['REP_TAB_CONNECTION'] != 'wf' ? '_' . strtoupper($aRow['REP_TAB_CONNECTION']) : '') . '_USER';
  	    $sDBPass = 'DB' . ($aRow['REP_TAB_CONNECTION'] != 'wf' ? '_' . strtoupper($aRow['REP_TAB_CONNECTION']) : '') . '_PASS';
  	    switch (DB_ADAPTER) {
  	  	  case 'mysql':
  	  	  case 'pgsql':
  	  	    eval('$oConnection = @mysql_connect(' . $sDBHost . ', ' . $sDBUser . ', ' . $sDBPass . ');');
  	  	    if (!$oConnection) {
  	  	    	throw new Exception('Cannot connect to the server!');
  	  	    }
  	  	    $aTableFields = $this->getTableVars($aRow['REP_TAB_UID'], true);
  	  	    eval("if (!@mysql_select_db($sDBName)) {
  	  	  	  throw new Exception('Cannot connect to the database ' . $sDBName . '!');
  	  	    }");
  	  	    if ($aRow['REP_TAB_TYPE'] == 'NORMAL') {
  	  	    	$oDataset2 = mysql_query("SELECT * FROM `" . $aRow['REP_TAB_NAME'] . "` WHERE APP_UID = '" . $sApplicationUid . "'");
  	  	    	if ($aRow2 = mysql_fetch_row($oDataset2)) {
  	  	    		$sQuery  = 'UPDATE `' . $aRow['REP_TAB_NAME'] . '` SET ';
  	  	    		foreach ($aTableFields as $aField) {
  	  	    			$sQuery .= '`' . $aField['sFieldName'] . '` = ';
  	  	        	switch ($aField['sType']) {
  	  	        		case 'number':
  	  	        		  $sQuery .= (isset($aFields[$aField['sFieldName']]) ? (float)str_replace(',', '', $aFields[$aField['sFieldName']]) : '0') . ',';
  	  	        		break;
  	  	        		case 'char':
  	  	        		case 'text':
  	  	        		  if (!isset($aFields[$aField['sFieldName']])) {
  	  	      		      $aFields[$aField['sFieldName']] = '';
  	  	      		    }
  	  	        		  if (function_exists('mb_detect_encoding')) {
  	  	      		      if (strtoupper(mb_detect_encoding($aFields[$aField['sFieldName']])) == 'UTF-8') {
                          $aFields[$aField['sFieldName']] = utf8_decode($aFields[$aField['sFieldName']]);
  	  	      		      }
  	  	      		    }
  	  	        		  $sQuery .= "'" . (isset($aFields[$aField['sFieldName']]) ? mysql_real_escape_string($aFields[$aField['sFieldName']]) : '') . "',";
  	  	        		break;
  	  	        		case 'date':
  	  	        		  $sQuery .= "'" . (isset($aFields[$aField['sFieldName']]) ? $aFields[$aField['sFieldName']] : '') . "',";
  	  	        		break;
  	  	        	}
  	  	        }
  	  	        $sQuery  = substr($sQuery, 0, -1);
  	  	        $sQuery .= " WHERE APP_UID = '" . $sApplicationUid . "'";
  	  	    	}
  	  	    	else {
  	  	    		$sQuery  = 'INSERT INTO `' . $aRow['REP_TAB_NAME'] . '` (';
  	  	        $sQuery .= '`APP_UID`,`APP_NUMBER`';
  	  	        foreach ($aTableFields as $aField) {
  	  	        	$sQuery .= ',`' . $aField['sFieldName'] . '`';
  	  	        }
  	  	        $sQuery .= ") VALUES ('" . $sApplicationUid . "'," . (int)$iApplicationNumber;
  	  	        foreach ($aTableFields as $aField) {
  	  	        	switch ($aField['sType']) {
  	  	        		case 'number':
  	  	        		  $sQuery .= ',' . (isset($aFields[$aField['sFieldName']]) ? (float)str_replace(',', '', $aFields[$aField['sFieldName']]) : '0');
  	  	        		break;
  	  	        		case 'char':
  	  	        		case 'text':
  	  	        		  if (!isset($aFields[$aField['sFieldName']])) {
  	  	      		      $aFields[$aField['sFieldName']] = '';
  	  	      		    }
  	  	        		  if (function_exists('mb_detect_encoding')) {
  	  	      		      if (strtoupper(mb_detect_encoding($aFields[$aField['sFieldName']])) == 'UTF-8') {
                          $aFields[$aField['sFieldName']] = utf8_decode($aFields[$aField['sFieldName']]);
  	  	      		      }
  	  	      		    }
  	  	        		  $sQuery .= ",'" . (isset($aFields[$aField['sFieldName']]) ? mysql_real_escape_string($aFields[$aField['sFieldName']]) : '') . "'";
  	  	        		break;
  	  	        		case 'date':
  	  	        		  $sQuery .= ",'" . (isset($aFields[$aField['sFieldName']]) ? $aFields[$aField['sFieldName']] : '') . "'";
  	  	        		break;
  	  	        	}
  	  	        }
  	  	        $sQuery .= ')';
  	  	    	}
  	  	    	if (!@mysql_query($sQuery, $oConnection)) {
  	  	  	    throw new Exception('Error in insert clause!'.mysql_error());
  	  	      }
  	  	    }
  	  	    else {
  	  	      mysql_query('DELETE FROM `' . $aRow['REP_TAB_NAME'] . "` WHERE APP_UID = '" . $sApplicationUid . "'");
  	  	      $aAux = explode('-', $aRow['REP_TAB_GRID']);
  	  	      if (isset($aFields[$aAux[0]])) {
  	  	        foreach ($aFields[$aAux[0]] as $iRow => $aGridRow) {
  	  	          $sQuery  = 'INSERT INTO `' . $aRow['REP_TAB_NAME'] . '` (';
  	  	          $sQuery .= '`APP_UID`,`APP_NUMBER`,`ROW`';
  	  	          foreach ($aTableFields as $aField) {
  	  	          	$sQuery .= ',`' . $aField['sFieldName'] . '`';
  	  	          }
  	  	          $sQuery .= ") VALUES ('" . $sApplicationUid . "'," . (int)$iApplicationNumber . ',' . $iRow;
  	  	          foreach ($aTableFields as $aField) {
  	  	          	switch ($aField['sType']) {
  	  	          		case 'number':
  	  	          		  $sQuery .= ',' . (isset($aGridRow[$aField['sFieldName']]) ? (float)str_replace(',', '', $aGridRow[$aField['sFieldName']]) : '0');
  	  	          		break;
  	  	          		case 'char':
  	  	          		case 'text':
  	  	          		  if (!isset($aGridRow[$aField['sFieldName']])) {
  	  	      		        $aGridRow[$aField['sFieldName']] = '';
  	  	      		      }
  	  	          		  if (function_exists('mb_detect_encoding')) {
  	  	      		        if (strtoupper(mb_detect_encoding($aGridRow[$aField['sFieldName']])) == 'UTF-8') {
                            $aGridRow[$aField['sFieldName']] = utf8_decode($aGridRow[$aField['sFieldName']]);
  	  	      		        }
  	  	      		      }
  	  	          		  $sQuery .= ",'" . (isset($aGridRow[$aField['sFieldName']]) ? mysql_real_escape_string($aGridRow[$aField['sFieldName']]) : '') . "'";
  	  	          		break;
  	  	          		case 'date':
  	  	          		  $sQuery .= ",'" . (isset($aGridRow[$aField['sFieldName']]) ? $aGridRow[$aField['sFieldName']] : '') . "'";
  	  	          		break;
  	  	          	}
  	  	          }
  	  	          $sQuery .= ')';
  	  	  	      if (!@mysql_query($sQuery, $oConnection)) {
  	  	  	        throw new Exception('Error in insert clause!');
  	  	          }
  	  	        }
  	  	      }
  	  	    }
  	  	  break;
  	  	}
      	$oDataset->next();
      }
  	}
  	catch (Exception $oError) {
    	throw($oError);
    }
  }
  function tableExist() {
  	$bExists  = true;
  	$oConnection = mysql_connect(DB_HOST, DB_USER, DB_PASS);
  	mysql_select_db(DB_NAME);
    $oDataset = mysql_query('SELECT COUNT(*) FROM REPORT_TABLE') || ($bExists = false);
  	return $bExists;
  }
}