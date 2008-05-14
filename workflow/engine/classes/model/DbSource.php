<?php

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
