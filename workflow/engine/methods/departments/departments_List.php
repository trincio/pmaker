<?php
/**
 * $Id$
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * You can contact Colosa Inc, 2655 Le Jeune Road, Suite 1112, Coral Gables, 
 * FL 33134, USA or email info@colosa.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * ProcessMaker" logo and retain the original copyright notice. If the display
 * of the logo is not reasonably feasible for technical reasons, the
 * Appropriate Legal Notices must display the words "Powered by ProcessMaker"
 * and retain the original copyright notice.
 * -
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