<?php
/**
 * $Id$
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * You can contact Colosa Inc, 2655 Le Jeune Road, Suite 1112, Coral Gables,
 * FL 33134, USA or email info@colosa.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * ProcessMaker" logo and retain the original copyright notice. If the display
 * of the logo is not reasonably feasible for technical reasons, the
 * Appropriate Legal Notices must display the words "Powered by ProcessMaker"
 * and retain the original copyright notice.
 * -
 */
/**
 * @package gulliver.system2
*/

 /**
 * Publisher class definition
 * It is to publish all content in a page
 * @package gulliver.system2
 * @author Fernando Ontiveros Lira <fernando@colosa.com>
 * @copyright (C) 2002 by Colosa Development Team.
 *
 */
class Publisher
{
  var $Parts = NULL;
  var $dbc   = NULL;
  var $scriptFile = '';
  var $publisherId = 'publisherContent';

  /* PHP 4 doesn't provide destructor where to free $scriptFileHandler resource */
  //var $scriptFileHandler = FALSE;

   /**
   * Add content in $Parts array
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   *
   * @param  $strType
   * @param  $strLayout
   * @param  $strName
   * @param  $strContent
   * @param  $arrData
   * @param  $strTarget
   * @return void
   *
   */
  function AddContent( $strType = "form", $strLayout = "form", $strName = "", $strContent = "", $arrData = NULL, $strTarget = "", $ajaxServer='')
  {
    $pos = 0;
    if( is_array($this->Parts ))
    {
      $pos = count($this->Parts);
    }
    $this->Parts[$pos] = array(
        'Type'     => $strType,
			  'Template' => $strLayout,
			  'File'     => $strName,
			  'Content'  => $strContent,
			  'Data'     => $arrData,
			  'Target'   => $strTarget,
			  'ajaxServer'   => $ajaxServer
			  );

    //This is needed to prepare the "header content"
		//before to send the body content. ($G_HEADER)
		ob_start();
		$this->RenderContent0($pos);
		if ((ob_get_contents()!=='') && ($this->publisherId!=='')) {
		//	var_dump($strContent);
  		$this->Parts[$pos]['RenderedContent'] = '<DIV id="'.$this->publisherId.'['.$pos.']" style="'.((is_string($strContent))?$strContent:'').'; margin:0px;" align="center">';
  		$this->Parts[$pos]['RenderedContent'].= ob_get_contents();
  		$this->Parts[$pos]['RenderedContent'].= '</DIV>';
  	} else {
  		$this->Parts[$pos]['RenderedContent']= ob_get_contents();
	  }
		ob_end_clean();
  }


    /**
   * Set objConnection
   *
   * @param  objConnection
   * @return void
   *
   */
function SetTo( &$objConnection )
  {
    if( is_object( $objConnection ) )
    {
      $this->dbc = $objConnection;
    }
  }

  /**
   * Function RenderContent
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string intPos
   * @parameter string showXMLFormName
   * @return string
   */
  function RenderContent( $intPos = 0)
  {
    print $this->Parts[$intPos]['RenderedContent'];
  }

