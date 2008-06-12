<?php
/**
 * class.rbac.php
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
/**
 * File: $Id$
 *
 * RBAC class definition
 *
 * @package gulliver
 * @copyright (C) 2002 by Colosa Development Team.
 * @link http://www.colosa.com
 *
 * @subpackage rbac
 * @link  http://manuals.colosa.com/gulliver/rbac.html
 * @author Fernando Ontiveros
 */

/**
 * Clase Wrapper
 *
 * @package rbac
 * @author Fernando Ontiveros
 */

class RBAC
{
  /**
  *
  * @access private
  * @var $userObj
  */
  var $userObj;
  var $usersPermissionsObj;
  var $usersRolesObj;
  var $systemObj;
  var $rolesObj;
  var $permissionsObj;
  var $userloggedobj;
  var $currentSystemobj;
  var $rolesPermissionsObj;

  var $aUserInfo = array();
  var $sSystem = '';

  static private $instance = NULL;

  private function __construct() {
  }

  function &getSingleton() {
    if (self::$instance == NULL) {
      self::$instance = new RBAC();
    }
    return self::$instance;
  }

   function initRBAC () {
    if ( is_null($this->userObj ) ) {
      require_once ( "classes/model/RbacUsers.php" );
      $this->userObj = new RbacUsers;
    }


    if ( is_null($this->systemObj ) ) {
      require_once ( "classes/model/Systems.php" );
      $this->systemObj = new Systems;
    }

    if ( is_null($this->usersRolesObj ) ) {
      require_once ( "classes/model/UsersRoles.php" );
      $this->usersRolesObj = new UsersRoles;
    }

    if ( is_null($this->rolesObj ) ) {
      require_once ( "classes/model/Roles.php" );
      $this->rolesObj = new Roles;
    }

    if ( is_null($this->permissionsObj ) ) {
      require_once ( "classes/model/Permissions.php" );
      $this->permissionsObj = new Permissions;
    }

    if ( is_null($this->rolesPermissionsObj ) ) {
      require_once ( "classes/model/RolesPermissions.php" );
      $this->rolesPermissionsObj = new RolesPermissions;
    }
  }

  function loadUserRolePermission( $sSystem, $sUser) {
    $this->sSystem = $sSystem;
    $fieldsSystem = $this->systemObj->loadByCode($sSystem);
    $fieldsRoles = $this->usersRolesObj->getRolesBySystem ($fieldsSystem['SYS_UID'], $sUser );
    $fieldsPermissions = $this->usersRolesObj->getAllPermissions ($fieldsRoles['ROL_UID'], $sUser );

    $this->aUserInfo[ $sSystem ]['SYS_UID'] = $fieldsSystem['SYS_UID'];
    $this->aUserInfo[ $sSystem ]['ROLE'] = $fieldsRoles;
    $this->aUserInfo[ $sSystem ]['PERMISSIONS'] = $fieldsPermissions;

  }


  /**
   * Autentificacion de un usuario a traves de la clase RBAC_user
   *
   * verifica que un usuario tiene derechos de iniciar una aplicaci?n
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public

   * @param  string $strUser    UserId  (login) de usuario
   * @param  string $strPass    Password
   * @return
   *  -1: no existe usuario
   *  -2: password errado
   *  -3: usuario inactivo
   *  -4: usuario vencido
   *  n : uid de usuario
   */
  function VerifyLogin( $strUser, $strPass)
  {
    $this->initRBAC();
    $res = $this->userObj->VerifyLogin($strUser, $strPass);
    return $res;
  }

  /**
   * Verify if the user has a right
   *
   * @author Everth S. Berrios
   * @access public   
   */
  function verifyUser($strUser) {  	
    $res = $this->userObj->verifyUser($strUser);
    return $res;    
  }

  /**
   * Verify if the user has a right
   *
   * @author Fernando Ontiveros
   * @access public

   * @param  string $uid    id of user
   * @param  string $system Code of System
   * @param  string $perm   id of Permissions
   * @return
   *    1: If it is ok
   *   -1: System doesn't exists
   *   -2: The User has not a Role
   *   -3: The User has not this Permission.
   */
  function userCanAccess ($perm)
  {
    if ( isset ( $this->aUserInfo[ $this->sSystem ]['PERMISSIONS'] ) ) {
      $res = -3;
      if ( !isset ( $this->aUserInfo[ $this->sSystem ]['ROLE'. 'x'] ) ) $res = -2;
      foreach ( $this->aUserInfo[ $this->sSystem ]['PERMISSIONS'] as $key=>$val )
        if ( $perm == $val['PER_CODE'] ) $res = 1;
    }
    else
      $res = -1;

    return $res;
  }
  //
  function createUser($aData = array(), $sRolCode = '') {
  	$sUserUID = $this->userObj->create($aData);
  	$this->assignRoleToUser($sUserUID, $sRolCode);
  	return $sUserUID;
  }

