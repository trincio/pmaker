<?php
/**
 * GroupUser.php
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

require_once 'classes/model/om/BaseGroupUser.php';
require_once 'classes/model/Content.php';

/**
 * Skeleton subclass for representing a row from the 'GROUP_USER' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the input directory.
 *
 * @package    classes.model
 */
class GroupUser extends BaseGroupUser {

  /**
	 * Create the application document registry
   * @param array $aData
   * @return string
  **/
  public function create($aData)
  {
  	$oConnection = Propel::getConnection(GroupUserPeer::DATABASE_NAME);
  	try {
  	  $oGroupUser = new GroupUser();
  	  $oGroupUser->fromArray($aData, BasePeer::TYPE_FIELDNAME);
  	  if ($oGroupUser->validate()) {
        $oConnection->begin();
        $iResult = $oGroupUser->save();
        $oConnection->commit();
        return $iResult;
  	  }
  	  else {
  	  	$sMessage = '';
  	    $aValidationFailures = $oGroupUser->getValidationFailures();
  	    foreach($aValidationFailures as $oValidationFailure) {
          $sMessage .= $oValidationFailure->getMessage() . '<br />';
        }
        throw(new Exception('The registry cannot be created!<br />'.$sMessage));
  	  }
  	}
    catch (Exception $oError) {
      $oConnection->rollback();
    	throw($oError);
    }
  }

  /**
	 * Remove the application document registry
   * @param string $sGrpUid
   * @param string $sUserUid
   * @return string
  **/
  public function remove($sGrpUid, $sUserUid)
  {
  	$oConnection = Propel::getConnection(GroupUserPeer::DATABASE_NAME);
  	try {
  	  $oGroupUser = GroupUserPeer::retrieveByPK($sGrpUid, $sUserUid);
  	  if (!is_null($oGroupUser))
  	  {
  	  	$oConnection->begin();
        $iResult = $oGroupUser->delete();
        $oConnection->commit();
        return $iResult;
      }
      else {
        throw(new Exception('This row doesn\'t exists!'));
      }
    }
    catch (Exception $oError) {
    	$oConnection->rollback();
      throw($oError);
    }
  }

} // GroupUser