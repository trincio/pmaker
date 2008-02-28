<?php

require_once 'classes/model/om/BaseRoles.php';


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
class Roles extends BaseRoles {
  function loadByCode($sRolCode = '') {
  	try {
  		$oCriteria = new Criteria('rbac');
      $oCriteria->add(RolesPeer::ROL_CODE, $sRolCode);
      $oDataset = RolesPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $aRow = $oDataset->getRow();
  	  if (is_array($aRow)) {
  	    return $aRow;
      }
      else {
        throw(new Exception('This row doesn\'t exists!'));
      }
    }
    catch (Exception $oError) {
    	throw($oError);
    }
  }
} // Roles
