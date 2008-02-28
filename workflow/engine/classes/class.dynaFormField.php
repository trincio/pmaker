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

G::LoadClass('xmlDb');

class DynaFormField extends DBTable
{
  function SetTo( $objConnection )  
  {
    DBTable::SetTo( $objConnection, 'dynaForm', array('XMLNODE_NAME') );
  }
  function Load( $sUID ){
    parent::Load( $sUID );
    if (is_array($this->Fields)) {
      foreach( $this->Fields as $name => $value ){
        if (strcasecmp($name,'dependentfields')==0) {
          $this->Fields[$name]=explode(',', $value );
        }
      }
    }
  }
  function Delete ( $uid )
  {
    $this->Fields['XMLNODE_NAME'] = $uid;
    parent::Delete();
  }  
  function Save ( $Fields , $labels=array() , $options=array() )
  {
    if ($Fields['TYPE'] === 'javascript'){
      $Fields['XMLNODE_VALUE'] = $Fields['CODE'];
      unset($Fields['CODE']);
      $labels = array();
    }
    $res = $this->_dbses->Execute('SELECT * FROM dynaForm WHERE XMLNODE_NAME="'.$Fields['XMLNODE_NAME'].'"');
    $this->is_new = ($res->count()==0);
    $this->Fields = $Fields;
    parent::Save();
    if ($this->is_new) {
      foreach( $labels as $lang => $value ) {
        $res = $this->_dbses->Execute('INSERT INTO dynaForm'.
          ' (XMLNODE_TYPE,XMLNODE_VALUE)'.
          ' VALUES ("cdata", "'."\n".'")');
        $res = $this->_dbses->Execute('INSERT INTO dynaForm.'
          .$Fields['XMLNODE_NAME'].' (XMLNODE_NAME,XMLNODE_VALUE,XMLNODE_TYPE) '
          .'VALUES ("","'."\n  ".'","cdata")');
        $res = $this->_dbses->Execute('INSERT INTO dynaForm.'
          .$Fields['XMLNODE_NAME'].' (XMLNODE_NAME,XMLNODE_VALUE) '
          .'VALUES ("'.$lang.'","'.str_replace('"','""',$value)."\n  ".'")');
        if (isset($options[$lang])) {
          foreach($options[$lang] as $option => $text ) {
            $res = $this->_dbses->Execute('INSERT INTO dynaForm.'
              .$Fields['XMLNODE_NAME'].'.'.$lang.' (XMLNODE_NAME,XMLNODE_VALUE,XMLNODE_TYPE) '
              .'VALUES ("","'."  ".'","cdata")');
            $res = $this->_dbses->Execute('INSERT INTO dynaForm.'
              .$Fields['XMLNODE_NAME'].'.'.$lang.' (XMLNODE_NAME,XMLNODE_VALUE,name) '
              .'VALUES ("option","'.str_replace('"','""',$text).'","'.str_replace('"','""',$option).'")');
            $res = $this->_dbses->Execute('INSERT INTO dynaForm.'
              .$Fields['XMLNODE_NAME'].'.'.$lang.' (XMLNODE_NAME,XMLNODE_VALUE,XMLNODE_TYPE) '
              .'VALUES ("","'."\n  ".'","cdata")');
          }
        }
        $res = $this->_dbses->Execute('INSERT INTO dynaForm.'
          .$Fields['XMLNODE_NAME'].' (XMLNODE_NAME,XMLNODE_VALUE,XMLNODE_TYPE) '
          .'VALUES ("","'."\n".'","cdata")');
      }
    } else {
      foreach( $labels as $lang => $value ) {
        $res = $this->_dbses->Execute('SELECT * FROM dynaForm.'
          .$Fields['XMLNODE_NAME'].' WHERE XMLNODE_NAME ="'.$lang.'"');
        if ($res->count()>0) {
          $res = $this->_dbses->Execute('UPDATE dynaForm.'
            .$Fields['XMLNODE_NAME'].' SET XMLNODE_VALUE = '
            .'"'.str_replace('"','""',$value).'" WHERE XMLNODE_NAME ="'.$lang.'"');
        } else {
          $res = $this->_dbses->Execute('INSERT INTO dynaForm.'
            .$Fields['XMLNODE_NAME'].' (XMLNODE_NAME,XMLNODE_VALUE) '
            .'VALUES ("'.$lang.'","'.str_replace('"','""',$value).'")');
        }
        if (isset($options[$lang])) {
          $res = $this->_dbses->Execute('DELETE FROM dynaForm.'
            .$Fields['XMLNODE_NAME'].'.'.$lang.' WHERE 1');
          foreach($options[$lang] as $option => $text ) {
            $res = $this->_dbses->Execute('INSERT INTO dynaForm.'
              .$Fields['XMLNODE_NAME'].'.'.$lang.' (XMLNODE_NAME,XMLNODE_VALUE,name) '
              .'VALUES ("option","'.str_replace('"','""',$text).'","'.str_replace('"','""',$option).'")');
          }
        }
      }
    }
  }
  function isNew()
  {
    $res = $this->_dbses->Execute('SELECT * FROM dynaForm WHERE XMLNODE_NAME="'.$this->Fields['XMLNODE_NAME'].'"');
    return ($res->count()==0);
  }
}

?>