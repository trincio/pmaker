<?php
/**
 * $Id$
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * You can contact Colosa Inc, 2655 Le Jeune Road, Suite 1112, Coral Gables,
 * FL 33134, USA or email info@colosa.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * ProcessMaker" logo and retain the original copyright notice. If the display
 * of the logo is not reasonably feasible for technical reasons, the
 * Appropriate Legal Notices must display the words "Powered by ProcessMaker"
 * and retain the original copyright notice.
 * -
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
      //require_once ( "classes/model/Roles.php" );
      require_once ( "classes/model/Permissions.php" );
      require_once ( "classes/model/RolesPermissions.php" );
      //require_once ( "classes/model/UsersRoles.php" );
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
   * Function userCanAccessApp
   * @author Aldo Mauricio Veliz Valenzuela
   * @access public
   * @parameter string uid
   * @parameter string app
   * @parameter string perm
   * @parameter string dbname
   * @return string
   */
  function userCanAccessApp($uid, $app, $perm, $dbname = '' )
  {
  	if ( $dbname == '' )
      $this->initDB();
    else
      $this->initDB2( $dbname);
    $res = $this->userObj->userCanAccess ($uid, $app, $perm );
    return $res;
  }
  /**
   * Function getPermissionsArray
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string dbname
   * @return string
   */
  function getPermissionsArray( $dbname = '' )
  {

    global $G_APPLICATION_CODE;

    $uid = $_SESSION['USER_LOGGED'];

  	if ( $dbname == '' )
      $this->initDB();
    else
      $this->initDB2( $dbname);
    $permisos = $this->userObj->getPermissionsArray ($uid, $G_APPLICATION_CODE );
    return $permisos;
  }

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


  /**
   * Edita datos de un usuario
   *
   * Realiza una actualizacion en un registro en la Tabla de Usuarios
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public

   * @param  integer $uid     id de usuario
   * @param  string $first    primer apellido del usuario
   * @param  string $mid      segundo apellido del usuario
   * @param  string $name     nombres del usuario
   * @param  string $email    email del usuario
   * @param  string $phone    telefono del usuario
   * @param  string $cell     n?mero dcelular del usuario
   * @param  string $fax      fax del usuario
   * @param  string $pobox    Casilla o PO Box del usuario
   * @param  string $userID   Nombre de Usuario (login) del usuario
   * @param  string $status   Estado actual del usuario
   * @param  string $due      fecha de vencimiento del usuario
   * @return integer  id del usuario creado
   */
  function editUser ($uid, $first, $mid, $name, $email, $phone, $cell, $fax, $zipCode, $userID, $status, $due , $question, $answer, $birthday, $dbname = '')
  {
  	if ( $dbname == '' )
      $this->initDB();
    else
      $this->initDB2($dbname);

    $this->userObj->editUser($uid, $first, $mid, $name, $email, $phone, $cell, $fax, $zipCode, $userID, $status, $due , $question, $answer, $birthday);
  }

  /**
   * Reinicia password
   *
   * Reinicia el password del usuario
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public

   * @param  integer $uid      id de usuario
   * @param  string $pass     password del usuario
   * @return integer  id del usuario creado
   */
  function changePassword ( $uid, $pass, $dbname = '' )
  {
  	if ( $dbname == '' )
      $this->initDB();
    else
      $this->initDB2( $dbname);
    return $this->userObj->changePassword ( $uid, $pass );
  }
  /**
   * Function changePasswordEncrypted
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string uid
   * @parameter string pass
   * @parameter string dbname
   * @return string
   */
  function changePasswordEncrypted( $uid, $pass, $dbname = '' )
  {
  	if ( $dbname == '' )
      $this->initDB();
    else
      $this->initDB2( $dbname);
    return $this->userObj->changePasswordEncrypted ( $uid, $pass );
  }

  /**
   * Verifica que no exista userid repetido
   *
   * verifica no exita otro usuario con el mismo login
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  integer $uid      id de usuario
   * @param  string $strUserName  UserId  (login) de usuario
   * @return
   *  0: no existe usuario con ese login
   *  n : uid de usuario que ya tiene ese login
   */
  function UserNameRepetido ( $uid, $strUserName, $dbname = '' )
  {
  	$this->initDB();
    return $this->userObj->UserNameRepetido ($uid, $strUserName );
  }
  /**
   * Function createUser
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string lastname
   * @parameter string mid
   * @parameter string firstname
   * @parameter string email
   * @parameter string default
   * @parameter string index
   * @parameter string user_type
   * @parameter string dbname
   * @return string
   */
  /*function createUser($lastname, $mid, $firstname, $email, $default=NULL, $index = '',$user_type, $dbname = '')
  {

	  if ( $dbname == '' )
	    $this->initDB();
	  else
	    $this->initDB2( $dbname);

      //print "createRBAC ($first, $mid, $name, $email, $phone, $cell, $fax, $zipCode, $question, $answer, $birthday)<br>" ;
      //die;
      //return $this->userObj->createUser($lastname, $mid, $firstname, $email, $phone, $cell, $fax, $zipCode, $question, $answer, $birthday,$default, $index);
      return $this->userObj->createUser($lastname, $mid, $firstname, $email,$default, $index,$user_type);
   }*/
  /**
   * Function createUser_old
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string first
   * @parameter string mid
   * @parameter string name
   * @parameter string email
   * @parameter string phone
   * @parameter string cell
   * @parameter string fax
   * @parameter string zipCode
   * @parameter string question
   * @parameter string answer
   * @parameter string birthday
   * @parameter string default
   * @parameter string index
   * @parameter string dbname
   * @return string
   */
  function createUser_old($first, $mid, $name, $email, $phone, $cell, $fax, $zipCode, $question, $answer, $birthday, $default=NULL, $index = '', $dbname = '')
  {
	  if ( $dbname == '' )
	    $this->initDB();
	  else
	    $this->initDB2( $dbname);

      //print "createRBAC ($first, $mid, $name, $email, $phone, $cell, $fax, $zipCode, $question, $answer, $birthday)<br>" ;
      //die;
      return $this->userObj->createUser_old($lastname, $mid, $firstname, $email, $phone, $cell, $fax, $zipCode, $question, $answer, $birthday,$default, $index);
   }


  /**
   * Reinicia login name y  password
   *
   * Reinicia el password y asigna un nuevo User Id (login name) al usuario
   *
   * @author Hardy Beltran Monasterios <hardy@colosa.com>
   * @access public
   * @param  string $uid      id de un usuario existente
   * @param  string $user     Nombre de usuario en el sistema (login)
   * @param  string $pass     Contrasenia del usuario
   * @return integer          id del usuario creado
   */
  function createUserName ( $uid, $user, $pass, $dbname = '' )
  {
   	if ( $dbname == '' )
      $this->initDB();
    else
      $this->initDB2( $dbname);
      return $this->userObj->createUserName($uid, $user, $pass);
  }

  /**
   * Asigna rol al usuario
   *
   *
   * @author Hardy Beltran Monasterios <hardy@colosa.com>
   * @access public
   * @param  string $uid   id de un usuario existente
   * @param  string $rol   rol del usuario
   * @return integer       1 si fue exitoso, 0 en otro caso
   */
  function assignUserRole ( $uid, $role, $application = '', $dbname = '' )
  {
  	if ( $dbname == '' )
      $this->initDB();
    else
      $this->initDB2( $dbname);
      return $this->userObj->assignUserRole($uid, $role, $application);
  }

  /**
   * Cambia el rol asignado a un usuario
   *
   *
   * @author Hardy Beltran Monasterios <hardy@colosa.com>
   * @access public
   * @param  string $uid      id de un usuario existente
   * @param  string $newrol   id rol del usuario
   * @return integer          1 si fue exitoso
   */
  function setUserRole ( $uid, $role, $dbname = '' )
  {
  	if ( $dbname == '' )
      $this->initDB();
    else
      $this->initDB2( $dbname );
      return $this->userObj->setUserRole($uid, $role);
  }


  /**
   * Lista todos los usuarios de una applicacion
   *
   * devuelve en un array todos los usuarios que pertenecen a una aplicaci?n especifica
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public

   * @param  string $application   id de Permiso
   * @return
   *    array: Lista de usuario
   */
  function listAllUsers ( $application, $dbname = '' ) {
  	if ( $dbname == '' )
      $this->initDB();
    else
      $this->initDB2( $dbname);
      return $this->userObj->listAllUsers($application);
  }

  /**
   * Lista todos los usuarios con un mismo role
   *
   * devuelve en un array todos los usuarios con un mismo role
   *
   * @author Mauricio Veliz <mauricio@colosa.com>
   * @access public

   * @param  string $role nombre del role
   * @return
   *    array: Lista de usuario
   */
  function listAllUsersByRole ( $role, $dbname = '' ) {
  	if ( $dbname == '' )
      $this->initDB();
    else
      $this->initDB2( $dbname);
    return $this->userObj->listAllUsersByRole($role);
  }


  /**
   * Lista todos los roles de una applicacion
   *
   * devuelve en un array todos los Roles que pertenecen a una aplicaci?n especifica
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public

   * @param  string $application   id de Permiso
   * @return
   *    array: Lista de usuario
   */
  function listAllRoles ( $application, $permission = "", $dbname = '' ) {
	if ( $dbname == '' )
	  $this->initDB();
	else
	  $this->initDB2( $dbname);
      return $this->userObj->listAllRoles($application, $permission);
  }

  /**
   * El Nombre del Usuario, para usos diversos
   *
   * devuelve el nombre del usuario en base a su ID, concatenando USR_NAMES, USR_MIDNAME, USR_FIRSTNAME (Debia ser LASTNAME)
   *
   * @author Jorge Hugo Loza <hugo@colosa.com>
   * @access public

   * @param  integer $userid        id de usuario
   * @return
   *    string: Nombre concatenado
   */
  function getUserName ( $userid, $dbname = '' ) {
  	if ( $dbname == '' )
      $this->initDB();
    else
      $this->initDB2( $dbname);
      return $this->userObj->getusername($userid);
  }

  /*
  * Elimina un usuario de RBAC
  * @param ID_User el ID del usuario
  */
  function deleteUser($ID_User, $dbname = '' ) {
  	if ( $dbname == '' )
      $this->initDB();
    else
      $this->initDB2( $dbname);
  	$this->userObj->deleteUser($ID_User);
  }

	/* 	*
			* By JHL 06/12/05
			* gets the info from the Role table
			* @param id_role el ID del rol
			* @return  array: Fields
	*/
  function loadroleinfo($id_role, $dbname = '') {
  	if ( $dbname == '' )
      $this->initDB();
    else
      $this->initDB2( $dbname);
  	return $this->rolesObj->loadroleinfo($id_role);
  }
  /**
   * Function roleCodeRepetido
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string code
   * @parameter string dbname
   * @return string
   */
  function roleCodeRepetido($code, $dbname = '')
  {
  	if ( $dbname == '' )
      $this->initDB();
    else
      $this->initDB2( $dbname);
  	return $this->rolesObj->roleCodeRepetido($code);
  }
  /**
   * Function editRole
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string roleid
   * @parameter string appid
   * @parameter string code
   * @parameter string descrip
   * @parameter string dbname
   * @return string
   */
  function editRole($roleid, $appid, $code , $descrip, $dbname = '' )
  {
  	if ( $dbname == '' )
      $this->initDB();
    else
      $this->initDB2( $dbname);
  	return $this->rolesObj->editRole($roleid, $appid, $code , $descrip);
  }

}
?>
