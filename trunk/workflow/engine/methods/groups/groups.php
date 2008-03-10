<?php
/**
 * groups.php
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
if (($RBAC_Response=$RBAC->userCanAccess("PM_USERS"))!=1) return $RBAC_Response;

  //G::genericForceLogin( 'WF_MYINFO' , 'login/noViewPage', $urlLogin = 'login/login' );

//  G::LoadClass('user');
//  G::LoadClass('group');
//  G::LoadClass('groupUser');
//  G::LoadClass('tree');

  $G_MAIN_MENU            = 'processmaker';
  $G_SUB_MENU             = 'users';
  $G_ID_MENU_SELECTED     = 'USERS';
  $G_ID_SUB_MENU_SELECTED = 'GROUPS';

  $G_HEADER->addScriptFile('/js/common/tree/tree.js');
  $G_HEADER->addInstanceModule('leimnud','rpc');

  $dbc = new DBConnection();
  $ses = new DBSession($dbc);

  /*$group = new Group( $dbc );
  $group->Fields['UID']='0';*/
  $Fields['WHERE'] = '';

  $G_PUBLISH = new Publisher;

  $G_PUBLISH->AddContent('view', 'groups/groups_Tree' );
  $G_PUBLISH->AddContent('pagedtable', 'paged-table', 'groups/groups_UsersList', '', $Fields , 'groups_Save');

  G::RenderPage( "publish-treeview" );

  $groups_Edit = G::encryptlink('groups_Edit');
  $groups_Delete = G::encryptlink('groups_Delete');
  $groups_List = G::encryptlink('groups_List');
  $groups_AddUser = G::encryptlink('groups_AddUser');
?>
<SCRIPT>
  document.getElementById('pagedtable['+currentPagedTable.id+']').style.visibility='hidden';
  var currentGroup=false;
  function editGroup( uid ) {
    popupWindow('' , '<?=$groups_Edit?>?UID=' + encodeURIComponent( uid )+'&nobug' , 500 , 200 );
    refreshTree();
  }
  function addGroup(){
    popupWindow('' , '<?=$groups_Edit?>' , 500 , 200 );
    refreshTree();
  }
  function addUserGroup( uid ){
    popupWindow('' , '<?=$groups_AddUser?>?UID='+uid+'&nobug' , 500 , 170 );
  }
  function saveGroup( form ) {
    ajax_post( form.action, form, 'POST' );
    currentPopupWindow.remove();
    refreshTree();
  }
  function selectGroup( uid , element ){
    var field,form;
    field = getField('CUR_GRP_UID');
    field.value=uid;
    form = field.form;
    currentPagedTable.doFilter( form );
    currentGroup = uid;
    tree.select( element );
    document.getElementById('pagedtable['+currentPagedTable.id+']').style.visibility='';
  }
  function deleteGroup( uid ){
    new leimnud.module.app.confirm().make({
    	label:"<?=G::LoadTranslation('ID_MSG_CONFIRM_DELETE_GROUP')?>",
    	action:function()
    	{
    		ajax_function('<?=$groups_Delete?>', 'asd', 'GRP_UID='+uid, "POST" );
        refreshTree();
        document.getElementById('pagedtable['+currentPagedTable.id+']').style.visibility='hidden';
    	}.extend(this)
    });
  }
  function refreshTree(){
    tree.refresh( document.getElementById("publisherContent[0]") , '<?=$groups_List?>');
  }

  var ofToAssignUser = function(sGroup, sUser)
  {
  	new leimnud.module.app.confirm().make({
    	label:"<?=G::LoadTranslation('ID_MSG_CONFIRM_REMOVE_USER')?>",
    	action:function()
    	{
        var oRPC = new leimnud.module.rpc.xmlhttp({
          url   : '../groups/groups_Ajax',
          async : false,
          method: 'POST',
          args  : 'action=ofToAssignUser&GRP_UID=' + sGroup + '&USR_UID=' + sUser
        });
        oRPC.make();
        var oAux, oForm;
        oAux = getField('CUR_GRP_UID');
        oAux.value = sGroup;
        oForm = oAux.form;
        currentPagedTable.doFilter(oForm);
        currentGroup = sGroup;
        document.getElementById('pagedtable[' + currentPagedTable.id + ']').style.visibility = '';
      }.extend(this)
    });
  };
</SCRIPT>
