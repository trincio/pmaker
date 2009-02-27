<?php

require_once 'classes/model/om/BaseObjectPermission.php';


/**
 * Skeleton subclass for representing a row from the 'OBJECT_PERMISSION' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class ObjectPermission extends BaseObjectPermission {

	public function load($UID)
	{
		try {
			$oRow = ObjectPermissionPeer::retrieveByPK( $UID );
			if (!is_null($oRow))
			{
				$aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
				$this->fromArray($aFields,BasePeer::TYPE_FIELDNAME);
				$this->setNew(false);
				return $aFields;
			}
			else {
				throw(new Exception( "The row '" . $UsrUid . "' in table USER doesn't exists!" ));
			}
		}
		catch (Exception $oError) {
			throw($oError);
		}
	}

	function create ($aData)
	{
		try
		{
		  $this->fromArray($aData,BasePeer::TYPE_FIELDNAME);
			$result=$this->save();
			return $result;
		}
		catch(Exception $e)
		{
			throw($e);
		}
	}

	function Exists ( $Uid ) {
		try {
			$oPro = ObjectPermissionPeer::retrieveByPk( $Uid );
			if ( get_class ($oPro) == 'ObjectPermission' ) {
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

	function remove($Uid)
	{
		$con = Propel::getConnection(DbSourcePeer::DATABASE_NAME);
		try {
			$con->begin();
			$this->setOpUid($Uid);
			$result = $this->delete();
			$con->commit();
			return $result;
		}
		catch (exception $e) {
			$con->rollback();
			throw ($e);
		}
	}

  function update($aFields) { //print_r($aFields); die;
    $oConnection = Propel::getConnection(ObjectPermissionPeer::DATABASE_NAME);
    try {
      $oConnection->begin();
      $this->load($aFields['OP_UID']);
      $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME);
      if ($this->validate()) {
        $iResult = $this->save();
        $oConnection->commit();
        return $iResult;
      }
      else {
        $oConnection->rollback();
        throw(new Exception('Failed Validation in class ' . get_class($this) . '.'));
      }
    }
    catch(Exception $e) {
      $oConnection->rollback();
      throw($e);
    }
  }
} // ObjectPermission
