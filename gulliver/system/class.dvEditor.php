<?php
/**
 * class.dvEditor.php
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
//XmlForm_Field_DVEditor
class XmlForm_Field_HTML extends XmlForm_Field
{
  var $toolbarSet = 'smallToolBar';
  var $width  = '100%';
  var $height = '200' ;
  
  var $defaultValue='<br/>';
  function render( $value , $owner=NULL ) {
    $value = ($value=='')?'<br/>':$value;
    $html = "<div style='width:" . $this->width . ";'>" ;
    $html .= "<input id='form[" . $this->name . "]' name='form[" . $this->name . "]' type='hidden' value=' " . htmlentities($value,ENT_QUOTES,'UTF-8') . "' />";
    $html .= "</div>"; 
    return $html;
  }
  function attachEvents($element) {
    $html='window._editor'.$this->name.'=new DVEditor(getField("'.$this->name.'").parentNode,getField("'.$this->name.'").value,element,"' . $this->height . '");';
    $html.='window._editor'.$this->name.'.loadToolBar("/js/dveditor/core/toolbars/'.$this->toolbarSet.'.html");';
    $html.='window._editor'.$this->name.'.syncHidden("window._editor'.$this->name.'");';
    return $html;
  }
}
