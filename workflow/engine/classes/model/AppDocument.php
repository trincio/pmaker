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

require_once 'classes/model/om/BaseAppDocument.php';
require_once 'classes/model/Content.php';

/**
 * Skeleton subclass for representing a row from the 'APP_DOCUMENT' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class AppDocument extends BaseAppDocument {

  /**
	 * This value goes in the content table
	 * @var string
	 */
	protected $app_doc_title = '';

  /**
	 * This value goes in the content table
	 * @var		string
	 */
	protected $app_doc_comment = '';

  /**
	 * This value goes in the content table
	 * @var string
	 */
	protected $app_doc_filename = '';

  /*
	* Load the application document registry
	* @param string $sAppDocUid
	* @return variant
	*/
  public function load($sAppDocUid)
  {
  	try {
  	  $oAppDocument = AppDocumentPeer::retrieveByPK($sAppDocUid);
  	  if (!is_null($oAppDocument))
  	  {
  	    $aFields = $oAppDocument->toArray(BasePeer::TYPE_FIELDNAME);
  	    $aFields['APP_DOC_TITLE']    = $oAppDocument->getAppDocTitle();
  	    $aFields['APP_DOC_COMMENT']  = $oAppDocument->getAppDocComment();
        $aFields['APP_DOC_FILENAME'] = $oAppDocument->getAppDocFilename();
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
  public function getLastIndex( $sAppUid )
  {
  	try {
  	  $oCriteria = new Criteria();
  	  $oCriteria->add( AppDocumentPeer::APP_UID , $sAppUid );
      //$oCriteria->addAscendingOrderByColumn ( AppDocumentPeer::APP_DOC_INDEX );
      $oCriteria->addDescendingOrderByColumn( AppDocumentPeer::APP_DOC_INDEX );
      $lastAppDoc = AppDocumentPeer::doSelectOne($oCriteria);
  	  if (!is_null($lastAppDoc))
  	  {
  	    return $lastAppDoc->getAppDocIndex();
      }
      else {
        return 0;
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
  	$oConnection = Propel::getConnection(AppDocumentPeer::DATABASE_NAME);
  	try {
  	  $oAppDocument = new AppDocument();
  	  $oAppDocument->fromArray($aData, BasePeer::TYPE_FIELDNAME);
  	  $sUID = G::generateUniqueID();
  	  $oAppDocument->setAppDocUid( $sUID );
  	  $oAppDocument->setAppDocIndex($this->getLastIndex( $oAppDocument->getAppUid() )+1);
  	  if ($oAppDocument->validate()) {
        $oConnection->begin();
        if (isset($aData['APP_DOC_TITLE'])) {
          $oAppDocument->setAppDocTitle($aData['APP_DOC_TITLE']);
        }
        if (isset($aData['APP_DOC_COMMENT'])) {
          $oAppDocument->setAppDocComment($aData['APP_DOC_COMMENT']);
        }
        if (isset($aData['APP_DOC_FILENAME'])) {
          $oAppDocument->setAppDocFilename($aData['APP_DOC_FILENAME']);
        }
        $iResult = $oAppDocument->save();
        $oConnection->commit();
        $this->fromArray($oAppDocument->toArray( BasePeer::TYPE_FIELDNAME ), BasePeer::TYPE_FIELDNAME);
        return $sUID;
  	  }
  	  else {
  	  	$sMessage = '';
  	    $aValidationFailures = $oAppDocument->getValidationFailures();
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
  	$oConnection = Propel::getConnection(AppDocumentPeer::DATABASE_NAME);
  	try {
  	  $oAppDocument = AppDocumentPeer::retrieveByPK($aData['APP_DOC_UID']);
  	  if (!is_null($oAppDocument))
  	  {
  	  	$oAppDocument->fromArray($aData, BasePeer::TYPE_FIELDNAME);
  	    if ($oAppDocument->validate()) {
  	    	$oConnection->begin();
          if (isset($aData['APP_DOC_TITLE']))
          {
            $oAppDocument->setAppDocTitle($aData['APP_DOC_TITLE']);
          }
          if (isset($aData['APP_DOC_COMMENT']))
          {
            $oAppDocument->setAppDocComment($aData['APP_DOC_COMMENT']);
          }
          if (isset($aData['APP_DOC_FILENAME']))
          {
            $oAppDocument->setAppDocFilename($aData['APP_DOC_FILENAME']);
          }
          $iResult = $oAppDocument->save();
          $oConnection->commit();
          return $iResult;
  	    }
  	    else {
  	    	$sMessage = '';
  	      $aValidationFailures = $oAppDocument->getValidationFailures();
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
  public function remove($sAppDocUid)
  {
  	$oConnection = Propel::getConnection(AppDocumentPeer::DATABASE_NAME);
  	try {
  	  $oAppDocument = AppDocumentPeer::retrieveByPK($sAppDocUid);
  	  if (!is_null($oAppDocument))
  	  {
  	  	$oConnection->begin();
        Content::removeContent('APP_DOC_TITLE', '', $oAppDocument->getAppDocUid());
        Content::removeContent('APP_DOC_COMMENT', '', $oAppDocument->getAppDocUid());
        Content::removeContent('APP_DOC_FILENAME', '', $oAppDocument->getAppDocUid());
        $iResult = $oAppDocument->delete();
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
	 * Get the [app_doc_title] column value.
	 * @return string
	 */
	public function getAppDocTitle()
	{
		if ($this->app_doc_title == '') {
			try {
	      $this->app_doc_title = Content::load('APP_DOC_TITLE', '', $this->getAppDocUid(), (defined('SYS_LANG') ? SYS_LANG : 'en'));
	    }
	    catch (Exception $oError) {
	    	throw($oError);
	    }
	  }
		return $this->app_doc_title;
	}

	/**
	 * Set the [app_doc_title] column value.
	 *
	 * @param string $sValue new value
	 * @return void
	 */
	public function setAppDocTitle($sValue)
	{
		if ($sValue !== null && !is_string($sValue)) {
			$sValue = (string)$sValue;
		}
		if ($this->app_doc_title !== $sValue || $sValue === '') {
			try {
				$this->app_doc_title = $sValue;
	      $iResult = Content::addContent('APP_DOC_TITLE', '', $this->getAppDocUid(), (defined('SYS_LANG') ? SYS_LANG : 'en'), $this->app_doc_title);
	    }
	    catch (Exception $oError) {
	    	$this->app_doc_title = '';
	    	throw($oError);
	    }
		}
	}

	/**
	 * Get the [app_doc_comment] column value.
	 * @return string
	 */
	public function getAppDocComment()
	{
		if ($this->app_doc_comment == '') {
			try {
		    $this->app_doc_comment = Content::load('APP_DOC_COMMENT', '', $this->getAppDocUid(), (defined('SYS_LANG') ? SYS_LANG : 'en'));
		  }
	    catch (Exception $oError) {
	    	throw($oError);
	    }
	  }
		return $this->app_doc_comment;
	}

	/**
	 * Set the [app_doc_comment] column value.
	 *
	 * @param string $sValue new value
	 * @return void
	 */
	public function setAppDocComment($sValue)
	{
		if ($sValue !== null && !is_string($sValue)) {
			$sValue = (string)$sValue;
		}
		if ($this->app_doc_comment !== $sValue || $sValue === '') {
			try {
				$this->app_doc_comment = $sValue;
	      $iResult = Content::addContent('APP_DOC_COMMENT', '', $this->getAppDocUid(), (defined('SYS_LANG') ? SYS_LANG : 'en'), $this->app_doc_comment);
	    }
	    catch (Exception $oError) {
	    	$this->app_doc_comment = '';
	    	throw($oError);
	    }
		}
	}

	/**
	 * Get the [app_doc_filename] column value.
	 * @return string
	 */
	public function getAppDocFilename()
	{
		if ($this->app_doc_filename == '') {
			try {
		    $this->app_doc_filename = Content::load('APP_DOC_FILENAME', '', $this->getAppDocUid(), (defined('SYS_LANG') ? SYS_LANG : 'en'));
		  }
	    catch (Exception $oError) {
	    	throw($oError);
	    }
		}
		return $this->app_doc_filename;
	}

	/**
	 * Set the [app_doc_filename] column value.
	 *
	 * @param string $sValue new value
	 * @return void
	 */
	public function setAppDocFilename($sValue)
	{
		if ($sValue !== null && !is_string($sValue)) {
			$sValue = (string)$sValue;
		}
		if ($this->app_doc_filename !== $sValue || $sValue === '') {
			try {
				$this->app_doc_filename = $sValue;
	      $iResult = Content::addContent('APP_DOC_FILENAME', '', $this->getAppDocUid(), (defined('SYS_LANG') ? SYS_LANG : 'en'), $this->app_doc_filename);
	    }
	    catch (Exception $oError) {
	    	$this->app_doc_filename = '';
	    	throw($oError);
	    }
		}
	}

} // AppDocument