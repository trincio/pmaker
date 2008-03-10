<?php
/**
 * class.user.php
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
//
// It works with the table user in a WF dataBase
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * User - User class
 * @package ProcessMaker
 * @author Aldo Mauricio Veliz Valenzuela
 * @copyright 2007 COLOSA
 */
G::LoadClassRBAC('user');
G::LoadClassRBAC('role');
G::LoadClassRBAC('usersRoles');

class User extends RBAC_User
{
  function SetTo( $oConnection )
  {
  	parent::setTo($oConnection, 'USERS', array('USR_UID'));
  }

  /**
	 * Save the Fields in USERS
   *
   *
   * @author Aldo Mauricio Veliz Valenzuela
   * @access public

   * @param  array $fields    id of User
   *
  **/

  function Save ($fields)
  {
    if(isset($fields['USR_UID'])){
      $proUsers=new Users();
      $proUsers->update($fields);
  	}else{
      $proUsers=new Users();
      $proUsers->create($fields);
      $proUsers->update($fields);
  	}
    $fields=$proUsers->toArray(BasePeer::TYPE_FIELDNAME);
    $this->Fields=$fields;

  	$dbcOther = new DBConnection( DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME , null, null ,DB_ERROR_SHOWALL_AND_CONTINUE );
		parent::setTo( $dbcOther );
		$this->Fields = array(
														'USR_USERNAME'     => (isset($fields['USR_USERNAME'])?$fields['USR_USERNAME']:''),
														'USR_PASSWORD'     => (isset($fields['USR_PASSWORD'])?$fields['USR_PASSWORD']:''),
														'USR_FIRSTNAME'    => (isset($fields['USR_FIRSTNAME'])?strtoupper( $fields['USR_FIRSTNAME']):'' ),
														'USR_LASTNAME'     => (isset($fields['USR_LASTNAME'])?strtoupper( $fields['USR_LASTNAME']):'' ),
														'USR_EMAIL'        => (isset($fields['USR_EMAIL'])?$fields['USR_EMAIL']:''),
														'USR_DUE_DATE'     => (isset($fields['USR_DUE_DATE'])?$fields['USR_DUE_DATE']:''),
														'USR_CREATE_DATE'  => (isset($fields['USR_CREATE_DATE'])?$fields['USR_CREATE_DATE']:''),
														'USR_UPDATE_DATE'  => (isset($fields['USR_UPDATE_DATE'])?$fields['USR_UPDATE_DATE']:''),
														'USR_STATUS'       => (isset($fields['USR_STATUS'])?$fields['USR_STATUS']:'') );
    $oRole = new RBAC_Role($dbcOther);
  	$aRol  = $oRole->loadByCode($fields['USR_ROLE']);
    $usersRoles = new RBAC_UsersRoles($dbcOther);
    if(isset($fields['USR_UID']))
    {
			parent::Save($fields['USR_UID']);
			$usersRoles->deleteRoles($fields['USR_UID']);
    }
  	else{
  		$this->Fields['USR_UID'] = $uid;
  		parent::Save();
  	}
  	$usersRoles->addRoleToUser($this->Fields['USR_UID'], $aRol['ROL_UID']);
  }
  /*
	* Verify if a username already exists
	* @param string $sUsername
	* @return boolean
	*/
  function usernameExists($sUsername)
  {
    try
    {
  		$criteria = new Criteria(UsersPeer::DATABASE_NAME);
  		$criteria->add(UsersPeer::USR_USERNAME, $sUsername);
  		$v = UsersPeer::doSelectOne($criteria);
  		return isset($v);
  	}
  	catch(Exception $e)
  	{
  	  trow($e);
  	}
  }
}

?>