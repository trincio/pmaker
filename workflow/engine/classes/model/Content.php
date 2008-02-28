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

require_once 'classes/model/om/BaseContent.php';


/**
 * Skeleton subclass for representing a row from the 'CONTENT' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class Content extends BaseContent {

  /*
  * Load the content row specified by the parameters: 
  * @param string $sUID
  * @return variant
  */
  function load($ConCategory, $ConParent, $ConId, $ConLang )
  {
    $content = ContentPeer::retrieveByPK( $ConCategory, $ConParent, $ConId, $ConLang );
    
    if ( is_null ( $content ) ) 
      //we dont find any value for this field and language in CONTENT table
      $ConValue = Content::autoLoadSave ( $ConCategory, $ConParent, $ConId, $ConLang );
	  else
      $ConValue = $content->getConValue();
    
    return $ConValue;
  }

  /*
  * Load the content row and the Save automatically the row for the destination language 
  * @param string $ConCategory
  * @param string  $ConParent
  * @param string $ConId 
  * @param string $destConLang
  * @return string
  * if the row doesn't exists, it will be created automatically, even the default 'en' language
  */
  function autoLoadSave($ConCategory, $ConParent, $ConId , $destConLang )
  {
    //search in 'en' language, the default language
    $content = ContentPeer::retrieveByPK( $ConCategory, $ConParent, $ConId, 'en' );

    //to do: review if the $destConLang is a valid language/
    if ( is_null ( $content ) )  
      $ConValue = '';  //we dont find any value for this field and language in CONTENT table
	  else
      $ConValue = $content->getConValue();
    
    try {
      $con = new Content();
      $con->setConCategory( $ConCategory );
      $con->setConParent( $ConParent );
      $con->setConId( $ConId );
      $con->setConLang( $destConLang );
      $con->setConValue( $ConValue );
      if ($con->validate() ) {
        $res = $con->save();
      }
    }
    catch (Exception $e) {
      throw ( $e );
    }

    return $ConValue;
  }

  /*
  * Insert a content row  
  * @param string $ConCategory
  * @param string $ConParent
  * @param string $ConId
  * @param string $ConLang
  * @param string $ConValue 
  * @return variant
  */
  function addContent($ConCategory, $ConParent, $ConId, $ConLang, $ConValue )
  {
    try {
      $con = ContentPeer::retrieveByPK( $ConCategory, $ConParent, $ConId, $ConLang );
  
      if ( is_null ( $con ) ) {
        $con = new Content();
      }
      $con->setConCategory( $ConCategory );
      $con->setConParent( $ConParent );
      $con->setConId( $ConId );
      $con->setConLang( $ConLang );
      $con->setConValue( $ConValue );
      if ($con->validate() ) {
        $res = $con->save();
        return $res;
      }
      else {
        $e = new Exception( "Error in addcontent, the row $ConCategory, $ConParent, $ConId, $ConLang is not Valid"); 
        throw ( $e );
      }
    }
    catch ( Exception $e ) { 
      throw ( $e );
    }
  }


  /*
  * Insert a content row  
  * @param string $ConCategory
  * @param string $ConParent
  * @param string $ConId
  * @param string $ConLang
  * @param string $ConValue 
  * @return variant
  */
  function removeContent($ConCategory, $ConParent, $ConId )
  {
    try {
      $c = new Criteria();
      $c->add( ContentPeer::CON_CATEGORY, $ConCategory );
      $c->add( ContentPeer::CON_PARENT, $ConParent );
      $c->add( ContentPeer::CON_ID, $ConId );
      $result = ContentPeer::doSelectRS( $c );
      $result->next();
      $row = $result->getRow();
      while ( is_array ( $row ) ) {
        ContentPeer::doDelete ( array ($ConCategory, $ConParent, $ConId, $row[3])  );
        $result->next();
        $row = $result->getRow();
      }
    }
    catch ( Exception $e ) { 
      throw ( $e );
    }

  }


} // Content
