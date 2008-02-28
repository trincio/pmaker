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
/**
 * @package home.gulliver.system2
*/
 /**
 * DBRecordset class definition
 * Provides access to a generalized table it assumes that the dbconnection object is already initialized for the table should be also provided in order to provide
 * @package home.gulliver.system2
 * @author Fernando Ontiveros Lira <fernando@colosa.com>
 * @copyright (C) 2002 by Colosa Development Team.
 */
class DBRecordSet
{
  var $result = null;

/*
   * Starts connection to Database using default values
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  $intResult Database recordset default value = false
   * @return void
   */
  function DBRecordSet( $intResult = null )
  {
    $this->SetTo( $intResult );
  }

/**
   * Set conecction to Database
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string   $intResult connection string default value = false
   * @return void
   */
  function SetTo( $intResult = null )
  {
  	if ( $intResult === null ) {
      $dberror = PEAR::raiseError(null, DB_ERROR_OBJECT_NOT_DEFINED, null, 'null',
             "You tried to call to a DBRecordset with an invalid result recordset.",
             'G_Error', true);
      DBconnection::logError( $dberror );
    }
    if ( $intResult )     {
      $this->result = $intResult;
    }
  }
  
  /**
   * Function Free
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return string
   */
  function Free()
  {
  	if ( $this->result === null ) {
      $dberror = PEAR::raiseError(null, DB_ERROR_OBJECT_NOT_DEFINED, null, 'null',
             "You tried to call to a DBRecordset with an invalid result recordset.",
             'G_Error', true);
      DBconnection::logError( $dberror );
    }
    $this->result->free();
    return;
  }
  
  /**
   * Function Count
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return string
   */
  function Count()
  {
  	if ( $this->result === null ) {
      $dberror = PEAR::raiseError(null, DB_ERROR_OBJECT_NOT_DEFINED, null, 'null',
             "You tried to call to a DBRecordset with an invalid result recordset.",
             'G_Error', true);
      DBconnection::logError( $dberror );
    }
    return $this->result->numRows();
  }

  /**
   * Function Read
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return string
   */
  function Read()
  {
  	if ( $this->result === null ) {
      $dberror = PEAR::raiseError(null, DB_ERROR_OBJECT_NOT_DEFINED, null, 'null',
             "You tried to call to a DBRecordset with an invalid result recordset.",
             'G_Error', true);
      DBconnection::logError( $dberror );
    }
    $res = $this->result->fetchRow(DB_FETCHMODE_ASSOC);
    //for Pgsql databases, 
    //if ( PEAR_DATABASE == "pgsql" && is_array ( $res ) ) { $res = array_change_key_case( $res, CASE_UPPER);  }

    /* Comment Code: This block is not required now because
     *  of the the use of the G::sqlEscape() instead of addslashes
     *  funcion over each  field in DBTable.
     * @author David Callizaya
     */
    /*if (is_array ($res) )
      foreach ($res as $key => $val)
        $res[$key] = stripslashes ($val);  remove the slashes*/
        
    return $res;
  }
  
  /**
   * Function ReadAbsolute
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return string
   */
  function ReadAbsolute()
  {
    $res = $this->result->fetchRow(DB_FETCHMODE_ORDERED);
    //for Pgsql databases, 
    //if ( PEAR_DATABASE == "pgsql" && is_array ( $res ) ) { $res = array_change_key_case( $res, CASE_UPPER);    }
    return $res;
  }
}

?>