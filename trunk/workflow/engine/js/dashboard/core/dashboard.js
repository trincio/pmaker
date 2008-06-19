var newDashboard = function() {
  oPanel = new leimnud.module.panel();
  oPanel.options = {
  	size     : {w:400,h:350},
  	position : {x:0,y:0,center:true},
  	title    : '',
  	theme    : 'processmaker',
  	statusBar: true,
  	control  : {resize:false, roll:false},
  	fx       : {modal:true, opacity:true, blinkToFront:true, fadeIn:false}
  };
  oPanel.events = {
  	remove: function() { delete(oPanel); }.extend(this)
  };
  oPanel.make();
  oPanel.loader.show();
  var oRPC = new leimnud.module.rpc.xmlhttp({
  	url : 'dashboardAjax',
  	args: 'action=showAvailableDashboards'
  });
  oRPC.callback = function(rpc){
  	oPanel.loader.hide();
  	var scs=rpc.xmlhttp.responseText.extractScript();
  	oPanel.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};