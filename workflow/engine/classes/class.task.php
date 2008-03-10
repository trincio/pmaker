<?php
/**
 * class.task.php
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
// It works with the table Task in a WF dataBase
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * Task - Task class
 * @package ProcessMaker
 * @author Aldo Mauricio Veliz Valenzuela
 * @copyright 2007 COLOSA
 */

G::LoadClass( "pmObject" );

class TaskOld extends PmObject
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
			return parent::setTo($oConnection, 'TASK', array('TAS_UID'));
		}
		else
		{
			return;
		}
	}
  /*
	* Load the task information
	* @param string $sUID
	* @return variant
	*/
	function load($sUID = '')
  {
    if ($sUID !== '')
  	{

  		parent::load($sUID);

  		$tasFields = $this->Fields;
  		/** Start Comment: load TAS_TITLE, TAS_DESCRIPTION, TAS_DEF_TITLE, TAS_DEF_DESCRIPTION, TAS_DEF_PROC_CODE AND TAS_DEF_MESSAGE  */

  	  $this->content->load(array('CON_CATEGORY' => "TAS_TITLE", 'CON_ID' => $sUID , 'CON_LANG' => SYS_LANG ));
			$tasFields['TAS_TITLE'] = $this->content->Fields['CON_VALUE'];

			$this->content->load(array('CON_CATEGORY' => "TAS_DESCRIPTION", 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG ));
			$tasFields['TAS_DESCRIPTION'] = $this->content->Fields['CON_VALUE'];

			$this->content->load(array('CON_CATEGORY' => "TAS_DEF_TITLE", 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG ));
			$tasFields['TAS_DEF_TITLE'] = $this->content->Fields['CON_VALUE'];

			$this->content->load(array('CON_CATEGORY' => "TAS_DEF_DESCRIPTION", 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG ));
			$tasFields['TAS_DEF_DESCRIPTION'] = $this->content->Fields['CON_VALUE'];

			$this->content->load(array('CON_CATEGORY' => "TAS_DEF_PROC_CODE", 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG ));
			$tasFields['TAS_DEF_PROC_CODE'] = $this->content->Fields['CON_VALUE'];

			$this->content->load(array('CON_CATEGORY' => "TAS_DEF_MESSAGE", 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG ));
			$tasFields['TAS_DEF_MESSAGE'] = $this->content->Fields['CON_VALUE'];

			$this->Fields = $tasFields;
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
	 * Save the Fields in TASK
   *
   *
   * @author Aldo Mauricio Veliz Valenzuela
   * @access public
	 * the parameter TAS_TYPE will content the next values
	 * Normal
	 * Ad hoc
	 * the parameter TAS_TIMEUNIT will content the next values
	 * Minutes
	 * Hours
	 * Day	//in a dataBase this option is default
	 * Months
	 * the parameter TAS_ALERT will content the next values
	 * False //in a dataBase this option is default
	 * True
	 * the parameter TAS_ASSIGN_TYPE will content the next values
	 * Balanced //in a dataBase this option is default
	 * Manual
	 * Evaluate
	 * SelfService
	 * the parameter TAS_ASSIGN_LOCATION will content the next values
	 * False //in a dataBase this option is default
	 * True
	 * the parameter TAS_ASSIGN_LOCATION_ADHOC will content the next values
	 * False //in a dataBase this option is default
	 * True
	 * the parameter TAS_TRANSFER_FLY will content the next values
	 * False //in a dataBase this option is default
	 * True
	 * the parameter TAS_CAN_UPLOAD will content the next values
	 * False //in a dataBase this option is default
	 * True
	 * the parameter TAS_VIEW_UPLOAD will content the next values
	 * False //in a dataBase this option is default
	 * True
	 * the parameter TAS_VIEW_ADDITIONAL_DOCUMENTATION will content the next values
	 * False //in a dataBase this option is default
	 * True
	 * the parameter TAS_CAN_CANCEL will content the next values
	 * False //in a dataBase this option is default
	 * True
	 * the parameter TAS_OWNER1_APP will content the next values
	 * False //in a dataBase this option is default
	 * True
	 * the parameter TAS_OWNER2_APP will content the next values
	 * False //in a dataBase this option is default
	 * True
	 * the parameter TAS_OWNER3_APP will content the next values
	 * False //in a dataBase this option is default
	 * True
	 * the parameter TAS_OWNER4_APP will content the next values
	 * False //in a dataBase this option is default
	 * True
	 * the parameter TAS_OWNER5_APP will content the next values
	 * False //in a dataBase this option is default
	 * True
	 * the parameter TAS_OWNER6_APP will content the next values
	 * False //in a dataBase this option is default
	 * True
	 * the parameter TAS_OWNER7_APP will content the next values
	 * False //in a dataBase this option is default
	 * True
	 * the parameter TAS_OWNER8_APP will content the next values
	 * False //in a dataBase this option is default
	 * True
	 * the parameter TAS_OWNER9_APP will content the next values
	 * False //in a dataBase this option is default
	 * True
	 * the parameter TAS_OWNER10_APP will content the next values
	 * False //in a dataBase this option is default
	 * True
	 * the parameter TAS_CAN_PAUSE will content the next values
	 * False //in a dataBase this option is default
	 * True
	 * the parameter TAS_CAN_SEND_MESSAGE will content the next values
	 * False
	 * True //in a dataBase this option is default
	 * the parameter TAS_CAN_DELETE_DOCS will content the next values
	 * True
	 * False //in a dataBase this option is default
	 * View
	 * the parameter TAS_SELF_SERVICE will content the next values
	 * False //in a dataBase this option is default
	 * True
	 * the parameter TAS_START will content the next values
	 * False //in a dataBase this option is default
	 * True
	 * the parameter TAS_TO_LAST_USER will content the next values
	 * False //in a dataBase this option is default
	 * True
	 * the parameter TAS_SEND_LAST_EMAIL will content the next values
	 * False
	 * True //in a dataBase this option is default
	 * the parameter TAS_DERIVATION will content the next values
	 * Normal //in a dataBase this option is default
	 * Fast
	 * Automatic
	 *
   * @param  array $fields    id of User
   * @return string
   * return uid task
  **/

  function save ($fields)
  {
		$this->Fields = array(
														'PRO_UID'        		                 => (isset($fields['PRO_UID'])                          ? $fields['PRO_UID']                          : $this->Fields['PRO_UID']),
														'TAS_TYPE'        		               => (isset($fields['TAS_TYPE'])                         ? $fields['TAS_TYPE']                         : ( isset ( $this->Fields['TAS_TYPE']) ? $this->Fields['TAS_TYPE'] : 'NORMAL' )) ,
														'TAS_DURATION'                       => (isset($fields['TAS_DURATION'])                     ? $fields['TAS_DURATION']                     : $this->Fields['TAS_DURATION']),
														'TAS_DELAY_TYPE'                     => (isset($fields['TAS_DELAY_TYPE'])                   ? $fields['TAS_DELAY_TYPE']                   : $this->Fields['TAS_DELAY_TYPE']),
														'TAS_TEMPORIZER'                     => (isset($fields['TAS_TEMPORIZER'])                   ? $fields['TAS_TEMPORIZER']                   : $this->Fields['TAS_TEMPORIZER']),
														'TAS_TYPE_DAY'                       => (isset($fields['TAS_TYPE_DAY'])                     ? $fields['TAS_TYPE_DAY']                     : $this->Fields['TAS_TYPE_DAY']),
														'TAS_TIMEUNIT'                       => (isset($fields['TAS_TIMEUNIT'])                     ? $fields['TAS_TIMEUNIT']                     : ( isset ( $this->Fields['TAS_TIMEUNIT']) ? $this->Fields['TAS_TIMEUNIT'] : 'DAY' )) ,
														'TAS_ALERT'                          => (isset($fields['TAS_ALERT'])                        ? $fields['TAS_ALERT']                        : ( isset ( $this->Fields['TAS_ALERT']) ? $this->Fields['TAS_ALERT'] : 'FALSE' )) ,
														'TAS_PRIORITY_VARIABLE'              => (isset($fields['TAS_PRIORITY_VARIABLE'])            ? $fields['TAS_PRIORITY_VARIABLE']            : $this->Fields['TAS_PRIORITY_VARIABLE']),
														'TAS_ASSIGN_TYPE'                    => (isset($fields['TAS_ASSIGN_TYPE'])                  ? $fields['TAS_ASSIGN_TYPE']                  : ( isset ( $this->Fields['TAS_ASSIGN_TYPE']) ? $this->Fields['TAS_ASSIGN_TYPE'] : 'BALANCED' )) ,
														'TAS_ASSIGN_VARIABLE'                => (isset($fields['TAS_ASSIGN_VARIABLE'])              ? $fields['TAS_ASSIGN_VARIABLE']              : ( isset ( $this->Fields['TAS_ASSIGN_VARIABLE']) ? $this->Fields['TAS_ASSIGN_VARIABLE'] : '@@SYS_NEXT_USER_TO_BE_ASSIGNED' )) ,
														'TAS_ASSIGN_LOCATION'                => (isset($fields['TAS_ASSIGN_LOCATION'])              ? $fields['TAS_ASSIGN_LOCATION']              : ( isset ( $this->Fields['TAS_ASSIGN_LOCATION']) ? $this->Fields['TAS_ASSIGN_LOCATION'] : 'FALSE' )) ,
														'TAS_ASSIGN_LOCATION_ADHOC'          => (isset($fields['TAS_ASSIGN_LOCATION_ADHOC'])        ? $fields['TAS_ASSIGN_LOCATION_ADHOC']        : ( isset ( $this->Fields['TAS_ASSIGN_LOCATION_ADHOC']) ? $this->Fields['TAS_ASSIGN_LOCATION_ADHOC'] : 'FALSE' )) ,
														'TAS_TRANSFER_FLY'                   => (isset($fields['TAS_TRANSFER_FLY'])                 ? $fields['TAS_TRANSFER_FLY']                 : ( isset ( $this->Fields['TAS_TRANSFER_FLY']) ? $this->Fields['TAS_TRANSFER_FLY'] : 'FALSE' )) ,
														'TAS_LAST_ASSIGNED'                  => (isset($fields['TAS_LAST_ASSIGNED'])                ? $fields['TAS_LAST_ASSIGNED']                : ( isset ( $this->Fields['TAS_LAST_ASSIGNED']) ? $this->Fields['TAS_LAST_ASSIGNED'] : '0' )) ,
														'TAS_USER'                           => (isset($fields['TAS_USER'])                         ? $fields['TAS_USER']                         : ( isset ( $this->Fields['TAS_USER']) ? $this->Fields['TAS_USER'] : '0' )) ,
														'TAS_CAN_UPLOAD'                     => (isset($fields['TAS_CAN_UPLOAD'])                   ? $fields['TAS_CAN_UPLOAD']                   : ( isset ( $this->Fields['TAS_CAN_UPLOAD']) ? $this->Fields['TAS_CAN_UPLOAD'] : 'FALSE' )) ,
														'TAS_VIEW_UPLOAD'                    => (isset($fields['TAS_VIEW_UPLOAD'])                  ? $fields['TAS_VIEW_UPLOAD']                  : ( isset ( $this->Fields['TAS_VIEW_UPLOAD']) ? $this->Fields['TAS_VIEW_UPLOAD'] : 'FALSE' )) ,
														'TAS_VIEW_ADDITIONAL_DOCUMENTATION'  => (isset($fields['TAS_VIEW_ADDITIONAL_DOCUMENTATION'])? $fields['TAS_VIEW_ADDITIONAL_DOCUMENTATION']: ( isset ( $this->Fields['TAS_VIEW_ADDITIONAL_DOCUMENTATION']) ? $this->Fields['TAS_VIEW_ADDITIONAL_DOCUMENTATION'] : 'FALSE' )) ,
														'TAS_CAN_CANCEL'                     => (isset($fields['TAS_CAN_CANCEL'])                   ? $fields['TAS_CAN_CANCEL']                   : ( isset ( $this->Fields['TAS_CAN_CANCEL']) ? $this->Fields['TAS_CAN_CANCEL'] : 'FALSE' )) ,
														'TAS_OWNER_APP'    		               => (isset($fields['TAS_OWNER_APP'])                    ? $fields['TAS_OWNER_APP']                    : ( isset ( $this->Fields['TAS_OWNER_APP']) ? $this->Fields['TAS_OWNER_APP'] : 'FALSE' )) ,
														'STG_UID'     					             => (isset($fields['STG_UID'])                          ? $fields['STG_UID']                          : $this->Fields['STG_UID']),
														'TAS_CAN_PAUSE'                      => (isset($fields['TAS_CAN_PAUSE'])                    ? $fields['TAS_CAN_PAUSE']                    : ( isset ( $this->Fields['TAS_CAN_PAUSE']) ? $this->Fields['TAS_CAN_PAUSE'] : 'FALSE' )) ,
														'TAS_CAN_SEND_MESSAGE'               => (isset($fields['TAS_CAN_SEND_MESSAGE'])             ? $fields['TAS_CAN_SEND_MESSAGE']             : ( isset ( $this->Fields['TAS_CAN_SEND_MESSAGE']) ? $this->Fields['TAS_CAN_SEND_MESSAGE'] : 'TRUE' )) ,
														'TAS_CAN_DELETE_DOCS'                => (isset($fields['TAS_CAN_DELETE_DOCS'])              ? $fields['TAS_CAN_DELETE_DOCS']              : ( isset ( $this->Fields['TAS_CAN_DELETE_DOCS']) ? $this->Fields['TAS_CAN_DELETE_DOCS'] : 'FALSE' )) ,
														'TAS_SELF_SERVICE'                   => (isset($fields['TAS_SELF_SERVICE'])                 ? $fields['TAS_SELF_SERVICE']                 : ( isset ( $this->Fields['TAS_SELF_SERVICE']) ? $this->Fields['TAS_SELF_SERVICE'] : 'FALSE' )) ,
														'TAS_START'                          => (isset($fields['TAS_START'])                        ? $fields['TAS_START']                        : ( isset ( $this->Fields['TAS_START']) ? $this->Fields['TAS_START'] : 'FALSE' )) ,
														'TAS_TO_LAST_USER'                   => (isset($fields['TAS_TO_LAST_USER'])                 ? $fields['TAS_TO_LAST_USER']                 : ( isset ( $this->Fields['TAS_TO_LAST_USER']) ? $this->Fields['TAS_TO_LAST_USER'] : 'FALSE' )) ,
														'TAS_SEND_LAST_EMAIL'                => (isset($fields['TAS_SEND_LAST_EMAIL'])              ? $fields['TAS_SEND_LAST_EMAIL']              : ( isset ( $this->Fields['TAS_SEND_LAST_EMAIL']) ? $this->Fields['TAS_SEND_LAST_EMAIL'] : 'TRUE' )) ,
														'TAS_DERIVATION'                     => (isset($fields['TAS_DERIVATION'])                   ? $fields['TAS_DERIVATION']                   : ( isset ( $this->Fields['TAS_DERIVATION']) ? $this->Fields['TAS_DERIVATION'] : 'NORMAL' )) ,
														'TAS_POSX'                           => (isset($fields['TAS_POSX'])                         ? $fields['TAS_POSX']                         : $this->Fields['TAS_POSX']),
														'TAS_POSY'                           => (isset($fields['TAS_POSY'])                         ? $fields['TAS_POSY']                         : $this->Fields['TAS_POSY']),
														'TAS_COLOR'                          => (isset($fields['TAS_COLOR'])                        ? $fields['TAS_COLOR']                        : $this->Fields['TAS_COLOR']) );


    //if is a new task we need to generate the guid
    $uid = G::generateUniqueID();
		$this->is_new = true;

    if(isset($fields['TAS_UID'])){
    	$this->Fields['TAS_UID'] = $fields['TAS_UID'];
			$fields['CON_ID'] = $fields['TAS_UID'];
			$this->is_new = false;
		}else{
			$this->Fields['TAS_UID'] = $uid;
			$fields['CON_ID'] = $uid;
		}
		$tasUid = $this->Fields['TAS_UID'];
  	parent::save();
		/** Start Comment: Save in the table CONTENT */
		$this->content->saveContent('TAS_TITLE',$fields);
		$this->content->saveContent('TAS_DESCRIPTION',$fields);
		$this->content->saveContent('TAS_DEF_TITLE',$fields);
		$this->content->saveContent('TAS_DEF_DESCRIPTION',$fields);
		$this->content->saveContent('TAS_DEF_PROC_CODE',$fields);
		$this->content->saveContent('TAS_DEF_MESSAGE',$fields);
		/** End Comment */

		return $tasUid;
  }

  /*
	* Delete a task
	* @param string $sProcessUID
	* @param string $sTaskUID
	* @return variant
	*/
	function delete($sProcessUID = '', $sTaskUID = '')
  {
  	if ($sTaskUID !== '')
		{
  	  $this->Fields['TAS_UID'] = $sTaskUID;
  	  parent::delete();
  	  $this->content->table_keys	= array('CON_ID' );
  	  $this->content->Fields['CON_ID'] = $sTaskUID;
  	  $this->content->delete();
  	  $this->content->table_keys	= array('CON_CATEGORY','CON_ID','CON_LANG');
  	  G::LoadClass('route');
  	  $oRoute   = new Route($this->_dbc);
  	  $oSession = new DBSession($this->_dbc);
  	  $oDataset = $oSession->Execute("SELECT * FROM ROUTE WHERE PRO_UID = '" . $sProcessUID . "' AND TAS_UID = '" . $sTaskUID . "'");
  	  while ($aRow = $oDataset->Read())
  	  {
  	    $oRoute->delete($aRow['ROU_UID']);
  	  }
  	  $oDataset = $oSession->Execute("SELECT * FROM ROUTE WHERE PRO_UID = '" . $sProcessUID . "' AND ROU_NEXT_TASK = '" . $sTaskUID . "'");
  	  while ($aRow = $oDataset->Read())
  	  {
  	    $oRoute->delete($aRow['ROU_UID']);
  	    $oSession->Execute("UPDATE ROUTE SET ROU_CASE = ROU_CASE - 1 WHERE PRO_UID = '" . $sProcessUID . "' AND TAS_UID = '" . $sTaskUID . "' AND ROU_CASE > " . $aRow['ROU_CASE']);
  	  }
  	  G::LoadClass('step');
  	  $oStep = new Step($this->_dbc);
  	  G::LoadClass('stepTrigger');
  	  $oStepTrigger = new StepTrigger($this->_dbc);
  	  $oDataset = $oSession->Execute("SELECT STEP_UID FROM STEP WHERE PRO_UID = '" . $sProcessUID . "' AND TAS_UID = '" . $sTaskUID . "'");
  	  while ($aRow = $oDataset->Read())
  	  {
  	    $oStep->delete($aRow['STEP_UID']);
  	  	$oStepTrigger->table_keys = array('STEP_UID');
  	  	$oStepTrigger->Fields['STEP_UID'] = $aRow['STEP_UID'];
  	  	$oStepTrigger->delete();
  	  }
  	  G::LoadClass('taskUser');
  	  $oTaskUser = new TaskUser($this->_dbc);
  	  $oTaskUser->table_keys = array('TAS_UID');
  	  $oTaskUser->Fields['TAS_UID'] = $sTaskUID;
  	  $oTaskUser->delete();
  	  return ;
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a delete method without send the Process UID and the Task UID!',
    	                        'G_Error',
    	                        true);
    }
  }


}

?>