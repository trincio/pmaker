var oLeyendsPanel;

var showInformation = function()
{
  if (!Cse.panels.step)
 {
   Cse=new cases();
   Cse.options = {
  	target     : "cases_target",
  	dataServer : "cases_Ajax",
  	action     : "information",
  	title      : "Information",
  	lang       : "en",
  	theme      : "processmaker",
  	images_dir :leimnud.path_root + "cases/core/images/"
  }
  Cse.make();
 }
 else
 {
   Cse.panels.step.events.remove[1]=function()
   {
   	var r = new leimnud.module.rpc.xmlhttp({
		url:"cases_Ajax",
		args:"showWindow=false"
	});
	r.make();
   };
   Cse.panels.step.elements.title.innerHTML = "Information";
   Cse.panels.step.clearContent();
   Cse.panels.step.loader.show();
   var oRPC = new leimnud.module.rpc.xmlhttp({
     url:  "cases_Ajax",
	   args: "action=information&showWindow=information"
   });
   oRPC.callback = function(rpc){
     Cse.panels.step.loader.hide();
	   var scs=rpc.xmlhttp.responseText.extractScript();
	   Cse.panels.step.addContent(rpc.xmlhttp.responseText);
	   scs.evalScript();
   }.extend(this);
   oRPC.make();
 }
};
var showActions = function()
{
  if (!Cse.panels.step)
  {
    Cse=new cases();
    Cse.options = {
  	  target     : "cases_target",
  	  dataServer : "cases_Ajax",
  	  action     : "actions",
  	  title      : "Actions",
  	  lang       : "en",
  	  theme      : "processmaker",
  	  images_dir :leimnud.path_root + "cases/core/images/"
    }
    Cse.make();
  }
  else
  {
   Cse.panels.step.events.remove[1]=function()
   {
   	var r = new leimnud.module.rpc.xmlhttp({
		url	:"casesAjax",
		args	:"showWindow=false"
	});
	r.make();
   };
    Cse.panels.step.elements.title.innerHTML = "Actions";
    Cse.panels.step.clearContent();
    Cse.panels.step.loader.show();
    var oRPC = new leimnud.module.rpc.xmlhttp({
      url:  "cases_Ajax",
	    args: "action=actions&showWindow=actions"
    });
    oRPC.callback = function(rpc){
      Cse.panels.step.loader.hide();
	   var scs=rpc.xmlhttp.responseText.extractScript();
	   Cse.panels.step.addContent(rpc.xmlhttp.responseText);
	   scs.evalScript();
    }.extend(this);
    oRPC.make();
  }
};
var showKT = function()
{
  if (!Cse.panels.step)
  {
    Cse=new cases();
    Cse.options = {
    	target     : "cases_target",
    	dataServer : "cases_Ajax",
    	action     : "KT",
    	title      : "Knowledge Tree",
    	lang       : "en",
    	theme      : "processmaker",
    	images_dir :leimnud.path_root + "cases/core/images/"
    }
    Cse.make();
  }
  else
  {
    Cse.panels.step.elements.title.innerHTML = "Knowledge Tree";
    Cse.panels.step.clearContent();
    Cse.panels.step.loader.show();
    var oRPC = new leimnud.module.rpc.xmlhttp({
      url:  "cases_Ajax",
	    args: "action=KT"
    });
    oRPC.callback = function(rpc){
      Cse.panels.step.loader.hide();
	    var scs=rpc.xmlhttp.responseText.extractScript();
	    Cse.panels.step.addContent(rpc.xmlhttp.responseText);
	    scs.evalScript();
    }.extend(this);
    oRPC.make();
  }
};
var showProcessMap = function ()
{
	oPanel = new leimnud.module.panel();
  oPanel.options = {
  	size	:{w:(document.body.clientWidth * 95) / 100,h:(document.body.clientHeight * 95) / 100},
  	position:{x:0,y:0,center:true},
  	title	:"Process Map",
  	theme	:"processmaker",
  	statusBar:false,
  	control	:{resize:false,roll:false,drag:false},
  	fx	:{modal:true,opacity:true,blinkToFront:false,fadeIn:false,drag:false}
  };
  oPanel.events = {
  	remove: function() { oLeyendsPanel.remove();delete(oPanel); }.extend(this)
  };
  oPanel.make();
  oPanel.loader.show();
  var oRPC = new leimnud.module.rpc.xmlhttp({
  	url : "cases_Ajax",
  	args: "action=showProcessMap"
  });
  oRPC.callback = function(rpc){
  	oPanel.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanel.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
    oLeyendsPanel = new leimnud.module.panel();
    oLeyendsPanel.options = {
    	size	:{w:160,h:120},
    	position:{x:((document.body.clientWidth * 95) / 100) - ((document.body.clientWidth * 95) / 100 - (((document.body.clientWidth * 95) / 100) - 160)),y:45,center:false},
    	title	:G_STRINGS.ID_COLOR_LEYENDS,
    	theme	:"processmaker",
    	statusBar:false,
    	control	:{resize:false,roll:false,drag:true,close:false},
    	fx	:{modal:false,opacity:false,blinkToFront:true,fadeIn:false,drag:false}
    };
    oLeyendsPanel.setStyle = {
    	content:{overflow:'hidden'}
    };
    oLeyendsPanel.events = {
    	remove: function() { delete(oLeyendsPanel); }.extend(this)
    };
    oLeyendsPanel.make();
    oLeyendsPanel.loader.show();
    var oRPC = new leimnud.module.rpc.xmlhttp({
    	url : "cases_Ajax",
    	args: "action=showLeyends"
    });
    oRPC.callback = function(rpc){
    	oLeyendsPanel.loader.hide();
    	var scs=rpc.xmlhttp.responseText.extractScript();
    	oLeyendsPanel.addContent(rpc.xmlhttp.responseText);
    }.extend(this);
    oRPC.make();
  }.extend(this);
  oRPC.make();
};
var showProcessInformation = function()
{
  oPanel = new leimnud.module.panel();
  oPanel.options = {
  	size	:{w:450,h:260},
  	position:{x:0,y:0,center:true},
  	title	:"Process Information",
  	theme	:"processmaker",
  	statusBar:false,
  	control	:{resize:false,roll:false},
  	fx	:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}
  };
  oPanel.events = {
  	remove: function() { delete(oPanel); }.extend(this)
  };
  oPanel.make();
  oPanel.loader.show();
  var oRPC = new leimnud.module.rpc.xmlhttp({
  	url : "cases_Ajax",
  	args: "action=showProcessInformation"
  });
  oRPC.callback = function(rpc){
  	oPanel.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanel.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};
