<?php
/**
 * classDynaformTest.php
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
  require_once( 'propel/Propel.php' );
  Propel::init(  PATH_CORE . "config/databases.php");


  G::LoadThirdParty('smarty/libs','Smarty.class');
  G::LoadSystem ( 'error');
  G::LoadSystem ( 'xmlform');
  G::LoadSystem ( 'xmlDocument');
  G::LoadSystem ( 'form');
  G::LoadSystem ( 'testTools');

  require_once( PATH_CORE.'/classes/model/Dynaform.php');

  $obj = new Dynaform ();
  $t   = new lime_test( 36, new lime_output_color() );

  $t->diag('class Dynaform' );
  $t->isa_ok( $obj  , 'Dynaform',  'class Dynaform created');

  //method load
  //#2
  $t->can_ok( $obj,      'getDynTitle',   'getDynTitle() is callable' );
  //#3
  $t->can_ok( $obj,      'setDynTitle',   'setDynTitle() is callable' );
  //#4
  $t->can_ok( $obj,      'getDynDescription',   'getDynDescription() is callable' );
  //#5
  $t->can_ok( $obj,      'setDynDescription',   'setDynDescription() is callable' );
  //#6
  $t->can_ok( $obj,      'create',   'create() is callable' );
  //#7
  $t->can_ok( $obj,      'update',   'update() is callable' );
  //#8
  $t->can_ok( $obj,      'load',   'load() is callable' );
  //#9
  $t->can_ok( $obj,      'remove',   'remove() is callable' );

  //getDynUid
  //#10
  $t->is( $obj->getDynUid(),      '',   'getDynUid() return empty, when the instance doesnt have any row' );
  
  //getDynTitle
  try {
    $obj = new Dynaform (); 
    $res = $obj->getDynTitle();
  } 
  catch ( Exception $e ) {
  //#11
    $t->isa_ok( $e,      'Exception',   'getDynTitle() return error when DYN_UID is not defined' );
  //#12
    $t->is ( $e->getMessage(),      "Error in getDynTitle, the DYN_UID can't be blank",   'getDynTitle() return Error in getDynTitle, the DYN_UID cant be blank' );
  }

  //setDynDescription
  try {
    $obj = new Dynaform (); 
    $obj->setDynDescription('x');
  } 
  catch ( Exception $e ) {
  //#13
    $t->isa_ok( $e,      'Exception',   'setDynDescription() return error when DYN_UID is not defined' );
  //#14
    $t->is ( $e->getMessage(),      "Error in setDynDescription, the DYN_UID can't be blank",   'setDynDescription() return Error in getAppTitle, the APP_UID cant be blank' );
  }

  //getDynDescription
  try {
    $obj = new Dynaform (); 
    $res = $obj->getDynDescription();
  } 
  catch ( Exception $e ) {
  //#15
    $t->isa_ok( $e,      'Exception',   'getDynDescription() return error when DYN_UID is not defined' );
  //#16
    $t->is ( $e->getMessage(),      "Error in getDynDescription, the DYN_UID can't be blank",   'getDynDescription() return Error in getDynDescription, the DYN_UID cant be blank' );
  }

  //setAppDescription
  try {
    $obj = new Dynaform (); 
    $obj->setDynDescription('x');
  } 
  catch ( Exception $e ) {
  //#17
    $t->isa_ok( $e,      'Exception',   'setAppDescription() return error when DYN_UID is not defined' );
  //#18
    $t->is ( $e->getMessage(),      "Error in setDynDescription, the DYN_UID can't be blank",   'setAppDescription() return Error in getAppDescription, the APP_UID cant be blank' );
  }
  


  //create new row
  try {
    $obj = new Dynaform ();
    $res = $obj->create();
  }
  catch ( Exception $e ) {
  //#19
    $t->isa_ok( $e,      'PropelException',   'create() return error when PRO_UID is not defined' );
  //#20
    $t->like ( $e->getMessage(),      "%The dynaform cannot be created. The PRO_UID is empty.%",   'create() return The dynaform cannot be created. The USR_UID is empty.' );
  }
  //create
  try {

    $Fields['PRO_UID'] = '1';  // we need a valid process id
    $obj = new Dynaform ();
    $proUid = $obj->create( $Fields );
  //#21
    $t->isa_ok( $proUid,      'string',   'create(), creates a new Dynaform' );
  //#22
  //$t->is ( strlen($proUid),      14,   'create(), creates a new Dynaform, Guid lenth=14 chars' );
    $t->is ( strlen($proUid),      32,   'create(), creates a new Dynaform, Guid lenth=32 chars' );
    $res = $obj->load( $proUid );
  //#23
    $t->isa_ok( $res,      'array',   'load(), loads a new Dynaform' );
  //#24
    $t->is ( $res['DYN_UID'],      $proUid,   'load(), loads a new Dynaform, valid DYN_UID' );
  //#25
    $t->is ( $res['DYN_DESCRIPTION'],      'Default Dynaform Description',   'load(), loads a new Dynaform, valid DYN_DESCRIPTION' );

  }
  catch ( Exception $e ) {
    $t->like ( $e->getMessage(),      "%Unable to execute INSERT statement%",   'create() return Error in getAppTitle, the APP_UID cant be blank' );
  }

  //update with empty
  try {
    $obj = new Dynaform ();
    $res = $obj->update( NULL );
  }
  catch ( Exception $e ) {
  //#26
    $t->isa_ok( $e,      'Exception',   'update() returns error when DYN_UID is not defined' );
  //#27
    $t->is ( $e->getMessage(),   "This row doesn't exists!",   "update() This row doesn't exists!" );
  }

  //update with $fields
  $newFilename = 'new filename ' . rand( 1000, 5000);
  $newTitle    = 'new title ' . rand( 1000, 5000);
  $newDescription = 'new Description '. rand( 1000, 5000);
  $Fields['DYN_UID'] = $proUid;
  $Fields['DYN_TITLE']    = $newTitle;
  $Fields['DYN_FILENAME'] = $newFilename;
  try {
    $obj = new Dynaform ();
    $res = $obj->update( $Fields);
  //#28
    $t->is ( $res,   1,   "update() update 1 row" );
    $Fields = $obj->Load ( $proUid );
  //#29
    $t->is ( $obj->getdynUid(),   $proUid,   "update() DYN_UID = ". $proUid );
  //#30
    $t->is ( $obj->getDynTitle(),   $newTitle,   "update() getAppTitle" );
  //#31
    $t->is ( $Fields['DYN_TITLE'],   $newTitle,   "update() DYN_TITLE= ". $newTitle );
  //#32
    $t->is ( $Fields['DYN_FILENAME'], $newFilename, "update() DYN_FILENAME = $newFilename" );
  }
  catch ( Exception $e ) {
  //#14
    $t->isa_ok( $e,      'PropelException',   'update() return error ' . $e->getMessage() );
    print $e->getMessage();
  }

//remove with empty

  try {
    $obj = new Dynaform ();
    $res = $obj->remove( NULL );
  }
  catch ( Exception $e ) {
  //#33
    $t->isa_ok( $e,      'Exception',   'remove() returns error when UID is not defined' );
  //#34
    $t->is ( $e->getMessage(),   "This row doesn't exists!",   "remove() This row doesn't exists!" );
  }

  //remove with $fields
  $Fields['DYN_UID'] = $proUid;
  try {
    $obj = new Dynaform ();
    $res = $obj->remove( $Fields );
  //#35
    $t->is ( $res,   NULL,   "remove() remove row $proUid" );
  }
  catch ( Exception $e ) {
  //#14
    $t->isa_ok( $e,      'PropelException',   'remove() return error ' . $e->getMessage() );
  }

  //remove with $proUid
  $obj = new Dynaform ();
  $proUid = $obj->create( '1' );
  try {
    $obj = new Dynaform ();
    $res = $obj->remove ($proUid );
  //#36
    $t->is ( $res,   NULL,   "remove() remove row $proUid" );
  }
  catch ( Exception $e ) {
  //#14
    $t->isa_ok( $e,      'PropelException',   'remove() return error ' . $e->getMessage() );
  }

?>
