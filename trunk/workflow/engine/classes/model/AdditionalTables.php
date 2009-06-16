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
  public function load($sUID) {
  	try {
  	  $oAdditionalTables = AdditionalTablesPeer::retrieveByPK($sUID);
  	  if (!is_null($oAdditionalTables)) {
  	    $aFields = $oAdditionalTables->toArray(BasePeer::TYPE_FIELDNAME);
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

  function createTableAT($sTableName, $sConnection = 'wf', $aFields = array()) {
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
  	  	  		  $sQuery .= '`' . $aField['sFieldName'] . '` ' . $aField['sType'] . '(' . $aField['iSize'] . ')' . " NOT NULL DEFAULT '',";
  	  	  		break;
  	  	  		case 'TEXT':
  	  	  		  $sQuery .= '`' . $aField['sFieldName'] . '` ' . $aField['sType'] . " NOT NULL DEFAULT '',";
  	  	  		break;
  	  	  		case 'DATE':
  	  	  		  $sQuery .= '`' . $aField['sFieldName'] . '` ' . $aField['sType'] . " NOT NULL DEFAULT '0000-00-00',";
  	  	  		break;
  	  	  		case 'INT':
  	  	  		  $sQuery .= '`' . $aField['sFieldName'] . '` ' . $aField['sType'] . '(' . $aField['iSize'] . ')' . " NOT NULL DEFAULT '0',";
  	  	  		break;
  	  	  		case 'FLOAT':
  	  	  		  $sQuery .= '`' . $aField['sFieldName'] . '` ' . $aField['sType'] . '(' . $aField['iSize'] . ')' . " NOT NULL DEFAULT '0',";
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

  function createPropelClasses($sClassName) {
    try {
      var_dump($sClassName);
    }
  	catch (Exception $oError) {
    	throw($oError);
    }
  }
} // AdditionalTables
