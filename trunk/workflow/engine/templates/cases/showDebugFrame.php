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
	$tree->width = "100%";
	$tree->value = '
	<div class="boxTopBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	<div class="boxContentBlue">
  		<table width="100%" style="margin:0px;" cellspacing="0" cellpadding="0">
  			<tr>
	  			<td class="userGroupTitle">Triggers Debug option is activated</td>
  			</tr>
		</table>
	</div>
	<div class="boxBottomBlue"><div class="a"></div><div class="b"></div><div class="c"></div></div>';

	$triggers_names = '';
	
	if($_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] != 0) {
		$triggers_onfly = $_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS']." trigger(s) was executed <font color='#641213'><b>".strtolower($_SESSION['TRIGGER_DEBUG']['TIME'])."</b></font><br/>";
		if(isset($_SESSION['TRIGGER_DEBUG']['TRIGGERS_NAMES']))
		foreach($_SESSION['TRIGGER_DEBUG']['TRIGGERS_NAMES'] as $name){
			$triggers_names .= "<li>Trigger: <font color='#52603A'>$name</font>";
		}
	} else {
		$triggers_onfly = " No triggers found <font color='#641213'><b>".strtolower($_SESSION['TRIGGER_DEBUG']['TIME'])."</b></font>";
	}
	
	$html = "[Triggers]<br><br>
	<table width='100%' cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
		<tr>
		<td width='410px' class='treeNode' style='border:0px;background-color:transparent;'>
			<fieldset class='userGroupTitle'>
				<font color='#0B58B6'> $triggers_onfly ...</font><br/>
				 $triggers_names
			</fieldset>
		</td>
		<td width='60px' class='treeNode' style='border:0px;background-color:transparent;'>
			<div id='status_'></div>
		</td>
		</tr>
	</table>";
	$ch = &$tree->addChild(0, $html, array('nodeType' => 'child'));

	$tree->showSign = false;
	$DEBUG_POST = $_SESSION['TRIGGER_DEBUG']['ERRORS'];
	for($i=0; $i<count($DEBUG_POST); $i++) {
		try{
			if(isset($DEBUG_POST[$i]['SINTAX']) and $DEBUG_POST[$i]['SINTAX'] != '') {
				$html = "
				<table width='100%' cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
					<tr>
					<td width='410px' class='treeNode' style='border:0px;background-color:transparent;'>
						<fieldset>
							<legend><font color='#9CBDFF'>Some trigger throws an error</font></legend>
							".$DEBUG_POST[$i]['SINTAX']."
						</fieldset>
					</td>
					<td width='60px' class='treeNode' style='border:0px;background-color:transparent;'>
						<div id='status_'></div>
					</td>
					</tr>
				</table>";
				$ch = &$tree->addChild(0, $html, array('nodeType' => 'child'));
				//$ch->point = '<img src="/images/iconoenlace.png" />';
			}
			if(isset($DEBUG_POST[$i]['FATAL']) and $DEBUG_POST[$i]['FATAL'] != '') {
				$html = "
				<table width='100%' cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
					<tr>
					<td width='410px' class='treeNode' style='border:0px;background-color:transparent;'>
						<fieldset style='width:103%'>
							<legend><font color='red'>Some trigger throws an error</font></legend>
							".trim($DEBUG_POST[$i]['FATAL'])."
						</fieldset>	
					</td>
					<td width='60px' class='treeNode' style='border:0px;background-color:transparent;'>
						<div id='status_'></div>
					</td>
					</tr>
				</table>";
				$ch = &$tree->addChild(0, $html, array('nodeType' => 'child'));
				//$ch->point = '<img src="/images/iconoenlace.png" />';
			}
		} catch(Exception $e) {
			
		}	
	}	

	krumo::$show_details = 'disabled';
	$vars_acum = Array();
	for($i=0; $i<count($_SESSION['TRIGGER_DEBUG']['DATA']); $i++) {
		$tdebug_var = $_SESSION['TRIGGER_DEBUG']['DATA'][$i]['value'];

		$vars_acum[$_SESSION['TRIGGER_DEBUG']['DATA'][$i]['key']] = $tdebug_var;
	}
	ob_start();
	Krumo($vars_acum);
	$oo1 = ob_get_contents();
	ob_end_clean();
	
	$html = "
	<table width='100%' cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
		<tr>
			<td width='*' class='treeNode' style='border:0px;background-color:transparent;'>
			<div id='action'><font color=black>[Variables involved in the triggers]</font><br>".$oo1."</div>
			</td>
		</tr>
	</table>";
	$ch = &$tree->addChild(0, $html, array('nodeType' => 'child'));
	//$ch->point = '<img src="/images/btnGreen.gif" />';
	
	print ($tree->render());
	
	if(isset($_POST['NextStep'])){
		print('<input type="button" value="Continue" class="module_app_button___gray" onclick="javascript:location.href=\''.$_POST['NextStep'].'\'">');
	}