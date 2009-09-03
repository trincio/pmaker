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


	$triggers_names = '';

	if($_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] != 0) {
		$triggers_onfly = $_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS']." trigger(s) was executed <font color='#641213'><b>".strtolower($_SESSION['TRIGGER_DEBUG']['TIME'])."</b></font><br/>";
		$cnt = -1;
		if(isset($_SESSION['TRIGGER_DEBUG']['TRIGGERS_NAMES']))
		foreach($_SESSION['TRIGGER_DEBUG']['TRIGGERS_NAMES'] as $name){
			$t_code = $_SESSION['TRIGGER_DEBUG']['TRIGGERS_VALUES'][++$cnt]['TRI_WEBBOT'];
			$t_code = str_replace('"', '\'',$t_code);
			$t_code = addslashes($t_code);
			//Krumo($t_code);
			$t_code = Only1br($t_code);

			$triggers_names .= "<li><a href='#' onmouseout='hideTooltip()' onmouseover=\"showTooltip(event,'<b>Trigger code</b><br/><br/>".$t_code."<br/><br/>');return false\">Trigger: $name</a>";
		}
	} else {
		$triggers_onfly = " No triggers found <font color='#641213'><b>".strtolower($_SESSION['TRIGGER_DEBUG']['TIME'])."</b></font>";
	}

	$html = '<div class="ui-widget-header ui-corner-all" width="100%" align="center">Processmaker - Debugger</div>'.
	"<div style='font-size:11px; font-weight:bold' align='left'>[Triggers]</div><br/>
	<table width='100%' cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
		<tr>
		<td width='410px' class='treeNode' style='border:0px;background-color:transparent;'>
			
				<font color='#0B58B6'> $triggers_onfly ...</font><br/>
				 $triggers_names
			
		</td>
		<td width='60px' class='treeNode' style='border:0px;background-color:transparent;'>
			<div id='status_'></div>
		</td>
		</tr>
	</table>";


	$tree->showSign = false;
	$DEBUG_POST = $_SESSION['TRIGGER_DEBUG']['ERRORS'];
	for($i=0; $i<count($DEBUG_POST); $i++) {
		try{
			if(isset($DEBUG_POST[$i]['SINTAX']) and $DEBUG_POST[$i]['SINTAX'] != '') {
				$html .= "
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
				$html .= "
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

	$aKeys1 = array_keys($vars_acum);
	//var_dump($aKeys1); echo"<hr>";
	G::LoadClass('case');
 	$oApp= new Cases();
  	$aFields = $oApp->loadCase($_SESSION['APPLICATION']);

  	$aKeys2 = array_keys($aFields['APP_DATA']);
  	//var_dump($aKeys2); die;
  	$x = array_merge($aFields['APP_DATA'], $vars_acum);
	foreach($x as $key => $value)
	{ //var_dump($x[$key]); echo "<br>";
		if (!in_array($key, $aKeys2))
		 {
		  	if(is_array($x[$key]))
		  		$x[$key] = $x[$key]; //. ' -->(CREATED / CHANGED)';
		  	else
		  		$x[$key] = $x[$key]. ' -->(CREATED / CHANGED)';
		 }
		else
		 {
				if (in_array($key, $aKeys1))
				 {
						if(is_array($x[$key]))
							$x[$key] = $x[$key];// . ' -->(CREATED / CHANGED)';
						else
							$x[$key] = $x[$key]. ' -->(CREATED / CHANGED)';
				 }
		  }
		/*foreach($aKeys as $ke => $val)
		{
				if($ke!=$key)
						$vars_acum[$key]=$value;
		}*/
		//echo "Key: $key; Valor: $value <br />";
	}

	ob_start();
	Krumo($x);
	$oo1 = ob_get_contents();
	ob_end_clean();

	$html .= "
	<table width='100%' cellspacing='0' cellpadding='0' border='0' style='border:0px;'>
		<tr>
			<td >
			<div id='action'>
			<div style='font-size:11px; font-weight:bold' align='left'>[Dynaform variables]</div>
			".$oo1."</div>
			</td>
		</tr>
	</table>";

	$width_content = isset($_POST['NextStep'])?'50%':'95%';
	
	echo '<div class="grid" style="width:'.$width_content.'">
	<div class="boxTop"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	<div class="content" style="">
		  <table width="99%">
	      <tbody><tr>
	        <td valign="top">
	           '.$html.'
	        </td>
	      </tr>
	    </tbody></table>
	</div>
	<div class="boxBottom"><div class="a"></div><div class="b"></div><div class="c"></div></div>
	</div>'; 
	

	if(isset($_POST['NextStep'])){
		print('<input type="button" value="Continue" class="module_app_button___gray" onclick="javascript:location.href=\''.$_POST['NextStep'].'\'">');
	}

	function Only1br($string)
	{
		return preg_replace("/(\r\n)+|(\n|\r)+/", "<br />", $string);
	}

?> 














<style type="text/css">
	#nyk_tooltip{
		background-color:#EEE;
		border:1px solid #000;
		position:absolute;
		display:none;
		z-index:20000;
		padding:2px;
		font-size:0.9em;
		-moz-border-radius:6px;	/* Rounded edges in Firefox */
		font-family: "Trebuchet MS", "Lucida Sans Unicode", Arial, sans-serif;

	}
	#nyk_tooltipShadow{
		position:absolute;
		background-color:#555;
		display:none;
		z-index:10000;
		opacity:0.7;
		filter:alpha(opacity=70);
		-khtml-opacity: 0.7;
		-moz-opacity: 0.7;
		-moz-border-radius:6px;	/* Rounded edges in Firefox */
	}
</style>
<SCRIPT type="text/javascript">
	var nyk_tooltip = false;
	var nyk_tooltipShadow = false;
	var nyk_shadowSize = 4;
	var nyk_tooltipMaxWidth = 400;
	var nyk_tooltipMinWidth = 100;
	var nyk_iframe = false;
	var tooltip_is_msie = (navigator.userAgent.indexOf('MSIE')>=0 && navigator.userAgent.indexOf('opera')==-1 && document.all)?true:false;
	function showTooltip(e,tooltipTxt)
	{

		var bodyWidth = Math.max(document.body.clientWidth,document.documentElement.clientWidth) - 20;

		if(!nyk_tooltip){
			nyk_tooltip = document.createElement('DIV');
			nyk_tooltip.id = 'nyk_tooltip';
			nyk_tooltipShadow = document.createElement('DIV');
			nyk_tooltipShadow.id = 'nyk_tooltipShadow';

			document.body.appendChild(nyk_tooltip);
			document.body.appendChild(nyk_tooltipShadow);

			if(tooltip_is_msie){
				nyk_iframe = document.createElement('IFRAME');
				nyk_iframe.frameborder='5';
				nyk_iframe.style.backgroundColor='#FFFFFF';
				nyk_iframe.src = '#';
				nyk_iframe.style.zIndex = 100;
				nyk_iframe.style.position = 'absolute';
				document.body.appendChild(nyk_iframe);
			}

		}

		nyk_tooltip.style.display='block';
		nyk_tooltipShadow.style.display='block';
		if(tooltip_is_msie)nyk_iframe.style.display='block';

		var st = Math.max(document.body.scrollTop,document.documentElement.scrollTop);
		if(navigator.userAgent.toLowerCase().indexOf('safari')>=0)st=0;
		var leftPos = e.clientX + 10;

		nyk_tooltip.style.width = null;	// Reset style width if it's set
		nyk_tooltip.innerHTML = tooltipTxt;
		nyk_tooltip.style.left = leftPos + 'px';
		nyk_tooltip.style.top = e.clientY + 10 + st + 'px';


		nyk_tooltipShadow.style.left =  leftPos + nyk_shadowSize + 'px';
		nyk_tooltipShadow.style.top = e.clientY + 10 + st + nyk_shadowSize + 'px';

		if(nyk_tooltip.offsetWidth>nyk_tooltipMaxWidth){	/* Exceeding max width of tooltip ? */
			nyk_tooltip.style.width = nyk_tooltipMaxWidth + 'px';
		}

		var tooltipWidth = nyk_tooltip.offsetWidth;
		if(tooltipWidth<nyk_tooltipMinWidth)tooltipWidth = nyk_tooltipMinWidth;


		nyk_tooltip.style.width = tooltipWidth + 'px';
		nyk_tooltipShadow.style.width = nyk_tooltip.offsetWidth + 'px';
		nyk_tooltipShadow.style.height = nyk_tooltip.offsetHeight + 'px';

		if((leftPos + tooltipWidth)>bodyWidth){
			nyk_tooltip.style.left = (nyk_tooltipShadow.style.left.replace('px','') - ((leftPos + tooltipWidth)-bodyWidth)) + 'px';
			nyk_tooltipShadow.style.left = (nyk_tooltipShadow.style.left.replace('px','') - ((leftPos + tooltipWidth)-bodyWidth) + nyk_shadowSize) + 'px';
		}

		if(tooltip_is_msie){
			nyk_iframe.style.left = nyk_tooltip.style.left;
			nyk_iframe.style.top = nyk_tooltip.style.top;
			nyk_iframe.style.width = nyk_tooltip.offsetWidth + 'px';
			nyk_iframe.style.height = nyk_tooltip.offsetHeight + 'px';

		}

	}

	function hideTooltip()
	{
		nyk_tooltip.style.display='none';
		nyk_tooltipShadow.style.display='none';
		if(tooltip_is_msie)nyk_iframe.style.display='none';
	}

	</SCRIPT>
