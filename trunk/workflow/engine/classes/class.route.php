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
// It works with the table ROUTE in a WF dataBase
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * Route - Route class
 * @package ProcessMaker
 * @author Aldo Mauricio Veliz Valenzuela
 * @copyright 2007 COLOSA
 */

G::LoadClass( "content" );

class Route extends DBTable
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
			return parent::setTo($oConnection, 'ROUTE', array('ROU_UID'));
		}
		else
		{
			return;
		}
	}

  /*
	* Load the route information
	* @param string $sUID
	* @return variant
	*/
	function load($sUID = '')
  {
    if ($sUID !== '')
  	{   parent::load($sUID);
  	  return ;
  	}
  	else
  	{
  		return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a load method without send the Route UID!',
    	                        'G_Error',
    	                        true);
  	}
  }

  /*
	* Load the route information by many parameters
	* @param string $sProcessUID
	* @param string $sTask1UID
	* @param string $sTask2UID
	* @return variant
	*/
	function loadByValues($sProcessUID = '', $sTask1UID = '', $sTask2UID = '')
  {
    if (($sProcessUID !== '') && ($sTask1UID !== '') && ($sTask2UID !== ''))
  	{
  		$this->array_keys = array('PRO_UID', 'TAS_UID', 'ROU_NEXT_TASK');
  		$this->Fields['PRO_UID']       = $sProcessUID;
  		$this->Fields['TAS_UID']       = $sTask1UID;
  		$this->Fields['ROU_NEXT_TASK'] = $sTask2UID;
  		return parent::load();
  	}
  	else
  	{
  		return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a load method without send the Process UID or Task 1 UID or Task 2 UID!',
    	                        'G_Error',
    	                        true);
  	}
  }

  /**
	 * Save the Fields in ROUTE
   *
   *
   * @author Aldo Mauricio Veliz Valenzuela
   * @access public
	 * the parameter ROU_TYPE will content the next values
	 * EVALUATE //in a dataBase this option is default
	 * SELECT
	 * PARALLEL
	 * the parameter ROU_TO_LAST_USER will content the next values
	 * FALSE //in a dataBase this option is default
	 * TRUE
	 * the parameter ROU_OPTIONAL will content the next values
	 * FALSE //in a dataBase this option is default
	 * TRUE
	 * the parameter ROU_SEND_EMAIL will content the next values
	 * FALSE
	 * TRUE	 //in a dataBase this option is default
	 * @param  array $fields    id of User
   * @return string
   * return uid SWI_UID
  **/

  function save ($fields)
  {
    if(is_array($fields)){
 			$this->Fields = array(	'ROU_PARENT'             => (isset($fields['ROU_PARENT'])           ? $fields['ROU_PARENT']                    : ( isset ( $this->Fields['ROU_PARENT']) ? $this->Fields['ROU_PARENT'] : '0' )),
 															'PRO_UID'                => (isset($fields['PRO_UID'])              ? $fields['PRO_UID']                       : $this->Fields['PRO_UID']),
    													'TAS_UID'                => (isset($fields['TAS_UID'])              ? $fields['TAS_UID']                       : $this->Fields['TAS_UID']),
		   												'ROU_NEXT_TASK'          => (isset($fields['ROU_NEXT_TASK'])        ? $fields['ROU_NEXT_TASK']                 : $this->Fields['ROU_NEXT_TASK']),
		   												'ROU_CASE'               => (isset($fields['ROU_CASE'])             ? $fields['ROU_CASE']                      : ( isset ( $this->Fields['ROU_CASE']) ? $this->Fields['ROU_CASE'] : '0' )),
		   												'ROU_TYPE'               => (isset($fields['ROU_TYPE'])             ? $fields['ROU_TYPE']                      : ( isset ( $this->Fields['ROU_TYPE']) ? $this->Fields['ROU_TYPE'] : 'EVALUATE' )) ,
		   												'ROU_CONDITION'          => (isset($fields['ROU_CONDITION'])        ? $fields['ROU_CONDITION']                 : ( isset ( $this->Fields['ROU_CONDITION']) ? $this->Fields['ROU_CONDITION'] : '' )),
		   												'ROU_TO_LAST_USER'       => (isset($fields['ROU_TO_LAST_USER'])     ? $fields['ROU_TO_LAST_USER']              : ( isset ( $this->Fields['ROU_TO_LAST_USER']) ? $this->Fields['ROU_TO_LAST_USER'] : 'FALSE' )) ,
		   												'ROU_OPTIONAL'           => (isset($fields['ROU_OPTIONAL'])         ? $fields['ROU_OPTIONAL']                  : ( isset ( $this->Fields['ROU_OPTIONAL']) ? $this->Fields['ROU_OPTIONAL'] : 'FALSE' )) ,
		   												'ROU_SEND_EMAIL'         => (isset($fields['ROU_SEND_EMAIL'])       ? $fields['ROU_SEND_EMAIL']                : ( isset ( $this->Fields['ROU_SEND_EMAIL']) ? $this->Fields['ROU_SEND_EMAIL'] : 'TRUE' )) ,
				  										'ROU_SOURCEANCHOR'       => (isset($fields['ROU_SOURCEANCHOR'])     ? $fields['ROU_SOURCEANCHOR']              : ( isset ( $this->Fields['ROU_SOURCEANCHOR']) ? $this->Fields['ROU_SOURCEANCHOR'] : '0' )),
					  									'ROU_TARGETANCHOR'       => (isset($fields['ROU_TARGETANCHOR'])     ? strtoupper( $fields['ROU_TARGETANCHOR']) : ( isset ( $this->Fields['ROU_TARGETANCHOR']) ? $this->Fields['ROU_TARGETANCHOR'] : '0' ) ) );

	    $uid = G::generateUniqueID();
			$this->is_new = true;

	    if(isset($fields['ROU_UID'])){
	    	$this->Fields['ROU_UID'] = $fields['ROU_UID'];
				$this->is_new = false;
			}else
				$this->Fields['ROU_UID'] = $uid;

			parent::save();

			return $this->Fields['ROU_UID'];

		}
		else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call the save method without send the route uid!',
    	                        'G_Error',
    	                        true);
    }
  }

  /*
	* Delete a route
	* @param string $sUID
	* @return variant
	*/
	function delete($sUID = '')
  {
  	if ($sUID !== '')
		{
			$this->Fields['ROU_UID'] = $sUID;
			parent::delete();
  	  return;
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a delete method without send the Route UID!',
    	                        'G_Error',
    	                        true);
    }
  }

  /*
	* Delete all routes from a task
	* @param string $sProcessUID
	* @param string $sTaskUID
	* @return variant
	*/
	function deleteAllRoutesOfTask($sProcessUID = '', $sTaskUID = '')
  {
  	if (($sProcessUID !== '') && ($sTaskUID !== ''))
		{
			$this->table_keys        = array('PRO_UID', 'TAS_UID');
			$this->Fields['PRO_UID'] = $sProcessUID;
			$this->Fields['TAS_UID'] = $sTaskUID;
			parent::delete();
			$this->array_keys = array('ROU_UID');
  	  return;
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a deleteAllRoutesOfTask method without send the Task UID!',
    	                        'G_Error',
    	                        true);
    }
  }

}

?>
