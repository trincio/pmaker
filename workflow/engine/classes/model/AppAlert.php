<?php

require_once 'classes/model/om/BaseAppAlert.php';


/**
 * Skeleton subclass for representing a row from the 'APP_ALERT' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class AppAlert extends BaseAppAlert {
  public function load($sApplicationUID, $iDelegation) {
  	try {
  	  $oAppAlert = AppAlertPeer::retrieveByPK($sApplicationUID, $iDelegation);
  	  if (!is_null($oAppAlert)) {
  	    $aFields = $oAppAlert->toArray(BasePeer::TYPE_FIELDNAME);
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
    $oConnection = Propel::getConnection(AppAlertPeer::DATABASE_NAME);
  	try {
  	  $oAppAlert = new AppAlert();
  	  $oAppAlert->fromArray($aData, BasePeer::TYPE_FIELDNAME);
  	  if ($oAppAlert->validate()) {
        $oConnection->begin();
        $iResult = $oAppAlert->save();
        $oConnection->commit();
        return true;
  	  }
  	  else {
  	  	$sMessage = '';
  	    $aValidationFailures = $oAppAlert->getValidationFailures();
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
    $oConnection = Propel::getConnection(AppAlertPeer::DATABASE_NAME);
  	try {
  	  $oAppAlert = AppAlertPeer::retrieveByPK($aData['APP_UID'], $aData['DEL_INDEX']);
  	  if (!is_null($oAppAlert)) {
  	  	$oAppAlert->fromArray($aData, BasePeer::TYPE_FIELDNAME);
  	    if ($oAppAlert->validate()) {
  	    	$oConnection->begin();
          $iResult = $oAppAlert->save();
          $oConnection->commit();
          return $iResult;
  	    }
  	    else {
  	    	$sMessage = '';
  	      $aValidationFailures = $oAppAlert->getValidationFailures();
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

  function remove($sApplicationUID, $iDelegation) {
    $oConnection = Propel::getConnection(AppAlertPeer::DATABASE_NAME);
  	try {
  	  $oAppAlert = AppAlertPeer::retrieveByPK($sApplicationUID, $iDelegation);
  	  if (!is_null($oAppAlert)) {
  	  	$oConnection->begin();
        $iResult = $oAppAlert->delete();
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

  function sendAlerts($sLastExecution) {
    try {
      $oCriteria = new Criteria('workflow');
      $oCriteria->addSelectColumn(AppAlertPeer::APP_UID);
      $oCriteria->addSelectColumn(AppAlertPeer::DEL_INDEX);
      $oCriteria->addSelectColumn(AppAlertPeer::ALT_UID);
      $oCriteria->addSelectColumn(AppAlertPeer::APP_ALT_ACTION_DATE);
      $oCriteria->addSelectColumn(AppAlertPeer::APP_ALT_ATTEMPTS);
      $oCriteria->addSelectColumn(AppAlertPeer::APP_ALT_LAST_EXECUTION_DATE);
      $oCriteria->addSelectColumn(AppAlertPeer::APP_ALT_STATUS);
      $oCriteria->addSelectColumn(AlertPeer::ALT_TEMPLATE);
      $oCriteria->addSelectColumn(AlertPeer::ALT_DIGEST);
      $oCriteria->addSelectColumn(AlertPeer::TRI_UID);
      $oCriteria->addJoin(AppAlertPeer::ALT_UID, AlertPeer::ALT_UID, Criteria::LEFT_JOIN);
      $oCriteria->add(AppAlertPeer::APP_ALT_ATTEMPTS, 0, Criteria::GREATER_THAN);
      $oCriteria->add(AppAlertPeer::APP_ALT_STATUS, 'OPEN');
      if ($sLastExecution != '') {
        $oCriteria->add(AppAlertPeer::APP_ALT_ACTION_DATE, $sLastExecution, Criteria::GREATER_EQUAL);
      }
      $oDataset = AppAlertPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
        var_dump($aRow);echo '<br /><br />';
        $oDataset->next();
      }die;
    }
    catch (Exception $oError) {
      //CONTINUE
    }
  }
} // AppAlert
