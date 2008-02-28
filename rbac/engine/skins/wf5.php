<?

  G::verifyPath ( '/tmp/smarty/c', true );
  G::verifyPath ( '/tmp/smarty/cache', true );
  // put full path to Smarty.class.php
  require_once(PATH_THIRDPARTY . 'smarty/libs/Smarty.class.php');


$smarty = new Smarty();

$smarty->template_dir = PATH_SKINS;
$smarty->compile_dir = '/tmp/smarty/c'; //'/web/www.domain.com/smarty/templates_c';
$smarty->cache_dir   = '/tmp/smarty/cache'; //web/www.domain.com/smarty/cache';
$smarty->config_dir = PATH_THIRDPARTY . 'smarty/configs';
$smarty->caching      = false;

global $G_HEADER;
if (isset($GLOBALS['G_HEADER'])) $header = $GLOBALS['G_HEADER']->printHeader();
$smarty->assign('header', $header );
$smarty->display('index.html');

?>