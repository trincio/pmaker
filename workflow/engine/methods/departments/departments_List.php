<?php
/**
 * departments_List.php
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
if (($RBAC_Response=$RBAC->userCanAccess("PM_USERS"))!=1) return $RBAC_Response;
  

  G::LoadClass('department');
  G::LoadClass('departmentDependencie');
  G::LoadClass('organizationalChart');
  G::LoadClass('toolBar');
  G::LoadClass('popupMenu');
  
  $G_MAIN_MENU            = 'processmaker';
  $G_SUB_MENU             = 'users';
  $G_ID_MENU_SELECTED     = 'USERS';
  $G_ID_SUB_MENU_SELECTED = 'DEPARTMENTS';
  
  $dbc = new DBConnection();
  $ses = new DBSession($dbc);

  $department = new departmentDependencie( $dbc );
  $department->Fields['DEP_UID']='0';
  $department->Fields['DEP_TITLE']='_root_';

  $orgChar = array();
  $orgChar = LoadSubDependencies( $department );
  function LoadSubDependencies( $department ){
    $department->Fields['DEP_TITLE'] = 
      (!isset($department->Fields['DEP_TITLE']))?$department->Fields['DEP_UID']:$department->Fields['DEP_TITLE'];
    $node = new Xml_Node($department->Fields['DEP_UID'],'open',$department->Fields['DEP_TITLE']);
    while ($dep = $department->LoadDependencie()) {
      $newNode = LoadSubDependencies($dep);
      $node->addChildNode( $newNode );
    }
    return $node;
  }
  $Fields['CHART'] = $orgChar->toArray();//[$department->Fields['DEP_TITLE']];
  
  //$Fields['CHART'] = array ('MAIN0'=>'SXS','MAIN'=>array('Development'=>array('fds','dsf','123'),'Design'=>array('fdg','ssss' => ARRAY('SDFH'))));
  
  
  $Fields['USERS'] = array ('name'=>array(''));
  $Fields['AJAXSERVER'] = G::encryptLink(SYS_URI.'departments/departments_Ajax');

  $G_PUBLISH = new Publisher;
  $G_HEADER->addInstanceModule('leimnud', 'app');
  $G_PUBLISH->AddContent('xmlform', 'popupMenu', 'departments/popupMenu', '', '' , 'authentication.php');
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'departments/organizationalChart', '', $Fields , '');
  
  G::RenderPage( "publish" );
?>