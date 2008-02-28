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
  switch ($RBAC->userCanAccess('PM_CASES'))
  {
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

  /* Menues */
  $G_MAIN_MENU        = 'processmaker';
  $G_SUB_MENU         = 'cases';
  $G_ID_MENU_SELECTED = 'CASES';

  /* Prepare page before to show */
  $aFields = array();
  $oCase = new Cases();
  $bCanStart = $oCase->canStartCase( $_SESSION['USER_LOGGED'] );

  if ($bCanStart)
  {
    $aFields['LANG'] = SYS_LANG;
    $aFields['USER'] = $_SESSION['USER_LOGGED'];
    $sXmlForm        = 'cases/cases_New.xml';
    $_DBArray['NewCase'] = $oCase->getStartCases( $_SESSION['USER_LOGGED'] );
    
  }
  else  {
    $sXmlForm = 'cases/cases_CannotInitiateCase.xml';
  }

  if ( isset( $_SESSION['G_MESSAGE']) && strlen($_SESSION['G_MESSAGE']) > 0 ) {
    $aMessage = array();
    $aMessage['MESSAGE'] = $_SESSION['G_MESSAGE'];
	  //$_SESSION['G_MESSAGE_TYPE'];
    unset($_SESSION['G_MESSAGE']);
    unset($_SESSION['G_MESSAGE_TYPE']);
  }
  
  /* Render page */
  $G_PUBLISH          = new Publisher;
  if ( isset ( $aMessage ) ) {
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
  }
  $G_PUBLISH->AddContent('xmlform', 'xmlform', $sXmlForm, '', $aFields, 'cases_Save');
  G::RenderPage( 'publish' );
