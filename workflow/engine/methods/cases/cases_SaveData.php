<?
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
  //validate the data post
  $oForm = new Form($_SESSION['PROCESS']. '/' . $_GET['UID'], PATH_DYNAFORM);
  $oForm->validatePost();

  /* Includes */
  G::LoadClass('case');

  //load the variables
  $oCase = new Cases();
  $Fields = $oCase->loadCase( $_SESSION['APPLICATION'] );
  $Fields['APP_DATA'] = array_merge( $Fields['APP_DATA'], $_POST['form']);

  //Execute after triggers - Start
  $Fields['APP_DATA'] = $oCase->ExecuteTriggers ( $_SESSION['TASK'], 'DYNAFORM', $_GET['UID'], 'AFTER', $Fields['APP_DATA'] );
  //Execute after triggers - End

  //save data
  $aData = array();
  $aData['APP_NUMBER']      = $Fields['APP_NUMBER'];
  $aData['APP_PROC_STATUS'] = $Fields['APP_PROC_STATUS'];
  $aData['APP_DATA']        = $Fields['APP_DATA'];
  $oCase->updateCase( $_SESSION['APPLICATION'], $aData );

  //go to the next step
  $aNextStep = $oCase->getNextStep($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION']);
  $_SESSION['STEP_POSITION'] = $aNextStep['POSITION'];
  G::header('location: ' . $aNextStep['PAGE']);
