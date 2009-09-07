<?php
/**
 * classDepartmentTest.php
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
  $unitFilename = $_SERVER['PWD'] . '/test/bootstrap/unit.php' ;
  require_once( $unitFilename );

  require_once( PATH_THIRDPARTY . '/lime/lime.php');
  require_once( PATH_THIRDPARTY.'lime/yaml.class.php');

  G::LoadThirdParty('smarty/libs','Smarty.class');
  G::LoadSystem ( 'error');
  G::LoadSystem ( 'xmlform');
  G::LoadSystem ( 'xmlDocument');
  G::LoadSystem ( 'form');
  G::LoadSystem ( 'dbconnection');
  G::LoadSystem ( 'dbsession');
  G::LoadSystem ( 'dbrecordset');
  G::LoadSystem ( 'dbtable');
  G::LoadClass  ( 'pmObject');
  G::LoadClass  ( 'department'); 

  require_once (  PATH_CORE . "config/databases.php");

  $dbc = new DBConnection();
  $ses = new DBSession( $dbc);

  $obj = new Department ($dbc);
  $t   = new lime_test( 8, new lime_output_color() );
 
  $t->diag('class Department' );
  $t->isa_ok( $obj  , 'Department',  'class Department created');


  //method LoadParent
  $t->can_ok( $obj,      'LoadParent',   'LoadParent() is callable' );

//  $result = $obj->LoadParent ( );
//  $t->isa_ok( $result,      'NULL',   'call to method LoadParent ');


  //method LoadDependencie
  $t->can_ok( $obj,      'LoadDependencie',   'LoadDependencie() is callable' );

//  $result = $obj->LoadDependencie ( );
//  $t->isa_ok( $result,      'NULL',   'call to method LoadDependencie ');


  //method Save
  $t->can_ok( $obj,      'Save',   'Save() is callable' );

//  $result = $obj->Save ( );
//  $t->isa_ok( $result,      'NULL',   'call to method Save ');


  //method load
  $t->can_ok( $obj,      'load',   'load() is callable' );

//  $result = $obj->load ( $sUID);
//  $t->isa_ok( $result,      'NULL',   'call to method load ');


  //method delete
  $t->can_ok( $obj,      'delete',   'delete() is callable' );

//  $result = $obj->delete ( );
//  $t->isa_ok( $result,      'NULL',   'call to method delete ');





  $t->todo(  'TODO This class has "generic" load, save and delete methods. Review later...');
  $t->fail(  'review all pendings methods in this class');
?>