  function updateUser($aData = array(), $sRolCode = '') {
  	if (isset($aData['USR_STATUS'])) {
  	  if ($aData['USR_STATUS'] == 'ACTIVE') {
  	  	$aData['USR_STATUS'] = 1;
  	  }
  	  else {
  	  	$aData['USR_STATUS'] = 0;
  	  }
    }
  	$this->userObj->update($aData);
  	if ($sRolCode != '') {
  		$this->removeRolesFromUser($aData['USR_UID']);
  	  $this->assignRoleToUser($aData['USR_UID'], $sRolCode);
    }
  }

  function assignRoleToUser($sUserUID = '', $sRolCode = '') {
  	$aRol = $this->rolesObj->loadByCode($sRolCode);
  	$this->usersRolesObj->create($sUserUID, $aRol['ROL_UID']);
  }

  function removeRolesFromUser($sUserUID = '') {
  	$oCriteria = new Criteria('rbac');
  	$oCriteria->add(UsersRolesPeer::USR_UID, $sUserUID);
  	UsersRolesPeer::doDelete($oCriteria);
  }

  function changeUserStatus($sUserUID = '', $sStatus = 'ACTIVE') {
  	$aFields               = $this->userObj->load($sUserUID);
  	$aFields['USR_STATUS'] = $sStatus;
  	$this->userObj->update($aFields);
  }

  function removeUser($sUserUID = '') {
  	$this->userObj->remove($sUserUID);
  	$this->removeRolesFromUser($sUserUID);
  }
  //

 
  /**
   * Obtiene los datos b?sicos (rbac) del usuario
   *
   * Obtiene los datos que son almacenados en RBAC.
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public

   * @param  string $uid    id de usuario
   * @return array con el registro del usuario
   */
  function load ($uid ){
  	$this->initRBAC();
    $this->userObj->load($uid);
    $role = $this->usersRolesObj->getRolesBySystem ( $uid , $this->currentSystemobj);
    $this->userObj->Fields['USR_ROLE'] = $role['ROL_CODE'];
    return $this->userObj->Fields;
  }
  
  function loadPermissionByCode($sCode) {
    return $this->permissionsObj->loadByCode($sCode);
  }

  function createPermision($sCode) {
    return $this->permissionsObj->create(array('PER_CODE' => $sCode));
  }

  function loadRoleByCode($sCode) {
    return $this->rolesObj->loadByCode($sCode);
  }

  
  
  
  /** @erik adds ****/
  function listAllRoles () {
      return $this->rolesObj->listAllRoles();
  }
  
  function createRole($aData) {
	  return $this->rolesObj->createRole($aData);	
  }
  function removeRole($ROL_UID){
	  return $this->rolesObj->removeRole($ROL_UID);
  }	
  function verifyNewRole($code){
	return $this->rolesObj->verifyNewRole($code);
  }
  function updateRole($fields){
	return $this->rolesObj->updateRole($fields);
  }
  function loadById($ROL_UID){
	return $this->rolesObj->loadById($ROL_UID);
  }
  function getRoleUsers($ROL_UID){
	return $this->rolesObj->getRoleUsers($ROL_UID);
  }
  function getRoleCode($ROL_UID){
	return $this->rolesObj->getRoleCode($ROL_UID);
  }
  function deleteUserRole($ROL_UID, $USR_UID){
	return $this->rolesObj->deleteUserRole($ROL_UID, $USR_UID);
  }
  function getAllUsers($ROL_UID){
	return $this->rolesObj->getAllUsers($ROL_UID);
  }
  function assignUserToRole($aData){
	return $this->rolesObj->assignUserToRole($aData);
  }
  function getRolePermissions($ROL_UID){
	return $this->rolesObj->getRolePermissions($ROL_UID);
  }
  function getAllPermissions($ROL_UID){
	return $this->rolesObj->getAllPermissions($ROL_UID);
  }
  function  assignPermissionRole($sData){
	return $this->rolesObj->assignPermissionRole($sData);
  }
  function assignPermissionToRole($sRoleUID, $sPermissionUID) {
    return $this->rolesPermissionsObj->create(array('ROL_UID' => $sRoleUID, 'PER_UID' => $sPermissionUID));
  }
  
  function  deletePermissionRole($ROL_UID, $PER_UID){
	return $this->rolesObj->deletePermissionRole($ROL_UID, $PER_UID);
  }
  function numUsersWithRole($ROL_UID){
	return $this->rolesObj->numUsersWithRole($ROL_UID);
  }

  function createSystem($sCode) {
    return $this->systemObj->create(array('SYS_CODE' => $sCode));
  }
 
  function verifyByCode($sCode) {
    return $this->rolesObj->verifyByCode($sCode);
  }
    
}
?>
