<?php
/**
 * departments_Tree.php
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

  G::LoadClass('group');
  G::LoadClass('tree');

  global $G_HEADER;
  $G_HEADER->addScriptFile('/js/common/tree/tree.js');

  $dbc = new DBConnection();
  $ses = new DBSession($dbc);

  $department = new departmentDependencie( $dbc );
  $department->Fields['DEP_UID']='0';
  $department->Fields['DEP_TITLE']='_root_';

  $orgChar = array();
  $orgChar = LoadSubDependencies( $department );
  function LoadSubDependencies( $department ){
    $department->Fields['DEP_TITLE'] = 
      (!isset($department->Fields['DEP_TITLE']))?$department->Fields['DEP_UID']:$department->Fields['DEP_TITLE'];
    $node = new Xml_Node($department->Fields['DEP_UID'],'open',$department->Fields['DEP_TITLE'],
      array('nodeType'=>'child',
        /*'minus'=>"<span class='treeMinus' onclick='tree.contract(this.parentNode);'>&nbsp;-</span>",
        'plus'=>"<span class='treePlus' style='zoom:100%;font-family:Courier New, Courier, mono;padding-left:1px;padding-right:1px;padding-top:0px;padding-bottom:1px;line-height:10px;cursor:pointer;'
       onclick='tree.expand(this.parentNode);'>&nbsp;+</span>",
        'point'=>'**',*/
      ));
    while ($dep = $department->LoadDependencie()) {
      $newNode = LoadSubDependencies($dep);
      $node->addChildNode( $newNode );
    }
    if (sizeof($node->children)>0) $node->attributes['nodeType']='parent';
    return $node;
  }

  $tree = new Tree($orgChar);
  $tree->name = 'Departments';
  $tree->nodeType="base";
  $tree->width="350px";
  $tree->value = 'dsfdsf'; 

  $tree->children[0]->name = 'Departments';
  $tree->children[0]->nodeType="base";
  $tree->children[0]->width="350px";
  $tree->children[0]->value = 'dsfdsf'; 
  /*'
	 <div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	 <div class="boxContentBlue">
 
	  <table width="100%" style="margin:0px;" cellspacing="0" cellpadding="0">
	  <tr>
		  <td class="userGroupTitle">Group Chart</td>
	  </tr>
	</table>
	</div>
	<div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
  	<div class="userGroupLink"><a href="#" onclick="addGroup();return false;">'.G::LoadTranslation('ID_NEW_GROUP').'</a></div>
	';*/
  $tree->showSign=false;
  print( $tree->render() );

?>
