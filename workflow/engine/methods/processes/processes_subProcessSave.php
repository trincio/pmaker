<?php
/**
 * processes_subProcessSave.php
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
 
global $RBAC;
switch ($RBAC->userCanAccess('PM_FACTORY')) {
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


$out = array();
for($i=1; $i<=count($_POST['form']['grid1']); $i++)
{  
		$out[$_POST['form']['grid1'][$i]['VAR_OUT1']]= $_POST['form']['grid1'][$i]['VAR_OUT2'];
}

$in = array();
for($j=1; $j<=count($_POST['form']['grid2']); $j++)
{
		$in[$_POST['form']['grid2'][$j]['VAR_IN1']] =  $_POST['form']['grid2'][$j]['VAR_IN2'];
}

require_once 'classes/model/Task.php';
$oTask= new Task();
$aTask=$oTask->load($_POST['form']['TASKS']);

require_once 'classes/model/SubProcess.php';
$oOP = new SubProcess();
$aData = array('SP_UID'          		 => $_POST['form']['SP_UID'],//G::generateUniqueID(),
               'PRO_UID'         		 => $aTask['PRO_UID'],
               'TAS_UID'         		 => $_POST['form']['TASKS'],
               'PRO_PARENT'      		 => $_POST['form']['PRO_PARENT'],
               'TAS_PARENT'					 => $_POST['form']['TAS_PARENT'],
               'SP_TYPE'   					 => 'SIMPLE',
               'SP_SYNCHRONOUS'   	 => $_POST['form']['SP_SYNCHRONOUS'],
               'SP_SYNCHRONOUS_TYPE' => 'ALL',
               'SP_SYNCHRONOUS_WAIT' => 0,
               'SP_VARIABLES_OUT'    => serialize($out),
               'SP_VARIABLES_IN'     => serialize($in),
               'SP_GRID_IN'          => '');
                    		
$oOP->update($aData);
    
//G::header('location: processes_Map?PRO_UID='. $_POST['form']['PRO_UID']);    
die;

  
  
  