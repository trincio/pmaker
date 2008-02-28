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

require_once 'classes/model/om/BaseConfiguration.php';
require_once 'classes/model/Content.php';


/**
 * Skeleton subclass for representing a row from the 'CONFIGURATION' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class Configuration extends BaseConfiguration {
  public function create($aData)
  {
    $con = Propel::getConnection(ConfigurationPeer::DATABASE_NAME);
    try
    {
      $con->begin();
      $this->setCfgUid($aData['CFG_UID']);
      $this->setObjUid($aData['OBJ_UID']);
      $this->setCfgValue(isset($aData['PRO_UID'])?$aData['PRO_UID']:'');
      $this->setProUid($aData['PRO_UID']);
      $this->setUsrUid($aData['USR_UID']);
      $this->setAppUid($aData['APP_UID']);
      if($this->validate())
      {
        $result=$this->save();
        $con->commit();
        return $result;
      }
      else
      {
        $con->rollback();
        throw(new Exception("Failed Validation in class ".get_class($this)."."));
      }
    }
    catch(Exception $e)
    {
      $con->rollback();
      throw($e);
    }
  }
  public function load($CfgUid, $ObjUid, $ProUid, $UsrUid, $AppUid)
  {
    try {
      $oRow = ConfigurationPeer::retrieveByPK( $CfgUid, $ObjUid, $ProUid, $UsrUid, $AppUid );
      if (!is_null($oRow))
      {
        $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
        $this->fromArray($aFields,BasePeer::TYPE_FIELDNAME);
        $this->setNew(false);
        return $aFields;
      }
      else {
        throw( new Exception( "This row doesn't exists!" ));
      }
    }
    catch (Exception $oError) {
      throw($oError);
    }
  }
  public function update($fields)
  {
    $con = Propel::getConnection(ConfigurationPeer::DATABASE_NAME);
    try
    {
      $con->begin();
      $this->load($fields['CFG_UID'], $fields['OBJ_UID'], $fields['PRO_UID'], $fields['USR_UID'], $fields['APP_UID']);
      $this->fromArray($fields,BasePeer::TYPE_FIELDNAME);
      if($this->validate())
      {
        $contentResult=0;
        $result=$this->save();
        $result=($result==0)?($contentResult>0?1:0):$result;
        $con->commit();
        return $result;
      }
      else
      {
        $con->rollback();
        throw(new Exception("Failed Validation in class ".get_class($this)."."));
      }
    }
    catch(Exception $e)
    {
      $con->rollback();
      throw($e);
    }
  }
  public function remove($CfgUid, $ObjUid, $ProUid, $UsrUid, $AppUid)
  {
    $con = Propel::getConnection(ConfigurationPeer::DATABASE_NAME);
    try
    {
      $con->begin();
      $this->setCfgUid($CfgUid);
      $this->setObjUid($ObjUid);
      $this->setProUid($ProUid);
      $this->setUsrUid($UsrUid);
      $this->setAppUid($AppUid);
      $result=$this->delete();
      $con->commit();
      return $result;
    }
    catch(Exception $e)
    {
      $con->rollback();
      throw($e);
    }
  }
  public function exists($CfgUid, $ObjUid, $ProUid, $UsrUid, $AppUid)
  {
    $oRow = ConfigurationPeer::retrieveByPK( $CfgUid, $ObjUid, $ProUid, $UsrUid, $AppUid );
    return ( get_class ($oRow) == 'Configuration' );
  }
} // Configuration
