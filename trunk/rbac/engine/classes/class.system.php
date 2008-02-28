<?php
G::LoadSystem('error');

////////////////////////////////////////////////////
// RBAC_System - Systems class
//
// Class to manipulate the Systems in RBAC
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * RBAC_System - Systems class
 * @package RBAC
 * @author Julio Cesar Laura Avendaño
 * @copyright 2007 COLOSA
 */

class RBAC_System extends DBTable
{
	/**
   * Auxiliar object
   */
  var $oObject = null;

	/*
	* Constructor
	* @param object $oConnection
	* @return variant
	*/
	function RBAC_System($oConnection = null)
	{
		if ($oConnection)
		{
			return parent::setTo($oConnection, 'SYSTEMS', array('SYS_UID'));
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
      return parent::setTo($oConnection, 'SYSTEMS', array('SYS_UID'));
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_DBCONNECTION,
    	                        null,
    	                        null,
    	                        'You tried to call to a setTo method without send an instance of DBConnection!',
    	                        'G_Error',
    	                        true);
    }
  }

  /*
	* Load the system information
	* @param string $sUID
	* @return variant
	*/
	function load($sUID = '')
  {
    if ($sUID !== '')
  	{
  	  return parent::load($sUID);
  	}
  	else
  	{
  		return PEAR::raiseError(null,
    	                        G_ERROR_SYSTEM_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a load method without send the System UID!',
    	                        'G_Error',
    	                        true);
  	}
  }

  /*
	* Insert or update a system data
	* @param string $sUID
	* @return variant
	*/
	function save($sUID = '')
  {
  	if ($sUID !== '')
  	{
  		$this->Fields['SYS_UID'] = $sUID;
  		$this->is_new = false;
  	}
    return parent::save();
  }

  /*
	* Delete a system
	* @param string $sUID
	* @return variant
	*/
	function delete($sUID = '')
  {
  	if ($sUID !== '')
  	{
  	  $this->Fields['SYS_UID'] = $sUID;
  	  $this->is_new = false;
  	  return parent::delete();
  	}
  	else
  	{
  		return PEAR::raiseError(null,
    	                        G_ERROR_SYSTEM_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a delete method without send the System UID!',
    	                        'G_Error',
    	                        true);
  	}
  }

  /*
	* Load the system information using the system code
	* @param string $sCode
	* @return variant
	*/
	function loadByCode($sCode = '')
  {
    if ($sCode !== '')
  	{
  		$this->table_keys = array('SYS_CODE');
  		$this->oObject    = parent::load($sCode);
  		$this->table_keys = array('SYS_UID');
  		return $this->oObject;
  	}
  	else
  	{
  		return PEAR::raiseError(null,
    	                        G_ERROR_SYSTEM_CODE,
    	                        null,
    	                        null,
    	                        'You tried to call to a loadByCode method without send the System Code!',
    	                        'G_Error',
    	                        true);
  	}
  }
}
?>