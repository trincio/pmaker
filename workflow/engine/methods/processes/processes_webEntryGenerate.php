<?php
    global $G_FORM;
    $sPRO_UID  = $oData->PRO_UID;
    $sTASKS    = $oData->TASKS;
    $sDYNAFORM = $oData->DYNAFORM;
    $sWE_TYPE  = $oData->WE_TYPE;
    $sWS_USER  = $oData->WS_USER;
    $sWS_PASS  = $oData->WS_PASS;
    $sWS_ROUNDROBIN = $oData->WS_ROUNDROBIN;
    
    $withWS = $sWE_TYPE == 'WS';

  try {
    $pathProcess = PATH_DATA_SITE . 'public' . PATH_SEP .  $sPRO_UID. PATH_SEP ;
    G::mk_dir ( $pathProcess, 0777 );

    $oTask    = new Task();
    $TaskFields = $oTask->load( $sTASKS);          
    if($TaskFields['TAS_ASSIGN_TYPE'] != 'BALANCED' )  { 
    	throw ( new Exception ( "The task '" . $TaskFields['TAS_TITLE'] . "' doesn't have a valid assignment type. The task needs to have a 'Cyclical Assignment'.") );
    }

    G::LoadClass('tasks');
    $oTask = new Tasks();
    $user = $oTask->assignUsertoTask($sTASKS);
    
    if($user == 0)  { 
    	throw ( new Exception ( "The task '" . $TaskFields['TAS_TITLE'] . "' doesn't have users.") );
    }
    
    //get the variables before render the template 
    if (G::is_https())
      $http= 'https://';
    else
   	  $http= 'http://';

    require_once 'classes/model/Dynaform.php';
	  $del = DBAdapter::getStringDelimiter();

    $oCriteria = new Criteria('workflow');
    $oCriteria->addSelectColumn(DynaformPeer::DYN_FILENAME);
    $oCriteria->add(DynaformPeer::DYN_UID, $sDYNAFORM);
    $oDataset = DynaformPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    $aRow = $oDataset->getRow();
    $dynFilename = $aRow['DYN_FILENAME'];

    $oCriteria = new Criteria('workflow');
    //$oCriteria->addSelectColumn(DynaformPeer::DYN_FILENAME);
    $oCriteria->add(ContentPeer::CON_CATEGORY, 'DYN_TITLE');
    $oCriteria->add(ContentPeer::CON_ID,  $sDYNAFORM);
    $oCriteria->add(ContentPeer::CON_LANG, SYS_LANG );
    $oDataset = ContentPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    $aRow = $oDataset->getRow();
    $dynTitle = $aRow['CON_VALUE'];

    $G_FORM = new Form ( $dynFilename, PATH_DYNAFORM  , SYS_LANG, false );
    if ( $withWS ) 
     	$Target = $http.$_SERVER['HTTP_HOST'].'/sys'.SYS_SYS.'/'.SYS_LANG.'/'.SYS_SKIN . 
   	          '/' . $sPRO_UID. '/' . $dynTitle. 'Post.php';
    else 
     	$Target = $http.$_SERVER['HTTP_HOST'].'/sys'.SYS_SYS.'/'.SYS_LANG.'/'.SYS_SKIN . 
   	          '/services/cases_StartExternal.php';

    if ( defined ( 'ENABLE_ENCRYPT' ) && ENABLE_ENCRYPT == 'yes' )
      	$G_FORM->action  = urlencode( G::encrypt( $Target, URL_KEY ) );
    else
        $G_FORM->action  = $Target;

    $template = PATH_CORE . 'templates/'  . 'xmlform' . '.html';
    $scriptCode='';

    $scriptCode = $G_FORM->render( $template , $scriptCode );
    $scriptCode = str_replace('/controls/cal.gif', $http.$_SERVER['HTTP_HOST'].'/controls/cal.gif', $scriptCode );
    $scriptCode = str_replace('</form>', '' ,$scriptCode );

    //render the template
    $pluginTpl = PATH_CORE . 'templates' . PATH_SEP . 'processes' .PATH_SEP . 'webentry.tpl';
    $template  = new TemplatePower( $pluginTpl );
    $template->prepare();

    $template->assign ( 'siteUrl', $http . $_SERVER['HTTP_HOST'] );
    $template->assign ( 'sysLang', SYS_LANG );
    $template->assign ( 'processUid',  $sPRO_UID );
    $template->assign ( 'dynaformUid', $sDYNAFORM );
    $template->assign ( 'taskUid',     $sTASKS );
    $template->assign ( 'dynFileName', $dynFilename );
    $template->assign ( 'formId',      $G_FORM->id );
    $template->assign ( 'scriptCode',  $scriptCode);
    $content = $template->getOutputContent();
    
    if ( $withWS ) {
      //saving the resulting .php file in the public/PRO_UID/ directory
      $fileName = $pathProcess . $dynTitle . '.php';
      $iSize = file_put_contents ( $fileName, $content );

      //creating the second file, the  post file who receive the post form.
      $pluginTpl = PATH_CORE . 'templates' . PATH_SEP . 'processes' .PATH_SEP . 'webentryPost.tpl';
      $template  = new TemplatePower( $pluginTpl );
      $template->prepare();
  
      $template->assign ( 'wsdlUrl', $http . $_SERVER['HTTP_HOST']. '/sys' . SYS_SYS .'/en/green/services/wsdl');
      $template->assign ( 'processUid',  $sPRO_UID );
      $template->assign ( 'dynaformUid', $sDYNAFORM );
      $template->assign ( 'taskUid',     $sTASKS );
      $template->assign ( 'wsUser',      $sWS_USER );
      $template->assign ( 'wsPass',      'md5:'.md5( $sWS_PASS ) );
      $template->assign ( 'wsRoundRobin',$sWS_ROUNDROBIN );

      $fileName = $pathProcess . $dynTitle . 'Post.php';
      $content = $template->getOutputContent();
      $iSize = file_put_contents ( $fileName, $content );

      //creating the third file, only if this wsClient.php file doesn't exists.
      $fileName = $pathProcess . 'wsClient.php';
      if ( ! file_exists ($fileName) ) {
        $pluginTpl = PATH_CORE . 'templates' . PATH_SEP . 'processes' .PATH_SEP . 'wsClient.tpl';
        $template  = new TemplatePower( $pluginTpl );
        $template->prepare();
        $content = $template->getOutputContent();
        $iSize = file_put_contents ( $fileName, $content );
      }
      $link = $http . $_SERVER['HTTP_HOST']. '/sys' . SYS_SYS .'/en/green/' . $sPRO_UID . '/' . $dynTitle .'.php';

      print "<br><a href='$link' target='_new' > $link  </a>";
    }
    else 
      print_r('<textarea cols="70" rows="20">'.htmlentities($content).'</textarea>');

    
  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'raw' );
  }

