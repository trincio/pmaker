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
  class popupMenu extends form {
    var $type = 'popupMenu';
    var $theme = 'processmaker';

    function renderPopup( $tableId, $tableFields ) {
      $this->name =$tableId;
      $fields = array_keys( $tableFields);
      foreach( $fields as $f ) {
        switch ( strtolower($tableFields[$f]['Type']))           {
          case 'javascript':
          case 'button':
          case 'private':
          case 'hidden':
          case 'cellmark':
               break;
          default:
            $label = ($tableFields[$f]['Label'] !='' ) ? $tableFields[$f]['Label'] : $f;
            $label = str_replace("\n", ' ', $label);
            $pmXmlNode = new Xml_Node( $f, 
                         'complete', 
                         '', 
                         array ( 'label'  => $label,
                                 'type'   => 'popupOption',
                                 'launch' => $tableId . '.showHideField("' . $f . '")' 
                                ) 
                         );
            $this->fields[$f] = new XmlForm_Field_popupOption( $pmXmlNode );
            $this->values[$f]='';
          }
        }
        $scTemp = '';
        $this->values['PAGED_TABLE_ID'] = $tableId;
        print( parent::render( PATH_CORE . 'templates/popupMenu.html', $scTemp));
  		  $sc = "<script type=\"text/javascript\">\n$scTemp\n loadPopupMenu_$tableId(); \n</script>" ;
        return $sc;
    }
    
  }
  
  class XmlForm_Field_popupOption extends XmlForm_Field   {
    var $launch = '';
    function getEvents(  ) {
      $script = '{name:"' . $this->name . '",text:"' . addcslashes($this->label,'\\"') .
        '", launch:leimnud.closure({Function:function(target){' . $this->launch . '}, args:target})}';
      return $script;
    }
  }
?>
