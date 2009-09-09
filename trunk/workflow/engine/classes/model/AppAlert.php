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

  function sendAlerts($sLastExecution, $sNow) {
    try {
      $oCriteria = new Criteria('workflow');
      $oCriteria->addSelectColumn(AppAlertPeer::APP_UID);
      $oCriteria->addSelectColumn(AppAlertPeer::DEL_INDEX);
      $oCriteria->addSelectColumn(AppAlertPeer::ALT_UID);
      $oCriteria->addSelectColumn(AppAlertPeer::APP_ALT_ACTION_DATE);
      $oCriteria->addSelectColumn(AppAlertPeer::APP_ALT_ATTEMPTS);
      $oCriteria->addSelectColumn(AppAlertPeer::APP_ALT_LAST_EXECUTION_DATE);
      $oCriteria->addSelectColumn(AppAlertPeer::APP_ALT_STATUS);
      $oCriteria->addSelectColumn(AlertPeer::PRO_UID);
      $oCriteria->addSelectColumn(AlertPeer::ALT_TEMPLATE);
      $oCriteria->addSelectColumn(AlertPeer::ALT_DIGEST);
      $oCriteria->addSelectColumn(AlertPeer::TRI_UID);
      $oCriteria->addSelectColumn(AppDelegationPeer::USR_UID);
      $oCriteria->addSelectColumn(AppDelegationPeer::DEL_TASK_DUE_DATE);
      $oCriteria->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE);
      $oCriteria->addAsColumn('TAS_TITLE', ContentPeer::CON_VALUE);
      $oCriteria->addJoin(AppAlertPeer::ALT_UID, AlertPeer::ALT_UID, Criteria::LEFT_JOIN);
      $aConditions   = array();
      $aConditions[] = array(AppAlertPeer::APP_UID, AppDelegationPeer::APP_UID);
      $aConditions[] = array(AppAlertPeer::DEL_INDEX, AppDelegationPeer::DEL_INDEX);
      $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
      $del = DBAdapter::getStringDelimiter();
      $aConditions   = array();
      $aConditions[] = array(AppDelegationPeer::TAS_UID, ContentPeer::CON_ID);
      $aConditions[] = array(ContentPeer::CON_CATEGORY, $del . 'TAS_TITLE' . $del);
      $aConditions[] = array(ContentPeer::CON_LANG, $del . SYS_LANG . $del);
      $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
      $oCriteria->add(AppAlertPeer::APP_ALT_ATTEMPTS, 0, Criteria::GREATER_THAN);
      $oCriteria->add(AppAlertPeer::APP_ALT_STATUS, 'OPEN');
      $oCriteria->add(AppAlertPeer::APP_ALT_ACTION_DATE, $sNow, Criteria::LESS_EQUAL);
      if ($sLastExecution != '') {
        $oCriteria->add(AppAlertPeer::APP_ALT_ACTION_DATE, $sLastExecution, Criteria::GREATER_EQUAL);
      }
      $oDataset = AppAlertPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      require_once 'classes/model/Configuration.php';
      $oConfiguration = new Configuration();
      $sDelimiter     = DBAdapter::getStringDelimiter();
      $oCriteria      = new Criteria('workflow');
      $oCriteria->add(ConfigurationPeer::CFG_UID, 'Emails');
      $oCriteria->add(ConfigurationPeer::OBJ_UID, '');
      $oCriteria->add(ConfigurationPeer::PRO_UID, '');
      $oCriteria->add(ConfigurationPeer::USR_UID, '');
      $oCriteria->add(ConfigurationPeer::APP_UID, '');
      if (ConfigurationPeer::doCount($oCriteria) == 0) {
        $oConfiguration->create(array('CFG_UID' => 'Emails', 'OBJ_UID' => '', 'CFG_VALUE' => '', 'PRO_UID' => '', 'USR_UID' => '', 'APP_UID' => ''));
        $aConfiguration = array();
      }
      else {
        $aConfiguration = $oConfiguration->load('Emails', '', '', '', '');
        if ($aConfiguration['CFG_VALUE'] != '') {
          $aConfiguration = unserialize($aConfiguration['CFG_VALUE']);
        }
        else {
          $aConfiguration = array();
        }
      }
      G::LoadClass('spool');
      $oSpool = new spoolRun();
      $oSpool->setConfig(array('MESS_ENGINE'   => $aConfiguration['MESS_ENGINE'],
                               'MESS_SERVER'   => $aConfiguration['MESS_SERVER'],
                               'MESS_PORT'     => $aConfiguration['MESS_PORT'],
                               'MESS_ACCOUNT'  => $aConfiguration['MESS_ACCOUNT'],
                               'MESS_PASSWORD' => $aConfiguration['MESS_PASSWORD'],
                               'SMTPAuth'      => $aConfiguration['MESS_RAUTH']));
      G::LoadClass('spool');
      $oCase = new Cases();header('Content-Type: text/plain;');
      while ($aRow = $oDataset->getRow()) {
        $sContent = '';
        if ($aRow['ALT_TEMPLATE'] != '') {
          $sContent = file_get_contents(PATH_DATA_SITE . 'mailTemplates' . PATH_SEP . $aRow['PRO_UID'] . PATH_SEP . $aRow['ALT_TEMPLATE']);
        }
        else {
          $sContent = file_get_contents(PATH_HOME. 'engine' . PATH_SEP . 'templates' . PATH_SEP . 'mails' . PATH_SEP . 'alert_message.html');
        }
        if (($sContent != '') && ($aRow['DEL_FINISH_DATE'] == null)) {
          $oCriteria = new Criteria('workflow');
	        $oCriteria->add(UsersPeer::USR_UID, $aRow['USR_UID']);
    	    $oDatasetu = UsersPeer::doSelectRS($oCriteria);
	        $oDatasetu->setFetchmode(ResultSet::FETCHMODE_ASSOC);
	        $oDatasetu->next();
	        $aRowUser = $oDatasetu->getRow();
          $aFields = $oCase->loadCase($aRow['APP_UID']);
          $aFields['TAS_TITLE'] = $aRow['TAS_TITLE'];
          $aFields['DEL_TASK_DUE_DATE'] = $aRow['DEL_TASK_DUE_DATE'];
          $oSpool->create(array('msg_uid'          => '',
                                'app_uid'          => $aRow['APP_UID'],
                                'del_index'        => $aRow['DEL_INDEX'],
                                'app_msg_type'     => 'ALERT',
                                'app_msg_subject'  => G::LoadTranslation('ID_ALERT_MESSAGE'),
                                'app_msg_from'     => 'ProcessMaker <' . $aConfiguration['MESS_ACCOUNT'] . '>',
                                'app_msg_to'       => $aRowUser['USR_EMAIL'],
                                'app_msg_body'     => G::replaceDataField($sContent, $aFields),
                                'app_msg_cc'       => '',
                                'app_msg_bcc'      => '',
                                'app_msg_attach'   => '',
                                'app_msg_template' => '',
                                'app_msg_status'   => 'pending'));
          $oSpool->sendMail();
          $oAppAlert = AppAlertPeer::retrieveByPK($aRow['APP_UID'], $aRow['DEL_INDEX']);
          $oAppAlert->setAppAltAttempts($oAppAlert->getAppAltAttempts() - 1);
          $oAppAlert->setAppAltLastExecutionDate(date('Y-m-d H:i:s'));
          if ($oSpool->status != 'pending') {
            $oAppAlert->setAppAltStatus('CLOSE');
          }
          $oAppAlert->save();
        }
        $oDataset->next();
      }
    }
    catch (Exception $oError) {
      //CONTINUE
    }
  }
} // AppAlert
