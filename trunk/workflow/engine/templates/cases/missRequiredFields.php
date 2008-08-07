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

/**
 * @Description This is the View of all groups from a determinated user
 * @author Erik Amaru Ortiz <erik@colosa.com>
 * @Date 24/04/2008
 * @LastModification 30/05/2008
 */

	G::LoadClass('tree');

  $tree = new Tree();
	$tree->name = 'debug';
	$tree->nodeType = "base";
	$tree->width = "400px";
	print '<table width="40%"><tr><td>';
	$tree->value = '	
	<div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	<div class="boxContentBlue">
  		<table width="100%" style="margin:0px;" cellspacing="0" cellpadding="0">
  			<tr>
	  			<td class="userGroupTitle"><font color="red">ERROR! </font>  Some fields in the form are causing an error</td>
  			</tr>
		</table>
	</div>
	<div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>';
		
	$tree->showSign=false;
	
	$rq = $_POST['req_val'];
	foreach( $rq as  $field) {
		$html = "<table width='100%' cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
			       <tr><td width='300px' class='treeNode' style='border:0px;background-color:transparent;'>
			       	<font color=black>(*) The field <font color=blue><b>$field</b></font> is required!</font>
			       </td></tr>
			       </table> ";
		$ch =& $tree->addChild($rq, $html, array('nodeType' => 'child'));
	}
	print ($tree->render());
	
	$netxpage = $_POST['next_step']['PAGE'];
	$previouspage = $_POST['previous_step']['PAGE'];	
	print '</td></tr><tr><td align="center">';
	print('<input type="button" value="Back" class="module_app_button___gray" onclick="javascript:location.href=\''.$previouspage.'\'">');
	print('<input type="button" value="Continue" class="module_app_button___gray" onclick="javascript:location.href=\''.$netxpage.'\'">');
	print '</td></tr></table>';
 
	