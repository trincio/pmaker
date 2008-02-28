<?php
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