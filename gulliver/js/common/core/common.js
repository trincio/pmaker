/* PACKAGE : COMMON
 */
  function get_xmlhttp() {
    try {
      xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
      try {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (E) {
        xmlhttp = false;
      }
    }
    if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
        xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
  }
  /* ajax_function
   * Envia una solicitud GET a ajax_server con la variables "function" y las definidas en parameters.
   * @author       David Callizaya <calidavidx21@hotmail.com>
   * @version 1.0
   * @package ajax
   * @param string ajax_server  url de la pagina servidor
   * @param string function     función solicitada en el lado del servidor
   * @param string parameters   variables pasadas por url. Ej. variable=valor&otravariable=suvalor
   */
  function ajax_function(ajax_server, funcion, parameters, method)
  {
      var objetus;
      objetus = get_xmlhttp();
      var response;
      try
      {
      	if (parameters) parameters = '&' + encodeURI(parameters);
      	if (!method ) method ="POST";
      	data = "function=" + funcion + parameters;
      	questionMark = (ajax_server.split('?').length > 1 ) ? '&' : '?';
        var callServer;
        callServer = new leimnud.module.rpc.xmlhttp({
        		url			: ajax_server,
        		async   : false,
        		method	: method,
        		args    : data
        	});
      	callServer.make();
      	response = callServer.xmlhttp.responseText;
      	var scs = callServer.xmlhttp.responseText.extractScript();
      	
	  	scs.evalScript();
      	delete callServer;
    	} catch(ss){
    		alert("Error: "+ss.message+var_dump(ss));
    	}
      return response;//objetus.responseText;
  }
  /* ajax_message
   * Envia una solicitud GET a ajax_server con la variables "function" y las definidas en parameters.
   * @author       David Callizaya <calidavidx21@hotmail.com>
   * @version 1.0
   * @package ajax
   * @param string ajax_server  url de la pagina servidor
   * @param string function     función solicitada en el lado del servidor
   * @param string parameters   variables pasadas por url. Ej. variable=valor&otravariable=suvalor
   */
  function ajax_message(ajax_server, funcion, parameters, method, callback)
  {
      var objetus;
      objetus = get_xmlhttp();
      var response;
      try
      {
      	if (parameters) parameters = '&' + encodeURI(parameters);
      	if (!method ) method ="POST";
      	data = "function=" + funcion + parameters;
      	questionMark = (ajax_server.split('?').length > 1 ) ? '&' : '?';
      	objetus.open( method, ajax_server + ((method==='GET')? questionMark+data : '') , true );
        objetus.onreadystatechange=function() {
          if ( objetus.readyState==4)
          {
            if( objetus.status==200)
            {
                if ( callback ) callback(objetus.responseText);
            }
          }
        }
        if (method==='POST') objetus.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        objetus.send(((method==='GET')? null : data));
    	}catch(ss)
    	{
    		alert("error"+ss.message);
    	}
  }
  /* ajax_post
   * Envia una solicitud GET/POST a ajax_server con los parametros definidos
   * o los campos de un formulario
   * @author       David Callizaya <calidavidx21@hotmail.com>
   * @version 1.0
   * @package ajax
   * @param string ajax_server  url de la pagina servidor
   * @param string function     función solicitada en el lado del servidor
   * @param string parameters   variables pasadas por url o formulario.
   * @example: ajax_post('foo.com', document.form[0], "POST", callback )
   */
  function ajax_post(ajax_server, parameters, method, callback, asynchronous )
  {
      var objetus;
      objetus = get_xmlhttp();
      var response;
      try
      {
        if (typeof(parameters)==='object') parameters = ajax_getForm(parameters);
      	if (!method ) method ="POST";
      	if (typeof(asynchronous)==='undefined') asynchronous = false;
      	data = parameters;
      	questionMark = (ajax_server.split('?').length > 1 ) ? '&' : '?';
      	if (method==='GET/POST') {
      	  objetus.open( 'POST', ajax_server + ((data.length<1024)?(questionMark+data):''), asynchronous );
      	} else {
      	  objetus.open( method, ajax_server + ((method==='GET')? questionMark+data : '') , asynchronous );
      	}
        objetus.onreadystatechange=function() {
          if ( objetus.readyState==4)
          {
            if( objetus.status==200)
            {
                if ( callback ) callback(objetus.responseText);
            }
          }
        }
        if ((method==='POST')||(method==='GET/POST')) objetus.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=utf-8");
        objetus.send(((method==='GET')? null : data));
      	if (!asynchronous)
      	{
          if ( callback ) callback(objetus.responseText);
      	  return objetus.responseText;
        }
    	}catch(ss)
    	{
    		alert("Error: "+ var_dump(ss));
    	}
  }

  /*
   * Refactoring.......................
   * Fixed by checkboxes binary values
   * By Neyek <erik@colosa.com>
   * Date March 27th, 2009 12:20:00 GMT-4
   */

  function ajax_getForm( thisform ) {
	var formdata='';

	// Loop through form fields
	for (var i=0; i < thisform.length; i++) {
	  if ( formdata!=='' ) formdata += '&';
	  //Build Send String
	  if(thisform.elements[i].type == "text") { //Handle Textbox's
		  formdata += thisform.elements[i].name + "=" + encodeURIComponent(thisform.elements[i].value);
	  } else if(thisform.elements[i].type == "textarea") { //Handle textareas
		  formdata += thisform.elements[i].name + "=" + encodeURIComponent(thisform.elements[i].value);
	  } else if(thisform.elements[i].type == "checkbox") { //Handle checkbox's
		  formdata += thisform.elements[i].name + '=' + ((thisform.elements[i].checked)? '1': '0');
	  } else if(thisform.elements[i].type == "radio") { //Handle Radio buttons
		  if(thisform.elements[i].checked==true){
			  formdata += thisform.elements[i].name + "=" + thisform.elements[i].value;
		  }
	  } else if(thisform.elements[i].type == "select-multiple") { //Handle list box
		  for(var j=0; j<thisform.elements[i].options.length ;j++) {
			  if ( j!==0 ) formdata += '&';
			  formdata += ( (thisform.elements[i].options[j].selected)? thisform.elements[i].name + "=" + encodeURIComponent(thisform.elements[i].options[j].value): '');
		  }
	  } else {
		  //finally, this should theoretically this is a select box.
		  formdata += thisform.elements[i].name + "=" + encodeURIComponent(thisform.elements[i].value);
	  }
	}
	return formdata;
  }

/* COMMON FUNCTIONS
 */


function isNumber (sValue)
{
	var sValue = new String(sValue);
  var bDot   = false;
  var i, sCharacter;
  if ((sValue == null) || (sValue.length == 0))
  {
    if (isNumber.arguments.length == 1)
    {
    	return false;
    }
    else
    {
    	return (isNumber.arguments[1] == true);
    }
  }
  for (i = 0; i < sValue.length; i++)
  {
    sCharacter = sValue.charAt(i);
    if (i != 0)
    {
      if (sCharacter == '.')
      {
        if (!bDot)
        {
          bDot = true;
        }
        else
        {
          return false;
        }
      }
      else
      {
        if (!((sCharacter >= '0') && (sCharacter <= '9')))
        {
        	return false;
        }
      }
    }
    else
    {
      if (sCharacter == '.')
      {
        if (!bDot)
        {
          bDot = true;
        }
        else
        {
          return false;
        }
      }
      else
      {
        if (!((sCharacter >= '0') && (sCharacter <= '9') && (sCharacter != '-') || (sCharacter == '+')))
        {
        	return false;
        }
      }
    }
  }
  return true;
}

function roundNumber(iNumber, iDecimals)
  {
	var iNumber   = parseFloat(iNumber || 0);
	var iDecimals = parseFloat(iDecimals || 2);
	return Math.round(iNumber * Math.pow(10, iDecimals)) / Math.pow(10, iDecimals);
}

function toMaskNumber(iNumber,dec)
{
	iNumber = fix(iNumber.toString(),dec || 2);
	var t=iNumber.split(".");
	var arrayResult=iNumber.replace(/\D/g,'').replace(/^0*/,'').split("").reverse();
	var final="";
	var aux=0;
	var sep=0;
	for(var i=0;i<arrayResult.length;i++)
	{
		if(i==1)
		{
			final="."+arrayResult[i]+final;
		}
		else
		{
			if(i>1 && aux>=3 && ((aux%3)==0))
			{
				final=arrayResult[i]+","+final;
				aux+=1;
				sep+=1;
			}
			else
			{
				final=arrayResult[i]+final;
				if(i>1)
				{
					aux+=1;
				}
			}
		}
	}
	return final;
}

function fix(val, dec)
{
	var a = val.split(".");
	var r="";
	if(a.length==1)
	{
		r=a[0]+"."+creaZero(dec);
	}
	else
	{
		if(a[1].length<=dec)
		{
			r=a[0]+"."+a[1]+creaZero(dec-a[1].length);
		}
		else
		{
			r=a[0]+"."+a[1].substr(0,dec);
		}
	}
	return r;
}

function creaZero(cant)
{
	var a="";
	for(var i=0;i<cant;i++)
	{
		a+="0";
	}
	return a;
}

function toUnmaskNumber(iNumber)
{
	var aux = "";
	var num = new String (iNumber);
	var len = num.length;
	var i = 0;
	for (i = 0; i < len; i++ ) {
		if (num.charAt ( i) != ',' && num.charAt (i) != '$' && num.charAt (i) != ' ' && num.charAt (i) != '%' ) aux = aux + num.charAt ( i);
	}
	return parseFloat(aux,2);
}

function compareDates(datea, dateB,porDias)
{
	var a = datea.split('/');

	var b = dateB.split('/');
	x = new Date(a[2], a[1], (porDias)?1:a[0]);
	y = new Date(b[2], b[1], (porDias)?1:b[0]);
	return ((x - y) <= 0) ? false : true;
}

/****THE ANSWER*****/
/*diferencia entre 2 fechas*/
function diff_date(fecha1, fecha2)
{ var f1 = fecha1.split('-');
	fecha1 = new Date();
	fecha1.setDate(f1[2]);
	fecha1.setMonth(f1[1]);
	fecha1.setYear(f1[0]);

  var f2 = fecha2.split('-');
	fecha2 = new Date();
	fecha2.setDate(f2[2]);
	fecha2.setMonth(f2[1]);
	fecha2.setYear(f2[0]);

	var dias = Math.floor((fecha1.getTime()-fecha2.getTime())/(3600000*24));
	return dias;
}

/*
 * author <calidavidx21@hotmail.com>
 */
function getField( fieldName , formId )
{
  if (formId)
  {
    var form = document.getElementById(formId);
    if (!form) {form=document.getElementsByName(formId);
      if (form) {
      	if (form.length > 0) {
      	  form = form[0];
        }
      }
    }
    if (form.length > 0) {
      return form.elements[ 'form[' + fieldName + ']' ];
    }
    else {
    	//return null;
    	return document.getElementById( 'form[' + fieldName + ']' );
    }
  }
  else
  {
    return document.getElementById( 'form[' + fieldName + ']' );
  }
}

/*
 * author <calidavidx21@hotmail.com>
 */
function getElementByName( fieldName )
{
  var elements = document.getElementsByName( fieldName );
  try{
    var x=0;
    if (elements.length === 1)
      return elements[0];
    else if (elements.length === 0)
      return elements[0];
    else
      return elements;
  } catch (E)
  {}
}


var myDialog;
function commonDialog ( type, title , text, buttons, values, callbackFn )  {
	myDialog = new leimnud.module.panel();
	myDialog.options = {
	  size:{w:400,h:200},
	  position:{center:true},
		title: title,
		control: { close	:false, roll	:false, drag	:true, resize	:false },
    fx: {
      //shadow	:true,
      blinkToFront:false,
      opacity	:true,
      drag:false,
      modal: true
    },
	  theme:"processmaker"
	};

	myDialog.make();
    switch (type) {
    case 'question':
       icon = 'question.gif';
       break
    case 'warning':
       icon = 'warning.gif';
       break
    case 'error':
       icon = 'error.gif';
       break
    default:
       icon = 'information.gif';
       break
    }

    var contentStr = '';
    contentStr += "<div><table border='0' width='100%' > <tr height='70'><td width='60' align='center' >";
    contentStr += "<img src='/js/maborak/core/images/" + icon + "'></td><td >" + text + "</td></tr>";
    contentStr += "<tr height='35' valign='bottom'><td colspan='2' align='center'> ";
    if ( buttons.custom && buttons.customText )
      contentStr += "<input type='button' value='" + buttons.customText + "' onclick='myDialog.dialogCallback(4); ';> &nbsp; ";
    if ( buttons.cancel )
      contentStr += "<input type='button' value='Cancel' onclick='myDialog.dialogCallback(0);'> &nbsp; ";
    if ( buttons.yes )
      contentStr += "<input type='button' value=' Yes ' onclick='myDialog.dialogCallback(1);'> &nbsp; ";
    if ( buttons.no )
      contentStr += "<input type='button' value=' No ' onclick='myDialog.dialogCallback(2);'> &nbsp; ";
    contentStr += "</td></tr>";
    contentStr += "</table>";

    myDialog.addContent( contentStr );
    myDialog.values = values;
	  myDialog.dialogCallback = function ( dialogResult ) {
		  myDialog.remove( );
      if ( callbackFn )
        callbackFn ( dialogResult );
    }

}
function var_dump(obj)
{
	var o,dump;
	dump='';
	if (typeof(obj)=='object') {
  	for(o in obj) if (typeof(obj[o])!=='function')
  	{
  		dump+=o+'('+typeof(obj[o])+'):'+obj[o]+"\n";
  	}
  }
	else
	dump=obj;
	return dump;
}

/*
 * @author David Callizaya
 */
var currentPopupWindow;
function popupWindow ( title , url, width, height, callbackFn , autoSizeWidth, autoSizeHeight,modal,showModalColor)  {
	modal = (modal===false)?false:true;
	showModalColor = (showModalColor===false)?false:true;
	var myPanel = new leimnud.module.panel();
	currentPopupWindow = myPanel;
	myPanel.options = {
		size:{w:width,h:height},
		position:{center:true},
		title: title,
		theme: "processmaker",
		control: { close :true, roll	:false, drag	:true, resize	:false},
		fx: {
			//shadow	:true,
			blinkToFront:true,
			opacity	:true,
			drag:true,
			modal: modal
		      //opacityModal:{static:'1'}
		}
	};
	if(showModalColor===true)
	{
		//Panel.setStyle={modal:{backgroundColor:"#ECF3F6"}};
	}
	else
	{
		myPanel.styles.fx.opacityModal.Static='0';
	}
	myPanel.make();
	myPanel.loader.show();
	var r = new leimnud.module.rpc.xmlhttp({url:url});
	r.callback=leimnud.closure({Function:function(rpc,myPanel){
  		myPanel.addContent(rpc.xmlhttp.responseText);
  		var myScripts = myPanel.elements.content.getElementsByTagName('SCRIPT');
  		for(var rr=0; rr<myScripts.length ; rr++){
  		  try {
  		    if (myScripts[rr].innerHTML!=='')
  		      if (window.execScript)
  	          window.execScript( myScripts[rr].innerHTML, 'javascript' );
  	        else
  	          window.setTimeout( myScripts[rr].innerHTML, 0 );
  		  } catch (e) {
  		    alert(e.description);
  		  }
  		}
  		/* Autosize of panels, to fill only the first child of the
  		 * rendered page (take note)
  		 */
  		var panelNonContentHeight = 62;
  		var panelNonContentWidth  = 28;
  		myPanel.elements.content.style.padding="0px;";
  		try {
  		  if (autoSizeWidth)
    		  myPanel.resize({w:myPanel.elements.content.childNodes[0].clientWidth+panelNonContentWidth});
  		  if (autoSizeHeight)
    		  myPanel.resize({h:myPanel.elements.content.childNodes[0].clientHeight+panelNonContentHeight});
  	  } catch (e) {
  	    alert(':(');
  	  }
  		delete newdiv;
  		delete myScripts;
			myPanel.command(myPanel.loader.hide);
	},args:[r, myPanel]});
	r.make();

/*
  myPanel.dialogCallback = function (  ) {
  }
*/
  delete myPanel;
}

// Get an object left position from the upper left viewport corner
// Tested with relative and nested objects
function getAbsoluteLeft(o) {
	oLeft = o.offsetLeft            // Get left position from the parent object
	while(o.offsetParent!=null) {   // Parse the parent hierarchy up to the document element
		oParent = o.offsetParent    // Get parent object reference
		oLeft += oParent.offsetLeft // Add parent left position
		o = oParent
	}
	// Return left postion
	return oLeft
}
// Get an object top position from the upper left viewport corner
// Tested with relative and nested objects
function getAbsoluteTop(o) {
	oTop = o.offsetTop            // Get top position from the parent object
	while(o.offsetParent!=null) { // Parse the parent hierarchy up to the document element
		oParent = o.offsetParent  // Get parent object reference
		oTop += oParent.offsetTop // Add parent top position
		o = oParent
	}
	// Return top position
	return oTop
}
/*
 */
function showHideElement(id)
{
  var element;
  if (typeof(id)=='object') element=id;
  else element=document.getElementById(id);
  if (element.style.display==='none') {
    switch(element.type) {
      case 'table':
        element.style.display = 'table';
        break;
      default:
        element.style.display = '';
    }
  } else {
    element.style.display = 'none';
  }
}
/*
 */
function showHideSearch(id,aElement,openText,closeText)
{
  var element=document.getElementById(id);
  if (element.style.display==='none') {
    if (!closeText) closeText=G_STRINGS.ID_CLOSE_SEARCH;
    if (aElement) {
      aElement.innerHTML=closeText;
      var bullet = document.getElementById(aElement.id+'[bullet]');
      bullet.src='/images/bulletButtonDown.gif';
    }
    switch(element.type) {
      case 'table':
        document.getElementById(id).style.display = 'table';
        break;
      default:
        document.getElementById(id).style.display = '';
    }
  } else {
    if (!openText) openText=G_STRINGS.ID_OPEN_SEARCH;
    if (aElement) {
      aElement.innerHTML=openText;
      var bullet = document.getElementById(aElement.id+'[bullet]');
      bullet.src='/images/bulletButton.gif';
    }
    document.getElementById(id).style.display = 'none';
  }
}
/* Loads a page but in a non visible div with absolute on (x,y)
 * and execute the javascript node that it contains.
 */
function loadPage ( url, x, y , visibility , div )  {
    visibility = typeof(visibility)==='udefined'?'hidden':visibility;
  	var r = new leimnud.module.rpc.xmlhttp({url:url});
  	r.callback=leimnud.closure({Function:function(rpc,div){
  	    if (typeof(div)==='undefined') div=createDiv('');
        if (typeof(x)!=='undefined') div.style.left=x;
        if (typeof(y)!=='undefined') div.style.top =y;
        div.innerHTML=rpc.xmlhttp.responseText;
    		var myScripts = div.getElementsByTagName('SCRIPT');
    		for(var rr=0; rr<myScripts.length ; rr++){
    		  try {
    		    if (myScripts[rr].innerHTML!=='')
    		      if (window.execScript)
    		          window.execScript( myScripts[rr].innerHTML, 'javascript' );
    		        else
    		          window.setTimeout( myScripts[rr].innerHTML, 0 );
    		  } catch (e) {
    		    alert(e.description);
    		  }
    		}
    		delete div;
    		delete myScripts;
  	},args:[r,div]});
  	r.make();
}
function createDiv(id) {

   var newdiv = document.createElement('div');
   newdiv.setAttribute('id', id);

   newdiv.style.position = "absolute";
   newdiv.style.left = 0;
   newdiv.style.top = 0;

   newdiv.style.visibility="hidden";

   document.body.appendChild(newdiv);

   return newdiv;
}

/* THIS FUNCTIONS WHERE COPIED FROM JSFORMS */

/*if (window.attachEvent)
  window.attachEvent('onload', _OnLoad_);
else
  window.addEventListener('load', _OnLoad_, true);*/

//function _OnLoad_() {


/*onload=function(){

	if (self.setNewDates)
    self.setNewDates();

  if (self.setReloadFields)
    self.setReloadFields();

  if (self.enableHtmlEdit)
    self.enableHtmlEdit();

  if (self.dynaformOnloadUsers)
    self.dynaformOnloadUsers();

  if (self.dynaformOnload)
    self.dynaformOnload();


}*/



function refillText( fldName, ajax_server, values ) {
	var objetus;
    objetus = get_xmlhttp();
    objetus.open ("GET", ajax_server + "?" + values, false);
    objetus.onreadystatechange=function() {
        if ( objetus.readyState == 1 )
        {
          var textfield = document.getElementById( 'form[' + fldName + ']' );
          if ( ! isdefined( textfield ))
            var textfield = document.getElementById( fldName );
          textfield.value = '';

        }
        else if ( objetus.readyState==4)
        {
            if( objetus.status==200)
            {
//              alert ( objetus.responseText );
              var xmlDoc = objetus.responseXML;
              if ( xmlDoc ) {
                 var textfield = document.getElementById( 'form[' + fldName + ']' );
                 if ( ! isdefined( textfield ))
                   var textfield = document.getElementById( fldName );
                 var dataArray = xmlDoc.getElementsByTagName('value');
                 if (dataArray[0].firstChild)
                 	 if((dataArray[0].firstChild.xml)!='_vacio'){
                 		 textfield.value = dataArray[0].firstChild.xml;
                 		 if(textfield.type != 'hidden')
                 		   if ( textfield.onchange )
                 			   textfield.onchange();
                 	 }
              }
            }
            else
            {
                window.alert('error-['+ objetus.status +']-' + objetus.responseText );
            }
        }
    }
    objetus.send(null);
}

function refillCaption( fldName, ajax_server, values ){
	var objetus;
    objetus = get_xmlhttp();
    objetus.open ("GET", ajax_server + "?" + values, false);
    objetus.onreadystatechange=function() {
        if ( objetus.readyState == 1 )
        {
          var textfield = document.getElementById( 'FLD_' + fldName );
          textfield.innerHTML = '';

        }
        else if ( objetus.readyState==4)
        {
            if( objetus.status==200)
            {
              var xmlDoc = objetus.responseXML;
              if ( xmlDoc ) {
                 var textfield = document.getElementById( 'FLD_' + fldName );
                 var dataArray = xmlDoc.getElementsByTagName('value');
                 if (dataArray[0].firstChild)
                 	  if((dataArray[0].firstChild.xml)!='_vacio')
                 		  //textfield.innerHTML = '<font size="1">' + dataArray[0].firstChild.xml + '</font>';
                 		  textfield.innerHTML = dataArray[0].firstChild.xml;
              }
            }
            else
            {
                window.alert('error-['+ objetus.status +']-' + objetus.responseText );
            }
        }
    }
    objetus.send(null);
}


function refillDropdown( fldName, ajax_server, values , InitValue)
{

	var objetus;
    objetus = get_xmlhttp();
    objetus.open ("GET", ajax_server + "?" + values, false);
    objetus.onreadystatechange=function() {
        if ( objetus.readyState == 1 )
        {
          var dropdown = document.getElementById( 'form[' + fldName + ']' );

          while ( dropdown.hasChildNodes() )
            dropdown.removeChild(dropdown.childNodes[0]);

        }
        else if ( objetus.readyState==4)
        {
            if( objetus.status==200)
            {
              var xmlDoc = objetus.responseXML;

              if ( xmlDoc ) {
                 var dropdown = document.getElementById( 'form[' + fldName + ']' );
                 var dataArray = xmlDoc.getElementsByTagName('item');
                 itemsNumber = dataArray.length;

                 if(InitValue == true) itemsNumber = dataArray.length-1;
                 for (var i=0; i<itemsNumber; i++){
                    dropdown.options[ dropdown.length] = new Option(dataArray[i].firstChild.xml, dataArray[i].attributes[0].value );
                    if(InitValue == true) {
                    	if(dropdown.options[ dropdown.length-1].value == dataArray[dataArray.length-1].firstChild.xml)
                    		dropdown.options[i].selected = true;
                    }
                 }
                 dropdown.onchange();
              }
            }
            else
            {
                window.alert('error-['+ objetus.status +']-' + objetus.responseText );
            }
        }
    }
    objetus.send(null);
}

function iframe_get_xmlhttp() {
  try {
    xmlhttp = new ActiveXObject('Msxml2.XMLHTTP');
  } catch (e) {
    try {
      xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
    } catch (E) {
      xmlhttp = false;
    }
  }
  if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
    xmlhttp = new XMLHttpRequest();
  }
  return xmlhttp;
}

