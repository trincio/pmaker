<?php
/**
 * customPage.php
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
if (($RBAC_Response=$RBAC->userCanAccess("PM_SETUP"))!=1) return $RBAC_Response;
	G::LoadInclude('ajax');

	$G_ENABLE_BLANK_SKIN = true;

$dbc = new DBConnection;
$ses = new DBSession($dbc);

$userid = $_SESSION['USER_LOGGED'];

function paint()
{
	$funcion=strtolower(get_ajax_value('function'));
	$funcions=get_defined_functions();
	if (!in_array($funcion,$funcions['user'])) $funcion='main';
	if (in_array($funcion,$funcions['user'])) eval($funcion.'();');
}
function activatePopup()
{
	global $ses,$userid;
	$id=explode(':',get_ajax_value('id'));
	$page=$id[1];
	$res=$ses->execute('update CUSTOMIZE_PAGES set STATUS="ACTIVE" where USER="'.$userid.'" and PAGE="'.$page.'"');
	echo G::LoadTranslation('ID_POPUP_ACTIVE');
}
function deactivatePopup()
{
	global $ses,$userid;
	$id=explode(':',get_ajax_value('id'));
	$page=$id[1];
	$res=$ses->execute('update CUSTOMIZE_PAGES set STATUS="INACTIVE" where USER="'.$userid.'" and PAGE="'.$page.'"');
	echo G::LoadTranslation('ID_POPUP_INACTIVE');
}
function main()
{
	global $G_PUBLISH,$ses,$userid;
	$template = new TemplatePower(PATH_CORE . 'templates/setup/customPage.html');
	$template->prepare();
	$template->newBlock('main');
	$query=$ses->execute('select * from CUSTOMIZE_PAGES where USER="'.$userid.'"');
	for($r=1;$r<=$query->count();$r++)
	{
		$result=$query->read();
		$template->newBlock('config');
		$template->assign('num',$r);
//		$template->assign('process',$result['PROCESS']);
//		$template->assign('task',$result['TASK']);
		$template->assign('idfield',$result['USER'] .':'. $result['PAGE']);
		if ($result['DESCRIPTION']=='') 
		switch (strtolower($result['PAGE']))
		{
		case 'welcomeadmin': $result['DESCRIPTION']='ID_WELCOME_ADMIN';break;
		case 'welcomeoperator': $result['DESCRIPTION']='ID_WELCOME_OPERATOR';break;
		}
		if ($result['DESCRIPTION']!='') 
			$template->assign('description',G::LoadXml('setupLabels',$result['DESCRIPTION']));
		else 
			$template->assign('description',$result['PAGE']);
		if ($result['STATUS']=='ACTIVE') $template->assign('status','checked=true');
		if ($result['STATUS']=='ACTIVE') $template->assign('tStatus',G::LoadTranslation('ID_POPUP_ACTIVE'));
		else $template->assign('tStatus',G::LoadTranslation('ID_POPUP_INACTIVE'));
		$cellcolor=(( isset ( $cellcolor ) ? $cellcolor : '' )  =='cellWhite')?'cellBlue':'cellWhite';
		$template->assign('cellcolor',$cellcolor);
	}
	
	$G_PUBLISH = new Publisher;
	$G_PUBLISH->SetTo( $dbc );
	$G_PUBLISH->AddContent( "view", "setup/tree_setupEnvironment" );
	$G_PUBLISH->AddContent('template', '', '', '', $template);
	$content = G::LoadContent( "empty" );
	G::RenderPage( $content, "publish-treeview" );
	?>
	<script language="JavaScript">
	var debug;
	debug = document.getElementById('debug');
	function checkbox_onchange(checkbox)
	{
		if (checkbox.checked) 
			checkbox.innerHTML=ajax_function('<?=G::encryptLink("customPage")?>','activatePopup','id='+checkbox.id);
		else
			checkbox.innerHTML=ajax_function('<?=G::encryptLink("customPage")?>','deactivatePopup','id='+checkbox.id);
	}
	function ajax_function(ajax_server, funcion, parameters)
	{
	    objetus = get_xmlhttp();    
	    var response;
	    try
	    {
	    	if (parameters) parameters = '&' + encodeURI(parameters);
	    	objetus.open("GET", ajax_server + "?function=" + funcion + parameters, false); 
	  	}catch(ss)
	  	{  	
	  		alert("error"+ss.message);
	  	}
	    objetus.send(null);
	    return objetus.responseText;
	}
	</script>
	<?php
}
paint();
?>