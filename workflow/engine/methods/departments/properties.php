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
  
  $G_MAIN_MENU     = 'wf.login';
  $G_MENU_SELECTED = '';
  $dbc = new DBConnection();
  $ses = new DBSession($dbc);

  $department = new Department( $dbc );
  $department->Load($_GET['DEP_UID']);
  $Fields = $department->Fields;
  $Fields['SYS_LANG']=SYS_LANG;

  $G_PUBLISH = new Publisher;
  $G_PUBLISH->publisherId='departmentProperties';
  $G_HEADER->clearScripts();
  
  $G_PUBLISH->AddContent('panel-init', 'mainPanel', array('title'=>G::LoadTranslation('ID_DEPARTMENT'),'left'=>'200','top'=>'100','width'=>600,'height'=>500,'tabWidth'=>120,'modal'=>true));
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'departments/properties', '', $Fields , 'department_Update');
  $G_PUBLISH->AddContent('pagedtable', 'paged-table', 'departments/departments_Users', '', $Fields , '');
  /*"  leimnud.Package.Load(false, {Type: 'file', Path: '".$G_FORM->scriptURL."', Absolute : true});\n";
  var_dump(  );*/

  $G_PUBLISH->AddContent('panel-tab','Properties','departmentProperties[1]','','');
  $G_PUBLISH->AddContent('panel-tab','Users','departmentProperties[2]','','');
  $G_PUBLISH->AddContent('panel-close');
  
  G::RenderPage( "publish" , "raw" );
?>