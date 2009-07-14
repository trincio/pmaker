/* PACKAGE : GULLIVER FORMS
 */
  function G_Form ( element, id )
  {
    var me=this;
    this.info = {name:'G_Fom', version :'1.0'};
    /*this.module=RESERVED*/
    this.element=element;
    if (!element) return;
    this.id=id;
    this.aElements=[];
    this.ajaxServer='';
    this.getElementIdByName = function (name)  {
      if (name=='') return -1;
      var j;
      for(j=0;j<me.aElements.length;j++) {
        if (me.aElements[j].name===name) return j;
      }
      return -1;
    }
    this.getElementByName = function (name)  {
      var i=me.getElementIdByName(name);
      if (i>=0) return me.aElements[i]; else return null;
    }
    this.hideGroup = function( group, parentLevel ){
      if (typeof(parentLevel)==='undefined') parentLevel = 1;
      for( var r=0 ; r < me.aElements.length ; r++ ) {
        if ((typeof(me.aElements[r].group)!=='undefined') && (me.aElements[r].group == group ))
          me.aElements[r].hide(parentLevel);
      }
    }
    this.showGroup = function( group, parentLevel ){
      if (typeof(parentLevel)==='undefined') parentLevel = 1;
      for( var r=0 ; r < me.aElements.length ; r++ ) {
        if ((typeof(me.aElements[r].group)!=='undefined') && (me.aElements[r].group == group ))
          me.aElements[r].show(parentLevel);
      }
    }
    this.verifyRequiredFields=function(){
      var valid=true;
      for(var i=0;i<me.aElements.length;i++){
        var verifiedField=((!me.aElements[i].required)||(me.aElements[i].required && (me.aElements[i].value()!=='')));
        valid=valid && verifiedField;
        if (!verifiedField) {
          me.aElements[i].highLight();
        }
      }
      return valid;
    }
  };

  function G_Field ( form, element, name )
  {
    var me=this;
    this.form=form;
    this.element=element;
    this.name=name;
    this.dependentFields=[];
    this.dependentOf=[];
    this.hide = function( parentLevel ){
      if (typeof(parentLevel)==='undefined') parentLevel = 1;
      var parent = me.element;
      for( var r=0; r< parentLevel ; r++ )
        parent = parent.parentNode;
      parent.style.display = 'none';
    }
    this.show = function( parentLevel ){
      if (typeof(parentLevel)==='undefined') parentLevel = 1;
      var parent = me.element;
      for( var r=0; r< parentLevel ; r++ )
        parent = parent.parentNode;
      parent.style.display = '';
    }
    this.setDependentFields = function(dependentFields) {
      var i;
      if (dependentFields.indexOf(',') > -1) {
        dependentFields = dependentFields.split(',');
      }
      else {
        dependentFields = dependentFields.split('|');
      }
      for(i=0;i<dependentFields.length;i++) {
        if (me.form.getElementIdByName(dependentFields[i])>=0) {
          me.dependentFields[i] = me.form.getElementByName(dependentFields[i]);
          me.dependentFields[i].addDependencie(me);
        }
      }
    }
    this.addDependencie = function (field) {
      var exists = false;
      for (i=0;i<me.dependentOf.length;i++)
        if (me.dependentOf[i]===field) exists = true;
      if (!exists) me.dependentOf[i] = field;
    }
    this.updateDepententFields=function(event) {
      if (me.dependentFields.length===0) return true;
      var fields=[],i,grid='',row=0;
      for(i in me.dependentFields) {
        if (me.dependentFields[i].dependentOf) {
          for (var j = 0; j < me.dependentFields[i].dependentOf.length; j++) {
            var oAux = me.dependentFields[i].dependentOf[j];
            if (oAux.name.indexOf('][') > -1) {
              var aAux  = oAux.name.split('][');
              grid      = aAux[0];
              row       = aAux[1];
              eval("var oAux2 = {" + aAux[2] + ":'" + oAux.value() + "'}");
              fields = fields.concat(oAux2);
            }
            else {
              fields = fields.concat(me.dependentFields[i].dependentOf);
            }
          }
        }
      }
      var callServer;
      callServer = new leimnud.module.rpc.xmlhttp({
      		url			: me.form.ajaxServer,
      		async   : false,
      		method	: "POST",
      		args    : "function=reloadField&" + 'form='+encodeURIComponent(me.form.id)+'&fields='+encodeURIComponent(fields.toJSONString())+(grid!=''?'&grid='+grid:'')+(row>0?'&row='+row:'')
      	});
    	callServer.make();
    	var response = callServer.xmlhttp.responseText;

      //Validate the response
      if (response.substr(0,1)==='[') {
        var newcont;
        eval('newcont=' + response + ';');
        if (grid == '') {
          for(var i=0;i<newcont.length;i++) {
            var j=me.form.getElementIdByName(newcont[i].name);
            me.form.aElements[j].setValue(newcont[i].value);
            me.form.aElements[j].setContent(newcont[i].content);
            if (me.form.aElements[j].element.fireEvent) {
  		        me.form.aElements[j].element.fireEvent("onchange");
  		      } else {
              var evObj = document.createEvent('HTMLEvents');
              evObj.initEvent( 'change', true, true );
    		      me.form.aElements[j].element.dispatchEvent(evObj);
  		      }
          }
        }
        else {
          for(var i=0;i<newcont.length;i++) {
            var oAux = me.form.getElementByName(grid);
            if (oAux) {
              var oAux2 = oAux.getElementByName(row, newcont[i].name);
              if (oAux2) {
                if (newcont[i].content.type == 'dropdown') {
                  oAux2.setValue(newcont[i].value);
                }
                oAux2.setContent(newcont[i].content);
                if (oAux2.element.fireEvent) {
  		            oAux2.element.fireEvent("onchange");
  		          } else {
                  var evObj = document.createEvent('HTMLEvents');
                  evObj.initEvent( 'change', true, true );
    		          oAux2.element.dispatchEvent(evObj);
  		          }
              }
            }
          }
        }
      } else {
        alert('Invalid response: '+response);
      }
      return true;
    }
    this.setValue = function(newValue) {
      me.element.value = newValue;
    }
    this.setContent = function(newContent) {

    }
    this.setAttributes = function (attributes) {
      for(var a in attributes) {
        switch (typeof(attributes[a])) {
          case 'string':
          case 'int':
          case 'boolean':
          if (a != 'strTo') {
            switch (true) {
              case typeof(me[a])==='undefined':
              case typeof(me[a])==='object':
              case typeof(me[a])==='function':
              case a==='isObject':
              case a==='isArray':
                break;
              default:
                me[a] = attributes[a];
            }
          }
          else {
            me[a] = attributes[a];
          }
        }
      }
    }
    this.value=function() {
      return me.element.value;
    }
    this.toJSONString=function()  {
      return '{'+me.name+':'+me.element.value.toJSONString()+'}';
    }
    this.highLight=function(){
      try{
        G.highLight(me.element);
        if (G.autoFirstField) {
          me.element.focus();
          G.autoFirstField=false;
          setTimeout("G.autoFirstField=true;",1000);
        }
      } catch (e){
      }
    }
  }

  function G_DropDown( form, element, name )
  {
    var me=this;
    this.parent = G_Field;
    this.parent( form, element, name );
    this.setContent=function(content) {
      var dd=me.element;
      while(dd.options.length>0) dd.remove(0);
      for(var o=0;o<content.options.length;o++) {
        var optn = $dce("OPTION");
        optn.text = content.options[o].value;
        optn.value = content.options[o].key;
        dd.options[o]=optn;
      }
    }
    if (!element) return;
    leimnud.event.add(this.element,'change',this.updateDepententFields);
  }
  G_DropDown.prototype=new G_Field();

  function G_Text( form, element, name )
  {
    var me=this;
    this.parent = G_Field;
    this.parent( form, element, name );
    if (element) {
      this.prev = element.value;
    }
    this.validate = 'Any';
    this.mask='';
    this.required=false;
    var doubleChange=false;

    this.setContent=function(content) {
      me.element.value = '';
      if (content.options) {
        if (content.options[0]) {
          me.element.value = content.options[0].value;
        }
      }
    }

    this.validateKey=function(event) {
      if(me.element.readOnly)  return true;
      me.prev = me.element.value;
      if (window.event) event=window.event;
      var keyCode= window.event ? event.keyCode : event.which ;
      me.mask = typeof(me.mask)==='undefined'?'':me.mask;
      if (me.mask !=='' ) {
        if (event.ctrlKey) return true;
        if (event.altKey) return true;
        if (event.shiftKey) return true;
      }
      if ((keyCode===0) ) if (event.keyCode===46) return true; else return true;
      if ( (keyCode===8)) return true;
      if (me.mask ==='') {
        if (me.validate == 'NodeName') {
          if (me.getCursorPos() == 0) {
            if ((keyCode >= 48) && (keyCode <= 57)) {
              return false;
            }
          }
          var k=new leimnud.module.validator({
            valid	:['Field'],
            key		:event,
            lang	:(typeof(me.language)!=='undefined')?me.language:"en"
          });
          return k.result();
        }
        else {
      	  var k=new leimnud.module.validator({
            valid	:[me.validate],
            key		:event,
            lang	:(typeof(me.language)!=='undefined')?me.language:"en"
          });
          return k.result();
        }
      } else {
        //return true;
        if (doubleChange) {doubleChange=false;return false;}
        var sel = me.getSelectionRange();
        var myValue = String.fromCharCode(keyCode);
        var startPos=sel.selectionStart;
        var endPos=sel.selectionEnd;
        var myField = me.element;
        var newValue = myField.value
        if (keyCode===8) {
          if (startPos>0)
          newValue = myField.value.substring(0, startPos + ((startPos==endPos)?-1:0) )
                    + myField.value.substring(endPos, myField.value.length);
        } else {
          newValue = myField.value.substring(0, startPos)
                    + myValue
                    + myField.value.substring(endPos, myField.value.length);
        }
        var Esperado = newValue;
        startPos++;
        var newValue2=G.cleanMask( newValue, me.mask, startPos );

        newValue2=G.toMask( newValue2.result, me.mask, newValue2.cursor );

        me.element.value=newValue2.result;
  		  me.setSelectionRange(newValue2.cursor, newValue2.cursor);


  		  if (me.element.fireEvent) {
  		    me.element.fireEvent("onchange");
  		  } else {
          var evObj = document.createEvent('HTMLEvents');
          evObj.initEvent( 'change', true, true );
    		  me.element.dispatchEvent(evObj);
  		  }

        return false;
      }
    }

    this.preValidateChange=function(event) {
      if(me.element.readOnly)  return true;
      if (me.mask ==='') return true;
      if (event.keyCode===46) {
        var sel=me.getSelectionRange();
        var startPos = sel.selectionStart;
        var endPos   = sel.selectionEnd;
        var myField  = me.element;
        var newValue = myField.value
        if (startPos<myField.value.length) {
          var newValue = myField.value.substring(0, startPos)
          + myField.value.substring(endPos+1, myField.value.length);
          newValue2=G.cleanMask( newValue, me.mask, startPos );
          newValue2=G.toMask( newValue2.result, me.mask, newValue2.cursor );
          me.element.value=newValue2.result;
    		  me.setSelectionRange(startPos, startPos);
  		  }
        return false;
      }
      if (event.keyCode===8) {
        var sel=me.getSelectionRange();
        var startPos = sel.selectionStart;
        var endPos   = sel.selectionEnd;
        var myField = me.element;
        var newValue = myField.value
        if (startPos>0) {
          newValue = myField.value.substring(0, startPos-1)
          + myField.value.substring(endPos, myField.value.length);
          newValue2=G.cleanMask( newValue, me.mask, startPos );
          newValue2=G.toMask( newValue2.result, me.mask, newValue2.cursor );
          me.element.value=newValue2.result;
    		  me.setSelectionRange(startPos-1, startPos-1);
  		  }
        return false;
      }
      me.prev=me.element.value;
      return true;
    }
    this.validateChange=function(event) {
      if (me.mask ==='') return true;
		  var sel=me.getSelectionRange();
      var newValue2=G.cleanMask( me.element.value, me.mask, sel.selectionStart );
	    newValue2=G.toMask( newValue2.result, me.mask, newValue2.cursor);
	    me.element.value = newValue2.result;
		  me.setSelectionRange(newValue2.cursor, newValue2.cursor);
      return true;
    }

    this.value=function()
    {
      return me.element.value;
    }

    this.getCursorPos = function () {
      var textElement=me.element;
      if (!document.selection) return textElement.selectionStart;
      //save off the current value to restore it later,
      var sOldText = textElement.value;

    //create a range object and save off it's text
      var objRange = document.selection.createRange();
      var sOldRange = objRange.text;

    //set this string to a small string that will not normally be encountered
      var sWeirdString = '#%~';

    //insert the weirdstring where the cursor is at
      objRange.text = sOldRange + sWeirdString; objRange.moveStart('character', (0 - sOldRange.length - sWeirdString.length));

    //save off the new string with the weirdstring in it
      var sNewText = textElement.value;

    //set the actual text value back to how it was
      objRange.text = sOldRange;

    //look through the new string we saved off and find the location of
    //the weirdstring that was inserted and return that value
      for (i=0; i <= sNewText.length; i++) {
        var sTemp = sNewText.substring(i, i + sWeirdString.length);
        if (sTemp == sWeirdString) {
          var cursorPos = (i - sOldRange.length);
          return cursorPos;
        }
      }
    }
    this.setSelectionRange = function(selectionStart, selectionEnd) {
      var input=me.element;
      if (input.createTextRange) {
      var range = input.createTextRange();
      range.collapse(true);
      range.moveEnd('character', selectionEnd);
      range.moveStart('character', selectionStart);
      range.select();
      }
      else if (input.setSelectionRange) {
      input.focus();
      input.setSelectionRange(selectionStart, selectionEnd);
      }
    }
    this.getSelectionRange = function() {
      if (document.selection) {
        var textElement=me.element;
        var sOldText = textElement.value;
        var objRange = document.selection.createRange();
        var sOldRange = objRange.text;
        var sWeirdString = '#%~';
        objRange.text = sOldRange + sWeirdString; objRange.moveStart('character', (0 - sOldRange.length - sWeirdString.length));
        var sNewText = textElement.value;
        objRange.text = sOldRange;
        for (i=0; i <= sNewText.length; i++) {
          var sTemp = sNewText.substring(i, i + sWeirdString.length);
          if (sTemp == sWeirdString) {
            var cursorPos = (i - sOldRange.length);
            return {selectionStart: cursorPos, selectionEnd: cursorPos+sOldRange.length};
          }
        }
      } else {
        var sel={selectionStart: 0, selectionEnd: 0};
        sel.selectionStart = me.element.selectionStart;
        sel.selectionEnd = me.element.selectionEnd;
        return sel;
      }
    }
    if (!element) return;
    if (!window.event)
      this.element.onkeypress = this.validateKey;
    else
      leimnud.event.add(this.element,'keypress',this.validateKey);
    leimnud.event.add(this.element,'change',this.updateDepententFields);
	this.element.onblur=function()
	{
	    	if(this.validate=="Email")
		{
			var pat=/^[\w\_\-\.çñ]{2,255}@[\w\_\-]{2,255}\.[a-z]{1,3}\.?[a-z]{0,3}$/;
			if(!pat.test(this.element.value))
			{
				this.element.className=this.element.className.split(" ")[0]+" FormFieldInvalid";
			}
			else
			{
				this.element.className=this.element.className.split(" ")[0]+" FormFieldValid";
			}
		}
		if (this.strTo) {
		  switch (this.strTo) {
		    case 'UPPER':
		      this.element.value = this.element.value.toUpperCase();
		    break;
		    case 'LOWER':
		      this.element.value = this.element.value.toLowerCase();
		    break;
		  }
		}
		if (this.validate == 'NodeName') {
		  var pat = /^[a-z\_](.)[a-z\d\_]{1,255}$/i;
		  if(!pat.test(this.element.value)) {
		    this.element.value = '_' + this.element.value;
		  }
		}
	}.extend(this);
/*    leimnud.event.add(this.element,'blur',function() {
    	if (this.validate == 'Email') {
 		//if (!this.element.value.match("[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[A-Z]{2}|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum)\$")) {
		var pat=/^[\w\_\.çñ]{2,255}@[\w]{2,255}\.[a-z]{1,3}\.?[a-z]{0,3}$/;
		if(!pat.test(this.element.value)){
 	//	if (!this.element.value.match("[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[A-Z]{2-3})\$")) {
    			new leimnud.module.app.alert().make({
					  label:G_STRINGS.ID_INVALID_EMAIL
					});
    			this.element.value = '';
    		}
    	}
    }.extend(this));*/
    leimnud.event.add(this.element,'keydown',this.preValidateChange);
  }
  G_Text.prototype=new G_Field();

  function G_Percentage( form, element, name )
  {
    var me=this;
    this.parent = G_Text;
    this.parent( form, element, name );
    this.validate = 'Int';
    this.mask= '###.##';
  }
  G_Percentage.prototype=new G_Field();

  function G_Currency( form, element, name )
  {
    var me=this;
    this.parent = G_Text;
    this.parent( form, element, name );
    this.validate = 'Int';
    this.mask= '_###,###,###,###,###;###,###,###,###,###.00';
  }
  function G_TextArea( form, element, name )
  {
    var me=this;
    this.parent = G_Text;
    this.parent( form, element, name );
    this.validate = 'Any';
    this.mask= '';
  }
  G_Percentage.prototype=new G_Field();

  function G_Date( form, element, name )
  {
    var me=this;
    this.parent = G_Text;
    this.parent( form, element, name );
    this.mask= 'dd-mm-yyyy';
  }
  G_Percentage.prototype=new G_Field();

