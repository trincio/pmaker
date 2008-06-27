<?php
     //print_r($_POST['PRO_UID'].'<br />'.$_POST['TASKS'].'<br />'.$_POST['DYNAFORM']);
          
     require_once ("classes/model/AppDelegation.php");     
     require_once ( "classes/model/Users.php" );
     G::LoadClass('case');       
     G::LoadClass('derivation');       
     $result  = array();
		 $oCriteria = new Criteria('workflow');
		 $del = DBAdapter::getStringDelimiter();
		 $oCriteria->addSelectColumn(AppDelegationPeer::APP_UID);
		 $oCriteria->addSelectColumn(AppDelegationPeer::DEL_INDEX);
		 $oCriteria->add(AppDelegationPeer::PRO_UID, $_POST['PRO_UID']);
		 $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
		 $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
		 $oDataset->next();
     $aRow = $oDataset->getRow();					 
			
                     
     $aData['APP_UID']   = $aRow['APP_UID'];     
     $aData['DEL_INDEX'] = $aRow['DEL_INDEX'];
     
     $oDerivation = new Derivation();
     $derive  = $oDerivation->prepareInformation($aData);
     
     print_r($derive);
     
     try {  	
     $oCase = new Cases();
     $aData = $oCase->startCase( $_POST['TASKS'], $_SESSION['USER_LOGGED'] );
     $_SESSION['APPLICATION']   = $aData['APPLICATION'];
     $_SESSION['INDEX']         = $aRow['APP_UID'];
     $_SESSION['PROCESS']       = $_POST['PRO_UID']; //$aData['PROCESS'];
     $_SESSION['TASK']          = $_POST['TASKS'];
     $_SESSION['STEP_POSITION'] = 0;
     
     $oCase     = new Cases();
     $aNextStep = $oCase->getNextStep($_POST['PRO_UID'], $aData['APPLICATION'], $aRow['DEL_INDEX'], $_SESSION['STEP_POSITION']);
     
     G::header('location: ' . $aNextStep['PAGE']);
     }
     catch ( Exception $e ) {
       $_SESSION['G_MESSAGE']      = $e->getMessage();
       $_SESSION['G_MESSAGE_TYPE'] = 'error';
       G::header('location: cases_New' );
     }      
      
?>