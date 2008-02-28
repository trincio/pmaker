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

require_once ( "classes/model/Task.php" );
require_once ( "classes/model/AppDelegation.php" );

// 
// It works with the table STEP in a WF dataBase
//
// Copyright (C) 2007 COLOSA
//
////////////////////////////////////////////////////

/**
 * Step - Step class
 * @package ProcessMaker
 * @author Aldo Mauricio Veliz Valenzuela
 * @copyright 2007 COLOSA
 */

class Step 
{
  /*
	* Load the step information using the tasUid
	* @param string $sTasUid
	* @return variant
	*/
	function loadByTask($sTasUid = '')
  {
    if ($sTasUid !== '')
  	{
  		$this->table_keys = array('TAS_UID');
  	  $this->oObject    = parent::load($sTasUid);
  		$this->table_keys = array('STEP_UID');
  		return $this->oObject;
  	}
  	else
  	{
  		return PEAR::raiseError(null,
    	                        G_ERROR_ROLE_CODE,
    	                        null,
    	                        null,
    	                        'You tried to call to a loadByCode method without send the Role Code!',
    	                        'G_Error',
    	                        true);
  	}
  }

  /*
	* Load the step information using the Task UID, the type and the object UID
	* @param string $sTaskUID
	* @param string $sType
	* @param string $sUID
	* @return variant
	*/
  function loadByType($sTaskUID = '', $sType = '', $sUID)
  {
  	if (($sTaskUID !== '') && ($sType !== '') && ($sUID !== ''))
  	{
  		$this->table_keys = array('TAS_UID', 'STEP_TYPE_OBJ', 'STEP_UID_OBJ');
  		return parent::load(array('TAS_UID' => $sTaskUID, 'STEP_TYPE_OBJ' => $sType, 'STEP_UID_OBJ' => $sUID));
  	}
  	else
  	{
  		return PEAR::raiseError(null,
    	                        G_ERROR_ROLE_CODE,
    	                        null,
    	                        null,
    	                        'You tried to call to a loadByType method without send the "Task UID" or "Type" or "Object UID"!',
    	                        'G_Error',
    	                        true);
  	}
  }

  /*
	* Load the step information using the tasUid, type of step and return all of fields that is necesary to fill a dropdown
	* @param string $sTasUid, $sTypeStep
	* @return variant
	*/
	function stepsAssignedByType($sTasUid = '', $sTypeStep = '')
  {
    if (($sTasUid !== ''))
  	{
  		if($sTypeStep !== '')
  			$this->table_keys = array('TAS_UID','STEP_TYPE_OBJ');
  		else
  			$this->table_keys = array('TAS_UID');
  	  $this->oObject    = parent::load($sTasUid);
  	  $steps = array();
  	  while(is_array($this->Fields)){
  	  	$steps[] = $this->Fields;
  	  	$this->next();
  		}
  		$this->table_keys = array('STEP_UID');
  		return $steps;
  	}
  	else
  	{
  		return PEAR::raiseError(null,
    	                        G_ERROR_ROLE_CODE,
    	                        null,
    	                        null,
    	                        'You tried to call to a loadByTypeStep method without send the tasUid or the type of step!',
    	                        'G_Error',
    	                        true);
  	}
  }



  /**
	 * Save the Fields in PROCESS
   *
   *
   * @author Aldo Mauricio Veliz Valenzuela
   * @access public
	 * the parameter STEP_TYPE_OBJ will content the next values
	 * DYNAFORM //in a dataBase this option is default
	 * OUTPUT_DOCUMENT
	 * INPUT_DOCUMENT
	 * MESSAGE
   * @param  array $fields    id of User
   * @return string
   * return uid ReqDynaform
  **/