function get_xmlhttp() {
        try {
                xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
                try {
                        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (E) {
                        xmlhttp = false;
                }
        }
        if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
                xmlhttp = new XMLHttpRequest();
        }
        return xmlhttp;
}



function refillTextError( div_container, fldName, ajax_server, values )
{
	var objetus;
    objetus = get_xmlhttp();
    objetus.open ("GET", ajax_server + "?" + values, false);
    objetus.onreadystatechange=function() {
        if ( objetus.readyState == 1 )
        {
          var textfield = document.getElementById( 'form[' + fldName + ']' );
          textfield.value = '';
          document.getElementById(div_container).innerHTML = '';

        }
        else if ( objetus.readyState==4)
        {
            if( objetus.status==200)
            {
              var xmlDoc = objetus.responseXML;
              if ( xmlDoc ) {
                 var textfield = document.getElementById( 'form[' + fldName + ']' );
                 var dataArray = xmlDoc.getElementsByTagName('value');
                 textfield.value = dataArray[0].firstChild.xml;
                 var dataArray = xmlDoc.getElementsByTagName('message');
                 if ( dataArray[0].firstChild )
                   document.getElementById(div_container).innerHTML = '<b>' + dataArray[0].firstChild.xml + '</b>';
              }
            }
            else
            {
                window.alert('error-['+ objetus.status +']-' + objetus.responseText );
            }
        }
    }
    objetus.send(null);
}



