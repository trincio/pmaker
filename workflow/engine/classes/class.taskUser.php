<?php
/**
 * class.taskUser.php
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
//
// It works with the table TASK_USER
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * TaskUser - TaskUser class
 * @package ProcessMaker
 * @author Julio Cesar Laura Avendaño
 * @copyright 2007 COLOSA
 */

G::LoadClass('pmObject');

class TaskUser extends PmObject
{
  /*
	* Constructor
	* @param object $oConnection
	* @return variant
	*/
	function TaskUser($oConnection = null)
	{
		if ($oConnection)
		{
			return parent::setTo($oConnection, 'TASK_USER', array('TAS_UID', 'USR_UID', 'TU_TYPE', 'TU_RELATION'));
		}
		else
		{
			return;
		}
	}

	/*
	* Set the Data Base connection
	* @param object $oConnection
	* @return variant
	*/
  function setTo($oConnection = null)
  {
  	if ($oConnection)
		{
			return parent::setTo($oConnection, 'TASK_USER', array('TAS_UID', 'USR_UID', 'TU_TYPE', 'TU_RELATION'));
		}
		else
		{
			return;
		}
	}

  /*
	* Assign a user to task
	* @param string $sTask
	* @param string $sUser
	* @param string $iType
	* @return void
	*/
  function assignUser($sTask = '', $sUser = '', $iType = '')
  {
  	$vAux = parent::load(array('TAS_UID' => $sTask, 'USR_UID' => $sUser, 'TU_TYPE' => $iType, 'TU_RELATION' => 1));
  	if (!is_array($vAux))
  	{
  		$this->Fields['TAS_UID']     = $sTask;
  	  $this->Fields['USR_UID']     = $sUser;
  	  $this->Fields['TU_TYPE']     = $iType;
  	  $this->Fields['TU_RELATION'] = 1;
  		$this->is_new                = true;
  		parent::save();
  	}
  }

  /*
	* Assign a group to task
	* @param string $sTask
	* @param string $sGroup
	* @param string $iType
	* @return void
	*/
  function assignGroup($sTask = '', $sGroup = '', $iType = '')
  {
  	$vAux = parent::load(array('TAS_UID' => $sTask, 'USR_UID' => $sGroup, 'TU_TYPE' => $iType, 'TU_RELATION' => 2));
  	if (!is_array($vAux))
  	{
  		$this->Fields['TAS_UID']     = $sTask;
  	  $this->Fields['USR_UID']     = $sGroup;
  	  $this->Fields['TU_TYPE']     = $iType;
  	  $this->Fields['TU_RELATION'] = 2;
  		$this->is_new                = true;
  		parent::save();
  	}
  	$oSession = new DBSession($this->_dbc);
  	$oDataset = $oSession->Execute("SELECT
  	                                  USR_UID
  	                                FROM
  	                                  GROUP_USER
  	                                WHERE
  	                                  GRP_UID = '" . $sGroup . "'");
  	while ($aRow = $oDataset->Read())
  	{
  	  $this->assignUser($sTask, $aRow['USR_UID'], $iType);
    }
  }

  /*
	* Of to assign a user from a task
	* @param string $sTask
	* @param string $sUser
	* @param integer $iType
	* @return void
	*/
  function ofToAssignUser($sTask = '', $sUser = '', $iType = 0)
  {
  	$this->Fields['TAS_UID']     = $sTask;
    $this->Fields['USR_UID']     = $sUser;
    $this->Fields['TU_TYPE']     = $iType;
    $this->Fields['TU_RELATION'] = 1;
  	parent::delete();
  }

  /*
	* Of to assign a group from a task
	* @param string $sTask
	* @param string $sGroup
	* @param integer $iType
	* @return void
	*/
  function ofToAssignGroup($sTask = '', $sGroup = '', $iType = 0)
  {
  	$this->Fields['TAS_UID']     = $sTask;
    $this->Fields['USR_UID']     = $sGroup;
    $this->Fields['TU_TYPE']     = $iType;
    $this->Fields['TU_RELATION'] = 2;
  	parent::delete();
  	$oSession = new DBSession($this->_dbc);
  	$oDataset = $oSession->Execute("SELECT
  	                                  USR_UID
  	                                FROM
  	                                  GROUP_USER
  	                                WHERE
  	                                  GRP_UID = '" . $sGroup . "'");
  	while ($aRow = $oDataset->Read())
  	{
  	  $this->ofToAssignUser($sTask, $aRow['USR_UID'], $iType);
    }
  }

  /*
	* Get the assigned users of a task
	* @param string $sTask
	* @return array
	*/
  function getUsersOfTask($sTask)
  {
  	$aUsers    = array();
  	$oSession  = new DBSession($this->_dbc);
    $oDataset  = $oSession->Execute("SELECT
                                       TU.USR_UID AS USR_UID,
                                       CONCAT(U.USR_LASTNAME, ' ', U.USR_FIRSTNAME) AS USR_FULLNAME
                                     FROM
                                       TASK_USER AS TU
                                     LEFT JOIN
                                       USERS AS U
                                     ON (
                                       TU.USR_UID = U.USR_UID
                                     )
                                     WHERE
                                       TU.TAS_UID     = '" . $sTask . "' AND
                                       TU.TU_TYPE     = 1 AND
                                       TU.TU_RELATION = 1 AND
                                       U.USR_STATUS   = 1");
    while ($aRow = $oDataset->Read())
    {
    	$aUsers[] = $aRow;
    }
    return $aUsers;
  }
}

?>