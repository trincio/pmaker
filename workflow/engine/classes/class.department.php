<?php
/**
 * class.department.php
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

class Department extends pmObject
{
  var $dependencies = NULL;
  var $contentFields=array('DEP_TITLE');
  function SetTo( $objConnection )  
  {
    parent::SetTo( $objConnection, 'DEPARTMENT', array('DEP_UID') );
  }
  
  function LoadParent() 
  {
    $parent = new DepartmentParent();
    $parent->SetTo( $this->_dbc );
    $parent->Load( $this->Fields['DEP_PARENT'] );
    $Fields = $parent->Fields;
    unset( $parent );
    return $Fields;
  }
  
  function LoadDependencie ( )
  {
    if (!isset($this->dependencies)) {
      $this->dependencies = new DepartmentDependencie( $this->_dbc );
      $this->dependencies->SetTo( $this->_dbc );
      $this->dependencies->Load( $this->Fields['DEP_UID'] );
    } else {
      $this->dependencies->Next();
    }
    $Fields = $this->dependencies->Fields;
    if ( is_array( $Fields ) ) {
      return $this->dependencies;
    } else {
      unset( $this->dependencies );
      return FALSE;
    }
  }

  function Save()
  {
    if (!isset($this->Fields['DEP_UID'])) { 
      $this->Fields['DEP_UID'] = G::generateUniqueID();
      $this->is_new = true;
    }
    else
    {
      $this->is_new = false;
    }
    pmObject::Save();
  }
}

?>