function iframe_get_xmlhttp() {
  try {
    xmlhttp = new ActiveXObject('Msxml2.XMLHTTP');
  } catch (e) {
    try {
      xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
    } catch (E) {
      xmlhttp = false;
    }
  }
  if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
    xmlhttp = new XMLHttpRequest();
  }
  return xmlhttp;
}

function iframe_ajax_init(ajax_server, div_container, values, callback) {
	var objetus;
  objetus = iframe_get_xmlhttp();
  objetus.open ('GET', ajax_server + '?' + values, true);
  objetus.onreadystatechange = function() {
    if ( objetus.readyState == 1 ) {
      document.getElementById(div_container).style.display = '';
      document.getElementById(div_container).innerHTML = '...';
    }
    else if (objetus.readyState==4) {
      if (objetus.status==200) {
        document.getElementById(div_container).innerHTML = objetus.responseText;
        if (callback != '')
          callback();
      }
      else {
        window.alert('error-['+ objetus.status +']-' + objetus.responseText );
      }
    }
  }
  objetus.send(null);
}

function iframe_ajax_init_2(ajax_server, div_container, values, callback) {
	var objetus;
  objetus = iframe_get_xmlhttp();
  objetus.open ('GET', ajax_server + '?' + values, true);
  objetus.onreadystatechange = function() {
    if ( objetus.readyState == 1 ) {
      div_container.style.display = '';
      div_container.innerHTML = '...';
    }
    else if (objetus.readyState==4) {
      if (objetus.status==200) {
        div_container.innerHTML = objetus.responseText;
        if (callback != '')
          callback();
      }
      else {
        window.alert('error-['+ objetus.status +']-' + objetus.responseText );
      }
    }
  }
  objetus.send(null);
}

