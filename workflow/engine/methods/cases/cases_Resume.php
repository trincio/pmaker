<?php
/**
 * cases_Resume.php
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
 /* Permissions */
  switch ($RBAC->userCanAccess('PM_CASES'))
  {
  	case -2:
  	  G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels');
  	  G::header('location: ../login/login');
  	  die;
  	break;
  	case -1:
  	  G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
  	  G::header('location: ../login/login');
  	  die;
  	break;
  } 

  /* Includes */
  G::LoadClass('case');
  
  /* GET , POST & $_SESSION Vars */
 
  /* Menues */
  $G_MAIN_MENU            = 'processmaker';
  $G_SUB_MENU             = 'cases';
  $G_ID_MENU_SELECTED     = 'CASES';
  $G_ID_SUB_MENU_SELECTED = '_';

 /* Prepare page before to show */
  $oCase = new Cases();
  $aFields = $oCase->loadCase( $_SESSION['APPLICATION'] );

  /* Render page */
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'cases/cases_Resume.xml', '', $aFields, '');
  G::RenderPage( 'publish' );
