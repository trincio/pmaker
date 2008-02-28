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

class ContentOld extends DBTable
{

	/*
	* Constructor
	* @param object $oConnection
	* @return variant
	*/
  function SetTo( $oConnection = null)
  {
  	if ($oConnection) {
			return parent::setTo($oConnection,'CONTENT' , array( 'CON_CATEGORY','CON_ID','CON_LANG' ) );
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
 	  return parent::load($sUID);
  }

  /*
	* Insert or update a user data
	* @param string $sUID
	* @return variant
	*/
	function save()
  {
    return parent::save();
  }

/*
	* Save content in a Table CONTENT
	*
	* save a row in content with :
	*       CON_CATEGORY = $sConCategory
	*       CON_PARENT   = default ( 0 ) 
	*       CON_ID       = $fields[ 'CON_ID' ]
	*       CON_VALUE    = $fields[ $sConCategory ]
	*       CON_LANG     = $sysLang 
	*  $fields is the "array of fields values returned by a generic DBTable Load.
	*
	* @param string $sConCategory
	* @param array $Fields
	* @return variant
	*/

 	function saveContent($sConCategory, $fields, $sysLang = SYS_LANG)
 	{
 			$this->table_name = 'CONTENT';
 			$this->table_keys	= array('CON_CATEGORY','CON_ID','CON_LANG' );
 		 if ( isset($fields[$sConCategory]) and (isset($sysLang)) )
   	{
   			$fieldsContent['CON_ID'] = $fields['CON_ID'];
 				 $fieldsContent['CON_LANG'] = $sysLang;
 		  	$fieldsContent['CON_CATEGORY'] = $sConCategory;
 
 		  	/** Start Comment: Verify if the row exists */
 		  	parent::load($fieldsContent);
 		  	$this->is_new = true;
   			if ( isset($this->Fields['CON_ID']) )
   					$this->is_new = false;
 		 		/** End Comment */
 		 		
 			 	if (isset($fields[$sConCategory]))
 				 {
 					  if ( !Content::is_utf8( $fields[$sConCategory ] ) )
 					  {
 						   $fields[$sConCategory] = utf8_encode($fields[$sConCategory]);
 					  }
 					  $fieldsContent['CON_VALUE'] = $fields[$sConCategory];
 				 }
 				 else {
 					  $fieldsContent['CON_VALUE'] = '';
 				 }
 			 	$this->Fields = $fieldsContent;
 				 return parent::save();
 			}
			 else
    {
     	return PEAR::raiseError(null,
     	       G_ERROR_USER_UID,
     	       null,
     	       null,
     	       'You tried to call to a save method without send the Category !',
     	       'G_Error',
     	       true);
    }
	 }

  /*
	* Delete a user
	* @param string $sUID
	* @return variant
	*/
	function delete()
  {
 	  return parent::delete();
  }

  function is_utf8($string)
  {
    if (is_array($string))
    {
      $enc = implode('', $string);
      return @!((ord($enc[0]) != 239) && (ord($enc[1]) != 187) && (ord($enc[2]) != 191));
    }
    else
    {
      return (utf8_encode(utf8_decode($string)) == $string);
    }
  }

}

?>
