<?php
/**
 * class.menu.php
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
/**
 * @package home.gulliver.system2
*/
/**
 *
 * Menu class definition
 * Render Menus
 * @package home.gulliver.system2
 * @author Fernando Ontiveros Lira <fernando@colosa.com>
 * @copyright (C) 2002 by Colosa Development Team.
 *
 */
class Menu
{
  var $Id      = NULL;
  var $Options = NULL;
  var $Labels  = NULL;
  var $Icons   = NULL;
  var $JS      = NULL;
  var $Types   = NULL;
  var $Class   = "mnu";
  var $Classes = NULL;
  var $Enabled = NULL;
  var $optionOn = -1;
  var $id_optionOn = "";
  var $WhatIsThis = NULL;

/**
   * Set menu style
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  $strClass name of style class default value 'mnu'
   * @return void
   */
  function SetClass( $strClass = "mnu" )
  {
    $this->Class = "mnu";
  }

/**
   * Load menu options
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  $strMenuName name of menu
   * @return void
   */
  function Load( $strMenuName )
  {
    global $G_TMP_MENU;
    $G_TMP_MENU = NULL;
    $G_TMP_MENU = new Menu;
    $fMenu = G::ExpandPath( "menus" ) . $strMenuName . ".php";
    
    //if the menu file doesn't exists, then try with the plugins folders
    if ( !is_file( $fMenu) )  {
      $aux = explode ( PATH_SEP, $strMenuName );    
      if ( count($aux) == 2 ) { 
        $oPluginRegistry =& PMPluginRegistry::getSingleton();
        if ( $oPluginRegistry->isRegisteredFolder($aux[0]) )
          $fMenu = PATH_PLUGINS . $aux[0] . PATH_SEP . $aux[1] . ".php";
      }
    }
     
    if( !is_file( $fMenu ) ) 
      return;
      
    include( $fMenu );
    //this line will add options to current menu.
    $oPluginRegistry =& PMPluginRegistry::getSingleton();
    $oPluginRegistry->getMenus ( $strMenuName );

    //?
    $c = 0;
    for ($i = 0; $i < count ($G_TMP_MENU->Options) ; $i++)
    if ($G_TMP_MENU->Enabled[$i] == 1) {
      $this->Options[$c] = $G_TMP_MENU->Options[$i];
      $this->Labels [$c] = $G_TMP_MENU->Labels[$i];
      $this->Icons  [$c] = $G_TMP_MENU->Icons[$i];
      $this->JS     [$c] = $G_TMP_MENU->JS[$i];
      $this->Types  [$c] = $G_TMP_MENU->Types[$i];
      $this->Enabled[$c] = $G_TMP_MENU->Enabled[$i];
      $this->Id     [$c] = $G_TMP_MENU->Id[$i];
      $this->Classes[$c] = $G_TMP_MENU->Classes[$i];
      //$this->WhatIsThis[$c] = $G_TMP_MENU->WhatIsThis[$i];
      $c ++;
    }
    else {
      if ($i == $this->optionOn) $this->optionOn = -1;
      elseif ($i <  $this->optionOn) $this->optionOn--;
      elseif ($this->optionOn > 0) $this->optionOn--;//added this line
    }
    $G_TMP_MENU = NULL;
  }

/**
   * Load menu options
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @return int
   */
  function OptionCount()
  {
    $result = 0;
    if( is_array( $this->Options ) )
    {
      $result = count( $this->Options );
    }
    return $result;
  }

/**
   * Add an option to menu
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param string $strLabel label to show
   * @param string $strURL link
   * @param string $strType type, defualt value ='relative'
   * @return void
   */
  function AddOption( $strLabel, $strURL, $strType = "relative" )
  {
    $pos = $this->OptionCount();
    $this->Options[$pos] = $strURL;
    $this->Labels[$pos] = $strLabel;
    $this->Types[$pos] = $strType;
    $this->Enabled[$pos] = 1;
    $this->Id[$pos]      = $pos;
    unset( $pos );
  }

/**
   * Add an option to menu by id
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param string $strId menu id
   * @param string $strLabel label to show
   * @param string $strURL link
   * @param string $strType type, defualt value ='relative'
   * @return void
   */
  function AddIdOption( $strId, $strLabel, $strURL, $strType = "relative" )
  {
    $pos = $this->OptionCount();
    $this->Options[$pos] = $strURL;
    $this->Labels[$pos] = $strLabel;
    $this->Types[$pos] = $strType;
    $this->Enabled[$pos] = 1;
    if (is_array ($strId)) {
      $this->Id[$pos]      = $strId[0];
      $this->Classes[$pos]      = $strId[1];
    }
    else
      $this->Id[$pos]      = $strId;
    unset( $pos );
  }

/**
   * Add an option to menu
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param string $strURL link
   * @param string $strType type, defualt value ='relative'
   * @return void
   */
  function AddRawOption( $strURL = "", $strType = "relative" )
  {
    $pos = $this->OptionCount();
    $this->Options[$pos] = $strURL;
    $this->Labels[$pos] = "";
    $this->Types[$pos] = $strType;
    $this->Enabled[$pos] = 1;
    $this->Id[$pos]      = $pos;
    unset( $pos );
  }

/**
   * Add an option to menu by id
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param string $strId menu id
   * @param string $strLabel label to show
   * @param string $strURL link
   * @param string $strType type, defualt value ='relative'
   * @param string $whatisthis
   * @return void
   */
  function AddIdRawOption( $strId, $strURL = "", $label = "", $icon = "",$js = "")
  {
    $pos = $this->OptionCount();
    $this->Options[$pos] = $strURL;
    $this->Labels[$pos] = $label;
    $this->Icons[$pos] = $icon;
    $this->JS[$pos] = $js;
    $this->Types[$pos] = $strType;
    $this->Enabled[$pos] = 1;
    $this->WhatIsThis[$pos] = $whatisthis;
    if (is_array ($strId)) {
      $this->Id[$pos]      = $strId[0];
      $this->Classes[$pos]      = $strId[1];
    }
    else
      $this->Id[$pos]      = $strId;
    unset( $pos );
  }

/**
   * Disable an menu option by menu's position
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param string $intPos menu option's position
   * @return void
   */
  function DisableOptionPos( $intPos )
  {
    $this->Enabled[$intPos] = 0;
  }

/**
   * Disable an menu's option by id
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param string $id menu's id
   * @return void
   */
  function DisableOptionId ( $id )
  {
    $this->Enabled[ array_search ($id, $this->Id) ] = 0;
  }

/**
   * Render an menu's option
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param string $intPos menu option's position
   * @return void
   */
  function RenderOption( $intPos )
  {
    if ( $this->Enabled[$intPos] != 1)
      return;

    $classname = $this->Class . "Link";
    if ( $this->Classes[$intPos] != "" ) $classname = $this->Classes[$intPos];
    $target = $this->Options[$intPos];
    if( $this->Types[$intPos] != "absolute" )
    {
    	if (defined('ENABLE_ENCRYPT'))  {

        $target = "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/" . $target;
    	}
      else
        if (defined('SYS_SYS'))
          $target = "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/" . $target;
        else
          $target = "/sys/" . SYS_LANG . "/" . SYS_SKIN . "/" . $target;
    }
    $label = $this->Labels[$intPos];
    $whatisthis = $this->WhatIsThis[$intPos];
    $result = "<a href=\"$target\"";
    $result .= " class=\"$classname\">";
    $result .= htmlentities( $label , ENT_NOQUOTES , 'utf-8');
    $result .= "</a>";
    $result .="$whatisthis";
    print( $result );

  }
}

?>