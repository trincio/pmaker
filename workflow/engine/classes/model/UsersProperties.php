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

  public function loadOrCreateIfNotExists($sUserUID, $aUserProperty = array()) {
    if (!$this->UserPropertyExists($sUserUID)) {
      $aUserProperty['USR_UID'] = $sUserUID;
      if (!isset($aUserProperty['USR_LAST_UPDATE_DATE'])) {
        $aUserProperty['USR_LAST_UPDATE_DATE'] = date('Y-m-d H:i:s');
      }
      if (!isset($aUserProperty['USR_LOGGED_NEXT_TIME'])) {
        $aUserProperty['USR_LOGGED_NEXT_TIME'] = 0;
      }
      $this->create($aUserProperty);
    }
    else {
      $aUserProperty = $this->load($sUserUID);
    }
    return $aUserProperty;
  }

  public function validatePassword($sPassword, $sLastUpdate, $iChangePasswordNextTime) {
    if (!defined('PPU_MINIMUN_LENGTH')) {
      define('PPU_MINIMUN_LENGTH', 5);
    }
    if (!defined('PPU_MAXIMUN_LENGTH')) {
      define('PPU_MAXIMUN_LENGTH', 20);
    }
    if (!defined('PPU_NUMERICAL_CHARACTER_REQUIRED')) {
      define('PPU_NUMERICAL_CHARACTER_REQUIRED', 0);
    }
    if (!defined('PPU_UPPERCASE_CHARACTER_REQUIRED')) {
      define('PPU_UPPERCASE_CHARACTER_REQUIRED', 0);
    }
    if (!defined('PPU_SPECIAL_CHARACTER_REQUIRED')) {
      define('PPU_SPECIAL_CHARACTER_REQUIRED', 0);
    }
    if (!defined('PPU_EXPIRATION_IN')) {
      define('PPU_EXPIRATION_IN', 0);
    }
    if (!defined('PPU_CHANGE_PASSWORD_AFTER_NEXT_LOGIN')) {
      define('PPU_CHANGE_PASSWORD_AFTER_NEXT_LOGIN', 0);
    }
    if (function_exists('mb_strlen')) {
      $iLength = mb_strlen($sPassword);
    }
    else {
      $iLength = strlen($sPassword);
    }
    $aErrors = array();
    if ($iLength < PPU_MINIMUN_LENGTH) {
      $aErrors[] = 'ID_PPU_MINIMUN_LENGTH';
    }
    if ($iLength > PPU_MAXIMUN_LENGTH) {
      $aErrors[] = 'ID_PPU_MAXIMUN_LENGTH';
    }
    if (PPU_NUMERICAL_CHARACTER_REQUIRED == 1) {
      if (preg_match_all('/[0-9]/', $sPassword, $aMatch, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE) == 0) {
        $aErrors[] = 'ID_PPU_NUMERICAL_CHARACTER_REQUIRED';
      }
    }
    if (PPU_UPPERCASE_CHARACTER_REQUIRED == 1) {
      if (preg_match_all('/[A-Z]/', $sPassword, $aMatch, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE) == 0) {
        $aErrors[] = 'ID_PPU_UPPERCASE_CHARACTER_REQUIRED';
      }
    }
    if (PPU_SPECIAL_CHARACTER_REQUIRED == 1) {
      if (preg_match_all('/[ºª\\!|"@·#$~%€&¬\/()=\'?¡¿*+\-_.:,;]/', $sPassword, $aMatch, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE) == 0) {
        $aErrors[] = 'ID_PPU_SPECIAL_CHARACTER_REQUIRED';
      }
    }
    if (PPU_EXPIRATION_IN > 0) {
      G::LoadClass('dates');
      $oDates = new dates();
      $fDays  = $oDates->calculateDuration(date('Y-m-d H:i:s'), $sLastUpdate);
      if ($fDays > PPU_EXPIRATION_IN) {
        $aErrors[] = 'ID_PPU_EXPIRATION_IN';
      }
    }
    if (PPU_CHANGE_PASSWORD_AFTER_NEXT_LOGIN == 1) {
      if ($iChangePasswordNextTime == 1) {
        $aErrors[] = 'ID_PPU_CHANGE_PASSWORD_AFTER_NEXT_LOGIN';
      }
    }
    return $aErrors;
  }
} // UsersProperties
