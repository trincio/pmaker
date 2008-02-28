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
//
// It works with the table Process in a WF dataBase
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * AppDocument - Application Documents class
 * @package ProcessMaker
 * @author Julio Cesar Laura Avendaño
 * @copyright 2007 COLOSA
 */

G::LoadClass('pmObject');

class AppDocumentOld extends PmObject
{

	/*
	* Constructor
	* @param object $oConnection
	* @return variant
	*/
  function SetTo( $oConnection = null)
  {
  	if ($oConnection)
		{
			return parent::setTo($oConnection, 'APP_DOCUMENT', array('APP_DOC_UID'));
		}
		else
		{
			return;
		}
	}

  /*
	* Load the application document information
	* @param string $sUID
	* @return variant
	*/
	function load($sUID = '')
  {
    if ($sUID !== '')
  	{
  		parent::load($sUID);
  		//Start Comment: If Document type is "OUTPUT" obtain APP_DOC_TITLE
  		if ($this->Fields['APP_DOC_TYPE'] == 'OUTPUT')
  		{
  	    $this->content->load(array('CON_CATEGORY' => 'APP_DOC_TITLE', 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG));
			  $this->Fields['APP_DOC_TITLE'] = $this->content->Fields['CON_VALUE'];
		  }
		  //End Comment
		  //Start Comment: If Document type is "INPUT" obtain APP_DOC_COMMENT
  		if ($this->Fields['APP_DOC_TYPE'] == 'INPUT')
  		{
  	    $this->content->load(array('CON_CATEGORY' => 'APP_DOC_COMMENT', 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG));
			  $this->Fields['APP_DOC_COMMENT'] = $this->content->Fields['CON_VALUE'];
		  }
		  //End Comment
		  $this->content->load(array('CON_CATEGORY' => 'APP_DOC_FILENAME', 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG));
			$this->Fields['APP_DOC_FILENAME'] = $this->content->Fields['CON_VALUE'];
		  //End Comment
  	  return $this->Fields;
  	}
  	else
  	{
  		return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a load method without send the Application Document UID!',
    	                        'G_Error',
    	                        true);
  	}
  }

  /**
	 * Save the application document information
   *
   * @author Julio Cesar Laura Avendaño
   * @access public
   * @param array $aData
   * @return string
  **/
  function save ($aData)
  {
		$this->Fields = array(
                          'APP_UID'             => (isset($aData['APP_UID'])             ? $aData['APP_UID']             : $this->Fields['APP_UID']),
                          'DEL_INDEX'           => (isset($aData['DEL_INDEX'])           ? $aData['DEL_INDEX']           : $this->Fields['DEL_INDEX']),
                          'DOC_UID'             => (isset($aData['DOC_UID'])             ? $aData['DOC_UID']             : $this->Fields['DOC_UID']),
                          'APP_DOC_TYPE'        => (isset($aData['APP_DOC_TYPE'])        ? $aData['APP_DOC_TYPE']        : (isset($this->Fields['APP_DOC_TYPE']) ? $this->Fields['APP_DOC_TYPE'] : 'INPUT')),
                          'APP_DOC_CREATE_DATE' => (isset($aData['APP_DOC_CREATE_DATE']) ? $aData['APP_DOC_CREATE_DATE'] : $this->Fields['APP_DOC_CREATE_DATE']),
                          );

    if (!isset($aData['APP_DOC_UID']))
    {
    	$aData['APP_DOC_UID'] = '';
    }

    if($aData['APP_DOC_UID'] != '')
    {
    	$sUID                        = $aData['APP_DOC_UID'];
    	$this->Fields['APP_DOC_UID'] = $sUID;
			$aData['CON_ID']             = $sUID;
			$this->is_new                = false;
		}
		else
		{
			$sUID                        = G::generateUniqueID();
			$this->Fields['APP_DOC_UID'] = $sUID;
			$aData['CON_ID']             = $sUID;
			$this->is_new                = true;
		}
  	parent::save();

		/** Start Comment: If Document type is "OUTPUT" save in the table CONTENT */
		if ($aData['APP_DOC_TYPE'] == 'OUTPUT')
    {
		  $this->content->saveContent('APP_DOC_TITLE',$aData);
		}
		/** End Comment */
		/** Start Comment: If Document type is "OUTPUT" save in the table CONTENT */
		if ($aData['APP_DOC_TYPE'] == 'INPUT')
    {
		  $this->content->saveContent('APP_DOC_COMMENT',$aData);
		}
		$this->content->saveContent('APP_DOC_FILENAME',$aData);
		/** End Comment */

		return $sUID;
  }

 /*
	* Delete a Application Document
	* @param string $sUID
	* @return variant
	*/
	function delete($sUID = '')
  {
  	if ($sUID !== '')
		{
  	  $this->Fields['APP_DOC_UID'] = $sUID;
  	  parent::delete();
  	  $this->content->table_keys = array('CON_ID');
  	  $this->content->Fields['CON_ID'] = $sUID;
  	  $this->content->delete();
  	  return ;
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a delete method without send the Application Document UID!',
    	                        'G_Error',
    	                        true);
    }
  }
}
?>