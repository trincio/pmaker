<?php
/**
 * DbSource.php
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

require_once 'classes/model/Content.php';
require_once 'classes/model/om/BaseDbSource.php';


/**
 * Skeleton subclass for representing a row from the 'DB_SOURCE' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class DbSource extends BaseDbSource
{

    /**
     * This value goes in the content table
     * @var        string
     */
    protected $db_source_description = '';

    /**
     * Get the rep_tab_title column value.
     * @return     string
     */
    public function getDBSourceDescription() {
      if ( $this->getDbsUid() == "" ) {
        throw ( new Exception( "Error in getDBSourceDescription, the getDbsUid() can't be blank") );
      }
      $lang = defined ( 'SYS_LANG' ) ? SYS_LANG : 'en';
      $this->db_source_description = Content::load ( 'DBS_DESCRIPTION', '', $this->getDbsUid(), $lang );
      return $this->db_source_description;
    }

    function getCriteriaDBSList($sProcessUID)
    {
        $sDelimiter = DBAdapter::getStringDelimiter();
        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn(DbSourcePeer::DBS_UID);
        $oCriteria->addSelectColumn(DbSourcePeer::PRO_UID);
        $oCriteria->addSelectColumn(DbSourcePeer::DBS_TYPE);
        $oCriteria->addSelectColumn(DbSourcePeer::DBS_SERVER);
        $oCriteria->addSelectColumn(DbSourcePeer::DBS_DATABASE_NAME);
        $oCriteria->addSelectColumn(DbSourcePeer::DBS_USERNAME);
        $oCriteria->addSelectColumn(DbSourcePeer::DBS_PASSWORD);
        $oCriteria->addSelectColumn(DbSourcePeer::DBS_PORT);
        $oCriteria->addAsColumn('DBS_DESCRIPTION', 'C.CON_VALUE');
        $oCriteria->addAlias('C', 'CONTENT');
        $aConditions = array();
        $aConditions[] = array(DbSourcePeer::DBS_UID, 'C.CON_ID');
        $aConditions[] = array('C.CON_CATEGORY', $sDelimiter . 'DBS_DESCRIPTION' . $sDelimiter);
        $aConditions[] = array('C.CON_LANG', $sDelimiter . SYS_LANG . $sDelimiter);
        $oCriteria->addJoinMC($aConditions, Criteria::LEFT_JOIN);
        $oCriteria->add(DbSourcePeer::PRO_UID, $sProcessUID);
        return $oCriteria;
    }

    public function load($Uid)
    {
        try {
            $oRow = DbSourcePeer::retrieveByPK($Uid);
            if (!is_null($oRow)) {
                $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
                $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME);
                $aFields['DBS_DESCRIPTION'] = $this->getDBSourceDescription();
                $this->setNew(false);
                return $aFields;
            } else {
        throw(new Exception( "The row '$Uid' in table DbSource doesn't exists!" ));
            }
        }
        catch (exception $oError) {
            throw ($oError);
        }
    }

	function Exists ( $Uid ) {
		try {
			$oPro = DbSourcePeer::retrieveByPk( $Uid );
			if ( get_class ($oPro) == 'DbSource' ) {
				return true;
			}
			else {
				return false;
			}
		}
		catch (Exception $oError) {
			throw($oError);
		}
	}

    public function update($fields)
    {
        $con = Propel::getConnection(DbSourcePeer::DATABASE_NAME);
        try {
            $con->begin();
            $this->load($fields['DBS_UID']);
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

    function remove($DbsUid)
    {
        $con = Propel::getConnection(DbSourcePeer::DATABASE_NAME);
        try {
            $con->begin();
            $this->setDbsUid($DbsUid);
            $result = $this->delete();
            $con->commit();
            return $result;
        }
        catch (exception $e) {
            $con->rollback();
            throw ($e);
        }
    }

    function create($aData)
    {
        $con = Propel::getConnection(UsersPeer::DATABASE_NAME);
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

} // DbSource
