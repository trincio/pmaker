<?php
/**
 * dashboard.php
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
$show 	= Array('cases','casesByUser','casesbyProcess');
$cls	= Array(
		'show'=>G::ifthen(@$_GET['show'],"cases")
	);
if(!in_array($cls['show'],$show)){die('Invalid Request');}
require_once ( "classes/model/Application.php" );
require_once ( "classes/model/AppDelegation.php" );
G::LoadThirdParty("libchart/classes","libchart");
header("Content-type: image/png");
$chart = new PieChart(650,380);
$dataSet = new XYDataSet();

$c = new Criteria('workflow');
$c->clearSelectColumns();
$c->addSelectColumn ( ApplicationPeer::APP_STATUS );
$c->addSelectColumn ( 'COUNT(*) AS CC') ;
$c->addJoin(ApplicationPeer::APP_UID,AppDelegationPeer::APP_UID, Criteria::LEFT_JOIN);
$c->add(AppDelegationPeer::USR_UID, '00000000000000000000000000000001');
$c->addGroupByColumn(ApplicationPeer::APP_STATUS);
$rs = ApplicationPeer::doSelectRS( $c );
$rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
$rs->next();
$row = $rs->getRow();
while ( is_array ( $row ) ) {
	$l = $row['APP_STATUS'];
	$v = $row['CC'];
	$dataSet->addPoint(new Point($l." ({$v})", (int)$v));
	$rs->next();
	$row = $rs->getRow();
}
$dataSet->addPoint(new Point("Otro  (0)",0));
$chart->setDataSet($dataSet);
$chart->setTitle("Cases list");
$chart->render();
