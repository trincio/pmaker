<?php

require_once 'classes/model/om/BaseCaseTrackerObject.php';


/**
 * Skeleton subclass for representing a row from the 'CASE_TRACKER_OBJECT' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class CaseTrackerObject extends BaseCaseTrackerObject {
  public function load($Uid) {
    try {
      $oRow = CaseTrackerObjectPeer::retrieveByPK( $Uid );
      if (!is_null($oRow))
      {
        $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
        $this->fromArray($aFields,BasePeer::TYPE_FIELDNAME);
        $this->setNew(false);
        return $aFields;
      }
      else {
        throw( new Exception( "The row '$Uid' in table CaseTrackerObject doesn't exists!" ));
      }
    }
    catch (Exception $oError) {
      throw($oError);
    }
  }

  public function create($aData) {
  	$oConnection = Propel::getConnection(CaseTrackerObjectPeer::DATABASE_NAME);
  	try {
  	  if (!isset($aData['CTO_UID'])) {
  	    $aData['CTO_UID'] = G::generateUniqueID();
  	  }
  	  $oCaseTrackerObject = new CaseTrackerObject();
  	  $oCaseTrackerObject->fromArray($aData, BasePeer::TYPE_FIELDNAME);
  	  if ($oCaseTrackerObject->validate()) {
        $oConnection->begin();
        $iResult = $oCaseTrackerObject->save();
        $oConnection->commit();
        return true;
  	  }
  	  else {
  	  	$sMessage = '';
  	    $aValidationFailures = $oCaseTrackerObject->getValidationFailures();
  	    foreach($aValidationFailures as $oValidationFailure) {
          $sMessage .= $oValidationFailure->getMessage() . '<br />';
        }
        throw(new Exception('The registry cannot be created!<br />'.$sMessage));
  	  }
  	}
    catch (Exception $oError) {
      $oConnection->rollback();
    	throw($oError);
    }
  }

  public function update($aData)
  {
  	$oConnection = Propel::getConnection(CaseTrackerObjectPeer::DATABASE_NAME);
  	try {
      $oCaseTrackerObject = CaseTrackerObjectPeer::retrieveByPK($aData['CTO_UID']);
  	  if (!is_null($oCaseTrackerObject))
  	  {
  	  	$oCaseTrackerObject->fromArray($aData, BasePeer::TYPE_FIELDNAME);
  	    if ($oCaseTrackerObject->validate()) {
  	    	$oConnection->begin();
          $iResult = $oCaseTrackerObject->save();
          $oConnection->commit();
          return $iResult;
  	    }
  	    else {
  	    	$sMessage = '';
  	      $aValidationFailures = $oCaseTrackerObject->getValidationFailures();
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

  public function remove($sCTOUID) {
  	$oConnection = Propel::getConnection(CaseTrackerObjectPeer::DATABASE_NAME);
  	try {
  	  	$oConnection->begin();
  	  	$this->setCtoUid($sCTOUID);
        $iResult = $this->delete();
        $oConnection->commit();
        return $iResult;
    }
    catch (Exception $oError) {
    	$oConnection->rollback();
    	throw($oError);
    }
  }

  function reorderPositions($sProcessUID, $iPosition) {
  	try {
      $oCriteria = new Criteria('workflow');
      $oCriteria->add(CaseTrackerObjectPeer::PRO_UID,      $sProcessUID);
  	  $oCriteria->add(CaseTrackerObjectPeer::CTO_POSITION, $iPosition, '>');
      $oDataset = CaseTrackerObjectPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
        $this->update(array('CTO_UID'       => $aRow['CTO_UID'],
                            'PRO_UID'       => $aRow['PRO_UID'],
                            'CTO_TYPE_OBJ'  => $aRow['CTO_TYPE_OBJ'],
                            'CTO_UID_OBJ'   => $aRow['CTO_UID_OBJ'],
                            'CTO_CONDITION' => $aRow['CTO_CONDITION'],
                            'CTO_POSITION'  => $aRow['CTO_POSITION'] - 1));
      	$oDataset->next();
      }
  	}
  	catch (Exception $oException) {
  		throw $Exception;
  	}
  }

  function caseTrackerObjectExists ( $sUid ) {
    try {
      $oObj = CaseTrackerObjectPeer::retrieveByPk($sUid);
      return (get_class($oObj) == 'CaseTrackerObject');
    }
    catch (Exception $oError) {
    	throw($oError);
    }
  }
} // CaseTrackerObject
