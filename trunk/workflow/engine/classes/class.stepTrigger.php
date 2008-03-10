<?php
/**
 * class.stepTrigger.php
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
// It works with the table STEP_TRIGGER
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * StepTrigger - StepTrigger class
 * @package ProcessMaker
 * @author Julio Cesar Laura Avendaño
 * @copyright 2007 COLOSA
 */

G::LoadClass('pmObject');

class StepTrigger extends PmObject
{
  /*
	* Constructor
	* @param object $oConnection
	* @return variant
	*/
	function StepTrigger($oConnection = null)
	{
		if ($oConnection)
		{
			return parent::setTo($oConnection, 'STEP_TRIGGER', array('TAS_UID', 'STEP_UID', 'TRI_UID', 'ST_TYPE'));
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
			return parent::setTo($oConnection, 'STEP_TRIGGER', array('TAS_UID', 'STEP_UID', 'TRI_UID', 'ST_TYPE'));
		}
		else
		{
			return;
		}
	}

	/*
	* Load the step triggers information
	* @param string $sTask
	* @param string $sStep
	* @param string $sTrigger
	* @param string $sType
	* @return variant
	*/
	function load($sTask = '', $sStep = '', $sTrigger = '', $sType = '')
  {
    if (($sTask !== '') || ($sStep !== '') || ($sTrigger !== '') || ($sType !== ''))
  	{
  	  return parent::load(array('TAS_UID' => $sTask, 'STEP_UID' => $sStep, 'TRI_UID' => $sTrigger, 'ST_TYPE' => $sType));
  	}
  	else
  	{
  		return PEAR::raiseError(null,
    	                        G_ERROR_SYSTEM_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a load method without send the task UID or step UID or trigger UID or type!',
    	                        'G_Error',
    	                        true);
  	}
  }

