<?php
/**
 * class.translation.php
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

/**
 * Translation - Translation class
 * @package ProcessMaker
 * @author Aldo Mauricio Veliz Valenzuela
 * @copyright 2007 COLOSA
 */
require_once(PATH_THIRDPARTY . 'pear/json/class.json.php');

class TranslationOld extends DBTable
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
			return parent::setTo($oConnection,'CONTENT' , array('CON_CATEGORY','CON_ID','CON_LANG') );
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
	* @param string $sConCategory
	* @param array $Fields
	* @return variant
	*/

	function saveContent($sConCategory,$fields,$sysLang = SYS_LANG)
	{
			$this->table_name = 'CONTENT';
			$this->table_keys	= array('CON_CATEGORY','CON_ID','CON_LANG' );
		  if (isset($fields[$sConCategory])and(isset($sysLang)))
  		{
  			$fieldsContent['CON_ID'] = $fields['CON_ID'];
				$fieldsContent['CON_LANG'] = $sysLang;
		  	$fieldsContent['CON_CATEGORY'] = $sConCategory;
		  	/** Start Comment: Verify if the row exists */
		  	parent::load($fieldsContent);
		  	$this->is_new = true;
  			if(isset($this->Fields['CON_ID']))
					$this->is_new = false;
				/** End Comment */
				$fieldsContent['CON_VALUE'] = (isset($fields[$sConCategory])?$fields[$sConCategory]:'') ;
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


	/* Load strings from a Database .
	 * @author Aldo Mauricio Veliz Valenzuela <mauricio@colosa.com>
	 * @parameter $languageFile a xml language file.
	 * @parameter $languageId   (es|en|...).
	 * @parameter $forceCharge   Force to generate the file from a Database.
	 */
  function generateFileTranslation ( $languageId = ''  )
  {
    if ($languageId === '') 
      $languageId = defined('SYS_LANG') ? SYS_LANG : 'en';
		
    $oSession = new DBSession($this->_dbc);
  	 $oDataset = $oSession->Execute("SELECT * FROM TRANSLATION WHERE TRN_LANG = '" . $languageId . "'");
  	 while ($aRow = $oDataset->Read())
  	 {
  	 print_r ($aRow);
  	   if ($aRow['TRN_CATEGORY']==='LABEL')
  		    $translation[$aRow['TRN_ID']] = $aRow['TRN_VALUE'];
  	   if ($aRow['TRN_CATEGORY']==='JAVASCRIPT')
  		    $translationJS[$aRow['TRN_ID']] = $aRow['TRN_VALUE'];
  	}

  	$cacheFile = PATH_LANGUAGECONT."translation.".$languageId;
    $cacheFileJS = PATH_LANGUAGECONT.$languageId.".js";
    
  	$f = fopen( $cacheFile , 'w');
    fwrite( $f , "<?\n" );
    fwrite( $f , '$translation =' . 'unserialize(\'' .
          addcslashes( serialize ( $translation ), '\\\'' ) .
          "');\n");
    fwrite( $f , "?>" );
    fclose( $f );

    $json=new Services_JSON();

  	$f = fopen( $cacheFileJS , 'w');
    fwrite( $f , "translationJS =". $json->encode( $translationJS ) .
          ";\n");
    fclose( $f );
    
		return $translation;
	}

}

?>
