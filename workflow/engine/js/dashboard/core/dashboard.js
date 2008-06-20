var oPanel;
var newDashboard = function() {
  oPanel = new leimnud.module.panel();
  oPanel.options = {
  	size     : {w:500,h:150},
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
  	var scs = rpc.xmlhttp.responseText.extractScript();
  	oPanel.addContent(rpc.xmlhttp.responseText);
  	scs.evalScript();
  }.extend(this);
  oRPC.make();
};

var addDashboard = function(aDashboard) {
  oPanel.loader.show();
  var oRPC = new leimnud.module.rpc.xmlhttp({
  	url : 'dashboardAjax',
  	args: 'action=addDashboard&sDashboardClass=' + aDashboard[0] + '&sChart=' + aDashboard[1]
  });
  oRPC.callback = function(rpc){
  	oPanel.loader.hide();
  	oPanel.remove();
  }.extend(this);
  oRPC.make();
};