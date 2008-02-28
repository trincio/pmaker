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
G::LoadInclude('ajax');
$aData = urldecode_values($_POST['form']);
switch ($aData['action']) {
	case 'savePattern':
	  G::LoadClass('tasks');
	  $oTasks = new Tasks();
	  //if ($aData['ROU_TYPE'] != $aData['ROU_TYPE_OLD'])
	  //{
	  	$oTasks->deleteAllRoutesOfTask($aData['PROCESS'], $aData['TASK']);
	  //}
	  require_once 'classes/model/Route.php';
	  $oRoute = new Route();
	  switch ($aData['ROU_TYPE']) {
	  	case 'SEQUENTIAL':
	  	case 'SEC-JOIN':
        /*if ($aData['ROU_UID'] != '')
        {
	  	    $aFields['ROU_UID'] = $aData['ROU_UID'];
	  	  }*/
	  	  $aFields['PRO_UID']          = $aData['PROCESS'];
	  	  $aFields['TAS_UID']          = $aData['TASK'];
	  	  $aFields['ROU_NEXT_TASK']    = $aData['ROU_NEXT_TASK'];
	  	  $aFields['ROU_TYPE']         = $aData['ROU_TYPE'];
	  	  //$aFields['ROU_TO_LAST_USER'] = $aData['ROU_TO_LAST_USER'];
	  	  $oRoute->create($aFields);
	  	break;
	  	case 'SELECT':
	  	  foreach ($aData['GRID_SELECT_TYPE'] as $iKey => $aRow)
	  	  {
	  	  	/*if ($aRow['ROU_UID'] != '')
          {
	  	      $aFields['ROU_UID'] = $aRow['ROU_UID'];
	  	    }*/
	  	    $aFields['PRO_UID']          = $aData['PROCESS'];
	  	    $aFields['TAS_UID']          = $aData['TASK'];
	  	    $aFields['ROU_NEXT_TASK']    = $aRow['ROU_NEXT_TASK'];
	  	    $aFields['ROU_CASE']         = $iKey;
	  	    $aFields['ROU_TYPE']         = $aData['ROU_TYPE'];
	  	    $aFields['ROU_CONDITION']    = $aRow['ROU_CONDITION'];
	  	    //$aFields['ROU_TO_LAST_USER'] = $aRow['ROU_TO_LAST_USER'];
	  	    $oRoute->create($aFields);
	  	    unset($aFields);
	  	  }
	  	break;
	  	case 'EVALUATE':
	  	  foreach ($aData['GRID_EVALUATE_TYPE'] as $iKey => $aRow)
	  	  {
	  	  	/*if ($aRow['ROU_UID'] != '')
          {
	  	      $aFields['ROU_UID'] = $aRow['ROU_UID'];
	  	    }*/
	  	    $aFields['PRO_UID']          = $aData['PROCESS'];
	  	    $aFields['TAS_UID']          = $aData['TASK'];
	  	    $aFields['ROU_NEXT_TASK']    = $aRow['ROU_NEXT_TASK'];
	  	    $aFields['ROU_CASE']         = $iKey;
	  	    $aFields['ROU_TYPE']         = $aData['ROU_TYPE'];
	  	    $aFields['ROU_CONDITION']    = $aRow['ROU_CONDITION'];
	  	    //$aFields['ROU_TO_LAST_USER'] = $aRow['ROU_TO_LAST_USER'];
	  	    $oRoute->create($aFields);
	  	    unset($aFields);
	  	  }
	  	break;
	  	case 'PARALLEL':
	  	  foreach ($aData['GRID_PARALLEL_TYPE'] as $iKey => $aRow)
	  	  {
	  	  	/*if ($aRow['ROU_UID'] != '')
          {
	  	      $aFields['ROU_UID'] = $aRow['ROU_UID'];
	  	    }*/
	  	    $aFields['PRO_UID']       = $aData['PROCESS'];
	  	    $aFields['TAS_UID']       = $aData['TASK'];
	  	    $aFields['ROU_NEXT_TASK'] = $aRow['ROU_NEXT_TASK'];
	  	    $aFields['ROU_CASE']      = $iKey;
	  	    $aFields['ROU_TYPE']      = $aData['ROU_TYPE'];
	  	    $oRoute->create($aFields);
	  	    unset($aFields);
	  	  }
	  	break;
	  	case 'PARALLEL-BY-EVALUATION':
	  	  foreach ($aData['GRID_PARALLEL_EVALUATION_TYPE'] as $iKey => $aRow)
	  	  {
	  	  	/*if ($aRow['ROU_UID'] != '')
          {
	  	      $aFields['ROU_UID'] = $aRow['ROU_UID'];
	  	    }*/
	  	    $aFields['PRO_UID']       = $aData['PROCESS'];
	  	    $aFields['TAS_UID']       = $aData['TASK'];
	  	    $aFields['ROU_NEXT_TASK'] = $aRow['ROU_NEXT_TASK'];
	  	    $aFields['ROU_CASE']      = $iKey;
	  	    $aFields['ROU_TYPE']      = $aData['ROU_TYPE'];
	  	    $aFields['ROU_CONDITION'] = $aRow['ROU_CONDITION'];
	  	    $oRoute->create($aFields);
	  	    unset($aFields);
	  	  }
	  	break;
	  }
	break;
}
?>