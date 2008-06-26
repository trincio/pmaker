<?php
/**
 * control.php
 *  
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd., 
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 * 
 */

if (($RBAC_Response=$RBAC->userCanAccess("PM_FACTORY"))!=1) return $RBAC_Response;

  $G_MAIN_MENU            = 'processmaker';
  $G_SUB_MENU             = 'setup';
  $G_ID_MENU_SELECTED     = 'SETUP';
  $G_ID_SUB_MENU_SELECTED = 'WEBSERVICES';

  $G_HEADER->addInstanceModule('leimnud','rpc');

  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('view', 'setup/webServicesTree' );
  $G_PUBLISH->AddContent('smarty', 'groups/groups_usersList', '', '', array());
  $G_HEADER->addScriptFile('/js/form/core/pagedTable.js');

  G::RenderPage( "publish-treeview" );

  $link_Edit = G::encryptlink('webServicesSetup');
  $link_List = G::encryptlink('webServicesList');
?>
<script>
  var oAux = document.getElementById("publisherContent[0]");
  oAux.id = "publisherContent[666]";
  var currentGroup=false;
  function webServicesSetup(){
    popupWindow('' , '<?=$link_Edit?>' , 500 , 300 );
//    refreshTree();
  }
  function showFormWS( uid, element ){
    currentGroup = uid;
    var oRPC = new leimnud.module.rpc.xmlhttp({
      url   : '../setup/webServicesAjax',
      async : false,
      method: 'POST',
      args  : 'action=showForm&wsID=' + uid
    });
    oRPC.make();
    //var scs=oRPC.xmlhttp.responseText.extractScript();
    //scs.evalScript();
    document.getElementById('spanUsersList').innerHTML = oRPC.xmlhttp.responseText;
  }
  function execWebService( uid) {
    var oRPC = new leimnud.module.rpc.xmlhttp({
      url   : '../setup/webServicesAjax',
      async : false,
      method: 'POST',
      args  : 'action=execWebService&wsID=' + uid
    });
    oRPC.make();
    document.getElementById('spanUsersList').innerHTML = oRPC.xmlhttp.responseText;
  }
  
  submitThisForm = function(oForm) {
	  var oAux;
	  var bContinue = true;
	  if (bContinue) {
		  result = ajax_post(oForm.action, oForm, 'POST');
		  document.getElementById('spanUsersList').innerHTML = result;
		  //alert ( oForm.action );
      refreshTree();
	  }
};


  function callbackWebService( ) {
/*
    var oRPC = new leimnud.module.rpc.xmlhttp({
      url   : '../setup/webServicesAjax',
      async : false,
      method: 'POST',
      args  : 'action=execWebService&wsID=' + uid
    });
    oRPC.make();
    document.getElementById('spanUsersList').innerHTML = oRPC.xmlhttp.responseText;
*/
    document.getElementById('spanUsersList').innerHTML = 'hola';
  }
  function saveGroup( form ) {
    ajax_post( form.action, form, 'POST' );
    currentPopupWindow.remove();
    refreshTree();
  }
  function refreshTree(){
    tree.refresh( document.getElementById("publisherContent[666]") , '<?=$link_List?>');
  }

</script>
