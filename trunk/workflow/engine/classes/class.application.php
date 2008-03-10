<?php
/**
 * class.application.php
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
// It works with the table Application in a WF dataBase
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * Application - Application class
 * @package ProcessMaker
 * @author Aldo Mauricio Veliz Valenzuela
 * @copyright 2007 COLOSA
 */

G::LoadClass( "pmObject" );
G::LoadClass( "process" );

class ApplicationOld extends PmObject
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
			return parent::setTo($oConnection, 'APPLICATION', array('APP_UID'));
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
  		parent::load($sUID);
  		$proFields = $this->Fields;
  		/** Start Comment: Load APP_TITLE */
  	  $this->content->load(array('CON_CATEGORY' => "APP_TITLE", 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG ));
  			$proFields['APP_TITLE'] = $this->content->Fields['CON_VALUE'];
  			
  			//unserialize the variables saved in field APP_DATA
		  	$proFields['APP_DATA']  = unserialize($proFields['APP_DATA']);
			  $this->Fields = $proFields;
			  /** End Comment*/

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
	 * Save the Fields in APPLICATION
   * @author Aldo Mauricio Veliz Valenzuela
   *
   * the parameter APP_PARALLEL will content the next values
	 * Y
	 * N
	 *
   * @param  array $fields
   * @return string
   * return uid application
  **/

  function save ($fields)
  {

    //if is a new process we need to generate the guid
    $uid = G::generateUniqueID();
	  	$this->is_new = true;
    if(isset($fields['APP_UID'])){
			$fields['CON_ID']  = $fields['APP_UID'];
			$this->is_new = false;
		}else{
			$oSession = new DBSession($this->_dbc);
	  	$oDataset = $oSession->Execute("SELECT MAX(APP_NUMBER) AS MAX_APP
	  	                                FROM APPLICATION ");
	  	$aRow     = $oDataset->Read();
			$fields['APP_NUMBER'] = $aRow['MAX_APP']+1;
			$fields['APP_UID'] = $uid;
			$fields['CON_ID'] = $uid;
		}
    /** Start Comment : Sets the $this->Fields['APP_DATA']
          Merge between stored APP_DATA with new APP_DATA.
     **/
      if (!isset($this->Fields['APP_DATA']))
      {
      	if (!$this->is_new)
      	{
          $this->load($fields['APP_UID']);
        }
      }
      $this->Fields['APP_DATA']=isset($this->Fields['APP_DATA'])?$this->Fields['APP_DATA']:array();
      if (isset($fields['APP_DATA']) && is_array($fields['APP_DATA']))
      {
        foreach($fields['APP_DATA'] as $k=>$v)
        {
          $this->Fields['APP_DATA'][$k]=$v;
        }
      }
    /** End Comment **/
    /** Begin Comment : Replace APP_DATA in APP_TITLE (before to serialize) **/
  		$pro = new process( $this->_dbc );
  		$pro->load((isset($fields['PRO_UID']) ? $fields['PRO_UID'] : $this->Fields['PRO_UID']));
  		$fields['APP_TITLE'] = G::replaceDataField( $pro->Fields['PRO_TITLE'], $this->Fields['APP_DATA']);
		/** End Comment **/
		$this->Fields = array(
														'APP_UID'		      => $fields['APP_UID'],
														'APP_NUMBER'		  => $fields['APP_NUMBER'],
														'PRO_UID'		      => (isset($fields['PRO_UID'])		      ? $fields['PRO_UID']	           : $this->Fields['PRO_UID']),
														'APP_PARENT'      => (isset($fields['APP_PARENT'])      ? $fields['APP_PARENT']          : $this->Fields['APP_PARENT']),
														'APP_STATUS'      => (isset($fields['APP_STATUS'])      ? $fields['APP_STATUS']          : $this->Fields['APP_STATUS']),
														'APP_PROC_STATUS' => (isset($fields['APP_PROC_STATUS']) ? $fields['APP_PROC_STATUS']     : $this->Fields['APP_PROC_STATUS']),
														'APP_PROC_CODE'   => (isset($fields['APP_PROC_CODE'])   ? $fields['APP_PROC_CODE']       : $this->Fields['APP_PROC_CODE']),
														'APP_PARALLEL'    => (isset($fields['APP_PARALLEL'])    ? $fields['APP_PARALLEL']        : $this->Fields['APP_PARALLEL']),
														'APP_INIT_USER'   => (isset($fields['APP_INIT_USER'])   ? $fields['APP_INIT_USER']       : $this->Fields['APP_INIT_USER']),
														'APP_CUR_USER'    => (isset($fields['APP_CUR_USER'])    ? $fields['APP_CUR_USER']        : $this->Fields['APP_CUR_USER']),
														'APP_CREATE_DATE' => (isset($fields['APP_CREATE_DATE']) ? $fields['APP_CREATE_DATE']     : $this->Fields['APP_CREATE_DATE']),
														'APP_INIT_DATE'   => (isset($fields['APP_INIT_DATE'])   ? $fields['APP_INIT_DATE']       : $this->Fields['APP_INIT_DATE']),
														'APP_FINISH_DATE' => (isset($fields['APP_FINISH_DATE']) ? $fields['APP_FINISH_DATE']     : $this->Fields['APP_FINISH_DATE']),
														'APP_UPDATE_DATE' => (isset($fields['APP_UPDATE_DATE']) ? $fields['APP_UPDATE_DATE']     : ( isset ( $this->Fields['APP_UPDATE_DATE']) ? $this->Fields['APP_UPDATE_DATE'] : G::CurDate())),
														'APP_DATA'        => serialize($this->Fields['APP_DATA'])
													);

  	parent::save();
    $this->Fields['APP_DATA'] = unserialize($this->Fields['APP_DATA']);
		/** Start Comment: Save in the table CONTENT */
  		$this->content->saveContent('APP_TITLE',$fields);
		/** End Comment */

		return $fields['APP_UID'];

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
  	  $this->Fields['APP_UID'] = $sUID;
  	  parent::delete();
  	  /** Star Comment : Delete Contents **/
  	  $this->content->table_keys	= array('CON_ID' );
  	  $this->content->Fields['CON_ID'] = $sUID;
  	  $this->content->delete();
  	  /** End Comment **/
  	  /** Star Comment : Delete AppDelegations **/
    	  $del = new DBTable($this->_dbc,'APP_DELEGATION', array('APP_UID'));
    	  $del->Fields['APP_UID']=$sUID;
    	  $del->delete();
  	  /** End Comment **/
  	  /** Star Comment : Delete AppDocuments **/
    	  $doc = new DBTable($this->_dbc,'APP_DOCUMENT', array('APP_UID'));
    	  $doc->Fields['APP_UID']=$sUID;
    	  $doc->delete();
  	  /** End Comment **/
  	  /** Star Comment : Delete AppMessages **/
    	  $msg = new DBTable($this->_dbc,'APP_MESSAGE', array('APP_UID'));
    	  $msg->Fields['APP_UID']=$sUID;
    	  $msg->delete();
  	  /** End Comment **/
  	  /** Star Comment : Delete AppOwners **/
    	  $own = new DBTable($this->_dbc,'APP_OWNER', array('APP_UID'));
    	  $own->Fields['APP_UID']=$sUID;
    	  $own->delete();
  	  /** End Comment **/
  	  /** Star Comment : Delete AppData **/
    	//$dat = new DBTable($this->_dbc,'APP_DATA', array('APP_UID'));
    	//$dat->Fields['APP_UID']=$sUID;
    	//$dat->delete();
  	  /** End Comment **/
  	  /** Star Comment : Delete App Configuration **/
    	  $cfg = new DBTable($this->_dbc,'CONFIGURATION', array('APP_UID'));
    	  $cfg->Fields['APP_UID']=$sUID;
    	  $cfg->delete();
  	  /** End Comment **/
  	  return ;
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a delete method without send the APP_UID!',
    	                        'G_Error',
    	                        true);
    }
  }
}

?>