<?php
/**
 * Roles.php
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

require_once 'classes/model/om/BaseRoles.php';
require_once 'classes/model/om/BaseRbacUsers.php';
require_once 'classes/model/om/BaseUsersRoles.php';

/**
 * Skeleton subclass for representing a row from the 'ROLES' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class Roles extends BaseRoles
{
	public function load($Uid)
    {
        try {
            $oRow = RolesPeer::retrieveByPK($Uid);
            if (!is_null($oRow)) {
                $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
                $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME);
                $this->setNew(false);
                return $aFields;
            } else {
                throw (new Exception("This row doesn't exists!"));
            }
        }
        catch (exception $oError) {
            throw ($oError);
        }
    }
    
    function loadByCode($sRolCode = '')
    {
        try {
            $oCriteria = new Criteria('rbac');
            $oCriteria->add(RolesPeer::ROL_CODE, $sRolCode);
            $oDataset = RolesPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aRow = $oDataset->getRow();
            if (is_array($aRow)) {
                return $aRow;
            } else {
                throw (new Exception('This row doesn\'t exists!'));
            }
        }
        catch (exception $oError) {
            throw ($oError);
        }
    }

    function listAllRoles()
    {
        try {
            $oCriteria = new Criteria('rbac');
            $oCriteria->addSelectColumn(RolesPeer::ROL_UID);
            $oCriteria->addSelectColumn(RolesPeer::ROL_PARENT);
            $oCriteria->addSelectColumn(RolesPeer::ROL_SYSTEM);
            $oCriteria->addSelectColumn(RolesPeer::ROL_CODE);
            $oCriteria->addSelectColumn(RolesPeer::ROL_CREATE_DATE);
            $oCriteria->addSelectColumn(RolesPeer::ROL_UPDATE_DATE);
            $oCriteria->addSelectColumn(RolesPeer::ROL_STATUS);
            $oCriteria->add(RolesPeer::ROL_UID, '', Criteria::NOT_EQUAL);
            $oCriteria->add(RolesPeer::ROL_CREATE_DATE, '', Criteria::NOT_EQUAL);
            $oCriteria->add(RolesPeer::ROL_UPDATE_DATE, '', Criteria::NOT_EQUAL);
            
            return $oCriteria;

        }
        catch (exception $oError) {
            throw (new Exception("CLASS RELES::FATAL ERROR. Criteria with rbac Can't initialized "));
        }
    }
    
    function createRole($aData)
    {
		$con = Propel::getConnection(RolesPeer::DATABASE_NAME);
        try {
            $this->fromArray($aData, BasePeer::TYPE_FIELDNAME);
            if ($this->validate()) {
                $result = $this->save();
            } else {
                $e = new Exception("Failed Validation in class " . get_class($this) . ".");
                $e->aValidationFailures = $this->getValidationFailures();
                throw ($e);
            }
            $con->commit();
            return $result;
        }
        catch (exception $e) {
            $con->rollback();
            throw ($e);
        }
	}
	
	public function updateRole($fields)
    {
        $con = Propel::getConnection(RolesPeer::DATABASE_NAME);
        try {
            $con->begin();
            $this->load($fields['ROL_UID']);
            $this->fromArray($fields, BasePeer::TYPE_FIELDNAME);
            if ($this->validate()) {
                $result = $this->save();
                $con->commit();
                return $result;
            } else {
                $con->rollback();
                throw (new Exception("Failed Validation in class " . get_class($this) . "."));
            }
        }
        catch (exception $e) {
            $con->rollback();
            throw ($e);
        }
    }
	
	function removeRole($ROL_UID)
    {
        $con = Propel::getConnection(RolesPeer::DATABASE_NAME);
        try {
            $con->begin();
            $this->setRolUid($ROL_UID);
            $result = $this->delete();
            $con->commit();
            return $result;
        }
        catch (exception $e) {
            $con->rollback();
            throw ($e);
        }
    }
    
    function verifyNewRole($code)
    {
    	$code = trim($code);
		$oCriteria = new Criteria('rbac');
        $oCriteria->addSelectColumn(RolesPeer::ROL_UID);
        $oCriteria->add(RolesPeer::ROL_CODE, $code);
		$count = RolesPeer::doCount($oCriteria);
        
        if($count == 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	function loadById($ROL_UID){

        $oCriteria = new Criteria('rbac');
        $oCriteria->addSelectColumn(RolesPeer::ROL_UID);
        $oCriteria->addSelectColumn(RolesPeer::ROL_PARENT);
        $oCriteria->addSelectColumn(RolesPeer::ROL_SYSTEM);
        $oCriteria->addSelectColumn(RolesPeer::ROL_CODE);
        $oCriteria->addSelectColumn(RolesPeer::ROL_CREATE_DATE);
        $oCriteria->addSelectColumn(RolesPeer::ROL_UPDATE_DATE);
        $oCriteria->addSelectColumn(RolesPeer::ROL_STATUS);
        $oCriteria->add(RolesPeer::ROL_UID, $ROL_UID);   
		
        $result = RolesPeer::doSelectRS($oCriteria);
        $result->next();
        $row = $result->getRow();

        $aFields['ROL_UID'] = $row[0];
        $aFields['ROL_PARENT'] = $row[1];
        $aFields['ROL_SYSTEM'] = $row[2];
        $aFields['ROL_CODE'] = $row[3];
        $aFields['ROL_CREATE_DATE'] = $row[4];
        $aFields['ROL_UPDATE_DATE'] = $row[5];
        $aFields['ROL_STATUS'] = $row[6];
        
        return $aFields;
	}
	function getRoleCode($ROL_UID)
	{
		$oCriteria = new Criteria('rbac');
        $oCriteria->addSelectColumn(RolesPeer::ROL_UID);
        $oCriteria->addSelectColumn(RolesPeer::ROL_CODE);  
        $oCriteria->add(RolesPeer::ROL_UID, $ROL_UID);   
		
        $result = RolesPeer::doSelectRS($oCriteria);
        $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $result->next();
        $row = $result->getRow();
        $ret = $row['ROL_CODE'];
        
        return $ret;
	}
	
	function getRoleUsers($ROL_UID)
    {
        try {
            $criteria = new Criteria();
            $criteria->addSelectColumn(RolesPeer::ROL_UID);
	        $criteria->addSelectColumn(RolesPeer::ROL_PARENT);
	        $criteria->addSelectColumn(RolesPeer::ROL_SYSTEM);
	        $criteria->addSelectColumn(RolesPeer::ROL_CODE);
	        $criteria->addSelectColumn(RolesPeer::ROL_CREATE_DATE);
	        $criteria->addSelectColumn(RolesPeer::ROL_UPDATE_DATE);
	        $criteria->addSelectColumn(RolesPeer::ROL_STATUS);
	        $criteria->addSelectColumn(RbacUsersPeer::USR_UID);
	        $criteria->addSelectColumn(RbacUsersPeer::USR_USERNAME);
	        $criteria->addSelectColumn(RbacUsersPeer::USR_FIRSTNAME);
	        $criteria->addSelectColumn(RbacUsersPeer::USR_LASTNAME);
            $criteria->add(RolesPeer::ROL_UID, "", Criteria::NOT_EQUAL);
            $criteria->add(RolesPeer::ROL_UID, $ROL_UID);
            $criteria->addJoin(RolesPeer::ROL_UID, UsersRolesPeer::ROL_UID);
            $criteria->addJoin(UsersRolesPeer::USR_UID, RbacUsersPeer::USR_UID);
            
            $oDataset = RolesPeer::doSelectRS($criteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            return $oDataset;
			
        }
        catch (exception $e) {
            throw $e;
        }
    }
    
    function getAllUsers()
    {
        try {
            $criteria = new Criteria();

	        $criteria->addSelectColumn(RbacUsersPeer::USR_UID);
	        $criteria->addSelectColumn(RbacUsersPeer::USR_USERNAME);
	        $criteria->addSelectColumn(RbacUsersPeer::USR_FIRSTNAME);
	        $criteria->addSelectColumn(RbacUsersPeer::USR_LASTNAME);
            $criteria->add(RbacUsersPeer::USR_UID, "", Criteria::NOT_EQUAL);
            
            $oDataset = RbacUsersPeer::doSelectRS($criteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            return $oDataset;
        }
        catch (exception $e) {
            throw $e;
        }
    }
    
    function assignUserToRole($aData)
	{
		$oUsersRoles = new UsersRoles();
		$oUsersRoles->setUsrUid($aData['USR_UID']);
		$oUsersRoles->setRolUid($aData['ROL_UID']);
		$oUsersRoles->save();
	} 
    
    function deleteUserRole($ROL_UID, $USR_UID)
    {
    	$crit = new Criteria();
		$crit->add(UsersRolesPeer::ROL_UID, $ROL_UID);
		$crit->add(UsersRolesPeer::USR_UID, $USR_UID);
		UsersRolesPeer::doDelete($crit);
	}


} // Roles
