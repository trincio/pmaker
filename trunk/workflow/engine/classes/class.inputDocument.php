<?php
/**
 * class.inputDocument.php
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
//
// It works with the table INP_DOCUMENT in a WF dataBase
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * ReqDocument - ReqDocument class
 * @package ProcessMaker
 * @author Aldo Mauricio Veliz Valenzuela
 * @copyright 2007 COLOSA
 */

G::LoadClass( "pmObject" );

class InputDocument extends PmObject
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
			return parent::setTo($oConnection, 'INPUT_DOCUMENT', array('INP_DOC_UID','PRO_UID'));
		}
		else
		{
			return;
		}
	}

  /*
	* Load the user information
	* @param string $sUID
	* @return variant
	*/
	function load($sUID = '')
  {
    if ($sUID !== '')
  	{
  		$this->table_keys	= array('INP_DOC_UID' );
  		parent::load($sUID);
  		$proFields = $this->Fields;

  		/** Start Comment: Charge INP_DOC_TITLE and INP_DOC_DESCRIPTION */
  	  $this->content->load(array('CON_CATEGORY' => "INP_DOC_TITLE", 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG ));
			$proFields['INP_DOC_TITLE'] = $this->content->Fields['CON_VALUE'];

			$this->content->load(array('CON_CATEGORY' => "INP_DOC_DESCRIPTION", 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG ));
			$proFields['INP_DOC_DESCRIPTION'] = $this->content->Fields['CON_VALUE'];

			$this->Fields = $proFields;
			/** End Comment*/

			$this->table_keys = array('INP_DOC_UID','PRO_UID');
  	  return ;
  	}
  	else
  	{
  		return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a load method without send the User UID!',
    	                        'G_Error',
    	                        true);
  	}
  }

  /**
	 * Save the Fields in INP_DOCUMENT
   *
   *
   * @author Aldo Mauricio Veliz Valenzuela
   * @access public
	 * the parameter INP_DOC_FORM_NEEDED will content the next values
	 * Real //in a dataBase this option is default
	 * Virtual
	 * VReal
	 * the parameter INP_DOC_ORIGINAL will content the next values
	 * Copy //in a dataBase this option is default
	 * Original
	 * CopyLegal
	 * Final
	 * the parameter INP_DOC_PUBLISHED will content the next values
	 * Private //in a dataBase this option is default
	 * Public
   * @param  array $fields    id of User
   * @return string
   * return uid INP_DOCUMENT
  **/

  function save ($fields)
  {
		$this->Fields = array(  'PRO_UID'             => (isset($fields['PRO_UID'])                    ? $fields['PRO_UID']                         : $this->Fields['PRO_UID']),
														'INP_DOC_FORM_NEEDED' => (isset($fields['INP_DOC_FORM_NEEDED'])        ? strtoupper($fields['INP_DOC_FORM_NEEDED']) : ( isset ( $this->Fields['INP_DOC_FORM_NEEDED']) ? $this->Fields['INP_DOC_FORM_NEEDED'] : 'REAL' )) ,
														'INP_DOC_ORIGINAL'    => (isset($fields['INP_DOC_ORIGINAL'])           ? strtoupper( $fields['INP_DOC_ORIGINAL'])   : ( isset ( $this->Fields['INP_DOC_ORIGINAL'])    ? $this->Fields['INP_DOC_ORIGINAL'] : 'COPY' )) ,
														'INP_DOC_PUBLISHED'   => (isset($fields['INP_DOC_PUBLISHED'])          ? strtoupper( $fields['INP_DOC_PUBLISHED'])  : ( isset ( $this->Fields['INP_DOC_PUBLISHED'])   ? $this->Fields['INP_DOC_PUBLISHED'] : 'PRIVATE' )) );

    //if is a new document we need to generate the guid
    $uid = G::generateUniqueID();
		$this->is_new = true;

    if(isset($fields['INP_DOC_UID'])){
    	$this->Fields['INP_DOC_UID'] = $fields['INP_DOC_UID'];
			$fields['CON_ID'] = $fields['INP_DOC_UID'];
			$this->is_new = false;
		}else{
			$this->Fields['INP_DOC_UID'] = $uid;
			$fields['CON_ID'] = $uid;
		}
		$uid = $this->Fields['INP_DOC_UID'];

  	parent::save();

		/** Start Comment: Save in the table CONTENT */
  	$this->content->saveContent('INP_DOC_TITLE',$fields);
		$this->content->saveContent('INP_DOC_DESCRIPTION',$fields);
		/** End Comment */

		 return $uid;

  }

    /*
	* Delete a user
	* @param string $sUID
	* @return variant
	*/
	function delete($sUID)
  {
  	if (isset($sUID))
		{
			$this->table_keys	= array('INP_DOC_UID' );
  	  $this->Fields['INP_DOC_UID'] = $sUID;
  	  parent::delete();
  	  $this->table_keys = array('INP_DOC_UID','PRO_UID');
  		$this->content->table_keys	= array('CON_ID' );
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
    	                        'You tried to call to a delete method without send the Document UID!',
    	                        'G_Error',
    	                        true);
    }
  }
}

?>