function myEmptyCallback() {
}

function disable (obj) {
  obj.disabled = true;
  return;
}

function enable (obj) {
  obj.disabled = false;
  return;
}

function disableById (id) {
  obj = getField(id);
  obj.disabled = true;
  return;
}

function enableById (id) {
  obj = getField(id);
  obj.disabled = false;
  return;
}

function visible (obj) {
  if (obj.style) {
    obj.style.visibility = 'visible';
  }
  return;
}

function hidden (obj) {
  if (obj.style) {
    obj.style.visibility = 'hidden';
  }
  return;
}

function visibleById (id) {
  obj = getField(id);
  obj.style.visibility = 'visible';
  return;
}

function hiddenById (id) {
  obj = getField(id);
  obj.style.visibility = 'hidden';
  return;
}

function hiddenRowById (id) {
	row = 'DIV_'+ id +'.style.visibility = \'hidden\';';
	hiden = 'DIV_'+ id +'.style.display = \'none\';';
	eval(row);
	eval(hiden);
  return;
}
function visibleRowById (id) {
	row = 'DIV_'+ id +'.style.visibility = \'visible\';';
	block = 'DIV_'+ id +'.style.display = \'block\';';
	eval(row);
	eval(block);
  return;
}

function setFocus (obj) {
  obj.focus();
  return;
}

