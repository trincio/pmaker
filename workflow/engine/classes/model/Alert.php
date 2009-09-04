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

  function calculateAlertsDueDate() {
    try {
      require_once 'classes/model/AppDelegation.php';
      require_once 'classes/model/AppAlert.php';
      G::LoadClass('dates');
      $oDates = new dates();
      $aProcesses = $aAlerts = array();
      $oCriteria = new Criteria('workflow');
      $oCriteria->addSelectColumn(AlertPeer::PRO_UID);
      $oCriteria->addGroupByColumn(AlertPeer::PRO_UID);
      $oDataset = AlertPeer::doSelectRs($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aData = $oDataset->getRow()) {
        $aProcesses[] = $aData['PRO_UID'];
        $oDataset->next();
      }
      $oCriteria = new Criteria('workflow');
      $oCriteria->addSelectColumn(AlertPeer::ALT_UID);
      $oCriteria->addSelectColumn(AlertPeer::PRO_UID);
      $oCriteria->addSelectColumn(AlertPeer::TAS_INITIAL);
      $oCriteria->addSelectColumn(AlertPeer::TAS_FINAL);
      $oCriteria->addSelectColumn(AlertPeer::ALT_TYPE);
      $oCriteria->addSelectColumn(AlertPeer::ALT_DAYS);
      $oCriteria->addSelectColumn(AlertPeer::ALT_MAX_ATTEMPTS);
      $oCriteria->addSelectColumn(AlertPeer::ALT_TEMPLATE);
      $oCriteria->addSelectColumn(AlertPeer::ALT_DIGEST);
      $oCriteria->addSelectColumn(AlertPeer::TRI_UID);
      $oCriteria->add(AlertPeer::PRO_UID, $aProcesses, Criteria::IN);
      $oDataset = AlertPeer::doSelectRs($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aData = $oDataset->getRow()) {
        $aAlerts[$aData['PRO_UID']][$aData['ALT_UID']] = $aData;
        $oDataset->next();
      }
      $oCriteria = new Criteria('workflow');
      $oCriteria->addSelectColumn(AppDelegationPeer::APP_UID);
      $oCriteria->addSelectColumn(AppDelegationPeer::DEL_INDEX);
      $oCriteria->addSelectColumn(AppDelegationPeer::PRO_UID);
      $oCriteria->addSelectColumn(AppDelegationPeer::DEL_INIT_DATE);
      $oCriteria->addSelectColumn(AppDelegationPeer::DEL_TASK_DUE_DATE);
      $aConditions   = array();
      $aConditions[] = array(AppDelegationPeer::APP_UID, AppAlertPeer::APP_UID);
      $aConditions[] = array(AppDelegationPeer::DEL_INDEX, AppAlertPeer::DEL_INDEX);
      //$oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
      $oCriteria->add(AppDelegationPeer::PRO_UID, $aProcesses, Criteria::IN);
      $oCriteria->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNOTNULL);
      //$oCriteria->add(AppAlertPeer::ALT_UID, null, Criteria::ISNULL);
      $oDataset = AppDelegationPeer::doSelectRs($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aData = $oDataset->getRow()) {
        foreach ($aAlerts[$aData['PRO_UID']] as $aAlert) {
          $oCriteria2 = new Criteria('workflow');
          $oCriteria2->add(AppAlertPeer::APP_UID, $aData['APP_UID']);
          $oCriteria2->add(AppAlertPeer::DEL_INDEX, $aData['DEL_INDEX']);
          $oCriteria2->add(AppAlertPeer::ALT_UID, $aAlert['ALT_UID']);
          $oCriteria2->add(AppAlertPeer::APP_ALT_STATUS, 'OPEN');
          if (AppDelegationPeer::doCount($oCriteria2) == 0) {
            if ($aAlert['TAS_FINAL'] != '') {
              $sDueDate = date('Y-m-d H:i:s', $oDates->calculateDate($aData['DEL_INIT_DATE'], $aAlert['TAS_DURATION'], 'hours', 1));
            }
            else {
              $sDueDate = $aData['DEL_TASK_DUE_DATE'];
            }
            //echo date('Y-m-d H:i:s', $oDates->calculateDate($sDueDate, -1, 'days', 1)) . ' | ' . $sDueDate . ' | ' . date('Y-m-d H:i:s', $oDates->calculateDate($sDueDate, 1, 'days', 1)) . '<br />';
            switch ($aAlert['ALT_TYPE']) {
              case 'BEFORE':
                $sActionDate = date('Y-m-d H:i:s', $oDates->calculateDate($sDueDate, (-1) * $aAlert['ALT_DAYS'], 'days', 1));
              break;
              case 'ON':
                $sActionDate = $sDueDate;
              break;
              case 'AFTER':
                $sActionDate = date('Y-m-d H:i:s', $oDates->calculateDate($sDueDate, $aAlert['ALT_DAYS'], 'days', 1));
              break;
              case 'RECURRENT':
                $sActionDate = date('Y-m-d H:i:s');
              break;
            }
            $oAppAlert = new AppAlert();
            $oAppAlert->create(array('APP_UID'                     => $aData['APP_UID'],
                                     'DEL_INDEX'                   => $aData['DEL_INDEX'],
                                     'ALT_UID'                     => $aAlert['ALT_UID'],
                                     'APP_ALT_ACTION_DATE'         => $sActionDate,
                                     'APP_ALT_ATTEMPTS'            => $aAlert['ALT_MAX_ATTEMPTS']));
          }
        }
        $oDataset->next();
      }
    }
    catch (Exception $oError) {
      //CONTINUE
    }
  }
} // Alert
