<?php
/**
 * class.pmObject.php
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
// It works with the table Content in a WF dataBase
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * User - User class
 * @package ProcessMaker
 * @author Aldo Mauricio Veliz Valenzuela
 * @copyright 2007 COLOSA
 */

//byonti: remove it... in reviewing..
//disabling content class in pmObject, many thinks will go wrong!!!
require_once ( "model/Content.php" );

class PmObject extends DBTable
{

  var $contentFields=array();
  var $content;
  var $contentKey='UID';
	/*
	* Constructor
	* @param object $oConnection
	* @return variant
	*/
  function SetTo( $oConnection = null,$strTable = "", $arrKeys = array( 'UID' ), $contentKey = NULL )
  {
  	if ($oConnection)
		{
		  $contentKey = isset($contentKey)?$contentKey:$arrKeys;
		  $this->contentKey = is_array($contentKey)?reset($contentKey):$contentKey; 
			return DBTable::setTo($oConnection,$strTable, $arrKeys );
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
	function load($sUID)
  {
 	  $fields = DBTable::load($sUID);
 	  if (isset($fields))
 	  {
 	    //krumo::backtrace();
/* in review 	    
   	  foreach($this->contentFields as $fieldName)
   	  {
        $this->content->load(array('CON_CATEGORY' => $fieldName, 'CON_ID' => $fields[$this->contentKey], 'CON_LANG' => SYS_LANG ));
        $fields[$fieldName] = $this->content->Fields['CON_VALUE'];
  		}
  */
   	  $this->Fields = $fields;
 	  }
  	return $fields;
  }

  /*
	* Insert or update a user data
	* @param string $sUID
	* @return variant
	*/
	function save()
  {
    $backFields=$this->Fields;
/* in review    
 	  foreach($this->contentFields as $fieldName)
 	  {
      $fields['CON_ID']=$this->Fields[$this->contentKey];
      $fields[$fieldName]=$this->Fields[$fieldName];
      unset($this->Fields[$fieldName]);
      $this->content->saveContent($fieldName,$fields);
    }
  */
    $result=DBTable::save();
    $this->Fields=$backFields;
    return $result;
  }



  /*
	* Delete a user
	* @param string $sUID
	* @return variant
	*/
	function delete()
  {
 	  return DBTable::delete();
  }

  function next()
  {
  	DBTable::next();
  	$fields=$this->Fields;
 	  if (isset($fields))
 	  {
/* in review
   	  foreach($this->contentFields as $fieldName)
   	  {
        $this->content->load(array('CON_CATEGORY' => $fieldName, 'CON_ID' => $fields[$this->contentKey], 'CON_LANG' => SYS_LANG ));
        $fields[$fieldName] = $this->content->Fields['CON_VALUE'];
  		}
  	*/
   	  $this->Fields = $fields;
 	  }
  }
}

?>
