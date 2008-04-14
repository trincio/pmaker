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
  $tree->name = 'Groups';
  $tree->nodeType="base";
  $tree->width="350px";
  $tree->value = '
	 <div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	 <div class="boxContentBlue">

	  <table width="100%" style="margin:0px;" cellspacing="0" cellpadding="0">
	  <tr>
		  <td class="userGroupTitle">'.G::loadTranslation("ID_GROUP_CHART").'</td>
	  </tr>
	</table>
	</div>
	<div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
  	<div class="userGroupLink"><a href="#" onclick="addGroup();return false;">'.G::LoadTranslation('ID_NEW_GROUP').'</a></div>
	';
  $tree->showSign=false;

  $allGroups= $groups->getAllGroups();
  foreach($allGroups as $group) {
    $ID_EDIT     = G::LoadTranslation('ID_EDIT');
    $ID_MEMBERS  = G::LoadTranslation('ID_MEMBERS');
    $ID_DELETE   = G::LoadTranslation('ID_DELETE');
    $UID         = htmlentities($group->getGrpUid());
    //$GROUP_TITLE = htmlentities($group->getGrpTitle());
    $GROUP_TITLE = strip_tags($group->getGrpTitle());
    $htmlGroup   = <<<GHTML
      <table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
        <tr>
          <td width='250px' class='treeNode' style='border:0px;background-color:transparent;'>{$GROUP_TITLE}</td>
          <td class='treeNode' style='border:0px;background-color:transparent;'>[<a href="#" onclick="editGroup('{$UID}');return false;">{$ID_EDIT}</a>]</td>
          <td class='treeNode' style='border:0px;background-color:transparent;'>[<a href="#" onclick="selectGroup('{$UID}');return false;">{$ID_MEMBERS}</a>]</td>
          <td class='treeNode' style='border:0px;background-color:transparent;'>[<a href="#" onclick="deleteGroup('{$UID}');return false;">{$ID_DELETE}</a>]</td>
        </tr>
      </table>
GHTML;
    $ch =& $tree->addChild($group->getGrpUid(), $htmlGroup, array('nodeType'=>'child'));
    $ch->point = '<img src="/images/users.png" />';
  }
  print( $tree->render() );
  //