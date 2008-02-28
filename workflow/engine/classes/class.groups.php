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
require_once 'classes/model/Groupwf.php';
require_once 'classes/model/GroupUser.php';
require_once 'classes/model/Users.php';

/**
 * Groups - Groups class
 * @package ProcessMaker
 * @author Julio Cesar Laura Avendaño
 * @copyright 2007 COLOSA
 */

class Groups {

  /*
  * Get the assigned users of a group
  * @param string $sGroupUID
  * @return array
  */
  function getUsersOfGroup($sGroupUID) {
    try {
      $aUsers    = array();
      $oCriteria = new Criteria();
      $oCriteria->addJoin(UsersPeer::USR_UID, GroupUserPeer::USR_UID, Criteria::LEFT_JOIN);
      $oCriteria->add(GroupUserPeer::GRP_UID, $sGroupUID);
      $oCriteria->add(UsersPeer::USR_STATUS,  1);
      $oDataset = UsersPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      
      while ($aRow = $oDataset->getRow()) {
      	$aUsers[] = $aRow;
      	$oDataset->next();
      }
      return $aUsers;
    }
    catch (Exception $oError) {
    	throw($oError);
    }
  }

  /*
  * Get the active groups for an user
  * @param string $sUserUID
  * @return array
  */
  function getActiveGroupsForAnUser($sUserUID) {
    try {
      $oCriteria = new Criteria();
      $oCriteria->addSelectColumn( GroupUserPeer::GRP_UID );
      $oCriteria->addSelectColumn( GroupwfPeer::GRP_STATUS );
      $oCriteria->add(GroupUserPeer::USR_UID,  $sUserUID );
      $oCriteria->add(GroupwfPeer::GRP_STATUS,  'ACTIVE' );
      $oCriteria->addJoin(GroupUserPeer::GRP_UID, GroupwfPeer::GRP_UID, Criteria::LEFT_JOIN);
      $oDataset = GroupUserPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      
      $aGroups = array();
      $aRow = $oDataset->getRow();
      while ( is_array ($aRow) ) {
      	$aGroups[] = $aRow['GRP_UID'];
      	$oDataset->next();
      	$aRow = $oDataset->getRow();
      }
      return $aGroups;
    }
    catch (Exception $oError) {
    	throw($oError);
    }
  }
  
  function addUserToGroup( $GrpUid, $UsrUid )
  {
    try {
      $oGrp = GroupUserPeer::retrieveByPk( $GrpUid, $UsrUid  );
  	  if ( get_class ($oGrp) == 'GroupUser' ) { 
        return true;
      }
      else {
        $oGrp = new GroupUser();
        $oGrp->setGrpUid ($GrpUid);
        $oGrp->setUsrUid ($UsrUid);
        $oGrp->Save();
      }      
    }
    catch (Exception $oError) {
    	throw($oError);
    }
  }
  
  function removeUserOfGroup( $UsrUid, $GrpUid )
  {
    $gu=new GroupUser();
    $gu->remove( $UsrUid, $GrpUid );
  }
  
  function getAllGroups()
  {
    try
    {
      $criteria = new Criteria();
      //$criteria->add(GroupwfPeer::GRP_STATUS, "ACTIVE");
      $con = Propel::getConnection(GroupwfPeer::DATABASE_NAME);
      $objects = GroupwfPeer::doSelect($criteria, $con);
      return $objects;
    }
    catch(Exception $e)
    {
    	throw $e;
    }
  }
  
  public function ofToAssignUserOfAllGroups($sUserUID = '') {
  	try {
  		$oCriteria = new Criteria('workflow');
  	  $oCriteria->add(GroupUserPeer::USR_UID, $sUserUID);
  	  GroupUserPeer::doDelete($oCriteria);
  	}
  	catch (Exception $oError) {
    	throw($oError);
    }
  }
}
?>
