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

require_once 'classes/model/om/BaseTaskUser.php';
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
class TaskUser extends BaseTaskUser {

  /**
	 * Create the application document registry
   * @param array $aData
   * @return string
  **/
  public function create($aData)
  {
  	$oConnection = Propel::getConnection(TaskUserPeer::DATABASE_NAME);
  	try {
  	  $oTaskUser = new TaskUser();
  	  $oTaskUser->fromArray($aData, BasePeer::TYPE_FIELDNAME);
  	  if ($oTaskUser->validate()) {
        $oConnection->begin();
        $iResult = $oTaskUser->save();
        $oConnection->commit();
        return $iResult;
  	  }
  	  else {
  	  	$sMessage = '';
  	    $aValidationFailures = $oTaskUser->getValidationFailures();
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
   * @param string $sTasUid
   * @param string $sUserUid
   * @return string
  **/
  public function remove($sTasUid, $sUserUid, $iType, $iRelation)
  {
  	$oConnection = Propel::getConnection(TaskUserPeer::DATABASE_NAME);
  	try {
  	  $oTaskUser = TaskUserPeer::retrieveByPK($sTasUid, $sUserUid, $iType, $iRelation);
  	  if (!is_null($oTaskUser))
  	  {
  	  	$oConnection->begin();
        $iResult = $oTaskUser->delete();
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

} // TaskUser