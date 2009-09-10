<?php

require_once 'classes/model/Content.php';
require_once 'classes/model/om/BaseEvent.php';


/**
 * Skeleton subclass for representing a row from the 'EVENT' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class Event extends BaseEvent {
  /**
   * This value goes in the content table
   * @var        string
   */
  protected $evn_description = '';

  /**
   * Get the evn_description column value.
   * @return     string
   */
  public function getEvnDescription() {
    if ( $this->getEvnUid() == "" ) {
      throw ( new Exception( "Error in getEvnDescription, the getEvnUid() can't be blank") );
    }
    $lang = defined ( 'SYS_LANG' ) ? SYS_LANG : 'en';
    $this->evn_description = Content::load ( 'EVN_DESCRIPTION', '', $this->getEvnUid(), $lang );
    return $this->evn_description;
  }

  /**
   * Set the evn_description column value.
   *
   * @param      string $v new value
   * @return     void
   */
  public function setEvnDescription($v)
  {
    if ( $this->getEvnUid() == "" ) {
      throw ( new Exception( "Error in setEvnDescription, the setEvnUid() can't be blank") );
    }
    $v=isset($v)?((string)$v):'';
    $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
    if ($this->evn_description !== $v || $v==="") {
      $this->evn_description = $v;
      $res = Content::addContent( 'EVN_DESCRIPTION', '', $this->getEvnUid(), $lang, $this->evn_description );
      return $res;
    }
    return 0;
  }

  public function load($sUID) {
  	try {
  	  $oEvent = EventPeer::retrieveByPK($sUID);
  	  if (!is_null($oEvent)) {
  	    $aFields = $oEvent->toArray(BasePeer::TYPE_FIELDNAME);
        $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME);
        $this->setNew(false);
  	    $this->setEvnDescription($aFields['EVN_DESCRIPTION'] = $this->getEvnDescription());
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
    if (!isset($aData['EVN_UID'])) {
      $aData['EVN_UID'] = G::generateUniqueID();
    }
    else {
      if ($aData['EVN_UID'] == '') {
        $aData['EVN_UID'] = G::generateUniqueID();
      }
    }
    $oConnection = Propel::getConnection(EventPeer::DATABASE_NAME);
  	try {
  	  $oEvent = new Event();
  	  $oEvent->fromArray($aData, BasePeer::TYPE_FIELDNAME);
  	  $oEvent->setEvnDescription($aData['EVN_DESCRIPTION']);
  	  if ($oEvent->validate()) {
        $oConnection->begin();
        $iResult = $oEvent->save();
        $oConnection->commit();
        return $aData['EVN_UID'];
  	  }
  	  else {
  	  	$sMessage = '';
  	    $aValidationFailures = $oEvent->getValidationFailures();
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
    $oConnection = Propel::getConnection(EventPeer::DATABASE_NAME);
  	try {
  	  $oEvent = EventPeer::retrieveByPK($aData['EVN_UID']);
  	  if (!is_null($oEvent)) {
  	  	$oEvent->fromArray($aData, BasePeer::TYPE_FIELDNAME);
  	    if ($oEvent->validate()) {
  	    	$oConnection->begin();
  	    	if (array_key_exists('EVN_DESCRIPTION', $aData)) $oEvent->setEvnDescription($aData['EVN_DESCRIPTION']);
          $iResult = $oEvent->save();
          $oConnection->commit();
          return $iResult;
  	    }
  	    else {
  	    	$sMessage = '';
  	      $aValidationFailures = $oEvent->getValidationFailures();
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
    $oConnection = Propel::getConnection(EventPeer::DATABASE_NAME);
  	try {
  	  $oEvent = EventPeer::retrieveByPK($sUID);
  	  if (!is_null($oEvent)) {
  	  	$oConnection->begin();
  	  	Content::removeContent('EVN_DESCRIPTION', '', $oEvent->getEvnUid());
        $iResult = $oEvent->delete();
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
} // Event
