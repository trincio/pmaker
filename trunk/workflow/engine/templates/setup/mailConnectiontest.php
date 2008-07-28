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
    G::LoadClass('net');

    $tree = new Tree();
    $host = new net($_POST['srv']);

	$tree->name = 'xx';
	$tree->nodeType = "base";
	$tree->width = "520px";
	$tree->value = '
	<div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	<div class="boxContentBlue">
		<table width="100%" style="margin:0px;" cellspacing="0" cellpadding="0">
			<tr>
				<td class="userGroupTitle">Test SMTP Connection</td>
			</tr>
		</table>
	</div>
	<div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>';

	$tests = Array('',
                'Resolve Host Name '.$_POST['srv'],
                'Checking port <b>'.$_POST['port'].'</b>',
                'Stablishing connection to host '.$host->hostname,
				'Account\'s auto login ['.$_POST['account'].'] in '.$host->hostname.' SMTP Server'
    );
	$tree->showSign = false;
	$n = Array('','uno','dos','tres','cuatro','cinco');
	for($i=1; $i<count($tests);$i++)
	{
		$html = "
		<div id='test_$i' style='display:none'>
		<table width='100%' cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
			<tr>
				<td width='10px' class='treeNode' style='border:0px;background-color:transparent;'>
					<IMG src=\"/images/".$n[$i].".gif\" width=\"25\" height=\"25\" align=\"left\" border=\"0\">
				</td>
				<td width='410px' class='treeNode' style='border:0px;background-color:transparent;'>
				<div id='action_$i'>$tests[$i]</div>
				</td>
			</tr>
			<tr>
				<td width='10px' class='treeNode' style='border:0px;background-color:transparent;'>
				</td>
				<td width='60px' class='treeNode' style='border:0px;background-color:transparent;'>
					<div id='status_$i'></div>
				</td>
			</tr>
		</table>
		</div>";
		$ch = &$tree->addChild($i, $html, array('nodeType' => 'child'));
		//$ch->point = '<img src="/images/iconoenlace.png" />';
	}
	print ($tree->render());
	print ("<div id='bnt_ok' style='display:none'><input type=button class='module_app_button___gray' onclick='jvascript:cancelTestConnection()' value='DONE'></div>");
