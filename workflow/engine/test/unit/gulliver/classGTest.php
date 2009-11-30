<?php
/**
 * classGTest.php
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
  
  require_once( PATH_THIRDPARTY . '/lime/lime.php');
  require_once( PATH_THIRDPARTY.'lime/yaml.class.php');
  require_once( PATH_GULLIVER .'class.g.php');

$obj = new G();
//
$method = array ( );
$testItems = 0;

$class_methods = get_class_methods('G');
foreach ($class_methods as $method_name) {
    //echo "$method_name\n";
    $methods[ $testItems ] = $method_name;
    $testItems++;
}
print_r( $testItems );
print_r( $methods );

$t = new lime_test( 122, new lime_output_color());

$t->diag('class G' );
$t->is(  $testItems , 92,  "class G " . 92 . " methods." );

$t->isa_ok( $obj  , 'G',  'class G created');

$t->todo(  'review which PHP version is the minimum for Gulliver');

$t->is( G::getVersion()  , '3.0-1',  'Gulliver version');
$t->todo(  'store the version in a file');

$t->is( $obj->getIpAddress()  , false,   'getIpAddress()');
$t->isnt( $obj->getMacAddress()  , '',  'getMacAddress()');

$t->can_ok( $obj,      'microtime_float', 'microtime_float()');
$t->can_ok( $obj,      'setFatalErrorHandler' ,  'setFatalErrorHandler()');
$t->can_ok( $obj,      'setErrorHandler',   'setErrorHandler()');

$t->is( $obj->fatalErrorHandler( 'Fatal error')  , 'Fatal error',  'fatalErrorHandler()');

$like = '<table cellpadding=1 cellspacing=0 border=0 bgcolor=#808080 width=250><tr><td ><table cellpadding=2 cellspacing=0 border=0 bgcolor=white width=100%><tr bgcolor=#d04040><td colspan=2 nowrap><font color=#ffffaa><code> ERROR CAUGHT check log file</code></font></td></tr><tr ><td colspan=2 nowrap><font color=black><code>IP address: </code></font></td></tr> </table></td></tr></table>';
$t->is( $obj->fatalErrorHandler( 'error</b>:abc<br>')  , $like,  'fatalErrorHandler()');

$t->can_ok( $obj,      'customErrorHandler',   'customErrorHandler()');

G::customErrorHandler ( G_DB_ERROR, "message error", "filename", 10, "context" ) ;

$t->can_ok( $obj,      'showErrorSource',   'showErrorSource()');
$t->can_ok( $obj,      'customErrorLog',   'customErrorLog()');
$t->can_ok( $obj,      'verboseError',   'verboseError()');
$t->can_ok( $obj,      'encrypt',   'encrypt()');

$k = URL_KEY;
$t->is( G::encrypt ("/sysOpenSource", $k),       'Ytap33°jmZ7D46bf2Jo',     'encrypt only workspace');
$t->is( G::encrypt ("/sysOpenSource/", $k),      'Ytap33°jmZ7D46bf2Jpo',    'encrypt terminal slash');
$t->is( G::encrypt ("/sysOpenSource/en", $k),    'Ytap33°jmZ7D46bf2Jpo158', 'encrypt two levels');
$t->is( G::encrypt ("/sysOpenSource/en/test/login/login", $k),         'Ytap33°jmZ7D46bf2Jpo15+cp8ij4F°fo5fZ4mDZ5Jyi4A',            'encrypt normal page');
$t->is( G::encrypt ("/sysOpenSource/en/test/login/login/demo", $k),    'Ytap33°jmZ7D46bf2Jpo15+cp8ij4F°fo5fZ4mDZ5Jyi4GDRmNCf',      'encrypt additional level');
$t->is( G::encrypt ("/sysOpenSource/en/test/login/login?a=1&b=2", $k), 'Ytap33°jmZ7D46bf2Jpo15+cp8ij4F°fo5fZ4mDZ5Jyi4HDOcJRWzm2l',  'encrypt normal query string');
$t->todo( 'encrypt query string plus pipe');
//$t->is( G::encrypt ("/sysOpenSource/en/test/login/login?a=1|b=2", $k), 'qObe1sHV2dm46OjXxteU2dmU7djY16HR49LO56LR0tnO4g',            'encrypt query string plus pipe');
$t->todo("encrypt query string plus pipe");
$t->can_ok( $obj,      'decrypt',   'decrypt()');
$t->is( G::decrypt ('Ytap33°jmZ7D46bf2Jo', $k),  "/sysOpenSource",          'decrypt only workspace');
$t->is( G::decrypt ('Ytap33°jmZ7D46bf2Jpo', $k),   "/sysOpenSource/",       'decrypt terminal slash');
$t->is( G::decrypt ('Ytap33°jmZ7D46bf2Jpo158', $k),  "/sysOpenSource/en",   'decrypt two levels');
$t->is( G::decrypt ('Ytap33°jmZ7D46bf2Jpo15+cp8ij4F°fo5fZ4mDZ5Jyi4A', $k),             "/sysOpenSource/en/test/login/login",       'decrypt normal page');
$t->is( G::decrypt ('Ytap33°jmZ7D46bf2Jpo15+cp8ij4F°fo5fZ4mDZ5Jyi4GDRmNCf', $k),       "/sysOpenSource/en/test/login/login/demo",  'decrypt additional level');
$t->is( G::decrypt ('Ytap33°jmZ7D46bf2Jpo15+cp8ij4F°fo5fZ4mDZ5Jyi4HDOcJRWzm2l', $k) ,  "/sysOpenSource/en/test/login/login?a=1&b=2",'decrypt normal query string');
$t->todo( 'decrypt query string plus pipe');

$t->can_ok( $obj,      'lookup',   'lookup()');
$t->is( G::lookup ('optimusprime.colosa.net'),  "192.168.1.22",          'lookup any address');

$t->can_ok( $obj,      'mk_dir',   'mk_dir()');
$newDir = '/tmp/test/directory';

$r = G::verifyPath ( $newDir );
if ( $r ) rmdir ( $newDir );
 
$r = G::mk_dir ( $newDir );
$r = G::verifyPath ( $newDir);
$t->is( $r,      true,   "mk_dir() $newDir");
$t->can_ok( $obj,      'verifyPath',   "verifyPath() $newDir");

$t->isnt( PATH_CORE,      'PATH_CORE',   'Constant PATH_CORE');
$t->isnt( PATH_GULLIVER,      'PATH_GULLIVER',   'Constant PATH_GULLIVER');
//$t->is( G::expandPath("class.x.php"),      '/opt/processmaker/trunk/workflow/engine/class.x.php/',   'expandPath()');
$phatSitio     = "/home/arturo/processmaker/trunk/workflow/engine/class.x.php/";
$phatBuscar = "/processmaker/trunk/workflow/engine/class.x.php/";
$t->is(( ereg( $phatBuscar , $phatSitio ) ), 1 ,   'expandPath()');

$t->is( G::LoadSystem("error"),      NULL,   'LoadSystem()');
$t->can_ok( $obj,      'RenderPage',   'RenderPage()');
$t->can_ok( $obj,      'LoadSkin',   'LoadSkin()');
$t->can_ok( $obj,      'LoadInclude',   'LoadInclude()');

$t->can_ok( $obj,      'LoadTemplate',   'LoadTemplate()');
$t->can_ok( $obj,      'LoadClassRBAC',   'LoadClassRBAC()');
$t->can_ok( $obj,      'LoadClass',   'LoadClass()');
$t->can_ok( $obj,      'LoadThirdParty',   'LoadThirdParty()');
$t->can_ok( $obj,      'encryptlink',   'encryptlink()');
$t->is( G::encryptlink("normal url"),      "normal url",   'encryptlink() normal url');
$t->todo(  'more tests with encryplink and remove ENABLE_ENCRYPT dependency');
$t->can_ok( $obj,      'parseURI',   'parseURI()');
G::parseURI("http:/192.168.0.9/sysos/en/wf5/login/login/abc?ab=123&bc=zy");
//$t->is( SYS_LANG,      'en',    'parseURI() SYS_LANG');
//$t->is( SYS_SKIN,      'wf5',   'parseURI() SYS_SKIN');
$t->todo(  'more tests with parseURI');

$t->can_ok( $obj,      'streamFile',   'streamFile()');

$t->can_ok( $obj,      'sendHeaders',   'sendHeaders()');
//$t->is( G::sendHeaders('ab','js'),      'sendHeaders',   'sendHeaders() image');
$t->todo(  'more tests with sendHeaders');

$t->can_ok( $obj,      'virtualURI',   'virtualURI()');
$t->can_ok( $obj,      'createUID',   'createUID()');
$t->is( G::createUID('directory','filename'),      'bDh5aTBaUG5vNkxwMnByWjJxT2EzNVk___',   'createUID() normal');

$t->can_ok( $obj,      'getUIDName',   'getUIDName()');
$t->is( G::getUIDName('bDh5aTBaUG5vNkxwMnByWjJxT2EzNVk___','12345678901234567890'),      false,   'getUIDName() normal?');

$t->can_ok( $obj,      'formatNumber',   'formatNumber()');
$t->is( G::formatNumber('100000'),      '100000',   'formatNumber() normal');
$t->todo(  'is useful the function formatNumber??');

$t->can_ok( $obj,      'formatDate',   'formatDate()');
$t->is( G::formatDate( '2001-02-29' ),      '2001-02-29',   'formatDate() ');
$t->is( G::formatDate( '2001-02-29', 'F d, Y' ),      'Februar01 29, 2001',   'formatDate() '); //is not working
$t->is( G::formatDate( '2001-02-29', 'd.m.Y' ),      '29.02.2001',   'formatDate() ');
$t->is( G::formatDate( '2001-02-29', 'F Y d', 'fa'  ),      'اردیبهشت 2001 29',   'formatDate() ');

//$t->fail(  'improve the function formatDate !!, the month literal text is defined here!!');
$t->todo( " the month literal text is defined here!! ");
$t->can_ok( $obj,      'replaceDataField',   'replaceDataField()');

$t->todo(  'improve the function replaceDataField !!');

$t->can_ok( $obj,      'loadLanguageFile',   'loadLanguageFile()');
$t->todo(  'more tests with the function loadLanguageFile !!');

$t->can_ok( $obj,      'registerLabel',   'registerLabel()');
$t->todo(  'more tests with the function registerLabel !!');

$t->can_ok( $obj,      'LoadMenuXml',   'LoadMenuXml()');
$t->todo(  'more tests with the function LoadMenuXml !!');

$t->can_ok( $obj,      'SendMessageXml',   'SendMessageXml()');
$t->todo(  'more tests with the function SendMessageXml !!');

$t->can_ok( $obj,      'SendTemporalMessage',   'SendTemporalMessage()');
$t->todo(  'more tests with the function SendTemporalMessage !!');

$t->can_ok( $obj,      'SendMessage',   'SendMessage()');
$t->todo(  'more tests with the function SendMessage !!');

$t->can_ok( $obj,      'LoadMessage',   'LoadMessage()');
$t->todo(  'more tests with the function LoadMessage !!');

$t->can_ok( $obj,      'LoadXmlLabel',   'LoadXmlLabel()');
$t->todo(  'is useful the function LoadXmlLabel ??? delete it!!');

$t->can_ok( $obj,      'LoadMessageXml',   'LoadMessageXml()');
$t->todo(  'more tests with the function LoadMessageXml !!');

$t->can_ok( $obj,      'LoadTranslation',   'LoadTranslation()');
$t->todo(  'more tests with the function LoadTranslation !!');

$t->can_ok( $obj,      'LoadXml',   'LoadXml()');
$t->todo(  'is useful the function LoadXml ??? delete it!!');

$t->can_ok( $obj,      'LoadArrayFile',   'LoadArrayFile()');
$t->todo(  'more tests with the function LoadArrayFile !!');

$t->can_ok( $obj,      'expandUri',   'expandUri()');
$t->todo(  'more tests with the function expandUri !!');

$t->can_ok( $obj,      'genericForceLogin',   'genericForceLogin()');
$t->todo(  'more tests with the function genericForceLogin !!');

$t->can_ok( $obj,      'capitalize',   'capitalize()');
$t->todo(  'more tests with the function capitalize !!');

$t->can_ok( $obj,      'http_build_query',   'http_build_query()');
$t->todo(  'more tests with the function http_build_query !!');

$t->can_ok( $obj,      'header',   'header()');
$t->todo(  'more tests with the function header !!');

$t->can_ok( $obj,      'forceLogin',   'forceLogin()');
$t->todo(  'more tests with the function forceLogin , DELETE IT!!');

$t->can_ok( $obj,      'add_slashes',   'add_slashes()');
$t->todo(  'more tests with the function add_slashes !!');

$t->can_ok( $obj,      'uploadFile',   'uploadFile()');
$t->todo(  'more tests with the function uploadFile !!');

$t->can_ok( $obj,      'array_merges',   'array_merges()');
$t->todo(  'more tests with the function array_merges !!');

$t->can_ok( $obj,      'array_merge_2',   'array_merge_2()');
$t->todo(  'more tests with the function array_merge_2 !!');

$t->can_ok( $obj,      'generateUniqueID',   'generateUniqueID()');
$t->todo(  'more tests with the function sqlEscape !! is useful?  delete it !!');

$t->can_ok( $obj,      'CurDate',   'CurDate()');
$t->todo(  'more tests with the function sqlEscape !!');


$t->todo(  'review all methods in class G');