  function save ($fields)
  {
		  $this->Fields = array(  'PRO_UID'            => (isset($fields['PRO_UID'])           ? $fields['PRO_UID']                    : $this->Fields['PRO_UID']),
    													'TAS_UID'            => (isset($fields['TAS_UID'])           ? $fields['TAS_UID']                    : $this->Fields['TAS_UID']),
		   												'STEP_NAME_OBJ'      => (isset($fields['STEP_NAME_OBJ'])     ? $fields['STEP_NAME_OBJ']              : $this->Fields['STEP_NAME_OBJ']),
		   												'STEP_TYPE_OBJ'      => (isset($fields['STEP_TYPE_OBJ'])     ? $fields['STEP_TYPE_OBJ']              : ( isset ( $this->Fields['STEP_TYPE_OBJ']) ? $this->Fields['STEP_TYPE_OBJ'] : 'DYNAFORM' )) ,
		   												'STEP_UID_OBJ'       => (isset($fields['STEP_UID_OBJ'])      ? $fields['STEP_UID_OBJ']               : $this->Fields['STEP_UID_OBJ']),
				  										'STEP_CONDITION'     => (isset($fields['STEP_CONDITION'])    ? $fields['STEP_CONDITION']             : $this->Fields['STEP_CONDITION']),
					  									'STEP_POSITION'      => (isset($fields['STEP_POSITION'])     ? strtoupper( $fields['STEP_POSITION']) : $this->Fields['STEP_POSITION'] ) );

    //if is a new dynaform we need to generate the guid
    $uid = G::generateUniqueID();
		$this->is_new = true;

    if(isset($fields['STEP_UID'])){
    	$this->Fields['STEP_UID'] = $fields['STEP_UID'];
			$fields['CON_ID'] = $fields['STEP_UID'];
			$this->is_new = false;
		}else{
			$this->Fields['STEP_UID'] = $uid;
			$fields['CON_ID'] = $uid;
		}
		$uid = $this->Fields['STEP_UID'];

  	parent::save();

		 return $uid;

  }

	/*
	* Change the step position
	* @param string $sUID
	* @param string $sTaskUid
	* @param integer $iPosition
	* @return void
	*/
	function delete($sUID = '', $sTaskUid = '', $iPosition = 0)
  {
  	if ($sUID !== '')
		{
  	  $this->Fields['STEP_UID'] = $sUID;
  	  parent::delete();
  		$oSession = new DBSession($this->_dbc);
  		$oSession->Execute("UPDATE
  		                  	  STEP
  		                  	SET
  		                  	  STEP_POSITION = STEP_POSITION - 1
  		                  	WHERE
  		                  	  TAS_UID        = '" . $sTaskUid     . "' AND
  		                  	  STEP_POSITION  >  " . $iPosition);
  		G::LoadClass('stepTrigger');
  		$oStepTrigger = new StepTrigger($this->_dbc);
  		$oStepTrigger->table_keys = array('STEP_UID');
  		$oStepTrigger->Fields['STEP_UID'] = $sUID;
  		$oStepTrigger->is_new = true;
  		$oStepTrigger->delete();
  	  return;
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a delete method without send the Dynaform UID!',
    	                        'G_Error',
    	                        true);
    }
  }


