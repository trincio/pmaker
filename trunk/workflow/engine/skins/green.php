<?
/**
 * green.php
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

  G::verifyPath ( PATH_SMARTY_C,     true );
  G::verifyPath ( PATH_SMARTY_CACHE, true );

  // put full path to Smarty.class.php
  require_once(PATH_THIRDPARTY . 'smarty/libs/Smarty.class.php');


  $smarty = new Smarty();

  $smarty->template_dir = PATH_SKINS;
  $smarty->compile_dir  = PATH_SMARTY_C;
  $smarty->cache_dir    = PATH_SMARTY_CACHE;
  $smarty->config_dir   = PATH_THIRDPARTY . 'smarty/configs';

  global $G_HEADER;
  global $G_ENABLE_BLANK_SKIN;

  if ( isset($G_ENABLE_BLANK_SKIN) && $G_ENABLE_BLANK_SKIN ) {
    $smarty->display('blank.html');
  }
  else {
	  
	  $header = '';
	  if (isset($GLOBALS['G_HEADER'])) {
      $GLOBALS['G_HEADER']->title = isset($_SESSION['USR_USERNAME']) ? '(' . $_SESSION['USR_USERNAME'] . ' ' . G::LoadTranslation('ID_IN') . ' ' . SYS_SYS . ')' : '';	  
	  	$header = $GLOBALS['G_HEADER']->printHeader();
	  }
	  $footer = '';
    if (strpos($_SERVER['REQUEST_URI'], '/login/login') !== false) {
      if ( defined('SYS_SYS') ) {
        $footer = "<a href=\"#\" onclick=\"openInfoPanel();return false;\" class=\"FooterLink\">| System Information |</a><br />";
      }
      $footer .= "<br />Copyright © 2003-2008 Colosa, Inc. All rights reserved.";
    }

    //menu
    global $G_MAIN_MENU;
    global $G_SUB_MENU;
    global $G_MENU_SELECTED;
    global $G_SUB_MENU_SELECTED;
    global $G_ID_MENU_SELECTED;
    global $G_ID_SUB_MENU_SELECTED;

 	  $oMenu = new Menu();
 	  $menus = $oMenu->generateArrayForTemplate ( $G_MAIN_MENU,'mnu',$G_MENU_SELECTED, $G_ID_MENU_SELECTED );
	  $smarty->assign('menus', $menus  );

 	  $oSubMenu = new Menu();
 	  $subMenus = $oSubMenu->generateArrayForTemplate ( $G_SUB_MENU,'mnu',$G_SUB_MENU_SELECTED, $G_ID_SUB_MENU_SELECTED );
	  $smarty->assign('subMenus', $subMenus  );

		$G_MENU = new Menu;
		$G_MENU->Load($G_SUB_MENU);
		$G_MENU->optionOn = $G_SUB_MENU_SELECTED;
		$G_MENU->id_optionOn = $G_ID_SUB_MENU_SELECTED;
		$G_MENU->Class = 'subMnu';

	  $smarty->assign('user',   isset($_SESSION['USR_USERNAME']) ? $_SESSION['USR_USERNAME'] : '');
	  $smarty->assign('pipe',   isset($_SESSION['USR_USERNAME']) ? ' | ' : '');	  
	  $smarty->assign('logout', G::LoadTranslation('ID_LOGOUT'));
  	$smarty->assign('header', $header );
  	$smarty->assign('footer', $footer);
	  $smarty->assign('tpl_menu', PATH_TEMPLATE . 'menu.html' );
	  $smarty->assign('tpl_submenu', PATH_TEMPLATE . 'submenu.html' );

    if (class_exists('PMPluginRegistry')) {
      $oPluginRegistry = &PMPluginRegistry::getSingleton();
      $sCompanyLogo = $oPluginRegistry->getCompanyLogo ( '/images/processmaker.logo.jpg' );
    }
    else
      $sCompanyLogo = '/images/processmaker.logo.jpg';

	  $smarty->assign('logo_company', $sCompanyLogo );
    $smarty->display('green.html');
  }
  
