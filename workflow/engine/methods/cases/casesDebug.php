<?php

  /* Includes */
  G::LoadClass('case');


  $sUIDUser = $_SESSION['USER_LOGGED'];

  /* Menues */
  $G_MAIN_MENU            = 'processmaker';
  $G_SUB_MENU             = 'cases';
  $G_ID_MENU_SELECTED     = 'CASES';

  /* Prepare page before to show */
  $oCases = new Cases();
  
    //check groups      
    G::LoadClass ( 'groups');
    $group = new Groups();
    $aGroups = $group->getActiveGroupsForAnUser ( $sUIDUser );
    krumo ( $aGroups );
    
    $c = new Criteria ();
    //$c->clearSelectColumns();
    //$c->addSelectColumn( 'COUNT(*)' );

    $c->addJoin ( TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN );
    $c->add ( TaskPeer::TAS_START, 'TRUE' );
    $c->add ( TaskUserPeer::USR_UID, $aGroups , Criteria::IN);

    $rs = TaskUserPeer::doSelectRS( $c );
    $rs->setFetchmode (ResultSet::FETCHMODE_ASSOC);
    $rs->next();
    $row = $rs->getRow();
    while ( is_array ($row ) ) {
      krumo ( $row );
      $rs->next();
      $row = $rs->getRow();
    }
    //$count = $row[0];

  /* Render page 
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent( 'propeltable', 'paged-table', $xmlfile, $Criteria );
  G::RenderPage( "publish" );
  */
