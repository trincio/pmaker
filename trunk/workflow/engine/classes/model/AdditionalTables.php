<?php

require_once 'classes/model/om/BaseAdditionalTables.php';


/**
 * Skeleton subclass for representing a row from the 'ADDITIONAL_TABLES' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class AdditionalTables extends BaseAdditionalTables {
  public function load($sUID, $bFields = false) {
  	try {
  	  $oAdditionalTables = AdditionalTablesPeer::retrieveByPK($sUID);
  	  if (!is_null($oAdditionalTables)) {
  	    $aFields = $oAdditionalTables->toArray(BasePeer::TYPE_FIELDNAME);
  	    if ($bFields) {
  	      require_once 'classes/model/Fields.php';
          $oCriteria = new Criteria('workflow');
          $oCriteria->addSelectColumn(FieldsPeer::FLD_UID);
          $oCriteria->addSelectColumn(FieldsPeer::FLD_NAME);
          $oCriteria->addSelectColumn(FieldsPeer::FLD_DESCRIPTION);
          $oCriteria->addSelectColumn(FieldsPeer::FLD_TYPE);
          $oCriteria->addSelectColumn(FieldsPeer::FLD_SIZE);
          $oCriteria->addSelectColumn(FieldsPeer::FLD_NULL);
          $oCriteria->addSelectColumn(FieldsPeer::FLD_AUTO_INCREMENT);
          $oCriteria->addSelectColumn(FieldsPeer::FLD_KEY);
          $oCriteria->addSelectColumn(FieldsPeer::FLD_FOREIGN_KEY);
          $oCriteria->addSelectColumn(FieldsPeer::FLD_FOREIGN_KEY_TABLE);
          $oCriteria->add(FieldsPeer::ADD_TAB_UID, $sUID);
          $oDataset = FieldsPeer::doSelectRS($oCriteria);
          $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
          $oDataset->next();
          $aFields['FIELDS'] = array();
          $i = 1;
          while ($aRow = $oDataset->getRow()) {
            $aFields['FIELDS'][$i] = $aRow;
            $oDataset->next();
            $i++;
          }
  	    }
        $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME);
  	    return $aFields;
      }
      else {
        throw(new Exception('This row doesn\'t exists!'));
      }
    }
    catch (Exception $oError) {
    	throw($oError);
    }
  }

  function create($aData) {
    if (!isset($aData['ADD_TAB_UID'])) {
      $aData['ADD_TAB_UID'] = G::generateUniqueID();
    }
    else {
      if ($aData['ADD_TAB_UID'] == '') {
        $aData['ADD_TAB_UID'] = G::generateUniqueID();
      }
    }
    $oConnection = Propel::getConnection(AdditionalTablesPeer::DATABASE_NAME);
  	try {
  	  $oAdditionalTables = new AdditionalTables();
  	  $oAdditionalTables->fromArray($aData, BasePeer::TYPE_FIELDNAME);
  	  if ($oAdditionalTables->validate()) {
        $oConnection->begin();
        $iResult = $oAdditionalTables->save();
        $oConnection->commit();
        return $aData['ADD_TAB_UID'];
  	  }
  	  else {
  	  	$sMessage = '';
  	    $aValidationFailures = $oAdditionalTables->getValidationFailures();
  	    foreach($aValidationFailures as $oValidationFailure) {
          $sMessage .= $oValidationFailure->getMessage() . '<br />';
        }
        throw(new Exception('The registry cannot be created!<br />' . $sMessage));
  	  }
  	}
    catch (Exception $oError) {
      $oConnection->rollback();
    	throw($oError);
    }
  }

  function update($aData) {
    $oConnection = Propel::getConnection(AdditionalTablesPeer::DATABASE_NAME);
  	try {
  	  $oAdditionalTables = AdditionalTablesPeer::retrieveByPK($aData['ADD_TAB_UID']);
  	  if (!is_null($oAdditionalTables)) {
  	  	$oAdditionalTables->fromArray($aData, BasePeer::TYPE_FIELDNAME);
  	    if ($oAdditionalTables->validate()) {
  	    	$oConnection->begin();
          $iResult = $oAdditionalTables->save();
          $oConnection->commit();
          return $iResult;
  	    }
  	    else {
  	    	$sMessage = '';
  	      $aValidationFailures = $oAdditionalTables->getValidationFailures();
  	      foreach($aValidationFailures as $oValidationFailure) {
            $sMessage .= $oValidationFailure->getMessage() . '<br />';
          }
          throw(new Exception('The registry cannot be updated!<br />'.$sMessage));
  	    }
      }
      else {
        throw(new Exception('This row doesn\'t exists!'));
      }
    }
    catch (Exception $oError) {
    	$oConnection->rollback();
    	throw($oError);
    }
  }

  function remove($sUID) {
    $oConnection = Propel::getConnection(AdditionalTablesPeer::DATABASE_NAME);
  	try {
  	  $oAdditionalTables = AdditionalTablesPeer::retrieveByPK($sUID);
  	  if (!is_null($oAdditionalTables)) {
  	  	$oConnection->begin();
        $iResult = $oAdditionalTables->delete();
        $oConnection->commit();
        return $iResult;
      }
      else {
        throw(new Exception('This row doesn\'t exists!'));
      }
    }
    catch (Exception $oError) {
    	$oConnection->rollback();
      throw($oError);
    }
  }

  function createTable($sTableName, $sConnection = 'wf', $aFields = array()) {
  	$sTableName = $sTableName;
  	if ($sConnection == '') {
  		$sConnection = 'wf';
  	}
  	$sDBName = 'DB' . ($sConnection != 'wf' ? '_' . strtoupper($sConnection) : '') . '_NAME';
  	$sDBHost = 'DB' . ($sConnection != 'wf' ? '_' . strtoupper($sConnection) : '') . '_HOST';
  	$sDBUser = 'DB' . ($sConnection != 'wf' ? '_' . strtoupper($sConnection) : '') . '_USER';
  	$sDBPass = 'DB' . ($sConnection != 'wf' ? '_' . strtoupper($sConnection) : '') . '_PASS';
  	try {
  	  switch (DB_ADAPTER) {
  	  	case 'mysql':
  	  	  eval('$oConnection = @mysql_connect(' . $sDBHost . ', ' . $sDBUser . ', ' . $sDBPass . ');');
  	  	  if (!$oConnection) {
  	  	  	throw new Exception('Cannot connect to the server!');
  	  	  }
  	  	  eval("if (!@mysql_select_db($sDBName)) {
  	  	  	throw new Exception('Cannot connect to the database ' . $sDBName . '!');
  	  	  }");
  	  	  $sQuery = 'CREATE TABLE IF NOT EXISTS `' . $sTableName . '` (';
  	  	  $aPKs   = array();
  	  	  foreach ($aFields as $aField) {
  	  	  	switch ($aField['sType']) {
  	  	  		case 'VARCHAR':
  	  	  		  $sQuery .= '`' . $aField['sFieldName'] . '` ' . $aField['sType'] . '(' . $aField['iSize'] . ')' . " " . ($aField['bNull'] ? 'NULL' : 'NOT NULL') . " DEFAULT '',";
  	  	  		break;
  	  	  		case 'TEXT':
  	  	  		  $sQuery .= '`' . $aField['sFieldName'] . '` ' . $aField['sType'] . " " . ($aField['bNull'] ? 'NULL' : 'NOT NULL') . " DEFAULT '',";
  	  	  		break;
  	  	  		case 'DATE':
  	  	  		  $sQuery .= '`' . $aField['sFieldName'] . '` ' . $aField['sType'] . " " . ($aField['bNull'] ? 'NULL' : 'NOT NULL') . " DEFAULT '0000-00-00',";
  	  	  		break;
  	  	  		case 'INT':
  	  	  		  $sQuery .= '`' . $aField['sFieldName'] . '` ' . $aField['sType'] . '(' . $aField['iSize'] . ')' . " " . ($aField['bNull'] ? 'NULL' : 'NOT NULL') . ' ' . ($aField['bAI'] ? 'AUTO_INCREMENT' : "DEFAULT '0'") . ',';
  	  	  		  array_unshift($aPKs, $aField['sFieldName']);
  	  	  		break;
  	  	  		case 'FLOAT':
  	  	  		  $sQuery .= '`' . $aField['sFieldName'] . '` ' . $aField['sType'] . '(' . $aField['iSize'] . ')' . " " . ($aField['bNull'] ? 'NULL' : 'NOT NULL') . " DEFAULT '0',";
  	  	  		break;
  	  	  	}
  	  	  	if ($aField['bPrimaryKey'] == 1) {
  	  	  	  $aPKs[] = $aField['sFieldName'];
  	  	  	}
  	  	  }
  	  	  $sQuery  = substr($sQuery, 0, -1);
  	  	  if (!empty($aPKs)) {
  	  	    $sQuery .= ',PRIMARY KEY (' . implode(',', $aPKs) . ')';
  	  	  }
  	  	  $sQuery .= ') DEFAULT CHARSET=utf8;';
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

  function updateTable($sTableName, $sConnection = 'wf', $aNewFields = array(), $aOldFields = array()) {
    try {
      $aKeys           = array();
      $aFieldsToAdd    = array();
      $aFieldsToDelete = array();
      $aFieldsToAlter  = array();
      foreach ($aNewFields as $aNewField) {
        if (!isset($aOldFields[$aNewField['FLD_UID']])) {
          $aFieldsToAdd[] = $aNewField;
        }
        if (($aNewField['FLD_KEY'] == 'on') || ($aNewField['FLD_AUTO_INCREMENT'] == 'on')) {
          $aKeys[] = $aNewField['FLD_NAME'];
        }
      }
      foreach ($aOldFields as $aOldField) {
        if (!isset($aNewFields[$aOldField['FLD_UID']])) {
          $aFieldsToDelete[] = $aOldField;
        }
      }
      foreach ($aNewFields as $aNewField) {
        if (isset($aOldFields[$aNewField['FLD_UID']])) {
          $bEqual = true;
          if (trim($aNewField['FLD_NAME']) != trim($aOldField['FLD_NAME'])) {
            $bEqual = false;
          }
          if (trim($aNewField['FLD_TYPE']) != trim($aOldField['FLD_TYPE'])) {
            $bEqual = false;
          }
          if (trim($aNewField['FLD_SIZE']) != trim($aOldField['FLD_SIZE'])) {
            $bEqual = false;
          }
          if (trim($aNewField['FLD_NULL']) != trim($aOldField['FLD_NULL'])) {
            $bEqual = false;
          }
          if (trim($aNewField['FLD_AUTO_INCREMENT']) != trim($aOldField['FLD_AUTO_INCREMENT'])) {
            $bEqual = false;
          }
          if (trim($aNewField['FLD_KEY']) != trim($aOldField['FLD_KEY'])) {
            $bEqual = false;
          }
          if (!$bEqual) {
            $aNewField['FLD_NAME_OLD'] = $aOldFields[$aNewField['FLD_UID']]['FLD_NAME'];
            $aFieldsToAlter[] = $aNewField;
          }
        }
      }
      G::LoadSystem('database_' . strtolower(DB_ADAPTER));
      $oDataBase = new database(DB_ADAPTER, DB_HOST, DB_USER, DB_PASS, DB_NAME);
      $oDataBase->iFetchType = MYSQL_NUM;
      $oDataBase->executeQuery($oDataBase->generateDropPrimaryKeysSQL($sTableName));
      foreach ($aFieldsToAdd as $aFieldToAdd) {
        switch ($aFieldToAdd['FLD_TYPE']) {
          case 'VARCHAR':
            $aData = array('Type'    => 'VARCHAR(' . $aFieldToAdd['FLD_SIZE'] . ')',
                           'Null'    => ($aFieldToAdd['FLD_NULL'] == 'on' ? 'YES' : ''),
                           'Default' => '');
          break;
          case 'TEXT':
            $aData = array('Type'    => 'TEXT',
                           'Null'    => ($aFieldToAdd['FLD_NULL'] == 'on' ? 'YES' : ''),
                           'Default' => '');
          break;
          case 'DATE':
            $aData = array('Type'    => 'DATE',
                           'Null'    => ($aFieldToAdd['FLD_NULL'] == 'on' ? 'YES' : ''),
                           'Default' => '0000-00-00');
          break;
          case 'INT':
            $aData = array('Type'    => 'INT(' . (int)$aFieldToAdd['FLD_SIZE'] . ')',
                           'Null'    => ($aFieldToAdd['FLD_NULL'] == 'on' ? 'YES' : ''),
                           'Default' => '0',
                           'AI'      => ($aFieldToAdd['FLD_AUTO_INCREMENT'] == 'on' ? 1 : 0));
          break;
          case 'FLOAT':
            $aData = array('Type'    => 'FLOAT(' . (int)$aFieldToAdd['FLD_SIZE'] . ')',
                           'Null'    => ($aFieldToAdd['FLD_NULL'] == 'on' ? 'YES' : ''),
                           'Default' => '0');
          break;
        }
        $oDataBase->executeQuery($oDataBase->generateAddColumnSQL($sTableName, $aFieldToAdd['FLD_NAME'], $aData));
      }
      foreach ($aFieldsToDelete as $aFieldToDelete) {
        $oDataBase->executeQuery($oDataBase->generateDropColumnSQL($sTableName, $aFieldToDelete['FLD_NAME']));
      }
      $oDataBase->executeQuery($oDataBase->generateAddPrimaryKeysSQL($sTableName, $aKeys));
      foreach ($aFieldsToAlter as $aFieldToAlter) {
        switch ($aFieldToAlter['FLD_TYPE']) {
          case 'VARCHAR':
            $aData = array('Type'    => 'VARCHAR(' . $aFieldToAlter['FLD_SIZE'] . ')',
                           'Null'    => ($aFieldToAlter['FLD_NULL'] == 'on' ? 'YES' : ''),
                           'Default' => '');
          break;
          case 'TEXT':
            $aData = array('Type'    => 'TEXT',
                           'Null'    => ($aFieldToAlter['FLD_NULL'] == 'on' ? 'YES' : ''),
                           'Default' => '');
          break;
          case 'DATE':
            $aData = array('Type'    => 'DATE',
                           'Null'    => ($aFieldToAlter['FLD_NULL'] == 'on' ? 'YES' : ''),
                           'Default' => '0000-00-00');
          break;
          case 'INT':
            $aData = array('Type'    => 'INT(' . (int)$aFieldToAlter['FLD_SIZE'] . ')',
                           'Null'    => ($aFieldToAlter['FLD_NULL'] == 'on' ? 'YES' : ''),
                           'Default' => '0',
                           'AI'      => ($aFieldToAlter['FLD_AUTO_INCREMENT'] == 'on' ? 1 : 0));
          break;
          case 'FLOAT':
            $aData = array('Type'    => 'FLOAT(' . (int)$aFieldToAlter['FLD_SIZE'] . ')',
                           'Null'    => ($aFieldToAlter['FLD_NULL'] == 'on' ? 'YES' : ''),
                           'Default' => '0');
          break;
        }
        $oDataBase->executeQuery($oDataBase->generateChangeColumnSQL($sTableName, $aFieldToAlter['FLD_NAME'], $aData, $aFieldToAlter['FLD_NAME_OLD']));
      }
    }
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

  function createPropelClasses($sTableName, $sClassName, $aFields) {
    try {
      $aTypes = array('VARCHAR' => 'string',
                      'TEXT'    => 'string',
                      'DATE'    => 'int',
                      'INT'     => 'int',
                      'FLOAT'   => 'double');
      $aCreoleTypes = array('VARCHAR' => 'VARCHAR',
                            'TEXT'    => 'LONGVARCHAR',
                            'DATE'    => 'TIMESTAMP',
                            'INT'     => 'INTEGER',
                            'FLOAT'   => 'DOUBLE');
      if ($sClassName == '') {
        $sClassName = $this->getPHPName($sTableName);
      }
      $sPath = PATH_DB . SYS_SYS . PATH_SEP . 'classes' . PATH_SEP;
      if (!file_exists($sPath)) {
        G::mk_dir($sPath);
        G::mk_dir($sPath . 'map');
        G::mk_dir($sPath . 'om');
      }
      $aData = array();
      $aData['pathClasses']    = substr(PATH_DB, 0, -1);
      $aData['tableName']      = $sTableName;
      $aData['className']      = $sClassName;
      $aData['firstColumn']    = $aFields[1]['FLD_NAME'];
      $aData['totalColumns']   = count($aFields);
      $aData['useIdGenerator'] = 'false';
      $oTP1  = new TemplatePower(PATH_TPL . 'additionalTables' . PATH_SEP . 'Table.tpl');
      $oTP1->prepare();
      $oTP1->assignGlobal($aData);
      file_put_contents($sPath . $sClassName . '.php', $oTP1->getOutputContent());
      $oTP2  = new TemplatePower(PATH_TPL . 'additionalTables' . PATH_SEP . 'TablePeer.tpl');
      $oTP2->prepare();
      $oTP2->assignGlobal($aData);
      file_put_contents($sPath . $sClassName . 'Peer.php', $oTP2->getOutputContent());
      $aColumns = array();
      $aPKs     = array();
      $aNotPKs  = array();
      $i        = 0;
      foreach($aFields as $iKey => $aField) {
        $aColumn    = array('name'        => $aField['FLD_NAME'],
                            'phpName'     => $this->getPHPName($aField['FLD_NAME']),
                            'type'        => $aTypes[$aField['FLD_TYPE']],
                            'creoleType'  => $aCreoleTypes[$aField['FLD_TYPE']],
                            'notNull'     => ($aField['FLD_NULL'] == 'on' ? 'true' : 'false'),
                            'size'        => (($aField['FLD_TYPE'] == 'VARCHAR') || ($aField['FLD_TYPE'] == 'INT') || ($aField['FLD_TYPE'] == 'FLOAT') ? $aField['FLD_SIZE'] : 'null'),
                            'var'         => strtolower($aField['FLD_NAME']),
                            'attribute'   => (($aField['FLD_TYPE'] == 'VARCHAR') || ($aField['FLD_TYPE'] == 'TEXT') || ($aField['FLD_TYPE'] == 'DATE') ? '$' . strtolower($aField['FLD_NAME']) . " = ''" : '$' . strtolower($aField['FLD_NAME']) . ' = 0'),
                            'index'       => $i,
                            );
        if ($aField['FLD_TYPE'] == 'DATE') {
          $aColumn['getFunction'] = '/**
	 * Get the [optionally formatted] [' . $aColumn['var'] . '] column value.
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function get' . $aColumn['phpName'] . '($format = "Y-m-d H:i:s")
	{

		if ($this->' . $aColumn['var'] . ' === null || $this->' . $aColumn['var'] . ' === "") {
			return null;
		} elseif (!is_int($this->' . $aColumn['var'] . ')) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->' . $aColumn['var'] . ');
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [' . $aColumn['var'] . '] as date/time value: " . var_export($this->' . $aColumn['var'] . ', true));
			}
		} else {
			$ts = $this->' . $aColumn['var'] . ';
		}
		if ($format === null) {
			return $ts;
		} elseif (strpos($format, "%") !== false) {
			return strftime($format, $ts);
		} else {
			return date($format, $ts);
		}
	}';
        }
        else {
          $aColumn['getFunction'] = '/**
	 * Get the [' . $aColumn['var'] . '] column value.
	 *
	 * @return     string
	 */
	public function get' . $aColumn['phpName'] . '()
	{

		return $this->' . $aColumn['var'] . ';
	}';
        }
        switch ($aField['FLD_TYPE']) {
          case 'VARCHAR':
          case 'TEXT':
            $aColumn['setFunction'] = '// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v;
		}

		if ($this->' . $aColumn['var'] . ' !== $v) {
			$this->' . $aColumn['var'] . ' = $v;
			$this->modifiedColumns[] = ' . $aData['className'] . 'Peer::' . $aColumn['name'] . ';
		}';
          break;
          case 'DATE':
            $aColumn['setFunction'] = 'if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [' . $aColumn['var'] . '] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->' . $aColumn['var'] . ' !== $ts) {
			$this->' . $aColumn['var'] . ' = $ts;
			$this->modifiedColumns[] = ' . $aData['className'] . 'Peer::' . $aColumn['name'] . ';
		}';
          break;
          case 'INT':
            $aColumn['setFunction'] = '// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->' . $aColumn['var'] . ' !== $v || $v === 1) {
			$this->' . $aColumn['var'] . ' = $v;
			$this->modifiedColumns[] = ' . $aData['className'] . 'Peer::' . $aColumn['name'] . ';
		}';
          break;
          case 'FLOAT':
            $aColumn['setFunction'] = 'if ($this->' . $aColumn['var'] . ' !== $v || $v === 0) {
			$this->' . $aColumn['var'] . ' = $v;
			$this->modifiedColumns[] = ' . $aData['className'] . 'Peer::' . $aColumn['name'] . ';
		}';
          break;
        }
        $aColumns[] = $aColumn;
        if ($aField['FLD_KEY'] == 'on') {
          $aPKs[] = $aColumn;
        }
        else {
          $aNotPKs[] = $aColumn;
        }
        if ($aField['FLD_AUTO_INCREMENT'] == 'on') {
          $aData['useIdGenerator'] = 'true';
        }
        $i++;
        //var_dump($aField);echo "\n";
      }
      $oTP3  = new TemplatePower(PATH_TPL . 'additionalTables' . PATH_SEP . 'map' . PATH_SEP . 'TableMapBuilder.tpl');
      $oTP3->prepare();
      $oTP3->assignGlobal($aData);
      foreach ($aPKs as $iIndex => $aColumn) {
        $oTP3->newBlock('primaryKeys');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP3->assign($sKey, $aColumn[$sKey]);
        }
      }
      $oTP3->gotoBlock('_ROOT');
      foreach ($aNotPKs as $iIndex => $aColumn) {
        $oTP3->newBlock('columnsWhitoutKeys');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP3->assign($sKey, $aColumn[$sKey]);
        }
      }
      file_put_contents($sPath . PATH_SEP . 'map' . PATH_SEP . $sClassName . 'MapBuilder.php', $oTP3->getOutputContent());
      $oTP4  = new TemplatePower(PATH_TPL . 'additionalTables' . PATH_SEP . 'om' . PATH_SEP . 'BaseTable.tpl');
      $oTP4->prepare();
      switch (count($aPKs)) {
        case 0:
          $aData['getPrimaryKeyFunction'] = 'return null;';
          $aData['setPrimaryKeyFunction'] = '';
        break;
        case 1:
          $aData['getPrimaryKeyFunction'] = 'return $this->get' . $aPKs[0]['phpName'] . '();';
          $aData['setPrimaryKeyFunction'] = '$this->set' . $aPKs[0]['phpName'] . '($key);';
        break;
        default:
          $aData['getPrimaryKeyFunction'] = '$pks = array();' . "\n";
          $aData['setPrimaryKeyFunction'] = '';
          foreach ($aPKs as $iIndex => $aColumn) {
            $aData['getPrimaryKeyFunction'] .= '$pks[' . $iIndex . '] = $this->get' . $aColumn['phpName'] . '();' . "\n";
            $aData['setPrimaryKeyFunction'] .= '$this->set' . $aColumn['phpName'] . '($keys[' . $iIndex . ']);' . "\n";
          }
          $aData['getPrimaryKeyFunction'] .= 'return $pks;' . "\n";
        break;
      }
      $oTP4->assignGlobal($aData);
      foreach ($aColumns as $iIndex => $aColumn) {
        $oTP4->newBlock('allColumns1');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP4->assign($sKey, $aColumn[$sKey]);
        }
        $oTP4->newBlock('allColumns2');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP4->assign($sKey, $aColumn[$sKey]);
        }
        $oTP4->newBlock('allColumns3');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP4->assign($sKey, $aColumn[$sKey]);
        }
        $oTP4->newBlock('allColumns4');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP4->assign($sKey, $aColumn[$sKey]);
        }
        $oTP4->newBlock('allColumns5');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP4->assign($sKey, $aColumn[$sKey]);
        }
        $oTP4->newBlock('allColumns6');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP4->assign($sKey, $aColumn[$sKey]);
        }
        $oTP4->newBlock('allColumns7');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP4->assign($sKey, $aColumn[$sKey]);
        }
        $oTP4->newBlock('allColumns8');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP4->assign($sKey, $aColumn[$sKey]);
        }
        $oTP4->newBlock('allColumns9');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP4->assign($sKey, $aColumn[$sKey]);
        }
      }
      $oTP4->gotoBlock('_ROOT');
      foreach ($aPKs as $iIndex => $aColumn) {
        $oTP4->newBlock('primaryKeys1');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP4->assign($sKey, $aColumn[$sKey]);
        }
      }
      $oTP4->gotoBlock('_ROOT');
      foreach ($aPKs as $iIndex => $aColumn) {
        $oTP4->newBlock('primaryKeys2');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP4->assign($sKey, $aColumn[$sKey]);
        }
      }
      $oTP4->gotoBlock('_ROOT');
      foreach ($aNotPKs as $iIndex => $aColumn) {
        $oTP4->newBlock('columnsWhitoutKeys');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP4->assign($sKey, $aColumn[$sKey]);
        }
      }
      file_put_contents($sPath . PATH_SEP . 'om' . PATH_SEP . 'Base' . $sClassName . '.php', $oTP4->getOutputContent());
      $oTP5  = new TemplatePower(PATH_TPL . 'additionalTables' . PATH_SEP . 'om' . PATH_SEP . 'BaseTablePeer.tpl');
      $oTP5->prepare();
      $oTP5->assignGlobal($aData);
      foreach ($aColumns as $iIndex => $aColumn) {
        $oTP5->newBlock('allColumns1');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP5->assign($sKey, $aColumn[$sKey]);
        }
        $oTP5->newBlock('allColumns2');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP5->assign($sKey, $aColumn[$sKey]);
        }
        $oTP5->newBlock('allColumns3');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP5->assign($sKey, $aColumn[$sKey]);
        }
        $oTP5->newBlock('allColumns4');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP5->assign($sKey, $aColumn[$sKey]);
        }
        $oTP5->newBlock('allColumns5');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP5->assign($sKey, $aColumn[$sKey]);
        }
        $oTP5->newBlock('allColumns6');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP5->assign($sKey, $aColumn[$sKey]);
        }
        $oTP5->newBlock('allColumns7');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP5->assign($sKey, $aColumn[$sKey]);
        }
        $oTP5->newBlock('allColumns8');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP5->assign($sKey, $aColumn[$sKey]);
        }
        $oTP5->newBlock('allColumns9');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP5->assign($sKey, $aColumn[$sKey]);
        }
        $oTP5->newBlock('allColumns10');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP5->assign($sKey, $aColumn[$sKey]);
        }
      }
      $oTP5->gotoBlock('_ROOT');
      foreach ($aPKs as $iIndex => $aColumn) {
        $oTP5->newBlock('primaryKeys');
        $aKeys = array_keys($aColumn);
        foreach ($aKeys as $sKey) {
          $oTP5->assign($sKey, $aColumn[$sKey]);
        }
      }
      file_put_contents($sPath . PATH_SEP . 'om' . PATH_SEP . 'Base' . $sClassName . 'Peer.php', $oTP5->getOutputContent());
    }
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

  function getPHPName($sName) {
    $sName = trim($sName);
    $aAux  = explode('_', $sName);
    foreach ($aAux as $iKey => $sPart) {
      /*if ($iKey == 0) {
        $aAux[$iKey] = strtolower($sPart);
      }
      else {*/
        $aAux[$iKey] = ucwords(strtolower($sPart));
      //}
    }
    return implode('', $aAux);
  }

  function deleteAll($sUID) {
    try {
      $aData = $this->load($sUID);
      $this->remove($sUID);
      require_once 'classes/model/Fields.php';
      $oCriteria = new Criteria('workflow');
      $oCriteria->add(FieldsPeer::ADD_TAB_UID, $sUID);
      FieldsPeer::doDelete($oCriteria);
      G::LoadSystem('database_' . strtolower(DB_ADAPTER));
      $oDataBase = new database(DB_ADAPTER, DB_HOST, DB_USER, DB_PASS, DB_NAME);
      $oDataBase->iFetchType = MYSQL_NUM;
      $oDataBase->executeQuery($oDataBase->generateDropTableSQL($aData['ADD_TAB_NAME']));
      $sClassName = $this->getPHPName($aData['ADD_TAB_CLASS_NAME'] != '' ? $aData['ADD_TAB_CLASS_NAME'] : $aData['ADD_TAB_NAME']);
      $sPath = PATH_DB . SYS_SYS . PATH_SEP . 'classes' . PATH_SEP;
      @unlink($sPath . $sClassName . '.php');
      @unlink($sPath . $sClassName . 'Peer.php');
      @unlink($sPath . PATH_SEP . 'map' . PATH_SEP . $sClassName . 'MapBuilder.php');
      @unlink($sPath . PATH_SEP . 'om' . PATH_SEP . 'Base' . $sClassName . '.php');
      @unlink($sPath . PATH_SEP . 'om' . PATH_SEP . 'Base' . $sClassName . 'Peer.php');
    }
  	catch (Exception $oError) {
    	throw($oError);
    }
  }
} // AdditionalTables