var showTransferHistory = function()
{
  oPanel = new leimnud.module.panel();
  oPanel.options = {
  	size	:{w:650,h:525},
  	position:{x:0,y:0,center:true},
  	title	:G_STRINGS.ID_CASE_HISTORY,
  	theme	:"processmaker",
  	statusBar:false,
  	control	:{resize:false,roll:false},
  	fx	:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}
  };
  oPanel.events = {
  	remove: function() { delete(oPanel); }.extend(this)
  };
  oPanel.make();
  oPanel.loader.show();
  var oRPC = new leimnud.module.rpc.xmlhttp({
  	url : "cases_Ajax",
  	args: "action=showTransferHistory"
  });
  oRPC.callback = function(rpc){
  	oPanel.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanel.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};
var showTaskInformation = function()
{
  oPanel = new leimnud.module.panel();
  oPanel.options = {
  	size	:{w:450,h:322},
  	position:{x:0,y:0,center:true},
  	title	:"Task Information",
  	theme	:"processmaker",
  	statusBar:true,
  	control	:{resize:false,roll:false},
  	fx	:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}
  };
  oPanel.events = {
  	remove: function() { delete(oPanel); }.extend(this)
  };
  oPanel.make();
  oPanel.loader.show();
  var oRPC = new leimnud.module.rpc.xmlhttp({
  	url : "cases_Ajax",
  	args: "action=showTaskInformation"
  });
  oRPC.callback = function(rpc){
  	oPanel.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanel.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};
var cancelCase = function()
{
  new leimnud.module.app.confirm().make({
    label : G_STRINGS.ID_CONFIRM_CANCEL_CASE,
    action: function() {
      var oRPC = new leimnud.module.rpc.xmlhttp({
        url:  'cases_Ajax',
        args: 'action=cancelCase'
      });
      oRPC.callback = function(oRPC) {
        window.location = 'cases_List';
      }.extend(this);
      oRPC.make();
    }.extend(this)
  });
};
