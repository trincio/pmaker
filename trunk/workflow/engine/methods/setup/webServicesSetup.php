<?php
/**
 * webServicesSetup.php
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
if (($RBAC_Response=$RBAC->userCanAccess("PM_FACTORY"))!=1) return $RBAC_Response;

  G::LoadClass('groups');

  $dbc = new DBConnection();
  $ses = new DBSession($dbc);

	$aFields['WS_HOST']	= $_SERVER['HTTP_HOST'];
	$aFields['WS_WORKSPACE'] = SYS_SYS;
/*
  $group = new Groupwf();
  $GrpUid = (isset($_GET['UID'])) ? urldecode($_GET['UID']):'';
  if ($GrpUid)
  {
    $aFields=$group->Load( $GrpUid );
    
  }
  else
  {
    $aFields=array();
  }*/
  $G_PUBLISH = new Publisher();
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'setup/webServicesSetup', '', $aFields , 'webServicesSetupSave');

  G::RenderPage( "publish" , "raw" );

?>