<?php
    global $G_FORM;

    $sPRO_UID=$oData->PRO_UID;
    $sTASKS=$oData->TASKS;
    $sDYNAFORM=$oData->DYNAFORM;

    G::LoadClass('tasks');
    $oTask = new Tasks();
    $user = $oTask->assignUsertoTask($sTASKS);
    if($user==0)
    	{ echo "The task has not assigned a user";
    		die;
      }

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

   	$link1 = $http.$_SERVER['HTTP_HOST'].'/skins/'.SYS_SKIN.'/style.css';

    $js1   = $http.$_SERVER['HTTP_HOST'].'/jscore/labels/en.js';
    $js2   = $http.$_SERVER['HTTP_HOST'].'/js/maborak/core/maborak.js';
    $js2_  = $http.$_SERVER['HTTP_HOST'].'/js/jscalendar/calendar.js';
    $js2__ = $http.$_SERVER['HTTP_HOST'].'/js/jscalendar/lang/calendar-' . SYS_LANG . '.js';
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


    $link1 = '<link rel="stylesheet" type="text/css" href="'.$link1.'"/>';

    $js1   = '<script type="text/javascript" src="'.$js1.'"></script>';
    $js2   = '<script type="text/javascript" src="'.$js2.'"></script>';
    $js2_  = '<script type="text/javascript" src="'.$js2_.'"></script>';
    $js2__ = '<script type="text/javascript" src="'.$js2__.'"></script>';
		$js3   = '<script type="text/javascript" src="'.$js3.'"></script>';
		$js4   = '<script type="text/javascript" src="'.$js4.'"></script>';


		$js6 = '<script type="text/javascript">
  		var leimnud = new maborak();
  		leimnud.make();
  		leimnud.Package.Load("panel,validator,app,rpc,fx,drag,drop,dom,abbr",{Instance:leimnud,Type:"module"});
			leimnud.exec(leimnud.fix.memoryLeak);
  		if(leimnud.browser.isIphone)
			{  leimnud.iphone.make(); }
			leimnud.event.add(window,"load",function(){loadForm_'.$G_FORM->id.'("'.$x.'")});
			</script>';

    $js7 = '<script type="text/javascript">
  		      var aux1 = window.location.href.split("?");
  		      if(aux1[1])
  		      {
  		         if(aux1[1]!="")
  		         	{	var aux2 = aux1[1].split("&");
  		         		for(var i=0; i<=aux2.length; i++)
  		         		{  if(aux2[i]=="__flag__=1")
  		         				{
  		         					alert("Request sended");
  		         				}
  		         		}
  		         	}
  		      }
			      </script>';

   	$Target = $http.$_SERVER['HTTP_HOST'].'/sys'.SYS_SYS.'/'.SYS_LANG.'/'.SYS_SKIN.'/services/cases_StartExternal.php';

    if ( defined ( 'ENABLE_ENCRYPT' ) && ENABLE_ENCRYPT == 'yes' )
      	$G_FORM->action  = urlencode( G::encrypt( $Target, URL_KEY ) );
    else
        $G_FORM->action  = $Target;

    $template = PATH_CORE . 'templates/'  . 'xmlform' . '.html';
    $scriptCode='';

    $form = str_replace('/controls/cal.gif', $http.$_SERVER['HTTP_HOST'].'/controls/cal.gif', $G_FORM->render( $template , $scriptCode ));

    $form=str_replace('/images/bulletButton.gif',$img,$form);
    $form=str_replace('</form>','',$form);

    $hPRO_UID  = '<input type="hidden" name="PRO_UID" value="'.$sPRO_UID.'">';
    $hTASKS    = '<input type="hidden" name="TASKS" value="'.$sTASKS.'">';
    $hDYNAFORM = '<input type="hidden" name="DYNAFORM" value="'.$sDYNAFORM.'">';

    $nform = $link1."\n".$js1."\n".$js2."\n".$js2_."\n".$js2__."\n".$js3."\n".$js5."\n".$js4."\n".$js6."\n".$js7."\n".$form."\n".$hPRO_UID."\n".$hTASKS."\n".$hDYNAFORM.'</form>';

    print_r('<textarea cols="70" rows="20">'.htmlentities($nform).'</textarea>');
    //print_r($nform);
?>