<?php
/**
 * cases_Open.php
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
	$oCase->thisIsTheCurrentUser($_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['USER_LOGGED'], 'REDIRECT', 'cases_List');

  /* Jump to Case Number APP_NUMBER*/
  if ( !isset($_GET['APP_UID']) && isset($_GET['APP_NUMBER']))
  {
//  print "Jump to Case Number APP_NUMBER: " . $_GET['APP_NUMBER'];
	  $_GET['APP_UID']   = $oCase->getApplicationUIDByNumber($_GET['APP_NUMBER']);
	  $_GET['DEL_INDEX'] = $oCase->getCurrentDelegation($_GET['APP_UID'], $_SESSION['USER_LOGGED']);
	  if (is_null($_GET['DEL_INDEX'])) {
	  	if (is_null($_GET['APP_UID'])) {
	  		G::SendMessageText(G::LoadTranslation('ID_CASE_DOES_NOT_EXISTS'), 'info');
	  	}
	  	else {
	  		G::SendMessageText(G::LoadTranslation('ID_CASE_IS_CURRENTLY_WITH_ANOTHER_USER'), 'info');
	  	}
	  	//G::SendMessageText(G::LoadTranslation('ID_CASE_IS_CURRENTLY_WITH_ANOTHER_USER'), 'info');
	  	G::header('location: cases_List');
	  }
    //krumo ($_GET);die;
  }

  $sAppUid   = $_GET['APP_UID'];
  $iDelIndex = $_GET['DEL_INDEX'];

  $aFields = $oCase->loadCase( $sAppUid, $iDelIndex );

  //draft or to do
  if (($aFields['APP_STATUS'] == 'DRAFT') || ($aFields['APP_STATUS'] == 'TO_DO'))
  {
    require_once 'classes/model/AppDelay.php';
    $oCriteria = new Criteria('workflow');
    $oCriteria->add(AppDelayPeer::APP_UID, $sAppUid);
    $oCriteria->add(AppDelayPeer::APP_DEL_INDEX, $iDelIndex);
    $oCriteria->add(AppDelayPeer::APP_TYPE, 'PAUSE');
    $oCriteria->add(AppDelayPeer::APP_DISABLE_ACTION_USER, null);
    $oCriteria->add($oCriteria->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_USER, null, Criteria::ISNULL)->addOr($oCriteria->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_USER, 0)));
    $oDataset = AppDelayPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    $aRow = $oDataset->getRow();

    if(!$aRow) {
      $oAppDelegation = new AppDelegation();
      $aDelegation = $oAppDelegation->load($sAppUid, $iDelIndex);
      if ($aDelegation['USR_UID'] == $_SESSION['USER_LOGGED']) {
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
        unset($_SESSION['bNoShowSteps']);
        $aNextStep = $oCase->getNextStep( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION'] );
        $sPage     = $aNextStep['PAGE'];
        G::header('location: ' . $sPage);
      }
      else {
        $_SESSION['APPLICATION']   = $_GET['APP_UID'];
  	    $_SESSION['INDEX']         = $iDelIndex;
        $_SESSION['PROCESS']       = $aFields['PRO_UID'];
        $_SESSION['TASK']          = -1;
        $_SESSION['STEP_POSITION'] = 0;
        require_once( PATH_METHODS . 'cases' . PATH_SEP . 'cases_Resume.php');
      }
    }
    else {
      $_SESSION['APPLICATION']   = $_GET['APP_UID'];
  	  $_SESSION['INDEX']         = $iDelIndex;
      $_SESSION['PROCESS']       = $aFields['PRO_UID'];
      $_SESSION['TASK']          = -1;
      $_SESSION['STEP_POSITION'] = 0;
      require_once( PATH_METHODS . 'cases' . PATH_SEP . 'cases_Resume.php');
    }
  }
  //APP_STATUS <> DRAFT and TO_DO
  else
  {
  	$_SESSION['APPLICATION']   = $_GET['APP_UID'];
  	$_SESSION['INDEX']         = $iDelIndex;
    //$_SESSION['INDEX']         = -1;
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
