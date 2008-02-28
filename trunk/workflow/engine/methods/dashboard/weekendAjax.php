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
G::ForceLogin( 'WF_PROCESS' );
G::LoadInclude('ajax');

$G_HELP_PAGE = "setup-environment-time-controls-weekend";

$G_MAIN_MENU = "processmaker";
$G_SUB_MENU = "setupPM";
$G_THIRD_MENU = "workingTime";

$G_ID_MENU_SELECTED = "SETUP";
$G_ID_SUB_MENU_SELECTED = "ENVIRONMENT";
$G_ID_THIRD_MENU_SELECTED = "WEEKEND";

$dbc = new DBConnection;
$ses = new DBSession($dbc);

$holidays=$ses->execute('SELECT LEX_VALUE FROM LEXICO WHERE LEX_TOPIC ="HOLIDAY"');

$funcion=strtolower(get_ajax_value('function'));
$funcions=get_defined_functions();
if (in_array($funcion,$funcions['user'])) eval($funcion.'();');

function setDays()
{
	$days=get_ajax_value('days');
	$values=get_ajax_value('values');
	$days=explode(',',$days);
	$values=explode(',',$values);
	for($r=1;$r<sizeof($days);$r++)
		setDay($days[$r],$values[$r]);
}
function setDay($day,$dayValue)
{
	global $ses;
	$dayValue = (strcasecmp($dayValue,'true')==0)?1:0;
	$res=$ses->execute(" SELECT * FROM LEXICO WHERE LEX_KEY = '$day' AND LEX_TOPIC ='HOLIDAY' ");
	if ($res->count()==0)
		$res=$ses->execute(" INSERT INTO LEXICO (LEX_TOPIC, LEX_KEY, LEX_VALUE) VALUES ('HOLIDAY', '$day', $dayValue) ");
	else
		$res=$ses->execute(" UPDATE LEXICO SET LEX_VALUE = $dayValue WHERE LEX_KEY = '$day' AND LEX_TOPIC ='HOLIDAY' ");
	$res=$ses->execute(" SELECT * FROM LEXICO WHERE LEX_KEY = '$day' AND LEX_TOPIC ='HOLIDAY' ");
	$res=$res->read();
	echo ($res['LEX_VALUE']=='1')?'true':'false';
}
?>