<?php
/**
 * class.swimlanesElements.php
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
// It works with the table SWIMLANES_ELEMENTS in a WF dataBase
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * SwimlanesElements - SwimlanesElements class
 * @package ProcessMaker
 * @author Aldo Mauricio Veliz Valenzuela
 * @copyright 2007 COLOSA
 */

G::LoadClass( "pmObject" );

class SwimlanesElements extends PmObject
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
			return parent::setTo($oConnection, 'SWIMLANES_ELEMENTS', array('SWI_UID','PRO_UID'));
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
			$this->table_keys	= array('SWI_UID' );

  		parent::load($sUID);
  		$proFields = $this->Fields;

  		/** Start Comment: Charge SWI_TEXT */
  	  $this->content->load(array('CON_CATEGORY' => "SWI_TEXT", 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG ));
			$proFields['SWI_TEXT'] = $this->content->Fields['CON_VALUE'];

			$this->Fields = $proFields;
			/** End Comment*/

			$this->table_keys = array('SWI_UID','PRO_UID');
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
	 * the parameter SWI_TYPE will content the next values
	 * 1 => Line //in a dataBase this option is default
	 * 2 => Title
	 * 3 => Text
   * @param  array $fields    id of User
   * @return string
   * return uid SWI_UID
  **/

  function save ($fields)
  {
		$this->Fields = array(  'PRO_UID'  => (isset($fields['PRO_UID'])  ? $fields['PRO_UID']                : $this->Fields['PRO_UID']),
														'SWI_TYPE' => (isset($fields['SWI_TYPE']) ? $fields['SWI_TYPE']               : ( isset ( $this->Fields['SWI_TYPE']) ? $this->Fields['SWI_TYPE'] : 'LINE' )) ,
														'SWI_X'    => (isset($fields['SWI_X'])    ? strtoupper( $fields['SWI_X'])     : $this->Fields['SWI_X'] ),
														'SWI_Y'    => (isset($fields['SWI_Y'])    ? strtoupper( $fields['SWI_Y'])     : $this->Fields['SWI_Y'] ) );

    //if is a new process we need to generate the guid
    $uid = G::generateUniqueID();
		$this->is_new = true;

    if(isset($fields['SWI_UID'])){
    	$this->Fields['SWI_UID'] = $fields['SWI_UID'];
			$fields['CON_ID'] = $fields['SWI_UID'];
			$this->is_new = false;
		}else{
			$this->Fields['SWI_UID'] = $uid;
			$fields['CON_ID'] = $uid;
		}
		$uid = $this->Fields['SWI_UID'];

  	parent::save();

	  /** Start Comment: Save in the table CONTENT */
	  $this->content->saveContent('SWI_TEXT',$fields);
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
			$this->table_keys	= array('SWI_UID' );
  	  $this->Fields['SWI_UID'] = $sUID;
  	  parent::delete();
  	  $this->table_keys = array('SWI_UID','PRO_UID');
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
    	                        'You tried to call to a delete method without send the Swimlane Element UID!',
    	                        'G_Error',
    	                        true);
    }
  }

  /*
	* Delete a user
	* @param string $sUID
	* @return variant
	*/
  function deleteGuides($sUID = '')
  {
  	if ($sUID !== '')
		{
			$this->table_keys	= array('PRO_UID', 'SWI_TYPE');
  	  $this->Fields['PRO_UID']  = $sUID;
  	  $this->Fields['SWI_TYPE'] = 1;
  	  parent::delete();
  	  $this->table_keys = array('SWI_UID','PRO_UID');
  	  return;
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a deleteGuides method without send the Swimlane Element UID!',
    	                        'G_Error',
    	                        true);
    }
  }
}

?>