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

require_once 'classes/model/om/BaseSwimlanesElements.php';
require_once 'classes/model/Content.php';

/**
 * Skeleton subclass for representing a row from the 'SWIMLANES_ELEMENTS' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the input directory.
 *
 * @package    classes.model
 */
class SwimlanesElements extends BaseSwimlanesElements {

  /**
	 * This value goes in the content table
	 * @var string
	 */
	protected $swi_text = '';

  /*
	* Load the application document registry
	* @param string $sAppDocUid
	* @return variant
	*/
  public function load($sSwiEleUid)
  {
  	try {
  	  $oSwimlanesElements = SwimlanesElementsPeer::retrieveByPK($sSwiEleUid);
  	  if (!is_null($oSwimlanesElements))
  	  {
  	    $aFields = $oSwimlanesElements->toArray(BasePeer::TYPE_FIELDNAME);
  	    $aFields['SWI_TEXT'] = $oSwimlanesElements->getSwiEleText();
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
  	$oConnection = Propel::getConnection(SwimlanesElementsPeer::DATABASE_NAME);
  	try {
  		$aData['SWI_UID']   = G::generateUniqueID();
  	  $oSwimlanesElements = new SwimlanesElements();
  	  $oSwimlanesElements->fromArray($aData, BasePeer::TYPE_FIELDNAME);
  	  if ($oSwimlanesElements->validate()) {
        $oConnection->begin();
        if (isset($aData['SWI_TEXT'])) {
          $oSwimlanesElements->setSwiEleText($aData['SWI_TEXT']);
        }
        $iResult = $oSwimlanesElements->save();
        $oConnection->commit();
        return $aData['SWI_UID'];
  	  }
  	  else {
  	  	$sMessage = '';
  	    $aValidationFailures = $oSwimlanesElements->getValidationFailures();
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
  	$oConnection = Propel::getConnection(SwimlanesElementsPeer::DATABASE_NAME);
  	try {
  	  $oSwimlanesElements = SwimlanesElementsPeer::retrieveByPK($aData['SWI_UID']);
  	  if (!is_null($oSwimlanesElements))
  	  {
  	  	$oSwimlanesElements->fromArray($aData, BasePeer::TYPE_FIELDNAME);
  	    if ($oSwimlanesElements->validate()) {
  	    	$oConnection->begin();
          if (isset($aData['SWI_TEXT']))
          {
            $oSwimlanesElements->setSwiEleText($aData['SWI_TEXT']);
          }
          $iResult = $oSwimlanesElements->save();
          $oConnection->commit();
          return $iResult;
  	    }
  	    else {
  	    	$sMessage = '';
  	      $aValidationFailures = $oSwimlanesElements->getValidationFailures();
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
  public function remove($sSwiEleUid)
  {
  	$oConnection = Propel::getConnection(SwimlanesElementsPeer::DATABASE_NAME);
  	try {
  	  $oSwimlanesElements = SwimlanesElementsPeer::retrieveByPK($sSwiEleUid);
  	  if (!is_null($oSwimlanesElements))
  	  {
  	  	$oConnection->begin();
        Content::removeContent('SWI_TEXT', '', $oSwimlanesElements->getSwiUid());
        $iResult = $oSwimlanesElements->delete();
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
	 * Get the [swi_text] column value.
	 * @return string
	 */
	public function getSwiEleText()
	{
		if ($this->swi_text == '') {
			try {
	      $this->swi_text = Content::load('SWI_TEXT', '', $this->getSwiUid(), (defined('SYS_LANG') ? SYS_LANG : 'en'));
	    }
	    catch (Exception $oError) {
	    	throw($oError);
	    }
	  }
		return $this->swi_text;
	}

	/**
	 * Set the [swi_text] column value.
	 *
	 * @param string $sValue new value
	 * @return void
	 */
	public function setSwiEleText($sValue)
	{
		if ($sValue !== null && !is_string($sValue)) {
			$sValue = (string)$sValue;
		}
		if ($this->swi_text !== $sValue || $sValue === '') {
			try {
				$this->swi_text = $sValue;
	      $iResult = Content::addContent('SWI_TEXT', '', $this->getSwiUid(), (defined('SYS_LANG') ? SYS_LANG : 'en'), $this->swi_text);
	    }
	    catch (Exception $oError) {
	    	$this->swi_text = '';
	    	throw($oError);
	    }
		}
	}

} // SwimlanesElements