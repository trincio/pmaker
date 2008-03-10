<?php
/**
 * class.role.php
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
G::LoadSystem('error');

////////////////////////////////////////////////////
// RBAC_Role - Roles class
//
// Class to manipulate the roles in RBAC
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * RBAC_Role - Roles class
 * @package RBAC
 * @author Julio Cesar Laura Avendaño
 * @copyright 2007 COLOSA
 */

class RBAC_Role extends DBTable
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
	function RBAC_Role($oConnection = null)
	{
		if ($oConnection)
		{
			return parent::setTo($oConnection, 'ROLES', array('ROL_UID'));
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
      return parent::setTo($oConnection, 'ROLES', array('ROL_UID'));
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
	* Load the role information
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
    	                        G_ERROR_ROLE_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a load method without send the Role UID!',
    	                        'G_Error',
    	                        true);
  	}
  }

  /*
	* Insert or update a role data
	* @param string $sUID
	* @return variant
	*/
	function save($sUID = '')
  {
  	if ($sUID !== '')
  	{
  		$this->Fields['ROL_UID'] = $sUID;
  		$this->is_new = false;
  	}
    return parent::save();
  }

  /*
	* Delete a role
	* @param string $sUID
	* @return variant
	*/
	function delete($sUID = '')
  {
  	if ($sUID !== '')
  	{
  	  $this->Fields['ROL_UID'] = $sUID;
  	  $this->is_new = false;
  	  return parent::delete();
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_ROLE_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a delete method without send the Role UID!',
    	                        'G_Error',
    	                        true);
    }
  }

  /*
	* Load the role information using the role code
	* @param string $sCode
	* @return variant
	*/
	function loadByCode($sCode = '')
  {
    if ($sCode !== '')
  	{
  		$this->table_keys = array('ROL_CODE');
  	  $this->oObject    = parent::load($sCode);
  		$this->table_keys = array('ROL_UID');
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
}
?>