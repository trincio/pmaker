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
// It works with the table APP_DELEGATION in a WF dataBase
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * APP_DELEGATION - AppDelegation class
 * @package ProcessMaker
 * @author Aldo Mauricio Veliz Valenzuela
 * @copyright 2007 COLOSA
 */

G::LoadClass( "pmObject" );

class AppDelegationOld extends DBTable
{
	/*
	* Constructor
	* @param object $oConnection
	* @return variant
	*/
	function AppDelegation($oConnection = null)
	{
		if ($oConnection)
		{
			return parent::setTo($oConnection, 'APP_DELEGATION', array('APP_UID', 'DEL_INDEX'));
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
  function SetTo($oConnection = null)
  {
  	if ($oConnection)
		{
			return parent::setTo($oConnection, 'APP_DELEGATION', array('APP_UID', 'DEL_INDEX'));
		}
		else
		{
			return;
		}
	}

  /*
	* Load the delegation information
	* @param string $sApplicationUID
	* @param integer $iDelegationIndex
	* @return variant
	*/
	function load($sApplicationUID = '', $iDelegationIndex = 0)
  {
    if ($sApplicationUID !== '' && $iDelegationIndex !== '')
  	{
  		return parent::load(array('APP_UID' => $sApplicationUID, 'DEL_INDEX' => $iDelegationIndex));
  	}
  	else
  	{
  		return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a load method without send the Application UID and Process UID and Delegation index!',
    	                        'G_Error',
    	                        true);
  	}
  }

  /**
	 * Save the Fields in APP_DELEGATION
   * @param array $aData
   * @return integer
  **/

  function save ($aData)
  {
		$this->Fields = array(
                          'APP_UID'           => (isset($aData['APP_UID'])           ? $aData['APP_UID']           : $this->Fields['APP_UID']),
                          'PRO_UID'           => (isset($aData['PRO_UID'])           ? $aData['PRO_UID']           : $this->Fields['PRO_UID']),
                          'DEL_INDEX'         => (isset($aData['DEL_INDEX'])         ? $aData['DEL_INDEX']         : $this->Fields['DEL_INDEX']),
                          'DEL_PREVIOUS'      => (isset($aData['DEL_PREVIOUS'])      ? $aData['DEL_PREVIOUS']      : $this->Fields['DEL_PREVIOUS']),
                          'TAS_UID'           => (isset($aData['TAS_UID'])           ? $aData['TAS_UID']           : $this->Fields['TAS_UID']),
                          'USR_UID'           => (isset($aData['USR_UID'])           ? $aData['USR_UID']           : $this->Fields['USR_UID']),
                          'DEL_TYPE'          => (isset($aData['DEL_TYPE'])          ? $aData['DEL_TYPE']          : (isset($this->Fields['DEL_TYPE']) ? $this->Fields['DEL_TYPE'] : 'NORMAL')),
                          'DEL_PRIORITY'      => (isset($aData['DEL_PRIORITY'])      ? $aData['DEL_PRIORITY']      : $this->Fields['DEL_PRIORITY']),
                          'DEL_THREAD'        => (isset($aData['DEL_THREAD'])        ? $aData['DEL_THREAD']        : $this->Fields['DEL_THREAD']),
                          'DEL_THREAD_STATUS' => (isset($aData['DEL_THREAD_STATUS']) ? $aData['DEL_THREAD_STATUS'] : (isset($this->Fields['DEL_THREAD_STATUS']) ? $this->Fields['DEL_THREAD_STATUS'] : 'OPEN')),
                          'DEL_DELEGATE_DATE' => (isset($aData['DEL_DELEGATE_DATE']) ? $aData['DEL_DELEGATE_DATE'] : $this->Fields['DEL_DELEGATE_DATE']),
                          'DEL_INIT_DATE'     => (isset($aData['DEL_INIT_DATE'])     ? $aData['DEL_INIT_DATE']     : $this->Fields['DEL_INIT_DATE']),
                          'DEL_TASK_DUE_DATE' => (isset($aData['DEL_TASK_DUE_DATE']) ? $aData['DEL_TASK_DUE_DATE'] : $this->Fields['DEL_TASK_DUE_DATE']),
                          'DEL_FINISH_DATE'   => (isset($aData['DEL_FINISH_DATE'])   ? $aData['DEL_FINISH_DATE']   : $this->Fields['DEL_FINISH_DATE'])
                          );

    if (!isset($aData['DEL_INDEX']))
    {
    	$this->Fields['DEL_INDEX'] = 1;
    	$this->is_new = true;
    }
    else
    {
      if ($this->Fields['DEL_INDEX'] != '')
      {
        
      	//$this->is_new = false;
      }
      else
      {
      	$this->Fields['DEL_INDEX'] = 1;
      	$this->is_new = true;
      }
    }
  	parent::save();

		return $this->Fields['DEL_INDEX'];
  }

 /*
	* Delete a delegation
	* @param string $sApplicationUID
	* @param integer $iDelegationIndex
	* @return variant
	*/
	function delete($sApplicationUID = '', $iDelegationIndex = 0)
  {
  	if ($sApplicationUID !== '' && $iDelegationIndex !== '')
		{
  	  $this->Fields['APP_UID']   = $sApplicationUID;
  	  $this->Fields['DEL_INDEX'] = $iDelegationIndex;
  	  return parent::delete();
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a delete method without send the Application UID and Process UID and Delegation index!',
    	                        'G_Error',
    	                        true);
    }
  }
}

?>