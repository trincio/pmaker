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
        $this->setProUid($aData['TAS_UID']);
        $this->setTasUid($aData['APP_UID']);
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

} // AppHistory
