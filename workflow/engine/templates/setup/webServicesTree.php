<?php
/**
 * groups_Tree.php
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

  //G::genericForceLogin( 'WF_MYINFO' , 'login/noViewPage', $urlLogin = 'login/login' );

  G::LoadClass('tree');

	$wsSessionId = '';
	if ( isset ( $_SESSION['WS_SESSION_ID'] ) ) {
		$wsSessionId = $_SESSION['WS_SESSION_ID'];
	};
/*
  $defaultEndpoint = 'http://' .$_SERVER['SERVER_NAME'] . ':' .$_SERVER['SERVER_PORT'] .
              '/sys' .SYS_SYS.'/en/green/services/wsdl';

      $endpoint = isset( $_SESSION['END_POINT'] ) ? $_SESSION['END_POINT'] : $defaultEndpoint;
*/
	if(isset($_GET['x']))
	{
			if($_GET['x']==1)
					$wsdl = $_SESSION['END_POINT'];
			else
					$wsdl = '<font color="red">'.G::LoadTranslation('ID_WSDL').'</font>';
	}
	else
	{
	  if (!isset($_SESSION['END_POINT'])) {
		  //$wsdl = 'http://'.$_SERVER['HTTP_HOST'].'/sys'.SYS_SYS.'/en/green/services/wsdl';
		  $wsdl = 'http://'.$_SERVER['HTTP_HOST'];
		  $workspace = SYS_SYS;
		}
		else {
		  $wsdl = $_SESSION['END_POINT'];
		}
	}

  $tree = new Tree();
  $tree->name = 'WebServices';
  $tree->nodeType="base";
  $tree->width="270px";
  $tree->value = '
	 <div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	 <div class="boxContentBlue">

	  <table width="100%" style="margin:0px;" cellspacing="0" cellpadding="0">
	  <tr>
		  <td class="userGroupTitle">'.G::loadTranslation("ID_WEB_SERVICES").'</td>
	  </tr>
	</table>
	</div>
	<div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
  	<div class="userGroupLink"><a href="#" onclick="webServicesSetup();return false;">'.G::LoadTranslation('ID_SETUP_WEBSERVICES').'</a></div>
    <div class="boxContentBlue"><b>' . G::LoadTranslation('ID_SESSION') . ': </b><span id="spanWsSessionId">' . $wsSessionId .'</span></div><br>
    <div class="boxContentBlue">
    	<b>' . G::LoadTranslation('ID_SITE') . ': </b><span id="spanWsSessionId">' . $wsdl .'</span>
    	<b>' . G::LoadTranslation('ID_WORKSPACE') . ': </b><span id="spanWsSessionId">' . $workspace .'</span>
    </div><br>
	';

  $tree->showSign=false;

  $allWebservices = array();
  $allWebservices[] = 'Login';
  $allWebservices[] = 'CreateUser';
  $allWebservices[] = 'AssignUserToGroup';
  $allWebservices[] = 'NewCase';
  $allWebservices[] = 'NewCaseImpersonate';
  $allWebservices[] = 'DerivateCase';
  $allWebservices[] = 'SendVariables';
  $allWebservices[] = 'SendMessage';
  $allWebservices[] = 'ProcessList';
  $allWebservices[] = 'CaseList';
  $allWebservices[] = 'RoleList';
  $allWebservices[] = 'GroupList';
  $allWebservices[] = 'UserList';
  $allWebservices[] = 'TaskList';
  $allWebservices[] = 'TaskCase';
  foreach($allWebservices as $ws) {
    $ID_TEST     = G::LoadTranslation('ID_TEST');
    $UID         = htmlentities($ws);
    $WS_TITLE    = strip_tags($ws);

    $htmlGroup = '';
    $htmlGroup .= "<table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>";
    $htmlGroup .= "<tr>";
    $htmlGroup .= "<td width='250px' class='treeNode' style='border:0px;background-color:transparent;'>{$WS_TITLE}</td>";
    $htmlGroup .= "<td class='treeNode' style='border:0px;background-color:transparent;'>";
    if ( $WS_TITLE == 'Login' || $wsSessionId != '' )
      $htmlGroup .= "[<a href='#' onclick=\"showFormWS('{$UID}');return false;\">{$ID_TEST}</a>]";
    $htmlGroup .= "</td></tr></table>";


    $ch =& $tree->addChild($ws, $htmlGroup, array('nodeType'=>'child'));
    $ch->point = '<img src="/images/trigger.gif" />';
  }
  print( $tree->render() );
  //
