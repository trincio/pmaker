<?php
/**
 * AppDelegation.php
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

require_once 'classes/model/om/BaseAppDelegation.php';
require_once ( "classes/model/HolidayPeer.php" );
require_once ( "classes/model/TaskPeer.php" );
G::LoadClass("dates");

/**
 * Skeleton subclass for representing a row from the 'APP_DELEGATION' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class AppDelegation extends BaseAppDelegation {

  function createAppDelegation ($sProUid, $sAppUid, $sTasUid, $sUsrUid, $sAppThread ) {
    if (!isset($sProUid) || strlen($sProUid) == 0 ) {
      throw ( new Exception ( 'Column "PRO_UID" cannot be null.' ) );
    }

    if (!isset($sAppUid) || strlen($sAppUid ) == 0 ) {
      throw ( new Exception ( 'Column "APP_UID" cannot be null.' ) );
    }

    if (!isset($sTasUid) || strlen($sTasUid ) == 0 ) {
      throw ( new Exception ( 'Column "TAS_UID" cannot be null.' ) );
    }

    if (!isset($sUsrUid) || strlen($sUsrUid ) == 0 ) {
      throw ( new Exception ( 'Column "USR_UID" cannot be null.' ) );
    }
    if (!isset($sAppThread) || strlen($sAppThread ) == 0 ) {
      throw ( new Exception ( 'Column "APP_THREAD" cannot be null.' ) );
    }
    //get max DEL_INDEX SELECT MAX(DEL_INDEX) AS M FROM APP_DELEGATION WHERE APP_UID="'.$Fields['APP_UID'].'"'
    $c = new Criteria ();
    $c->clearSelectColumns();
    $c->addSelectColumn ( 'MAX(' . AppDelegationPeer::DEL_INDEX . ') ' );
    $c->add ( AppDelegationPeer::APP_UID, $sAppUid );
    $rs = AppDelegationPeer::doSelectRS ( $c );
    $rs->next();
    $row = $rs->getRow();
    $delIndex = $row[0] + 1;

    $this->setAppUid          ( $sAppUid );
    $this->setProUid          ( $sProUid );
    $this->setTasUid          ( $sTasUid );
    $this->setDelIndex        ( $delIndex );
    $this->setDelPrevious     ( 0 );
    $this->setUsrUid          ( $sUsrUid );
    $this->setDelType         ( 'NORMAL' );
    $this->setDelPriority     ( '3' );
    $this->setDelThread       ( $sAppThread );
    $this->setDelThreadStatus ( 'OPEN' );
    $this->setDelDelegateDate ( 'now' );
    $this->setDelTaskDueDate  ( $this->calculateDueDate() );
    if ( $delIndex == 1 )  //the first delegation, init date this should be now for draft applications
      $this->setDelInitDate     ('now' );
//    $this->setDelFinishDate   (isset($aData['DEL_FINISH_DATE'])  ? $aData['DEL_FINISH_DATE']  : 'now' );

    if ($this->validate() ) {
      try {
        $res = $this->save();
      }
			catch ( PropelException $e ) {
        throw ( $e );
      }
    }
    else {
      // Something went wrong. We can now get the validationFailures and handle them.
      $msg = '';
      $validationFailuresArray = $this->getValidationFailures();
      foreach($validationFailuresArray as $objValidationFailure) {
        $msg .= $objValidationFailure->getMessage() . "<br/>";
      }
      throw ( new Exception ( 'Failed Data validation. ' . $msg ) );
     }
    return $this->getDelIndex();
  }

	/**
	 * Load the Application Delegation row specified in [app_id] column value.
	 *
	 * @param      string $AppUid   the uid of the application
	 * @return     array  $Fields   the fields
	 */

  function Load ( $AppUid, $sDelIndex ) {
  	$con = Propel::getConnection(AppDelegationPeer::DATABASE_NAME);
    try {
      $oAppDel = AppDelegationPeer::retrieveByPk( $AppUid, $sDelIndex );
  	  if ( get_class ($oAppDel) == 'AppDelegation' ) {
  	    $aFields = $oAppDel->toArray( BasePeer::TYPE_FIELDNAME);
  	    $this->fromArray ($aFields, BasePeer::TYPE_FIELDNAME );
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

	/**
	 * Update the application row
   * @param     array $aData
   * @return    variant
  **/

  public function update($aData)
  {
  	$con = Propel::getConnection( AppDelegationPeer::DATABASE_NAME );
  	try {
      $con->begin();
  	  $oApp = AppDelegationPeer::retrieveByPK( $aData['APP_UID'], $aData['DEL_INDEX'] );
  	  if ( get_class ($oApp) == 'AppDelegation' ) {
  	  	$oApp->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
  	    if ($oApp->validate()) {
          $res = $oApp->save();
          $con->commit();
          return $res;
  	    }
  	    else {
         $msg = '';
         foreach($this->getValidationFailures() as $objValidationFailure)
           $msg .= $objValidationFailure->getMessage() . "<br/>";

         throw ( new PropelException ( 'The row cannot be created!', new PropelException ( $msg ) ) );
  	    }
      }
      else {
        $con->rollback();
        throw(new Exception( "This AppDelegation row doesn't exists!" ));
      }
    }
    catch (Exception $oError) {
    	throw($oError);
    }
  }

  function remove($sApplicationUID, $iDelegationIndex) {
    $oConnection = Propel::getConnection(StepTriggerPeer::DATABASE_NAME);
    try {
      $oConnection->begin();
  	  $oApp = AppDelegationPeer::retrieveByPK( $sApplicationUID, $iDelegationIndex );
  	  if ( get_class ($oApp) == 'AppDelegation' ) {
        $this->setAppUid($sApplicationUID);
        $this->setDelIndex($iDelegationIndex);
        $result = $this->delete();
      }
      $oConnection->commit();
      return $result;
    }
    catch(Exception $e) {
      $oConnection->rollback();
      throw($e);
    }
  }

  function calculateDueDate()
  {
//	  print_r("aaaaaaaaaaaaaaaa");
//	  die();
    //Fatal error: Call to undefined method Task::getUsrUid() in /opt/processmaker/trunk/workflow/engine/classes/model/AppDelegation.php on line 190
    //return 'tomorrow'; //Sample

    $dates = new dates();
    //Get TasDuration
    $task = TaskPeer::retrieveByPK( $this->getTasUid() );
    if (strcasecmp($task->getTasTimeUnit(),"days")==0)
    {
      if ($task->getTasTypeDay()==1)
      {
        $iDueDate=$dates->calculateDate( $this->getDelDelegateDate() , $task->getTasDuration() , $this->getUsrUid() , $task->getProUid() );
      }
      else
      {
        $iDueDate=strtotime($task->getTasDuration().' '.strtolower($task->getTasTimeUnit()) , strtotime($this->getDelDelegateDate()) );
      }
    }
    else
    {
      $iDueDate=strtotime($task->getTasDuration().' '.strtolower($task->getTasTimeUnit()) , strtotime($this->getDelDelegateDate()) );
    }
    return date('Y-m-d H:i:s', $iDueDate);
  }
} // AppDelegation
