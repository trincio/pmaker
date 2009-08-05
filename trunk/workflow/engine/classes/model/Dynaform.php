<?php
/**
 * Dynaform.php
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

require_once 'classes/model/om/BaseDynaform.php';
require_once 'classes/model/Content.php';


/**
 * Skeleton subclass for representing a row from the 'DYNAFORM' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class Dynaform extends BaseDynaform {
	/**
	 * This value goes in the content table
	 * @var        string
	 */
	protected $dyn_title = '';

	/**
	 * Get the [Dyn_title] column value.
	 * @return     string
	 */
	public function getDynTitle()
	{
	  if ( $this->getDynUid() == '' ) {
      throw ( new Exception( "Error in getDynTitle, the DYN_UID can't be blank") );
	  }
	  $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
	  $this->dyn_title = Content::load ( 'DYN_TITLE', '', $this->getDynUid(), $lang );
		return $this->dyn_title;
	}

	/**
	 * Set the [Dyn_title] column value.
	 *
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDynTitle($v)
	{
	  if ( $this->getDynUid() == '' ) {
      throw ( new Exception( "Error in setDynTitle, the DYN_UID can't be blank") );
	  }
		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v;
		}

		if ($this->dyn_title !== $v || $v === '') {
			$this->dyn_title = $v;
  	  $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
	    $res = Content::addContent( 'DYN_TITLE', '', $this->getDynUid(), $lang, $this->dyn_title );
		}

	} // set()

	/**
	 * This value goes in the content table
	 * @var        string
	 */
	protected $dyn_description = '';

	/**
	 * Get the [Dyn_description] column value.
	 * @return     string
	 */
	public function getDynDescription()
	{
	  if ( $this->getDynUid() == '' ) {
      throw ( new Exception( "Error in getDynDescription, the DYN_UID can't be blank") );
	  }
	  $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
	  $this->dyn_description = Content::load ( 'DYN_DESCRIPTION', '', $this->getDynUid(), $lang );
		return $this->dyn_description;
	}

	/**
	 * Set the [Dyn_description] column value.
	 *
	 * @param      string $v new value
	 * @return     void
	 */
	public function setDynDescription($v)
	{
	  if ( $this->getDynUid() == '' ) {
      throw ( new Exception( "Error in setDynDescription, the DYN_UID can't be blank") );
	  }
		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v;
		}

		if ($this->dyn_description !== $v || $v === '') {
			$this->dyn_description = $v;
  	  $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
	    $res = Content::addContent( 'DYN_DESCRIPTION', '', $this->getDynUid(), $lang, $this->dyn_description );
		}

	} // set()

	/**
	 * Creates the Dynaform
	 *
	 * @param      array $aData  Fields with :
	 *                   $aData['DYN_UID'] the dynaform id
	 *                   $aData['USR_UID'] the userid
	 * @return     void
	 */

  function create ($aData ) {
    if ( !isset ( $aData['PRO_UID'] ) ) {
       throw ( new PropelException ( 'The dynaform cannot be created. The PRO_UID is empty.' ) );
    }
  	$con = Propel::getConnection( DynaformPeer::DATABASE_NAME );
    try {
      if ( isset ( $aData['DYN_UID'] ) && $aData['DYN_UID']== '' )
        unset ( $aData['DYN_UID'] );
      if ( !isset ( $aData['DYN_UID'] ) )
  	    $dynUid  = ( G::generateUniqueID() );
  	  else
  	    $dynUid  = $aData['DYN_UID'];
  	  $this->setDynUid          ( $dynUid );
      $this->setProUid          ( $aData['PRO_UID'] );
      $this->setDynType         ( isset($aData['DYN_TYPE'])?$aData['DYN_TYPE']:'xmlform' );
      $this->setDynFilename     ( $aData['PRO_UID'] . PATH_SEP . $dynUid );

	    if ( $this->validate() ) {
        $con->begin();
        $res = $this->save();

        if (isset ( $aData['DYN_TITLE'] ) )
          $this->setDynTitle (  $aData['DYN_TITLE'] );
        else
          $this->setDynTitle (  'Default Dynaform Title' );

        if (isset ( $aData['DYN_DESCRIPTION'] ) )
          $this->setDynDescription (  $aData['DYN_DESCRIPTION'] );
        else
          $this->setDynDescription (  'Default Dynaform Description' );

        $con->commit();
        $sXml  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		    $sXml .= '<dynaForm type="' . $this->getDynType() . '" name="' . $this->getProUid() . '/' . $this->getDynUid() . '" width="500" enabletemplate="0" mode="edit">'."\n";
		    $sXml .= '</dynaForm>';
		    G::verifyPath(PATH_DYNAFORM . $this->getProUid(), true);
		    $oFile = fopen(PATH_DYNAFORM . $this->getProUid() . '/' . $this->getDynUid() . '.xml', 'w');
		    fwrite($oFile, $sXml);
		    fclose($oFile);
        return $this->getDynUid();
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
	 * Load the Dynaform row specified in [dyn_id] column value.
	 *
	 * @param      string $ProUid   the uid of the Prolication
	 * @return     array  $Fields   the fields
	 */

  function Load ( $ProUid ) {
  	$con = Propel::getConnection(DynaformPeer::DATABASE_NAME);
    try {
      $oPro = DynaformPeer::retrieveByPk( $ProUid );
  	  if ( get_class ($oPro) == 'Dynaform' ) {
  	    $aFields = $oPro->toArray(BasePeer::TYPE_FIELDNAME);
  	    $this->fromArray ($aFields, BasePeer::TYPE_FIELDNAME );
  	    $aFields['DYN_TITLE']       = $oPro->getDynTitle();
  	    $aFields['DYN_DESCRIPTION'] = $oPro->getDynDescription();
  	    $this->setDynTitle       (  $oPro->getDynTitle() );
  	    $this->setDynDescription (  $oPro->getDynDescription() );
  	    return $aFields;
  	  }
      else {
        throw(new Exception( "The row '$ProUid' in table Dynaform doesn't exists!" ));
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
  	$con = Propel::getConnection( DynaformPeer::DATABASE_NAME );
  	try {
      $con->begin();
  	  $oPro = DynaformPeer::retrieveByPK( $aData['DYN_UID'] );
  	  if ( get_class ($oPro) == 'Dynaform' ) {
  	  	$oPro->fromArray( $aData, BasePeer::TYPE_FIELDNAME );
  	    if ($oPro->validate()) {
  	      if ( isset ( $aData['DYN_TITLE'] ) )
            $oPro->setDynTitle( $aData['DYN_TITLE'] );
  	      if ( isset ( $aData['DYN_DESCRIPTION'] ) )
            $oPro->setDynDescription( $aData['DYN_DESCRIPTION'] );
          $res = $oPro->save();
          $con->commit();
          return $res;
  	    }
  	    else {
         foreach($this->getValidationFailures() as $objValidationFailure)
           $msg .= $objValidationFailure->getMessage() . "<br/>";
         throw ( new PropelException ( 'The row cannot be created!', new PropelException ( $msg ) ) );
  	    }
      }
      else {
        $con->rollback();
        throw(new Exception( "The row '" . $aData['DYN_UID'] . "' in table Dynaform doesn't exists!" ));
      }
    }
    catch (Exception $oError) {
    	throw($oError);
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
      $ProUid = ( isset ( $ProUid['DYN_UID'] ) ? $ProUid['DYN_UID'] : '' );
    }
  	try {
  	  $oPro = DynaformPeer::retrieveByPK( $ProUid );
  	  if (!is_null($oPro))
  	  {
        Content::removeContent('DYN_TITLE', '',       $oPro->getDynUid());
        Content::removeContent('DYN_DESCRIPTION', '', $oPro->getDynUid());
        $iResult = $oPro->delete();
        if (file_exists(PATH_DYNAFORM . $oPro->getProUid() . PATH_SEP . $oPro->getDynUid() . '.xml')) {
          unlink(PATH_DYNAFORM . $oPro->getProUid() . PATH_SEP . $oPro->getDynUid() . '.xml');
        }
        if (file_exists(PATH_DYNAFORM . $oPro->getProUid() . PATH_SEP . $oPro->getDynUid() . '_tmp0.xml')) {
          unlink(PATH_DYNAFORM . $oPro->getProUid() . PATH_SEP . $oPro->getDynUid() . '_tmp0.xml');
        }
        if (file_exists(PATH_DYNAFORM . $oPro->getProUid() . PATH_SEP . $oPro->getDynUid() . '.html')) {
          unlink(PATH_DYNAFORM . $oPro->getProUid() . PATH_SEP . $oPro->getDynUid() . '.html');
        }
        if (file_exists(PATH_DYNAFORM . $oPro->getProUid() . PATH_SEP . $oPro->getDynUid() . '_tmp0.html')) {
          unlink(PATH_DYNAFORM . $oPro->getProUid() . PATH_SEP . $oPro->getDynUid() . '_tmp0.html');
        }
        return $iResult;
      }
      else {
        throw(new Exception( "The row '$ProUid' in table Dynaform doesn't exists!" ));
      }
    }
    catch (Exception $oError) {
      throw($oError);
    }
  }

  public function exists($DynUid)
  {
    $oPro = DynaformPeer::retrieveByPk( $DynUid );
    return ( get_class ($oPro) == 'Dynaform' );
  }

	/**
	 * verify if Dynaform row specified in [DynUid] exists.
	 *
	 * @param      string $sProUid   the uid of the Prolication
	 */

  function dynaformExists ( $DynUid ) {
  	$con = Propel::getConnection(TaskPeer::DATABASE_NAME);
    try {
      $oDyn = DynaformPeer::retrieveByPk( $DynUid );
  	  if ( get_class ($oDyn) == 'Dynaform' ) {
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
  
  function getDynaformContent( $dynaformUid) {
  	$content = '';
    $fields = $this->Load ( $dynaformUid);
    $filename = PATH_DYNAFORM . $fields['PRO_UID'] . PATH_SEP . $fields['DYN_UID'] . '.xml';
    if (file_exists( $filename )) {
    	$content = file_get_contents ( $filename );
    }
    
    return $content;
  }    

  function getDynaformFields( $dynaformUid) {
  	$content = '';
    $fields = $this->Load ( $dynaformUid);
    $filename = PATH_DYNAFORM . $fields['PRO_UID'] . PATH_SEP . $fields['DYN_UID'] . '.xml';
    if (file_exists( $filename )) {
    	$content = file_get_contents ( $filename );
    }
    
    //$G_FORM = new Form ( $Part['File'] , $sPath , SYS_LANG, false );
    $G_FORM = new xmlform ( $fields['DYN_FILENAME'] , PATH_DYNAFORM );
    $G_FORM->parseFile( $filename , SYS_LANG, true );
    
    return $G_FORM->fields;
  }    

} // Dynaform
