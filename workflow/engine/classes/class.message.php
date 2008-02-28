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
