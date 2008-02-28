<?php
  G::GenericForceLogin ('RBAC_LOGIN','login/noViewPage','login/login');

  $G_MAIN_MENU     = 'rbac';
  $G_SUB_MENU      = 'rbac.userEdit';
  $G_MENU_SELECTED = 0;
  
  $dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME);
  $ses = new DBSession($dbc);
  
  $dset    = $ses->Execute('SELECT UID, USR_USE_LDAP FROM USERS where UID = ' . $_SESSION['CURRENT_USER']);
  $row     = $dset->Read();
  $useLdap = $row['USR_USE_LDAP'] == 'Y';
  $access  = $RBAC->userCanAccess ('RBAC_CREATE_USERS');
  
  G::LoadClassRBAC('user');
  $obj = new RBAC_User;
  $obj->SetTo($dbc);
  $obj->Load($_SESSION['CURRENT_USER']);
  
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->SetTo ($dbc);
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'rbac/userEdit',      '', $obj->Fields, 'userEdit2');
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'rbac/userChangePwd', '', $obj->Fields, 'userChangePwd2');
  $G_PUBLISH->AddContent('xmlform', 'pagedTable', 'rbac/usersRolesList', '', array('CURRENT_USER' => $_SESSION['CURRENT_USER']), '');
  
  G::RenderPage('publish');
?>
<script language='Javascript'>

	function go () {

    myPanel=new leimnud.module.panel();
    	myPanel.options={
    		size:{w:620,h:300},
    		position:{center:true},
    		title:"Assign Role",		
    		control:{
    			close	:true,
    			roll	:true,
    			drag	:true,
    			resize	:true
    		},
    		fx:{
    			//shadow	:true,
    			blinkToFront:false,
    			modal	:true
    		},
    		theme:"simple"
    	};

    myPanel.make();
    
    myPanel.addContent("<iframe width='580' height='250' frameborder='0' src='userAssignRole'></iframe>");
		
	}

  function commonDialog ( type, title , text, buttons, values, callbackFn ) {
    myDialog = new leimnud.module.panel();
    myDialog.options={
    	size:{w:400,h:200}, position:{center:true},
    	title: title,		
    	control:{
    		close	:false,
    		roll	:false,
    		drag	:true,
    		resize	:false
    	},
    	fx:{
    		//shadow	:true,
    		blinkToFront:false,
    	  opacity	:true,
    	  modal: true
    	},
    	theme:"panel"
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

	function removeRoleCallback ( dialogResult ) {
		if ( dialogResult == '4' ) {
      window.location = 'userRoleDel?r='+ myDialog.values.role;
    }
  }                    
	
	function removeRole ( application, role ) {
    commonDialog ( 'question', 'Remove Role', 'Do you want to remove this role?', 
                   { custom:true, cancel: true , customText:'Delete' },
                   { application:application, role:role },
                   removeRoleCallback
                  );
	  
  } 
	  
</script>