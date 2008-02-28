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

require_once 'classes/model/om/BaseTranslation.php';


/**
 * Skeleton subclass for representing a row from the 'TRANSLATION' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class Translation extends BaseTranslation {

	/* Load strings from a Database .
	 * @author Fernando Ontiveros <fernando@colosa.com>
	 * @parameter $languageId   (es|en|...).
	 */

  function generateFileTranslation ( $languageId = ''  )
  {
    if ($languageId === '')
      $languageId = defined('SYS_LANG') ? SYS_LANG : 'en';

    $c = new Criteria();
    $c->add(TranslationPeer::TRN_LANG,  $languageId );
    $c->addAscendingOrderByColumn ( 'TRN_CATEGORY' );
    $c->addAscendingOrderByColumn ( 'TRN_ID' );
    $tranlations = TranslationPeer::doSelect($c);

    $cacheFile   = PATH_LANGUAGECONT."translation.".$languageId;
    $cacheFileJS = PATH_CORE . 'js' . PATH_SEP . 'labels' . PATH_SEP . $languageId.".js";

		  foreach ( $tranlations as $key => $row ) {
  	   if ( $row->getTrnCategory() === 'LABEL' )
  	     $translation[ $row->getTrnId() ] = $row->getTrnValue();
  	   if ( $row->getTrnCategory() === 'JAVASCRIPT')
  	     $translationJS[ $row->getTrnId() ] = $row->getTrnValue();
		  }


    $f = fopen( $cacheFile , 'w');
    fwrite( $f , "<?\n" );
    fwrite( $f , '$translation =' . 'unserialize(\'' .
      addcslashes( serialize ( $translation ), '\\\'' ) .
      "');\n");
    fwrite( $f , "?>" );
    fclose( $f );

    $json=new Services_JSON();

   	$f = fopen( $cacheFileJS , 'w');
    fwrite( $f , "var G_STRINGS =". $json->encode( $translationJS ) .
          ";\n");
    fclose( $f );

    $res['cacheFile'] = $cacheFile;
    $res['cacheFileJS'] = $cacheFileJS;
    $res['rows']   = count (  $translation );
    $res['rowsJS'] = count (  $translationJS );
	  	return $res;
	}

	  //returns an array with
	  // codError      0 - no error, < 0 error
	  // rowsAffected  0,1 the number of rows affected
	  // message       message error.
	 function addTranslation ( $category, $id, $languageId, $value  )
  {
     //if exists the row in the database propel will update it, otherwise will insert.
     $tr = TranslationPeer::retrieveByPK( $category, $id, $languageId );
     if ( ! ( is_object ( $tr ) &&  get_class ($tr) == 'Translation' ) ) {
       $tr = new Translation();
     }
     $tr->setTrnCategory( $category );
     $tr->setTrnId( $id );
     $tr->setTrnLang( $languageId);
     $tr->setTrnValue( $value );

     if ($tr->validate() ) {
       // we save it, since we get no validation errors, or do whatever else you like.
       $res = $tr->save();
     }
     else {
       // Something went wrong. We can now get the validationFailures and handle them.
       $msg = '';
       $validationFailuresArray = $tr->getValidationFailures();
       foreach($validationFailuresArray as $objValidationFailure) {
         $msg .= $objValidationFailure->getMessage() . "<br/>";
       }
       return array ( 'codError' => -100, 'rowsAffected' => 0, 'message' => $msg );
     }
    return array ( 'codError' => 0, 'rowsAffected' => $res, 'message' => '');

    //to do: uniform  coderror structures for all classes
  }

} // Translation
