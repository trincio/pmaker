<?php
/**
 * Process.php
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

require_once 'classes/model/om/BaseProcess.php';
require_once 'classes/model/Content.php';


/**
 * Skeleton subclass for representing a row from the 'PROCESS' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class Process extends BaseProcess {
	/**
	 * This value goes in the content table
	 * @var        string
	 */
	protected $pro_title = '';

	/**
	 * Get the [Pro_title] column value.
	 * @return     string
	 */
	public function getProTitle()
	{
	  if ( $this->getProUid() == '' ) {
      throw ( new Exception( "Error in getProTitle, the PRO_UID can't be blank") );
	  }
	  $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
	  $this->pro_title = Content::load ( 'PRO_TITLE', '', $this->getProUid(), $lang );
		return $this->pro_title;
	}

	/**
	 * Set the [Pro_title] column value.
	 *
	 * @param      string $v new value
	 * @return     void
	 */
	public function setProTitle($v)
	{
	  if ( $this->getProUid() == '' ) {
      throw ( new Exception( "Error in setProTitle, the PRO_UID can't be blank") );
	  }
		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v;
		}

		if ($this->pro_title !== $v || $v === '') {
			$this->pro_title = $v;
    	  $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
	    $res = Content::addContent( 'PRO_TITLE', '', $this->getProUid(), $lang, $this->pro_title );
		}

	} // set()

	/**
	 * This value goes in the content table
	 * @var        string
	 */
	protected $pro_description = '';

	/**
	 * Get the [Pro_description] column value.
	 * @return     string
	 */
	public function getProDescription()
	{
	  if ( $this->getProUid() == '' ) {
      throw ( new Exception( "Error in getProDescription, the PRO_UID can't be blank") );
	  }
	  $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
	  $this->pro_description = Content::load ( 'PRO_DESCRIPTION', '', $this->getProUid(), $lang );
		return $this->pro_description;
	}

	/**
	 * Set the [Pro_description] column value.
	 *
	 * @param      string $v new value
	 * @return     void
	 */
	public function setProDescription($v)
	{
	  if ( $this->getProUid() == '' ) {
      throw ( new Exception( "Error in setProDescription, the PRO_UID can't be blank") );
	  }
		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v;
		}

		if ($this->pro_description !== $v || $v === '') {
			$this->pro_description = $v;
  	  $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
	    $res = Content::addContent( 'PRO_DESCRIPTION', '', $this->getProUid(), $lang, $this->pro_description );
		}

	} // set()

	/**
	 * Creates the Process
	 *
	 * @param      array $aData  Fields with :
	 *                   $aData['PRO_UID'] the process id
	 *                   $aData['USR_UID'] the userid
	 * @return     void
	 */

  function create ($aData ) {
    if ( !isset ( $aData['USR_UID'] ) ) {
       throw ( new PropelException ( 'The process cannot be created. The USR_UID is empty.' ) );
    }
  	$con = Propel::getConnection( ProcessPeer::DATABASE_NAME );
    try {
      do {
       $sNewProUid = G::generateUniqueID() ;
      } while ( $this->processExists ( $sNewProUid ) );
      
  	  $this->setProUid          ( $sNewProUid );
      $this->setProParent       ( $sNewProUid );
      $this->setProTime         ( 1 );
      $this->setProTimeunit     ( 'DAYS' );
      $this->setProStatus       ( 'ACTIVE' );
      $this->setProTypeDay      ( '' );
      $this->setProType         ( 'NORMAL' );
      $this->setProAssignment   ( 'FALSE' );
      $this->setProShowMap      ( '' );
      $this->setProShowMessage  ( '' );
      $this->setProShowDelegate ( '' );
      $this->setProShowDynaform ( '' );
      $this->setProCategory     ( '' );
      $this->setProSubCategory  ( '' );
      $this->setProIndustry     ( '' );
      $this->setProCreateDate   ('now' );
      $this->setProCreateUser   ( $aData['USR_UID'] );
      $this->setProHeight       ( 5000 );
      $this->setProWidth        ( 10000 );
      $this->setProTitleX       ( 0 );
      $this->setProTitleY       ( 0 );

	    if ( $this->validate() ) {
        $con->begin();
        $res = $this->save();

        if (isset ( $aData['PRO_TITLE'] ) )
          $this->setProTitle (  $aData['PRO_TITLE'] );
        else
          $this->setProTitle (  'Default Process Title' );

        if (isset ( $aData['PRO_DESCRIPTION'] ) )
          $this->setProDescription (  $aData['PRO_DESCRIPTION'] );
        else
          $this->setProDescription (  'Default Process Description' );

        $con->commit();
        return $this->getProUid();
      }
      else {
       $msg = '';
       foreach($this->getValidationFailures() as $objValidationFailure)
         $msg .= $objValidationFailure->getMessage() . "<br/>";

       throw ( new PropelException ( 'The row cannot be created!', new PropelException ( $msg ) ) );
      }

    }
    catch (Exception $e) {
      $con->rollback();
      throw ($e);
    }
  }
  
	/**
	 * verify if Process row specified in [pro_id] exists.
	 *
	 * @param      string $sProUid   the uid of the Prolication
	 */

  function processExists ( $ProUid ) {
  	$con = Propel::getConnection(ProcessPeer::DATABASE_NAME);
    try {
      $oPro = ProcessPeer::retrieveByPk( $ProUid );
  	  if ( get_class ($oPro) == 'Process' ) {
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
 
  

	/**
	 * Load the Process row specified in [pro_id] column value.
	 *
	 * @param      string $ProUid   the uid of the Prolication
	 * @return     array  $Fields   the fields
	 */

  function load ( $ProUid ) {
  	
	#verification PRO_DEBUG field routines
	$con = Propel::getConnection("workflow");
	$stmt = $con->prepareStatement("SHOW COLUMNS FROM PROCESS");
	$rs = $stmt->executeQuery();
	$rs->next();
	
	while($rs->next()) {
		$row = $rs->getRow();
		$fields[] = $row['Field'];
  	}
  
  	if (in_array ('PRO_DEBUG', $fields)) {
    	$SW_DEBUG = false;
	} else {
		$SW_DEBUG = true;
	}
  	
  	if($SW_DEBUG) { # if PRO_DEBUG filed doesn't exist! build it!
		$stmt = $con->prepareStatement("ALTER TABLE PROCESS ADD PRO_DEBUG INT(1) DEFAULT '0';");
		$rs = $stmt->executeQuery();
	}
  	########
  	
  	$con = Propel::getConnection(ProcessPeer::DATABASE_NAME);
    try {
      $oPro = ProcessPeer::retrieveByPk( $ProUid );
  	  if ( get_class ($oPro) == 'Process' ) {
  	    $aFields = $oPro->toArray(BasePeer::TYPE_FIELDNAME);
  	    $this->fromArray ($aFields, BasePeer::TYPE_FIELDNAME );
  	    $aFields['PRO_TITLE']       = $oPro->getProTitle();
  	    $aFields['PRO_DESCRIPTION'] = $oPro->getProDescription();
  	    $this->setProTitle (  $oPro->getProTitle() );
  	    $this->setProDescription (  $oPro->getProDescription() );
  	    
  	    //the following code is to copy the parent in old process, when the parent was empty.
  	    if ( $oPro->getProParent() == '' ) {
          $oPro->setProParent ( $oPro->getProUid() );
          $oPro->save();
  	    }
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
	 * Update the Prolication row
   * @param     array $aData
   * @return    variant
  **/

  public function update($aData)
  {
  	$con = Propel::getConnection( ProcessPeer::DATABASE_NAME );
  	try {
      $con->begin();
  	  $oPro = ProcessPeer::retrieveByPK( $aData['PRO_UID'] );
  	  if ( get_class ($oPro) == 'Process' ) {
  	  	$oPro->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
  	    if ($oPro->validate()) {
  	      if ( isset ( $aData['PRO_TITLE'] ) )
            $oPro->setProTitle( $aData['PRO_TITLE'] );
            
  	      if ( isset ( $aData['PRO_DESCRIPTION'] ) )
            $oPro->setProDescription( $aData['PRO_DESCRIPTION'] );
          $res = $oPro->save();
          $con->commit();
          return $res;
  	    }
  	    else {
         $msg = '';
         foreach($oPro->getValidationFailures() as $objValidationFailure)
           $msg .= $objValidationFailure->getMessage() . "<br/>";

         throw ( new Exception ( 'The row cannot be updated!' . $msg  ) );
  	    }
      }
      else {
        $con->rollback();
        throw(new Exception( "This row doesn't exists!" ));
      }
    }
    catch (Exception $oError) {
    	throw($oError);
    }
  }

	/**
	 * creates an Application row
   * @param     array $aData
   * @return    variant
  **/

  public function createRow($aData)
  {
  	$con = Propel::getConnection( ProcessPeer::DATABASE_NAME );
    $con->begin();
  	$this->setProUid          ( $aData['PRO_UID'] );
    $this->setProParent       ( $aData['PRO_PARENT'] );
    $this->setProTime         ( $aData['PRO_TIME'] );
    $this->setProTimeunit     ( $aData['PRO_TIMEUNIT'] );
    $this->setProStatus       ( $aData['PRO_STATUS'] );
    $this->setProTypeDay      ( $aData['PRO_TYPE_DAY'] );
    $this->setProType         ( $aData['PRO_TYPE'] );
    $this->setProAssignment   ( $aData['PRO_ASSIGNMENT'] );
    $this->setProShowMap      ( $aData['PRO_SHOW_MAP'] );
    $this->setProShowMessage  ( $aData['PRO_SHOW_MESSAGE'] );
    $this->setProShowDelegate ( $aData['PRO_SHOW_DELEGATE'] );
    $this->setProShowDynaform ( $aData['PRO_SHOW_DYNAFORM'] );
    $this->setProCategory     ( $aData['PRO_CATEGORY'] );
    $this->setProSubCategory  ( $aData['PRO_SUB_CATEGORY'] );
    $this->setProIndustry     ( $aData['PRO_INDUSTRY'] );
    $this->setProCreateDate   ( $aData['PRO_CREATE_DATE'] );
    $this->setProCreateUser   ( $aData['PRO_CREATE_USER'] );
    $this->setProHeight       ( $aData['PRO_HEIGHT'] );
    $this->setProWidth        ( $aData['PRO_WIDTH'] );
    $this->setProTitleX       ( $aData['PRO_TITLE_X'] );
    $this->setProTitleY       ( $aData['PRO_TITLE_Y'] );
	  if ( $this->validate() ) {
      $con->begin();
      $res = $this->save();

      if (isset ( $aData['PRO_TITLE'] ) )
        $this->setProTitle (  $aData['PRO_TITLE'] );
      else
        $this->setProTitle (  'Default Process Title' );

      if (isset ( $aData['PRO_DESCRIPTION'] ) )
        $this->setProDescription (  $aData['PRO_DESCRIPTION'] );
      else
        $this->setProDescription (  'Default Process Description' );

      $con->commit();
      return $this->getProUid();
    }
    else {
     $msg = '';
     foreach($this->getValidationFailures() as $objValidationFailure)
       $msg .= $objValidationFailure->getMessage() . "<br/>";

     throw ( new PropelException ( 'The row cannot be created!', new PropelException ( $msg ) ) );
    }

  }

  /**
	 * Remove the Prolication document registry
   * @param     array $aData or string $ProUid
   * @return    string
  **/
  public function remove($ProUid)
  {
    if ( is_array ( $ProUid ) ) {
      $ProUid = ( isset ( $ProUid['PRO_UID'] ) ? $ProUid['PRO_UID'] : '' );
    }
  	try {
  	  $oPro = ProcessPeer::retrieveByPK( $ProUid );
  	  if (!is_null($oPro))
  	  {
        Content::removeContent('PRO_TITLE', '',       $oPro->getProUid());
        Content::removeContent('PRO_DESCRIPTION', '', $oPro->getProUid());
        return $oPro->delete();
      }
      else {
        throw(new Exception( "This row doesn't exists!" ));
      }
    }
    catch (Exception $oError) {
      throw($oError);
    }
  }

  public function exists($ProUid)
  {
    $oPro = ProcessPeer::retrieveByPk( $ProUid );
    return ( get_class ($oPro) == 'Process' );
  }
} // Process
