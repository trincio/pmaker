<?php
/**
 * classTranslationTest.php
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
  require_once ( 'classes/model/Translation.php');

  require_once (  PATH_CORE . "config/databases.php");
  $obj = new Translation ();
  $obj = new Translation ();
	$method = array ( );
	$testItems = 0;
	$class_methods = get_class_methods($obj);
	foreach ($class_methods as $method_name) {
	$methods[ $testItems ] = $method_name;
	$testItems++;
	}
	
	$t = new lime_test( 2, new lime_output_color());
	
	$t->diag('class filterForm' );
	$t->is(  $testItems , 37,  "class Translation " . $testItems . " methods." ); 
	$t->todo(  'review all pendings in this class');

