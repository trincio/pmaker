<?php

require_once 'classes/model/om/BaseUsersProperties.php';


/**
 * Skeleton subclass for representing a row from the 'USERS_PROPERTIES' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class UsersProperties extends BaseUsersProperties {
  function UserPropertyExists($sUserUID) {
    try {
      $oUserProperty = UsersPropertiesPeer::retrieveByPk($sUserUID);
  	  if (get_class($oUserProperty) == 'UsersProperties') {
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

  public function load($sUserUID) {
  	try {
  	  $oUserProperty = UsersPropertiesPeer::retrieveByPK($sUserUID);
  	  if (!is_null($oUserProperty)) {
  	    $aFields = $oUserProperty->toArray(BasePeer::TYPE_FIELDNAME);
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

  public function create($aData) {
  	$oConnection = Propel::getConnection(UsersPropertiesPeer::DATABASE_NAME);
  	try {
  	  $oUserProperty = new UsersProperties();
  	  $oUserProperty->fromArray($aData, BasePeer::TYPE_FIELDNAME);
  	  if ($oUserProperty->validate()) {
        $oConnection->begin();
        $iResult = $oUserProperty->save();
        $oConnection->commit();
        return true;
  	  }
  	  else {
  	  	$sMessage = '';
  	    $aValidationFailures = $oUserProperty->getValidationFailures();
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

  public function update($aData) {
  	$oConnection = Propel::getConnection(UsersPropertiesPeer::DATABASE_NAME);
  	try {
  	  $oUserProperty = UsersPropertiesPeer::retrieveByPK($aData['USR_UID']);
  	  if (!is_null($oUserProperty)) {
  	  	$oUserProperty->fromArray($aData, BasePeer::TYPE_FIELDNAME);
  	    if ($oUserProperty->validate()) {
  	    	$oConnection->begin();
          $iResult = $oUserProperty->save();
          $oConnection->commit();
          return $iResult;
  	    }
  	    else {
  	    	$sMessage = '';
  	      $aValidationFailures = $oUserProperty->getValidationFailures();
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
} // UsersProperties
