<?php

require_once 'classes/model/om/BaseAppEvent.php';


/**
 * Skeleton subclass for representing a row from the 'APP_EVENT' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class AppEvent extends BaseAppEvent {
    public function load($sApplicationUID, $iDelegation) {
    try {
      $oAppEvent = AppEventPeer::retrieveByPK($sApplicationUID, $iDelegation);
      if (!is_null($oAppEvent)) {
        $aFields = $oAppEvent->toArray(BasePeer::TYPE_FIELDNAME);
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
    $oConnection = Propel::getConnection(AppEventPeer::DATABASE_NAME);
    try {
      $oAppEvent = new AppEvent();
      $oAppEvent->fromArray($aData, BasePeer::TYPE_FIELDNAME);
      if ($oAppEvent->validate()) {
        $oConnection->begin();
        $iResult = $oAppEvent->save();
        $oConnection->commit();
        return true;
      }
      else {
        $sMessage = '';
        $aValidationFailures = $oAppEvent->getValidationFailures();
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
    $oConnection = Propel::getConnection(AppEventPeer::DATABASE_NAME);
    try {
      $oAppEvent = AppEventPeer::retrieveByPK($aData['APP_UID'], $aData['DEL_INDEX']);
      if (!is_null($oAppEvent)) {
        $oAppEvent->fromArray($aData, BasePeer::TYPE_FIELDNAME);
        if ($oAppEvent->validate()) {
          $oConnection->begin();
          $iResult = $oAppEvent->save();
          $oConnection->commit();
          return $iResult;
        }
        else {
          $sMessage = '';
          $aValidationFailures = $oAppEvent->getValidationFailures();
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

  function remove($sApplicationUID, $iDelegation, $sEvnUid) {
    $oConnection = Propel::getConnection(AppEventPeer::DATABASE_NAME);
    try {
      $oAppEvent = AppEventPeer::retrieveByPK($sApplicationUID, $iDelegation, $sEvnUid);
      if (!is_null($oAppEvent)) {
        $oConnection->begin();
        $iResult = $oAppEvent->delete();
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

  function getAppEventsCriteria($sProcessUid, $sStatus = '') {
    try {
      require_once 'classes/model/Event.php';
      $oCriteria = new Criteria('workflow');
      $oCriteria->addSelectColumn(AppEventPeer::APP_UID);
      $oCriteria->addSelectColumn(AppEventPeer::DEL_INDEX);
      $oCriteria->addSelectColumn(AppEventPeer::EVN_UID);
      $oCriteria->addSelectColumn(AppEventPeer::APP_EVN_ACTION_DATE);
      $oCriteria->addSelectColumn(AppEventPeer::APP_EVN_ATTEMPTS);
      $oCriteria->addSelectColumn(AppEventPeer::APP_EVN_LAST_EXECUTION_DATE);
      $oCriteria->addSelectColumn(AppEventPeer::APP_EVN_STATUS);
      $oCriteria->addSelectColumn(EventPeer::PRO_UID);
      $oCriteria->addSelectColumn(EventPeer::EVN_WHEN_OCCURS);
      $oCriteria->addSelectColumn(EventPeer::EVN_ACTION);
      $oCriteria->addAsColumn('EVN_DESCRIPTION', 'C1.CON_VALUE');
      $oCriteria->addAsColumn('TAS_TITLE', 'C2.CON_VALUE');
      $oCriteria->addAsColumn('APP_TITLE', 'C3.CON_VALUE');
      $oCriteria->addAlias('C1', 'CONTENT');
      $oCriteria->addAlias('C2', 'CONTENT');
      $oCriteria->addAlias('C3', 'CONTENT');
      $oCriteria->addJoin(AppEventPeer::EVN_UID, EventPeer::EVN_UID, Criteria::LEFT_JOIN);
      $del = DBAdapter::getStringDelimiter();
      $aConditions   = array();
      $aConditions[] = array(EventPeer::EVN_UID, 'C1.CON_ID');
      $aConditions[] = array('C1.CON_CATEGORY', $del . 'EVN_DESCRIPTION' . $del);
      $aConditions[] = array('C1.CON_LANG', $del . SYS_LANG . $del);
      $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
      $aConditions   = array();
      $aConditions[] = array(AppEventPeer::APP_UID, AppDelegationPeer::APP_UID);
      $aConditions[] = array(AppEventPeer::DEL_INDEX, AppDelegationPeer::DEL_INDEX);
      $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
      $aConditions   = array();
      $aConditions[] = array(AppDelegationPeer::TAS_UID, 'C2.CON_ID');
      $aConditions[] = array('C2.CON_CATEGORY', $del . 'TAS_TITLE' . $del);
      $aConditions[] = array('C2.CON_LANG', $del . SYS_LANG . $del);
      $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
      $aConditions   = array();
      $aConditions[] = array(AppDelegationPeer::APP_UID, 'C3.CON_ID');
      $aConditions[] = array('C3.CON_CATEGORY', $del . 'APP_TITLE' . $del);
      $aConditions[] = array('C3.CON_LANG', $del . SYS_LANG . $del);
      $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
      $oCriteria->add(AppEventPeer::EVN_UID, '', Criteria::NOT_EQUAL);
      $oCriteria->add(EventPeer::PRO_UID, $sProcessUid);
      switch ($sStatus) {
        case '':
          //Nothing
        break;
        case 'PENDING':
          $oCriteria->add(AppEventPeer::APP_EVN_STATUS, 'OPEN');
        break;
        case 'COMPLETED':
          $oCriteria->add(AppEventPeer::APP_EVN_STATUS, 'CLOSE');
        break;
      }
      $oCriteria->addDescendingOrderByColumn(AppEventPeer::APP_EVN_ACTION_DATE);
      return $oCriteria;
    }
    catch (Exception $oError) {
      throw($oError);
    }
  }

  function executeEvents($sLastExecution, $sNow) {
    try {
      $oCriteria = new Criteria('workflow');
      $oCriteria->addSelectColumn(AppEventPeer::APP_UID);
      $oCriteria->addSelectColumn(AppEventPeer::DEL_INDEX);
      $oCriteria->addSelectColumn(AppEventPeer::EVN_UID);
      $oCriteria->addSelectColumn(AppEventPeer::APP_EVN_ACTION_DATE);
      $oCriteria->addSelectColumn(AppEventPeer::APP_EVN_LAST_EXECUTION_DATE);
      $oCriteria->addSelectColumn(AppEventPeer::APP_EVN_STATUS);
      $oCriteria->addSelectColumn(EventPeer::PRO_UID);
      $oCriteria->addSelectColumn(EventPeer::EVN_ACTION);
      $oCriteria->addSelectColumn(EventPeer::TRI_UID);

      $oCriteria->addSelectColumn(AppDelegationPeer::USR_UID);
      $oCriteria->addSelectColumn(AppDelegationPeer::DEL_TASK_DUE_DATE);
      $oCriteria->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE);

      //$oCriteria->addAsColumn('TAS_TITLE', ContentPeer::CON_VALUE);

      $oCriteria->addJoin(AppEventPeer::EVN_UID, EventPeer::EVN_UID, Criteria::JOIN);
      $aConditions   = array();
      $aConditions[] = array(AppEventPeer::APP_UID,   AppDelegationPeer::APP_UID);
      $aConditions[] = array(AppEventPeer::DEL_INDEX, AppDelegationPeer::DEL_INDEX);
      $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);

//      $oCriteria->add(AppEventPeer::APP_EVN_ATTEMPTS, 0, Criteria::GREATER_THAN);
      $oCriteria->add(AppEventPeer::APP_EVN_STATUS, 'OPEN');
/*      if ($sLastExecution == '') {
        $oCriteria->add(AppEventPeer::APP_EVN_ACTION_DATE, $sNow, Criteria::LESS_EQUAL);
      }
      else {
        $oCriteria->add($oCriteria->getNewCriterion(AppEventPeer::APP_EVN_ACTION_DATE, $sLastExecution, Criteria::GREATER_EQUAL)->addAnd($oCriteria->getNewCriterion(AppEventPeer::APP_EVN_ACTION_DATE, $sNow, Criteria::LESS_EQUAL)));
      }
*/
      G::LoadClass('case');
      $oCase = new Cases();

      $oDataset = AppEventPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      while ($aRow = $oDataset->getRow()) {
      	krumo ($aRow);
        $oTrigger = new Triggers();
        $aTrigger = $oTrigger->load($aRow['TRI_UID']);
        $aFields = $oCase->loadCase($aRow['APP_UID']);
        $oPMScript = new PMScript();
        $oPMScript->setFields($aFields['APP_DATA']);
        $oPMScript->setScript($aTrigger['TRI_WEBBOT']);
        $oPMScript->execute();
        $aFields['APP_DATA'] = $oPMScript->aFields;
        $oCase->updateCase($aRow['APP_UID'], $aFields);

        //update the appevent record.
        $oAppEvent = AppEventPeer::retrieveByPK($aRow['APP_UID'], $aRow['DEL_INDEX'], $aRow['EVN_UID']);
        if ( $oAppEvent->getAppEvnAttempts() >= 1 )
          $oAppEvent->setAppEvnAttempts($oAppEvent->getAppEvnAttempts() - 1);
        else
          $oAppEvent->setAppEvnAttempts( 0 );

        $oAppEvent->setAppEvnLastExecutionDate(date('Y-m-d H:i:s'));
        $oAppEvent->setAppEvnStatus('CLOSE');
        $oAppEvent->save();

        $oDataset->next();
      }
