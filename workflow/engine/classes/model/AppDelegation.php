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

  function createAppDelegation ($sProUid, $sAppUid, $sTasUid, $sUsrUid, $sAppThread, $iPriority = 3 ) {
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
    $this->setDelPriority     ( ($iPriority != '' ? $iPriority : '3') );
    $this->setDelThread       ( $sAppThread );
    $this->setDelThreadStatus ( 'OPEN' );
    $this->setDelDelegateDate ( 'now' );
    $this->setDelTaskDueDate  ( $this->calculateDueDate() );
    if ( $delIndex == 1 )  //the first delegation, init date this should be now for draft applications, in other cases, should be null.
      $this->setDelInitDate     ('now' );

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
        throw( new Exception( "The row '$AppUid, $sDelIndex' in table AppDelegation doesn't exists!" ));
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
        $result = $oApp->delete();
      }
      $oConnection->commit();
      return $result;
    }
    catch(Exception $e) {
      $oConnection->rollback();
      throw($e);
    }
  }

  // TasTypeDay = 1  => working days
  // TasTypeDay = 2  => calendar days
  function calculateDueDate()
  {
    //Get Task properties
    $task = TaskPeer::retrieveByPK( $this->getTasUid() );

    //use the dates class to calculate dates
    $dates = new dates();
    $iDueDate = $dates->calculateDate( $this->getDelDelegateDate(), 
                                       $task->getTasDuration(), 
                                       $task->getTasTimeUnit(),   //hours or days, ( we only accept this two types or maybe weeks
                                       $task->getTasTypeDay(), //working or calendar days
                                       $this->getUsrUid(), 
                                       $task->getProUid(),
                                       $this->getTasUid() );
    return date('Y-m-d H:i:s', $iDueDate);
  }

  function calculateDuration() {
    try {
    	//patch the rows with initdate is null and have a date in finish_date
      $c = new Criteria();
      $c->clearSelectColumns();
      $c->addSelectColumn(AppDelegationPeer::APP_UID );
      $c->addSelectColumn(AppDelegationPeer::DEL_INDEX  );
      $c->addSelectColumn(AppDelegationPeer::DEL_DELEGATE_DATE);
      $c->add(AppDelegationPeer::DEL_INIT_DATE, NULL, Criteria::ISNULL);
      $c->add(AppDelegationPeer::DEL_FINISH_DATE, NULL, Criteria::ISNOTNULL);
      //$c->add(AppDelegationPeer::DEL_INDEX, 1);
   
      $rs = AppDelegationPeer::doSelectRS($c);
      $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $rs->next();
      $row = $rs->getRow();
   
      while (is_array($row)) {
        $oAppDel = AppDelegationPeer::retrieveByPk($row['APP_UID'], $row['DEL_INDEX'] );
        $oAppDel->setDelInitDate($row['DEL_DELEGATE_DATE']);
        $oAppDel->save();

        $rs->next();
        $row = $rs->getRow();
      }
   
    	//walk in all rows with DEL_STARTED = 0 or DEL_FINISHED = 0
    	
      $c = new Criteria();
      $c->clearSelectColumns();
      $c->addSelectColumn(AppDelegationPeer::APP_UID );
      $c->addSelectColumn(AppDelegationPeer::DEL_INDEX  );
      $c->addSelectColumn(AppDelegationPeer::DEL_DELEGATE_DATE);
      $c->addSelectColumn(AppDelegationPeer::DEL_INIT_DATE);
      $c->addSelectColumn(AppDelegationPeer::DEL_TASK_DUE_DATE);
      $c->addSelectColumn(AppDelegationPeer::DEL_FINISH_DATE);
      $c->addSelectColumn(AppDelegationPeer::DEL_DURATION);
      $c->addSelectColumn(AppDelegationPeer::DEL_QUEUE_DURATION);
      $c->addSelectColumn(AppDelegationPeer::DEL_STARTED);
      $c->addSelectColumn(AppDelegationPeer::DEL_FINISHED);
      $c->addSelectColumn(TaskPeer::TAS_DURATION);
      $c->addSelectColumn(TaskPeer::TAS_TIMEUNIT);
      $c->addSelectColumn(TaskPeer::TAS_TYPE_DAY);
      $c->addJoin(AppDelegationPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::JOIN );
      $cton1 = $c->getNewCriterion(AppDelegationPeer::DEL_STARTED,  0);
      $cton2 = $c->getNewCriterion(AppDelegationPeer::DEL_FINISHED, 0);
      $cton1->addOr($cton2);
      $c->add($cton1);
      //$c->add(AppDelegationPeer::DEL_STARTED, 0 );
   
      $rs = AppDelegationPeer::doSelectRS($c);
      $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $rs->next();
      $row = $rs->getRow();
   
      while (is_array($row)) {
      	$iDelegateDate = strtotime ( $row['DEL_DELEGATE_DATE'] );
      	$iInitDate     = strtotime ( $row['DEL_INIT_DATE'] );
      	$iDueDate      = strtotime ( $row['DEL_TASK_DUE_DATE'] );
      	$iFinishDate   = strtotime ( $row['DEL_FINISH_DATE'] );
      	$isStarted     = intval ( $row['DEL_STARTED'] );
      	$isFinished    = intval ( $row['DEL_FINISHED'] );

        //get the object, and then field by field change if correspond...
        $oAppDel = AppDelegationPeer::retrieveByPk($row['APP_UID'], $row['DEL_INDEX'] );

      	if ( $isStarted == 0 ) {
        	if ( $iInitDate != NULL ) {
            $oAppDel->setDelStarted(1);
          	$queueDuration = ($iInitDate - $iDelegateDate ) / 3600;
            $oAppDel->setDelQueueDuration( $queueDuration);
      	  }
      	  else {
        	  $queueDuration = ( strtotime( 'now' ) - $iDelegateDate ) / 3600;
            $oAppDel->setDelQueueDuration( $queueDuration);
      	  }
        }
        
      	if ( $isFinished == 0 ) {
        	if ( $iFinishDate != NULL ) {
            $oAppDel->setDelFinished(1);
        	  $delDuration = ($iFinishDate - $iInitDate ) / 3600;
            $oAppDel->setDelDuration( $delDuration);
      	  }
      	  //else {
        	//  $queueDuration = ( strtotime( 'now' ) - $iDelegateDate ) / 3600;
          //  $oAppDel->setDelQueueDuration( $queueDuration);
          //}
      	}
        //and finally save the record
        $oAppDel->save();
        //krumo ( $row );
      	
        $rs->next();
        $row = $rs->getRow();
      }
    }
    catch ( Exception $oError) {
      krumo ( $oError->getMessage() );
    }
  }
} // AppDelegation
