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
  /* Permissions */
  switch ($RBAC->userCanAccess('PM_CASES')) {
	  case -2:
  	  G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels');
	    G::header('location: ../login/login');
	    die;
  	break;
	  case -1:
  	  G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
	    G::header('location: ../login/login');
  	  die;
  	break;
  }

  /* Includes */
  G::LoadClass('case');

/* GET , POST & $_SESSION Vars */
  if (isset($_SESSION['APPLICATION']))  { unset($_SESSION['APPLICATION']);  }
  if (isset($_SESSION['PROCESS']))      { unset($_SESSION['PROCESS']);      }
  if (isset($_SESSION['INDEX']))        { unset($_SESSION['INDEX']);        }
  if (isset($_SESSION['STEP_POSITION'])){ unset($_SESSION['STEP_POSITION']);}
  try {
  /* Process the info */
	$oCase = new Cases();
  
  /* Jump to Case Number APP_NUMBER*/
  if ( !isset($_GET['APP_UID']) && isset($_GET['APP_NUMBER']))
  {
//  print "Jump to Case Number APP_NUMBER: " . $_GET['APP_NUMBER'];
    
	  $_GET['APP_UID']   = $oCase->getApplicationUIDByNumber($_GET['APP_NUMBER']);
	  $_GET['DEL_INDEX'] = $oCase->getCurrentDelegation($_GET['APP_UID'], $_SESSION['USER_LOGGED']);
	  var_export( $_GET['APP_UID'] );
	  var_export( $_GET['APP_UID'] );
//  krumo ($_GET);die;
  }

  $sAppUid   = $_GET['APP_UID'];
  $iDelIndex = $_GET['DEL_INDEX'];
  
  $aFields = $oCase->loadCase( $sAppUid, $iDelIndex );

  //draft or to do
  if ( $aFields['APP_STATUS'] == 'DRAFT' || $aFields['APP_STATUS'] == 'TO_DO' )
  {
    $_SESSION['APPLICATION']   = $sAppUid;
    $_SESSION['INDEX']         = $iDelIndex;

    if ( is_null ( $aFields['DEL_INIT_DATE']) ) {
      $oCase->setDelInitDate( $sAppUid, $iDelIndex );
      $aFields = $oCase->loadCase( $sAppUid, $iDelIndex );
    }
  
    $_SESSION['PROCESS']       = $aFields['PRO_UID'];
    $_SESSION['TASK']          = $aFields['TAS_UID'];
    $_SESSION['STEP_POSITION'] = 0;

    /* Redirect to next step */
    $aNextStep = $oCase->getNextStep( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION'] );
    $sPage     = $aNextStep['PAGE'];
    G::header('location: ' . $sPage);
  }
  //APP_STATUS <> DRAFT and TO_DO
  else
  {
  	$_SESSION['APPLICATION']   = $_GET['APP_UID'];
    $_SESSION['INDEX']         = -1;
    $_SESSION['PROCESS']       = $aFields['PRO_UID'];
    $_SESSION['TASK']          = -1;
    $_SESSION['STEP_POSITION'] = 0;
    require_once( PATH_METHODS . 'cases' . PATH_SEP . 'cases_Resume.php');
  }
}
catch ( Exception $e ) {
    $aMessage = array();
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH          = new Publisher;
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish' );  
}
