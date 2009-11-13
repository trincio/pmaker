var bFunctionAlreadyAssigned = false;
var aTaskFlag = [false,false,false,false,false,false];
var iLastTab   = -1;
var oTaskData  = {};

var re = /&/g;
var re2 = /@amp@/g;

var saveDataTaskTemporal = function(iForm)
{
  oAux = getField('TAS_UID');
  
  if (oAux)
  {
    switch (iLastTab)
    {
      case 1:
      case '1':
        oTaskData.TAS_TITLE       = getField('TAS_TITLE').value.replace(re, "@amp@");
        oTaskData.TAS_DESCRIPTION = getField('TAS_DESCRIPTION').value.replace(re, "@amp@");
        oTaskData.TAS_START       = (getField('TAS_START').checked ? 'TRUE' : 'FALSE');
        oTaskData.TAS_PRIORITY_VARIABLE = getField('TAS_PRIORITY_VARIABLE').value;
      break;
      case 2:
      case '2':
        if (getField('TAS_ASSIGN_TYPE][BALANCED').checked)
        {
          oTaskData.TAS_ASSIGN_TYPE = 'BALANCED';
        }
        if (getField('TAS_ASSIGN_TYPE][MANUAL').checked)
        {
          oTaskData.TAS_ASSIGN_TYPE = 'MANUAL';
        }
        if (getField('TAS_ASSIGN_TYPE][EVALUATE').checked)
        {
          oTaskData.TAS_ASSIGN_TYPE = 'EVALUATE';
        }
        oTaskData.TAS_ASSIGN_VARIABLE = getField('TAS_ASSIGN_VARIABLE').value;
      break;
      case 3:
      case '3':
        oTaskData.TAS_DURATION     = getField('TAS_DURATION').value;
        oTaskData.TAS_TIMEUNIT     = getField('TAS_TIMEUNIT').value;
        oTaskData.TAS_TYPE_DAY     = getField('TAS_TYPE_DAY').value;
        //oTaskData.TAS_TRANSFER_FLY = (getField('TAS_TRANSFER_FLY').checked ? 'TRUE' : 'FALSE');
      break;
      case 4:
      case '4':
      break;
      case 5:
      case '5':
        /*oTaskData.TAS_CAN_CANCEL                    = (getField('TAS_CAN_CANCEL').checked ? 'TRUE' : 'FALSE');
        oTaskData.TAS_CAN_PAUSE                     = (getField('TAS_CAN_PAUSE').checked ? 'TRUE' : 'FALSE');*/
        oTaskData.TAS_TYPE                          = (getField('TAS_TYPE').checked ? 'ADHOC' : 'NORMAL');
        /*oTaskData.TAS_CAN_SEND_MESSAGE              = (getField('TAS_CAN_SEND_MESSAGE').checked ? 'TRUE' : 'FALSE');
        oTaskData.TAS_CAN_UPLOAD                    = (getField('TAS_CAN_UPLOAD').checked ? 'TRUE' : 'FALSE');
        oTaskData.TAS_VIEW_UPLOAD                   = (getField('TAS_VIEW_UPLOAD').checked ? 'TRUE' : 'FALSE');
        oTaskData.TAS_VIEW_ADDITIONAL_DOCUMENTATION = (getField('TAS_VIEW_ADDITIONAL_DOCUMENTATION').checked ? 'TRUE' : 'FALSE');
        oTaskData.TAS_CAN_DELETE_DOCS               = getField('TAS_CAN_DELETE_DOCS').value;*/
      break;
      case 6:
      case '6':
        oTaskData.TAS_DEF_TITLE       = getField('TAS_DEF_TITLE').value;
        oTaskData.TAS_DEF_DESCRIPTION = getField('TAS_DEF_DESCRIPTION').value;
        //oTaskData.TAS_DEF_PROC_CODE   = getField('TAS_DEF_PROC_CODE').value;
        //oTaskData.SEND_EMAIL          = (getField('SEND_EMAIL').checked ? 'TRUE' : 'FALSE');
        //oTaskData.TAS_DEF_MESSAGE     = getField('TAS_DEF_MESSAGE').value;
      break;
      case 7:
      case '7':
        oTaskData.SEND_EMAIL      = (getField('SEND_EMAIL').checked ? 'TRUE' : 'FALSE');
        oTaskData.TAS_DEF_MESSAGE = getField('TAS_DEF_MESSAGE').value;
      break;
    }
  }
  else
  {
    oTaskData = {};
  }
  iLastTab = iForm;
};