function G()
{
  /*MASK*/
  var reserved=['_',';','#','.','0','d','m','y','-'];
  function invertir(num)
  {
    var num0='';
    num0=num;num="";
    for(r=num0.length-1;r>=0;r--) num+= num0.substr(r,1);
    return num;
  }
  function __toMask(num, mask, cursor)
  {
    var inv=false;
    if (mask.substr(0,1)==='_') {mask=mask.substr(1);inv=true;}
    var re;
    if (inv) {
      mask=invertir(mask);
      num=invertir(num);
    }

    var minAdd=-1;
    var minLoss=-1;
    var newCursorPosition=cursor;
    var betterOut="";
    for(var r0=0;r0< mask.length; r0++) {
      var out="";
      var j=0;
      var loss=0;var add=0;
      loss=0;add=0;var cursorPosition=cursor;
      var i=-1;
      var dayPosition=0;
      var mounthPosition=0;
      var dayAnalized ='';
      var mounthAnalized ='';
      var blocks={};
      for(var r=0;r< r0 ;r++) {
        var e=false;
        var m=mask.substr(r,1);
        __parseMask();
      }
      i=0;
      for(r=r0;r< mask.length;r++) {
        j++;if (j>200) break;
        e=num.substr(i,1);
        e=(e==='')?false:e;
        m=mask.substr(r,1);
        __parseMask();
      }
      var io=num.length - i;
      io=(io<0)?0:io;
      loss+=io;
      loss=loss+add/1000;
      //var_dump($loss);
      if (loss===0) {betterOut=out;minLoss=0;newCursorPosition=cursorPosition; break;}
      if ((minLoss===-1)||(loss< minLoss)) { minLoss=loss; betterOut=out; newCursorPosition=cursorPosition; }
      //echo('min:');var_dump($minLoss);
    }
  //  var_dump($minLoss);
    out=betterOut;
    if (inv) {
      out=invertir(out);
      mask=invertir(mask);
    }
    return {'result':out,'cursor':newCursorPosition,'value':minLoss,'mask':mask};
    function searchBlock( where , what )
    {
      for(var r=0; r < where.length ; r++ ) {
        if (where[r].key === what) return where[r];
      }
    }
    function __parseMask()
    {
      var ok=true;
      switch(false) {
        case m==='d': dayAnalized='';break;
        case m==='m': mounthAnalized='';break;
        default:
      }
      if ( e!==false ) {
        if (typeof(blocks[m])==='undefined') blocks[m] = e; else blocks[m] += e;
      }
      switch(m) {
      case '0':
        if (e===false) {out+='0';add++; break;}
      case 'y':
      case '#':
        if (e===false) {out+='';break;}
        //Use direct comparition to increse speed of processing
        if ((e==='0')||(e==='1')||(e==='2')||(e==='3')||(e==='4')||(e==='5')||(e==='6')||(e==='7')||(e==='8')||(e==='9')||(e==='-')) {
          out+=e;i++;
        } else {
          //loss
          loss++;
          i++;r--;
        }
        break;
      case '(':
          if (e===false) {out+='';break;}
          out+=m;
          if (i<cursor){cursorPosition++;}
          break;
      case 'd':
        if (e===false) {out+='';break;}
        if ((e==='0')||(e==='1')||(e==='2')||(e==='3')||(e==='4')||(e==='5')||(e==='6')||(e==='7')||(e==='8')||(e==='9')) ok=true; else ok=false;
        //if (ok) if (dayPosition===0) if (parseInt(e)>3) ok=false
        //dayPosition=(dayPosition+1) | 1;
        if (ok) dayAnalized = dayAnalized + e;
        if ((ok) && (parseInt(dayAnalized)>31)) ok = false;
        if (ok) {
          out+=e;i++;
        } else {
          //loss
          loss++;
          i++;r--;
        }
        break;
      case 'm':
        if (e===false) {out+='';break;}
        if ((e==='0')||(e==='1')||(e==='2')||(e==='3')||(e==='4')||(e==='5')||(e==='6')||(e==='7')||(e==='8')||(e==='9')) ok=true; else ok=false;
        if (ok) mounthAnalized = mounthAnalized + e;
        if ((ok) && (parseInt(mounthAnalized)>12)) ok=false;
        if (ok) {
          out+=e;i++;
        } else {
          //loss
          loss++;
          i++;r--;
        }
        break;
      default:
        if (e===false) {out+='';break;}
        if (e===m) {
          out+=e;i++;
        } else {
          //if (m==='.') alert(i.toString() +'.'+ cursor.toString());
          out+=m;add++;if (i<cursor){cursorPosition++;};
        }
      }
    }
  }
  this.toMask = function (num, mask, cursor)
  {
    if (mask==='') return {'result':new String(num), 'cursor':cursor};
    var subMasks=mask.split(';');
    var result = [];
    num = new String(num);
    for(var r=0; r<subMasks.length; r++) {
      result[r]=__toMask(num, subMasks[r], cursor);
    }
    var betterResult=0;
    for(r=1; r<subMasks.length; r++) {
      if (result[r].value<result[betterResult].value) betterResult=r;
    }
    return result[betterResult];
  }
  this.cleanMask = function (num, mask, cursor)
  {
    mask = typeof(mask)==='undefined'?'':mask;
    if (mask==='') return {'result':new String(num), 'cursor':cursor};
    var a,r,others=[];
    num = new String(num);
    //alert(oDebug.var_dump(num));
    if (typeof(cursor)==='undefined') cursor=0;
    a = num.substr(0,cursor);
    for(r=0; r<reserved.length; r++) mask=mask.split(reserved[r]).join('');
    while(mask.length>0) {
      r=others.length;
      others[r] = mask.substr(0,1);
      mask= mask.split(others[r]).join('');
      num = num.split(others[r]).join('');
      cursor -= a.split(others[r]).length-1;
    }
    return {'result':num, 'cursor':cursor};
  }
  this.getId=function(element){
    var re=/(\[(\w+)\])+/;
		var res=re.exec(element.id);
		return res?res[2]:element.id;
  }
  this.getObject=function(element){
    var objId=G.getId(element);
    switch (element.tagName){
      case 'FORM':
        return eval('form_'+objId);
        break;
      default:
        if (element.form) {
          var formId=G.getId(element.form);
          return eval('form_'+objId+'.getElementByName("'+objId+'")');
        }
    }
  }

  /*BLINK EFECT*/
  this.blinked=[];
  this.blinkedt0=[];
  this.autoFirstField=true;
  this.pi=Math.atan(1)*4;
  this.highLight = function(element){
    var newdiv = $dce('div');
    newdiv.style.position="absolute";
    newdiv.style.display="inline";
    newdiv.style.height=element.clientHeight+2;
    newdiv.style.width=element.clientWidth+2;
    newdiv.style.background = "#FF5555";
    element.style.backgroundColor='#FFCACA';
    element.parentNode.insertBefore(newdiv,element);
    G.doBlinkEfect(newdiv,1000);
  }
  this.setOpacity=function(e,o){
    e.style.filter='alpha';
    if (e.filters) {
      e.filters['alpha'].opacity=o*100;
    } else {
      e.style.opacity=o;
    }
  }
  this.doBlinkEfect=function(div,T){
    var f=1/T;
    var j=G.blinked.length;
    G.blinked[j]=div;
    G.blinkedt0[j]=(new Date()).getTime();
    for(var i=1;i<=20;i++){
      setTimeout("G.setOpacity(G.blinked["+j+"],0.3-0.3*Math.cos(2*G.pi*((new Date()).getTime()-G.blinkedt0["+j+"])*"+f+"));",T/20*i);
    }
    setTimeout("G.blinked["+j+"].parentNode.removeChild(G.blinked["+j+"]);G.blinked["+j+"]=null;",T/20*i);
  }
  var alertPanel;
  this.alert=function(html, title , width, height, autoSize, modal, showModalColor, runScripts)
  {
    html='<div>'+html+'</div>';
  	width = (width)?width:300;
  	height = (height)?height:200;
  	autoSize = (showModalColor===false)?false:true;
  	modal = (modal===false)?false:true;
  	showModalColor = (showModalColor===true)?true:false;
  	var alertPanel = new leimnud.module.panel();
  	alertPanel.options = {
  		size:{w:width,h:height},
  		position:{center:true},
  		title: title,
  		theme: "processmaker",
  		control: { close :true, roll	:false, drag	:true, resize	:true},
  		fx: {
  			blinkToFront:true,
  			opacity	:true,
  			drag:true,
  			modal: modal
  		}
  	};
  	if(showModalColor===false)
  	{
  		alertPanel.styles.fx.opacityModal.Static='0';
  	}
  	alertPanel.make();
		alertPanel.addContent(html);
		if(runScripts)
		{
  		var myScripts=alertPanel.elements.content.getElementsByTagName('SCRIPT');
  		var sMyScripts=[];
  		for(var rr=0; rr<myScripts.length ; rr++) sMyScripts.push(myScripts[rr].innerHTML);
  		for(var rr=0; rr<myScripts.length ; rr++){
  		  try {
  		    if (sMyScripts[rr]!=='')
  		      if (window.execScript)
  	          window.execScript( sMyScripts[rr], 'javascript' );
  	        else
  	          window.setTimeout( sMyScripts[rr], 0 );
  		  } catch (e) {
  		    alert(e.description);
  		  }
  		}
  	}
		/* Autosize of panels, to fill only the first child of the
		 * rendered page (take note)
		 */
		var panelNonContentHeight = 44;
		var panelNonContentWidth  = 28;
		try {
		  if (autoSize)
		  {
		    var newW=alertPanel.elements.content.childNodes[0].clientWidth+panelNonContentWidth;
		    var newH=alertPanel.elements.content.childNodes[0].clientHeight+panelNonContentHeight;
  		  alertPanel.resize({w:((newW<width)?width:newW)});
  		  alertPanel.resize({h:((newH<height)?height:newH)});
  		}
	  } catch (e) {
	    alert(var_dump(e));
	  }
		delete newdiv;
		delete myScripts;
		alertPanel.command(alertPanel.loader.hide);
  }
}
var G = new G();


