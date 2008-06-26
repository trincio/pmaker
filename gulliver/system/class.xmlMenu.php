<?php
/**
 * class.xmlMenu.php
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
  class xmlMenu extends form {
    var $type = 'xmlmenu';
  }

  class XmlForm_Field_XmlMenu extends XmlForm_Field   {
    var $xmlfile = '';
    var $type = 'xmlmenu';
    var $xmlMenu;
    var $home='';
    var $withoutLabel = true;

    function XmlForm_Field_XmlMenu($xmlNode, $lang='en', $home='', $owner)  {
      parent::XmlForm_Field($xmlNode, $lang, $home, $owner);
      $this->home = $home;
    }

    function render( $value ) {
      $this->xmlMenu = new xmlMenu( $this->xmlfile , $this->home );
      $this->xmlMenu->setValues($value);
      $template = PATH_CORE . 'templates/'  . $this->type . '.html';
    	$out =  $this->xmlMenu->render( $template , $scriptCode ) ;
    	
      $oHeadPublisher =& headPublisher::getSingleton();
      $oHeadPublisher->addScriptFile( $this->xmlMenu->scriptURL );
      $oHeadPublisher->addScriptCode( $scriptCode );
      return $out;
    }
    function renderGrid ($value){
    	return $this->render($value);
    }


  }
