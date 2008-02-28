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
class XmlForm_Field_Label extends XmlForm_Field
{
  var $withoutValue=true;
  var $align='left';
}
/* Special class for pagedTable
 * condition: expresion php cuyo resultado define si se "marcara"
 * las siguientes columnas (es caso de true)
 */
class XmlForm_Field_cellMark extends XmlForm_Field
{
  /* Defines the style of the next tds
     of the pagedTable.
   */
  var $showInTable="0";
  var $style="";
  var $styleAlt="";
  var $className="";
  var $classNameAlt="";
  var $condition='false';
  function tdStyle( $values , $owner )
  {
    $value = G::replaceDataField( $this->condition, $owner->values );
    $value = @eval('return ('.$value.');');
    $row=$values['row__'];
    $style=((($row % 2)==0) && ($this->styleAlt!=0)) ? 
      $this->styleAlt : $this->style;
    return ($value)?$style:'';
  }
  function tdClass( $values, $owner )
  {
    $value = G::replaceDataField( $this->condition, $owner->values );
    $value = @eval('return ('.$value.');');
    $row=$values['row__'];
    $style=(($row % 2)==0) ? 
      $this->classNameAlt : $this->className;
    return ($value)?$style:'';
  }
}
class XmlForm_Field_DVEditor extends XmlForm_Field
{
  var $toolbarSet = 'toolbar2lines.html';
	var $width  = '90%';
	var $height = '200' ;
  function render( $value , $owner=NULL ) {
    return '<div style="width:'.htmlentities($this->width,ENT_QUOTES,'utf-8').';height:'.htmlentities($this->height,ENT_QUOTES,'utf-8').'"><input id="form['.$this->name.']" name="form['.$this->name.']" type="hidden" value="'.htmlentities($value,ENT_QUOTES,'UTF-8').'"/></div>';
  }
  function attachEvents($element) {
		$html='var _editor'.$this->name.'=new DVEditor(getField("form['.$this->name.']").parentNode,getField("form['.$this->name.']").value)';
		return $html;
  }
}
/*
 * Special field: Add a search box (fast search) for the related pagedTable
 *    
 * The PAGED_TABLE_ID reserved field must be defined in the xml.
 * Use PAGED_TABLE_FAST_SEARCH reserved field, it contains the saved value for each table.
 *
 * Ex1.
 *   <PAGED_TABLE_ID type="private"/>
 *   <PAGED_TABLE_FAST_SEARCH type="FastSearch">
 *     <en>Search</en>
 *   </PAGED_TABLE_FAST_SEARCH> 
 * Ex2 (Using type="text").
 *   <PAGED_TABLE_ID type="private"/>
 *   <PAGED_TABLE_FAST_SEARCH type="text" colAlign="right"  colWidth="180" onkeypress="if (event.keyCode===13)@#PAGED_TABLE_ID.doFastSearch(this.value);if (event.keyCode===13)return false;">
 *     <en>Search</en>
 *   </PAGED_TABLE_FAST_SEARCH> 
 */
class XmlForm_Field_FastSearch extends XmlForm_Field_Text
{
  var $onkeypress="if (event.keyCode===13)@#PAGED_TABLE_ID.doFastSearch(this.value);if (event.keyCode===13)return false;";
  var $colAlign="right";
  var $colWidth="180";
  var $label="@G::LoadTranslation(ID_SEARCH)"; 
} 
?>