  /*
	* Load all triggers using the step UID
	* @param string $sStep
	* @return variant
	*/
  function loadTriggers($sTask = '', $sStep = '', $sType = '')
  {
  	$aTriggers = array();
  	$oSession  = new DBSession($this->_dbc);
  	$oDataset  = $oSession->Execute("SELECT
  	                                  *
  	                                FROM
  	                                  STEP_TRIGGER AS ST
  	                                LEFT JOIN
  	                                  `TRIGGER` AS T
  	                                ON (
  	                                  ST.TRI_UID = T.TRI_UID
  	                                )
  	                                WHERE
  	                                  ST.TAS_UID  = '" . $sTask . "' AND
  	                                  ST.STEP_UID = '" . $sStep . "' AND
  	                                  ST_TYPE     = '" . $sType . "'
  	                                ORDER BY
  	                                  ST_POSITION");
    while ($aRow = $oDataset->Read())
    {
    	$aTriggers[] = $aRow;
    }
    return $aTriggers;
  }

  /*
	* Insert or update a system data
	* @param array $aData
	* @return variant
	*/
	function save($aData = array())
  {
		$this->Fields = array('TAS_UID'      => (isset($aData['TAS_UID'])      ? $aData['TAS_UID']             : $this->Fields['TAS_UID']),
		                      'STEP_UID'     => (isset($aData['STEP_UID'])     ? $aData['STEP_UID']            : $this->Fields['STEP_UID']),
													'TRI_UID'      => (isset($aData['TRI_UID'])      ? $aData['TRI_UID']             : $this->Fields['TRI_UID']) ,
													'ST_TYPE'      => (isset($aData['ST_TYPE'])      ? strtoupper($aData['ST_TYPE']) : $this->Fields['ST_TYPE']),
													'ST_CONDITION' => (isset($aData['ST_CONDITION']) ? $aData['ST_CONDITION']        : $this->Fields['ST_CONDITION']),
													'ST_POSITION'  => (isset($aData['ST_POSITION'])  ? $aData['ST_POSITION']         : $this->Fields['ST_POSITION']));
    if(isset($aData['TAS_UID']) && isset($aData['STEP_UID']) && isset($aData['TRI_UID']) && isset($aData['ST_TYPE']))
    {
    	$this->is_new = false;
    }
    return parent::save();
  }

	/*
	* Change the trigger position
	* @param string $sTask
	* @param string $sStep
	* @param string $sTrigger
	* @param string $sType
	* @param integer $iPosition
	* @return void
	*/
	function up($sTask = '', $sStep = '', $sTrigger = '', $sType = '', $iPosition = 0)
  {
  	if ($iPosition > 1)
  	{
  		//No se usa el "save" de la clase "dbtable" porque no se puede hacer un "set" de una llave
  		$oSession = new DBSession($this->_dbc);
  		$oSession->Execute("UPDATE
  		                      STEP_TRIGGER
  		                    SET
  		                      ST_POSITION = "  . $iPosition . "
  		                    WHERE
  		                      TAS_UID     = '" . $sTask . "' AND
  		                      STEP_UID    = '" . $sStep . "' AND
  		                      ST_TYPE     = '" . $sType . "' AND
  		                      ST_POSITION = "  . ($iPosition - 1));
  	  $oSession->Execute("UPDATE
  		                      STEP_TRIGGER
  		                    SET
  		                      ST_POSITION = "  . ($iPosition - 1) . "
  		                    WHERE
  		                      TAS_UID  = '" . $sTask    . "' AND
  		                      STEP_UID = '" . $sStep    . "' AND
  		                      TRI_UID  = '" . $sTrigger . "' AND
  		                      ST_TYPE  = '" . $sType    . "'");
  	}
  }

  /*
	* Change the trigger position
	* @param string $sTask
	* @param string $sStep
	* @param string $sTrigger
	* @param string $sType
	* @param integer $iPosition
	* @return void
	*/
  function down($sTask = '', $sStep = '', $sTrigger = '', $sType = '', $iPosition = 0)
  {
  	$oSession = new DBSession($this->_dbc);
  	$oDataset = $oSession->Execute("SELECT * FROM STEP_TRIGGER WHERE TAS_UID = '" . $sTask . "' AND STEP_UID = '" . $sStep . "' AND ST_TYPE = '" . $sType . "'");
  	if ($iPosition < $oDataset->count())
  	{
  		//No se usa el "save" de la clase "dbtable" porque no se puede hacer un "set" de una llave
  		$oSession->Execute("UPDATE
  		                      STEP_TRIGGER
  		                    SET
  		                      ST_POSITION = "  . $iPosition . "
  		                    WHERE
  		                      TAS_UID     = '" . $sTask    . "' AND
  		                      STEP_UID    = '" . $sStep . "' AND
  		                      ST_TYPE     = '" . $sType . "' AND
  		                      ST_POSITION = "  . ($iPosition + 1));
  	  $oSession->Execute("UPDATE
  		                      STEP_TRIGGER
  		                    SET
  		                      ST_POSITION = "  . ($iPosition + 1) . "
  		                    WHERE
  		                      TAS_UID  = '" . $sTask    . "' AND
  		                      STEP_UID = '" . $sStep    . "' AND
  		                      TRI_UID  = '" . $sTrigger . "' AND
  		                      ST_TYPE  = '" . $sType    . "'");
  	}
  }

  /*
	* Assign a trigger to step
	* @param string $sTask
	* @param string $sStep
	* @param string $sTrigger
	* @param string $sType
	* @param string $sCondition
	* @param integer $iPosition
	* @return void
	*/
  function assign($sTask = '', $sStep = '', $sTrigger = '', $sType = '', $sCondition = '', $iPosition = 0)
  {
  	$this->Fields['TAS_UID']      = $sTask;
  	$this->Fields['STEP_UID']     = $sStep;
  	$this->Fields['TRI_UID']      = $sTrigger;
  	$this->Fields['ST_TYPE']      = $sType;
  	$this->Fields['ST_CONDITION'] = $sCondition;
  	$this->Fields['ST_POSITION']  = $iPosition;
  	parent::save();
  }

  /*
	* Of to assign a trigger to step
	* @param string $sTask
	* @param string $sStep
	* @param string $sTrigger
	* @param string $sType
	* @param string $sCondition
	* @param integer $iPosition
	* @return void
	*/
  function ofToAssign($sTask = '', $sStep = '', $sTrigger = '', $sType = '', $iPosition = 0)
  {
  	$this->Fields['TAS_UID']  = $sTask;
  	$this->Fields['STEP_UID'] = $sStep;
  	$this->Fields['TRI_UID']  = $sTrigger;
  	$this->Fields['ST_TYPE']  = $sType;
  	$this->is_new = false;
  	parent::delete();
  	//No se usa el "save" de la clase "dbtable" porque no se puede hacer un "set" de una llave
  	$oSession = new DBSession($this->_dbc);
  	$oSession->Execute("UPDATE
  		                    STEP_TRIGGER
  		                  SET
  		                    ST_POSITION = ST_POSITION - 1
  		                  WHERE
  		                    TAS_UID     = '" . $sTask     . "' AND
  		                    STEP_UID    = '" . $sStep     . "' AND
  		                    ST_TYPE     = '" . $sType     . "' AND
  		                    ST_POSITION >  " . $iPosition);
  }

  /*
	* Of to assign a trigger of all steps
	* @param string $sTrigger
	* @return void
	*/
  function ofToAssignAll($sTrigger = '')
  {
  	$oSession = new DBSession($this->_dbc);
  	$oDataset = $oSession->Execute("SELECT * FROM STEP_TRIGGER WHERE TRI_UID = '" . $sTrigger . "'");
  	while ($aRow = $oDataset->Read())
  	{
  		$this->ofToAssign($aRow['STEP_UID'], $aRow['TRI_UID'], $aRow['ST_TYPE'], $aRow['ST_POSITION']);
  	}
  }
}

?>