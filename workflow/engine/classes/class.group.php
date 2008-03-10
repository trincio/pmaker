<?php
/**
 * class.group.php
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
require_once 'classes/model/Groupwf.php';

class Group extends DBTable
{
  var $groupUsers = NULL;
  
  //function SetTo( $objConnection )  
  //{
//    DBTable::SetTo( $objConnection, 'GROUPWF', array('UID') );
//  }
  
  function LoadGroupUsers ( )
  {
    if (!isset($this->groupUsers)) {
      $this->groupUsers = new groupUser( $this->_dbc );
      $this->groupUsers->Load( $this->Fields['UID'] );
    } 
    else {
      $this->groupUsers->Next();
    }
    
    $Fields = $this->groupUsers->Fields;
    if ( is_array( $Fields ) ) {
      return $this->groupUsers;
    } else {
      unset( $this->groupUsers );
      return FALSE;
    }
  }
  
  function LoadUser() 
  {
    $group= $this->LoadGroupUsers();
    return $group->LoadUser();
  }
  

}

?>