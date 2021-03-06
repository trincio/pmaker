if (typeof(dynaformEditor)==="undefined")
{
var dynaformEditor={
	A:"",
	dynUid:"",
	ajax:"",
	currentView:"preview",
	views:{},
	toolbar:{},
	htmlEditorLoaded:false,
	loadPressLoaded:true,
	codePressLoaded:false,
	currentJS:false,
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
		/*this.saveProperties();*/
		try {
			this.saveCurrentView();
		} catch (e) {
			alert(e);
		}
		res=this.ajax.save(this.A,this.dynUid);
		if (res==0) {
			alert(G_STRINGS.ID_SAVED);
		}
		else
		{
			G.alert(res["*message"]);
		}
	},
	save_as:function(){
		/*this.saveProperties();*/
		try {
			this.saveCurrentView();
		} catch (e) {
			alert(e);
		}
	  url='dynaforms_Saveas';
		popupWindow('Save as', url+'?DYN_UID='+this.dynUid+'&AA='+this.A , 500, 350);
	},
        /*
         * close function
         * @author who knows
         * @modifier Gustavo Cruz
         * @purpose this function handles the close of a dynaform editor window
         *          now whenever a dynaform window is close, if the form wasn't
         *          saved the function also delete the temporal *_tmp0.xml files
         *          discarding all the changes that were made, bug 3861.
         */
	close:function()
	{
		var modified=this.ajax.is_modified(this.A,this.dynUid);
		if (typeof(modified)==="boolean")
		{
                    if (!modified || confirm(G_STRINGS.ID_EXIT_WITHOUT_SAVING))
			{
                                res=this.ajax.close(this.A);
                                    if (res==0) {
                                        //alert(G_STRINGS.ID_DYNAFORM_NOT_SAVED);
                                    }
                                    else
                                    {
                                        //alert(res["response"]);
                                        alert(res["*message"]);
                                    }
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
			case "properties":
				this.saveProperties();
				break;
		}
	},
	saveXmlCode:function()
	{
//		var xmlCode = getField("XML").value;
		var xmlCode = this.getXMLCode();
		var todoRefreshXmlCode = xmlCode === null;
		if (todoRefreshXmlCode) return;
		var res = this.ajax.set_xmlcode(this.A,xmlCode.replace(/\+/g, '%2B'));
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
			var res=this.ajax.set_javascript(this.A,field.value,code.replace(/\+/g, '%2B'));
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
		//to adecuate the view perspective @Neyek
		content_div = getElementByPMClass('panel_content___processmaker')
		content_div.style.overflow='auto';
		
		if (this.currentView!="preview")this.refresh_preview();
		this.currentView="preview";
	},
	changeToXmlCode:function()
	{
		//to adecuate the view perspective @Neyek
		content_div = getElementByPMClass('panel_content___processmaker')
		content_div.style.overflow='auto';
		
		this.refresh_xmlcode();
		this.currentView="xmlcode";
		if (this.loadPressLoaded && !XMLCodePress)
		{
			startXMLCodePress();
		}
	},
	changeToHtmlCode:function()
	{
		//to adecuate the view perspective @Neyek
		content_div = getElementByPMClass('panel_content___processmaker')
		content_div.style.overflow='auto';
		
		this.refresh_htmlcode();
		this.currentView="htmlcode";
	},
	changeToFieldsList:function()
	{
		//to adecuate the view perspective @Neyek
		content_div = getElementByPMClass('panel_content___processmaker')
		content_div.style.overflow='visible';
		
		this.refreshFieldsList();
		this.currentView="fieldslist";
	},
	changeToJavascripts:function()
	{
		//to adecuate the view perspective @Neyek
		content_div = getElementByPMClass('panel_content___processmaker')
		content_div.style.overflow='auto';
		
		this.currentView="javascripts";
		this.refreshJavascripts();
		if (this.loadPressLoaded && !JSCodePress)
		{
			startJSCodePress();
		}
	},
	changeToProperties:function()
	{
		//to adecuate the view perspective @Neyek
		content_div = getElementByPMClass('panel_content___processmaker')
		content_div.style.overflow='auto';
		
		this.currentView="properties";
		this.refreshProperties();
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
			return;
		}
		getField("PME_HTML_ENABLETEMPLATE","dynaforms_HtmlEditor").checked=this.getEnableTemplate();
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
		//fields_List.refresh(); return;
		var oRPC = new leimnud.module.rpc.xmlhttp({
		  	url : 'fieldsHandler',
		  	args: ''
	  	});
		document.getElementById('dynaformEditor[6]').innerHTML = '<center>Loading</center>';
	  	oRPC.callback = function(rpc) {
		  	
		  	var scs=rpc.xmlhttp.responseText.extractScript();
		  	document.getElementById('dynaformEditor[6]').innerHTML = rpc.xmlhttp.responseText;
		  	scs.evalScript();
		  	
	  	}.extend(this);
		oRPC.make();
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
			JSCodePress.setCode(newCode);
			//JSCodePress.edit(newCode,"javascript");
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
			XMLCodePress.setCode(newCode);
			//XMLCodePress.edit(newCode,"xmlform");
		}
		else
		{
			var code=getField("XML","dynaforms_XmlEditor");
			code.value=newCode;
		}
	},
	setEnableTemplate:function(value)
	{
		value = value ? "1" : "0";
		this.ajax.set_enabletemplate( this.A , value );
	},
	getEnableTemplate:function()
	{
		var value = this.ajax.get_enabletemplate( this.A );
		return value == "1";
	},
	refreshJavascripts:function()
	{
		var field=getField("JS_LIST","dynaforms_JSEditor");
		for(j=0; j<field.options.length; j++) {
			if( field.options[j].value == '___pm_boot_strap___' ){
				field.remove(j);
			}
		}
		
		this.currentJS=field.value;
		var res=this.ajax.get_javascripts(this.A,field.value);
		if(field.value == ''){
			if( typeof(res.aOptions[0]) !== "undefined" && res.aOptions[0].value != '___pm_boot_strap___'){
				res = this.ajax.get_javascripts(this.A, res.aOptions[0].value);
				this.currentJS = res.aOptions[0].value;
			}
		}
		
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
			field.value = this.currentJS;
			this.setJSCode(res.sCode);
		}
		else
		{
			G.alert(response.error["*message"],"Error");
		}
		
		var field=getField("JS_LIST","dynaforms_JSEditor");
		for(j=0; j<field.options.length; j++) {
			if( field.options[j].value == '___pm_boot_strap___' ){
				field.options[j].text = '';
			}
		}
	
	},
	changeJavascriptCode:function()
	{
		var field=getField("JS_LIST","dynaforms_JSEditor");
		var value=field.value;
		if (this.currentJS)
		{
			field.value=this.currentJS;
			this.saveJavascript();
			field.value=value;
		}
		this.refreshJavascripts();
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
		/*getField("ENABLETEMPLATE","dynaforms_Properties").checked=(prop.ENABLETEMPLATE=="1");*/
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
					if (window.execScript) {
						window.execScript( myScripts[rr], "javascript" );}
						else
							window.setTimeout( "try{\n"+myScripts[rr]+"\n}catch(e){\ndynaformEditor.displayError(e,"+rr+")}", 0 );
			} catch (e) {
				dynaformEditor.displayError(e,rr);
			}
		}
		delete myScripts;
	},
	restoreHTML:function()
	{
	  window._editorHTML.doc.body.innerHTML = this.ajax.restore_html(this.A);
		html_html2();
		html2_html();
	},
	displayError:function(err,rr)
	{
		G.alert(err.message.split("\n").join("<br />"),"Javascript Error");
	}
};
}
else
{
  alert("Donde esta esto!!!");
}


function getElementByPMClass(__class){
	divs = document.getElementsByTagName('div');
	for(i=0; i<divs.length; i++){
		if(divs[i].className == __class){
			return divs[i];
		}
	}
	return false;
}