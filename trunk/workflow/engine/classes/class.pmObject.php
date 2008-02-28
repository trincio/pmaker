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
