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