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

require_once 'classes/model/om/BaseInputDocument.php';
require_once 'classes/model/Content.php';

/**
 * Skeleton subclass for representing a row from the 'INPUT_DOCUMENT' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the input directory.
 *
 * @package    classes.model
 */
class InputDocument extends BaseInputDocument {

  /**
	 * This value goes in the content table
	 * @var string
	 */
	protected $inp_doc_title = '';

  /**
	 * This value goes in the content table
	 * @var		string
	 */
	protected $inp_doc_description = '';

  /*
	* Load the application document registry
	* @param string $sAppDocUid
	* @return variant
	*/
  public function load($sInpDocUid)
  {
  	try {
  	  $oInputDocument = InputDocumentPeer::retrieveByPK($sInpDocUid);
  	  if (!is_null($oInputDocument))
  	  {
  	    $aFields = $oInputDocument->toArray(BasePeer::TYPE_FIELDNAME);
  	    $aFields['INP_DOC_TITLE']       = $oInputDocument->getInpDocTitle();
  	    $aFields['INP_DOC_DESCRIPTION'] = $oInputDocument->getInpDocDescription();
  	    $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME);
  	    return $aFields;
      }
      else {
        throw(new Exception('This row doesn\'t exists!'));
      }
    }
    catch (Exception $oError) {
    	throw($oError);
    }
  }

  /**
	 * Create the application document registry
   * @param array $aData
   * @return string
  **/
  public function create($aData)
  {
  	$oConnection = Propel::getConnection(InputDocumentPeer::DATABASE_NAME);
  	try {
  		$aData['INP_DOC_UID'] = G::generateUniqueID();
  	  $oInputDocument       = new InputDocument();
  	  $oInputDocument->fromArray($aData, BasePeer::TYPE_FIELDNAME);
  	  if ($oInputDocument->validate()) {
        $oConnection->begin();
        if (isset($aData['INP_DOC_TITLE'])) {
          $oInputDocument->setInpDocTitle($aData['INP_DOC_TITLE']);
        }
        if (isset($aData['INP_DOC_DESCRIPTION'])) {
          $oInputDocument->setInpDocDescription($aData['INP_DOC_DESCRIPTION']);
        }
        $iResult = $oInputDocument->save();
        $oConnection->commit();
        return $aData['INP_DOC_UID'];
  	  }
  	  else {
  	  	$sMessage = '';
  	    $aValidationFailures = $oInputDocument->getValidationFailures();
  	    foreach($aValidationFailures as $oValidationFailure) {
          $sMessage .= $oValidationFailure->getMessage() . '<br />';
        }
        throw(new Exception('The registry cannot be created!<br />'.$sMessage));
  	  }
  	}
    catch (Exception $oError) {
      $oConnection->rollback();
    	throw($oError);
    }
  }

  /**
	 * Update the application document registry
   * @param array $aData
   * @return string
  **/
  public function update($aData)
  {
  	$oConnection = Propel::getConnection(InputDocumentPeer::DATABASE_NAME);
  	try {
  	  $oInputDocument = InputDocumentPeer::retrieveByPK($aData['INP_DOC_UID']);
  	  if (!is_null($oInputDocument))
  	  {
  	  	$oInputDocument->fromArray($aData, BasePeer::TYPE_FIELDNAME);
  	    if ($oInputDocument->validate()) {
  	    	$oConnection->begin();
          if (isset($aData['INP_DOC_TITLE']))
          {
            $oInputDocument->setInpDocTitle($aData['INP_DOC_TITLE']);
          }
          if (isset($aData['INP_DOC_DESCRIPTION']))
          {
            $oInputDocument->setInpDocDescription($aData['INP_DOC_DESCRIPTION']);
          }
          $iResult = $oInputDocument->save();
          $oConnection->commit();
          return $iResult;
  	    }
  	    else {
  	    	$sMessage = '';
  	      $aValidationFailures = $oInputDocument->getValidationFailures();
  	      foreach($aValidationFailures as $oValidationFailure) {
            $sMessage .= $oValidationFailure->getMessage() . '<br />';
          }
          throw(new Exception('The registry cannot be updated!<br />'.$sMessage));
  	    }
      }
      else {
        throw(new Exception('This row doesn\'t exists!'));
      }
    }
    catch (Exception $oError) {
    	$oConnection->rollback();
    	throw($oError);
    }
  }

  /**
	 * Remove the application document registry
   * @param array $aData
   * @return string
  **/
  public function remove($sInpDocUid)
  {
  	$oConnection = Propel::getConnection(InputDocumentPeer::DATABASE_NAME);
  	try {
  	  $oInputDocument = InputDocumentPeer::retrieveByPK($sInpDocUid);
  	  if (!is_null($oInputDocument))
  	  {
  	  	$oConnection->begin();
        Content::removeContent('INP_DOC_TITLE', '', $oInputDocument->getInpDocUid());
        Content::removeContent('INP_DOC_DESCRIPTION', '', $oInputDocument->getInpDocUid());
        $iResult = $oInputDocument->delete();
        $oConnection->commit();
        return $iResult;
      }
      else {
        throw(new Exception('This row doesn\'t exists!'));
      }
    }
    catch (Exception $oError) {
    	$oConnection->rollback();
      throw($oError);
    }
  }

	/**
	 * Get the [inp_doc_title] column value.
	 * @return string
	 */
	public function getInpDocTitle()
	{
		if ($this->inp_doc_title == '') {
			try {
	      $this->inp_doc_title = Content::load('INP_DOC_TITLE', '', $this->getInpDocUid(), (defined('SYS_LANG') ? SYS_LANG : 'en'));
	    }
	    catch (Exception $oError) {
	    	throw($oError);
	    }
	  }
		return $this->inp_doc_title;
	}

	/**
	 * Set the [inp_doc_title] column value.
	 *
	 * @param string $sValue new value
	 * @return void
	 */
	public function setInpDocTitle($sValue)
	{
		if ($sValue !== null && !is_string($sValue)) {
			$sValue = (string)$sValue;
		}
		if ($this->inp_doc_title !== $sValue || $sValue === '') {
			try {
				$this->inp_doc_title = $sValue;
	      $iResult = Content::addContent('INP_DOC_TITLE', '', $this->getInpDocUid(), (defined('SYS_LANG') ? SYS_LANG : 'en'), $this->inp_doc_title);
	    }
	    catch (Exception $oError) {
	    	$this->inp_doc_title = '';
	    	throw($oError);
	    }
		}
	}

	/**
	 * Get the [inp_doc_comment] column value.
	 * @return string
	 */
	public function getInpDocDescription()
	{
		if ($this->inp_doc_description == '') {
			try {
		    $this->inp_doc_description = Content::load('INP_DOC_DESCRIPTION', '', $this->getInpDocUid(), (defined('SYS_LANG') ? SYS_LANG : 'en'));
		  }
	    catch (Exception $oError) {
	    	throw($oError);
	    }
	  }
		return $this->inp_doc_description;
	}

	/**
	 * Set the [inp_doc_comment] column value.
	 *
	 * @param string $sValue new value
	 * @return void
	 */
	public function setInpDocDescription($sValue)
	{
		if ($sValue !== null && !is_string($sValue)) {
			$sValue = (string)$sValue;
		}
		if ($this->inp_doc_description !== $sValue || $sValue === '') {
			try {
				$this->inp_doc_description = $sValue;
	      $iResult = Content::addContent('INP_DOC_DESCRIPTION', '', $this->getInpDocUid(), (defined('SYS_LANG') ? SYS_LANG : 'en'), $this->inp_doc_description);
	    }
	    catch (Exception $oError) {
	    	$this->inp_doc_description = '';
	    	throw($oError);
	    }
		}
	}

} // InputDocument
