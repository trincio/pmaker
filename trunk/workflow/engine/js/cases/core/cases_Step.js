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
  	title      : G_STRINGS.ID_INFORMATION,
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
   Cse.panels.step.elements.title.innerHTML = G_STRINGS.ID_INFORMATION;
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
  	  title      : G_STRINGS.ID_ACTIONS,
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
    Cse.panels.step.elements.title.innerHTML = G_STRINGS.ID_ACTIONS;
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
/*var showKT = function()
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
    	images_dir :leimnud.path_rofile:///rodimus.erik/processmaker/trunk/workflow/engine/js/cases/core/cases_Step.jsot + "cases/core/images/"
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
};*/
var showProcessMap = function ()
{
	oPanel = new leimnud.module.panel();
  oPanel.options = {
  	size	:{w:(document.body.clientWidth * 95) / 100,h:(document.body.clientHeight * 95) / 100},
  	position:{x:0,y:0,center:true},
  	title	:G_STRINGS.ID_PROCESS_MAP,
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
  	title	:G_STRINGS.ID_PROCESS_INFORMATION,
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
  	size	:{w:900,h:520},
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
  	title	:G_STRINGS.ID_TASK_INFORMATION,
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
var reactivateCase = function()
{
  new leimnud.module.app.confirm().make({
    label : G_STRINGS.ID_MSG_CONFIRM_REACTIVATE_CASES,
    action: function() {
      var oRPC = new leimnud.module.rpc.xmlhttp({
        url:  'cases_Ajax',
        args: 'action=reactivateCase'
      });
      oRPC.callback = function(oRPC) {
        window.location = 'cases_List';
      }.extend(this);
      oRPC.make();
    }.extend(this)
  });
};

var pausecasePanel;
var pauseCase = function() //we work here @erik
{
  /*new leimnud.module.app.confirm().make({
    label : G_STRINGS.ID_CONFIRM_PAUSE_CASE,
    action: function() {
      var oRPC = new leimnud.module.rpc.xmlhttp({
        url:  'cases_Ajax',
        args: 'action=pauseCase'
      });
      oRPC.callback = function(oRPC) {
        window.location = 'cases_List';
      }.extend(this);
      oRPC.make();
    }.extend(this)
  });*/

	oPanel2 = new leimnud.module.panel();
	pausecasePanel = oPanel2;
	oPanel2.options = {
		size    :{w:400,h:120},
		position:{x:0,y:0,center:true},
		title   :'',
		theme   :'processmaker',
		statusBar:true,
		control :{drag: false, resize:false,roll:false,close:true},
		fx  	:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}
	};
	oPanel2.events = {
		remove: function() { delete(oPanel2); }.extend(this)
	};
	oPanel2.make();
	oPanel2.loader.show();
	var oRPC = new leimnud.module.rpc.xmlhttp({
		url : 'cases_Ajax',
		args: 'action=showPauseCaseInput'
	});
	oRPC.callback = function(rpc){
		oPanel2.loader.hide();
		//var scs=rpc.xmlhttp.responseText.extractScript();
		oPanel2.addContent(rpc.xmlhttp.responseText);
		//scs.evalScript();
	}.extend(this);
	oRPC.make();
};

function pauseConfirm()
{
	unpausedate = document.getElementById('form[unpause_date]').value;
	new leimnud.module.app.confirm().make({
		label : G_STRINGS.ID_CONFIRM_PAUSE_CASE,
		action: function() {
			var oRPC = new leimnud.module.rpc.xmlhttp({
				url:  'cases_Ajax',
				args: 'action=pauseCase&unpausedate='+unpausedate
			});
			oRPC.callback = function(oRPC) {
				pausecasePanel.remove();
				window.location = 'cases_List';
			}.extend(this);
			oRPC.make();
		}.extend(this)
	});
}

var deleteCase = function(sApplicationUID)
{
  new leimnud.module.app.confirm().make({
    label : G_STRINGS.ID_CONFIRM_DELETE_CASE,
    action: function() {
      var oRPC = new leimnud.module.rpc.xmlhttp({
        url:  'cases_Ajax',
        args: 'action=deleteCase&sApplicationUID='+sApplicationUID
      });
      oRPC.callback = function(oRPC) {
        window.location = 'cases_List';
      }.extend(this);
      oRPC.make();
    }.extend(this)
  });
};

var unpauseCase = function()
{
  new leimnud.module.app.confirm().make({
    label : G_STRINGS.ID_CONFIRM_UNPAUSE_CASE,
    action: function() {
      var oRPC = new leimnud.module.rpc.xmlhttp({
        url:  'cases_Ajax',
        args: 'action=unpauseCase'
      });
      oRPC.callback = function(oRPC) {
        window.location = 'cases_List';
      }.extend(this);
      oRPC.make();
    }.extend(this)
  });
};

var view_reassignCase = function()
{
	 var panel =new leimnud.module.panel();
						panel.options={
							size	:{w:450,h:450},
							position:{x:50,y:50,center:true},
							/*statusBarButtons:[
								{value:G_STRINGS.ID_DELETE},
								{value:G_STRINGS.CANCEL}
							],*/
							title	:'',
							control	:{close:true,resize:false},fx:{modal:true},
							statusBar:false,
							fx	:{shadow:true,modal:true}
						};
						panel.make();
						/*panel.elements.statusBarButtons[0].onmouseup=function()
						{
							window.location="processes_Delete.php?PRO_UID="+uid;
						};
						panel.elements.statusBarButtons[1].onmouseup=panel.remove;*/
						panel.loader.show();
						var r = new leimnud.module.rpc.xmlhttp({
							url:"cases_Ajax",
							args:"action=view_reassignCase"
						});
						r.callback=function(rpc)
						{
							panel.loader.hide();
							panel.addContent(rpc.xmlhttp.responseText);
							var scs=rpc.xmlhttp.responseText.extractScript();
  	          scs.evalScript();
						};
						r.make();

};

var reassignCase = function(USR_UID, THETYPE)
{
  var oRPC = new leimnud.module.rpc.xmlhttp({
        url:  'cases_Ajax',
        args: 'action=reassignCase'+'&USR_UID='+USR_UID + '&THETYPE=' + THETYPE
      });

      oRPC.callback = function(oRPC) {
        window.location = 'cases_List';
      }.extend(this);
      oRPC.make();
};

var adhocAssignmentUsers = function () {
  oPanel = new leimnud.module.panel();
  oPanel.options = {
  	size	:{w:450,h:450},
  	position:{x:0,y:0,center:true},
  	title	:'',
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
  	url : 'cases_Ajax',
  	args: 'action=adhocAssignmentUsers'
  });
  oRPC.callback = function(rpc){
  	oPanel.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanel.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};

var showUploadedDocuments = function()
{
  oPanel = new leimnud.module.panel();
  oPanel.options = {
  	size	:{w:700,h:350},
  	position:{x:0,y:0,center:true},
  	title	:G_STRINGS.ID_UPLOADED_DOCUMENTS,
  	theme	:'processmaker',
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
  	url : 'cases_Ajax',
  	args: 'action=showUploadedDocuments'
  });
  oRPC.callback = function(rpc){
  	oPanel.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanel.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};

var showUploadedDocument = function(APP_DOC_UID) {
  oPanel2 = new leimnud.module.panel();
  oPanel2.options = {
  	size	:{w:300,h:300},
  	position:{x:0,y:0,center:true},
  	title	:'',
  	theme	:'processmaker',
  	statusBar:true,
  	control	:{resize:false,roll:false},
  	fx	:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}
  };
  oPanel2.events = {
  	remove: function() { delete(oPanel2); }.extend(this)
  };
  oPanel2.make();
  oPanel2.loader.show();
  var oRPC = new leimnud.module.rpc.xmlhttp({
  	url : 'cases_Ajax',
  	args: 'action=showUploadedDocument&APP_DOC_UID=' + APP_DOC_UID
  });
  oRPC.callback = function(rpc){
  	oPanel2.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanel2.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};

var showGeneratedDocuments = function()
{
  oPanel = new leimnud.module.panel();
  oPanel.options = {
  	size	:{w:600,h:350},
  	position:{x:0,y:0,center:true},
  	title	:G_STRINGS.ID_GENERATED_DOCUMENTS,
  	theme	:'processmaker',
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
  	url : 'cases_Ajax',
  	args: 'action=showGeneratedDocuments'
  });
  oRPC.callback = function(rpc){
  	oPanel.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanel.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};

var showGeneratedDocument = function(APP_DOC_UID) {
  oPanel2 = new leimnud.module.panel();
  oPanel2.options = {
  	size	:{w:300,h:250},
  	position:{x:0,y:0,center:true},
  	title	:'',
  	theme	:'processmaker',
  	statusBar:true,
  	control	:{resize:false,roll:false},
  	fx	:{modal:true,opacity:true,blinkToFront:true,fadeIn:false}
  };
  oPanel2.events = {
  	remove: function() { delete(oPanel2); }.extend(this)
  };
  oPanel2.make();
  oPanel2.loader.show();
  var oRPC = new leimnud.module.rpc.xmlhttp({
  	url : 'cases_Ajax',
  	args: 'action=showGeneratedDocument&APP_DOC_UID=' + APP_DOC_UID
  });
  oRPC.callback = function(rpc){
  	oPanel2.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanel2.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};

var showDynaforms = function() {

  oPanel2 = new leimnud.module.panel();
  oPanel2.options = {
    size    :{w:400,h:300},
    position:{x:0,y:0,center:true},
    title   :G_STRINGS.ID_DYNAFORMS,
    theme   :'processmaker',
    statusBar:true,
    control :{resize:false,roll:false},
    fx  :{modal:true,opacity:true,blinkToFront:true,fadeIn:false}
  };
  oPanel2.events = {
    remove: function() { delete(oPanel2); }.extend(this)
  };
  oPanel2.make();
  oPanel2.loader.show();
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url : 'cases_Ajax',
    args: 'action=showDynaformList'
  });
  oRPC.callback = function(rpc){
    oPanel2.loader.hide();
    var scs=rpc.xmlhttp.responseText.extractScript();
    oPanel2.addContent(rpc.xmlhttp.responseText);
    scs.evalScript();
  }.extend(this);
  oRPC.make();
  };

  function showDynaform(DYN_UID)
  {
    oPanel2 = new leimnud.module.panel();
    oPanel2.options = {
        size    :{w:550,h:400},
        position:{x:0,y:0,center:true},
        title   :'',
        theme   :'processmaker',
        statusBar:true,
        control :{resize:false,roll:false},
        fx  :{modal:true,opacity:true,blinkToFront:true,fadeIn:false}
    };
    oPanel2.events = {
        remove: function() { delete(oPanel2); }.extend(this)
    };
    oPanel2.make();
    oPanel2.loader.show();
    var oRPC = new leimnud.module.rpc.xmlhttp({
        url : 'cases_Ajax',
        args: 'action=showDynaform&DYN_UID='+DYN_UID
    });
    oRPC.callback = function(rpc){
        oPanel2.loader.hide();
        var scs=rpc.xmlhttp.responseText.extractScript();
        oPanel2.addContent(rpc.xmlhttp.responseText);
        scs.evalScript();
    }.extend(this);
    oRPC.make();
  }

