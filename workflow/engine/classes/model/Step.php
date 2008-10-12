<?php
/**
 * Step.php
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

require_once 'classes/model/om/BaseStep.php';


/**
 * Skeleton subclass for representing a row from the 'STEP' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class Step extends BaseStep {
  function create($aData)
  {
    $con = Propel::getConnection(StepPeer::DATABASE_NAME);
    try
    {
      if ( isset ( $aData['STEP_UID'] ) && $aData['STEP_UID']== '' )
        unset ( $aData['STEP_UID'] );
      if ( isset ( $aData['STEP_UID'] ) )
        $sStepUID = $aData['STEP_UID'];
      else
    	  $sStepUID = G::generateUniqueID();

      $con->begin();
      $this->setStepUid($sStepUID);
      $this->setProUid($aData['PRO_UID']);
      $this->setTasUid($aData['TAS_UID']);

      if (isset ( $aData['STEP_TYPE_OBJ'] ))
        $this->setStepTypeObj( $aData['STEP_TYPE_OBJ'] );
      else
        $this->setStepTypeObj("DYNAFORM");

      if (isset ( $aData['STEP_UID_OBJ'] ))
        $this->setStepUidObj( $aData['STEP_UID_OBJ'] );
      else
        $this->setStepUidObj("");

      if (isset ( $aData['STEP_CONDITION'] ))
        $this->setStepCondition( $aData['STEP_CONDITION'] );
      else
        $this->setStepCondition("");

      if (isset ( $aData['STEP_POSITION'] ))
        $this->setStepPosition( $aData['STEP_POSITION'] );
      else
        $this->setStepPosition("");
      if($this->validate())
      {
        $result=$this->save();
        $con->commit();
        return $sStepUID;
      }
      else
      {
        $con->rollback();
        throw(new Exception("Failed Validation in class ".get_class($this)."."));
      }
    }
    catch(Exception $e)
    {
      $con->rollback();
      throw($e);
    }
  }

  public function load($StepUid)
  {
    try {
      $oRow = StepPeer::retrieveByPK( $StepUid );
      if (!is_null($oRow))
      {
        $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
        $this->fromArray($aFields,BasePeer::TYPE_FIELDNAME);
        $this->setNew(false);
        return $aFields;
      }
      else {
        throw( new Exception( "The row '$StepUid' in table StepUid doesn't exists!" ));
      }
    }
    catch (Exception $oError) {
      throw($oError);
    }
  }

  public function loadByProcessTaskPosition($sProUid, $sTasUid, $sPosition )
  {
    try {
      $c = new Criteria('workflow');
      $c->add ( StepPeer::PRO_UID, $sProUid );
      $c->add ( StepPeer::TAS_UID, $sTasUid );
      $c->add ( StepPeer::STEP_POSITION, $sPosition );
      $rs = StepPeer::doSelect( $c );
      if (!is_null($rs) && !is_null($rs[0]))
      {
        return $rs[0];
      }
      else {
        return null;
      }
    }
    catch (Exception $oError) {
      throw($oError);
    }
  }

  /*
	* Load the step information using the Task UID, the type and the object UID
	* @param string $sTaskUID
	* @param string $sType
	* @param string $sUID
	* @return variant
	*/
  public function loadByType($sTasUid, $sType, $sUid )
  {
    try {
      $c = new Criteria('workflow');
      $c->add ( StepPeer::TAS_UID, $sTasUid );
      $c->add ( StepPeer::STEP_TYPE_OBJ, $sType );
      $c->add ( StepPeer::STEP_UID_OBJ,  $sUid );
      $rs = StepPeer::doSelect( $c );
      if (!is_null($rs) && !is_null($rs[0]))
      {
        return $rs[0];
      }
      else {
        throw( new Exception( "You tried to call to loadByType method without send the Task UID or Type or Object UID !" ));
      }
    }
    catch (Exception $oError) {
      throw($oError);
    }
  }

  /*
	* update the step information using an array with all values
	* @param array $fields
	* @return variant
	*/
  function update($fields)
  {
    $con = Propel::getConnection(StepPeer::DATABASE_NAME);
    try
    {
      $con->begin();
      $this->load($fields['STEP_UID']);
      $this->fromArray($fields,BasePeer::TYPE_FIELDNAME);
      if($this->validate())
      {
        $result=$this->save();
        $con->commit();
        return $result;
      }
      else
      {
        $con->rollback();
        throw(new Exception("Failed Validation in class ".get_class($this)."."));
      }
    }
    catch(Exception $e)
    {
      $con->rollback();
      throw($e);
    }
  }
  function remove($sStepUID)
  {
    /*$con = Propel::getConnection(StepPeer::DATABASE_NAME);
    try
    {
      $con->begin();
      //$this->fromArray($fields,BasePeer::TYPE_FIELDNAME);
      $this->setStepUid($sStepUID);
      $result=$this->delete();
      $con->commit();
      return $result;
    }
    catch(Exception $e)
    {
      $con->rollback();
      throw($e);
    }*/
    $oConnection = Propel::getConnection(StepPeer::DATABASE_NAME);
  	try {
  	  $oStep = StepPeer::retrieveByPK($sStepUID);
  	  if (!is_null($oStep))
  	  {
  	  	$oConnection->begin();
        $iResult = $oStep->delete();
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

  function getNextPosition($sTaskUID) {
  	try {
  		$oCriteria = new Criteria('workflow');
      $oCriteria->addSelectColumn('(COUNT(*) + 1) AS POSITION');
      $oCriteria->add(StepPeer::TAS_UID, $sTaskUID);
      $oDataset = StepPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $aRow = $oDataset->getRow();
      return (int)$aRow['POSITION'];
  	}
  	catch (Exception $oException) {
  		throw $Exception;
  	}
  }

  function reOrder($sStepUID, $iPosition) {
  	try {
  	  /*$oCriteria1 = new Criteria('workflow');
  	  $oCriteria1->add(StepPeer::STEP_POSITION, StepPeer::STEP_POSITION);
  	  $oCriteria2 = new Criteria('workflow');
  	  $oCriteria2->add(StepPeer::TAS_UID,      $sTaskUID);
  	  $oCriteria2->add(StepPeer::STEP_POSITION, $iPosition, '>');
      BasePeer::doUpdate($oCriteria2, $oCriteria1, Propel::getConnection('workflow'));*/
      $oStep    = StepPeer::retrieveByPK($sStepUID);
      $sTaskUID = $oStep->getTasUid();
      $oCriteria = new Criteria('workflow');
      $oCriteria->add(StepPeer::TAS_UID,       $sTaskUID);
  	  $oCriteria->add(StepPeer::STEP_POSITION, $iPosition, '>');
      $oDataset = StepPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
      	$oStep = StepPeer::retrieveByPK($aRow['STEP_UID']);
      	$oStep->setStepPosition(($aRow['STEP_POSITION']) - 1);
      	$oStep->save();
      	$oDataset->next();
      }
  	}
  	catch (Exception $oException) {
  		throw $oException;
  	}
  }

	function up($sStepUID = '', $sTaskUID = '', $iPosition = 0) {
		try {
  	  if ($iPosition > 1) {
  	  	$oCriteria1 = new Criteria('workflow');
  	  	$oCriteria1->add(StepPeer::STEP_POSITION, $iPosition);
  	  	$oCriteria2 = new Criteria('workflow');
  	  	$oCriteria2->add(StepPeer::TAS_UID, $sTaskUID);
  	  	$oCriteria2->add(StepPeer::STEP_POSITION, ($iPosition - 1));
  	  	BasePeer::doUpdate($oCriteria2, $oCriteria1, Propel::getConnection('workflow'));
  	  	$oCriteria1 = new Criteria('workflow');
  	  	$oCriteria1->add(StepPeer::STEP_POSITION, ($iPosition - 1));
  	  	$oCriteria2 = new Criteria('workflow');
  	  	$oCriteria2->add(StepPeer::STEP_UID, $sStepUID);
  	  	BasePeer::doUpdate($oCriteria2, $oCriteria1, Propel::getConnection('workflow'));
  	  }
  	}
  	catch (Exception $oException) {
  		throw $oException;
  	}
  }

  function down($sStepUID = '', $sTaskUID = '', $iPosition = 0) {
  	try {
  		$oCriteria = new Criteria('workflow');
      $oCriteria->addSelectColumn('COUNT(*) AS MAX_POSITION');
      $oCriteria->add(StepPeer::TAS_UID, $sTaskUID);
      $oDataset = StepPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $aRow = $oDataset->getRow();
      if ($iPosition < (int)$aRow['MAX_POSITION']) {
      	$oCriteria1 = new Criteria('workflow');
  	  	$oCriteria1->add(StepPeer::STEP_POSITION, $iPosition);
  	  	$oCriteria2 = new Criteria('workflow');
  	  	$oCriteria2->add(StepPeer::TAS_UID, $sTaskUID);
  	  	$oCriteria2->add(StepPeer::STEP_POSITION, ($iPosition + 1));
  	  	BasePeer::doUpdate($oCriteria2, $oCriteria1, Propel::getConnection('workflow'));
  	  	$oCriteria1 = new Criteria('workflow');
  	  	$oCriteria1->add(StepPeer::STEP_POSITION, ($iPosition + 1));
  	  	$oCriteria2 = new Criteria('workflow');
  	  	$oCriteria2->add(StepPeer::STEP_UID, $sStepUID);
  	  	BasePeer::doUpdate($oCriteria2, $oCriteria1, Propel::getConnection('workflow'));
      }
  	}
  	catch (Exception $oException) {
  		throw $oException;
  	}
  }

  function removeStep($sType = '', $sObjUID = '') {
  	try {
  		$oCriteria = new Criteria('workflow');
      $oCriteria->add(StepPeer::STEP_TYPE_OBJ, $sType);
      $oCriteria->add(StepPeer::STEP_UID_OBJ,  $sObjUID);
      $oDataset = StepPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
      	$this->reOrder($aRow['STEP_UID'], $aRow['STEP_POSITION']);
      	$this->remove($aRow['STEP_UID']);
      	$oDataset->next();
      }
  	}
  	catch (Exception $oException) {
  		throw $oException;
  	}
  }

	/**
	 * verify if Step row specified in [sUid] exists.
	 *
	 * @param      string $sUid   the uid of the
	 */

  function StepExists ( $sUid ) {
  	$con = Propel::getConnection(StepPeer::DATABASE_NAME);
    try {
      $oObj = StepPeer::retrieveByPk( $sUid );
  	  if ( get_class ($oObj) == 'Step' ) {
  	    return true;
  	  }
      else {
        return false;
      }
    }
    catch (Exception $oError) {
    	throw($oError);
    }
  }

} // Step