function setFocusById (id) {
  obj = getField (id);
  setFocus(obj);
  return;
}

function submitForm () {
  document.webform.submit();
  return;
}

function changeValue(id, newValue) {
  obj = getField(id);
  obj.value = newValue;
  return ;
}

function getValue(obj) {
  return obj.value;
}

function getValueById (id) {
  obj = getField(id);
  return obj.value;
}

function removeCurrencySign (snumber) {
   var aux = '';
   var num = new String (snumber);
   var len = num.length;
   var i = 0;
   for (i=0; !(i>=len); i++)
     if (num.charAt(i) != ',' && num.charAt(i) != '$' && num.charAt(i) != ' ') aux = aux + num.charAt(i);
   return aux;
 }

 function removePercentageSign (snumber) {
   var aux = '';
   var num = new String (snumber);
   var len = num.length;
   var i = 0;
   for (i=0; !(i>=len); i++)
     if (num.charAt(i) != ',' && num.charAt(i) != '%' && num.charAt(i) != ' ') aux = aux + num.charAt(i);
   return aux;
 }

 function toReadOnly(obj) {
 	 if (obj) {
     obj.readOnly = 'readOnly';
     obj.style.background = '#CCCCCC';
   }
   return;
 }

 function toReadOnlyById(id) {
   obj = getField(id);
   if (obj) {
     obj.readOnly = 'readOnly';
     obj.style.background = '#CCCCCC';
   }
   return ;
 }

