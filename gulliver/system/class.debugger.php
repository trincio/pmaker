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
/*
 * Created on 30-01-2008
 *
 * @author David Callizaya <davidsantos@colosa.com>
 */
global $BUG_OUTPUT;
G::VerifyPath(realpath(PATH_TRUNK."..")."/bug/",true);
$BUG_OUTPUT = fopen(PATH_TRUNK."../bug/log.txt","a");
//sleep(1);
class BUG {
  function send( $msg ) {
    global $BUG_OUTPUT;
    if ($BUG_OUTPUT) fwrite($BUG_OUTPUT, $msg . "\r\n" );
    return $msg . "\r\n";
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
                BUG::printArgs($step['args']). ")\n";
        $limit--;
        if ($limit===0) return $out;
      }
    }
    return $out;
  }
  function traceRoute( $tts=2 , $limit=-1 ) {
    return BUG::send(BUG::traceError( $tts , $limit ));
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

?>