<?php
/**
 * $Id$
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * You can contact Colosa Inc, 2655 Le Jeune Road, Suite 1112, Coral Gables, 
 * FL 33134, USA or email info@colosa.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * ProcessMaker" logo and retain the original copyright notice. If the display
 * of the logo is not reasonably feasible for technical reasons, the
 * Appropriate Legal Notices must display the words "Powered by ProcessMaker"
 * and retain the original copyright notice.
 * -
 */
if (($RBAC_Response=$RBAC->userCanAccess("PM_SETUP"))!=1) return $RBAC_Response;
G::LoadInclude('ajax');

	$G_ENABLE_BLANK_SKIN = true;

$ARR_WEEKDAYS[0] = array('SUNDAY', 'MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY');
$ARR_WEEKDAYS['es'] = array("Domingo?", "Lunes?", "Martes?", "Miércoles?", "Jueves?", "Viernes?", "Sábado?");
$ARR_WEEKDAYS['en'] = array("Sunday?", "Monday?", "Tuesday?", "Wednesday?", "Thursday?", "Friday?", "Saturday?");
$ARR_WEEKDAYS['fa'] = array('یکشنبه','دوشنبه','سه شنبه','چهارشنبه','پنجشنبه ','جمعه','آدینه');

$dbc = new DBConnection;
$ses = new DBSession($dbc);

$holidays=$ses->execute( "SELECT LEX_VALUE FROM LEXICO WHERE LEX_TOPIC ='NOWORKINGDAY' ");

$config=array();
for($id=0;$id<7;$id++)
{
	$res=$ses->execute(" SELECT * FROM LEXICO WHERE LEX_KEY = '".$ARR_WEEKDAYS[0][$id]."' AND LEX_TOPIC ='HOLIDAY' ");
	$res=$res->read();
	$config[$ARR_WEEKDAYS[0][$id]]=$res['LEX_VALUE'];
}
$G_PUBLISH = new Publisher;
$G_PUBLISH->SetTo( $dbc );
$G_PUBLISH->AddContent('xmlform', 'xmlform', 'setup/weekend', '',$config ,'' );
G::RenderPage( 'publish' );
?>
<script language="JavaScript">
function var_dump(obj)
{
	msg='';
	if (typeof(obj)=='object')
	for(a in obj)
	{
		msg+=a;//+':'+obj[a];
		msg+="\t";
	}
	else
		msg=obj;
	alert(msg);
}
function on_submit(myForm)
{
	days='';values='';
	for(cbi in myForm.elements)
	if (cbi.substr(0,4)=='form')
	{
		cb=myForm.elements[cbi];
		days+=','+cb.id;
		values+=','+cb.checked;
	}
	ajax_function('<?=G::encryptLink('weekendAjax.php');?>','setDays','days='+days+'&values='+values);
	document.location.reload(true);
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