var saveTaskData = function(oForm, iForm, iType)
{
  iLastTab = iForm;
  saveDataTaskTemporal(iForm);
  oTaskData.TAS_UID = getField('TAS_UID').value;
  var sParameters = 'function=saveTaskData';
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : '../tasks/tasks_Ajax',
    async : false,
    method: 'POST',
    args  : sParameters + '&oData=' + oTaskData.toJSONString()
  });
  oRPC.make();
  if (oTaskData.TAS_TITLE)
  {
    Pm.data.db.task[getField('INDEX').value].label = Pm.data.db.task[getField('INDEX').value].object.elements.label.innerHTML = oTaskData.TAS_TITLE.replace(re2, "&amp;");
  }
  if (oTaskData.TAS_START)
  {
    oTaskData.TAS_START = (oTaskData.TAS_START == 'TRUE' ? true : false);
    Pm.data.render.setTaskINI({task: oTaskData.TAS_UID, value: oTaskData.TAS_START});
  }
  Pm.tmp.propertiesPanel.remove();
};

var showTriggers = function(sStep, sType)
{
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : '../steps/steps_Ajax',
    async : false,
    method: 'POST',
    args  : 'action=showTriggers&sProcess=' + Pm.options.uid + '&sStep=' + sStep + '&sType=' + sType
  });  
   
  oRPC.make();
  
  scs = oRPC.xmlhttp.responseText.extractScript();
  scs.evalScript();
  
  document.getElementById('triggersSpan_' + sStep + '_' + sType).innerHTML = oRPC.xmlhttp.responseText;

  var tri = document.getElementById('TRIG_'+sStep+'_'+sType);
  if (tri) {
  	oRPC = new leimnud.module.rpc.xmlhttp({
      url   : '../steps/steps_Ajax',
      async : false,
      method: 'POST',
      args  : 'action=counterTriggers&sStep='+sStep+'&sType='+sType
    });
    oRPC.make();
    aAux = oRPC.xmlhttp.responseText.split('|');
    tri.innerHTML=aAux[1];
    var tri = document.getElementById('TRIG_'+sStep);
    if (tri) {
    	tri.innerHTML=aAux[0];
    }
  }
};

var editTriggerCondition = function(sStep, sTrigger, sType)
{
  popupWindow('', '../steps/steps_Ajax?action=editTriggerCondition&sStep=' + sStep + '&sTrigger=' + sTrigger + '&sType=' + sType, 500, 220);
};

var upTrigger = function(sStep, sTrigger, sType, iPosition)
{
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : '../steps/steps_Ajax',
    async : false,
    method: 'POST',
    args  : 'action=upTrigger&sStep=' + sStep + '&sTrigger=' + sTrigger + '&sType=' + sType + '&iPosition=' + iPosition
  });
  oRPC.make();
  showTriggers(sStep, sType);
};

var downTrigger = function(sStep, sTrigger, sType, iPosition)
{
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : '../steps/steps_Ajax',
    async : false,
    method: 'POST',
    args  : 'action=downTrigger&sStep=' + sStep + '&sTrigger=' + sTrigger + '&sType=' + sType + '&iPosition=' + iPosition
  });
  oRPC.make();
  showTriggers(sStep, sType);
};

var availableTriggers = function(sStep, sType)
{
  popupWindow('', '../steps/steps_Ajax?action=availableTriggers&sProcess=' + Pm.options.uid + '&sStep=' + sStep + '&sType=' + sType, 500, 250);
};

var ofToAssignTrigger = function(sStep, sTrigger, sType, iPosition)
{
	new leimnud.module.app.confirm().make({
    label:G_STRINGS.ID_MSG_CONFIRM_REMOVE_TRIGGER,
    action:function()
    {
      var oRPC = new leimnud.module.rpc.xmlhttp({
        url   : '../steps/steps_Ajax',
        async : false,
        method: 'POST',
        args  : 'action=ofToAssignTrigger&sStep=' + sStep + '&sTrigger=' + sTrigger + '&sType=' + sType + '&iPosition=' + iPosition
      });
      oRPC.make();
      showTriggers(sStep, sType);
    }.extend(this)
  });
};