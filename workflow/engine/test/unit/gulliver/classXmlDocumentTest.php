<?php
/**
 * classXmlDocumentTest.php
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
  if ( !defined ('PATH_THIRDPARTY') ) {
    require_once(  $_SERVER['PWD']. '/test/bootstrap/unit.php');
  }

  require_once( PATH_THIRDPARTY . 'lime/lime.php');
  define ( 'G_ENVIRONMENT', G_TEST_ENV);
  require_once( PATH_CORE . 'config' . PATH_SEP . 'environments.php');

  global $G_ENVIRONMENTS;
  if ( isset ( $G_ENVIRONMENTS ) ) {
    $dbfile = $G_ENVIRONMENTS[ G_TEST_ENV ][ 'dbfile'];
    if ( !file_exists ( $dbfile ) ) {
      printf("%s \n", pakeColor::colorize( "dbfile $dbfile doesn't exist for environment " . G_ENVIRONMENT  , 'ERROR'));
      exit (200);
    }
    else
     include ( $dbfile );
  }
  else
   exit (201);

  require_once( PATH_GULLIVER . 'class.dbconnection.php');
  require_once( PATH_GULLIVER . 'class.dbsession.php');
  require_once( PATH_GULLIVER . 'class.dbrecordset.php');

G::LoadThirdParty('smarty/libs','Smarty.class');
G::LoadSystem ( 'xmlform');
G::LoadSystem ( 'xmlDocument');
G::LoadSystem ( 'form');
G::LoadSystem ( 'xmlDocument');
G::LoadSystem ( 'dbconnection');
G::LoadSystem ( 'dbsession');
G::LoadSystem ( 'dbrecordset');
G::LoadSystem ( 'dbtable');


//$dbc = new DBConnection(); 
//$ses = new DBSession( $dbc);
//$obj = new DBTable ( $dbc, "APPLICATION" , array ( 'APP_UID' ) );
 
$t = new lime_test(10, new lime_output_color());
 
$obj = new Xml_Node( 'name', 'type', 'value'  ); 
$t->diag('class Xml_Node' );
$t->isa_ok( $obj  , 'Xml_Node',  'class Xml_Node created');
$t->can_ok( $obj,      'addAttribute',   'addAttribute()');
$t->can_ok( $obj,      'addChildNode',   'addChildNode()');
$t->can_ok( $obj,      'toTree',   'toTree()');
$t->can_ok( $obj,      'findNode',   'findNode()');
$t->can_ok( $obj,      'getXML',   'getXML()');

$obj = new Xml_document( 'name', 'type', 'value'  ); 
$t->diag('class Xml_document' );
$t->isa_ok( $obj  , 'Xml_document',  'class Xml_document created');
$t->can_ok( $obj,      'parseXmlFile',   'parseXmlFile()');
$t->can_ok( $obj,      'getXML',   'getXML()');

$t->todo(  'review all pendings in this class');