var messagesListPanel;
var showHistoryMessages = function()
 {
  oPanel = new leimnud.module.panel();
  oPanel.options = {
  	size	:{w:800,h:420},
  	position:{x:0,y:0,center:true},
  	title	:G_STRINGS.ID_HISTORY_MESSAGE_CASE,
  	theme	:'processmaker',
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
  	url : 'cases_Ajax',
  	args: 'action=showHistoryMessages'
  });
  oRPC.callback = function(rpc){
  	oPanel.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanel.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
  messagesListPanel = oPanel;
};

function showHistoryMessage(APP_UID, APP_MSG_UID)
  {
    oPanel2 = new leimnud.module.panel();
    oPanel2.options = {
        size    :{w:600,h:400},
        position:{x:0,y:0,center:true},
        title   :'',
        theme   :'processmaker',
        statusBar:true,
        control :{resize:false,roll:false},
        fx  :{modal:true,opacity:true,blinkToFront:true,fadeIn:false}
    };
    oPanel2.events = {
        remove: function() { delete(oPanel2); }.extend(this)
    };
    oPanel2.make();
    oPanel2.loader.show();
    var oRPC = new leimnud.module.rpc.xmlhttp({
        url : 'cases_Ajax',
        args: 'action=showHistoryMessage&APP_UID='+APP_UID+'&APP_MSG_UID='+APP_MSG_UID
    });
    oRPC.callback = function(rpc){
        oPanel2.loader.hide();
        var scs=rpc.xmlhttp.responseText.extractScript();
        oPanel2.addContent(rpc.xmlhttp.responseText);
        scs.evalScript();
    }.extend(this);
    oRPC.make();
  }

