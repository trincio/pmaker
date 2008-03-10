<?php
/**
 * class.usersPermissions.php
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
// RBAC_UsersPermissions - Users Permissions class
//
// Class to manipulate the Permissions of the Users in RBAC
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * RBAC_UsersPermissions - Users Permissions class
 * @package RBAC
 * @author Julio Cesar Laura Avendaño
 * @copyright 2007 COLOSA
 */

class RBAC_UsersPermissions extends RBAC_User
{

  /**
   *
   * @access private
   * @var $userObj
   */
   var $systemObj;
   var $usersRolesObj;

	/**
	 * array of Permissions
   *
   * @access private
   * @var array $Permissions
   */
  var $aPermissions = "";


	/**
   * Auxiliar object
   */
  var $oObject = null;


	/*
	* Get all the permissions of a user
	* @param string $sUserUID
	* @param string $sSystemUID
	* @return variant
	*/
	function getPermissions($sUserUID, $sSystemUID = '')
  {
  	if ($sUserUID !== '')
  	{
  		if ($sSystemUID !== '')
  		{
  			$oDataset = $this->_dbses->Execute(" SELECT P.* ".
  																				 " FROM PERMISSIONS AS P ".
  																				 " LEFT JOIN ROLES_PERMISSIONS AS RP ON (P.PER_UID = RP.PER_UID) ".
  																				 " LEFT JOIN ROLES AS R ON (RP.ROL_UID = R.ROL_UID) ".
  																				 " LEFT JOIN USERS_ROLES AS UR ON (R.ROL_UID = UR.ROL_UID) ".
  																				 " WHERE R.ROL_SYSTEM = '" . $sSystemUID . "' AND ".
  																				 " UR.USR_UID = '" . $sUserUID . "' ".
  																				 " AND P.PER_STATUS = 1 AND R.ROL_STATUS = 1");
  		}
  		else
  		{
  			$oDataset = $this->_dbses->Execute(" SELECT P.* ".
  																				 " FROM PERMISSIONS AS P ".
  																				 " LEFT JOIN ROLES_PERMISSIONS AS RP ON (P.PER_UID = RP.PER_UID) ".
  																				 " LEFT JOIN ROLES AS R ON (RP.ROL_UID = R.ROL_UID) ".
  																				 " LEFT JOIN USERS_ROLES AS UR ON (R.ROL_UID = UR.ROL_UID) ".
  																				 " WHERE UR.USR_UID = '" . $sUserUID . "' AND ".
  																				 " P.PER_STATUS = 1 AND R.ROL_STATUS = 1");
  		}
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
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a getPermissions method without send the User UID!',
    	                        'G_Error',
    	                        true);
    }
  }

  /*
	* Verify if a user has a permission
	* @param string $sUserUID
	* @param string $sPermisionUID
	* @return variant
	*/
	function verifyPermission($sUserUID, $sPermisionUID)
  {
  	if ($sUserUID !== '')
  	{
  		if ($sPermisionUID !== '')
  		{
  		  $oDataset = $this->_dbses->Execute("SELECT P.* FROM PERMISSIONS AS P LEFT JOIN ROLES_PERMISSIONS AS RP ON (P.PER_UID = RP.PER_UID) LEFT JOIN ROLES AS R ON (RP.ROL_UID = R.ROL_UID) LEFT JOIN USERS_ROLES AS UR ON (R.ROL_UID = UR.ROL_UID) WHERE UR.USR_UID = '" . $sUserUID . "' AND P.PER_UID = '" . $sPermisionUID . "' AND P.PER_STATUS = 1 AND R.ROL_STATUS = 1");
  		  $aRow     = $oDataset->Read();
  		  return is_array($aRow);
  	  }
  	  else
  	  {
  	  	return PEAR::raiseError(null,
    	                          G_ERROR_PERMISSION_UID,
    	                          null,
    	                          null,
    	                          'You tried to call to a verifyPermission method without send the Permission UID!',
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
    	                        'You tried to call to a verifyPermission method without send the User UID!',
    	                        'G_Error',
    	                        true);
    }
  }

  /*
	* Verify if a user has a permission using the permission code
	* @param string $sUserUID
	* @param string $sPermisionCode
	* @return variant
	*/
	function verifyPermissionByCode($sUserUID, $sPermisionCode)
  {
  	if ($sUserUID !== '')
  	{
  		if ($sPermisionCode !== '')
  		{
  		  $oDataset = $this->_dbses->Execute("SELECT P.* FROM PERMISSIONS AS P LEFT JOIN ROLES_PERMISSIONS AS RP ON (P.PER_UID = RP.PER_UID) LEFT JOIN ROLES AS R ON (RP.ROL_UID = R.ROL_UID) LEFT JOIN USERS_ROLES AS UR ON (R.ROL_UID = UR.ROL_UID) WHERE UR.USR_UID = '" . $sUserUID . "' AND P.PER_CODE = '" . strtoupper($sPermisionCode) . "' AND P.PER_STATUS = 1 AND R.ROL_STATUS = 1");
  		  $aRow     = $oDataset->Read();
  		  return is_array($aRow);
  	  }
  	  else
  	  {
  	  	return PEAR::raiseError(null,
    	                          G_ERROR_PERMISSION_CODE,
    	                          null,
    	                          null,
    	                          'You tried to call to a verifyPermissionByCode method without send the Permission Code!',
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
    	                        'You tried to call to a verifyPermissionByCode method without send the User UID!',
    	                        'G_Error',
    	                        true);
    }
  }

  /**
	 * Get in array the user permissions
   *
   *
   * @author Aldo Mauricio Veliz Valenzuela
   * @access public

   * @param  string $uid    id of User
   * @param  string $system    Code of System
   * @return
   *   0: You get a array of permissions
   *  -1: System doesn't exists
   *  -2: The user hasn't a role
  */
  function userLoadPermissions ($uid, $system) {

    $this->aPermissions = array ();

    $dbcOther = new DBConnection( DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME , null, null ,DB_ERROR_SHOWALL_AND_CONTINUE );
		//include clase RBAC_System
    $file = PATH_RBAC .  "class.system.php";
    require_once( $file );
    $this->systemObj = new RBAC_System;
    $this->systemObj->SetTo( $dbcOther );

    $this->systemObj->loadByCode(strtoupper ($system));
    $sytemFields = $this->systemObj->Fields;

    if ( !is_array ($sytemFields) ) {
      return -1;
    }
    $systemid = (isset($sytemFields['UID']) ? $sytemFields['UID'] : '');

 		//include clase RBAC_UsersRoles
    $file = PATH_RBAC .  "class.usersRoles.php";
    require_once( $file );
    $this->usersRolesObj = new RBAC_UsersRoles;
    $this->usersRolesObj->SetTo($dbcOther);
		$roles = $this->usersRolesObj->getRolesBySystem($uid,$system);

    if ( !is_array ($roles) ) {
      return -2;
    }

    $rolid = "ROL_UID = '".$roles['ROL_UID']."'";

    //Get fathers-roles .

    $parents = $this->usersRolesObj->getAllParents ( $roles['ROL_UID'] );
    $parent = explode(',',$parents);

    if(is_array($parent))
    foreach($parent as $key => $val)
    	$rolid = $rolid." OR ROL_UID = '".$val."'";

    //Get all of Permissions
    $stQry = "SELECT PER_CODE from ROLES_PERMISSIONS AS RP LEFT JOIN PERMISSIONS AS P ON (P.PER_UID = RP.PER_UID) " .
             "WHERE " . $rolid ;
    $dset = $this->_dbses->Execute( $stQry );

    $row = $dset->Read();
    while ( is_array ($row)  ) {
      array_push ( $this->aPermissions, $row['PER_CODE'] );
      $row = $dset->Read();
    }
    return 0;
  }


  /**
   * Verify if a user has a permissions
   *
   * @author Aldo Mauricio Veliz Valenzuela
   * @access public
   * @param  string $uid    id of User
   * @param  string $app    Code of System
   * @param  string $perm   id of Permissions
   * @return
   *    1: return 1 if the user has a permission
   *   -1: System doesn't exists
   *   -2: The User hasn't role
   *   -3: The user hasn't a permission.
   */
  function userCanAccess ($uid, $system, $perm)
  {
  	if ((int)$uid != 0)
  	{
      if(!is_array ($this->aPermissions))
      {
        $ret = $this->userLoadPermissions($uid,$system);

      }
      if ($ret == 0) {
        $ret = -1;
        for ($i = 0; $i < count ($this->aPermissions); $i++) {
          if ($this->aPermissions[$i] == $perm) $ret = 1;
        }
      }
    }
    else
    {
    	$ret = -2;
    }
    return $ret;
  }

}
?>