function getGridField(Grid, Row, Field) {
	obj = document.getElementById('form[' + Grid + ']' + '[' + Row + ']' + '[' + Field + ']');
  return obj;
}

function getGridValueById(Grid, Row, Field) {
  obj = getGridField(Grid, Row, Field);
  if (obj)
    return obj.value;
  else
    return '';
}

function Number_Rows_Grid(Grid, Field) {
	Number_Rows = 1;
	if (getGridField(Grid, Number_Rows, Field)) {
		Number_Rows = 0;
	  while (getGridField(Grid, (Number_Rows + 1), Field))
	    Number_Rows++;
	  return Number_Rows;
	}
	else
	  return 0;
}

function attachFunctionEventOnChange(Obj, TheFunction) {
	Obj.oncustomize = TheFunction;
}

function attachFunctionEventOnChangeById(Id, TheFunction) {
	Obj = getField(Id);
	Obj.oncustomize = TheFunction;
}

function attachFunctionEventOnKeypress(Obj, TheFunction) {
	Obj.attachEvent('onkeypress', TheFunction);
}

function attachFunctionEventOnKeypressById(Id, TheFunction) {
	Obj = getField(Id);
	Obj.attachEvent('onkeypress', TheFunction);
}

function unselectOptions ( field ) {
var radios = document.getElementById('form[' + field + ']');
	if (radios) {
	  var inputs = radios.getElementsByTagName ('input');
	  if (inputs) {
		  for(var i = 0; i < inputs.length; ++i) {
		  	inputs[i].checked = false;
			}
	  }
	}
}

