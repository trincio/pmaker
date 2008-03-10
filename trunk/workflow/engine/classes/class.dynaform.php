<?php
/**
 * class.dynaform.php
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
// It works with the table DYNAFORM in a WF dataBase
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * Dynaform - Dynaform class
 * @package ProcessMaker
 * @author Aldo Mauricio Veliz Valenzuela
 * @copyright 2007 COLOSA
 */

G::LoadClass( "pmObject" );

class Dynaform extends PmObject
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
			return parent::setTo($oConnection, 'DYNAFORM', array('DYN_UID','PRO_UID'));
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
  		$this->table_keys	= array('DYN_UID' );
  		parent::load($sUID);
  		$proFields = $this->Fields;

  		/** Start Comment: Charge DYN_TITLE and DYN_DESCRIPTION */
  	  $this->content->load(array('CON_CATEGORY' => "DYN_TITLE", 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG ));
			$proFields['DYN_TITLE'] = $this->content->Fields['CON_VALUE'];

			$this->content->load(array('CON_CATEGORY' => "DYN_DESCRIPTION", 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG ));
			$proFields['DYN_DESCRIPTION'] = $this->content->Fields['CON_VALUE'];

			$this->Fields = $proFields;
			/** End Comment*/

			$this->table_keys = array('DYN_UID','PRO_UID');
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
	 * Save the Fields in PROCESS
   *
   *
   * @author Aldo Mauricio Veliz Valenzuela
   * @access public
	 * the parameter DYN_TYPE will content the next values
	 * Normal //in a dataBase this option is default
	 * Grid
   * @param  array $fields    id of User
   * @return string
   * return uid Dynaform
  **/

  function save ($fields)
  {
		  $this->Fields = array(  'PRO_UID'          => (isset($fields['PRO_UID'])         ? $fields['PRO_UID']                      : $this->Fields['PRO_UID']),
														  'DYN_TYPE'     => (isset($fields['DYN_TYPE'])    ? $fields['DYN_TYPE']                 : ( isset ( $this->Fields['DYN_TYPE'])    ? $this->Fields['DYN_TYPE'] : 'NORMAL' )) ,
														  'DYN_FILENAME' => (isset($fields['DYN_FILENAME'])? strtoupper( $fields['DYN_FILENAME']): $this->Fields['DYN_FILENAME'] ) );

    //if is a new dynaform we need to generate the guid
    $uid = G::generateUniqueID();
		$this->is_new = true;

    if(isset($fields['DYN_UID'])){
    	$this->Fields['DYN_UID'] = $fields['DYN_UID'];
			$fields['CON_ID'] = $fields['DYN_UID'];
			$this->is_new = false;
		}else{
			$this->Fields['DYN_UID'] = $uid;
			$fields['CON_ID'] = $uid;
		}
		$uid = $this->Fields['DYN_UID'];
		//if is a new dynaform need to define the filename
		if ($this->is_new) {
		  $this->Fields['DYN_FILENAME'] = (isset($fields['DYN_FILENAME'])? strtoupper( $fields['DYN_FILENAME']): $this->Fields['PRO_UID'].'/'.$this->Fields['DYN_UID'] );
		}

  	parent::save();

		/** Start Comment: Save in the table CONTENT */
		$this->content->saveContent('DYN_TITLE',$fields);
		$this->content->saveContent('DYN_DESCRIPTION',$fields);
		/** End Comment */
		 return $uid;

  }

    /*
	* Delete a user
	* @param string $sUID
	* @return variant
	*/
	function delete($sUID = '')
  {
  	if ($sUID !== '')
		{
			$this->table_keys	= array('DYN_UID' );
  	  $this->Fields['DYN_UID'] = $sUID;
  	  parent::delete();
  	  $this->table_keys = array('DYN_UID','PRO_UID');
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
    	                        'You tried to call to a delete method without send the Dynaform UID!',
    	                        'G_Error',
    	                        true);
    }
  }
}

?>