/*
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
      G::LoadClass('case');
      $oCase = new Cases();
      while ($aRow = $oDataset->getRow()) {
        switch ($aRow['EVN_ACTION']) {
          case 'SEND_MESSAGE':
            $sContent = '';
            if ($aRow['EVN_MESSAGE_TEMPLATE'] != '') {
              $sContent = file_get_contents(PATH_DATA_SITE . 'mailTemplates' . PATH_SEP . $aRow['PRO_UID'] . PATH_SEP . $aRow['EVN_MESSAGE_TEMPLATE']);
            }
            else {
              $sContent = file_get_contents(PATH_HOME. 'engine' . PATH_SEP . 'templates' . PATH_SEP . 'mails' . PATH_SEP . 'alert_message.html');
            }
            if (($sContent != '') && ($aRow['DEL_FINISH_DATE'] == null)) {
              $aRow['EVN_MESSAGE_TO'] = unserialize($aRow['EVN_MESSAGE_TO']);
              if (is_array($aRow['EVN_MESSAGE_TO']['TO'])) {
                foreach ($aRow['EVN_MESSAGE_TO']['TO'] as $iKey => $sEmail) {
                  if ($sEmail == '-1') {
                    $oCriteria = new Criteria('workflow');
                    $oCriteria->add(UsersPeer::USR_UID, $aRow['USR_UID']);
                    $oDatasetu = UsersPeer::doSelectRS($oCriteria);
                    $oDatasetu->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                    $oDatasetu->next();
                    $aRowUser = $oDatasetu->getRow();
                    $aRow['EVN_MESSAGE_TO']['TO'][$iKey] = $aRowUser['USR_EMAIL'];
                  }
                }
              }
              else {
                $aRow['EVN_MESSAGE_TO']['TO'] = array();
              }
              if (is_array($aRow['EVN_MESSAGE_TO']['CC'])) {
                foreach ($aRow['EVN_MESSAGE_TO']['CC'] as $iKey => $sEmail) {
                  if ($sEmail == '-1') {
                    $oCriteria = new Criteria('workflow');
                    $oCriteria->add(UsersPeer::USR_UID, $aRow['USR_UID']);
                    $oDatasetu = UsersPeer::doSelectRS($oCriteria);
                    $oDatasetu->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                    $oDatasetu->next();
                    $aRowUser = $oDatasetu->getRow();
                    $aRow['EVN_MESSAGE_TO']['CC'][$iKey] = $aRowUser['USR_EMAIL'];
                  }
                }
              }
              else {
                $aRow['EVN_MESSAGE_TO']['CC'] = array();
              }
              if (is_array($aRow['EVN_MESSAGE_TO']['BCC'])) {
                foreach ($aRow['EVN_MESSAGE_TO']['BCC'] as $iKey => $sEmail) {
                  if ($sEmail == '-1') {
                    $oCriteria = new Criteria('workflow');
                    $oCriteria->add(UsersPeer::USR_UID, $aRow['USR_UID']);
                    $oDatasetu = UsersPeer::doSelectRS($oCriteria);
                    $oDatasetu->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                    $oDatasetu->next();
                    $aRowUser = $oDatasetu->getRow();
                    $aRow['EVN_MESSAGE_TO']['BCC'][$iKey] = $aRowUser['USR_EMAIL'];
                  }
                }
              }
              else {
                $aRow['EVN_MESSAGE_TO']['BCC'] = array();
              }
              $aFields = $oCase->loadCase($aRow['APP_UID']);
              $aFields['TAS_TITLE'] = $aRow['TAS_TITLE'];
              $aFields['DEL_TASK_DUE_DATE'] = $aRow['DEL_TASK_DUE_DATE'];
              $oSpool->create(array('msg_uid'          => '',
                                    'app_uid'          => $aRow['APP_UID'],
                                    'del_index'        => $aRow['DEL_INDEX'],
                                    'app_msg_type'     => 'EVENT',
                                    'app_msg_subject'  => $aRow['EVN_MESSAGE_SUBJECT'],
                                    'app_msg_from'     => 'ProcessMaker <' . $aConfiguration['MESS_ACCOUNT'] . '>',
                                    'app_msg_to'       => implode(',', $aRow['EVN_MESSAGE_TO']['TO']),
                                    'app_msg_body'     => G::replaceDataField($sContent, $aFields),
                                    'app_msg_cc'       => implode(',', $aRow['EVN_MESSAGE_TO']['CC']),
                                    'app_msg_bcc'      => implode(',', $aRow['EVN_MESSAGE_TO']['BCC']),
                                    'app_msg_attach'   => '',
                                    'app_msg_template' => '',
                                    'app_msg_status'   => 'pending'));
              $oSpool->sendMail();
              $oAppEvent = AppEventPeer::retrieveByPK($aRow['APP_UID'], $aRow['DEL_INDEX']);
              $oAppEvent->setAppEvnAttempts($oAppEvent->getAppEvnAttempts() - 1);
              $oAppEvent->setAppEvnLastExecutionDate(date('Y-m-d H:i:s'));
              if ($oSpool->status != 'pending') {
                $oAppEvent->setAppEvnStatus('CLOSE');
              }
              $oAppEvent->save();
            }
          break;
          case 'EXECUTE_TRIGGER':
            if ($aRow['TRI_UID'] != '') {
              $oTrigger = new Triggers();
              $aTrigger = $oTrigger->load($aRow['TRI_UID']);
              $aFields = $oCase->loadCase($aRow['APP_UID']);
              $oPMScript = new PMScript();
              $oPMScript->setFields($aFields['APP_DATA']);
              $oPMScript->setScript($aTrigger['TRI_WEBBOT']);
              $oPMScript->execute();
              $aFields['APP_DATA'] = $oPMScript->aFields;
              $oCase->updateCase($aRow['TRI_UID'], $aFields);
            }
          break;
        }
        $oDataset->next();
      }
*/
    }
    catch (Exception $oError) {
      return  $oError->getMessage();
    }
  }
} // AppEvent
