<?php
  $ipaddress = $_SERVER['REMOTE_ADDR'];
  try {   
    $aux = explode ( '|', $_GET['id'] );

    $index=0;
    $ObjUid = str_replace ( '"', '', $aux[$index++] );
    if ( isset ($_GET['v'] ))
      $versionReq = $_GET['v'];

    //downloading the file    
    $localPath     = PATH_DOCUMENT . 'input' . PATH_SEP ; 
    $newfilename = G::GenerateUniqueId() . '.pm';
   
    $downloadUrl = PML_DOWNLOAD_URL . '?id=' . $ObjUid;
    //print "<hr>$downloadUrl<hr>";
    
    G::LoadClass('processes');
    $oProcess = new Processes();
    $oProcess->downloadFile( $downloadUrl, $localPath, $newfilename);

    //getting the ProUid the file recent downloaded
    $oData = $oProcess->getProcessData ( $localPath . $newfilename  );

    $Fields['PRO_FILENAME']  = $newfilename;
    $Fields['IMPORT_OPTION'] = 2;

    $sProUid = $oData->process['PRO_UID'];     

    if ( $oProcess->processExists ( $sProUid ) ) {
      $G_MAIN_MENU            = 'processmaker';
      $G_ID_MENU_SELECTED     = 'PROCESSES';
      $G_PUBLISH = new Publisher;
      $G_PUBLISH->AddContent('xmlform', 'xmlform', 'processes/processes_ImportExisting', '', $Fields, 'processes_ImportExisting'  );
      G::RenderPage('publish');
      die;
    }

    $oProcess->createProcessFromData ($oData, $localPath . $newfilename );
    G::header ( 'Location: processes_List');

  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
  }      
   