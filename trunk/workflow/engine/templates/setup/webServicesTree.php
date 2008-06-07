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

  //G::LoadClass('group');
  G::LoadClass('groups');
  G::LoadClass('tree');

  global $G_HEADER;
  $G_HEADER->addScriptFile('/js/common/tree/tree.js');
  $groups = new Groups();

  $tree = new Tree();
  $tree->name = 'WebServices';
  $tree->nodeType="base";
  $tree->width="280px";
  $tree->value = '
	 <div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	 <div class="boxContentBlue">

	  <table width="100%" style="margin:0px;" cellspacing="0" cellpadding="0">
	  <tr>
		  <td class="userGroupTitle">'.G::loadTranslation("	ID_WEB_SERVICES").'</td>
	  </tr>
	</table>
	</div>
	<div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
  	<div class="userGroupLink"><a href="#" onclick="webServicesSetup();return false;">'.G::LoadTranslation('ID_SETUP_WEBSERVICES').'</a></div>
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
  foreach($allWebservices as $ws) {
    $ID_TEST     = G::LoadTranslation('ID_TEST');
    $UID         = htmlentities($ws);
    $WS_TITLE    = strip_tags($ws);
    $htmlGroup   = <<<GHTML
      <table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
        <tr>
          <td width='200px' class='treeNode' style='border:0px;background-color:transparent;'>{$WS_TITLE}</td>
          <td class='treeNode' style='border:0px;background-color:transparent;'>[<a href="#" onclick="showFormWS('{$UID}');return false;">{$ID_TEST}</a>]</td>
        </tr>
      </table>
GHTML;
    $ch =& $tree->addChild($ws, $htmlGroup, array('nodeType'=>'child'));
    $ch->point = '<img src="/images/trigger.gif" />';
  }
  print( $tree->render() );
  //
