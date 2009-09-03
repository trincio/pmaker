<?php 

$request = isset($_POST['request'])? $_POST['request']: null;
if( !isset($request) ){
	$request = isset($_GET['request'])? $_GET['request']: null;
}
if( isset($request) ){
	switch($request){
		case 'deleteGridRowOnDynaform':
			
			if( isset($_SESSION['APPLICATION']) ){
				G::LoadClass('case');
			 	$oApp= new Cases();
			  	$aFields = $oApp->loadCase($_SESSION['APPLICATION']);
			  	unset($aFields['APP_DATA'][$_POST['gridname']][$_POST['rowpos']]);
			  	$oApp->updateCase($_SESSION['APPLICATION'], $aFields);
			}
			
		break;
	}	
}

