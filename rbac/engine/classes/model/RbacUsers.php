<?php
/**
 * RbacUsers.php
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

require_once 'classes/model/om/BaseRbacUsers.php';


/**
 * Skeleton subclass for representing a row from the 'USERS' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class RbacUsers extends BaseRbacUsers {
  /**
   * Autentificacion de un usuario a traves de la clase RBAC_user
   *
   * verifica que un usuario tiene derechos de iniciar una aplicacion
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
  function verifyLogin($sUsername, $sPassword )
  {
    //invalid user
    if ( $sUsername == '' ) return -1;

    //invalid password
    if ( $sPassword == '' ) return -2;

 	  $con = Propel::getConnection(RbacUsersPeer::DATABASE_NAME);
    try {
      $c = new Criteria( 'rbac' );
      $c->add ( RbacUsersPeer::USR_USERNAME, $sUsername );
      $rs = RbacUsersPeer::doSelect( $c );
      if ( is_array($rs) && isset( $rs[0] ) && get_class ( $rs[0] ) == 'RbacUsers' ) {
  	    $aFields = $rs[0]->toArray(BasePeer::TYPE_FIELDNAME);
        //verify password with md5
        if ( $aFields['USR_PASSWORD'] == md5 ($sPassword ) ) {
          if ($aFields['USR_DUE_DATE'] < date('Y-m-d') )
            return -4;
          if ($aFields['USR_STATUS'] != 1 )
            return -3;
          return $aFields['USR_UID'];
        }
        else
          return -2;
  	  }
      else {
        return -1;
      }
    }
    catch (Exception $oError) {
    	throw($oError);
    }
    return -1;
  }
  
  function verifyUser($sUsername)
  {
    //invalid user
    if ( $sUsername == '' ) return 0;  
 	  $con = Propel::getConnection(RbacUsersPeer::DATABASE_NAME);
    try {
      $c = new Criteria( 'rbac' );
      $c->add ( RbacUsersPeer::USR_USERNAME, $sUsername );
      $rs = RbacUsersPeer::doSelect( $c );
      if (is_array($rs) && isset( $rs[0] ) && get_class ( $rs[0] ) == 'RbacUsers') 
      {
  	    return 1;
  	  }
      else 
      { 
      	return 0;       
      }
    }
    catch (Exception $oError) {
    	throw($oError);
    }    
  }

  function load($sUsrUid)
  {
 	  $con = Propel::getConnection(RbacUsersPeer::DATABASE_NAME);
    try {
      $c = new Criteria( 'rbac' );
      $c->add ( RbacUsersPeer::USR_UID, $sUsrUid );
      $rs = RbacUsersPeer::doSelect( $c );
      if ( is_array($rs) && isset( $rs[0] ) && get_class ( $rs[0] ) == 'RbacUsers' ) {
  	    $aFields = $rs[0]->toArray(BasePeer::TYPE_FIELDNAME);
        return $aFields;
      }
    }
    catch ( Exception $oError) {
    	throw($oError);
    }
    return $res;
  }

  function create($aData) {
  	$oConnection = Propel::getConnection(RbacUsersPeer::DATABASE_NAME);
  	try {
  		$aData['USR_UID'] = G::generateUniqueID();
  	  $oRBACUsers       = new RbacUsers();
  	  $oRBACUsers->fromArray($aData, BasePeer::TYPE_FIELDNAME);
  	  //if ($oRBACUsers->validate()) {
        //$oConnection->begin();
        $iResult = $oRBACUsers->save();
        //$oConnection->commit();
        return $aData['USR_UID'];
  	  /*}
  	  else {
  	  	$sMessage = '';
  	    $aValidationFailures = $oRBACUsers->getValidationFailures();
  	    foreach($aValidationFailures as $oValidationFailure) {
          $sMessage .= $oValidationFailure->getMessage() . '<br />';
        }
        throw(new Exception('The registry cannot be created!<br />' . $sMessage));
  	  }*/
  	}
    catch (Exception $oError) {
      $oConnection->rollback();
    	throw($oError);
    }
  }

  function update($aData) {
  	try {
  	  $this->fromArray($aData, BasePeer::TYPE_FIELDNAME);
      $this->setNew(false);
      $iResult = $this->save();
  	}
    catch (Exception $oError) {
      $oConnection->rollback();
    	throw($oError);
    }
  }

  function remove($sUserUID = '') {
  	$this->setUsrUid($sUserUID);
  	$this->delete();
  }

} // Users