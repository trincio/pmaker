<?php
    global $G_FORM;  
    $sPRO_UID=$oData->PRO_UID;
    $sTASKS=$oData->TASKS;
    $sDYNAFORM=$oData->DYNAFORM;      


    require_once 'classes/model/Dynaform.php';
    $oCriteria = new Criteria('workflow');
		$del = DBAdapter::getStringDelimiter();
    $oCriteria->addSelectColumn(DynaformPeer::DYN_FILENAME);
    $oCriteria->add(DynaformPeer::DYN_UID, $sDYNAFORM);
    $oDataset = DynaformPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    $aRow = $oDataset->getRow();    
    
    $G_FORM = new Form ( $aRow['DYN_FILENAME'], PATH_DYNAFORM  , SYS_LANG, false );               		
    
    if(G:: is_https())          	
     	  $http= 'https://';          	
    else		   	 
   	 	  $http= 'http://';   	 	     	 	     	 
   	
   	$img=$http.$_SERVER['HTTP_HOST'].'/images/bulletButton.gif';
   	$link1 = $http.$_SERVER['HTTP_HOST'].'/images/bulletButton.gif'; 	     	
   	$link2 = $http.$_SERVER['HTTP_HOST'].'/skins/'.SYS_SKIN.'/style.css'; 	  
   	
    $js1   = $http.$_SERVER['HTTP_HOST'].'/jscore/labels/en.js'; 
    $js2   = $http.$_SERVER['HTTP_HOST'].'/js/maborak/core/maborak.js';                       
    $js3   = $http.$_SERVER['HTTP_HOST'].'/jsform/gulliver/dynaforms_Options.js';
    $js4   = $http.$_SERVER['HTTP_HOST'].'/jsform/'.$aRow['DYN_FILENAME'].'.js';    
      
	  $js5='';           
    foreach($G_FORM->fields as $key=>$values)
    { 
      if($values->type=='grid')
      	{	      		      		
      		$y=$values->xmlGrid;   
      		$js5 = $js5.' <script type="text/javascript" src="'.$http.$_SERVER['HTTP_HOST'].'/jsform/'.$y.'.js"></script>';     		      		      		      		      		
        }	      		      
    } 
    //print_r($js5); die;       
	     
    $x = $http.$_SERVER['HTTP_HOST'].'/gulliver/defaultAjaxDynaform';
        					
		$link1 = '<link rel="shortcut icon" href="'.$link1.'"   type="image/x-icon"/>';
    $link2 = '<link rel="stylesheet" type="text/css" href="'.$link2.'"/>'; 	  
    
    $js1  = '<script type="text/javascript" src="'.$js1.'"></script>';
    $js2  = '<script type="text/javascript" src="'.$js2.'"></script>';				
		$js3  = '<script type="text/javascript" src="'.$js3.'"></script>';
		$js4  = '<script type="text/javascript" src="'.$js4.'"></script>';
		
		  
		$js6 = '<script type="text/javascript">
  		var leimnud = new maborak();
  		leimnud.make();
  		leimnud.Package.Load("panel,validator,app,rpc,fx,drag,drop,dom,abbr",{Instance:leimnud,Type:"module"});
			leimnud.exec(leimnud.fix.memoryLeak);
  		if(leimnud.browser.isIphone)
			{  leimnud.iphone.make(); }
			leimnud.event.add(window,"load",function(){loadForm_'.$G_FORM->id.'("'.$x.'")}); </script>';						    
        
    
   	$Target = $http.$_SERVER['HTTP_HOST'].'/sys'.SYS_SYS.'/'.SYS_LANG.'/'.SYS_SKIN.'/cases/cases_StartExternal.php';   			          
                  
    if ( defined ( 'ENABLE_ENCRYPT' ) && ENABLE_ENCRYPT == 'yes' )
      	$G_FORM->action  = urlencode( G::encrypt( $Target, URL_KEY ) );
    else
        $G_FORM->action  = $Target;				

    $template = PATH_CORE . 'templates/'  . 'xmlform' . '.html';       
    $scriptCode='';                  
     
    $form = $G_FORM->render( $template , $scriptCode );
    
    $form=str_replace('/images/bulletButton.gif',$img,$form);
    $form=str_replace('</form>','',$form);
    $hPRO_UID  = '<input type="hidden" name="PRO_UID" value="'.$sPRO_UID.'">';
    $hTASKS    = '<input type="hidden" name="TASKS" value="'.$sTASKS.'">';
    $hDYNAFORM = '<input type="hidden" name="DYNAFORM" value="'.$sDYNAFORM.'">';    
        
    $nform = $link1.$link2.$js1.$js2.$js3.$js5.$js4.$js6.$form.'<br />'.$hPRO_UID.'<br />'.$hTASKS.'<br />'.$hDYNAFORM.'</form>';
    
    print_r('<textarea cols="70" rows="20">'.htmlentities($nform).'</textarea>');
    //print_r($nform);
?>