/* PACKAGE : DEBUG
 */
function G_Debugger()
{
  this.var_dump = function(obj)
  {
    var o,dump;
    dump='';
    if (typeof(obj)=='object')
    for(o in obj)
    {
      dump+='<b>'+o+'</b>:'+obj[o]+"<br>\n";
    }
    else
      dump=obj;
    debugDiv = document.getElementById('debug');
    if (debugDiv) debugDiv.innerHTML=dump;
    return dump;
  }
}
var oDebug = new G_Debugger();

/* PACKAGE : date field
 */
var datePickerPanel;

function showDatePicker(ev, formId, idName, value, min, max  ) {
	var coor = leimnud.dom.mouse(ev);
	var coorx = ( coor.x - 50 );
	var coory = ( coor.y - 40 );
	datePickerPanel=new leimnud.module.panel();
	datePickerPanel.options={
		size:{w:275,h:240},
		position:{x:coorx,y:coory},
		title:"Date Picker",
		theme:"panel",
		control:{
			close:true,
			drag:true
		},
		fx:{
			modal:true
		}
	};

	datePickerPanel.setStyle={
				containerWindow:{borderWidth:0}
			};
	datePickerPanel.make();
	datePickerPanel.idName = idName;
	datePickerPanel.formId = formId;

	var sUrl = "/controls/calendar.php?v="+value+"&d="+value+"&min="+min+"&max="+max;
	var r = new leimnud.module.rpc.xmlhttp({url: sUrl });
	r.callback=leimnud.closure({Function:function(rpc){
		datePickerPanel.addContent(rpc.xmlhttp.responseText);
	},args:r})
	r.make();

}

