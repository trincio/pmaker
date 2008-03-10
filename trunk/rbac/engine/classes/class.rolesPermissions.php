<?php
/**
 * class.rolesPermissions.php
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
G::LoadClassRBAC('role');

////////////////////////////////////////////////////
// RBAC_RolesPermissions - Roles Permissions class
//
// Class to manipulate the Permissions of the Roles in RBAC
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * RBAC_RolesPermissions - Roles Permissions class
 * @package RBAC
 * @author Julio Cesar Laura Avendaño
 * @copyright 2007 COLOSA
 */

class RBAC_RolesPermissions extends RBAC_Role
{
	/**
   * Auxiliar object
   */
  var $oObject = null;

	/*
	* Get all the permissions of a role
	* @param string $sRoleUID
	* @return variant
	*/
	function getPermissions($sRoleUID)
  {
  	if ($sRoleUID !== '')
  	{
      $oDataset = $this->_dbses->Execute("SELECT P.* FROM ROLES_PERMISSIONS AS RP LEFT JOIN PERMISSIONS AS P ON (RP.PER_UID = P.PER_UID) WHERE RP.ROL_UID = '" . $sRoleUID . "' AND P.PER_STATUS = 1");
      if (method_exists($oDataset, 'Read'))
      {
        $aPermissions = null;
        while ($aRow = $oDataset->Read())
        {
        	$aPermissions[] = $aRow;
        }
        return $aPermissions;
      }
      else
      {
      	return $oDataset;
      }
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_ROLE_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a getPermissions method without send the Role UID!',
    	                        'G_Error',
    	                        true);
    }
  }

	/*
	* Delete all the permissions of a role
	* @param string $sRoleUID
	* @return variant
	*/
	function deletePermissions($sRoleUID)
  {
  	if ($sRoleUID !== '')
  	{
      return $this->_dbses->Execute("DELETE FROM ROLES_PERMISSIONS WHERE ROL_UID = '" . $sRoleUID . "'");
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_ROLE_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a deletePermissions method without send the Role UID!',
    	                        'G_Error',
    	                        true);
    }
  }

  /*
	* Add a permission to role
	* @param string $sRoleUID
	* @param string $sPermissionUID
	* @return variant
	*/
	function addPermissionToRole($sRoleUID, $sPermissionUID)
  {
  	if ($sRoleUID !== '')
  	{
  		if ($sPermissionUID !== '')
  		{
  		  $aPermisions = $this->getPermissions($sRoleUID);
  		  $bAssigned   = false;
  		  if (is_array($aPermisions))
  		  {
  		    foreach ($aPermisions as $sKey => $aValues)
  		    {
  		    	if ($aValues['PER_UID'] === $sPermissionUID)
  		    	{
  		    		$bAssigned = true;
  		    	}
  		    }
  		  }
  		  if (!$bAssigned)
  		  {
  		    $this->Fields = array();
          return $this->_dbses->Execute("INSERT INTO ROLES_PERMISSIONS (ROL_UID, PER_UID) VALUES ('" . $sRoleUID . "', '" . $sPermissionUID . "')");
        }
        else
        {
        	return PEAR::raiseError(null,
    	                            G_ERROR_ALREADY_ASSIGNED,
    	                            null,
    	                            null,
    	                            'Permission already assigned!',
    	                            'G_Error',
    	                            true);
        }
      }
      else
      {
      	return PEAR::raiseError(null,
    	                          G_ERROR_PERMISSION_UID,
    	                          null,
    	                          null,
    	                          'You tried to call to a addPermissionToRole method without send the Permission UID!',
    	                          'G_Error',
    	                          true);
      }
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_ROLE_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a addPermissionToRole method without send the Role UID!',
    	                        'G_Error',
    	                        true);
    }
  }

  /*
	* Delete a permission of the role
	* @param string $sRoleUID
	* @param string $sPermissionUID
	* @return variant
	*/
	function deletePermissionOfTheRole($sRoleUID, $sPermissionUID)
  {
  	if ($sRoleUID !== '')
  	{
  		if ($sPermissionUID !== '')
  		{
        return $this->_dbses->Execute("DELETE FROM ROLES_PERMISSIONS WHERE ROL_UID = '" . $sRoleUID . "' AND PER_UID = '" . $sPermissionUID . "'");
      }
      else
      {
      	return PEAR::raiseError(null,
    	                          G_ERROR_PERMISSION_UID,
    	                          null,
    	                          null,
    	                          'You tried to call to a deletePermissionOfTheRole method without send the Permission UID!',
    	                          'G_Error',
    	                          true);
      }
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_ROLE_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a deletePermissionOfTheRole method without send the Role UID!',
    	                        'G_Error',
    	                        true);
    }
  }
}
?>