function validDate(TheField, Required) {
	TheYear  = getField(TheField + '][YEAR');
	TheMonth = getField(TheField + '][MONTH');
	TheDay   = getField(TheField + '][DAY');
	if (!TheYear || !TheMonth || !TheDay)
	  return false;
	if (Required)
	  if ((TheYear.value == 0) || (TheMonth.value == 0) || (TheDay.value == 0))
	    return false;
	if (TheMonth.value == 2)
	  if (TheDay.value > 29)
	    return false;
	if ((TheMonth.value == 4) || (TheMonth.value == 6) || (TheMonth.value == 9) || (TheMonth.value == 11))
	  if (TheDay.value > 30)
	    return false;
	return true;
}

/* @author David S. Callizaya S.
 */
function globalEval(scriptCode) {
  if (scriptCode!=='')
    if (window.execScript)
      window.execScript( scriptCode, 'javascript' );
    else
      window.setTimeout( scriptCode, 0 );
}
function switchImage(oImg,url1,url2){
  if (oImg && (url2!=='')) {
    oImg.src=(oImg.src.substr(oImg.src.length-url1.length,url1.length)===url1)? url2: url1;
  }
}
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}
function backImage(oImg,p){
  oImg.style.background=p;
}

/* javascript para el toolbar */
var lc=false;
var sh=function(a,i)
{
    var c = (a.nextSibling.nextSibling)?a.nextSibling.nextSibling:a.nextSibling;
    if(lc && lc.c!=i){
        lc.d.style.display='none';
        lc.a.style.color='#666';
    }
    lc={d:c,c:i,a:a};
    a.style.color=(c.style.display=='' || c.style.display=='none')?"black":"#666";
    c.style.display=(!c.style.display || c.style.display=='none')?"block":"none";
}

/** 
 * ********************************* Misc Functions by Neyek ****************************************
 */

/**
 * get the htmlentities from a string
 *  
 * @Author Erik Amaru Ortiz 
 * @Param (string) string within the htmlentities to parse 
 * @Param (string) quote_style it can be (ENT_QUOTES or ENT_NOQUOTES)
 * @Return (string) the parsed string with htmlentities at the string passed such as parameter
 */
function htmlentities (string, quote_style) {
 
    var hash_map = {}, symbol = '', tmp_str = '', entity = '';
    tmp_str = string.toString();
    
    if (false === (hash_map = this.get_html_translation_table('HTML_ENTITIES', quote_style))) {
        return false;
    }
    hash_map["'"] = '&#039;';
    for (symbol in hash_map) {
        entity = hash_map[symbol];
        tmp_str = tmp_str.split(symbol).join(entity);
    }
    
    return tmp_str;
}