    /**
   * It Renders content according to Part['Type']
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   *
   * @param  intPos = 0
   * @return void
   *
   */
function RenderContent0( $intPos = 0, $showXMLFormName = false)
  {
    global $G_FORM;
    global $G_TABLE;
    global $G_TMP_TARGET;
    global $G_OP_MENU;
    global $G_IMAGE_FILENAME;
    global $G_IMAGE_PARTS;
    global $HTTP_SESSION_VARS;
    $this->intPos = $intPos;

    $Part = $this->Parts[ $intPos ];
    switch( $Part['Type'] )
    {
    case 'showcontent':
      $G_CONTENT = new Content;
      $G_CONTENT = G::LoadContent( $Part['File'] );
      $G_CONTENT->output ( $Part['Template'] );
      return;
      break;

    case 'content':
      break;

    case 'externalContent':
	    $G_CONTENT = new Content;
	    if( $Part['Content'] != "" ) $G_CONTENT = G::LoadContent( $Part['Content'] );
	    G::LoadTemplateExternal( $Part['Template'] );
      break;

    case 'image':
       $G_IMAGE_FILENAME = $Part['File'];
       $G_IMAGE_PARTS = $Part['Data'];
       break;

    case 'appform':
      global $APP_FORM;
      $G_FORM = $APP_FORM;
      break;

    case 'xmlform':
    case 'dynaform':
      global $G_FORM, $G_HEADER;
      global $G_HEADER;
      if ($Part['Type'] == 'xmlform')
      	$sPath = PATH_XMLFORM;
      else
      	$sPath = PATH_DYNAFORM;

      //if the xmlform file doesn't exists, then try with the plugins folders
      if ( !is_file ( $sPath . $Part['File'] ) ) {
        $aux = explode ( PATH_SEP, $Part['File'] );
        //check if G_PLUGIN_CLASS is defined, because publisher can be called without an environment
        if ( count($aux) == 2 && defined ( 'G_PLUGIN_CLASS' ) ) {
          $oPluginRegistry =& PMPluginRegistry::getSingleton();
          if ( $oPluginRegistry->isRegisteredFolder($aux[0]) ) {
            $sPath = PATH_PLUGINS;
          }
        }
      }

      if (!class_exists($Part['Template']) || $Part['Template']==='xmlform')
        $G_FORM = new Form ( $Part['File'] , $sPath , SYS_LANG, false );
      else
        eval( '$G_FORM = new ' . $Part['Template'] . ' ( $Part[\'File\'] , "' . $sPath . '");');

      if (($Part['Type'] == 'dynaform') && ($Part['Template'] == 'xmlform'))
      {
      	$G_FORM->fields=G::array_merges(
      		array('__DYNAFORM_OPTIONS' => new XmlForm_Field_XmlMenu(
      			new Xml_Node(
      				'__DYNAFORM_OPTIONS',
      				'complete',
      				'',
      				array('type'=>'xmlmenu','xmlfile'=>'gulliver/dynaforms_Options')
      				),SYS_LANG,PATH_XMLFORM,$G_FORM) ),
      		$G_FORM->fields);
      }

      //Needed to make ajax calls
      //Now it's is part of the default modules.
      //$G_HEADER->addInstanceModule('leimnud', 'rpc');

      //The action in the form tag.
      if ( defined ( 'ENABLE_ENCRYPT' ) && ENABLE_ENCRYPT == 'yes' )
      	$G_FORM->action  = urlencode( G::encrypt( $Part['Target'] ,URL_KEY ) );
      else
        $G_FORM->action  = $Part['Target'];

      if (!(isset($Part['ajaxServer']) && ($Part['ajaxServer']!=='')))
        if ($Part['Type'] == 'dynaform')
          $Part['ajaxServer']='../gulliver/defaultAjaxDynaform';
        else
          $Part['ajaxServer']='../gulliver/defaultAjax';

      if ( defined ( 'ENABLE_ENCRYPT' ) && ENABLE_ENCRYPT == 'yes' )
      	$G_FORM->ajaxServer  = urlencode( G::encrypt( $Part['ajaxServer'] ,URL_KEY ) );
      else
        $G_FORM->ajaxServer  = $Part['ajaxServer'];

      $G_FORM->setValues ($Part['Data']);
      $G_FORM->setValues ( array( 'G_FORM_ID' => $G_FORM->id ) );
      //Asegurese de que no entre cuando $Part['Template']=="grid"
      //de hecho soo deberia usarse cuando $Part['Template']=="xmlform"
      if (($Part['Type'] == 'dynaform') || ($Part['Template']=="xmlform"))
      {//var_dump($G_FORM->values, $Part['Data']);
      	$G_FORM->values=G::array_merges(
      	array('__DYNAFORM_OPTIONS' => isset($Part['Data']['__DYNAFORM_OPTIONS'])? $Part['Data']['__DYNAFORM_OPTIONS']:''),
      	$G_FORM->values);
      }
      if (isset($_SESSION)) $_SESSION[$G_FORM->id]=$G_FORM->values;

        $template = PATH_CORE . 'templates/'  . $Part['Template'] . '.html';
        if ($Part['Template'] == 'grid') print ('<form class="formDefault">');
        $scriptCode='';
        print $G_FORM->render( $template , $scriptCode );
        if ($Part['Template'] == 'grid') print ('</form>');
        $G_HEADER->addScriptFile( $G_FORM->scriptURL );
        $G_HEADER->addScriptCode( $scriptCode );
      break;

    case 'pagedtable':
      global $G_FORM;
      global $G_HEADER;

      //if the xmlform file doesn't exists, then try with the plugins folders
      $sPath = PATH_XMLFORM;
      if ( !is_file ( $sPath . $Part['File'] ) ) {
        $aux = explode ( PATH_SEP, $Part['File'] );
        if ( count($aux) == 2 ) {
          $oPluginRegistry =& PMPluginRegistry::getSingleton();
          if ( $oPluginRegistry->isRegisteredFolder($aux[0]) ) {
            $sPath = PATH_PLUGINS; // . $aux[0] . PATH_SEP ;
          }
        }
      }

      $G_FORM = new Form ( $Part['File'] , $sPath, SYS_LANG, true );

      if ( defined ( 'ENABLE_ENCRYPT' ) && ENABLE_ENCRYPT == 'yes' )
      	$G_FORM->ajaxServer  = urlencode( G::encrypt( $Part['ajaxServer'] ,URL_KEY ) );
      else
        $G_FORM->ajaxServer  = $Part['ajaxServer'];

      $G_FORM->setValues ($Part['Data']);
      if (isset($_SESSION)) $_SESSION[$G_FORM->id]=$G_FORM->values;

  		$G_HEADER->addScriptFile( '/js/form/core/pagedTable.js' );

  		$oTable                           = new pagedTable();
  		$oTable->template                 = 'templates/'.$Part['Template'] . '.html';
  		$G_FORM->xmlform                  = '';
  		$G_FORM->xmlform->fileXml         = $G_FORM->fileName;
  		$G_FORM->xmlform->home            = $G_FORM->home;
  		$G_FORM->xmlform->tree->attribute = $G_FORM->tree->attributes;
  		$G_FORM->values                   = array_merge($G_FORM->values,$Part['Data']);

  		$oTable->setupFromXmlform($G_FORM);

  		if (isset($Part['ajaxServer']) && ($Part['ajaxServer']!==''))
  		  $oTable->ajaxServer  = $Part['ajaxServer'];
  		/* Start Block: Load user configuration for the pagedTable */
        G::LoadClass('configuration');
		$objUID = $Part['File'];
        $conf = new Configurations(  );
        $conf->loadConfig($oTable,'pagedTable',$objUID,'',$_SESSION['USER_LOGGED'],'');
        $oTable->__OBJ_UID=$objUID;
      /* End Block */

      /* Start Block: PagedTable Right Click */
        G::LoadClass('popupMenu');
        $pm=new popupMenu('gulliver/pagedTable_PopupMenu');
        $pm->name=$oTable->id;
        $fields=array_keys($oTable->fields);
        foreach($fields as $f) {
          switch (strtolower($oTable->fields[$f]['Type']))
          {
            case 'javascript':
            case 'button':
            case 'private':
            case 'hidden':
            case 'cellmark':
            break;
            default:
            $label=($oTable->fields[$f]['Label']!='')?$oTable->fields[$f]['Label']:$f;
            $label=str_replace("\n",' ',$label);
            $pm->fields[$f]=new XmlForm_Field_popupOption(new Xml_Node($f, 'complete', '', array('label'=>$label,'type'=>'popupOption',
            'launch'=>$oTable->id.'.showHideField("'.$f.'")') ) );
            $pm->values[$f]='';
          }
        }
        $sc='';
        $pm->values['PAGED_TABLE_ID']=$oTable->id;
        print($pm->render(PATH_CORE . 'templates/popupMenu.html',$sc));
      /* End Block */

  		$oTable->renderTable();

  	  /* Start Block: Load PagedTable Right Click */
  	    print('<script type="text/javascript">');
        print($sc);
  	    print('loadPopupMenu_'.$oTable->id.'();');
  	    print('</script>');
  	  /* End Block */
      break;

    case 'propeltable':
      global $G_FORM;
      global $G_HEADER;

      //if the xmlform file doesn't exists, then try with the plugins folders
      $sPath = PATH_XMLFORM;
      if ( !is_file ( $sPath . $Part['File'] ) ) {
        $aux = explode ( PATH_SEP, $Part['File'] );
        if ( count($aux) == 2 ) {
          $oPluginRegistry =& PMPluginRegistry::getSingleton();
          if ( $oPluginRegistry->isRegisteredFolder($aux[0]) ) {
            $sPath = PATH_PLUGINS; // . $aux[0] . PATH_SEP ;
          }
        }
      }

      $G_FORM = new Form ( $Part['File'] , $sPath, SYS_LANG, true );

    	$G_FORM->ajaxServer  = urlencode( G::encrypt( $Part['ajaxServer'] ,URL_KEY ) );

      //$G_FORM->setValues ($Part['Data']);
      if (isset($_SESSION)) $_SESSION[$G_FORM->id] = $G_FORM->values;
  		$G_HEADER->addScriptFile( '/js/form/core/pagedTable.js' );
      G::LoadClass('propelTable');

      $oTable                           = new propelTable();
  		$oTable->template                 = 'templates/'.$Part['Template'] . '.html';
  		$oTable->criteria                 = $Part['Content'];
  		if ( isset($Part['ajaxServer'] ) && ( $Part['ajaxServer']!=='' ))
  		  $oTable->ajaxServer  = $Part['ajaxServer'];
  		$G_FORM->xmlform->fileXml         = $G_FORM->fileName;
  		$G_FORM->xmlform->home            = $G_FORM->home;
  		$G_FORM->xmlform->tree->attribute = $G_FORM->tree->attributes;
      if ( is_array($Part['Data'] ))
  		  $G_FORM->values                 = array_merge($G_FORM->values, $Part['Data'] );

  		$oTable->setupFromXmlform($G_FORM);
  	  /* Start Block: Load user configuration for the pagedTable */
        G::LoadClass('configuration');
      $objUID = $Part['File'];
      $conf = new Configurations( /*$oTable*/ );
      $conf->loadConfig($oTable,'pagedTable',$objUID,'',$_SESSION['USER_LOGGED'],'');
      $oTable->__OBJ_UID = $objUID;
      /* End Block */

      /* Start Block: PagedTable Right Click */
      G::LoadClass('popupMenu');
      $pm = new popupMenu( 'gulliver/pagedTable_PopupMenu' );
      $sc = $pm->renderPopup ( $oTable->id, $oTable->fields );
      /* End Block */
      try {
    		$oTable->renderTable();

        print($sc);
      }
      catch ( Exception $e ) {
        $aMessage['MESSAGE'] = $e->getMessage();
        $this->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
      }
      break;

    case 'panel-init':
      global $G_HEADER;
      global $mainPanelScript;
      global $panelName;
      global $tabCount;
      G::LoadThirdParty('pear/json','class.json');
      $json=new Services_JSON();
      //$G_HEADER->addInstanceModule('leimnud', 'panel');
      $tabCount = 0;
      $panelName = $Part['Template'];
      $data = $Part['File'];
      if (!is_array($data)) $data=array();
      $data = G::array_merges( array(
        'title'=>'',
        'style'=>array(),
        'left'=>'getAbsoluteLeft(mycontent)',
        'top'=>'getAbsoluteTop(mycontent)',
        'width'=>700,
        'height'=>600,
        'drag'=>true,
        'close'=>true,
        'modal'=>true,
        'roll'=>false,
        'resize'=>false,
        'tabWidth'=>120,
        'tabStep'=>3,
        'blinkToFront'=>true,
        'tabSpace'=>10), $data );

      $mainPanelScript = 'var '.$panelName.'={},'.$panelName.'Tabs=[];var dynaformEditor = {};'.
        'leimnud.event.add(window,"load",function(){'.$panelName.' = new leimnud.module.panel();'.
        'var mycontent=document.getElementById("'.$this->publisherId.'['.$intPos.']");'.
	      $panelName.'.options={'.
	        'size:{w:'.$data['width'].',h:'.$data['height'].'},'.
	        'position:{x:'.$data['left'].',y:'.$data['top'].'},'.
	        'title:"'.addcslashes($data['title'],'\\"').'",'.
	        'theme:"processmaker",'.
	        'statusBar:true,'.
	        'headerBar:true,'.
	        'control:{'.
	        '	close:'.($data['close']?'true':'false').','.
	        '	roll:'.($data['roll']?'true':'false').','.
	        '	drag:'.($data['drag']?'true':'false').','.
	        '	resize:'.($data['resize']?'true':'false').
	        '},'.
	        'fx:{'.
	        '	drag:'.($data['drag']?'true':'false').','.
	        '	modal:'.($data['modal']?'true':'false').','.
	        ' blinkToFront:'.($data['blinkToFront']?'true':'false').
	        '}'.
	      '};'.
        $panelName.'.setStyle='.$json->encode($data['style']).';'.
        $panelName.'.tab={'.
          'width:'.($data['tabWidth']+$data['tabSpace']).','.
          'optWidth:'.$data['tabWidth'].','.
          'step	:'.$data['tabStep'].','.
          'options:[]'.
          '};';
       print(' ');
      break;
    case 'panel-tab':
        global $G_HEADER;
        global $tabCount;
        global $mainPanelScript;
        global $panelName;
        $onChange = $Part['Content'];
        $beforeChange = $Part['Data'];
        $mainPanelScript .=
          $panelName.'Tabs['.$tabCount.']='.
          'document.getElementById("'.$Part['File'].'");'.
          $panelName.'.tab.options['.$panelName.'.tab.options.length]='.
          '{'.
          'title	:"'.addcslashes($Part['Template'],'\\"').'",'.
          'noClear	:true,'.
          'content	:function(){'.
              ($beforeChange!='' ? ('if (typeof('.$beforeChange.')!=="undefined") {'.$beforeChange.'();}'): '').
                $panelName.'Clear();'.
                $panelName.'Tabs['.$tabCount.'].style.display="";'.
//              'this.addContent('.$panelName.'Tabs['.$tabCount.']);'.
//              'this.addContent(document.getElementById("'.$Part['File'].'"));'.
//              $panelName.'Tabs['.$tabCount.']='.$panelName.'Tabs['.$tabCount.'].cloneNode( true );'.
              ($onChange!='' ? ('if (typeof('.$onChange.')!=="undefined") {'.$onChange.'();}'): '').
            '}.extend('.$panelName.'),'.
          'selected:'.($tabCount==0 ? 'true' : 'false').
          '};';
        $tabCount++;

      break;
    case 'panel-close':
      global $G_HEADER;
      global $mainPanelScript;
      global $panelName;
      global $tabCount;
      $mainPanelScript .= $panelName.'.make();';
      $mainPanelScript .= 'for(var r=0;r<'.$tabCount.';r++)'.
        'if ('.$panelName.'Tabs[r])'.$panelName.
        '.addContent('.$panelName.'Tabs[r]);';
      $mainPanelScript .= 'dynaformEditor={
	A:"",
	dynUid:"",
	ajax:"",
	currentView:"preview",
	views:[],
	toolbar:{},
	htmlEditorLoaded:false,
	loadPressLoaded:true,
	codePressLoaded:false,
	_run:function()
	{
		//LOADING PARTS
		this.toolbar = document.getElementById("fields_Toolbar")
		mainPanel.elements.headerBar.style.backgroundColor="#CBDAEF";
		mainPanel.elements.headerBar.style.borderBottom="1px solid #808080";
		mainPanel.elements.headerBar.appendChild(this.toolbar);
		mainPanel.events.remove = function(){
		}
		this.refresh_preview();
	},
	_review:function()
	{

	},
	save:function(){
		try {
			this.saveCurrentView();
		} catch (e) {
			alert(e);
		}
		this.saveProperties();
		res=this.ajax.save(this.A,this.dynUid);
		if (res==0) {
			alert(G_STRINGS.ID_SAVED);
		}
		else
		{
			G.alert(res["*message"]);
		}
	},
	close:function()
	{
		var modified=this.ajax.is_modified(this.A,this.dynUid);
		if (typeof(modified)==="boolean")
		{
			if (!modified || confirm(G_STRINGS.ID_EXIT_WITHOUT_SAVING))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			if (typeof(modified["*message"])==="string") G.alert(modified["*message"]);
			return false;
		}
	},
	// Save functions
	saveCurrentView:function()
	{
		switch(this.currentView)
		{
			case "xmlcode":
				this.saveXmlCode();
				break;
			case "htmlcode":
				this.saveHtmlCode();
				break;
			case "javascripts":
				this.saveJavascript();
				break;
		}
	},
	saveXmlCode:function()
	{
//		var xmlCode = getField("XML").value;
		var xmlCode = this.getXMLCode();
		var todoRefreshXmlCode = xmlCode === null;
		if (todoRefreshXmlCode) return;
		var res = this.ajax.set_xmlcode(this.A,xmlCode);
		if (res!=="") G.alert(res);
	},
	saveHtmlCode:function()
	{
		var htmlCode = getField("HTML");
		todoRefreshHtmlCode = htmlCode === null;
		if (todoRefreshHtmlCode) return;
		var response=this.ajax.set_htmlcode(this.A,htmlCode.value);
		if (response) G.alert(response["*message"],"Error");
	},
	saveJavascript:function()
	{
		var field=getField("JS_LIST","dynaforms_JSEditor");
		var code=this.getJSCode();
		if (field.value)
		{
			var res=this.ajax.set_javascript(this.A,field.value,code);
			if (typeof(res["*message"])==="string")
			{
				G.alert(res["*message"]);
			}
		}
	},
	saveProperties:function()
	{
		var form=this.views["properties"].getElementsByTagName("form")[0];
		var post=ajax_getForm(form);
		var response=this.ajax.set_properties(this.A,this.dynUid,post);
		if (response!=0)
		{
			G.alert(response["*message"]);
		}
	},
	// Change view point functions
	changeToPreview:function()
	{
		if (this.currentView!="preview")this.refresh_preview();
		this.currentView="preview";
	},
	changeToXmlCode:function()
	{
		this.refresh_xmlcode();
		this.currentView="xmlcode";
	  if (this.loadPressLoaded && !XMLCodePress)
	  {
		  startXMLCodePress();
	  }
	},
	changeToHtmlCode:function()
	{
		this.refresh_htmlcode();
		this.currentView="htmlcode";
	},
	changeToFieldsList:function()
	{
		this.refreshFieldsList();
		this.currentView="fieldslist";
	},
	changeToJavascripts:function()
	{
		this.currentView="javascripts";
		this.refreshJavascripts();
	  if (this.loadPressLoaded && !JSCodePress)
	  {
		  startJSCodePress();
	  }
	},
	changeToProperties:function()
	{
		this.currentView="properties";
	},
	// Refresh functions
	refreshCurrentView:function()
	{
		switch(this.currentView)
		{
			case "preview":this.refresh_preview();break;
			case "htmlcode":this.refresh_htmlcode();break;
			case "xmlcode":this.refresh_xmlcode();break;
			case "fieldslist":this.refreshFieldsList();break;
			case "javascripts":this.refreshJavascripts();break;
			case "properties":this.refreshProperties();break;
		}
	},
	refresh_preview:function()
	{
		var editorPreview = document.getElementById("editorPreview");
		var	todoRefreshPreview = editorPreview === null;
		if (todoRefreshPreview) return;
		editorPreview.innerHTML = this.ajax.render_preview(this.A);
		var myScripts = editorPreview.getElementsByTagName("SCRIPT");
		this.runScripts(myScripts);
		delete myScripts;
	},
	refresh_htmlcode:function()
	{
		var dynaformEditorHTML = this.views["htmlcode"];
		if (this.htmlEditorLoaded)
		{
			var response=this.ajax.get_htmlcode(this.A);
			response={"html":response,
				"error":((typeof(response)==="string")?0:response)};
		}
		else
		{
			var response=this.ajax.render_htmledit(this.A);
		}
		if ((response.error==0) && (this.htmlEditorLoaded))
		{
			window._editorHTML.doc.body.innerHTML=response.html;
			html_html2();
			html2_html();
		}
		else if ((response.error==0) && (!this.htmlEditorLoaded))
		{
			dynaformEditorHTML.innerHTML=response.html;
			this.runScripts(dynaformEditorHTML.getElementsByTagName("SCRIPT"));
			this.htmlEditorLoaded=true;
		}
		else
		{
			dynaformEditorHTML.innerHTML=response.html;
			this.runScripts(dynaformEditorHTML.getElementsByTagName("SCRIPT"));
			G.alert(response.error["*message"],"Error");
		}
	},
	refresh_xmlcode:function()
	{
		var response=this.ajax.get_xmlcode(this.A);
		if (response.error===0)
		{
			//xmlCode.value = response.xmlcode;
			this.setXMLCode(response.xmlcode);
		}
		else
		{
			G.alert(response.error["*message"],"Error");
		}
	},
	refreshFieldsList:function() {
		ZHluYWZvcm1zL2ZpZWxkc19MaXN0LnhtbA______.refresh();
	},
	getJSCode:function()
	{
		if (JSCodePress)
		{
			return JSCodePress.getCode();
		}
		else
		{
			return getField("JS","dynaforms_JSEditor").value;
		}
	},
	setJSCode:function(newCode)
	{
		if (JSCodePress)
		{
//			JSCodePress.setCode(newCode);
			JSCodePress.edit(newCode,"javascript");
		}
		else
		{
			var code=getField("JS","dynaforms_JSEditor");
			code.value=newCode;
		}
	},
	getXMLCode:function()
	{
		if (XMLCodePress)
		{
			return XMLCodePress.getCode();
		}
		else
		{
			return getField("XML","dynaforms_XmlEditor").value;
		}
	},
	setXMLCode:function(newCode)
	{
		if (XMLCodePress)
		{
//			XMLCodePress.setCode(newCode);
			XMLCodePress.edit(newCode,"html");
		}
		else
		{
			var code=getField("XML","dynaforms_XmlEditor");
			code.value=newCode;
		}
	},
	refreshJavascripts:function()
	{
		var field=getField("JS_LIST","dynaforms_JSEditor");
		var res=this.ajax.get_javascripts(this.A,field.value);
		if (typeof(res["*message"])==="undefined")
		{
			while(field.options.length>0) field.remove(0);
			for(var i=0;i<res.aOptions.length;i++)
			{
				var optn = document.createElement ("OPTION");
				optn.text = res.aOptions[i].value;
				optn.value = res.aOptions[i].key;
				field.options[i]=optn;
			}
			this.setJSCode(res.sCode);
		}
		else
		{
			G.alert(response.error["*message"],"Error");
		}
	},
	refreshProperties:function()
	{
		var form=this.views["properties"].getElementsByTagName("form")[0];
		var prop=this.ajax.get_properties(this.A,this.dynUid);
		getField("A","dynaforms_Properties").value=prop.A;
		getField("DYN_UID","dynaforms_Properties").value=prop.DYN_UID;
		getField("PRO_UID","dynaforms_Properties").value=prop.PRO_UID;
		getField("DYN_TITLE","dynaforms_Properties").value=prop.DYN_TITLE;
		getField("DYN_TYPE","dynaforms_Properties").value=prop.DYN_TYPE;
		getField("DYN_DESCRIPTION","dynaforms_Properties").value=prop.DYN_DESCRIPTION;
		getField("WIDTH","dynaforms_Properties").value=prop.WIDTH;
		getField("ENABLETEMPLATE","dynaforms_Properties").checked=(prop.ENABLETEMPLATE=="1");
		getField("MODE","dynaforms_Properties").value=prop.MODE;
	},
	// Internal functions
	runScripts:function(scripts)
	{
		var myScripts=[];
		for(var rr=0; rr < scripts.length ; rr++){
			myScripts.push(scripts[rr].innerHTML);
		}
		for(var rr=0; rr < myScripts.length ; rr++){
			try {
				if (myScripts[rr]!=="")
					if (window.execScript)
							window.execScript( myScripts[rr], "javascript" );
						else
							window.setTimeout( myScripts[rr], 0 );
			} catch (e) {
				alert(e.description);
			}
		}
		delete myScripts;
	}
};
dynaformEditor.views["preview"]=document.getElementById("dynaformEditor[3]");
dynaformEditor.views["xmlcode"]=document.getElementById("dynaformEditor[4]");
dynaformEditor.views["htmlcode"]=document.getElementById("dynaformEditor[5]");
dynaformEditor.views["fieldslist"]=document.getElementById("dynaformEditor[6]");
dynaformEditor.views["javascripts"]=document.getElementById("dynaformEditor[7]");
dynaformEditor.views["properties"]=document.getElementById("dynaformEditor[8]");
loadEditor();' . '});';
      $mainPanelScript .= 'function '.$panelName.'Clear(){';
      $mainPanelScript .= 'for(var r=0;r<'.$tabCount.';r++)'.
        'if ('.$panelName.'Tabs[r])'.$panelName.'Tabs[r].style.display="none";}';
      $G_HEADER->addScriptCode( $mainPanelScript );

      break;
    case 'blank';
      print(' ');
      break;
    case 'varform':
      global $G_FORM;
      $G_FORM = new Form;
      G::LoadSystem("varform");
      $xml = new varForm;
      //$xml->parseFile (  );
      $xml->renderForm ($G_FORM, $Part['File']);
      $G_FORM->Values = $Part['Data'];
      $G_FORM->SetUp( $Part['Target'] );
      $G_FORM->width = 500;
      break;
    case 'table':
      $G_TMP_TARGET = $Part['Target'];
      $G_TABLE = G::LoadRawTable( $Part['File'], $this->dbc, $Part['Data'] );
      break;
    case 'menu':
      $G_TMP_TARGET = $Part['Target'];
      $G_OP_MENU = new Menu;
      $G_OP_MENU->Load( $Part['File'] );
      break;

    case 'smarty': //To do: Please check it 26/06/07
      $template = new Smarty();
      $template->compile_dir  = PATH_SMARTY_C;
      $template->cache_dir    = PATH_SMARTY_CACHE;
      $template->config_dir   = PATH_THIRDPARTY . 'smarty/configs';
      $template->caching      = false;
      $dataArray = $Part['Data'] ;

      // verify if there are templates folders registered, template and method folders are the same
      $folderTemplate = explode ( '/',$Part['Template'] );
      $oPluginRegistry =& PMPluginRegistry::getSingleton();
      if ( $oPluginRegistry->isRegisteredFolder( $folderTemplate[0] ) )
        $template->templateFile = PATH_PLUGINS . $Part['Template'] . '.html';
      else
        $template->templateFile = PATH_TPL . $Part['Template'] . '.html';

      //assign the variables and use the template $template
      $template->assign( $dataArray);
      print $template->fetch($template->templateFile);
      break;

    case 'template': //To do: Please check it 26/06/07
      if ( gettype( $Part['Data'] ) == 'array' ) {
        G::LoadSystem ( 'template' ); //template phpBB
        $template = new Template();
        $template->set_filenames(array('body' => $Part['Template'] . '.html') );
        $dataArray = $Part['Data'] ;
          if ( is_array ( $dataArray ) ) {
          	foreach ( $dataArray as $key => $val )
              if ( is_array ( $val ) ) {
              	foreach ( $val as $key_val => $val_array )
       	  			  $template->assign_block_vars( $key, $val_array );
              }
              else
   	            $template->assign_vars( array ( $key => $val ) );
            }
  	      $template->pparse('body' );
  	    }
  	    if ( gettype( $Part['Data'] ) == 'object' &&  strtolower(get_class ( $Part['Data'] )) == 'templatepower' ) {
          $Part['Data']->printToScreen();

    	  }
        return;
      break;
    case 'view':
      break;
    }

//    $G_CONTENT = new Content;
//    if( $Part['Content'] != "" ) $G_CONTENT = G::LoadContent( $Part['Content'] );
    G::LoadTemplate( $Part['Template'] );
    //$G_FORM = NULL;
    //$G_CONTENT = NULL;
    $G_TABLE = NULL;
  }
}
?>
