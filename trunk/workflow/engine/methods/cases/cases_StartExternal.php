<?php
     try {  	
     //print_r($_POST['PRO_UID'].'<br />'.$_POST['TASKS'].'<br />'.$_POST['DYNAFORM']).'<br />';         
     require_once ( "classes/model/Task.php" );
     require_once ( "classes/model/Users.php" );
     G::LoadClass('case');    
     G::LoadClass('derivation');  
     
     $oTask    = new Task();
     $TaskFields = $oTask->load( $_POST['TASKS'] );          
     $aDerivation['NEXT_TASK'] = $TaskFields;
     $oDerivation = new Derivation();  
     $deriva = $oDerivation->getNextAssignedUser($aDerivation);    
     
     $oCase = new Cases();
     $aData = $oCase->startCase( $_POST['TASKS'], $deriva['USR_UID'] );    		 
     
     
     $case = $oCase->loadCase($aData['APPLICATION'], 1);
     
     $Fields = array();
     $Fields['APP_NUMBER']      = $case['APP_NUMBER'];
     $Fields['APP_PROC_STATUS'] = 'draft';
     $Fields['APP_DATA']        = $_POST['form'];
     $Fields['DEL_INDEX']       = 1;
     $Fields['TAS_UID']         = $_POST['TASKS'];
     
     $oCase->updateCase( $aData['APPLICATION'], $Fields );
     
          
     if($_SERVER['HTTP_REFERER']!='')     
     		G::header('location: ' . $_SERVER['HTTP_REFERER']);     		
     else
     		echo"Se registro ok";		
     }
     catch ( Exception $e ) {
       $_SESSION['G_MESSAGE']      = $e->getMessage();
       $_SESSION['G_MESSAGE_TYPE'] = 'error';
       G::header('location: cases_New' );
     }      
      
?>