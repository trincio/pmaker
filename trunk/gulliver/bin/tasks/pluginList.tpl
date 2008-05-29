<?php

  $G_MAIN_MENU = '{className}/menu{className}';
  $G_SUB_MENU = '{className}/menu{className}';
  $G_ID_MENU_SELECTED = '{menuId}';
  $G_ID_SUB_MENU_SELECTED = '{menuId}';

  $G_PUBLISH = new Publisher;
  $dbc = new DBConnection;
  $ses = new DBSession($dbc);
  $G_PUBLISH = new Publisher;

  //$G_PUBLISH->AddContent('xmlform', 'xmlform', '{className}/{className}', '', array() , '');
  $G_PUBLISH->AddContent('pagedtable', 'paged-table', '{className}/{className}List', '', array() , '');
  G::RenderPage('publish');
