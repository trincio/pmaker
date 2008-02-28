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
 * Process - Process class
 * @package ProcessMaker
 * @author Aldo Mauricio Veliz Valenzuela
 * @copyright 2007 COLOSA
 */

G::LoadClass( "pmObject" );

class ProcessOld extends PmObject
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
			 return parent::setTo($oConnection, 'PROCESS', array('PRO_UID'));
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
  	  	$result = parent::load($sUID);

  		  $proFields = $this->Fields;

  		  /** Start Comment: Load PRO_TITLE and PRO_DESCRIPTION from CONTENT TABLE */
  	   $this->content->load( array('CON_CATEGORY' => "PRO_TITLE", 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG ));
			   $proFields['PRO_TITLE'] = $this->content->Fields['CON_VALUE'];

			   $this->content->load(array('CON_CATEGORY' => "PRO_DESCRIPTION", 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG ));
			   $proFields['PRO_DESCRIPTION'] = $this->content->Fields['CON_VALUE'];

			   $this->Fields = $proFields;
			   /** End Comment*/

  	   return $result;
  	 }
  	 else
  	 {
  		  return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a load method without send the Process UID!',
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
	 * the parameter PRO_TIMEUNIT will content the next values
	 *     Minutes
	 *     Hours
	 *     Days	//in a dataBase this option is default
	 *     Months
	 * the parameter PRO_STATUS will content the next values
	 *   - Inactive
	 *   - Active	//in a dataBase this option is default
	 *   - Test  ???
	 * the parameter PRO_TYPE will content the next values
	 *   - Normal	//in a dataBase this option is default
	 * Auto
	 *   - the parameter PRO_ASSIGNMENT will content the next values
	 *   - Inactive
	 *   - Active	//in a dataBase this option is default
	 *
  * @param  array $fields    id of User
  * @return string
  * return uid process
  **/

  function save ($fields)
  {
		  $this->Fields = array(
														'PRO_PARENT'        => (isset($fields['PRO_PARENT'])       ? $fields['PRO_PARENT']          : $this->Fields['PRO_PARENT']),
														'PRO_TIME'          => (isset($fields['PRO_TIME'])         ? strtoupper($fields['PRO_TIME']): $this->Fields['PRO_TIME']),
														'PRO_TIMEUNIT'      => (isset($fields['PRO_TIMEUNIT'])     ? $fields['PRO_TIMEUNIT']        : ( isset ( $this->Fields['PRO_TIMEUNIT']) ? $this->Fields['PRO_TIMEUNIT'] : 'DAY' )) ,
														'PRO_STATUS'        => (isset($fields['PRO_STATUS'])       ? $fields['PRO_STATUS']          : ( isset ( $this->Fields['PRO_STATUS']) ? $this->Fields['PRO_STATUS'] : 'ACTIVE' )) ,
														'PRO_TYPE_DAY'      => (isset($fields['PRO_TYPE_DAY'])     ? $fields['PRO_TYPE_DAY']        : $this->Fields['PRO_TYPE_DAY']),
														'PRO_TYPE'          => (isset($fields['PRO_TYPE'])         ? $fields['PRO_TYPE']            : ( isset ( $this->Fields['PRO_TYPE']) ? $this->Fields['PRO_TYPE'] : 'NORMAL' )) ,
														'PRO_ASSIGNMENT'    => (isset($fields['PRO_ASSIGNMENT'])   ? $fields['PRO_ASSIGNMENT']      : ( isset ( $this->Fields['PRO_ASSIGNMENT']) ? $this->Fields['PRO_ASSIGNMENT'] : 'ACTIVE' )) ,
														'PRO_SHOW_MAP'      => (isset($fields['PRO_SHOW_MAP'])   	 ? $fields['PRO_SHOW_MAP']        : $this->Fields['PRO_SHOW_MAP']),
														'PRO_SHOW_MESSAGE'  => (isset($fields['PRO_SHOW_MESSAGE']) ? $fields['PRO_SHOW_MESSAGE']    : $this->Fields['PRO_SHOW_MESSAGE']),
														'PRO_SHOW_DELEGATE' => (isset($fields['PRO_SHOW_DELEGATE'])? $fields['PRO_SHOW_DELEGATE']   : $this->Fields['PRO_SHOW_DELEGATE']),
														'PRO_SHOW_DYNAFORM' => (isset($fields['PRO_SHOW_DYNAFORM'])? $fields['PRO_SHOW_DYNAFORM']   : $this->Fields['PRO_SHOW_DYNAFORM']),
														'PRO_CATEGORY'      => (isset($fields['PRO_CATEGORY'])     ? $fields['PRO_CATEGORY']        : $this->Fields['PRO_CATEGORY']),
														'PRO_SUB_CATEGORY'  => (isset($fields['PRO_SUB_CATEGORY']) ? $fields['PRO_SUB_CATEGORY']    : $this->Fields['PRO_SUB_CATEGORY']),
														'PRO_INDUSTRY'      => (isset($fields['PRO_INDUSTRY'])     ? $fields['PRO_INDUSTRY']        : $this->Fields['PRO_INDUSTRY']),
														'PRO_UPDATE_DATE'   => (isset($fields['PRO_UPDATE_DATE'])  ? $fields['PRO_UPDATE_DATE']     : $this->Fields['PRO_UPDATE_DATE']),
														'PRO_CREATE_DATE'   => (isset($fields['PRO_CREATE_DATE'])  ? $fields['PRO_CREATE_DATE']     : $this->Fields['PRO_CREATE_DATE']),
														'PRO_CREATE_USER'   => (isset($fields['PRO_CREATE_USER'])  ? $fields['PRO_CREATE_USER']     : $this->Fields['PRO_CREATE_USER']),
														'PRO_HEIGHT'        => (isset($fields['PRO_HEIGHT'])       ? $fields['PRO_HEIGHT']          : $this->Fields['PRO_HEIGHT']),
														'PRO_WIDTH'         => (isset($fields['PRO_WIDTH'])        ? $fields['PRO_WIDTH']           : $this->Fields['PRO_WIDTH']),
														'PRO_TITLE_X'       => (isset($fields['PRO_TITLE_X'])      ? $fields['PRO_TITLE_X']         : $this->Fields['PRO_TITLE_X']),
														'PRO_TITLE_Y'       => (isset($fields['PRO_TITLE_Y'])      ? $fields['PRO_TITLE_Y']         : $this->Fields['PRO_TITLE_Y']));

    //if is a new process we need to generate the guid
    $uid = G::generateUniqueID();
  		$this->is_new = true;

    if(isset($fields['PRO_UID'])){
     	$this->Fields['PRO_UID'] = $fields['PRO_UID'];
			 $fields['CON_ID'] = $fields['PRO_UID'];
			 $this->is_new = false;
		}
		else {
			$this->Fields['PRO_UID'] = $uid;
			$fields['CON_ID'] = $uid;
		}
		$proUid = $this->Fields['PRO_UID'];
  parent::save();

		/** Start Comment: Save in the table CONTENT */
		$this->content->saveContent('PRO_TITLE',$fields);
		$this->content->saveContent('PRO_DESCRIPTION',$fields);
		/** End Comment */

		return $proUid;

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
  	  $this->Fields['PRO_UID'] = $sUID;
  	  parent::delete();
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
    	                        'You tried to call to a delete method without send the Process UID!',
    	                        'G_Error',
    	                        true);
    }
  }
}

?>
