<?php
/**
 * class.toolBar.php
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
  class ToolBar extends form {
    var $type = 'toolbar';
    var $align = 'left';
  }

  class XmlForm_Field_ToolBar extends XmlForm_Field   {
    var $xmlfile = '';
    var $type = 'toolbar';
    var $toolBar;
    var $home='';
    var $withoutLabel = true;

    function XmlForm_Field_ToolBar($xmlNode, $lang='en', $home='', $owner)  {
      parent::XmlForm_Field($xmlNode, $lang, $home, $owner);
      $this->home = $home;
    }

    function render( $value ) {
      global $G_HEADER;
      $this->toolBar = new toolBar( $this->xmlfile , $this->home );
      $template = PATH_CORE . 'templates/'  . $this->type . '.html';
    	$out =  $this->toolBar->render( $template , $scriptCode ) ;
      $G_HEADER->addScriptFile( $this->toolBar->scriptURL );
      $G_HEADER->addScriptCode( $scriptCode );
      return $out;
    }

  }
  class XmlForm_Field_toolButton extends XmlForm_Field
  {
    var $file = '';
    var $fileAlt = '';
    var $url = '';
    var $urlAlt = '';
    var $home = 'public_html';
    /* types of buttons:
     *    image
     *    text
     *    image/text
     *    text/image
     */
    var $buttonType = 'image';
    var $withoutLabel = false;
    var $buttonStyle = '';
    /*$hoverMethod : back | switch*/
    var $hoverMethod='back';
    function render( $value )
    {
      $url = $this->file;
      if ($this->home === "methods") $url = G::encryptlink( SYS_URI . $url );
      if ($this->home === "public_html") $url ='/' . $url ;
      $urlAlt = $this->fileAlt;
      if ($this->fileAlt!=='') {
        if ($this->home === "methods") $urlAlt = G::encryptlink( SYS_URI . $urlAlt );
        if ($this->home === "public_html") $urlAlt ='/' . $urlAlt ;
      }
      $this->url=$url;
      $this->urlAlt=$urlAlt;
      switch($this->buttonType){
        case 'image':
          $html='';
          if ($this->hoverMethod==='back') {
            $html='<img src="'.htmlentities( $url, ENT_QUOTES, 'utf-8').'"'.
            (($this->style)?' style="'.$this->style.'"':'').' onmouseover=\'backImage(this,"url('.htmlentities( $urlAlt, ENT_QUOTES, 'utf-8').') no-repeat")\' onmouseout=\'backImage(this,"")\' title=\'' . addslashes($this->label) . '\' />';
          } elseif($this->hoverMethod==='switch'){
            $html='<img src="'.htmlentities( $url, ENT_QUOTES, 'utf-8').'"'.
            (($this->style)?' style="'.$this->style.'"':'').' onmouseover=\'switchImage(this,"'.htmlentities( $url, ENT_QUOTES, 'utf-8').'","'.htmlentities( $urlAlt, ENT_QUOTES, 'utf-8').'")\' onmouseout=\'switchImage(this,"'.htmlentities( $url, ENT_QUOTES, 'utf-8').'","'.htmlentities( $urlAlt, ENT_QUOTES, 'utf-8').'")\'/>';
          } else {
            $html='<img src="'.htmlentities( $url, ENT_QUOTES, 'utf-8').'"'.
            (($this->style)?' style="'.$this->style.'"':'').'/>';
          }
          break;
        case 'text':$html=$this->htmlentities($this->label, ENT_QUOTES,'utf-8');
          break;
        case 'image/text':$html='<img src="'.htmlentities( $url, ENT_QUOTES, 'utf-8').'"'.
          (($this->style)?' style="'.$this->style.'"':'').'/><br/>'.
          $this->htmlentities($this->label, ENT_QUOTES,'utf-8');
          break;
        case 'text/image':$html=$this->htmlentities($this->label, ENT_QUOTES,'utf-8').
          '<br/><img src="'.htmlentities( $url, ENT_QUOTES, 'utf-8').'"'.
          (($this->style)?' style="'.$this->style.'"':'').'/>';
          break;
      }
      return '<A class="toolButton" '.
      (($this->buttonStyle)?' style="'.$this->buttonStyle.'"':'').
      (($this->onclick)?' onclick="'. htmlentities($this->onclick, ENT_QUOTES,'utf-8').'"':'').
      '>'.$html.'</A>';
    }
  }
?>