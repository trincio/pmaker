<?php
/**
 * mail.php
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
if (($RBAC_Response=$RBAC->userCanAccess("PM_SETUP"))!=1) return $RBAC_Response;
	$G_ENABLE_BLANK_SKIN = true;

	$required=array();
	$required['mail']=array('Mailer' => 'mail', 'Timeout' => '30');
	$required['smtp']=array('Mailer' => 'smtp','Host' => 'smtp.server.com','SMTPAuth' => 'true','Username' => 'username@server.com','Password' => '','Timeout' => '30', 'Port' => '25');
	$config=array();
	$dbc = new DBConnection;
	$ses = new DBSession($dbc);

	$result=$ses->execute("select * from LEXICO where LEX_TOPIC='MAILER'");

	for($r=0;$r < $result->count();$r++)
	{
		$a=$result->read();
		$config[$a['LEX_KEY']]=$a['LEX_VALUE'];
	}

	function verifyConfig()
	{
		global $required, $config,$ses;
		switch(true)
		{
		case (!array_key_exists('Mailer',$config)):
			$config=array('Mailer' => 'mail');
			$ses->execute("insert into LEXICO (LEX_TOPIC,LEX_KEY,LEX_VALUE) values ('MAILER','Mailer','mail')");
		case (array_key_exists($config['Mailer'],$required)):
			foreach($required[$config['Mailer']] as $param => $paramValue)
				if (!array_key_exists($param,$config))
				{
					$config[$param]=$paramValue;
					$ses->execute("insert into LEXICO (LEX_TOPIC,LEX_KEY,LEX_VALUE) values ('MAILER','$param','$paramValue')");
				}
			break;
		default:
			//mm libre??
		}
	}
	verifyConfig();

	$G_PUBLISH = new Publisher;
	$G_PUBLISH->SetTo( $dbc );
	$uconfig=array();
	foreach ($config as $key => $value)
	{
		if ($key==='SMTPAuth') $value=($value=='true')?'Y':'N';
		$uconfig[strtoupper($key)]=$value;
	}
	$G_PUBLISH->AddContent('xmlform', 'xmlform', 'setup/mail','',$uconfig, 'mailSave');
	
	G::RenderPage( 'publish' );
	$mail    = G::encryptLink("mailAjax.php");

?>
<script language="JavaScript">
attachFunctionEventOnChange(document.getElementById('form[MAILER]'),mailer_onchange);
mailer_onchange();
function mailer_onchange()
{
	mailer=document.getElementById('form[MAILER]');
	switch(mailer.value)
	{
		case 'mail':
			hideField('HOST');
			hideField('USERNAME');
			hideField('PASSWORD');
			hideField('SMTPAUTH');
			hideField('PORT');
			break;
		case 'smtp':
			showField('HOST');
			showField('USERNAME');
			showField('PASSWORD');
			showField('SMTPAUTH');
			showField('PORT');
			break;
	}
}
function hideField(name)
{
	if (name=='SMTPAUTH')
	{
		field = document.getElementById('form['+name+']');
		field=field.parentNode.parentNode;
	}
	else
		field = document.getElementById('DIV_'+name);
	field.style.visibility='hidden';
	field.style.display='none';
}

function showField(name)
{
	if (name=='SMTPAUTH')
	{
		field = document.getElementById('form['+name+']');
		field=field.parentNode.parentNode;
	}
	else
		field = document.getElementById('DIV_'+name);
	field.style.visibility='visible';
	field.style.display='';
}

function save_onclick(save)
{
	var pars="";
	<?php
	foreach ($required['smtp'] as $param => $paramValue)
		echo('pars=pars+"&'.$param.'="+encodeURIComponent(document.getElementById("form['.strtoupper($param).']").value);'."\n");
	?>
	ajax_function('<?=G::encryptLink("mailAjax.php")?>','changeConfig',pars.substr(1));
	document.location.reload();
}
function sendMail(boton)
{
	var pars="";
	target=document.getElementById('target');
	content=document.getElementById('content');
	alert(ajax_function('<?=G::encryptLink("mail.php")?>','sendMail','target='+encodeURIComponent(target.value)+'&content='+encodeURIComponent(content.value)));
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