	/*
	* Change the step position
	* @param string $sUID
	* @param string $sTaskUid
	* @param integer $iPosition
	* @return void
	*/
	function up($sUID = '', $sTaskUid = '',$iPosition = 0)
  {
  	if ($iPosition > 1)
  	{
  		//No se usa el "save" de la clase "dbtable" porque no se puede hacer un "set" de una llave
  		$oSession = new DBSession($this->_dbc);
  		$oSession->Execute("UPDATE
  		                      STEP
  		                    SET
  		                      STEP_POSITION = "  . $iPosition . "
  		                    WHERE
  		                      TAS_UID        = '" . $sTaskUid     . "' AND
  		                      STEP_POSITION = "  . ($iPosition - 1));
  	  $oSession->Execute("UPDATE
  		                      STEP
  		                    SET
  		                      STEP_POSITION = "  . ($iPosition - 1) . "
  		                    WHERE
  		                      STEP_UID    = '" . $sUID    . "' ");
  	}
  }

	/*
	* Change the step position
	* @param string $sUID
	* @param string $sTaskUid
	* @param integer $iPosition
	* @return void
	*/
  function down($sUID = '', $sTaskUid = '',$iPosition = 0)
  {
  	$oSession = new DBSession($this->_dbc);
  	$oDataset = $oSession->Execute("SELECT * FROM STEP WHERE TAS_UID = '" . $sTaskUid . "'");

  	if ($iPosition < $oDataset->count())
  	{
  		//No se usa el "save" de la clase "dbtable" porque no se puede hacer un "set" de una llave
  		$oSession->Execute("UPDATE
  		                      STEP
  		                    SET
  		                      STEP_POSITION = "  . $iPosition . "
  		                    WHERE
  		                      TAS_UID        = '" . $sTaskUid     . "' AND
  		                      STEP_POSITION = "  . ($iPosition + 1));
  	  $oSession->Execute("UPDATE
  		                      STEP
  		                    SET
  		                      STEP_POSITION = "  . ($iPosition + 1) . "
  		                    WHERE
  		                      STEP_UID    = '" . $sUID    . "' AND
  		                      TAS_UID     = '" . $sTaskUid . "' " );
  	}
  }

  /*
	* Get the previous step
	* @param string $sProcessUID
	* @param string $sApplicationUID
	* @param integer $iDelegationIndex
	* @param integer $iPosition
	* @return array
	*/
  function getPreviousStep($sProcessUID = '', $sApplicationUID = '', $iDelegationIndex = 0, $iPosition = 0)
  {
  	$oSession = new DBSession($this->_dbc);
  	//To do: change to class.delegation.php
  	$oDataset = $oSession->Execute("SELECT
  	                                  TAS_UID
  	                                FROM
  	                                  APP_DELEGATION
  	                                WHERE
  	                                  PRO_UID   = '" . $sProcessUID . "' AND
  	                                  APP_UID   = '" . $sApplicationUID . "' AND
  	                                  DEL_INDEX = "  . $iDelegationIndex);
  	$aRow     = $oDataset->Read();
  	$sTaskUID = $aRow['TAS_UID'];
  	//--
  	$aRow           = $oDataset->Read();
  	$iFirstStep     = 1;
  	if ($iPosition == 10000)
  	{
  		$oDataset = $oSession->Execute("SELECT
  	                                    MAX(STEP_POSITION) AS LAST_STEP
  	                                  FROM
  	                                    STEP
  	                                  WHERE
  	                                    PRO_UID = '" . $sProcessUID . "' AND
  	                                    TAS_UID = '" . $sTaskUID . "'");
  	  $aRow      = $oDataset->Read();
  	  $iPosition = (int)$aRow['LAST_STEP'];
  	}
  	else
  	{
  	  $iPosition -= 1;
    }
  	$aPreviousStep  = null;
  	if ($iPosition >= 1)
  	{
  		$this->table_keys = array('PRO_UID', 'TAS_UID', 'STEP_POSITION');
  		G::LoadClass('application');
  		$oApplication = new Application($this->_dbc);
  		$oApplication->load($sApplicationUID);
  		G::LoadClass('pmScript');
  		$oPMScript = new PMScript();
  		$oPMScript->setFields($oApplication->Fields['APP_DATA']);
  		while ($iPosition >= $iFirstStep)
  		{
  			$bAccessStep = false;
  		  $this->Load(array('PRO_UID' => $sProcessUID, 'TAS_UID' => $sTaskUID, 'STEP_POSITION' => $iPosition));
  		  if ($this->Fields['STEP_CONDITION'] !== '')
  		  {
  		  	$oPMScript->setScript($this->Fields['STEP_CONDITION']);
  		  	$bAccessStep = $oPMScript->evaluate();
  		  }
  		  else
  		  {
  		  	$bAccessStep = true;
  		  }
  		  if ($bAccessStep)
  		  {
  		  	switch ($this->Fields['STEP_TYPE_OBJ'])
  		  	{
  		  		case 'DYNAFORM':
  		  		  $sAction = 'EDIT';
  		  		break;
  		  		case 'OUTPUT_DOCUMENT':
  		  		  $sAction = 'GENERATE';
  		  		break;
  		  		case 'INPUT_DOCUMENT':
  		  		  $sAction = 'ATTACH';
  		  		break;
  		  		case 'MESSAGE':
  		  		  $sAction = '';
  		  		break;
  		  	}
  		  	$aPreviousStep = array('TYPE' => $this->Fields['STEP_TYPE_OBJ'], 'UID' => $this->Fields['STEP_UID_OBJ'], 'POSITION' => $this->Fields['STEP_POSITION'], 'PAGE' => 'cases_Step?TYPE=' . $this->Fields['STEP_TYPE_OBJ'] . '&UID=' . $this->Fields['STEP_UID_OBJ'] . '&POSITION=' . $this->Fields['STEP_POSITION'] . '&ACTION=' . $sAction);
  		  	$iPosition     = $iFirstStep;
  		  }
  		  $iPosition -= 1;
  		}
  	}
  	if (!$aPreviousStep)
  	{
  		$aPreviousStep = false;
  	}
  	return $aPreviousStep;
  }

}
?>
