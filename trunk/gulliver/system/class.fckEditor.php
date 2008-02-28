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
/*include(PATH_THIRDPARTY."fckeditor/fckeditor.php") ;
class XmlForm_Field_HTML extends XmlForm_Field
{
  //'default','office2003','silver'
  var $skin       = 'default';
  //'Default','Basic'
  var $toolbarSet = 'Default';
	var $width  = '90%';
	var $height = '200' ;
  function render( $value , $owner=NULL ) {
    $this->FCKeditor = new FCKeditor('form['.$this->name.']') ;
    $this->FCKeditor->BasePath = '/fckeditor/';
  	$this->FCKeditor->Config['AutoDetectLanguage']	= false ;
  	$this->FCKeditor->Config['DefaultLanguage']		= $this->language ;
    $this->FCKeditor->ToolbarSet = $this->toolbarSet;
    $this->FCKeditor->Config['SkinPath'] = $this->FCKeditor->BasePath . 'editor/skins/' . htmlspecialchars($this->skin) . '/' ;
    $this->FCKeditor->Value	 = $value;
    $this->FCKeditor->Width	 = $this->width;
    $this->FCKeditor->Height = $this->height;
    
    return $this->FCKeditor->CreateHtml();
  }
}*/
?>