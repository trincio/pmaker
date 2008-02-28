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

	G::LoadInclude('ajax');
  G::LoadClass('dynaform');
  G::LoadClass('xmlDb');
  
  $dbc = new DBConnection();
  $ses = new DBSession($dbc);

  //$dynaform = new dynaform( $dbc );

  if ($_POST['form']['DYN_UID']==='') unset($_POST['form']['DYN_UID']);
  $Fields = $_POST['form'];
  if (!isset($Fields['DYN_UID'])) return;
  $file = G::decrypt( $Fields['A'] , URL_KEY );
  $Fields['DYN_FILENAME'] = (strcasecmp(substr($file,-5),'_tmp0')==0)? substr($file,0,strlen($file)-5) : $file;
  $_SESSION['CURRENT_DYNAFORM']=$Fields;
  //$dynaform->Save( $Fields );

  $dbc2 = new DBConnection( PATH_DYNAFORM . $file . '.xml' ,'','','','myxml' );
  $ses2 = new DBSession($dbc2);


  if (!isset($Fields['ENABLETEMPLATE'])) $Fields['ENABLETEMPLATE'] ="0";

  $ses2->execute(G::replaceDataField("UPDATE . SET WIDTH = @@WIDTH WHERE XMLNODE_NAME = 'dynaForm' ", $Fields));
  $ses2->execute(G::replaceDataField("UPDATE . SET ENABLETEMPLATE = @@ENABLETEMPLATE WHERE XMLNODE_NAME = 'dynaForm' ", $Fields));
  $ses2->execute(G::replaceDataField("UPDATE . SET MODE = @@MODE WHERE XMLNODE_NAME = 'dynaForm' ", $Fields));
  
?>