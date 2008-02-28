<?php

  //G::genericForceLogin( 'WF_MYINFO' , 'login/noViewPage', $urlLogin = 'login/login' );

  $G_MAIN_MENU            = 'processmaker';
  $G_SUB_MENU             = 'processes';
  $G_ID_MENU_SELECTED     = 'PROCESSES';
  $G_ID_SUB_MENU_SELECTED = '{$pack->name|upper}';

  $dbc = new DBConnection();
  $ses = new DBSession($dbc);

  //Hardcode: UID of the library by default
  $PRO_UID='746B734DC23311';
  $G_PUBLISH = new Publisher;
  $Fields=array( 'SYS_LANG' => SYS_LANG,
                 'PRO_UID'  => $PRO_UID );
  
  $G_PUBLISH->AddContent('pagedtable', 'paged-table', '{$pack->name}/{$pack->name}_List', '', $Fields , '{$pack->name}_Save');
  
  G::RenderPage( "publish" );

?>