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
		$con = Propel::getConnection(UsersPeer::DATABASE_NAME);
		try
		{
			$result=$this->save();
			return $result;
		}
		catch(Exception $e)
		{
			//$con->rollback();
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

} // ObjectPermission
