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
/*
 * Created on 21/12/2007
 *
 * @author David Callizaya <davidsantos@colosa.com>
 */
G::LoadSystem("webResource");
G::LoadClass('toolBar');
G::LoadClass('dynaFormField');
require_once('classes/model/Process.php');
require_once('classes/model/Dynaform.php');
G::LoadClass('xmlDb');

class dynaformEditor extends WebResource
{
	private $isOldCopy=false;
	var $file='';
	var $title='New Dynaform';
	var $dyn_uid='';
	var $dyn_type='';
	var $home='';
	/*
	 * Other Options for Editor:
	 *   left: 'getAbsoluteLeft(document.getElementById("dynaformEditor[0]"))',
	 *   top: 'getAbsoluteTop(document.getElementById("dynaformEditor[0]"))',
	 *   height: '3/4*(document.body.clientWidth-getAbsoluteLeft(document.getElementById("dynaformEditor[0]"))*2)',
	 *   left: 'getAbsoluteLeft(document.getElementById("dynaformEditor[0]"))'
	 *   left: 'getAbsoluteLeft(document.getElementById("dynaformEditor[0]"))'
	 *
	 * Other Options for Toolbar:
	 *   left: 'getAbsoluteLeft(document.getElementById("dynaformEditor[0]"))',
	 *   top: 'getAbsoluteTop(document.getElementById("dynaformEditor[0]"))',
	 */
	var $defaultConfig = array(
			'Editor'=>array(
				'left'=>'0',
				'top'=>'0',
				'width'=>'document.body.clientWidth-4',
				'height'=>'document.body.clientHeight-4'
			),
			'Toolbar'=>array(
				'left'=>'document.body.clientWidth-2-toolbar.clientWidth-24-3+7',
				'top'=>'52'
			),
			'FieldsList'=>array(
				'left'=>'4+toolbar.clientWidth+24',
				'top'=>'getAbsoluteTop(document.getElementById("dynaformEditor[0]"))',
				'width'=> 244,
				'height'=>400,
			)
		);
	var $panelConf=array(
			'style'       =>array(
				'title'=>array('textAlign'=>'left')
			),
			'width'       =>700,
			'height'      =>600,
			'tabWidth'    =>120,
			'modal'       =>true,
			'drag'        =>false,
			'resize'      =>false,
			'blinkToFront'=>false
		);
	/* Constructor
	 **/
	function dynaformEditor($get)
	{
		$this->panelConf = array_merge( $this->panelConf , $this->defaultConfig['Editor'] );
		//'title' => G::LoadTranslation('ID_DYNAFORM_EDITOR').' - ['.$this->title.']',
	}
	function _createDefaultXmlForm($fileName)
	{
		//Create the default Dynaform
		$sampleForm='<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$sampleForm.='<dynaForm type="'.$this->dyn_type.'" name="" width="500" enabletemplate="0" mode="edit">'."\n";
		switch ($this->dyn_type)
		{
			case "xmlform":
				$sampleForm.='<title type="title" enablehtml="0">' . "\n" .
						'  <en>Sample form</en>' . "\n" .
						'</title>'."\n";
				$sampleForm.='<submit type="submit" enablehtml="0" onclick="">' . "\n" .
						'  <en>Submit</en>' . "\n" .
						'</submit>'."\n";
				break;
			case "grid":
				$sampleForm.='<fieldA type="text" >' . "\n" .
						'<en>A</en>' . "\n" .
						'</fieldA>'."\n";
				$sampleForm.='<fieldB type="text" >' . "\n" .
						'<en>B</en>' . "\n" .
						'</fieldB>'."\n";
				break;
		}
		$sampleForm.='</dynaForm>';
		G::verifyPath(dirname($fileName), true);
		$fp=fopen($fileName,'w');
		$sampleForm=str_replace('name=""','name="'.$this->_getFilename($this->file).'"', $sampleForm );
		fwrite($fp, $sampleForm);
		fclose($fp);
	}
	/* Prints the DynaformEditor
	 **/
	function _render()
	{
		global $G_HEADER;
		global $G_PUBLISH;
		$script='';
		/* Start Block: Load (Create if not exists) the xmlform */
		$Parameters = array(
			'SYS_LANG' => SYS_LANG,
			'URL'=> G::encrypt( $this->file , URL_KEY ),
			'DYN_UID'=> $this->dyn_uid,
			'PRO_UID'=> $this->pro_uid,
			'DYNAFORM_NAME'=>$this->dyn_title
			);
		$XmlEditor = array(
			'URL'=> G::encrypt( $this->file , URL_KEY ),
			'XML'=> ''//$openDoc->getXml()
		);
		$JSEditor = array(
			'URL'=> G::encrypt( $this->file , URL_KEY ),
		);
		$A=G::encrypt( $this->file , URL_KEY );
		try
		{
			$openDoc = new Xml_Document();
			$fileName= $this->home . $this->file . '.xml';
			if (file_exists($fileName))
			{
				$openDoc->parseXmlFile($fileName);
			}
			else
			{
				$this->_createDefaultXmlForm($fileName);
				$openDoc->parseXmlFile($fileName);
			}
			//$form = new Form( $this->file , $this->home, SYS_LANG, true );
			$Properties = dynaformEditorAjax::get_properties( $A , $this->dyn_uid );
		}
		catch(Exception $e)
		{

		}
		/* Start Block: Prepare the XMLDB connection */
		define('DB_XMLDB_HOST', PATH_DYNAFORM  . $this->file . '.xml' );
		define('DB_XMLDB_USER','');
		define('DB_XMLDB_PASS','');
		define('DB_XMLDB_NAME','');
		define('DB_XMLDB_TYPE','myxml');
		/* Start Block: Prepare the dynaformEditor */
		$G_PUBLISH = new Publisher;
		$G_PUBLISH->publisherId='dynaformEditor';
		$G_HEADER->setTitle(G::LoadTranslation('ID_DYNAFORM_EDITOR'). ' - ' . $Properties['DYN_TITLE']);

		$G_PUBLISH->AddContent('blank');
		$this->panelConf['title']=$this->title;
		$G_PUBLISH->AddContent('panel-init', 'mainPanel', $this->panelConf );
		$G_PUBLISH->AddContent('xmlform', 'toolbar', 'dynaforms/fields_Toolbar', 'display:none', $Parameters , '', '');
		$G_PUBLISH->AddContent('xmlform', 'xmlform', 'dynaforms/dynaforms_Editor', 'display:none', $Parameters , '', '');
		$G_PUBLISH->AddContent('xmlform', 'xmlform', 'dynaforms/dynaforms_XmlEditor', 'display:none', $XmlEditor , '', '');
		$G_PUBLISH->AddContent('blank');
		try
		{
		$G_PUBLISH->AddContent('pagedtable', 'paged-table', 'dynaforms/fields_List', 'display:none', $Parameters , '', SYS_URI.'dynaforms/dynaforms_PagedTableAjax');
		} catch (Exception $e) {}
		try
		{
		$G_PUBLISH->AddContent('xmlform', 'xmlform', 'dynaforms/dynaforms_JSEditor', 'display:none', $JSEditor , '', '');
		} catch (Exception $e) {}
		$G_PUBLISH->AddContent('xmlform', 'xmlform', 'dynaforms/dynaforms_Properties', 'display:none', $Properties , '', '');

		$G_PUBLISH->AddContent('panel-tab',G::LoadTranslation("ID_PREVIEW"),'dynaformEditor[3]','dynaformEditor.changeToPreview','dynaformEditor.saveCurrentView');
		$G_PUBLISH->AddContent('panel-tab',G::LoadTranslation("ID_XML"),'dynaformEditor[4]','dynaformEditor.changeToXmlCode','dynaformEditor.saveCurrentView');
		$G_PUBLISH->AddContent('panel-tab',G::LoadTranslation("ID_HTML"),'dynaformEditor[5]','dynaformEditor.changeToHtmlCode','dynaformEditor.saveCurrentView');
		$G_PUBLISH->AddContent('panel-tab',G::LoadTranslation("ID_FIELDS_LIST"),'dynaformEditor[6]','dynaformEditor.changeToFieldsList','dynaformEditor.saveCurrentView');
		$G_PUBLISH->AddContent('panel-tab',G::LoadTranslation("ID_JAVASCRIPTS"),'dynaformEditor[7]','dynaformEditor.changeToJavascripts','dynaformEditor.saveCurrentView');
		$G_PUBLISH->AddContent('panel-tab',G::LoadTranslation("ID_PROPERTIES"),'dynaformEditor[8]','dynaformEditor.changeToProperties','dynaformEditor.saveCurrentView');
		$G_PUBLISH->AddContent('panel-close');
		/*$G_HEADER->addScriptFile('/jscore/dynaformEditor/core/dynaformEditor.js');*/
		/*$G_HEADER->addScriptCode('var dynaformEditor = {};leimnud.event.add(window,"load",function(){dynaformEditor={
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
	{alert("despues");
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
}
dynaformEditor.views["preview"]=document.getElementById("dynaformEditor[3]");
dynaformEditor.views["xmlcode"]=document.getElementById("dynaformEditor[4]");
dynaformEditor.views["htmlcode"]=document.getElementById("dynaformEditor[5]");
dynaformEditor.views["fieldslist"]=document.getElementById("dynaformEditor[6]");
dynaformEditor.views["javascripts"]=document.getElementById("dynaformEditor[7]");
dynaformEditor.views["properties"]=document.getElementById("dynaformEditor[8]");
loadEditor();
});');*/
		$G_HEADER->addScriptFile('/js/dveditor/core/dveditor.js');
		$G_HEADER->addScriptFile('/codepress/codepress.js',1);
		$G_HEADER->addScriptFile('/js/grid/core/grid.js');
		/*$G_HEADER->addScriptCode('leimnud.event.add(window,"load",function(){' .
				'dynaformEditor.views["preview"]=document.getElementById("dynaformEditor[3]");' .
				'dynaformEditor.views["xmlcode"]=document.getElementById("dynaformEditor[4]");' .
				'dynaformEditor.views["htmlcode"]=document.getElementById("dynaformEditor[5]");' .
				'dynaformEditor.views["fieldslist"]=document.getElementById("dynaformEditor[6]");' .
				'dynaformEditor.views["javascripts"]=document.getElementById("dynaformEditor[7]");' .
				'dynaformEditor.views["properties"]=document.getElementById("dynaformEditor[8]");' .
				'loadEditor();' .
				'});');*/
		G::RenderPage( "publish-treeview" );
	}
	function _getFilename($file)
	{
		return (strcasecmp(substr($file,-5),'_tmp0')==0)? substr($file,0,strlen($file)-5) : $file;
	}
	function _setUseTemporalCopy($onOff)
	{
		$file = self::_getFilename( $this->file );
		if ($onOff)
		{
			$this->file=$file.'_tmp0';
			self::_setTmpData(array('useTmpCopy'=>true));
			if (!file_exists(PATH_DYNAFORM  . $file . '.xml'))
				$this->_createDefaultXmlForm(PATH_DYNAFORM  . $file . '.xml');
			//Creates a copy if it does not exists, else, use the old copy
			if (!file_exists(PATH_DYNAFORM  . $this->file . '.xml'))
				self::_copyFile(PATH_DYNAFORM  . $file . '.xml',PATH_DYNAFORM  . $this->file . '.xml');
			if (!file_exists(PATH_DYNAFORM  . $this->file . '.html')
				&& file_exists(PATH_DYNAFORM  . $file . '.html'))
				self::_copyFile(PATH_DYNAFORM  . $file . '.html',PATH_DYNAFORM  . $this->file . '.html');
		}
		else
		{
			$this->file=$file;
			self::_setTmpData(array());
		}
	}
	function _setTmpData($data)
	{
		G::verifyPath(PATH_C . 'dynEditor/',true);
		$fp=fopen(PATH_C . 'dynEditor/'.session_id().'.php','w');
		fwrite($fp,'$tmpData=unserialize(\''.addcslashes(serialize($data),'\\\'').'\');');
		fclose($fp);
	}
	function _getTmpData()
	{
		$tmpData=array();
		$file=PATH_C . 'dynEditor/'.session_id().'.php';
		if (file_exists($file)) eval(implode('',file($file)));
		return $tmpData;
	}
	function _copyFile($from,$to)
	{
		$copy = implode('',file($from));
		$fcopy=fopen($to,"w");
		fwrite($fcopy, $copy);
		fclose($fcopy);
	}
}
interface iDynaformEditorAjax
{
	//public function render_preview($A);
}
class dynaformEditorAjax extends dynaformEditor implements iDynaformEditorAjax
{
	function dynaformEditorAjax($post)
	{
		$this->_run($post);
	}
	function _run($post)
	{
		WebResource::WebResource($_SERVER['REQUEST_URI'],$post);
	}
	function render_preview($A)
	{
		ob_start();
		$file = G::decrypt( $A , URL_KEY );
		global $G_HEADER;
		global $G_PUBLISH;
		$G_PUBLISH = new Publisher;
		$G_PUBLISH->publisherId='preview';
		$G_HEADER->clearScripts();
		$form = new Form( $file , PATH_DYNAFORM, SYS_LANG, true );
		switch(basename($form->template,'.html'))
		{
		  case 'grid': $template='grid';
		  	$aFields = (array_combine(array_keys($form->fields),array_keys($form->fields)));
		  	foreach($aFields as $key => $val) $aFields[$key]=array("","","","","");
		  break;
		  default: $template='xmlform';
			$aFields = array(
					'__DYNAFORM_OPTIONS'=> array(
					'PREVIOUS_STEP' => '#',
					'NEXT_STEP' => '#',
					'PREVIOUS_ACTION' => 'return false;',
					'NEXT_ACTION' => 'return false;'
				)
			);
		}
		$G_PUBLISH->AddContent('dynaform', $template , $file, '',
			$aFields, '');
		G::RenderPage('publish','raw');
		return ob_get_clean();
	}
	function render_htmledit($A)
	{
		$script='';
		$file = G::decrypt( $A , URL_KEY );
		ob_start();
		global $G_HEADER;
		global $G_PUBLISH;
		$form = new Form( $file , PATH_DYNAFORM, SYS_LANG, true );
		$G_PUBLISH = new Publisher;
		$G_PUBLISH->publisherId='';
		$G_HEADER->clearScripts();
		$html=$this->get_htmlcode($A);
		if (!is_string($html))
		{
			$error=$html;
			$html='';
		}
		else
		{
			$error=0;
		}
		$HtmlEditor = array(
			'URL'=> $A,
			'HTML'=> $html
		);
		$G_PUBLISH->AddContent('xmlform', 'xmlform', 'dynaforms/dynaforms_HtmlEditor', '', $HtmlEditor , '', '');
		G::RenderPage( "publish", 'raw' );
		return array('error'=>$error,'html'=>ob_get_clean());
	}
	function get_htmlcode($A)
	{
		try
		{
			$script='';
			$file = G::decrypt( $A , URL_KEY );
			$form = new Form( $file , PATH_DYNAFORM, SYS_LANG, true );
			/* Navigation Bar */
			$form->fields=G::array_merges(
				array('__DYNAFORM_OPTIONS' => new XmlForm_Field_XmlMenu(
					new Xml_Node(
						'__DYNAFORM_OPTIONS',
						'complete',
						'',
						array('type'=>'xmlmenu','xmlfile'=>'gulliver/dynaforms_Options')
			  		),SYS_LANG,PATH_XMLFORM,$form)
			  	),
				$form->fields);
			/**/
			/*
			 * Loads the stored HTML or the default Template if
			 * it does not exists.
			 */
			$filename = substr($form->fileName , 0, -3) .
				( $form->type==='xmlform' ? '' : '.' . $form->type  ) . 'html';
			if (!file_exists( $filename ))
			{
				$html=$form->printTemplate( $form->template , $script );
			}
			else
			{
				$html=implode( '', file( $filename ) );
			}
			/*
			 * It adds the new fields automatically at the bottom of the form.
			 * TODO: ¿TOP OR BOTTOM?
			 * TODO: Mejorar el algoritmo de deteccion de nuevos fields.
			 *       Actual: No revisar los fields que ya fueron revisados (guardando)
			 *       los ya revisados en el archivo temporal del editor de dynaforms.
			 */
			$tmp=self::_getTmpData();
			if (!isset($tmp['OLD_FIELDS'])) $tmp['OLD_FIELDS']=array();
			foreach($form->fields as $field)
			{
				if ((strpos( $html , '{$form.'.$field->name.'}' )===FALSE) &&
					(strpos( $html , '{$'.$field->name.'}' )===FALSE) )
				{
					//Aparantly is new (but could be a deleted or non visible like private type fields)
					switch (strtolower($field->type))
					{
						case 'private':
						case 'phpvariable':
						break;
						default:
							if (array_search( $field->name , $tmp['OLD_FIELDS'] )===false)
							{
								//TOP
								$html.='<br/>{$'.$field->name.'}'.'{$form.'.$field->name.'}';
								//BOTTOM
								//$html='{$'.$field->name.'}'.'{$form.'.$field->name.'}'.$html;
								$tmp['OLD_FIELDS'][]=$field->name;
							}
					}
				}
			}
			self::_setTmpData($tmp);
			$html=str_replace('{$form_className}','formDefault', $html );
			return $html;
		}
		catch (Exception $e)
		{
			return (array) $e;
		}
	}
	function set_htmlcode($A,$htmlcode)
	{
		try
		{
			$file = G::decrypt( $A , URL_KEY );
			$form = new Form( $file , PATH_DYNAFORM, SYS_LANG, true );
			$filename = substr($form->fileName , 0, -3) .
			  ( $form->type==='xmlform' ? '' : '.' . $form->type  ) . 'html';
			$fp=fopen($filename, 'w');
			fwrite($fp, $htmlcode );
			fclose($fp);
			return 0;
		}
		catch(Exception $e)
		{
			return (array)$e;
		}
	}
	function get_xmlcode($A)
	{
		try
		{
			$file = G::decrypt( $A , URL_KEY );
			$xmlcode=implode('',file(PATH_DYNAFORM  . $file . '.xml'));
			return array("xmlcode"=>$xmlcode,"error"=>0);
		}
		catch(Exception $e)
		{
			return array("xmlcode"=>"","error"=>(array)$e);
		}
	}
	function set_xmlcode($A,$xmlcode)
	{
		$file = G::decrypt( $A , URL_KEY );
		$fp=fopen(PATH_DYNAFORM  . $file . '.xml', 'w');
		fwrite($fp, $xmlcode );
		fclose($fp);
		return "";
	}
	function get_javascripts($A,$fieldName)
	{
		try
		{
			$file = G::decrypt( $A , URL_KEY );
			$form = new Form( $file , PATH_DYNAFORM, SYS_LANG, true );
			$aOptions=array();$sCode='';
			foreach($form->fields as $name => $value )
			{
				if (strcasecmp($value->type,"javascript")==0)
				{
					$aOptions[]=array('key'=>$name,'value'=>$name);
					$sCode=$value->code;
				}
			}
			return array('aOptions'=>$aOptions, 'sCode'=>$sCode );
		}
		catch(Exception $e)
		{
			return (array) $e;
		}
	}
	function set_javascript($A,$fieldName,$sCode)
	{
		try
		{
			$file = G::decrypt( $A , URL_KEY );
			$dbc2 = new DBConnection( PATH_DYNAFORM . $file . '.xml' ,'','','','myxml' );
			$ses2 = new DBSession($dbc2);
			$ses2->execute(G::replaceDataField("UPDATE dynaForm SET XMLNODE_VALUE = @@CODE WHERE XMLNODE_NAME = @@FIELDNAME ", array('FIELDNAME'=>$fieldName,'CODE'=>$sCode), "myxml" ));
			return 0;
		}
		catch(Exception $e)
		{
			return (array) $e;
		}
	}
	function get_properties( $A, $DYN_UID )
	{
		$file = G::decrypt( $A , URL_KEY );
		$tmp=self::_getTmpData();
		if (!(isset($tmp['Properties']) && isset($tmp['useTmpCopy'])))
		{
	  		$dynaform = new dynaform;
			$dynaform->load( $DYN_UID );
			$form = new Form( $file , PATH_DYNAFORM, SYS_LANG, true );
			$Properties=array(
				'A'=>$A,
				'DYN_UID'=> $dynaform->getDynUid(),
				'PRO_UID'=> $dynaform->getProUid(),
				'DYN_TITLE'=> $dynaform->getDynTitle(),
				'DYN_TYPE'=> $dynaform->getDynType(),
				'DYN_DESCRIPTION'=> $dynaform->getDynDescription(),
				'WIDTH'=> $form->width,
				'ENABLETEMPLATE'=> $form->enableTemplate,
				'MODE'=> $form->mode
			);
			$tmp['Properties']=$Properties;
			self::_setTmpData($tmp);
		}
		else
		{
			$Properties=$tmp['Properties'];
			if (!isset($Properties['ENABLETEMPLATE'])) $Properties['ENABLETEMPLATE'] ="0";
		}
		return $Properties;
	}
	function set_properties($A, $DYN_UID, $getFields)
	{
		try
		{
			$post=array();
			parse_str( $getFields, $post );
			$Fields = $post['form'];
			if (!isset($Fields['ENABLETEMPLATE'])) $Fields['ENABLETEMPLATE'] ="0";
			$file = G::decrypt( $A , URL_KEY );
			$tmp=self::_getTmpData();
			if (!isset($tmp['useTmpCopy']))
			{
		  		$dynaform = new dynaform;
				$dynaform->update( $Fields );
			}
			else
			{
				$tmp['Properties']=$Fields;
				self::_setTmpData($tmp);
			}

			$dbc2 = new DBConnection( PATH_DYNAFORM . $file . '.xml' ,'','','','myxml' );
			$ses2 = new DBSession($dbc2);
					if (!isset($Fields['ENABLETEMPLATE'])) $Fields['ENABLETEMPLATE'] ="0";
			if (isset($Fields['WIDTH'])) {
			  $ses2->execute(G::replaceDataField("UPDATE . SET WIDTH = @@WIDTH WHERE XMLNODE_NAME = 'dynaForm' ", $Fields));
		  }
		  if (isset($Fields['ENABLETEMPLATE'])) {
			  $ses2->execute(G::replaceDataField("UPDATE . SET ENABLETEMPLATE = @@ENABLETEMPLATE WHERE XMLNODE_NAME = 'dynaForm' ", $Fields));
			}
			if (isset($Fields['DYN_TYPE'])) {
			  $ses2->execute(G::replaceDataField("UPDATE . SET TYPE = @@DYN_TYPE WHERE XMLNODE_NAME = 'dynaForm' ", $Fields));
			}
			if (isset($Fields['MODE'])) {
			  $ses2->execute(G::replaceDataField("UPDATE . SET MODE = @@MODE WHERE XMLNODE_NAME = 'dynaForm' ", $Fields));
			}
			return 0;
  		}
		catch(Exception $e)
		{
			return (array) $e;
		}
	}
	function save($A,$DYN_UID)
	{
		try
		{
			$file = G::decrypt( $A , URL_KEY );
			$tmp=self::_getTmpData();
			if (isset($tmp['useTmpCopy']))
			{			/*Save Register*/
		  		$dynaform = new dynaform;
				$dynaform->update( $tmp['Properties'] );

				/*Save file*/
				$copy = implode('',file(PATH_DYNAFORM  . $file . '.xml'));
				$copyHtml = false;
				if (file_exists(PATH_DYNAFORM  . $file . '.html'))
				{
					$copyHtml = implode('',file(PATH_DYNAFORM  . $file . '.html'));
				}
				$file = dynaformEditor::_getFilename($file);
				$fcopy=fopen(PATH_DYNAFORM  . $file . '.xml',"w");
				fwrite($fcopy, $copy);
				fclose($fcopy);
				if ($copyHtml)
				{
					$fcopy=fopen(PATH_DYNAFORM  . $file . '.html',"w");
					fwrite($fcopy, $copyHtml);
					fclose($fcopy);
				}
			}
			else
			{
				//throw new Exception("No deberia entrar aqui a menos que se haya deshabilitado la copia temporal.");
			}
			return 0;
		}
		catch(Exception $e)
		{
			return (array) $e;
		}
	}
	function close($A)
	{
		$file = G::decrypt( $A , URL_KEY );
		/* Delete the temporal copy */
		if (isset($tmp['useTmpCopy']))
		{
			if ($file!==dynaformEditor::_getFilename($file))
			{
				unlink(PATH_DYNAFORM  . $file . '.xml');
				unlink(PATH_DYNAFORM  . $file . '.html');
			}
		}
	}
	function is_modified($A,$DYN_UID)
	{
		$file = G::decrypt( $A , URL_KEY );
		try
		{
		/* Compare Properties */
  		$dynaform = new dynaform;
		$dynaform->load( $DYN_UID );
		$form = new Form( $file , PATH_DYNAFORM, SYS_LANG, true );
		$sp=array(
			'A'=>$A,
			'DYN_UID'=> $dynaform->getDynUid(),
			'PRO_UID'=> $dynaform->getProUid(),
			'DYN_TITLE'=> $dynaform->getDynTitle(),
			'DYN_TYPE'=> $dynaform->getDynType(),
			'DYN_DESCRIPTION'=> $dynaform->getDynDescription(),
			'WIDTH'=> $form->width,
			'ENABLETEMPLATE'=> $form->enableTemplate,
			'MODE'=> $form->mode
		);
		$P=self::get_properties($A,$DYN_UID);
		if (!isset($P['DYN_TITLE'])) {
			$P['DYN_TITLE'] = $sp['DYN_TITLE'];
		}
		if (!isset($P['DYN_TYPE'])) {
			$P['DYN_TYPE'] = $sp['DYN_TYPE'];
		}
		if (!isset($P['DYN_DESCRIPTION'])) {
			$P['DYN_DESCRIPTION'] = $sp['DYN_DESCRIPTION'];
		}
		if (!isset($P['WIDTH'])) {
			$P['WIDTH'] = $sp['WIDTH'];
		}
		if (!isset($P['ENABLETEMPLATE'])) {
			$P['ENABLETEMPLATE'] = $sp['ENABLETEMPLATE'];
		}
		if (!isset($P['MODE'])) {
			$P['MODE'] = $sp['MODE'];
		}
		$modPro = ($sp['DYN_TITLE']!=$P['DYN_TITLE']) ||
			($sp['DYN_TYPE']!=$P['DYN_TYPE']) ||
			($sp['DYN_DESCRIPTION']!=$P['DYN_DESCRIPTION']) ||
			($sp['WIDTH']!=$P['WIDTH']) ||
			($sp['ENABLETEMPLATE']!=$P['ENABLETEMPLATE']) ||
			($sp['MODE']!=$P['MODE']);
		/* Compare copies */
		$fileOrigen=dynaformEditor::_getFilename($file);
		$copy = implode('',file(PATH_DYNAFORM  . $file . '.xml'));
		$origen = implode('',file(PATH_DYNAFORM  . $fileOrigen . '.xml'));
		$copyHTML = file_exists(PATH_DYNAFORM  . $file . '.html')?implode('',file(PATH_DYNAFORM  . $file . '.html')):false;
		$origenHTML = file_exists(PATH_DYNAFORM  . $fileOrigen . '.html')? implode('',file(PATH_DYNAFORM  . $fileOrigen . '.html')):false;
		$modFile = ($copy!==$origen) || ($origenHTML && ($copyHTML!==$origenHTML));
		//Return
		//return array("*message"=>sprintf("%s, (%s= %s %s):", $modPro?"1":"0" , $modFile?"1":"0", ($copy!==$origen)?"1":"0" , ($origenHTML && ($copyHTML!==$origenHTML))?"1":"0" ));
		return $modPro || $modFile;
		}
		catch(Exception $e)
		{
			return (array) $e;
		}
	}
}

?>