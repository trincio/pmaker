<?php
G::LoadSystem('error');

////////////////////////////////////////////////////
// RBAC_Permission - Permissions class
//
// Class to manipulate the permissions in RBAC
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * RBAC_Permission - Permissions class
 * @package RBAC
 * @author Julio Cesar Laura Avendaño
 * @copyright 2007 COLOSA
 */

class RBAC_Permission extends DBTable
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
	function RBAC_Permission($oConnection = null)
	{
		if ($oConnection)
		{
			return parent::setTo($oConnection, 'PERMISSIONS', array('PER_UID'));
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
      return parent::setTo($oConnection, 'PERMISSIONS', array('PER_UID'));
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
	* Load the permission information
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
    	                        G_ERROR_PERMISSION_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a load method without send the Permission UID!',
    	                        'G_Error',
    	                        true);
  	}
  }

  /*
	* Insert or update a permission data
	* @param string $sUID
	* @return variant
	*/
	function save($sUID = '')
  {
  	if ($sUID !== '')
  	{
  		$this->Fields['PER_UID'] = $sUID;
  		$this->is_new = false;
  	}
    return parent::save();
  }

  /*
	* Delete a permission
	* @param string $sUID
	* @return variant
	*/
	function delete($sUID = '')
  {
  	if ($sUID !== '')
  	{
  	  $this->Fields['PER_UID'] = $sUID;
  	  $this->is_new = false;
  	  return parent::delete();
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_PERMISSION_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a delete method without send the Permission UID!',
    	                        'G_Error',
    	                        true);
    }
  }

  /*
	* Load the permission information using the permission code
	* @param string $sCode
	* @return variant
	*/
	function loadByCode($sCode = '')
  {
    if ($sCode !== '')
  	{
  		$this->table_keys = array('PER_CODE');
  	  $this->oObject    = parent::load($sCode);
  		$this->table_keys = array('PER_UID');
  		return $this->oObject;
  	}
  	else
  	{
  		return PEAR::raiseError(null,
    	                        G_ERROR_PERMISSION_CODE,
    	                        null,
    	                        null,
    	                        'You tried to call to a loadByCode method without send the Permission Code!',
    	                        'G_Error',
    	                        true);
  	}
  }
}
?>