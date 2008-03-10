<?php
/**
 * class.message.php
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
// It works with the table MESSAGE in a WF dataBase
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * Message - Message class
 * @package ProcessMaker
 * @author Aldo Mauricio Veliz Valenzuela
 * @copyright 2007 COLOSA
 */

G::LoadClass( "pmObject" );

class Message extends PmObject
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
			return parent::setTo($oConnection, 'MESSAGE', array('MESS_UID','PRO_UID'));
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
  		$this->table_keys	= array('MESS_UID' );
  		parent::load($sUID);
  		$proFields = $this->Fields;

  		/** Start Comment: Charge MESS_TITLE and MESS_DESCRIPTION */
  	  $this->content->load(array('CON_CATEGORY' => "MESS_TITLE", 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG ));
			$proFields['MESS_TITLE'] = $this->content->Fields['CON_VALUE'];

			$this->content->load(array('CON_CATEGORY' => "MESS_DESCRIPTION", 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG ));
			$proFields['MESS_DESCRIPTION'] = $this->content->Fields['CON_VALUE'];

			$this->Fields = $proFields;
			/** End Comment*/

			$this->table_keys = array('MESS_UID','PRO_UID');
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
	 * Save the Fields in MESSUMENT
   *
   *
	 * @author Aldo Mauricio Veliz Valenzuela
   * @access public
	 * the parameter MESS_TYPE will content the next values
	 * HTML //in a dataBase this option is default
	 * TEXT
   * @return string
   * return uid
  **/

  function save ($fields)
  {
		$this->Fields = array(  'PRO_UID'   => (isset($fields['PRO_UID'])   ? $fields['PRO_UID']   : $this->Fields['PRO_UID']),
														'MESS_TYPE' => (isset($fields['MESS_TYPE']) ? $fields['MESS_TYPE'] : ( isset ( $this->Fields['MESS_TYPE']) ? $this->Fields['MESS_TYPE'] : 'HTML' )) );

    //if is a new document we need to generate the guid
    $uid = G::generateUniqueID();
		$this->is_new = true;

    if(isset($fields['MESS_UID'])){
    	$this->Fields['MESS_UID'] = $fields['MESS_UID'];
			$fields['CON_ID'] = $fields['MESS_UID'];
			$this->is_new = false;
		}else{
			$this->Fields['MESS_UID'] = $uid;
			$fields['CON_ID'] = $uid;
		}
		$uid = $this->Fields['MESS_UID'];

  	parent::save();

		/** Start Comment: Save in the table CONTENT */
  	$this->content->saveContent('MESS_TITLE',$fields);
		$this->content->saveContent('MESS_DESCRIPTION',$fields);
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
			$this->table_keys	= array('MESS_UID' );
  	  $this->Fields['MESS_UID'] = $sUID;
  	  parent::delete();
  	  $this->table_keys = array('MESS_UID','PRO_UID');
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
