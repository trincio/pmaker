<?php
/**
 * classXmlDbTest.php
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
  require_once (  PATH_CORE . "config/databases.php");  
  require_once ( "propel/Propel.php" );
  Propel::init(  PATH_CORE . "config/databases.php");
 
  G::LoadThirdParty('smarty/libs','Smarty.class');
  G::LoadSystem ( 'error');
  G::LoadSystem ( 'xmlform');
  G::LoadSystem ( 'xmlDocument');
  G::LoadSystem ( 'dbconnection');
  G::LoadSystem ( 'dbsession');
  G::LoadSystem ( 'form');
  G::LoadClass ( 'xmlDb');

  $dbc = new DBConnection(); 
  $ses = new DBSession( $dbc);
  $obj = new XmlDb ($dbc); 
  $t   = new lime_test( 5, new lime_output_color() );

//--------
  $className = "XmlDb";
  $reflect = new ReflectionClass( $className );
	$method = array ( );
	$testItems = 0;
//-------
 
  $t->diag('class XmlDb' );
  $t->isa_ok( $obj  , 'XMLDB',  'class XmlDb created');
//---- 
  $t->is( count($reflect->getMethods() ) , 2,  "class $className currenlty have " . count($reflect->getMethods()) . ' methods.' );
//----
  //method connect
  $t->can_ok( $obj,      'connect',   'connect() is callable' );

//  $result = $obj->connect ( $dsn, $options);
//  $t->isa_ok( $result,      'NULL',   'call to method connect ');


  //method isError
  $t->can_ok( $obj,      'isError',   'isError() is callable' );

//  $result = $obj->isError ( $result);
//  $t->isa_ok( $result,      'NULL',   'call to method isError ');


  $t->todo(  'this class permits CRUD operations over an xml file');

?>