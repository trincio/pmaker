<?php
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