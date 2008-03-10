<?php
/**
 * class.debugger.php
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
global $BUG_OUTPUT;

if (!defined('BUG_REMOTE_HOST')) define('BUG_REMOTE_HOST', '192.168.0.58');
if (!defined('BUG_REMOTE_PORT')) define('BUG_REMOTE_PORT', 80);
$BUG_OUTPUT = fopen(PATH_TPL.'debug.log','a');//@fsockopen(BUG_REMOTE_HOST, BUG_REMOTE_PORT , $errno, $errstr, 30);
//sleep(1);
class BUG {
  function send( $msg ) {
    global $BUG_OUTPUT;
    if ($BUG_OUTPUT) fwrite($BUG_OUTPUT, $msg . "\r\n" );
  }
  function print_r( $var ) {
    global $BUG_OUTPUT;
    $msg = print_r( $var , 1 ) . "\r\n";
    if ($BUG_OUTPUT) fwrite($BUG_OUTPUT, $msg );
  }
  function var_dump( $var ) {
    global $BUG_OUTPUT;
    ob_start();
    var_dump( $var );
    if ($BUG_OUTPUT) fwrite($BUG_OUTPUT, ob_get_contents() );
    ob_end_clean();
  }
  function close(){
    global $BUG_OUTPUT;
    fclose($BUG_OUTPUT);
  }
  function traceError( $tts=2 , $limit=-1 ) {
    $trace = debug_backtrace();
    $out='';
    foreach($trace as $step) {
      if ($tts>0) {
        $tts--;
      } else {
        $out .= '['.basename($step['file']).': '.$step['line'].'] : ' . $step['function'] .'(' .
                (isset($step['args'])?BUG::printArgs($step['args']):''). ")\n";
        $limit--;
        if ($limit===0) return $out;
      }
    }
    return $out;
  }
  function traceRoute( $tts=2 , $limit=-1 ) {
    BUG::send(BUG::traceError( $tts , $limit ));
  }
  function printArgs( $args ) {
    $out = '';
    if (is_array($args)){
      foreach($args as $arg) {
        if ($out!=='') $out .= ' ,';
        if (is_string($arg)) $out .= "'".($arg)."'";
        elseif (is_array($arg) )
          $out .= print_r ( $arg ,1 );
        elseif (is_object($arg))
          $out .= get_class($arg);// print_r ( $arg ,1 );
        else
          $out .= sprintf ( "%s" ,$arg );
      }
    } else {
      $out = print_r($args,1);
    }
    return $out;
  }
}
/*
  // error handler function
  function myErrorHandler($errno, $errstr, $errfile, $errline)
  {
    $out='';
    switch ($errno) {
    case E_USER_ERROR:
     $out .= "My ERROR [$errno] $errstr\n";
     $out .= "  Fatal error in line $errline of file $errfile";
     $out .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")\n";
     $out .= "Aborting...\n";
     BUG::send($out);
     BUG::send(BUG::traceError(2));
     exit(1);
     break;
    case E_USER_WARNING:
     $out .= "My WARNING [$errno] $errstr\n";
     BUG::send(BUG::traceError(2));
     BUG::send($out);
     break;
    case E_USER_NOTICE:
     $out .= "My NOTICE [$errno] $errstr\n";
     BUG::send(BUG::traceError(2));
     BUG::send($out);
     break;
    default:
     //$out .= "Unknown error type: [$errno] $errstr \n in line $errline of file $errfile\n";
     //BUG::send($out);
     //BUG::send(BUG::traceError(2));
     break;
    }
  }
  ini_alter("display_errors",TRUE);
	//ini_set('error_reporting', E_ALL);
	error_reporting(8191);
  set_error_handler("myErrorHandler");*/
?>