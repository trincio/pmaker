<?php

require_once 'classes/model/om/BaseUsersRoles.php';


/**
 * Skeleton subclass for representing a row from the 'USERS_ROLES' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class UsersRoles extends BaseUsersRoles {

  function getRolesBySystem ( $SysUid, $UsrUid ) {
  	$con = Propel::getConnection(UsersRolesPeer::DATABASE_NAME);
    try {
      $c = new Criteria( 'rbac' );
      $c->clearSelectColumns();
      $c->addSelectColumn ( RolesPeer::ROL_UID );
      $c->addSelectColumn ( RolesPeer::ROL_CODE );
      $c->addJoin ( UsersRolesPeer::ROL_UID, RolesPeer::ROL_UID );
      $c->add ( UsersRolesPeer::USR_UID, $UsrUid );
      $c->add ( RolesPeer::ROL_SYSTEM, $SysUid );
      $rs = UsersRolesPeer::doSelectRs( $c );
      $rs->setFetchmode (ResultSet::FETCHMODE_ASSOC);
      $rs->next();
      $row = $rs->getRow();
/*  return only the first row, no other rows can be permitted
      while ( is_array ( $row ) ) {
        $rows[] = $row;
        $rs->next();
        $row = $rs->getRow();
      }
*/
      return $row;
    }
    catch (Exception $oError) {
    	throw($oError);
    }
  }


  function getAllPermissions  ( $sRolUid, $sUsrUid ) {
  	$con = Propel::getConnection(RolesPermissionsPeer::DATABASE_NAME);
    try {
      $c = new Criteria( 'rbac' );
//      $c->clearSelectColumns();
      $c->addSelectColumn ( RolesPermissionsPeer::PER_UID );
      $c->addSelectColumn ( PermissionsPeer::PER_CODE );
      $c->addJoin ( RolesPermissionsPeer::PER_UID, PermissionsPeer::PER_UID );
      $c->add ( RolesPermissionsPeer::ROL_UID, $sRolUid);
      $rs = RolesPermissionsPeer::doSelectRs( $c );
      $rs->setFetchmode (ResultSet::FETCHMODE_ASSOC);
      $rs->next();
      $row = $rs->getRow();
      $rows = array();
      while ( is_array ( $row ) ) {
        $rows[] = $row;
        $rs->next();
        $row = $rs->getRow();
      }
      return $rows;
    }
    catch (Exception $oError) {
    	throw($oError);
    }
  }

  function create($sUserUID = '', $sRolUID = '') {
  	$this->setUsrUid($sUserUID);
  	$this->setRolUid($sRolUID);
  	$this->save();
  }

  function remove($sUserUID = '', $sRolUID = '') {
  	$this->setUsrUid($sUserUID);
  	$this->setRolUid($sRolUID);
  	$this->delete();
  }

} // UsersRoles
