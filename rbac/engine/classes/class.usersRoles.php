<?php
/**
 * class.usersRoles.php
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
G::LoadClassRBAC('user');

////////////////////////////////////////////////////
// RBAC_UsersRoles - Users Roles class
//
// Class to manipulate the Roles of the Users in RBAC
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * RBAC_UsersRoles - Users Roles class
 * @package RBAC
 * @author Julio Cesar Laura Avendaño
 * @copyright 2007 COLOSA
 */

class RBAC_UsersRoles extends RBAC_User
{
	/**
   * Auxiliar object
   */
  var $oObject = null;

	/*
	* Get all the roles of a user
	* @param string $sUserUID
	* @param string $sSystemUID
	* @return variant
	*/
	function getRoles($sUserUID, $sSystemUID = '')
  {
  	if ($sUserUID !== '')
  	{
  		if ($sSystemUID !== '')
  		{
        $oDataset = $this->_dbses->Execute("SELECT R.* FROM USERS_ROLES AS UR LEFT JOIN ROLES AS R ON (UR.ROL_UID = R.ROL_UID) WHERE UR.USR_UID = '" . $sUserUID . "' AND R.ROL_SYSTEM = '" . $sSystemUID . "' AND R.ROL_STATUS = 1");
      }
      else
      {
      	$oDataset = $this->_dbses->Execute("SELECT R.* FROM USERS_ROLES AS UR LEFT JOIN ROLES AS R ON (UR.ROL_UID = R.ROL_UID) WHERE UR.USR_UID = '" . $sUserUID . "' AND R.ROL_STATUS = 1");
      }
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
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a getRoles method without send the User UID!',
    	                        'G_Error',
    	                        true);
    }
  }

 /*
	* Get role of a user in a System
	* @author Aldo Mauricio Veliz Valenzuela
	* @param string $sUserUID
	* @return array
	*/
	function getRolesBySystem($sUserUID,$sSystem)
  {
  	if ($sUserUID !== '')
  	{
  		$aRoles   = null;
      $oDataset = $this->_dbses->Execute(" SELECT R.* ".
                                         " FROM USERS_ROLES AS UR ".
                                         " LEFT JOIN ROLES AS R ON (UR.ROL_UID = R.ROL_UID) ".
                                         " LEFT JOIN SYSTEMS AS S ON (S.SYS_UID = R.ROL_SYSTEM) ".
                                         " WHERE UR.USR_UID = '" . $sUserUID . "' AND ".
                                         " S.SYS_CODE ='" . $sSystem . "' AND ".
                                         " R.ROL_STATUS = 1");
      $aRow = $oDataset->Read();
      return $aRow;
    }
    else
    {
    	return null;
    }
  }

	/*
	* Delete all the roles of a user
	* @param string $sUserUID
	* @return variant
	*/
	function deleteRoles($sUserUID)
  {
  	if ($sUserUID !== '')
  	{
      return $this->_dbses->Execute("DELETE FROM USERS_ROLES WHERE USR_UID = '" . $sUserUID . "'");
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a deletePermissions method without send the User UID!',
    	                        'G_Error',
    	                        true);
    }
  }

  /*
	* Add a role to user
	* @param string $sUserUID
	* @param string $sRoleUID
	* @return variant
	*/
	function addRoleToUser($sUserUID, $sRoleUID)
  {
  	if ($sUserUID !== '')
  	{
  		if ($sRoleUID !== '')
  		{
  		  $aRoles    = $this->getRoles($sUserUID);
  		  $bAssigned = false;
  		  if (is_array($aRoles))
  		  {
  		    foreach ($aRoles as $sKey => $aValues)
  		    {
  		    	if ($aValues['ROL_UID'] === $sRoleUID)
  		    	{
  		    		$bAssigned = true;
  		    	}
  		    }
  		  }
  		  if (!$bAssigned)
  		  {
  		    $this->Fields = array();
          return $this->_dbses->Execute("INSERT INTO USERS_ROLES (USR_UID, ROL_UID) VALUES ('" . $sUserUID . "', '" . $sRoleUID . "')");
        }
        else
        {
        	return PEAR::raiseError(null,
    	                            G_ERROR_ALREADY_ASSIGNED,
    	                            null,
    	                            null,
    	                            'Role already assigned!',
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
    	                          'You tried to call to a addRoleToUser method without send the Role UID!',
    	                          'G_Error',
    	                          true);
      }
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a addRoleToUser method without send the User UID!',
    	                        'G_Error',
    	                        true);
    }
  }

  /*
	* Delete a role of the user
	* @param string $sUserUID
	* @param string $sRoleUID
	* @return variant
	*/
	function deleteRoleOfTheUser($sUserUID, $sRoleUID)
  {
  	if ($sUserUID !== '')
  	{
  		if ($sRoleUID !== '')
  		{
        return $this->_dbses->Execute("DELETE FROM USERS_ROLES WHERE USR_UID = '" . $sUserUID . "' AND ROL_UID = '" . $sRoleUID . "'");
      }
      else
      {
      	return PEAR::raiseError(null,
    	                          G_ERROR_ROLE_UID,
    	                          null,
    	                          null,
    	                          'You tried to call to a deleteRoleOfTheUser method without send the Role UID!',
    	                          'G_Error',
    	                          true);
      }
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a deleteRoleOfTheUser method without send the User UID!',
    	                        'G_Error',
    	                        true);
    }
  }

  /**
   * Get all of inheritance roles
   *
   *
   * @author Aldo Mauricio Veliz Valenzuela
   * @access public
   * @param  integer $rolid       Role id
   * @return 1
   */
  function getAllParents ( $rolid )
  {
    $aux = "0";
    while ($rolid != 0 ) {
      $stQry = "select ROL_PARENT from ROLES WHERE ROL_UID = '".$rolid ."'";
      $dset = $this->_dbses->Execute( $stQry );
      $row = $dset->Read();
      $rolid = 0;
      if ( is_array ($row) ) {
      	if ( $row['ROL_PARENT'] != '0') {
	        $rolid = $row['ROL_PARENT'];
	    		$aux .= "," . $rolid;
	    	}
      }
    }
    return $aux;
  }
}
?>