<?php
/**
 * properties.php
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