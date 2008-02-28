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
//XmlForm_Field_DVEditor
class XmlForm_Field_HTML extends XmlForm_Field
{
  var $toolbarSet = 'smallToolBar';
  var $width  = '90%';
  var $height = '200' ;
  var $defaultValue='<br/>';
  function render( $value , $owner=NULL ) {
    global $G_HEADER;
    $value = ($value=='')?'<br/>':$value;
    $G_HEADER->addScriptFile("/js/dveditor/core/dveditor.js",1);
    $G_HEADER->addScriptFile("/js/common/core/webResource.js",1);
    return '<div style="width:'.$this->width.';"><input id="form['.$this->name.']" name="form['.$this->name.']" type="hidden" value="'.htmlentities($value,ENT_QUOTES,'UTF-8').'"/></div>';
  }
  function attachEvents($element) {
		$html='window._editor'.$this->name.'=new DVEditor(getField("'.$this->name.'").parentNode,getField("'.$this->name.'").value,element,"'.$this->height.'");';
		$html.='window._editor'.$this->name.'.loadToolBar("/js/dveditor/core/toolbars/'.$this->toolbarSet.'.html");';
		$html.='window._editor'.$this->name.'.syncHidden("window._editor'.$this->name.'");';
		return $html;
  }
}
?>