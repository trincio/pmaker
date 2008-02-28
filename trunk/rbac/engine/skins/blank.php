<?

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
if (isset($GLOBALS['G_HEADER'])) $header = $GLOBALS['G_HEADER']->printHeader();
$smarty->assign('header', $header );
//$smarty->assign('tpl_menu', PATH_TEMPLATE . 'menu.html' );
//$smarty->assign('tpl_submenu', PATH_TEMPLATE . 'submenu.html' );
$smarty->display('blank.html');
?>