function moveDatePicker( n_datetime ) {
	var dtmin_value = document.getElementById ( 'dtmin_value' );
	var dtmax_value = document.getElementById ( 'dtmax_value' );

	var sUrl = "/controls/calendar.php?d="+n_datetime + '&min='+dtmin_value.value + '&max='+dtmax_value.value;
	var r = new leimnud.module.rpc.xmlhttp({url:sUrl });
	r.callback=leimnud.closure({Function:function(rpc){
		datePickerPanel.clearContent();
		datePickerPanel.addContent(rpc.xmlhttp.responseText);
	},args:r})
	r.make();
}

function selectDate(  day ) {
	var obj = document.getElementById ( 'span['+datePickerPanel.formId+'][' + datePickerPanel.idName + ']' );
	getField(datePickerPanel.idName, datePickerPanel.formId ).value = day;
	obj.innerHTML = day;
	datePickerPanel.remove();
}

function set_datetime(n_datetime, b_close) {
	moveDatePicker(n_datetime);
}

/* Functions for show and hide rows of a simple xmlform.
 * @author David Callizaya <davidsantos@colosa.com>
 */
function getRow( element ){
  if (typeof(element)==='string') element = getField(element);
  while ( element.tagName !== 'TR' ) {
    element=element.parentNode;
  }
  return element;
}
var getRowById=getRow;
function hideRow( element ){
  var row=getRow(element);
  if (row) row.style.display='none';
  delete row;
}
var hideRowById=hideRow;
function showRow( element ){
  var row=getRow(element);
  if (row) row.style.display='';
  delete row;
}
var showRowById=showRow;
function hideShowControl(element , name){
  var control;
  if (element) {
      control = element.parentNode.getElementsByTagName("div")[0];
    control.style.display=control.style.display==='none'?'':'none';
    if (control.style.display==='none') getField( name ).value='';
    delete control;
  }
}
/*SHOW/HIDE A SUBTITLE CONTENT*/
function contractSubtitle( subTitle ){
  subTitle=getRow(subTitle);
  var c=subTitle.cells[0].className;
  var a=subTitle.rowIndex;
  var t=subTitle.parentNode;
  for(var i=a+1,m=t.rows.length;i<m;i++){
    if (t.rows[i].cells.length==1) break;
    t.rows[i].style.display='none';
  }
}
function expandSubtitle( subTitle ){
  subTitle=getRow(subTitle);
  var c=subTitle.cells[0].className;
  var a=subTitle.rowIndex;
  var t=subTitle.parentNode;
  for(var i=a+1,m=t.rows.length;i<m;i++){
    if (t.rows[i].cells.length==1) break;
    t.rows[i].style.display='';
  }
}
function contractExpandSubtitle(subTitle){
  subTitle=getRow(subTitle);
  var c=subTitle.cells[0].className;
  var a=subTitle.rowIndex;
  var t=subTitle.parentNode;
  var contracted=false;
  for(var i=a+1,m=t.rows.length;i<m;i++){
    if (t.rows[i].cells.length==1) break;
    if (t.rows[i].style.display==='none'){
      contracted=true;
    }
  }
  if (contracted) expandSubtitle(subTitle);
  else contractSubtitle(subTitle);
}

