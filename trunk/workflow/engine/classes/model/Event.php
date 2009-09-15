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
  	    $aFields['EVN_MESSAGE_TO'] = unserialize($aFields['EVN_MESSAGE_TO']);
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
    if (is_array($aData['EVN_MESSAGE_TO'])) {
      $aData['EVN_MESSAGE_TO'] = serialize($aData['EVN_MESSAGE_TO']);
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
    if (is_array($aData['EVN_MESSAGE_TO'])) {
      $aData['EVN_MESSAGE_TO'] = serialize($aData['EVN_MESSAGE_TO']);
    }
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

  function calculateEventsExecutionDate() {
    try {
      require_once 'classes/model/AppDelegation.php';
      require_once 'classes/model/AppEvent.php';
      G::LoadClass('dates');
      $oDates = new dates();
      $aProcesses = $aEvents = array();
      $oCriteria = new Criteria('workflow');
      $oCriteria->addSelectColumn(EventPeer::PRO_UID);
      $oCriteria->addGroupByColumn(EventPeer::PRO_UID);
      $oDataset = EventPeer::doSelectRs($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aData = $oDataset->getRow()) {
        $aProcesses[] = $aData['PRO_UID'];
        $oDataset->next();
      }
      $oCriteria = new Criteria('workflow');
      $oCriteria->addSelectColumn(EventPeer::EVN_UID);
      $oCriteria->addSelectColumn(EventPeer::PRO_UID);
      $oCriteria->addSelectColumn(EventPeer::EVN_RELATED_TO);
      $oCriteria->addSelectColumn(EventPeer::TAS_UID);
      $oCriteria->addSelectColumn(EventPeer::EVN_TAS_UID_FROM);
      $oCriteria->addSelectColumn(EventPeer::EVN_TAS_UID_TO);
      $oCriteria->addSelectColumn(EventPeer::EVN_TAS_STIMATED_DURATION);
      $oCriteria->addSelectColumn(EventPeer::EVN_WHEN);
      $oCriteria->addSelectColumn(EventPeer::EVN_MAX_ATTEMPTS);
      $oCriteria->addSelectColumn(EventPeer::EVN_ACTION);
      $oCriteria->addSelectColumn(EventPeer::EVN_MESSAGE_SUBJECT);
      $oCriteria->addSelectColumn(EventPeer::EVN_MESSAGE_TO);
      $oCriteria->addSelectColumn(EventPeer::EVN_MESSAGE_TEMPLATE);
      $oCriteria->addSelectColumn(EventPeer::EVN_MESSAGE_DIGEST);
      $oCriteria->addSelectColumn(EventPeer::TRI_UID);
      $oCriteria->add(EventPeer::PRO_UID, $aProcesses, Criteria::IN);
      $oDataset = EventPeer::doSelectRs($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aData = $oDataset->getRow()) {
        $aData['EVN_TAS_STIMATED_DURATION'] = (float)$aData['EVN_TAS_STIMATED_DURATION'];
        $aData['EVN_WHEN'] = (float)$aData['EVN_WHEN'];
        $aEvents[$aData['PRO_UID']][$aData['EVN_UID']] = $aData;
        $oDataset->next();
      }
      $oCriteria = new Criteria('workflow');
      $oCriteria->addSelectColumn(AppDelegationPeer::APP_UID);
      $oCriteria->addSelectColumn(AppDelegationPeer::DEL_INDEX);
      $oCriteria->addSelectColumn(AppDelegationPeer::PRO_UID);
      $oCriteria->addSelectColumn(AppDelegationPeer::DEL_INIT_DATE);
      $oCriteria->addSelectColumn(AppDelegationPeer::DEL_TASK_DUE_DATE);
      $oCriteria->add(AppDelegationPeer::PRO_UID, $aProcesses, Criteria::IN);
      $oCriteria->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
      $oDataset = AppDelegationPeer::doSelectRs($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aData = $oDataset->getRow()) {
        foreach ($aEvents[$aData['PRO_UID']] as $aEvent) {
          $oCriteria2 = new Criteria('workflow');
          $oCriteria2->add(AppEventPeer::APP_UID, $aData['APP_UID']);
          $oCriteria2->add(AppEventPeer::DEL_INDEX, $aData['DEL_INDEX']);
          $oCriteria2->add(AppEventPeer::EVN_UID, $aEvent['EVN_UID']);
          $oCriteria2->add(AppEventPeer::APP_EVN_STATUS, 'OPEN');
          if (AppDelegationPeer::doCount($oCriteria2) == 0) {
            if ($aEvent['EVN_RELATED_TO'] != 'SINGLE') {
              $sDueDate = date('Y-m-d H:i:s', $oDates->calculateDate($aData['DEL_INIT_DATE'], $aEvent['EVN_RELATED_TO'], 'hours', 1));
            }
            else {
              $sDueDate = $aData['DEL_TASK_DUE_DATE'];
            }
            if ($aEvent['EVN_WHEN'] != 0) {
              $sActionDate = date('Y-m-d H:i:s', $oDates->calculateDate($sDueDate, $aEvent['EVN_WHEN'], 'days', 1));
            }
            else {
              $sActionDate = $sDueDate;
            }
            $oAppEvent = new AppEvent();
            $oAppEvent->create(array('APP_UID'                     => $aData['APP_UID'],
                                     'DEL_INDEX'                   => $aData['DEL_INDEX'],
                                     'EVN_UID'                     => $aEvent['EVN_UID'],
                                     'APP_EVN_ACTION_DATE'         => $sActionDate,
                                     'APP_EVN_ATTEMPTS'            => $aEvent['EVN_MAX_ATTEMPTS']));
          }
        }
        $oDataset->next();
      }
    }
    catch (Exception $oError) {
      //CONTINUE
    }
  }
} // Event
