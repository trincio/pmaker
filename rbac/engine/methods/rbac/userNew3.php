<?php
$G_MAIN_MENU     = 'rbac';
$G_BACK_PAGE     = 'rbac/userList';
$G_SUB_MENU      = 'cancel';
$G_MENU_SELECTED = 0;

$G_PUBLISH = new Publisher;
$G_PUBLISH->AddContent('xmlform', 'xmlform', 'rbac/userNewPwd', '', '', 'userNew4');
G::RenderPage( 'publish');
?>