<?php

require_once 'classes/model/om/BaseSystems.php';


/**
 * Skeleton subclass for representing a row from the 'SYSTEMS' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class Systems extends BaseSystems {

	/**
	 * Load the Application row specified in [app_id] column value.
	 * 
	 * @param      string $AppUid   the uid of the application 
	 * @return     array  $Fields   the fields 
	 */
  
  function Load ( $SysUid ) {
  	$con = Propel::getConnection(SystemsPeer::DATABASE_NAME);
    try {
      $oSystem = SystemsPeer::retrieveByPk( $SysUid );
  	  if ( get_class ($oSystem) == 'Systems' ) { 
  	    $aFields = $oSystem->toArray(BasePeer::TYPE_FIELDNAME);
  	    $this->fromArray ($aFields, BasePeer::TYPE_FIELDNAME );
  	    return $aFields;
  	  }
      else {
        throw( new Exception( "This Systems row doesn't exists!" ));
      }
    }
    catch (Exception $oError) {
    	throw($oError);
    }
  }

  function LoadByCode ( $SysUid ) {
  	$con = Propel::getConnection(SystemsPeer::DATABASE_NAME);
    try {
      $c = new Criteria( 'rbac' );
      $c->add ( SystemsPeer::SYS_CODE, $SysUid );
      $rs = SystemsPeer::doSelect( $c );
      if ( is_array($rs) && isset( $rs[0] ) && get_class ( $rs[0] ) == 'Systems' ) { 
  	    $aFields = $rs[0]->toArray(BasePeer::TYPE_FIELDNAME);
  	    $this->fromArray ($aFields, BasePeer::TYPE_FIELDNAME );
  	    return $aFields;
  	  }
      else {
        throw( new Exception( "This Systems row doesn't exists!" ));
      }
    }
    catch (Exception $oError) {
    	throw($oError);
    }
  }


} // Systems
