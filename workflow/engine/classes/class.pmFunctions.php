<?php
/**
 * class.pmFunctions.php
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
////////////////////////////////////////////////////
// PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

function pauseCase($sApplicationUID = '', $iDelegation = 0, $sUserUID = '', $sUnpauseDate = null) {//var_dump($sApplicationUID, $iDelegation, $sUserUID, $sUnpauseDate);die(':|');
  try {
    if ($sApplicationUID == '') {
      throw new Exception('The application UID cannot be empty!');
    }
    if ($iDelegation == 0) {
      throw new Exception('The delegation index cannot be 0!');
    }
    if ($sUserUID == '') {
      throw new Exception('The user UID cannot be empty!');
    }
    G::LoadClass('case');
    $oCase = new Cases();
    $oCase->pauseCase($sApplicationUID, $iDelegation, $sUserUID, $sUnpauseDate);
  }
  catch (Exception $oException) {
    throw $oException;
  }
}
?>