<?php
/**
 * installServer.php
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
if(isset($_POST['form']['NW_TITLE']))
{
	G::LoadClass('Installer');
	G::LoadClass('json');
	$name	= trim($_POST['form']['NW_TITLE']);
	$inst	= new Installer();
	$isset	= $inst->isset_site($name);
	$new	= ((!$isset) && ctype_alnum($name))?true:false;
	$user	= (isset($_POST['form']['NW_USERNAME']))?trim($_POST['form']['NW_USERNAME']):'admin';
	$pass	= (isset($_POST['form']['NW_PASSWORD']))?$_POST['form']['NW_PASSWORD']:'admin';
	if($new)
	{
		$inst->create_site(Array(
			'name'=>$name,
			'admin'=>Array('username'=>$user,'password'=>$pass)
		),true);
	}
	$json	= new Services_JSON();
	$ec;
	$ec->created=($new)?true:false;
	$ec->name=$name;
	$ec->message=($new)?"Workspace created":"Workspace already exists or Name invalid";
	echo $json->encode($ec);
}
else
{
	$G_PUBLISH = new Publisher;
	$G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/newSite', '', '', '/sys/en/green/install/newSite');
	G::RenderPage( "publish" );
}
?>