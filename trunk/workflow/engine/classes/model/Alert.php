<?php

require_once 'classes/model/Content.php';
require_once 'classes/model/om/BaseAlert.php';


/**
 * Skeleton subclass for representing a row from the 'ALERT' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class Alert extends BaseAlert {

  /**
   * This value goes in the content table
   * @var        string
   */
  protected $alt_title = '';

  /**
   * Get the alt_title column value.
   * @return     string
   */
  public function getAltTitle() {
    if ( $this->getAltUid() == "" ) {
      throw ( new Exception( "Error in getAltTitle, the getAltUid() can't be blank") );
    }
    $lang = defined ( 'SYS_LANG' ) ? SYS_LANG : 'en';
    $this->alt_title = Content::load ( 'ALT_TITLE', '', $this->getAltUid(), $lang );
    return $this->alt_title;
  }

  /**
   * Set the alt_title column value.
   *
   * @param      string $v new value
   * @return     void
   */
  public function setAltTitle($v)
  {
    if ( $this->getAltUid() == "" ) {
      throw ( new Exception( "Error in setAltTitle, the setAltUid() can't be blank") );
    }
    $v=isset($v)?((string)$v):'';
    $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
    if ($this->alt_title !== $v || $v==="") {
      $this->alt_title = $v;
      $res = Content::addContent( 'ALT_TITLE', '', $this->getAltUid(), $lang, $this->alt_title );
      return $res;
    }
    return 0;
  }

  public function load($sUID) {
  	try {
  	  $oAlert = AlertPeer::retrieveByPK($sUID);
  	  if (!is_null($oAlert)) {
  	    $aFields = $oAlert->toArray(BasePeer::TYPE_FIELDNAME);
        $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME);
        $this->setNew(false);
  	    $this->setAltTitle($aFields['ALT_TITLE'] = $this->getAltTitle());
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
    if (!isset($aData['ALT_UID'])) {
      $aData['ALT_UID'] = G::generateUniqueID();
    }
    else {
      if ($aData['ALT_UID'] == '') {
        $aData['ALT_UID'] = G::generateUniqueID();
      }
    }
    $oConnection = Propel::getConnection(AlertPeer::DATABASE_NAME);
  	try {
  	  $oAlert = new Alert();
  	  $oAlert->fromArray($aData, BasePeer::TYPE_FIELDNAME);
  	  $oAlert->setAltTitle($aData['ALT_TITLE']);
  	  if ($oAlert->validate()) {
        $oConnection->begin();
        $iResult = $oAlert->save();
        $oConnection->commit();
        return $aData['ALT_UID'];
  	  }
  	  else {
  	  	$sMessage = '';
  	    $aValidationFailures = $oAlert->getValidationFailures();
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
    $oConnection = Propel::getConnection(AlertPeer::DATABASE_NAME);
  	try {
  	  $oAlert = AlertPeer::retrieveByPK($aData['ALT_UID']);
  	  if (!is_null($oAlert)) {
  	  	$oAlert->fromArray($aData, BasePeer::TYPE_FIELDNAME);
  	    if ($oAlert->validate()) {
  	    	$oConnection->begin();
  	    	if (array_key_exists('ALT_TITLE', $aData)) $oAlert->setAltTitle($aData['ALT_TITLE']);
          $iResult = $oAlert->save();
          $oConnection->commit();
          return $iResult;
  	    }
  	    else {
  	    	$sMessage = '';
  	      $aValidationFailures = $oAlert->getValidationFailures();
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
    $oConnection = Propel::getConnection(AlertPeer::DATABASE_NAME);
  	try {
  	  $oAlert = AlertPeer::retrieveByPK($sUID);
  	  if (!is_null($oAlert)) {
  	  	$oConnection->begin();
  	  	Content::removeContent('ALT_TITLE', '', $oAlert->getAltUid());
        $iResult = $oAlert->delete();
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
} // Alert