function get_html_translation_table (table, quote_style) {
    //example 1: get_html_translation_table('HTML_SPECIALCHARS');
    //returns 1: {'"': '&quot;', '&': '&amp;', '<': '&lt;', '>': '&gt;'}
    
    var entities = {}, hash_map = {}, decimal = 0, symbol = '';
    var constMappingTable = {}, constMappingQuoteStyle = {};
    var useTable = {}, useQuoteStyle = {};
    
    // Translate arguments
    constMappingTable[0]      = 'HTML_SPECIALCHARS';
    constMappingTable[1]      = 'HTML_ENTITIES';
    constMappingQuoteStyle[0] = 'ENT_NOQUOTES';
    constMappingQuoteStyle[2] = 'ENT_COMPAT';
    constMappingQuoteStyle[3] = 'ENT_QUOTES';
 
    useTable       = !isNaN(table) ? constMappingTable[table] : table ? table.toUpperCase() : 'HTML_SPECIALCHARS';
    useQuoteStyle = !isNaN(quote_style) ? constMappingQuoteStyle[quote_style] : quote_style ? quote_style.toUpperCase() : 'ENT_COMPAT';
 
    if (useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
        throw new Error("Table: "+useTable+' not supported');
        // return false;
    }
 
    entities['38'] = '&amp;';
    if (useTable === 'HTML_ENTITIES') {
        entities['160'] = '&nbsp;';
        entities['161'] = '&iexcl;';
        entities['162'] = '&cent;';
        entities['163'] = '&pound;';
        entities['164'] = '&curren;';
        entities['165'] = '&yen;';
        entities['166'] = '&brvbar;';
        entities['167'] = '&sect;';
        entities['168'] = '&uml;';
        entities['169'] = '&copy;';
        entities['170'] = '&ordf;';
        entities['171'] = '&laquo;';
        entities['172'] = '&not;';
        entities['173'] = '&shy;';
        entities['174'] = '&reg;';
        entities['175'] = '&macr;';
        entities['176'] = '&deg;';
        entities['177'] = '&plusmn;';
        entities['178'] = '&sup2;';
        entities['179'] = '&sup3;';
        entities['180'] = '&acute;';
        entities['181'] = '&micro;';
        entities['182'] = '&para;';
        entities['183'] = '&middot;';
        entities['184'] = '&cedil;';
        entities['185'] = '&sup1;';
        entities['186'] = '&ordm;';
        entities['187'] = '&raquo;';
        entities['188'] = '&frac14;';
        entities['189'] = '&frac12;';
        entities['190'] = '&frac34;';
        entities['191'] = '&iquest;';
        entities['192'] = '&Agrave;';
        entities['193'] = '&Aacute;';
        entities['194'] = '&Acirc;';
        entities['195'] = '&Atilde;';
        entities['196'] = '&Auml;';
        entities['197'] = '&Aring;';
        entities['198'] = '&AElig;';
        entities['199'] = '&Ccedil;';
        entities['200'] = '&Egrave;';
        entities['201'] = '&Eacute;';
        entities['202'] = '&Ecirc;';
        entities['203'] = '&Euml;';
        entities['204'] = '&Igrave;';
        entities['205'] = '&Iacute;';
        entities['206'] = '&Icirc;';
        entities['207'] = '&Iuml;';
        entities['208'] = '&ETH;';
        entities['209'] = '&Ntilde;';
        entities['210'] = '&Ograve;';
        entities['211'] = '&Oacute;';
        entities['212'] = '&Ocirc;';
        entities['213'] = '&Otilde;';
        entities['214'] = '&Ouml;';
        entities['215'] = '&times;';
        entities['216'] = '&Oslash;';
        entities['217'] = '&Ugrave;';
        entities['218'] = '&Uacute;';
        entities['219'] = '&Ucirc;';
        entities['220'] = '&Uuml;';
        entities['221'] = '&Yacute;';
        entities['222'] = '&THORN;';
        entities['223'] = '&szlig;';
        entities['224'] = '&agrave;';
        entities['225'] = '&aacute;';
        entities['226'] = '&acirc;';
        entities['227'] = '&atilde;';
        entities['228'] = '&auml;';
        entities['229'] = '&aring;';
        entities['230'] = '&aelig;';
        entities['231'] = '&ccedil;';
        entities['232'] = '&egrave;';
        entities['233'] = '&eacute;';
        entities['234'] = '&ecirc;';
        entities['235'] = '&euml;';
        entities['236'] = '&igrave;';
        entities['237'] = '&iacute;';
        entities['238'] = '&icirc;';
        entities['239'] = '&iuml;';
        entities['240'] = '&eth;';
        entities['241'] = '&ntilde;';
        entities['242'] = '&ograve;';
        entities['243'] = '&oacute;';
        entities['244'] = '&ocirc;';
        entities['245'] = '&otilde;';
        entities['246'] = '&ouml;';
        entities['247'] = '&divide;';
        entities['248'] = '&oslash;';
        entities['249'] = '&ugrave;';
        entities['250'] = '&uacute;';
        entities['251'] = '&ucirc;';
        entities['252'] = '&uuml;';
        entities['253'] = '&yacute;';
        entities['254'] = '&thorn;';
        entities['255'] = '&yuml;';
    }
 
    if (useQuoteStyle !== 'ENT_NOQUOTES') {
        entities['34'] = '&quot;';
    }
    if (useQuoteStyle === 'ENT_QUOTES') {
        entities['39'] = '&#39;';
    }
    entities['60'] = '&lt;';
    entities['62'] = '&gt;';
 
 
    // ascii decimals to real symbols
    for (decimal in entities) {
        symbol = String.fromCharCode(decimal);
        hash_map[symbol] = entities[decimal];
    }
    
    return hash_map;
}

/**
 * This function sets netsted properties to dom elements
 *  
 * @Author Erik Amaru Ortiz 
 * @Param (object) dom element refered by ID
 * @Param (array) nested array properties
 * @Param (object) value to set
 * @Return <none>
 * 
 * Example:
 * 		setNestedProperty(document, ['body','style','backgroundColor'], 'black');
 */
function setNestedProperty(obj, propertyName, propertyValue){
	var oTarget = obj;
	for (var i=0; i<propertyName.length; i++){
		if (i == (propertyName.length - 1)){
			oTarget[propertyName[i]] = propertyValue;
			return false;
		}
		oTarget = oTarget[propertyName[i]];	
	}
}