var notValidateThisFields = new Array();

function validateForm(aRequiredFields)
{
	//alert(oRequiredFields.junior['type']);
	//alert(var_dump(aRequiredFields.toJSONString()));	
	var sMessage = '';
	for (var i = 0; i < aRequiredFields.length; i++) { 
		 var sw = 0;			 
		 for(var j=0; j < notValidateThisFields.length; j++)
		   { if(aRequiredFields[i].name ==  notValidateThisFields[j])
		   			sw=1;		   		
		   }
		 
		 if(sw==0)
		 	{	
		 		switch(aRequiredFields[i].type) { 
		 			  case 'text':
		 			    var vtext = new input(getField(aRequiredFields[i].name));
		 			    if(getField(aRequiredFields[i].name).value=='')
		 			    	{ sMessage += "- " + aRequiredFields[i].label + "\n";
		 			    		vtext.failed();
		 			    	}
		 			    	else
		 			    	{
		 			    	  vtext.passed();
		 			    	}
		 			  break;
     		
		 			  case 'dropdown':
		 			    if(getField(aRequiredFields[i].name).value=='')
		 			    		sMessage += "- " + aRequiredFields[i].label + "\n";
		 			  break;
     		
		 			  case 'textarea':
		 			    if(getField(aRequiredFields[i].name).value=='')
		 			    		sMessage += "- " + aRequiredFields[i].label + "\n";
		 			  break;
     		
		 			  case 'password':
		 			    var vpass = new input(getField(aRequiredFields[i].name));
		 			    if(getField(aRequiredFields[i].name).value=='')
		 			    	{ sMessage += "- " + aRequiredFields[i].label + "\n";
		 			    		vpass.failed();
		 			    	}
		 			    	else
		 			    	{
		 			    	  vpass.passed();
		 			    	}
		 			  break;
     		
		 			  case 'currency':
		 			    var vcurr = new input(getField(aRequiredFields[i].name));
		 			    if(getField(aRequiredFields[i].name).value=='')
		 			    	{ sMessage += "- " + aRequiredFields[i].label + "\n";
		 			    		vcurr.failed();
		 			    	}
		 			    	else
		 			    	{
		 			    	  vcurr.passed();
		 			    	}
		 			  break;
     		
		 			  case 'percentage':
		 			    var vper = new input(getField(aRequiredFields[i].name));
		 			    if(getField(aRequiredFields[i].name).value=='')
		 			    	{ sMessage += "- " + aRequiredFields[i].label + "\n";
		 			    		vper.failed();
		 			    	}
		 			    	else
		 			    	{
		 			    	  vper.passed();
		 			    	}
		 			  break;
     		
		 			  case 'yesno':
		 			    if(getField(aRequiredFields[i].name).value=='')
		 			    		sMessage += "- " + aRequiredFields[i].label + "\n";
		 			  break;
     		
		 			  case 'date':
		 			    if(getField(aRequiredFields[i].name).value=='')
		 			    		sMessage += "- " + aRequiredFields[i].label + "\n";
		 			  break;
     		
		 			  case 'file':
		 			    if(getField(aRequiredFields[i].name).value=='')
		 			    		sMessage += "- " + aRequiredFields[i].label + "\n";
		 			  break;
     		
		 			  case 'listbox':
		 			    var oAux = getField(aRequiredFields[i].name);
							var bOneSelected = false;
							for (var j = 0; j < oAux.options.length; j++) {
							 	if (oAux.options[j].selected) {
							    bOneSelected = true;
							    j = oAux.options.length;
							  }
							}
							if(bOneSelected == false)
		 			    		sMessage += "- " + aRequiredFields[i].label + "\n";
		 			  break;
     		
		 			  case 'radiogroup':
		 			  	var x=aRequiredFields[i].name;
		 			  	var oAux = document.getElementsByName('form['+ x +']');
							var bOneChecked = false;
							for (var k = 0; k < oAux.length; k++) {
							    var r = oAux[k];
							    if (r.checked) {
							      bOneChecked = true;
							    	k = oAux.length;
							  	}
							}
     		
							if(bOneChecked == false)
		 			    	sMessage += "- " + aRequiredFields[i].label + "\n";
     		
		 			  break;
		 			}
		 	}		
	}

	if (sMessage != '') {
		alert(G_STRINGS.ID_REQUIRED_FIELDS + ": \n\n" + sMessage);
		/*
		new leimnud.module.app.alert().make({
     label:G_STRINGS.ID_REQUIRED_FIELDS + ": <br />" + sMessage
    });
    */
		return false;
	}
	else
	{	return true;
  }
}

var getObject = function(sObject) {
  var i;
  var oAux = null;
  var iLength = __aObjects__.length;
  for (i = 0; i < iLength; i++) {
    oAux = __aObjects__[i].getElementByName(sObject);
    if (oAux) {
      return oAux;
    }
  }
  return oAux;
};

var saveAndRefreshForm = function(oObject) {
  if (oObject) {
    oObject.form.action += '&_REFRESH_=1';
    oObject.form.submit();
  }
  else {
    var oAux = window.document.getElementsByTagName('form');
    if (oAux.length > 0) {
      oAux[0].action += '&_REFRESH_=1';
      oAux[0].submit();
    }
  }
};

var removeRequiredById = function(campo)
{ //the answer
	var tam = notValidateThisFields.length;		
	notValidateThisFields[tam+1] = campo;
	return;	
};

var enableRequiredById = function(campo)
{ var aAux = new Array();
	for(j=0; j < notValidateThisFields.length; j++)
		{ 
			if(notValidateThisFields[j]!=campo)
				 aAux[j] = notValidateThisFields[j];		
		}
		
	notValidateThisFields = aAux;	
}


