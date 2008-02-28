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
if (($RBAC_Response=$RBAC->userCanAccess("PM_FACTORY"))!=1) return $RBAC_Response;

  //G::genericForceLogin( 'WF_MYINFO' , 'login/noViewPage', $urlLogin = 'login/login' );

  require_once('classes/model/Dynaform.php');
  
  $dynUid=(isset($_GET['DYN_UID'])) ? urldecode($_GET['DYN_UID']):'';
  $dynaform = new dynaform();
  if ($dynUid=='')
  {
    $aFields['DYN_UID']= $dynUid ;
  }
  else
  {
    $aFields=$dynaform->load( $dynUid );
  }
  $aFields['PRO_UID'] = isset($dynaform->Fields['PRO_UID'])?$dynaform->Fields['PRO_UID']:$_GET['PRO_UID'];

  $G_PUBLISH = new Publisher();
  $G_HEADER->clearScripts();
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'dynaforms/dynaforms_Edit', '', $aFields , SYS_URI.'dynaforms/dynaforms_Save');
  
  G::RenderPage( "publish-raw" , "raw" );
?>