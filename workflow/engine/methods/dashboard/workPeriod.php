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
	$G_ENABLE_BLANK_SKIN = true;

	G::LoadClass( "workPeriod" );

	$dbc = new DBConnection;
	$ses = new DBSession( $dbc );
	$obj = new workPeriod( $dbc );
	
	$row = $obj->Load ();

	$row['SUNDAY']    = $row['noWorkingDays'][0];
	$row['MONDAY']    = $row['noWorkingDays'][1];
	$row['TUESDAY']   = $row['noWorkingDays'][2];
	$row['WEDNESDAY'] = $row['noWorkingDays'][3];
	$row['THURSDAY']  = $row['noWorkingDays'][4];
	$row['FRIDAY']    = $row['noWorkingDays'][5];
	$row['SATURDAY']  = $row['noWorkingDays'][6];

  $G_PUBLISH = new Publisher;
  $G_PUBLISH->SetTo( $dbc );
  $G_PUBLISH->AddContent( "image", "image", "workPeriodGraph" );
  $G_PUBLISH->AddContent( "xmlform", "xmlform", "setup/workPeriod","", $row , "workPeriodSave" );

  G::RenderPage( 'publish' );
?>