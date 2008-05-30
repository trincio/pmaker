<?php
/**
 * processes_Export.php
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

G::LoadThirdParty('pear/json','class.json');

try {

  $oJSON = new Services_JSON();
  $stdObj = $oJSON->decode( $_POST['data'] );
  if ( isset ($stdObj->pro_uid ) )
    $sProUid = $stdObj->pro_uid;
  else
    throw ( new Exception ( 'the process uid is not defined!.' ) );

/* Includes */
G::LoadClass('processes');
$oProcess  = new Processes();
$proFields = $oProcess->serializeProcess( $sProUid );
$Fields = $oProcess->saveSerializedProcess ( $proFields );

  /* Render page */
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'processes/processes_Export', '', $Fields );
  G::RenderPage( 'publish', 'raw' );

}
catch ( Exception $e ){
  $G_PUBLISH = new Publisher;
	$aMessage['MESSAGE'] = $e->getMessage();
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
  G::RenderPage('publish', 'raw' );
}