var deleteUploadedDocument = function(APP_DOC_UID) {
  new leimnud.module.app.confirm().make({
    label : G_STRINGS.ID_MSG_CONFIRM_DELETE_FILE,
    action: function() {
      var oRPC = new leimnud.module.rpc.xmlhttp({
        url:  'cases_Ajax',
        args: 'action=deleteUploadedDocument&DOC=' + APP_DOC_UID
      });
      oRPC.callback = function(oRPC) {
        if ((window.location.href.indexOf('/cases/cases_Step') > -1) &&
            (window.location.href.indexOf('?TYPE=INPUT_DOCUMENT&UID=') > -1) &&
            (window.location.href.indexOf('&ACTION=VIEW&') > -1) &&
            (window.location.href.indexOf('&DOC=' + APP_DOC_UID) > -1)) {
          window.location = getField('DYN_FORWARD');
        }
        else {
          cases_AllInputdocsList.refresh();
        }
      }.extend(this);
      oRPC.make();
    }.extend(this)
  });
};

var deleteGeneratedDocument = function(APP_DOC_UID) {
  new leimnud.module.app.confirm().make({
    label : G_STRINGS.ID_MSG_CONFIRM_DELETE_FILE,
    action: function() {
      var oRPC = new leimnud.module.rpc.xmlhttp({
        url:  'cases_Ajax',
        args: 'action=deleteGeneratedDocument&DOC=' + APP_DOC_UID
      });
      oRPC.callback = function(oRPC) {
        if ((window.location.href.indexOf('/cases/cases_Step') > -1) &&
            (window.location.href.indexOf('?TYPE=OUTPUT_DOCUMENT&UID=') > -1) &&
            (window.location.href.indexOf('&ACTION=VIEW&') > -1) &&
            (window.location.href.indexOf('&DOC=' + APP_DOC_UID) > -1)) {
          window.location = getField('DYN_FORWARD');
        }
        else {
          cases_AllOutputdocsList.refresh();
        }
      }.extend(this);
      oRPC.make();
    }.extend(this)
  });
};

