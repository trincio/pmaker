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

try {
  
$rows[] = array ( 'uid' => 'char', 'name' => 'char', 'age' => 'integer', 'balance' => 'float' );
$rows[] = array ( 'uid' => 11, 'name' => 'john',   'age' => 44, 'balance' => 123423 );
$rows[] = array ( 'uid' => 22, 'name' => 'bobby',  'age' => 33, 'balance' => 23456 );
$rows[] = array ( 'uid' => 33, 'name' => 'Dan',    'age' => 22, 'balance' => 34567 );
$rows[] = array ( 'uid' => 33, 'name' => 'Mike',   'age' => 21, 'balance' => 4567 );
$rows[] = array ( 'uid' => 44, 'name' => 'Paul',   'age' => 22, 'balance' => 567 );
$rows[] = array ( 'uid' => 55, 'name' => 'Will',   'age' => 23, 'balance' => 67 );
$rows[] = array ( 'uid' => 66, 'name' => 'Ernest', 'age' => 24, 'balance' => 7 );
$rows[] = array ( 'uid' => 77, 'name' => 'Albert', 'age' => 25, 'balance' => 84567 );
$rows[] = array ( 'uid' => 88, 'name' => 'Sue',    'age' => 26, 'balance' => 94567 );
$rows[] = array ( 'uid' => 99, 'name' => 'Freddy', 'age' => 22, 'balance' => 04567 );

$_DBArray['user'] = $rows;
$_SESSION['_DBArray'] = $_DBArray;
//krumo ( $_DBArray );

  G::LoadClass( 'ArrayPeer');
    $c = new Criteria ('dbarray');
    $c->setDBArrayTable('user');
    $c->add ( 'user.age', 122 , Criteria::GREATER_EQUAL );
    $c->add ( 'user.balance', 3456 , Criteria::GREATER_EQUAL );
    $c->addAscendingOrderByColumn ('name');

      /*$rs = ArrayBasePeer::doSelectRs ( $c );
      $rs->next();
      $row = $rs->getRow();
      while ( is_array ( $row ) ) {
        $rs->next();
        $row = $rs->getRow();
      }*/

/* Includes */
G::LoadClass('pmScript');
G::LoadClass('case');
G::LoadClass('derivation');
$oCase     = new Cases ();
$appUid = isset ($_SESSION['APPLICATION']) ? $_SESSION['APPLICATION'] : '';
$appFields = $oCase->loadCase( $appUid );
$Fields['APP_UID']       = $appFields['APP_UID'];
$Fields['APP_NUMBER']    = $appFields['APP_NUMBER'];
$Fields['APP_STATUS']    = $appFields['APP_STATUS'];
$Fields['STATUS']        = $appFields['STATUS'];
$Fields['APP_TITLE']     = $appFields['TITLE'];
$Fields['PRO_UID']       = $appFields['PRO_UID'];
$Fields['APP_PARALLEL']  = $appFields['APP_PARALLEL'];
$Fields['APP_INIT_USER'] = $appFields['APP_INIT_USER'];
$Fields['APP_CUR_USER']  = $appFields['APP_CUR_USER'];
$Fields['APP_DATA']      = $appFields['APP_DATA'];
$Fields['CREATOR']       = $appFields['CREATOR'];

$Fields['PRO_TITLE'] = Content::load ( 'PRO_TITLE', '', $appFields['PRO_UID'], SYS_LANG );
$oUser = new Users();
$oUser->load( $appFields['APP_CUR_USER'] );
$Fields['CUR_USER']     = $oUser->getUsrFirstname() . ' ' . $oUser->getUsrLastname();

$threads     = $oCase->GetAllThreads ($appFields['APP_UID']); 
$Fields['THREADS']  = $threads;
$Fields['CANT_THREADS']  = count($threads);
$delegations = $oCase->GetAllDelegations ($appFields['APP_UID']); 
foreach ( $delegations as $key => $val ) {
  $delegations[$key]['TAS_TITLE'] = Content::load ( 'TAS_TITLE', '', $val['TAS_UID'], SYS_LANG );
  $oUser->load( $val['USR_UID'] );
  $delegations[$key]['USR_NAME'] = $oUser->getUsrFirstname() . ' ' . $oUser->getUsrLastname();
}
$Fields['CANT_DELEGATIONS']  = count($delegations);
$Fields['DELEGATIONS']  = $delegations;
  /* Render page */
  $G_PUBLISH = new Publisher;
  //$G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'cases/casesDemo', $c );
	$G_PUBLISH->AddContent('smarty', 'cases/casesDemo', '', '', $Fields);
  G::RenderPage( "publish" );

}
catch ( Exception $e ){
  $G_PUBLISH = new Publisher;
	$aMessage['MESSAGE'] = $e->getMessage();
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
  G::RenderPage('publish');
}
