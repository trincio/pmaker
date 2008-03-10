<?php
/**
 * class.systemsRoles.php
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
G::LoadClassRBAC('system');

////////////////////////////////////////////////////
// RBAC_SystemsRoles - Systems Roles class
//
// Class to manipulate the Roles of the Systems in RBAC
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * RBAC_SystemsRoles - Systems Roles class
 * @package RBAC
 * @author Julio Cesar Laura Avendaño
 * @copyright 2007 COLOSA
 */

class RBAC_SystemsRoles extends RBAC_System
{
	/*
	* Get all the roles of a system
	* @param string $sSystemUID
	* @return variant
	*/
	function getRoles($sSystemUID)
  {
  	if ($sSystemUID !== '')
  	{
      $oDataset = $this->_dbses->Execute("SELECT * FROM ROLES WHERE ROL_SYSTEM = '" . $sSystemUID . "' AND ROL_STATUS = 1");
      if (method_exists($oDataset, 'Read'))
      {
      	$aRoles = null;
        while ($aRow = $oDataset->Read())
        {
        	$aRoles[] = $aRow;
        }
        return $aRoles;
      }
      else
      {
      	return $oDataset;
      }
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_SYSTEM_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a getRoles method without send the System UID!',
    	                        'G_Error',
    	                        true);
    }
  }

	/*
	* Delete all the roles of a system
	* @param string $sSystemUID
	* @return variant
	*/
	function deleteRoles($sSystemUID)
  {
  	if ($sSystemUID !== '')
  	{
      return $this->_dbses->Execute("DELETE FROM ROLES WHERE ROL_SYSTEM = '" . $sSystemUID . "'");
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_SYSTEM_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a deleteRoles method without send the System UID!',
    	                        'G_Error',
    	                        true);
    }
  }
}
?>