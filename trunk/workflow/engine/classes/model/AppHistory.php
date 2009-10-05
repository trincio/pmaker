<?php

require_once 'classes/model/om/BaseAppHistory.php';


/**
 * Skeleton subclass for representing a row from the 'APP_HISTORY' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class AppHistory extends BaseAppHistory {
    
    function insertHistory($aData){
        krumo($aData);

        $this->setAppUid($aData['APP_UID']);
        $this->setDelIndex($aData['DEL_INDEX']);
        $this->setProUid($aData['PRO_UID']);
        $this->setTasUid($aData['TAS_UID']);
        $this->setDynUid($aData['CURRENT_DYNAFORM']);
        $this->setUsrUid($aData['USER_UID']);        
        $this->setAppStatus($aData['APP_STATUS']);
        $this->setHistoryDate($aData['APP_UPDATE_DATE']);
        $this->setHistoryData($aData['APP_DATA']);
        
        
        if ($this->validate() ) {
           $res = $this->save(); 
           krumo($res);          
         }
         else {
           // Something went wrong. We can now get the validationFailures and handle them.
           $msg = '';
           $validationFailuresArray = $this->getValidationFailures();
           foreach($validationFailuresArray as $objValidationFailure) {
             $msg .= $objValidationFailure->getMessage() . "<br/>";
           }
           krumo($msg);
           //return array ( 'codError' => -100, 'rowsAffected' => 0, 'message' => $msg );
         }
        
        
        
        
        
    }
    
    function getDynaformHistory($PRO_UID,$TAS_UID,$APP_UID,$DYN_UID=""){
        $c = new Criteria('workflow');       
        $c->addSelectColumn(AppHistoryPeer::APP_UID);        
        $c->addSelectColumn(AppHistoryPeer::DEL_INDEX);
        $c->addSelectColumn(AppHistoryPeer::PRO_UID);
        $c->addSelectColumn(AppHistoryPeer::TAS_UID);
        $c->addSelectColumn(AppHistoryPeer::DYN_UID);
        $c->addSelectColumn(AppHistoryPeer::USR_UID);
        $c->addSelectColumn(AppHistoryPeer::APP_STATUS);
        $c->addSelectColumn(AppHistoryPeer::HISTORY_DATE);
        $c->addSelectColumn(AppHistoryPeer::HISTORY_DATA);
        
        //WHERE
        $c->add(AppHistoryPeer::PRO_UID, $PRO_UID);
        $c->add(AppHistoryPeer::TAS_UID, $TAS_UID);
        $c->add(AppHistoryPeer::APP_UID, $APP_UID);
        if((isset($DYN_UID))&&($DYN_UID!="")){
            $c->add(AppHistoryPeer::$DYN_UID, $DYN_UID);
        }
        
        //ORDER BY
        $c->clearOrderByColumns();
        $c->addAscendingOrderByColumn(AppHistoryPeer::HISTORY_DATE);
        
        
        //Execute
        $oDataset = AppHistoryPeer::doSelectRS($c);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        
        $aDynHistory = array();
        $aDynHistory[] = array(
      		'DYN_TITLE' => 'char'
        );
        
        while ($aRow = $oDataset->getRow()) {
                        
            $o = new Dynaform();
            $o->setDynUid($aRow['DYN_UID']);
            $aRow['DYN_TITLE'] = $o->getDynTitle();
            $changedValues=unserialize($aRow['HISTORY_DATA']);
            $html="<table border='0' cellpadding='0' cellspacing='0'>";
            $sw_add=false;
            foreach($changedValues as $key =>$value){
                if($value!=NULL){
                    $sw_add=true;
                    $html.="<tr>";
                    $html.="<td><b>$key:</b> </td>";
                    $html.="<td>$value</td>";
                    $html.="</tr>";
                }            
            }
            $html.="</table>";
            $aRow['FIELDS']    = $html;
                       
            if($sw_add){
                $aDynHistory[] = $aRow;
            }            
            $oDataset->next();
        }
        
        global $_DBArray;
        $_DBArray['DynaformsHistory'] = $aDynHistory;
        $_SESSION['_DBArray'] = $_DBArray;
        G::LoadClass('ArrayPeer');
        $oCriteria = new Criteria('dbarray');
        $oCriteria->setDBArrayTable('DynaformsHistory');
        //$oCriteria->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_CREATE_DATE);
        return $oCriteria;
    
    }

} // AppHistory
