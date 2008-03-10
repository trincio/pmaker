<?php
/**
 * class.htmlArea.php
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
global $G_HEADER;
//$G_HEADER->addScriptFile('/htmlarea/editor.js');
class XmlForm_Field_HTML extends XmlForm_Field_Textarea
{
 /* //'default','office2003','silver'
  var $skin       = 'default';
  //'Default','Basic'
  var $toolbarSet = 'Default';
	var $width  = '90%';
	var $height = '200' ;
	var $cols   = 40;
	var $rows   = 6;
  function render( $value , $owner=NULL ) {
	  if ($this->mode==='edit') {
	    if ($this->readOnly)
    		$html='<textarea id="form['.$this->name.']" name="form['.$this->name.']" cols="'.$this->cols.'" rows="'.$this->rows.'" style="'.$this->style.'" wrap="'.htmlentities($this->wrap,ENT_QUOTES,'UTF-8').'" class="FormTextArea" readOnly>'.$this->htmlentities( $value ,ENT_COMPAT,'utf-8').'</textarea>';
	    else
    		$html='<textarea id="form['.$this->name.']" name="form['.$this->name.']" cols="'.$this->cols.'" rows="'.$this->rows.'" style="'.$this->style.'" wrap="'.htmlentities($this->wrap,ENT_QUOTES,'UTF-8').'" class="FormTextArea" >'.$this->htmlentities( $value ,ENT_COMPAT,'utf-8').'</textarea>';
		} elseif ($this->mode==='view') {
  		$html='<textarea id="form['.$this->name.']" name="form['.$this->name.']" cols="'.$this->cols.'" rows="'.$this->rows.'" readOnly style="border:0px;backgroud-color:inherit;'.$this->style.'" wrap="'.htmlentities($this->wrap,ENT_QUOTES,'UTF-8').'"  class="FormTextArea" >'.$this->htmlentities( $value ,ENT_COMPAT,'utf-8').'</textarea>';
		} else {
  		$html='<textarea id="form['.$this->name.']" name="form['.$this->name.']" cols="'.$this->cols.'" rows="'.$this->rows.'" style="'.$this->style.'" wrap="'.htmlentities($this->wrap,ENT_QUOTES,'UTF-8').'"  class="FormTextArea" >'.$this->htmlentities( $value ,ENT_COMPAT,'utf-8').'</textarea>';
		}
    return $html;
  }*/
  function attachEvents($element) {
		$html='var _editor_url = "";editor_generate("form['.$this->name.']");';
		return $html;
  }
}
?>