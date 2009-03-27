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
  var $authSourcesObj;

  var $aUserInfo = array();
  var $aRbacPlugins = array();
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

    if (is_null($this->authSourcesObj)) {
      require_once 'classes/model/AuthenticationSource.php';
      $this->authSourcesObj = new AuthenticationSource();
    }
    //hook for RBAC plugins
    $pathPlugins = PATH_RBAC . 'plugins';
    if ( is_dir ( $pathPlugins ) ) {
      if ($handle = opendir( $pathPlugins )) {
        while ( false !== ($file = readdir($handle))) {
          if ( strpos($file, '.php',1) && is_file( $pathPlugins . PATH_SEP . $file) &&
               substr($file,0,6) == 'class.' && substr($file,-4) == '.php' )  {

            $sClassName = substr($file,6, strlen($file) - 10);
            require_once ($pathPlugins . PATH_SEP . $file);
            $this->aRbacPlugins[] = $sClassName;

          }
        }
      }
    }

  }

  /**
   * Gets the roles and permission for one RBAC_user
   *
   * gets the Role and their permissions for one User
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public

   * @param  string $sSystem    the system
   * @param  string $sUser      the user
   * @return $this->aUserInfo[ $sSystem ]
   */
  function loadUserRolePermission( $sSystem, $sUser) {
    $this->sSystem = $sSystem;
    $fieldsSystem = $this->systemObj->loadByCode($sSystem);
    $fieldsRoles = $this->usersRolesObj->getRolesBySystem ($fieldsSystem['SYS_UID'], $sUser );
    $fieldsPermissions = $this->usersRolesObj->getAllPermissions ($fieldsRoles['ROL_UID'], $sUser );

    $this->aUserInfo[ $sSystem ]['SYS_UID'] = $fieldsSystem['SYS_UID'];
    $this->aUserInfo[ $sSystem ]['ROLE'] = $fieldsRoles;
    $this->aUserInfo[ $sSystem ]['PERMISSIONS'] = $fieldsPermissions;

  }

  function VerifyWithOtherAuthenticationSource( $sAuthType, $sAuthSource, $aUserFields, $sAuthUserDn, $strPass)
  {
    //check if the user is active
    if ( $aUserFields['USR_STATUS']  != 1 )
      return -3;  //inactive user

    //check if the user's due date is valid
    if ( $aUserFields['USR_DUE_DATE']  < date('Y-m-d') )
      return -4;  //due date

    foreach ( $this->aRbacPlugins as $sClassName) {
      if ( $sClassName == $sAuthType ) {
        $plugin =  new $sClassName();
        $plugin->sAuthSource = $sAuthSource;
        $plugin->sSystem     = $this->sSystem;
        krumo ( $aUserFields );

        $bValidUser = $plugin->VerifyLogin ( $sAuthUserDn, $strPass );

        if ( $bValidUser == TRUE)
          return ( $aUserFields['USR_UID'] );
        else
          return -2; //wrong password

      }
    }
    return -5; //invalid authentication source
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
   *  -5: invalid authentication source   ( **new )
   *  n : uid de usuario
   */
  function VerifyLogin( $strUser, $strPass)
  {
  	//check if the user exists in the table RB_WORKFLOW.USERS
    $this->initRBAC();
    if ( $this->userObj->verifyUser($strUser) == 0 ) {
      return -1;
    }
    //if the user exists, the VerifyUser function will return the user properties

    //default values
    $sAuthType = 'mysql';
    if ( isset($this->userObj->fields['USR_AUTH_TYPE']) ) $sAuthType = strtolower ( $this->userObj->fields['USR_AUTH_TYPE'] );

    //hook for RBAC plugins
    if ( $sAuthType != 'mysql' && $sAuthType != '' ) {
      $sAuthSource = $this->userObj->fields['UID_AUTH_SOURCE'];
      $sAuthUserDn = $this->userObj->fields['USR_AUTH_USER_DN'];
      $res = $this->VerifyWithOtherAuthenticationSource( $sAuthType, $sAuthSource, $this->userObj->fields, $sAuthUserDn, $strPass);
      return $res;
    }
    else {
      $res = $this->userObj->VerifyLogin($strUser, $strPass);
      return $res;
    }
  }

  /**
   * Verify if the user exist or not exists, the argument is the UserName
   *
   * @author Everth S. Berrios
   * @access public
   */
  function verifyUser($strUser) {
    $res = $this->userObj->verifyUser($strUser);
    return $res;
  }

  /**
   * Verify if the user exist or not exists, the argument is the UserUID
   *
   * @author Everth S. Berrios
   * @access public
   */
  function verifyUserId($strUserId) {
    $res = $this->userObj->verifyUserId($strUserId);
    return $res;
  }

  /**
   * Verify if the user has a right over the permission
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
      //if ( !isset ( $this->aUserInfo[ $this->sSystem ]['ROLE'. 'x'] ) ) $res = -2;
      foreach ( $this->aUserInfo[ $this->sSystem ]['PERMISSIONS'] as $key=>$val )
        if ( $perm == $val['PER_CODE'] ) $res = 1;
    }
    else
      $res = -1;

    return $res;
  }

  function createUser($aData = array(), $sRolCode = '') {
    if ($aData['USR_STATUS'] == 'ACTIVE') {
      $aData['USR_STATUS'] = 1;
    }
    if ($aData['USR_STATUS'] == 'INACTIVE') {
      $aData['USR_STATUS'] = 0;
    }
    $sUserUID = $this->userObj->create($aData);
    if ($sRolCode != '') {
      $this->assignRoleToUser($sUserUID, $sRolCode);
    }
    return $sUserUID;
  }

  function updateUser($aData = array(), $sRolCode = '') {
    if (isset($aData['USR_STATUS'])) {
      if ($aData['USR_STATUS'] == 'ACTIVE') {
        $aData['USR_STATUS'] = 1;
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
    if ($sStatus == 'ACTIVE') {
      $sStatus = 1;
    }

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
    $this->userObj->Fields = $this->userObj->load($uid);

    $fieldsSystem = $this->systemObj->loadByCode($this->sSystem);
    $fieldsRoles = $this->usersRolesObj->getRolesBySystem ($fieldsSystem['SYS_UID'], $uid );
    $this->userObj->Fields['USR_ROLE'] = $fieldsRoles['ROL_CODE'];
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
  function listAllRoles ( $systemCode = 'PROCESSMAKER') {
      return $this->rolesObj->listAllRoles($systemCode);
  }

  function listAllPermissions ( $systemCode = 'PROCESSMAKER') {
      return $this->rolesObj->listAllPermissions($systemCode);
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

  /* Authentication Sources */
  function getAllAuthSources() {
    return $this->authSourcesObj->getAllAuthSources();
  }

  function getAuthSource($sUID) {
    return $this->authSourcesObj->load($sUID);
  }

  function createAuthSource($aData) {
    $this->authSourcesObj->create($aData);
  }

  function updateAuthSource($aData) {
    $this->authSourcesObj->update($aData);
  }

  function removeAuthSource($sUID) {
    $this->authSourcesObj->remove($sUID);
  }

  function searchUsers($sUID, $sKeyword) {
    $aAuthSource = $this->getAuthSource($sUID);
    $sAuthType   = strtolower($aAuthSource['AUTH_SOURCE_PROVIDER']);
    foreach ( $this->aRbacPlugins as $sClassName) {
      if ( $sClassName == $sAuthType ) {
        $plugin =  new $sClassName();
        $plugin->sAuthSource = $sUID;
        $plugin->sSystem     = $this->sSystem;
        return $plugin->searchUsers($sKeyword);
      }
    }
    return array();
  }
}