<?php
/**
 * Users.php
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

require_once 'classes/model/om/BaseUsers.php';


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
class Users extends BaseUsers {
  function create ($aData)
  {
    $con = Propel::getConnection(UsersPeer::DATABASE_NAME);
    try
    {
      $this->fromArray($aData, BasePeer::TYPE_FIELDNAME);
      if($this->validate())
      {
        $result=$this->save();
      }
      else
      {
        $e=new Exception("Failed Validation in class ".get_class($this).".");
        $e->aValidationFailures=$this->getValidationFailures();
        throw($e);
      }
      $con->commit();
      return $result;
    }
    catch(Exception $e)
    {
      $con->rollback();
      throw($e);
    }
  }
  public function load($UsrUid)
  {
    try {
      $oRow = UsersPeer::retrieveByPK( $UsrUid );
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
    $con = Propel::getConnection(UsersPeer::DATABASE_NAME);
    try
    {
      $con->begin();
      $this->load($fields['USR_UID']);
      $this->fromArray($fields,BasePeer::TYPE_FIELDNAME);
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
  function remove($UsrUid)
  {
    $con = Propel::getConnection(UsersPeer::DATABASE_NAME);
    try
    {
      $con->begin();
      $this->setUsrUid($UsrUid);
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

  function loadByUsername($sUsername)
  {
  	$c = new Criteria('workflow');
    $del = DBAdapter::getStringDelimiter();

    $c->clearSelectColumns();
    $c->addSelectColumn( UsersPeer::USR_USERNAME );

    $c->add(UsersPeer::USR_USERNAME, $sUsername);
    return $c;
  }

  function getAllInformation($UsrUid)
  {
	if( !isset($UsrUid) or $UsrUid == '' ) {
		throw $oException;
	}
	try {

		require_once 'classes/model/IsoCountry.php';
		require_once 'classes/model/IsoLocation.php';
		require_once 'classes/model/IsoSubdivision.php';
		require_once 'classes/model/Language.php';

		$aFields = $this->load($UsrUid);
		$c = new Criteria('workflow');
		$c->add(IsoCountryPeer::IC_UID, $aFields['USR_COUNTRY']);
		$rs = IsoCountryPeer::doSelectRS($c);
		$rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
		$rs->next();
		$Crow = $rs->getRow();

		$c->clearSelectColumns();
		$c->add(IsoSubdivisionPeer::IC_UID, $aFields['USR_COUNTRY']);
		$c->add(IsoSubdivisionPeer::IS_UID, $aFields['USR_CITY']);
		$rs = IsoSubdivisionPeer::doSelectRS($c);
		$rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
		$rs->next();
		$Srow = $rs->getRow();

		$aRet['username'] 	= $aFields['USR_USERNAME'];
		$aRet['firstname'] 	= $aFields['USR_FIRSTNAME'];
		$aRet['lastname']	= $aFields['USR_LASTNAME'];
		$aRet['mail'] 		= $aFields['USR_EMAIL'];
		$aRet['status'] 	= $aFields['USR_STATUS'];
		$aRet['address'] 	= $aFields['USR_ADDRESS'];
		$aRet['phone'] 		= $aFields['USR_PHONE'];
		$aRet['fax'] 		= $aFields['USR_FAX'];
		$aRet['cellular'] 	= $aFields['USR_CELLULAR'];
		$aRet['birthday'] 	= $aFields['USR_BIRTHDAY'];
		$aRet['country']   	= $Crow['IC_NAME'];
		$aRet['city']   	= $Srow['IS_NAME'];

		return $aRet;
	}
	catch (Exception $oException) {
		throw $oException;
	}
  }
} // Users

?>