/**
 * Resend the message that was sent.
 * 
 * @Param Application ID
 * @Param Message ID
 * @Author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
 */
var resendMessage = function(APP_UID, APP_MSG_UID)
{
	new leimnud.module.app.confirm().make({
	    label : G_STRINGS.ID_MSG_CONFIRM_RESENDMSG,
	    action: function() {
		  var oRPC = new leimnud.module.rpc.xmlhttp({
		      url : 'cases_Ajax',
		      args: 'action=resendMessage&APP_UID='+APP_UID+'&APP_MSG_UID='+APP_MSG_UID
		  });
		  oRPC.callback = function(rpc){
		      //var scs=rpc.xmlhttp.responseText.extractScript();
		      //alert(rpc.xmlhttp.responseText);
		      //scs.evalScript();
			  messagesListPanel.clearContent();
			  messagesListPanel.loader.show();
			  var oRPC2 = new leimnud.module.rpc.xmlhttp({
			  	url : 'cases_Ajax',
			  	args: 'action=showHistoryMessages'
			  });
			  oRPC2.callback = function(rpc){
				messagesListPanel.loader.hide();
			  	var scs=rpc.xmlhttp.responseText.extractScript();
			  	messagesListPanel.addContent(rpc.xmlhttp.responseText);
			  	scs.evalScript();
			  }.extend(this);
			  oRPC2.make();
			  
		  }.extend(this);
		  oRPC.make();
	    }.extend(this)
	});
};