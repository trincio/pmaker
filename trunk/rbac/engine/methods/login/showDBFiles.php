<?php

$G_MAIN_MENU     = 'rbac.login';
$G_MENU_SELECTED = '';


$G_PUBLISH = new Publisher;
$G_PUBLISH->AddContent('view', 'login/showDBFiles');

G::RenderPage( "publish" );
?>