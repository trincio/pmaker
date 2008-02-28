<?php

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
    $GROUP_TITLE = htmlentities($group->getGrpTitle());
    $htmlGroup   = <<<GHTML
      <table cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
        <tr>
          <td width='250px' class='treeNode' style='border:0px;background-color:transparent;'>{$GROUP_TITLE}</td>
          <td class='treeNode' style='border:0px;background-color:transparent;'>[<a href="#" onclick="editGroup('{$UID}');return false;">{$ID_EDIT}</a>]</td>
          <td class='treeNode' style='border:0px;background-color:transparent;'>[<a href="#" onclick="selectGroup('{$UID}', this);return false;">{$ID_MEMBERS}</a>]</td>
          <td class='treeNode' style='border:0px;background-color:transparent;'>[<a href="#" onclick="deleteGroup('{$UID}');return false;">{$ID_DELETE}</a>]</td>
        </tr>
      </table>
GHTML;
    $ch =& $tree->addChild($group->getGrpUid(), $htmlGroup, array('nodeType'=>'child'));
    $ch->point = '<img src="/images/users.png" />';
  }
  print( $tree->render() );

?>
