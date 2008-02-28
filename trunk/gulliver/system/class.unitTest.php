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
class unitTest
{
  var $dbc;
  var $times;
  var $yml;
  var $domain;
  var $testLime;
  function unitTest( $ymlFile, &$testLime, &$testDomain )
  {
    if (!isset($testDomain)) $testDomain = new ymlDomain();
    $this->domain =& $testDomain;
    $this->testLime =& $testLime;
    $this->yml = new ymlTestCases( $ymlFile, $this->domain, $this->testLime );
  }
  //Load a Test (group of unitary tests) defined in the Yml file.
  function load( $testName, $fields=array() )
  {
    $this->yml->load( $testName, $fields );
  }
  //Run one single unit test from the loaded Test
  function runSingle( $fields=array() )
  {
    return $this->yml->runSingle( $this, $fields );
  }
  //Run a group of unit tests from the loaded Test 
  function runMultiple( $fields=array(), $count = -1, $start=0)
  {
    return $this->yml->runMultiple( $this, $fields, $count, $start );
  }
  //Run all the unit tests from the loaded Test
  function runAll( $fields=array())
  {
    return $this->yml->runMultiple( $this, $fields, -1, 0 );
  }
  //A sample of "Function" to run a unit test. 
  function sampleTestFunction( $testCase , &$Fields )
  {
    $result = ($Fields['APP_UID']!='')?"OK":"FALSE";
    return $result;
  }
}
?>