<?php
/**
 * class.g.php
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

/**
 * @package home.gulliver.system2
*/

class G
{

  function is_https()
  {
    if(isset($_SERVER['HTTPS']))
    {   if($_SERVER['HTTPS']=='on')
            return true;
        else
            return false;
    }
    else
      return false;
  }
  /**
  * Fill array values (recursive)
  * @author maborak <maborak@maborak.com>
  * @access public
  * @param  Array $arr
  * @param  Void  $value
  * @param  Boolean $recursive
  * @return Array
  */
  function array_fill_value($arr=Array(),$value='',$recursive=false)
  {
    if(is_array($arr))
    {
      foreach($arr as $key=>$val)
      {
        if(is_array($arr[$key]))
        {
          $arr[$key]=($recursive===true)?G::array_fill_value($arr[$key],$value,true):$val;
        }
        else
        {
          $arr[$key]=$value;
        }
      }
    }
    else
    {
      $arr=Array();
    }
    return $arr;
  }
  /**
  * Generate Password Random
  * @author maborak <maborak@maborak.com>
  * @access public
  * @param  Int
  * @return String
  */
  function generate_password($length=8)
  {
    $password = "";
    $possible = "0123456789bcdfghjkmnpqrstvwxyz";
    $i = 0;
    while($i<$length)
    {
      $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
      if(!strstr($password, $char))
      {
        $password .= $char;
        $i++;
      }
    }
    return $password;
  }
  /**
  * Array concat
  * array_concat(ArrayToConcat,ArrayOriginal);
  * @author maborak <maborak@maborak.com>
  * @access public
  * @param  Array
  * @return Array
  */
  function array_concat()
  {
    $nums = func_num_args();
    $vars = func_get_args();
    $ret  = Array();
    for($i=0;$i<$nums;$i++)
    {
      if(is_array($vars[$i]))
      {
        foreach($vars[$i] as $key=>$value)
        {
          $ret[$key]=$value;
        }
      }
    }
    return $ret;
  }

  /**
  * Compare Variables
  * var_compare(value,[var1,var2,varN]);
  * @author maborak <maborak@maborak.com>
  * @access public
  * @param  void $value
  * @param  void $var1-N
  * @return Boolean
  */
  function var_compare($value=true,$varN)
  {
    $nums = func_num_args();
    if($nums<2){return true;}
    $vars = func_get_args();
    $ret  = Array();
    for($i=1;$i<$nums;$i++)
    {
      if($vars[$i]!==$value)
      {
        return false;
      }
    }
    return true;
  }
  /**
  * Emulate variable selector
  * @author maborak <maborak@maborak.com>
  * @access public
  * @param  void
  * @return void
  */
  function var_probe()
  {
    //return (!$variable)?
    $nums = func_num_args();
    $vars = func_get_args();
    for($i=0;$i<$nums;$i++)
    {
      if($vars[$i])
      {
        return $vars[$i];
      }
    }
    return 1;
  }

/**
   * Get the current version of gulliver classes
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @return string
   */
  /*public static*/ function &getVersion(  )
  {
    //majorVersion.minorVersion-SvnRevision
    return '3.0-1';
  }

  /*public static*/ function getIpAddress () {
    if (getenv('HTTP_CLIENT_IP')) {
      $ip = getenv('HTTP_CLIENT_IP');
    }
    elseif(getenv('HTTP_X_FORWARDED_FOR')) {
      $ip = getenv('HTTP_X_FORWARDED_FOR');
    }
    else {
      $ip = getenv('REMOTE_ADDR');
    }
    return $ip;
  }
  function getMacAddress() {
    if ( strstr ( getenv ( 'OS' ), 'Windows' ) ) {
      $ipconfig = `ipconfig /all`;
      preg_match('/[\dA-Z]{2,2}[\:-][\dA-Z]{2,2}[\:-][\dA-Z]{2,2}[\:-][\dA-Z]{2,2}[\:-][\dA-Z]{2,2}[\:-][\dA-Z]{2,2}/i',$ipconfig,$mac);
    } else {
      $ifconfig = `/sbin/ifconfig`;
      preg_match('/[\dA-Z]{2,2}[\:-][\dA-Z]{2,2}[\:-][\dA-Z]{2,2}[\:-][\dA-Z]{2,2}[\:-][\dA-Z]{2,2}[\:-][\dA-Z]{2,2}/i',$ifconfig,$mac);
    }
    return isset($mac[0])? $mac[0]:'00:00:00:00:00:00';
  }

  /*public static*/ function microtime_float() {
    return array_sum(explode(' ',microtime()));
  }
  /* custom error functions */

  /*public static*/ function &setFatalErrorHandler( $newFatalErrorHandler = null )
  {
    if ( isset ( $newFatalErrorHandler ) ) {
      set_error_handler( $newFatalErrorHandler );
    }
    else {
      ob_start( array ( 'G', 'fatalErrorHandler' ) );
    }
    return true;
  }

  /*public static*/ function setErrorHandler( $newCustomErrorHandler = null )
  {
    if ( isset ( $newCustomErrorHandler ) ) {
      set_error_handler( $newCustomErrorHandler );
    }
    else {
      set_error_handler( array("G", "customErrorHandler"));
    }
    return true;
  }

  /*public static*/ function fatalErrorHandler($buffer) {
    if (ereg("(error</b>:)(.+)(<br)", $buffer, $regs) ) {
      $err = preg_replace("/<.*?>/","",$regs[2]);
      G::customErrorLog('FATAL', $err,  '', 0, '');
      $ip_addr = G::getIpAddress();
      $errorBox = "<table cellpadding=1 cellspacing=0 border=0 bgcolor=#808080 width=250><tr><td >" .
                  "<table cellpadding=2 cellspacing=0 border=0 bgcolor=white width=100%>" .
                  "<tr bgcolor=#d04040><td colspan=2 nowrap><font color=#ffffaa><code> ERROR CAUGHT check log file</code></font></td></tr>" .
                  "<tr ><td colspan=2 nowrap><font color=black><code>IP address: $ip_addr</code></font></td></tr> " .
                  "</table></td></tr></table>";
      return $errorBox;
    }
    return $buffer;
  }

  /*public static*/ function customErrorHandler ( $errno, $msg, $file, $line, $context) {
  switch ($errno) {
    case E_ERROR:
    case E_USER_ERROR:
      $type = "FATAL";
      G::customErrorLog ($type, $msg, $file, $line);
      G::verboseError ($type, $errno, $msg, $file, $line, $context);
      if (defined ("ERROR_SHOW_SOURCE_CODE") && ERROR_SHOW_SOURCE_CODE)
        G::showErrorSource ($type, $msg, $file, $line, "#c00000");
      die ();
      break;
    case E_WARNING:
    case E_USER_WARNING:
      $type = "WARNING";
      G::customErrorLog ($type, $msg, $file, $line);
      break;
    case E_NOTICE:
    case E_USER_NOTICE:
      $type = "NOTICE";
      if (defined ("ERROR_LOG_NOTICE_ERROR") && ERROR_LOG_NOTICE_ERROR)
        G::customErrorLog ($type, $msg, $file, $line);
      break;
    case E_STRICT:
      $type = "STRICT"; //dont show STRICT Errors
      //if (defined ("ERROR_LOG_NOTICE_ERROR") && ERROR_LOG_NOTICE_ERROR)
      //  G::customErrorLog ($type, $msg, $file, $line);
      break;
    default:
      $type = "ERROR ($errno)";
        G::customErrorLog ($type, $msg, $file, $line);
      break;
    }

    if (defined ("ERROR_SHOW_SOURCE_CODE") && ERROR_SHOW_SOURCE_CODE && $errno <> E_STRICT  )
      G::showErrorSource ($type, $msg, $file, $line);
  }
  /**
   * Function showErrorSource
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string type
   * @parameter string msg
   * @parameter string file
   * @parameter string line
   * @return string
   */
  function showErrorSource($type, $msg, $file, $line)
  {
    global $__src_array;
    $line_offset = 3;

    if (! isset ($__src_array[$file]))
      $__src_array[$file] = @file ($file);

    if (!$__src_array[$file])
      return;

    if ($line - $line_offset < 1)
      $start = 1;
    else
      $start = $line - $line_offset;

    if ($line + $line_offset > count ($__src_array[$file]))
      $end = count ($__src_array[$file]);
    else
      $end = $line + $line_offset;

    print "<table cellpadding=1 cellspacing=0 border=0 bgcolor=#808080 width=80%><tr><td >";
    print "<table cellpadding=2 cellspacing=0 border=0 bgcolor=white width=100%>";
    print "<tr bgcolor=#d04040>
      <td colspan=2 nowrap><font color=#ffffaa><code> $type: $msg</code></font></td></tr>
      <tr >
      <td colspan=2 nowrap><font color=gray>File: $file</font></td></tr>
    ";
    for ($i = $start; $i <= $end; $i++) {
      $str = @highlight_string ("<?" . $__src_array[$file][$i-1] . "?>", TRUE);

      $pos1 = strpos ($str,"&lt;?");
      $pos2 = strrpos ($str,"?&gt;");

      $str = substr ($str, 0, $pos1) .
        substr ($str, $pos1+5, $pos2-($pos1+5)) .
        substr ($str, $pos2+5);

      ($i == $line) ? $bgcolor = "bgcolor=#ffccaa" : $bgcolor = "bgcolor=#ffffff";
      print "<tr><td bgcolor=#d0d0d0 width=15 align=right><code>$i</code></td>
        <td $bgcolor>$str</td></tr>";
    }

    print "</table></td></tr></table><p>";
  }

  /*public static*/ function customErrorLog ($type, $msg, $file, $line)
  {
    global $HTTP_X_FORWARDED_FOR, $REMOTE_ADDR, $HTTP_USER_AGENT, $REQUEST_URI;

    $ip_addr = G::getIpAddress();

    if (defined ('APPLICATION_CODE'))
      $name = APPLICATION_CODE;
    else
      $name = "php";

    if ( $file != '') $msg .= " in $file:$line ";

    $date = date ( 'Y-m-d H:i:s');
    $REQUEST_URI = getenv ( 'REQUEST_URI' );
    $HTTP_USER_AGENT = getenv ( 'HTTP_USER_AGENT' );
    error_log ("[$date] [$ip_addr] [$name] $type: $msg [$HTTP_USER_AGENT] URI: $REQUEST_URI", 0);
  }


  /*public static*/ function verboseError ($type, $errno, $msg, $file, $line, $context) {
    global $SERVER_ADMIN;

    print "<h1>Error!</h1>";
    print "An error occurred while executing this script. Please
  contact the <a href=mailto:$SERVER_ADMIN>$SERVER_ADMIN</a> to
  report this error.";
    print "<p>";
    print "Here is the information provided by the script:";
    print "<hr><pre>";
    print "Error type: $type (code: $errno)<br>";
    print "Error message: $msg<br>";
    print "Script name and line number of error: $file:$line<br>";
    print "Variable context when error occurred: <br>";
    print_r ($context);
    print "</pre><hr>";
  }

/*** Encrypt and decrypt functions ****/

/**
   * Encrypt string
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $string
   * @param  string $key
   * @return string
   */
  function encrypt($string, $key) {
    //print $string;
//    if ( defined ( 'ENABLE_ENCRYPT' ) && ENABLE_ENCRYPT == 'yes' ) {
      if (strpos($string, '|', 0) !== false) return $string;
      $result = '';
      for($i=0; $i<strlen($string); $i++) {
        $char = substr($string, $i, 1);
        $keychar = substr($key, ($i % strlen($key))-1, 1);
        $char = chr(ord($char)+ord($keychar));
        $result.=$char;
      }
      //echo $result . '<br>';
      $result = base64_encode($result);
      $result = str_replace ( '/' , '°' , $result);
      $result = str_replace ( '=' , '' , $result);
  //  }
   // else
    //  $result = $string;

    return $result;
  }

/**
   * Decrypt string
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $string
   * @param  string $key
   * @return string
   */
  /*public static*/ function decrypt($string, $key) {

//   if ( defined ( 'ENABLE_ENCRYPT' ) && ENABLE_ENCRYPT == 'yes' ) {

     //if (strpos($string, '|', 0) !== false) return $string;
     $result = '';
     $string = str_replace ( '°', '/' , $string);
     $string_jhl=explode("?",$string);
     $string = base64_decode($string);
     $string = base64_decode($string_jhl[0]);

     for($i=0; $i<strlen($string); $i++) {
       $char = substr($string, $i, 1);
       $keychar = substr($key, ($i % strlen($key))-1, 1);
       $char = chr(ord($char)-ord($keychar));
       $result.=$char;
     }
     if (!empty($string_jhl[1])) $result.='?' . $string_jhl[1];
  // }
  // else
    // $result = $string;
   return $result;
  }

  /**
   * Look up an IP address direction
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $target
   * @return  void
  */
  function lookup($target)
  {
    if( eregi("[a-zA-Z]", $target) )
      $ntarget = gethostbyname($target);
    else
      $ntarget = gethostbyaddr($target);
    return($ntarget);
  }


/***************  path functions *****************/

  /*public static*/ function mk_dir( $strPath, $rights = 0777)
  {
    $folder_path = array($strPath);
    $oldumask = umask(0);
    while(!@is_dir(dirname(end($folder_path)))
            && dirname(end($folder_path)) != '/'
            && dirname(end($folder_path)) != '.'
            && dirname(end($folder_path)) != '')
      array_push($folder_path, dirname(end($folder_path))); //var_dump($folder_path); die;
    while($parent_folder_path = array_pop($folder_path))
      if(!@is_dir($parent_folder_path))
          if(!@mkdir($parent_folder_path, $rights))
        //trigger_error ("Can't create folder \"$parent_folder_path\".", E_USER_WARNING);
    umask($oldumask);
  }

  function rm_dir($dirName) {
    if(empty($dirName)) {
        return;
    }
    if(file_exists($dirName)) {
        $dir = dir($dirName);
        while($file = $dir->read()) {
            if($file != '.' && $file != '..') {
                if(is_dir($dirName.'/'.$file)) {
                    G::rm_dir($dirName.'/'.$file);
                } else {
                    @unlink($dirName.'/'.$file) or die('File '.$dirName.'/'.$file.' couldn\'t be deleted!');
                }
            }
        }
        @rmdir($dirName.'/'.$file) or die('Folder '.$dirName.'/'.$file.' couldn\'t be deleted!');
    } else {
        echo 'Folder "<b>'.$dirName.'</b>" doesn\'t exist.';
    }
  }

/**
   * verify path
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string  $strPath      path
   * @param  boolean $createPath   if true this function will create the path
   * @return boolean
   */
  /*public static*/ function verifyPath( $strPath , $createPath = false )
  {
    $folder_path = strstr($strPath, '.') ? dirname($strPath) : $strPath;

    if ( file_exists($strPath ) || @is_dir( $strPath )) {
      return true;
    }
    else {
      if ( $createPath ) {
        //TODO:: Define Environment constants: Devel (0777), Production (0770), ...
        G::mk_dir ( $strPath , 0777 );
      }
      else
        return false;
    }
    return false;
  }

/**
   * Expand the path using the path constants
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strPath
   * @return string
   */
  function expandPath( $strPath = '' )
  {
    $res = "";
    $res = PATH_CORE;
    if( $strPath != "" )
    {
      $res .= $strPath . "/";
    }
    return $res;
  }

/**
   * Load Gulliver Classes
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strClass
   * @return void
   */
  function LoadSystem( $strClass )
  {
    require_once( PATH_GULLIVER . 'class.' . $strClass . '.php' );
  }

/**
   * Render Page
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  object $objContent
   * @param  string $strTemplate
   * @param  string $strSkin
   * @return void
   */
  function RenderPage( $strTemplate = "default", $strSkin = SYS_SKIN , $objContent = NULL )
  {
    global $G_CONTENT;
    global $G_TEMPLATE;
    $G_CONTENT = $objContent;
    $G_TEMPLATE = $strTemplate;
    try {
      G::LoadSkin( $strSkin );
    }
    catch ( Exception $e ) {
      $aMessage['MESSAGE'] = $e->getMessage();
      global $G_PUBLISH;
      global $G_MAIN_MENU;
      global $G_SUB_MENU;
      $G_MAIN_MENU = '';
      $G_SUB_MENU = '';
      //$G_PUBLISH          = new Publisher;

      //remove the login.js script      
      global $oHeadPublisher;
      if ( count ( $G_PUBLISH->Parts ) == 1 )
        array_shift ( $G_PUBLISH->Parts );
      $leimnudInitString = $oHeadPublisher->leimnudInitString;
      //restart the oHeadPublisher
      $oHeadPublisher->clearScripts();
      //add the missing components, and go on.
      $oHeadPublisher->leimnudInitString = $leimnudInitString;
      $oHeadPublisher->addScriptFile("/js/maborak/core/maborak.js");

      $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', null, $aMessage );
      G::LoadSkin( 'green' );
      die;
    }
  }

/**
   * Load a skin
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strSkinName
   * @return void
   */
  function LoadSkin( $strSkinName )
  {
    //print $strSkinName;
    //now, we are using the skin, a skin is a file in engine/skin directory
    $file = G::ExpandPath( "skins" ) . $strSkinName. ".php";
    if (file_exists ($file) ) {
      require_once( $file );
      return;
    }
    else {
      if (file_exists ( PATH_HTML . 'errors/error703.php') ) {
        header ( 'location: /errors/error703.php' );
        die;
      }
      else   {
        $text = "The Skin $file is not exist, please review the Skin Definition";
        throw ( new Exception ( $text)  );
      }
    }


  }

/**
   * Include javascript files
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strInclude
   * @return void
   */
  function LoadInclude( $strInclude )
  {
    $incfile = G::ExpandPath( "includes" ) . 'inc.' . $strInclude . '.php';
    if ( !file_exists( $incfile )) {
      $incfile = PATH_GULLIVER_HOME . 'includes' . PATH_SEP . 'inc.' . $strInclude . '.php';
    }

    if ( file_exists( $incfile )) {
      require_once( $incfile  );
      return true;
    }
    else {
      return false;
    }
  }

/**
   * Include all model files
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strInclude
   * @return void
   */
  function LoadAllModelClasses( )
  {
    $baseDir = PATH_CORE . 'classes' . PATH_SEP . 'model';
    if ($handle = opendir( $baseDir  )) {
      while ( false !== ($file = readdir($handle))) {
        if ( strpos($file, '.php',1) && !strpos($file, 'Peer.php',1) ) {
          require_once ( $baseDir . PATH_SEP . $file );
        }
      }
    }
  }

/**
   * Include all model plugin files
   *
   * @author Hugo Loza <hugo@colosa.com>
   * @access public
   * @return void
   */
  function LoadAllPluginModelClasses(){
    //Get the current Include path, where the plugins directories should be
  if ( !defined('PATH_SEPARATOR') ) {
    define('PATH_SEPARATOR', ( substr(PHP_OS, 0, 3) == 'WIN' ) ? ';' : ':');
  }
    $path=explode(PATH_SEPARATOR,get_include_path());


    foreach($path as $possiblePath){
      if(strstr($possiblePath,"plugins")){
        $baseDir = $possiblePath . 'classes' . PATH_SEP . 'model';
        if(file_exists($baseDir)){
        if ($handle = opendir( $baseDir  )) {
          while ( false !== ($file = readdir($handle))) {
            if ( strpos($file, '.php',1) && !strpos($file, 'Peer.php',1) ) {
              require_once ( $baseDir . PATH_SEP . $file );
            }
          }
        }
        //Include also the extendGulliverClass that could have some new definitions for fields
        if(file_exists($possiblePath . 'classes' . PATH_SEP.'class.extendGulliver.php')){
          include_once $possiblePath . 'classes' . PATH_SEP.'class.extendGulliver.php';
        }
      }
      }
    }
  }

/**
   * Load a template
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strTemplateName
   * @return void
   */
  function LoadTemplate( $strTemplateName )
  {
    if ( $strTemplateName == '' ) return;
    $temp = $strTemplateName . ".php";
    $file = G::ExpandPath( 'templates' ) . $temp;
    // Check if its a user template
    if ( file_exists($file) ) {
        //require_once( $file );
        include( $file );
    } else {
        // Try to get the global system template
        $file = PATH_TEMPLATE . PATH_SEP . $temp;
        //require_once( $file );
        if ( file_exists($file) )
          include( $file );
    }
  }
  /**
   * Function LoadClassRBAC
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string strClass
   * @return string
   */
  function LoadClassRBAC( $strClass )
  {
    $classfile = PATH_RBAC . "class.$strClass"  . '.php';
    require_once( $classfile );
  }
/**
   * Loads a Class. If the class is not defined by the aplication, it
   * attempt to load the class from gulliver.system
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>, David S. Callizaya
   * @access public
   * @param  string $strClass
   * @return void
   */
  function LoadClass( $strClass )
  {
    $classfile = G::ExpandPath( "classes" ) . 'class.' . $strClass . '.php';
    if (!file_exists( $classfile )) {
      if (file_exists( PATH_GULLIVER . 'class.' . $strClass . '.php' ))
        return require_once( PATH_GULLIVER . 'class.' . $strClass . '.php' );
      else
        return false;
    } else {
      return require_once( $classfile );
    }
  }
/**
   * Loads a Class. If the class is not defined by the aplication, it
   * attempt to load the class from gulliver.system
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>, David S. Callizaya
   * @access public
   * @param  string $strClass
   * @return void
   */
  function LoadThirdParty( $sPath , $sFile )
  {
    $classfile = PATH_THIRDPARTY . $sPath .'/'. $sFile .
      ( (substr($sFile,0,-4)!=='.php')? '.php': '' );
    return require_once( $classfile );
  }

/**
   * Encrypt URL
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $urlLink
   * @return string
   */
  function encryptlink($url)
  {
    if ( defined ( 'ENABLE_ENCRYPT' ) && ENABLE_ENCRYPT == 'yes' )
      return urlencode( G::encrypt( $url ,URL_KEY ) );
    else
      return $url;
  }

/**
   * Parsing the URI
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $urlLink
   * @return string
   */
  function parseURI( $uri )
  {
    $aRequestUri = explode('/', $uri );
    if ( substr ( $aRequestUri[1], 0, 3 ) == 'sys' ) {
      define( 'SYS_TEMP', substr ( $aRequestUri[1], 3 ) );
    }
    else {
      define("ENABLE_ENCRYPT", 'yes' );

      define( 'SYS_TEMP', $aRequestUri[1] );

      $plain = '/sys' . SYS_TEMP;

      for ($i = 2 ; $i < count($aRequestUri); $i++ ) {
        $decoded = G::decrypt ( urldecode($aRequestUri[$i]) , URL_KEY );
        if ( $decoded == 'sWì›' ) $decoded = $VARS[$i]; //this is for the string  "../"
        $plain .= '/' . $decoded;
      }
      $_SERVER["REQUEST_URI"] = $plain;
    }

    $CURRENT_PAGE = $_SERVER["REQUEST_URI"];

    $work = explode('?', $CURRENT_PAGE);
    if ( count($work) > 1 )
      define( 'SYS_CURRENT_PARMS', $work[1]);
    else
      define( 'SYS_CURRENT_PARMS', '');
    define( 'SYS_CURRENT_URI'  , $work[0]);

    if (!defined('SYS_CURRENT_PARMS'))
      define('SYS_CURRENT_PARMS', $work[1]);
    $preArray = explode('&', SYS_CURRENT_PARMS);
    $buffer = explode( '.', $work[0] );
    if ( count($buffer) == 1 ) $buffer[1]='';

    //request type
    define('REQUEST_TYPE', ($buffer[1] != "" ?$buffer[1] : 'html'));

    $toparse  = substr($buffer[0], 1, strlen($buffer[0]) - 1);
    $URL = "";
    $URI_VARS = explode('/', $toparse);
    for ( $i=3; $i < count( $URI_VARS) ; $i++)
      $URL .= $URI_VARS[$i].'/';

    $URI_VARS = explode('/', $toparse);

    unset($work);
    unset($buffer);
    unset($toparse);

    array_shift($URI_VARS);

    define("SYS_LANG", array_shift($URI_VARS));
    define("SYS_SKIN", array_shift($URI_VARS));

    $SYS_COLLECTION = array_shift($URI_VARS);
    $SYS_TARGET     = array_shift($URI_VARS);

    //to enable more than 2 directories...in the methods structure
    $exit = 0;
    while ( count ( $URI_VARS ) > 0 && $exit == 0) {
      $SYS_TARGET .= '/' . array_shift($URI_VARS);
    }
    define('SYS_COLLECTION',   $SYS_COLLECTION    );
    define('SYS_TARGET',       $SYS_TARGET    );

    if ( $SYS_COLLECTION == 'js2' ) {
      print "ERROR"; die;
    }
  }

/**
   * streaming a file
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $file
   * @param  boolean $download
   * @param  string $downloadFileName
   * @return string
   */
  function streamFile( $file, $download = false, $downloadFileName = '' )
  {
    $typearray = explode ( '.', $file );
    $typefile = $typearray[ count($typearray) -1 ];
    $filename = $file;
    if ( file_exists ( $filename ) ) {
      switch ( strtolower ($typefile ) ) {
        case 'swf' :
          G::sendHeaders ( $filename , 'application/x-shockwave-flash', $download, $downloadFileName ); break;
        case 'js' :
          G::sendHeaders ( $filename , 'text/javascript', $download, $downloadFileName ); break;
        case 'htm' :
        case 'html' :
            G::sendHeaders ( $filename , 'text/html', $download, $downloadFileName ); break;
        case 'htc' :
          G::sendHeaders ( $filename , 'text/plain', $download, $downloadFileName ); break;
        case 'json' :
          G::sendHeaders ( $filename , 'text/plain', $download, $downloadFileName ); break;
        case 'gif' :
          G::sendHeaders ( $filename , 'image/gif', $download, $downloadFileName ); break;
        case 'png' :
          G::sendHeaders ( $filename , 'image/png', $download, $downloadFileName ); break;
        case 'jpg' :
          G::sendHeaders ( $filename , 'image/jpg', $download, $downloadFileName ); break;
        case 'css' :
          G::sendHeaders ( $filename , 'text/css', $download, $downloadFileName ); break;
        case 'css' :
          G::sendHeaders ( $filename , 'text/css', $download, $downloadFileName ); break;
        case 'xml' :
          G::sendHeaders ( $filename , 'text/xml', $download, $downloadFileName ); break;
        case 'txt' :
          G::sendHeaders ( $filename , 'text/html', $download, $downloadFileName ); break;
        case 'doc' :
        case 'pdf' :
        case 'pm'  :
        case 'po'  :
          G::sendHeaders ( $filename , 'application/octet-stream', $download, $downloadFileName ); break;
        case 'php' :
          if ($download) {
            G::sendHeaders ( $filename , 'text/plain', $download, $downloadFileName );
          }
          else {
            require_once( $filename  );
            return;
          }
          break;
        default :
          //throw new Exception ( "Unknown type of file '$file'. " );
          G::sendHeaders ( $filename , 'application/octet-stream', $download, $downloadFileName ); break;
          break;
      }
    }
    else {
      throw new Exception ( "file '$file' doesn't exists. " );
    }

    switch ( strtolower($typefile ) ) {
      case "js" :
        $paths = explode ( '/', $filename);
        $jsName = $paths[ count ($paths) -1 ];
        $output = '';
        switch ( $jsName ) {
          case 'maborak.js' :
            $oHeadPublisher =& headPublisher::getSingleton();
            foreach ( $oHeadPublisher->maborakFiles as $fileJS ) {
              $output .= G::trimSourceCodeFile ($fileJS );
            }
          break;
          case 'maborak.loader.js':
            $oHeadPublisher =& headPublisher::getSingleton();
            foreach ( $oHeadPublisher->maborakLoaderFiles as $fileJS ) {
              $output .= G::trimSourceCodeFile ($fileJS );
            }
          break;
        default :
          $output = G::trimSourceCodeFile ($filename );
        }
        print $output;
      break;
      case 'css' :
       print G::trimSourceCodeFile ($filename );
        break;
     default :
        readfile($filename);
    }
  }

  function trimSourceCodeFile ( $filename ) {
    $handle = fopen ($filename, "r");
    $lastChar = '';
    $firstChar = '';
    $content = '';
    $line = '';

//no optimizing code
    if ($handle) {
      while (!feof($handle)) {
        //$line = trim( fgets($handle, 16096) ) . "\n" ;
        $line = fgets($handle, 16096);
        $content .= $line;
      }
      fclose($handle);
    }
    return $content;
//end NO optimizing code
//begin optimizing code
/*
    if ($handle) {
      while (!feof($handle)) {
        $lastChar = ( strlen ( $line ) > 5 ) ? $line[strlen($line)-1] : '';

        $line = trim( fgets($handle, 16096) ) ;
        if ( substr ($line,0,2 ) == '//' )  $line = '';
        $firstChar = ( strlen ( $line ) > 6 ) ? strtolower($line[0]) : '';
        if ( ord( $firstChar ) > 96 && ord($firstChar) < 122 && $lastChar == ';')
          $content .= '';
        else
          $content .= "\n";
//          $content .= '('.$firstChar . $lastChar . ord( $firstChar ).'-'. ord( $lastChar ) . ")\n";

        $content .= $line;
      }
      fclose($handle);
    }
*/
//end optimizing code

    $index = 0;
    $output = '';
    while ( $index < strlen ($content) ) {
      $car = $content[$index];
      $index++;
      if ( $car == '/' && isset($content[$index]) && $content[$index] == '*' ) {
        $endComment = false;
        $index ++;
        while ( $endComment == false && $index < strlen ($content) ) {
          if ($content[$index] == '*' && isset($content[$index+1]) && $content[$index+1] == '/' ) {
            $endComment = true; $index ++;
          }
          $index ++;
        }
        $car = '';
      }
      $output .= $car;
    }
    return $output;
  }

  function sendHeaders ( $filename , $contentType = '', $download = false, $downloadFileName = '' )
  {
    if ($download) {
      if ($downloadFileName == '') {
        $aAux = explode('/', $filename);
        $downloadFileName = $aAux[count($aAux) - 1];
      }
      header('Content-Disposition: attachment; filename="' . $downloadFileName . '"');
    }
    header('Content-Type: ' . $contentType);
    if (!$download) {

      header('Pragma: cache');

      $mtime = filemtime($filename);
      $gmt_mtime = gmdate("D, d M Y H:i:s", $mtime ) . " GMT";
      header('ETag: "' . md5 ($mtime . $filename ) . '"' );
      header("Last-Modified: " . $gmt_mtime );
      header('Cache-Control: public');
      header("Expires: " . gmdate("D, d M Y H:i:s", time () + 90*60*60*24 ) . " GMT");
      if( isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ) {
        if ($_SERVER['HTTP_IF_MODIFIED_SINCE'] == $gmt_mtime) {
           header('HTTP/1.1 304 Not Modified');
           exit();
        }
      }

      if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
        if ( str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == md5( $mtime . $filename))  {
          header("HTTP/1.1 304 Not Modified");
          exit();
        }
      }
    }
  }

/**
 * Transform a public URL into a local path.
 *
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @access public
 * @param  string $url
 * @param  string $corvertionTable
 * @param  string $realPath = local path
 * @return boolean
 */
  function virtualURI( $url , $convertionTable , &$realPath )
  {
    foreach($convertionTable as $urlPattern => $localPath ) {
//      $urlPattern = addcslashes( $urlPattern , '/');
      $urlPattern = addcslashes( $urlPattern , './');
        $urlPattern = '/^' . str_replace(
        array('*','?'),
        array('.*','.?'),
        $urlPattern) . '$/';
      if (preg_match($urlPattern , $url, $match)) {
        if ($localPath === FALSE) {
          $realPath = $url;
          return false;
        }
        if ( $localPath != 'jsMethod' )
          $realPath = $localPath . $match[1];
        else
          $realPath = $localPath;
        return true;
      }
    }
    $realPath = $url;
    return false;
  }


/**
   * Create an encrypted unique identifier based on $id and the selected scope id.
   *
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param  string $scope
   * @param  string $id
   * @return string
   */
  function createUID( $scope, $id )
  {
    $e = $scope . $id;
    $e=G::encrypt( $e , URL_KEY );
    $e=str_replace(array('+','/','='),array('__','_','___'),base64_encode($e));
    return $e;
  }
/**
   * (Create an encrypted unique identificator based on $id and the selected scope id.) ^-1
   *
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @param  string $id
   * @param  string $scope
   * @return string
   */
  function getUIDName( $uid , $scope = '' )
  {
    $e=str_replace(array('=','+','/'),array('___','__','_'),$uid);
    $e=base64_decode($e);
    $e=G::decrypt( $e , URL_KEY );
    $e=substr( $e , strlen($scope) );
    return $e;
  }
  /* formatNumber
   *
   * @author David Callizaya <calidavidx21@yahoo.com.ar>
   * @param  int/string $num
   * @return string number
   */
  function formatNumber($num, $language='latin')
  {
    switch($language)
    {
    default:
      $snum=$num;
    }
    return $snum;
  }
 /* Returns a date formatted according to the given format string
  * @author David Callizaya <calidavidx21@hotmail.com>
  * @param string $format     The format of the outputted date string
  * @param string $datetime   Date in the format YYYY-MM-DD HH:MM:SS
  */
  function formatDate($datetime, $format='Y-m-d', $lang='')
  {
    if ($lang==='') $lang=defined(SYS_LANG)?SYS_LANG:'en';
    $aux          = explode (' ', $datetime);  //para dividir la fecha del dia
    $date         = explode ('-', isset ( $aux[0] ) ? $aux[0] : '00-00-00' );   //para obtener los dias, el mes, y el año.
    $time         = explode (':', isset ( $aux[1] ) ? $aux[1] : '00:00:00' );   //para obtener las horas, minutos, segundos.
    $date[0]=(int)((isset($date[0]))?$date[0]:'0');
    $date[1]=(int)((isset($date[1]))?$date[1]:'0');
    $date[2]=(int)((isset($date[2]))?$date[2]:'0');
    $time[0]=(int)((isset($time[0]))?$time[0]:'0');
    $time[1]=(int)((isset($time[1]))?$time[1]:'0');
    $time[2]=(int)((isset($time[2]))?$time[2]:'0');
    // Spanish months
    $ARR_MONTHS['es'] = array ("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
    // English months
    $ARR_MONTHS['en'] = array("January", "February", "March", "April", "May", "June","July", "August", "September", "October", "November", "December");
    // mouths in persian calendar
    $ARR_MONTHS['fa'] = array('فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند');
    // Spanish days
    $ARR_WEEKDAYS['es'] = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
    // English days
    $ARR_WEEKDAYS['en'] = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
    // Persian days
    $ARR_WEEKDAYS['fa'] = array('یک شنبه','دوشنبه','سه شنبه','چهارشنبه','پنج شنبه','جمعه','شنبه');

    if ($lang=='fa') $number='persian'; else $number='latin';
    $d = '0'.$date[2];$d=G::formatNumber(substr($d,strlen($d)-2,2),$number);
    $j = G::formatNumber($date[2],$number);
    $F = isset ( $ARR_MONTHS[$lang][$date[1]-1] ) ? $ARR_MONTHS[$lang][$date[1]-1] : '';
    $m = '0'.$date[1];$m=G::formatNumber(substr($m,strlen($m)-2,2),$number);
    $n = G::formatNumber($date[1],$number);
    $y = G::formatNumber(substr($date[0],strlen($date[0])-2,2),$number);
    $Y = '0000'.$date[0];$Y=G::formatNumber(substr($Y,strlen($Y)-4,4),$number);
    $g = ($time[0] % 12);if ($g===0)$g=12;
    $G = $time[0];
    $h = '0'.$g;$h=G::formatNumber(substr($h,strlen($h)-2,2),$number);
    $H = '0'.$G;$H=G::formatNumber(substr($H,strlen($H)-2,2),$number);
    $i = '0'.$time[1];$i=G::formatNumber(substr($i,strlen($i)-2,2),$number);
    $s = '0'.$time[2];$s=G::formatNumber(substr($s,strlen($s)-2,2),$number);
    $names=array('d','j','F','m','n','y','Y','g','G','h','H','i','s');
    $values=array($d, $j, $F, $m, $n, $y, $Y, $g, $G, $h, $H, $i, $s);
    $_formatedDate = str_replace( $names, $values, $format );
    return $_formatedDate;
  }

  function getformatedDate($date, $format='yyyy-mm-dd', $lang='')
  {
    /********************************************************************************************************
    * if the year is 2008 and the format is yy  then -> 08
  * if the year is 2008 and the format is yyyy  then -> 2008
  *
  * if the month is 05 and the format is mm  then -> 05
  * if the month is 05 and the format is m and the month is less than 10 then -> 5 else digit normal
  * if the month is 05 and the format is MM or M then -> May
  *
  * if the day is 5 and the format is dd  then -> 05
  * if the day is 5 and the format is d and the day is less than 10 then -> 5 else digit normal
  * if the day is 5 and the format is DD or D then -> five
  *********************************************************************************************************/

    //scape the literal
  switch($lang)
  {
      case 'es':
       $format = str_replace(' de ', '[of]', $format);
    break;
  }

  //first we must formatted the string
    $format = str_replace('yyyy', '{YEAR}', $format);
  $format = str_replace('yy', '{year}', $format);

    $format = str_replace('mm', '{YONTH}', $format);
  $format = str_replace('m', '{month}', $format);
  $format = str_replace('M', '{XONTH}', $format);

    $format = str_replace('dd', '{DAY}', $format);
  $format = str_replace('d', '{day}', $format);



    if ($lang==='') $lang=defined(SYS_LANG)?SYS_LANG:'en';

    $aux  = explode (' ', $date);  //para dividir la fecha del dia
    $date = explode ('-', isset ( $aux[0] ) ? $aux[0] : '00-00-00' );   //para obtener los dias, el mes, y el año.
    $time = explode (':', isset ( $aux[1] ) ? $aux[1] : '00:00:00' );   //para obtener las horas, minutos, segundos.

    $year = (int)((isset($date[0]))?$date[0]:'0'); //year
    $month  = (int)((isset($date[1]))?$date[1]:'0'); //month
    $day  = (int)((isset($date[2]))?$date[2]:'0'); //day

    $time[0]=(int)((isset($time[0]))?$time[0]:'0'); //hour
    $time[1]=(int)((isset($time[1]))?$time[1]:'0'); //minute
    $time[2]=(int)((isset($time[2]))?$time[2]:'0'); //second

  /*witch($lang)
  {
    case 'es':
      // Spanish months
      $MONTHS = array ("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
      // Spanish days
        $WEEKDAYS['es'] = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
      $number='latin';
    break;
    case 'fa':
      // mouths in persian calendar
      $MONTHS = array('فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند');
      // Persian days
        $WEEKDAYS['fa'] = array('یک شنبه','دوشنبه','سه شنبه','چهارشنبه','پنج شنبه','جمعه','شنبه');
      $number='persian';

    break;

    default:
      case 'en':
      // English months
      $MONTHS = array("January", "February", "March", "April", "May", "June","July", "August", "September", "October", "November", "December");
      // English days
        $WEEKDAYS['en'] = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
      $number='latin';
    break;
  }*/
    $MONTHS = Array();
  for($i=1; $i<=12; $i++){
      $MONTHS[$i] =   G::LoadTranslation("ID_MONTH_$i", $lang);
  }

    $d = (int)$day;
  $dd = G::complete_field($day, 2, 1);

  //missing D

    $M = $MONTHS[$month];
  $m = (int)$month;
  $mm = G::complete_field($month, 2, 1);


    $yy = substr($year,strlen($year)-2,2);
  $yyyy = $year;

    $names=array('{day}', '{DAY}', '{month}', '{YONTH}', '{XONTH}', '{year}', '{YEAR}');
    $values=array($d, $dd, $m, $mm, $M, $yy, $yyyy);

    $ret = str_replace( $names, $values, $format );

  //recovering the original literal
  switch($lang)
  {
      case 'es':
       $ret = str_replace('[of]', ' de ', $ret);
    break;
  }

    return $ret;
  }

  /**
  *  By <erik@colosa.com>
  *  Here's a little wrapper for array_diff - I found myself needing
  *  to iterate through the edited array, and I didn't need to original keys for anything.
  */
  function arrayDiff($array1, $array2) {
  // This wrapper for array_diff rekeys the array returned
  $valid_array = array_diff($array1,$array2);

  // reinstantiate $array1 variable
  $array1 = array();

  // loop through the validated array and move elements to $array1
  // this is necessary because the array_diff function returns arrays that retain their original keys
  foreach ($valid_array as $valid){
    $array1[] = $valid;
  }
  return $array1;
  }

  /**
  * @author Erik Amaru Ortiz <erik@colosa.com>
  * @name complete_field($string, $lenght, $type={1:number/2:string/3:float})
  */

  function complete_field($campo, $long, $tipo)
  {
    $campo=trim($campo);
    switch($tipo)
    {
      case 1: //number
        $long = $long-strlen($campo);
        for($i=1; $i<=$long; $i++) {
          $campo = "0".$campo;
        }
      break;

      case 2: //string
        $long = $long-strlen($campo);
        for($i=1; $i<=$long; $i++) {
          $campo = " ".$campo;
        }
      break;

      case 3: //float
        if($campo!="0") {
          $vals = explode(".",$long);
          $ints = $vals[0];

          $decs = $vals[1];

          $valscampo = explode(".",$campo);

          $intscampo = $valscampo[0];
          $decscampo = $valscampo[1];

          $ints = $ints-strlen($intscampo);

          for($i=1; $i<=$ints; $i++) {
            $intscampo = "0".$intscampo;
          }

          //los decimales pueden ser 0 uno o dos
          $decs = $decs-strlen($decscampo);
          for($i=1; $i<=$decs; $i++) {
            $decscampo = $decscampo."0";
          }

          $campo= $intscampo.".".$decscampo;
        } else {
          $vals = explode(".",$long);
          $ints = $vals[0];
          $decs = $vals[1];

          $campo="";
          for($i=1; $i<=$ints; $i++) {
            $campo = "0".$campo;
          }
          $campod="";
          for($i=1; $i<=$decs; $i++) {
            $campod = "0".$campod;
          }

          $campo=$campo.".".$campod;
        }
      break;
    }
    return $campo;
  }

 /* Escapes special characters in a string for use in a SQL statement
  * @author David Callizaya <calidavidx21@hotmail.com>
  * @param string $sqlString  The string to be escaped
  * @param string $DBEngine   Target DBMS
  */
  function sqlEscape( $sqlString, $DBEngine = 'mysql' )
  {
    switch($DBEngine){
      case 'mysql':
        return mysql_real_escape_string(stripslashes($sqlString));
      case 'myxml':
        $sqlString = str_replace('"', '""', $sqlString);
        return str_replace("'", "''", $sqlString);
        //return str_replace(array('"',"'"),array('""',"''"),stripslashes($sqlString));
      default:
        return addslashes(stripslashes($sqlString));
    }
  }
 /* Returns a sql string with @@parameters replaced with its values defined
  * in array $result using the next notation:
  * NOTATION:
  *     @@  Quoted parameter acording to the SYSTEM's Database
  *     @Q  Double quoted parameter \\  \"
  *     @q  Single quoted parameter \\  \'
  *     @%  URL string
  *     @#  Non-quoted parameter
  *     @!  Evaluate string : Replace the parameters in value and then in the sql string
  *     @fn()  Evaluate string with the function "fn"
  * @author David Callizaya <calidavidx21@hotmail.com>
  */
  function replaceDataField( $sqlString, $result, $DBEngine = 'mysql' )
  {
    if (!is_array($result)) {
      $result = array();
    }
    $result = $result + G::getSystemConstants();
    $__textoEval="";$u=0;
    //$count=preg_match_all('/\@(?:([\@\%\#\!Qq])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*(?:[\\\\][\w\W])?)*)\))/',$sqlString,$match,PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
    $count=preg_match_all('/\@(?:([\@\%\#\!Qq])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*?)*)\))/',$sqlString,$match,PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);

    if ($count)
    {
      for($r=0;$r<$count;$r++)
      {
        if (!isset($result[$match[2][$r][0]])) $result[$match[2][$r][0]] = '';
        if (!is_array($result[$match[2][$r][0]])) {
          $__textoEval.=substr($sqlString,$u,$match[0][$r][1]-$u);
          $u=$match[0][$r][1]+strlen($match[0][$r][0]);
          //Mysql quotes scape
          if (($match[1][$r][0]=='@')&&(isset($result[$match[2][$r][0]])))
          {$__textoEval.="\"". G::sqlEscape($result[$match[2][$r][0]],$DBEngine) ."\"";continue;}
          //URL encode
          if (($match[1][$r][0]=='%')&&(isset($result[$match[2][$r][0]])))
          {$__textoEval.=urlencode($result[$match[2][$r][0]]);continue;}
          //Double quoted parameter
          if (($match[1][$r][0]=='Q')&&(isset($result[$match[2][$r][0]])))
          {$__textoEval.='"'.addcslashes($result[$match[2][$r][0]],'\\"').'"';continue;}
          //Single quoted parameter
          if (($match[1][$r][0]=='q')&&(isset($result[$match[2][$r][0]])))
          {$__textoEval.="'".addcslashes($result[$match[2][$r][0]],'\\\'')."'";continue;}
          //Substring (Sub replaceDataField)
          if (($match[1][$r][0]=='!')&&(isset($result[$match[2][$r][0]])))
          {$__textoEval.=G::replaceDataField($result[$match[2][$r][0]],$result);continue;}
          //Call function
          if (($match[1][$r][0]==='')&&($match[2][$r][0]==='')&&($match[3][$r][0]!==''))
          {eval('$__textoEval.='.$match[3][$r][0].'(\''.addcslashes(G::replaceDataField(stripslashes($match[4][$r][0]),$result),'\\\'').'\');');continue;}
          //Non-quoted
          if (($match[1][$r][0]=='#')&&(isset($result[$match[2][$r][0]])))
          {$__textoEval.=G::replaceDataField($result[$match[2][$r][0]],$result);continue;}
        }
      }
    }
    $__textoEval.=substr($sqlString,$u);
    return $__textoEval;
  }

  /* Load strings from a XMLFile.
   * @author David Callizaya <davidsantos@colosa.com>
   * @parameter $languageFile An xml language file.
   * @parameter $languageId   (es|en|...).
   * @parameter $forceParse   Force to read and parse the xml file.
   */
  function loadLanguageFile ( $filename , $languageId = '', $forceParse = false )
  {
    global $arrayXmlMessages;
    if ($languageId==='') $languageId = defined('SYS_LANG') ? SYS_LANG : 'en';
    $languageFile = basename( $filename , '.xml' );
    $cacheFile = substr( $filename , 0 ,-3 ) . $languageId;
    if (($forceParse) || (!file_exists($cacheFile)) ||
        ( filemtime($filename) > filemtime($cacheFile))
        //|| ( filemtime(__FILE__) > filemtime($cacheFile))
        ) {
      $languageDocument = new Xml_document();
      $languageDocument->parseXmlFile( $filename );
      if (!is_array($arrayXmlMessages)) $arrayXmlMessages = array();
      $arrayXmlMessages[ $languageFile ] = array();
      for($r=0 ; $r < sizeof($languageDocument->children[0]->children) ; $r++ ) {
        $n = $languageDocument->children[0]->children[$r]->findNode($languageId);
        if ($n) {
          $k = $languageDocument->children[0]->children[$r]->name;
          $arrayXmlMessages[ $languageFile ][ $k ] = $n->value;
        }
      }
      $f = fopen( $cacheFile , 'w');
        fwrite( $f , "<?\n" );
        fwrite( $f , '$arrayXmlMessages[\'' . $languageFile . '\']=' . 'unserialize(\'' .
          addcslashes( serialize ( $arrayXmlMessages[ $languageFile ] ), '\\\'' ) .
          "');\n");
        fwrite( $f , "?>" );
      fclose( $f );
    } else {
      require( $cacheFile );
    }
  }
  /* Funcion auxiliar Temporal:
   *   Registra en la base de datos los labels xml usados en el sistema
   * @author David Callizaya <calidavidx21@hotmail.com>
   */
  function registerLabel( $id , $label )
  {
     return 1;
    $dbc=new DBConnection();
    $ses=new DBSession($dbc);
    $ses->Execute(G::replaceDataField(
    'REPLACE INTO `TRANSLATION` (`TRN_CATEGORY`, `TRN_ID`, `TRN_LANG`, `TRN_VALUE`) VALUES
("LABEL", @@ID, "'.SYS_LANG.'", @@LABEL);',array('ID'=>$id,'LABEL'=>($label !== null ? $label : ''))));
  }
  /**
   * Function LoadMenuXml
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string msgID
   * @return string
   */
  function LoadMenuXml( $msgID )
  {
    global $arrayXmlMessages;
    if (!isset($arrayXmlMessages['menus']))
      G::loadLanguageFile( G::ExpandPath('content') . 'languages/menus.xml' );
    G::registerLabel($msgID,$arrayXmlMessages['menus'][$msgID]);
    return $arrayXmlMessages['menus'][$msgID];
  }
  /**
   * Function SendMessageXml
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string msgID
   * @parameter string strType
   * @parameter string file
   * @return string
   */
  function SendMessageXml( $msgID, $strType , $file="labels")
  {
    global $arrayXmlMessages;
    if (!isset($arrayXmlMessages[$file]))
      G::loadLanguageFile( G::ExpandPath('content') . 'languages/' . $file . '.xml' );
    $_SESSION['G_MESSAGE_TYPE'] = $strType;
    G::registerLabel($msgID,$arrayXmlMessages[$file][$msgID]);
    $_SESSION['G_MESSAGE'] = nl2br ($arrayXmlMessages[$file][$msgID]);
  }

  function SendTemporalMessage($msgID, $strType)
  {
    $_SESSION['G_MESSAGE_TYPE'] = $strType;
    $_SESSION['G_MESSAGE'] = nl2br(G::LoadTranslation($msgID));
  }

  function SendMessage( $msgID, $strType , $file="labels")
  {
    global $arrayXmlMessages;
    $_SESSION['G_MESSAGE_TYPE'] = $strType;
    $_SESSION['G_MESSAGE'] = nl2br (G::LoadTranslation($msgID));
  }

  //just put the $text in the message text
  function SendMessageText( $text, $strType)
  {
    global $arrayXmlMessages;
    $_SESSION['G_MESSAGE_TYPE'] = $strType;
    $_SESSION['G_MESSAGE'] = nl2br ( $text );
  }

/**
   * Render message from XML file
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $msgID
   * @return void
   */
  function LoadMessage( $msgID, $file = "messages" ) {
    global $_SESSION;
    global $arrayXmlMessages;

    if ( !is_array ($arrayXmlMessages) )
      $arrayXmlMessages = G::LoadArrayFile( G::ExpandPath( 'content' ) . $file . "." . SYS_LANG );

    $aux = $arrayXmlMessages[$msgID];
    $msg = "";
    for ($i = 0; $i < strlen($aux); $i++) {
      if ( $aux[$i] == "$") {
        $token = ""; $i++;
        while ($i < strlen ($aux) && $aux[$i]!=" " && $aux[$i]!="."  && $aux[$i]!="'" && $aux[$i]!='"')
          $token.= $aux[$i++];
        eval ( "\$msg.= \$_SESSION['".$token."'] ; ");
        $msg .= $aux[$i];
      }
      else
        $msg = $msg . $aux[$i];
    }
    return $msg;
  }
  /**
   * Function LoadXmlLabel
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string file
   * @parameter string msgID
   * @return string
   */
  function LoadXmlLabel( $msgID , $file = 'labels' )
  {
    return 'xxxxxx';
    global $arrayXmlMessages;
    if (!isset($arrayXmlMessages[$file]))
      G::loadLanguageFile( G::ExpandPath('content') . 'languages/' . $file . '.xml' );
    G::registerLabel($msgID,$arrayXmlMessages[$file][$msgID]);
    return $arrayXmlMessages[$file][$msgID];
  }
  /**
   * Function LoadMessageXml
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string msgID
   * @parameter string file
   * @return string
   */
  function LoadMessageXml( $msgID , $file ='labels' )
  {
    global $arrayXmlMessages;
    if ( !isset($arrayXmlMessages[$file]) )
      G::loadLanguageFile( G::ExpandPath('content') . 'languages/' . $file . '.xml' );
    if ( isset($arrayXmlMessages[$file][$msgID]) ) {
      G::registerLabel( $msgID, $arrayXmlMessages[$file][$msgID] );
      return $arrayXmlMessages[$file][$msgID];
    }
    else {
      G::registerLabel($msgID,'');
      return NULL;
    }
  }
    /**
   * Function LoadTranslation
   * @author Aldo Mauricio Veliz Valenzuela. <mauricio@colosa.com>
   * @access public
   * @parameter string msgID
   * @parameter string file
   * @return string
   */
  function LoadTranslation( $msgID , $lang = SYS_LANG )
  {
    global $translation;    
    if ( file_exists (PATH_LANGUAGECONT . 'translation.' . $lang) ){
      require_once( PATH_LANGUAGECONT . 'translation.' . $lang );      
    }elseif((!defined("SHOW_UNTRANSLATED_AS_TAG"))||(SHOW_UNTRANSLATED_AS_TAG==0)){
      // --Default English --
      require_once( PATH_LANGUAGECONT . 'translation.' . 'en' );
    }    
    if ( isset ( $translation[$msgID] ) ){      
      return $translation[$msgID];
    }else{
        if(defined("UNTRANSLATED_MARK")){
            $untranslatedMark=strip_tags(UNTRANSLATED_MARK);
        }else{
            $untranslatedMark="**";
        }
    	return $untranslatedMark . $msgID . $untranslatedMark; 
    }
      
  }
  /**
   * Function LoadXml
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string file
   * @parameter string msgID
   * @return string
   */
  function LoadXml( $file, $msgID )
  {return 'XxXxX';
    global $arrayXmlMessages;
    if (!isset($arrayXmlMessages[$file]))
      G::loadLanguageFile( G::ExpandPath('content') . 'languages/' . $file . '.xml' );
    G::registerLabel($msgID,$arrayXmlMessages[$file][$msgID]);
    return $arrayXmlMessages[$file][$msgID];
  }

/**
   * Load an array File Content
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strFile
   * @return void
   */
  function LoadArrayFile( $strFile = '' )
  {
    $res = NULL;
    if ( $strFile != '' )
    {
      $src = file( $strFile );
      if( is_array( $src ) )
      {
        foreach( $src as $key => $val )
  {
    $res[$key] = trim( $val );
  }
      }
    }
    unset( $src );
    return $res;
  }

/**
   * Expand an uri based in the current URI
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $methodPage the method directory and the page
   * @return the expanded uri, later, will encryt the uri...
   */
  function expandUri ( $methodPage ) {
    $uri = explode ( '/', getenv ( 'REQUEST_URI' ) );
    $sw = 0;
    $newUri = '';
    if ( !defined ( 'SYS_SKIN' ) ) {
      for ( $i =0; $i < count( $uri) ; $i++ ) {
        if ( $sw == 0 ) $newUri .= $uri[ $i ] . PATH_SEP ;
        if ( $uri[ $i ] == SYS_SKIN ) $sw = 1;
      }
    }
    else {
      for ( $i =0; $i < 4 ; $i++ ) {
        if ( $sw == 0 ) $newUri .= $uri[ $i ] . PATH_SEP ;
        if ( $uri[ $i ] == SYS_SKIN ) $sw = 1;
      }
    }
    $newUri .= $methodPage;
    return $newUri;
  }

/**
   * Forces login for generic applications
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $userid
   * @param  string $permission
   * @param  string $urlNoAccess
   * @return void
   */
  function genericForceLogin( $permission , $urlNoAccess, $urlLogin = 'login/login' )  {
    global $RBAC;

    //the session is expired, go to login page,
    //the login page is login/login.html
    if ( ! isset ( $_SESSION ) ) {
      header ( 'location: ' . G::expandUri ( $urlLogin ) );
      die ();
    }

    //$permission is an array, we'll verify all permission to allow access.
    if ( is_array($permission) )
      $aux = $permission;
    else
      $aux[0] = $permission;

    $sw = 0;
    for ($i = 0; $i < count ($aux); $i++ ) {
      $res = $RBAC->userCanAccess($aux[$i]);
      if ($res == 1) $sw = 1;
    }

    //you don't have access to this page
    if ($sw == 0) {
      header ( 'location: ' . G::expandUri ( $urlNoAccess ) );
      die;
    }
  }
  function capitalize($string)
  {
    $capitalized = '';
    $singleWords = preg_split( "/\W+/m" , $string );
    for($r=0; $r < sizeof($singleWords) ; $r++ ) {
      $string = substr($string , 0 , $singleWords[$r][1]) .
        strtoupper( substr($singleWords[$r][0], 0,1) ) .
        strtolower( substr($singleWords[$r][0], 1) ) .
        substr( $string , $singleWords[$r][1] + strlen($singleWords[$r][0]) );
    }
    return $string;
  }
  function toUpper($sText)
  {
  return strtoupper($sText);
  }
  function toLower($sText)
  {
  return strtolower($sText);
  }
  function http_build_query( $formdata, $numeric_prefix = null, $key = null )
  {
    $res = array();
    foreach ((array)$formdata as $k=>$v) {
      $tmp_key = rawurlencode(is_int($k) ? $numeric_prefix.$k : $k);
      if ($key) $tmp_key = $key.'['.$tmp_key.']';
      if ( is_array($v) || is_object($v) ) {
         $res[] = G::http_build_query($v, null /* or $numeric_prefix if you want to add numeric_prefix to all indexes in array*/, $tmp_key);
      } else {
         $res[] = $tmp_key."=".rawurlencode($v);
      }
      /*
      If you want, you can write this as one string:
      $res[] = ( ( is_array($v) || is_object($v) ) ? G::http_build_query($v, null, $tmp_key) : $tmp_key."=".urlencode($v) );
      */
    }
    $separator = ini_get('arg_separator.output');
    return implode($separator, $res);
  }
/**
   * Redirect URL
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $parameter
   * @return string
   */
  function header( $parameter ) {
    if ( defined ('ENABLE_ENCRYPT' ) && (ENABLE_ENCRYPT == 'yes') && (substr ( $parameter, 0, 9) == 'location:')) {
        $url = G::encryptUrl ( substr( $parameter, 10) , URL_KEY );
        header ( 'location:' . $url );
    }
    else
      header ( $parameter );
    return ;
  }

/**
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $permission
   * @param  string $urlNoAccess
   * @return void
   */
  function forceLogin( $permission = "", $urlNoAccess = "" )  {
    global $RBAC;

    if ( isset(  $_SESSION['USER_LOGGED'] ) && $_SESSION['USER_LOGGED'] == '' ) {
        $sys        = (ENABLE_ENCRYPT=='yes'?SYS_SYS :"sys".SYS_SYS);
        $lang       = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode(SYS_LANG) , URL_KEY ):SYS_LANG);
        $skin       = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode(SYS_SKIN) , URL_KEY ):SYS_SKIN);
        $login      = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode('login') , URL_KEY ):'login');
        $loginhtml  = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode('login.html') , URL_KEY ):'login.html');
        $direction  = "/$sys/$lang/$skin/$login/$loginhtml";
        die;
        header ("location: $direction");
        die;
      return;
    }

    $Connection = new DBConnection;
    $ses    = new DBSession($Connection);
    $stQry = "SELECT LOG_STATUS FROM LOGIN WHERE LOG_SID = '" . session_id() . "'";
    $dset = $ses->Execute  ( $stQry );
    $row = $dset->read();
    $sessionPc      = defined ( 'SESSION_PC' ) ? SESSION_PC  : '' ;
    $sessionBrowser = defined ( 'SESSION_BROWSER' ) ? SESSION_BROWSER  : '' ;
    if (($sessionPc == "1" ) or ( $sessionBrowser == "1"))
      if($row['LOG_STATUS'] == 'X'){
        $sys        = (ENABLE_ENCRYPT=='yes'?SYS_SYS :"sys".SYS_SYS);
        $lang       = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode(SYS_LANG) , URL_KEY ):SYS_LANG);
        $skin       = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode(SYS_SKIN) , URL_KEY ):SYS_SKIN);
        $login      = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode('login') , URL_KEY ):'login');
        $loginhtml  = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode('login.html') , URL_KEY ):'login.html');
        $direction  = "/$sys/$lang/$skin/$login/$loginhtml";
        G::SendMessageXml ('ID_CLOSE_SESSION', "warning");
        header ("location: $direction");
        die;
      return;
    }

    if ( defined( 'SIN_COMPATIBILIDAD_RBAC')  and SIN_COMPATIBILIDAD_RBAC == 1 )
      return;

    if ( $permission == "" ) {
      return;
    }

    if ( is_array($permission) )
      $aux = $permission;
    else
      $aux[0] = $permission;


    $sw = 0;
    for ($i = 0; $i < count ($aux); $i++ ) {
      $res = $RBAC->userCanAccess($aux[$i]);
      if ($res == 1) $sw = 1;
      //print " $aux[$i]  $res $sw <br>";
    }

    if ($sw == 0 && $urlNoAccess != "") {
      $aux = explode ( '/', $urlNoAccess );
  $sys      = (ENABLE_ENCRYPT=='yes'?SYS_SYS :"/sys".SYS_LANG);
  $lang       = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode(SYS_LANG) , URL_KEY ):SYS_LANG);
    $skin       = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode(SYS_SKIN) , URL_KEY ):SYS_SKIN);
    $login      = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode($aux[0]) , URL_KEY ):$aux[0]);
    $loginhtml  = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode($aux[1]) , URL_KEY ):$aux[1]);
      //header ("location: /$sys/$lang/$skin/$login/$loginhtml");
      header ("location: /fluid/mNE/o9A/mNGm1aLiop3V4qU/dtij4J°gmaLPwKDU3qNn2qXanw");
      die;
    }


    if ($sw == 0) {
      header ("location: /fluid/mNE/o9A/mNGm1aLiop3V4qU/dtij4J°gmaLPwKDU3qNn2qXanw");
      //header ( "location: /sys/" . SYS_LANG . "/" . SYS_SKIN . "/login/noViewPage.html" );
      die;
    }
  }
/**
   * Add slashes to a string
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $val_old
   * @return string
   */
  function add_slashes($val_old) {

      if (!is_string ($val_old)) $val_old ="$val_old";

    $tamano_cadena = strlen ($val_old);
  $contador_cadena = 0;
    $new_val ="";

    for ($contador_cadena=0; $contador_cadena< $tamano_cadena; $contador_cadena ++)
    {
        $car  = $val_old[$contador_cadena];

      if ( $car != chr(34) && $car != chr(39) && $car != chr(92))
          {
              $new_val .= $car;
          }
      else
      {
        if ($car2 != chr (92) )
        {
              //print " xmlvar: $new_val -- $car -- $car2 <br>";
                          $new_val .= chr(92) . $car;
                }
                else
                    $new_val .= $car;
      }
    }
    return $new_val;
  }
/**
   * Upload a file and then copy to path+ nameToSave
   *
   * @author Mauricio Veliz <mauricio@colosa.com>
   * @access public
   * @param  string $file
   * @param  string $path
   * @param  string $nameToSave
   * @param  integer $permission
   * @return void
   */
  function uploadFile($file, $path ,$nameToSave, $permission=0666) {
    try {
      if ($file == '') {
        throw new Exception('The filename is empty!');
      }
      if (filesize($file) > ((((ini_get('upload_max_filesize') + 0)) * 1024) * 1024)) {
        throw new Exception('The size of upload file exceeds the allowed by the server!');
      }
      $oldumask = umask(0);
      if (!is_dir($path)) {
        G::verifyPath($path, true);
      }
      move_uploaded_file($file , $path . "/" . $nameToSave);
      chmod($path . "/" . $nameToSave , $permission);
      umask($oldumask);
    }
    catch (Exception $oException) {
      throw $oException;
    }
  }

  function resizeImage($path, $resWidth, $resHeight, $saveTo=null) {
    try {
      list($width, $height) = getimagesize($path);
      $percentHeight        = $resHeight / $height;
      $percentWidth         = $resWidth / $width;
      $percent              = ($percentWidth < $percentHeight) ? $percentWidth : $percentHeight;
      $resWidth             = $width * $percent;
      $resHeight            = $height * $percent;

      // Resample
      $image_p = imagecreatetruecolor($resWidth, $resHeight);
      if (strcasecmp(substr(strtolower($path),-4),'.jpg')===0) $image = imagecreatefromjpeg($path);
      if (strcasecmp(substr(strtolower($path),-5),'.jpeg')===0)$image = imagecreatefromjpeg($path);
      if (strcasecmp(substr(strtolower($path),-4),'.png')===0) $image = imagecreatefrompng($path);
      if (strcasecmp(substr(strtolower($path),-4),'.gif')===0) $image = imagecreatefromgif($path);
      imagecopyresampled($image_p, $image, 0, 0, 0, 0, $resWidth, $resHeight, $width, $height);

      // Output
      imagejpeg($image_p, $saveTo, 100);
      chmod($saveTo, 0666);
    }
    catch (Exception $oException) {
      throw $oException;
    }
  }

  /**
   * Merge 2 arrays
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @return array
   */
   function array_merges() {
       $array = array();
       $arrays =& func_get_args();
       foreach ($arrays as $array_i) {
           if (is_array($array_i)) {
               G::array_merge_2($array, $array_i);
           }
       }
       return $array;
   }

  /**
   * Merge 2 arrays
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $array
   * @param  string $array_i
   * @return array
   */
   function array_merge_2(&$array, &$array_i) {
       foreach ($array_i as $k => $v) {
           if (is_array($v)) {
               if (!isset($array[$k])) {
                   $array[$k] = array();
               }
               G::array_merge_2($array[$k], $v);
           } else {
               if (isset($array[$k]) && is_array($array[$k])) {
                   $array[$k][0] = $v;
               } else {
                   if (isset($array) && !is_array($array)) {
                       $temp = $array;
                       $array = array();
                       $array[0] = $temp;
                   }
                   $array[$k] = $v;
               }
           }
       }
   }

/**
   * Generate random number
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @return int
   */
  function generateUniqueID() {
    do {
      $sUID = str_replace('.', '0', uniqid(rand(0, 999999999), true));
    } while (strlen($sUID) != 32);
    return $sUID;
    //return strtoupper(substr(uniqid(rand(0, 9), false),0,14));
  }


  /**
   * Generate a numeric or alphanumeric code
   *
   * @author Julio Cesar Laura Avenda𭞼juliocesar@colosa.com>
   * @access public
   * @return string
   */
  function generateCode($iDigits = 4, $sType = 'NUMERIC') {
    if (($iDigits < 4) || ($iDigits > 50)) {
      $iDigits = 4;
    }
    if (($sType != 'NUMERIC') && ($sType != 'ALPHA') && ($sType != 'ALPHANUMERIC')) {
      $sType = 'NUMERIC';
    }
    $aValidCharacters = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
                              'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J',
                              'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
                              'U', 'V', 'W', 'X', 'Y', 'Z');
    switch ($sType) {
      case 'NUMERIC':
        $iMin = 0;
        $iMax = 9;
      break;
      case 'ALPHA':
        $iMin = 10;
        $iMax = 35;
      break;
      case 'ALPHANUMERIC':
        $iMin = 0;
        $iMax = 35;
      break;
    }
    $sCode = '';
    for ($i = 0; $i < $iDigits; $i++) {
      $sCode .= $aValidCharacters[rand($iMin, $iMax)];
    }
    return $sCode;
  }

/**
   * Verify if the input string is a valid UID
   *
   * @author David Callizaya <davidsantos@colosa.com>
   * @access public
   * @return int
   */
  function verifyUniqueID( $uid ) {
  return (bool) preg_match('/^[0-9A-Za-z]{14,}/',$uid);
  }

  function is_utf8($string)
  {
    if (is_array($string))
    {
      $enc = implode('', $string);
      return @!((ord($enc[0]) != 239) && (ord($enc[1]) != 187) && (ord($enc[2]) != 191));
    }
    else
    {
      return (utf8_encode(utf8_decode($string)) == $string);
    }
  }


  /**
   * Return date in Y-m-d format
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @return void
   */
  function CurDate($sFormat = '')
  {
    $sFormat = ( $sFormat != '' )? $sFormat: 'Y-m-d H:i:s';
    return date($sFormat);
  }

  /*
   * Return the System defined constants and Application variables
   *   Constants: SYS_*
   *   Sessions : USER_* , URS_*
   */
  function getSystemConstants()
  {
    $t1 = G::microtime_float();
    $sysCon = array();
    if ( defined('SYS_LANG' )) $sysCon['SYS_LANG'] = SYS_LANG;
    if ( defined('SYS_SKIN' )) $sysCon['SYS_SKIN'] = SYS_SKIN;
    if ( defined('SYS_SYS' ) ) $sysCon['SYS_SYS']  = SYS_SYS;

    if (isset($_SESSION['APPLICATION']) ) $sysCon['APPLICATION'] = $_SESSION['APPLICATION'];
    if (isset($_SESSION['PROCESS'])     ) $sysCon['PROCESS']     = $_SESSION['PROCESS'];
    if (isset($_SESSION['TASK'])        ) $sysCon['TASK']        = $_SESSION['TASK'];
    if (isset($_SESSION['INDEX'])       ) $sysCon['INDEX']       = $_SESSION['INDEX'];
    if (isset($_SESSION['USER_LOGGED']) ) $sysCon['USER_LOGGED'] = $_SESSION['USER_LOGGED'];
    if (isset($_SESSION['USR_USERNAME'])) $sysCon['USR_USERNAME']= $_SESSION['USR_USERNAME'];

    return $sysCon;
  }


/*
   * Return the Friendly Title for a string, capitalize every word and remove spaces
   *   param : text string
   */
  function capitalizeWords( $text )
  {
    /*$result = '';
    $space = true;
    for ( $i = 0; $i < strlen ( $text); $i++ ) {
      $car = strtolower ( $text[$i] );
      if ( strpos( "abcdefghijklmnopqrstuvwxyz1234567890", $car ) !== false ) {
        if ($space ) $car = strtoupper ( $car );
        $result .= $car;
        $space  = false;
      }
      else
        $space = true;
    }
    return $result;*/
    if (function_exists('mb_detect_encoding')) {
      if (strtoupper(mb_detect_encoding($text)) == 'UTF-8') {
        $text = utf8_encode($text);
      }
    }
    if(function_exists('mb_ucwords')) {
      return mb_ucwords($text);
    }
    else {
      return mb_convert_case($text, MB_CASE_TITLE, "UTF-8");
    }
  }

  function unhtmlentities ($string)
  {
    $trans_tbl = get_html_translation_table (HTML_ENTITIES);
    foreach($trans_tbl as $k => $v)
    {
      $ttr[$v] = utf8_encode($k);
    }
    return strtr ($string, $ttr);
  }
  
  	/*************************************** init **********************************************
	* Xml parse collection functions
	* Returns a associative array within the xml structure and data
	* 
	* @Author Erik Amaru Ortiz <erik@colosa.com>
	* @Date   Aug 24th, 2009
	*/
	function xmlParser(&$string) {
	    $parser = xml_parser_create();
	    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	    xml_parse_into_struct($parser, $string, $vals, $index);
	
	    $mnary=array();
	    $ary=&$mnary;
	    foreach ($vals as $r) {
	        $t=$r['tag'];
	        if ($r['type']=='open') {
	            if (isset($ary[$t])) {
	                if (isset($ary[$t][0])) $ary[$t][]=array();
	                else $ary[$t]=array($ary[$t], array());
	                $cv=&$ary[$t][count($ary[$t])-1];
	            } else $cv=&$ary[$t];
	            if (isset($r['attributes'])) {
	            	foreach ($r['attributes'] as $k=>$v) $cv['__ATTRIBUTES__'][$k]=$v;
	            }
	            $cv['__CONTENT__']=array();
	            $cv['__CONTENT__']['_p']=&$ary;
	            $ary=&$cv['__CONTENT__'];
	
	        } elseif ($r['type']=='complete') {
	            if (isset($ary[$t])) { // same as open
	                if (isset($ary[$t][0])) $ary[$t][]=array();
	                else $ary[$t]=array($ary[$t], array());
	                $cv=&$ary[$t][count($ary[$t])-1];
	            } else $cv=&$ary[$t];
	            if (isset($r['attributes'])) {
	            	foreach ($r['attributes'] as $k=>$v) $cv['__ATTRIBUTES__'][$k]=$v;
	            }
	            $cv['__VALUE__']=(isset($r['value']) ? $r['value'] : '');
	
	        } elseif ($r['type']=='close') {
	            $ary=&$ary['_p'];
	        }
	    }
	
	    self::_del_p($mnary);
	
		$obj_resp->code = xml_get_error_code($parser);
		$obj_resp->message = xml_error_string($obj_resp->code);
		$obj_resp->result = $mnary;
		xml_parser_free($parser);
	
	    return $obj_resp;
	}
	
	// _Internal: Remove recursion in result array
	function _del_p(&$ary) {
	    foreach ($ary as $k=>$v) {
	        if ($k==='_p') unset($ary[$k]);
	        elseif (is_array($ary[$k])) self::_del_p($ary[$k]);
	    }
	}
	
	// Array to XML
	function ary2xml($cary, $d=0, $forcetag='') {
	    $res=array();
	    foreach ($cary as $tag=>$r) {
	        if (isset($r[0])) {
	            $res[]=self::ary2xml($r, $d, $tag);
	        } else {
	            if ($forcetag) $tag=$forcetag;
	            $sp=str_repeat("\t", $d);
	            $res[]="$sp<$tag";
	            if (isset($r['_a'])) {foreach ($r['_a'] as $at=>$av) $res[]=" $at=\"$av\"";}
	            $res[]=">".((isset($r['_c'])) ? "\n" : '');
	            if (isset($r['_c'])) $res[]=ary2xml($r['_c'], $d+1);
	            elseif (isset($r['_v'])) $res[]=$r['_v'];
	            $res[]=(isset($r['_c']) ? $sp : '')."</$tag>\n";
	        }
	
	    }
	    return implode('', $res);
	}
	
	// Insert element into array
	function ins2ary(&$ary, $element, $pos) {
	    $ar1=array_slice($ary, 0, $pos); $ar1[]=$element;
	    $ary=array_merge($ar1, array_slice($ary, $pos));
	}
	
	/*
	* Xml parse collection functions
	*************************************** end **********************************************/

	
	function evalJScript($c){
		print("<script languaje=\"javascript\">{$c}</script>");
	}
};



/*
 ****  **    ***        ***  *   * *    *    *  *   *  **** ***
**  ** **    ** *      *     *   * *    *    *  *   *  *    *  *
**  ** **    **  *     *  ** *   * *    *    *  *   *  **** ***
**  ** **    **  *     *   * *   * *    *    *   * *   *    *  *
 ****  ***** *****      ***   ***  **** **** *    *    **** *  *

******************************************************************
*/



/**
 * G class definition
 * @package home.gulliver.system2
 * @author Fernando Ontiveros Lira <fernando@colosa.com>
 * @copyright (C) 2002 by Colosa Development Team.
 *
 */
class oldG
{



/**
   * Prepare SESSION vars to be renderized
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $arrContent
   * @param  integer $useGrid
   * @return void
   */
  function PrepareFormArray( $arrContent, $useGrid = 1 )
  {

    $res = "";
    if ( is_array ( $arrContent ) )
    foreach( $arrContent as $key=>$val )
    {
      if( is_array($val) )
      {
        //formato del grid, es decir variables separadas por comas
        if( is_array ( current( $val) ) && $useGrid ) {
          $rows = count ($val);
          $cols = count ( current($val));
          $aux = "";
          for ($j = 1; $j <= $rows; $j++)
            for ($i = 0; $i < $cols; $i++)
              $aux .= $val[$j][$i] . "|";
            $res[$key] = $aux;
        }
        else //otros casos especiales y  Formularios Multiples
        {
          //modified by Onti, sep 25th,2004   //<------ this is for mask fields
          $aux = "";
          $cant = count($val); $c=0;
          foreach ( $val as $k=>$v ) {
            if ( is_array ($v ) )  {
              $c++; $aux .= "<$k>";

              foreach ( $v as $k2=>$v2 )
                if ( is_array ($v2 ) )  {
                  $aux .= "<$k2>";

                  if( $v2['YEAR'] != "" ) //DATE NORMAL
                    $aux .= $v2['YEAR'] . "-" .  $v2['MONTH'] . "-" . $v2['DAY'];
                  else {
                    foreach ( $v2 as $k3=>$v3 )
                      if (is_array($v3)) {
                        $aux .= "<$k3>";
                        foreach ( $v3 as $k4=>$v4 )
                          if (is_array($v4)) {
                            $aux .= "<$k4>";
                            foreach ( $v5 as $k5=>$v5 )
                              $aux .= "<$k5>$v5</$k5>";
                            $aux .= "</$k4>";
                          }
                          else
                            $aux .= "<$k4>$v4</$k4>";
                        $aux .= "</$k3>";
                      }
                      else
                        $aux .= "<$k3>$v3</$k3>";
                  }
                  $aux .= "</$k2>";
                }
                else
                  $aux .= "<$k2>$v2</$k2>";
               //end foreach $v

              $aux .= "</$k>";
            }
            else {
              $aux .= "<$k>$v</$k>";
            }
          }
          $res[$key] = $aux;

          //si son caso especificos de fecha, etc.
//              $res[$key] = "" . $val['YEAR'] . "-" .  $val['MONTH'] . "-" . $val['DAY'];
//            $res[$key] = "" . $val['YEAR'] . "-" .  $val['MONTH'] . "-" . $val['DAY'] . " " . $val['HOUR'] . ":" . $val['MINUTES'] . ":" . $val['SECONDS']  ;
//              $res[$key] = "" . $val['DO'] . "-" .  $val['SEC'] . "-" . $val['YEAR']  ;
//              $res[$key] = "" . $val['TIME'] . "-" .  $val['UNIT']  ;
          //si son caso especificos de fecha, etc.
          if( isset( $val['YEAR'] ) && $val['YEAR'] != "" ) //DATE NORMAL
              $res[$key] = "" . $val['YEAR'] . "-" .  $val['MONTH'] . "-" . $val['DAY'];
          if( isset( $val['HOUR'] ) && $val['HOUR'] != "" ) //DATE CON TIME
            $res[$key] = "" . $val['YEAR'] . "-" .  $val['MONTH'] . "-" . $val['DAY'] . " " . $val['HOUR'] . ":" . $val['MINUTES'] . ":" . $val['SECONDS']  ;
          if( isset( $val['DO'] ) && $val['DO']  != "" ) //D&O Certificate Number
              $res[$key] = "" . $val['DO'] . "-" .  $val['SEC'] . "-" . $val['YEAR']  ;
          if( isset( $val['TIME'] ) &&  $val['TIME'] != "" ) //Duration time
              $res[$key] = "" . $val['TIME'] . "-" .  $val['UNIT']  ;
        }
      }//if is_array($val)
      else
      {
        //$res[$key] = htmlentities(str_replace("'", "°", $val));
        $val = urlencode($val);
        $val = htmlentities(addslashes($val));
        $val = urldecode($val);
        $res[$key] = $val;
      }
    }
    return $res;
  }

/**
   * Copy array values into a string
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $arrTarget
   * @param  string $arrSource
   * @return void
   */
  function CopyArrayValues( &$arrTarget, $arrSource )
  {
    if( is_array( $arrSource ) )
    {
      foreach( $arrSource as $key=>$val )
      {
        $arrTarget[$key] = $val;
      }
    }
  }

/**
   * It is change language according to REQUEST_URI
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strLang
   * @return void
   */
  function ChangeLang( $strLang = "es" )
  {
    $work = getenv( "REQUEST_URI" );
    $srchLang = "/" . SYS_LANG . "/";
    $arrURI = explode( $srchLang, $work );
    $res = $arrURI[0] . "/" . $strLang . "/" . $arrURI[1];
    print($res);
  }


/**
   *  Convert all applicable characters to HTML entities
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $string
   * @return void
   */
  // For users prior to PHP 4.3.0 you may do this TO decode htmlentities:
  function unhtmlentities ($string)
  {
    $trans_tbl = get_html_translation_table (HTML_ENTITIES);
    foreach($trans_tbl as $k => $v)
    {
      $ttr[$v] = utf8_encode($k);
    }
    //Modified by julichu
    //$trans_tbl = array_flip ($trans_tbl);
    //return strtr ($string, $trans_tbl);
    return strtr ($string, $ttr);
    //Removed by Onti to unfix utf8 decode
    //Added by JHL to fix the utf8 errors 17-11-06
    //return utf8_decode(strtr ($string, $trans_tbl));
  }

/**
   * Set currency format (add $ symbol at end)
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $number
   * @param  string $decimalPlaces
   * @param  string $number
   * @return void
   */
  function NumberToCurrency ($number , $decimalPlaces = 2, $symbol = '$ ') {
    $number = trim(str_replace('$', '', $number));
    $number = preg_replace("/(?:\\b[0-9]+(?:\\.[0-9]*)?|\\.[0-9]+\\b)(?:[eE][-+]?[0-9]+\\b)/e", "number_format(('\\0'),20,'.','')", $number);
    return $symbol . number_format ( $number,$decimalPlaces );
  }

/**
   * Convert number to percentage (add % symbol at end)
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $number
   * @param  string $decimalPlaces
   * @return void
   */
  function NumberToPercentage ($number, $decimalPlaces = 2) {
    $number = preg_replace("/(?:\\b[0-9]+(?:\\.[0-9]*)?|\\.[0-9]+\\b)(?:[eE][-+]?[0-9]+\\b)/e", "number_format(('\\0'),20,'.','')", $number);
    return number_format ( $number,$decimalPlaces ) . " %";
  }

/**
   * Convert currency to number (delete $ symbol)
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $currency
   * @return void
   */
  function CurrencyToNumber ($currency) {
    $number = ereg_replace("[^0123456789.-]","",$currency);
    if ($number == '' ) $number = 0;
    return $number;
  }

/**
   * Convert percentage to number (delete % symbol)
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $percentage
   * @return void
   */
  function PercentageToNumber ($percentage) {
    $number = ereg_replace("[^0123456789.-]","",$percentage);
    return $number;
  }

/**
   * Set currency format
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $frmData
   * @param  string $frmFields
   * @return void
   */
  function FormatCurrency( $frmData, $frmFields )
  // created 24.06: jtm
  // param: $frmData is user-entered
  //        $frmFields is used to query the Type of each form field
  {
    $fields = $frmFields->Fields;
    if ( is_array( $frmData ) && is_array( $fields ) )
    {
      foreach ( $fields as $value )
      {
        $type = $value['Type'];
  $name = $value['Name'];
  $data = $frmData[$name];
  if ( $type == 'currency' )
  {
      $pos = strpos ($data, ".");
      if ( is_integer($pos) )        // if there's a decimal place,
    $data = substr ($data, 0, $pos);  // cut off any decimals
      $frmData[$name] = ereg_replace("[^0-9]","",$data);  // strip all non-digits
  }
      }
    }
    return $frmData;
  }

/**
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $permission
   * @param  string $urlNoAccess
   * @return void
   */
  function ForceLogin( $permission = "", $urlNoAccess = "" )  {
    global $HTTP_SESSION_VARS;
    global $RBAC;


    if ( $HTTP_SESSION_VARS['USER_LOGGED'] == "" ) {
        $sys        = (ENABLE_ENCRYPT=='yes'?SYS_SYS :"sys".SYS_SYS);
        $lang       = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode(SYS_LANG) , URL_KEY ):SYS_LANG);
        $skin       = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode(SYS_SKIN) , URL_KEY ):SYS_SKIN);
        $login      = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode('login') , URL_KEY ):'login');
        $loginhtml  = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode('login.html') , URL_KEY ):'login.html');
        $direction  = "/$sys/$lang/$skin/$login/$loginhtml";
        header ("location: $direction");
        die;
      return;
    }
    $Connection = new DBConnection;
    $ses    = new DBSession($Connection);
  $stQry = "SELECT LOG_STATUS FROM LOGIN WHERE LOG_SID = '" . session_id() . "'";
    $dset = $ses->Execute  ( $stQry );
    $row = $dset->read();
    $sessionPc      = defined ( 'SESSION_PC' ) ? SESSION_PC  : '' ;
    $sessionBrowser = defined ( 'SESSION_BROWSER' ) ? SESSION_BROWSER  : '' ;
    if (($sessionPc == "1" ) or ( $sessionBrowser == "1"))
      if($row['LOG_STATUS'] == 'X'){
        $sys        = (ENABLE_ENCRYPT=='yes'?SYS_SYS :"sys".SYS_SYS);
        $lang       = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode(SYS_LANG) , URL_KEY ):SYS_LANG);
        $skin       = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode(SYS_SKIN) , URL_KEY ):SYS_SKIN);
        $login      = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode('login') , URL_KEY ):'login');
        $loginhtml  = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode('login.html') , URL_KEY ):'login.html');
        $direction  = "/$sys/$lang/$skin/$login/$loginhtml";
        G::SendMessageXml ('ID_CLOSE_SESSION', "warning");
        header ("location: $direction");
        die;
      return;
    }
    if ( defined( 'SIN_COMPATIBILIDAD_RBAC')  and SIN_COMPATIBILIDAD_RBAC == 1 )
      return;

    if ( $permission == "" ) {
      return;
    }

    if ( is_array($permission) )
      $aux = $permission;
    else
      $aux[0] = $permission;


    $sw = 0;
    for ($i = 0; $i < count ($aux); $i++ ) {
      $res = $RBAC->userCanAccess($aux[$i]);
      if ($res == 1) $sw = 1;
      //print " $aux[$i]  $res $sw <br>";
    }


    if ($sw == 0 && $urlNoAccess != "") {
      $aux = explode ( '/', $urlNoAccess );
  $sys      = (ENABLE_ENCRYPT=='yes'?SYS_SYS :"/sys".SYS_LANG);
  $lang       = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode(SYS_LANG) , URL_KEY ):SYS_LANG);
    $skin       = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode(SYS_SKIN) , URL_KEY ):SYS_SKIN);
    $login      = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode($aux[0]) , URL_KEY ):$aux[0]);
    $loginhtml  = (ENABLE_ENCRYPT=='yes'?G::encrypt ( urldecode($aux[1]) , URL_KEY ):$aux[1]);
      //header ("location: /$sys/$lang/$skin/$login/$loginhtml");
      header ("location: /fluid/mNE/o9A/mNGm1aLiop3V4qU/dtij4J°gmaLPwKDU3qNn2qXanw");
      die;
    }


    if ($sw == 0) {
      header ("location: /fluid/mNE/o9A/mNGm1aLiop3V4qU/dtij4J°gmaLPwKDU3qNn2qXanw");
      //header ( "location: /sys/" . SYS_LANG . "/" . SYS_SKIN . "/login/noViewPage.html" );
      die;
    }
  }

/**
   * Expand an uri based in the current URI
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $methodPage the method directory and the page
   * @return the expanded uri, later, will encryt the uri...
   */
  function expandUri ( $methodPage ) {
    $uri = explode ( '/', getenv ( 'REQUEST_URI' ) );
    $sw = 0;
    $newUri = '';
    if ( !defined ( 'SYS_SKIN' ) ) {
      for ( $i =0; $i < count( $uri) ; $i++ ) {
        if ( $sw == 0 ) $newUri .= $uri[ $i ] . PATH_SEP ;
        if ( $uri[ $i ] == SYS_SKIN ) $sw = 1;
      }
    }
    else {
      for ( $i =0; $i < 4 ; $i++ ) {
        if ( $sw == 0 ) $newUri .= $uri[ $i ] . PATH_SEP ;
        if ( $uri[ $i ] == SYS_SKIN ) $sw = 1;
      }
    }
    $newUri .= $methodPage;
    return $newUri;
  }

/**
   * Forces login for generic applications
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $userid
   * @param  string $permission
   * @param  string $urlNoAccess
   * @return void
   */
  function genericForceLogin( $permission , $urlNoAccess, $urlLogin = 'login/login' )  {
    global $RBAC;

    //the session is expired, go to login page,
    //the login page is login/login.html

    if ( ! isset ( $_SESSION ) ) {
      header ( 'location: ' . G::expandUri ( $urlLogin ) );
      die ();
    }

    //$permission is an array, we'll verify all permission to allow access.
    if ( is_array($permission) )
      $aux = $permission;
    else
      $aux[0] = $permission;

    $sw = 0;

    for ($i = 0; $i < count ($aux); $i++ ) {
      $res = $RBAC->userCanAccess($aux[$i]);
      if ($res == 1) $sw = 1;
    }

    //you don't have access to this page
    if ($sw == 0) {
      header ( 'location: ' . G::expandUri ( $urlNoAccess ) );
      die;
    }
 }


/**
   * Add slashes to a string
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $val_old
   * @return string
   */
  function add_slashes($val_old) {

      if (!is_string ($val_old)) $val_old ="$val_old";

    $tamano_cadena = strlen ($val_old);
  $contador_cadena = 0;
    $new_val ="";

    for ($contador_cadena=0; $contador_cadena< $tamano_cadena; $contador_cadena ++)
    {
        $car  = $val_old[$contador_cadena];

      if ( $car != chr(34) && $car != chr(39) && $car != chr(92))
          {
              $new_val .= $car;
          }
      else
      {
        if ($car2 != chr (92) )
        {
              //print " xmlvar: $new_val -- $car -- $car2 <br>";
                          $new_val .= chr(92) . $car;
                }
                else
                    $new_val .= $car;
      }
    }
    return $new_val;
  }



/**
   * Encrypt Url
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $string
   * @param  string $key
   * @param  boolean $debug defaulkt value = false
   * @return string
   */
  function encryptUrl($string, $key, $debug = false) {

    $aux = explode ( '/', $string );
    //print_r($aux);
    $encrypted = '';
    for ($i = 0; $i < count($aux) ; $i++ ) {
      if ( $aux[$i] == '..' )
        $encrypted .= '..';
      else
        $encrypted .= urlencode(G::encrypt ( $aux[$i] , $key )) ;
      if ( $i != count($aux) -1 ) $encrypted .= '/';
    }
    if ( $debug )
      print "url $string = $encrypted";
   return $encrypted;
  }

/**
   * Encrypt Url
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $string
   * @param  string $key
   * @param  boolean $debug
   * @return string
   */
  function encryptUrlAbsolute ($string, $key, $debug = false) {
    $pos1 = strpos( strtolower ( $string) , 'javascript' );

    if ($pos1 !== false) {  //is a javascript... no encrypt the url
      $encrypted = $string;
    }
    else {
      $aux = explode ( '/', $string );
      $encrypted = '/' . $aux[1] . '/';
      for ($i = 2; $i < count($aux) ; $i++ ) {
        if ( $aux[$i] == '..' )
          $encrypted .= '..';
        else
          $encrypted .= urlencode(G::encrypt ( $aux[$i] , $key )) ;
        if ( $i != count($aux) -1 ) $encrypted .= '/';
      }
    }
//   if ( $debug )
//     print "[abs]"; // $string = $encrypted";
   return $encrypted;
  }

/**
   * Redirect URL
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $parameter
   * @return string
   */
  function header( $parameter ) {
    if ( defined ('ENABLE_ENCRYPT' ) && (ENABLE_ENCRYPT == 'yes') && (substr ( $parameter, 0, 9) == 'location:')) {
        $url = G::encryptUrl ( substr( $parameter, 10) , URL_KEY );
        header ( 'location:' . $url );
    }
    else
      header ( $parameter );
    return ;
  }

/**
   * Redirect URL using PHP header function
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $parameter
   * @return void
   */
  function headerLogin( $parameter) {
    if ((ENABLE_ENCRYPT == 'yes') && (substr ( $parameter, 0, 9) == 'location:')) {
        $url = G::encryptUrl ( substr( $parameter, 10) , URL_KEY );
        header ( 'location:' . $url );
    }
    else
      header ( $parameter );
    return ;
  }

/**
   * Redirect URL using javascript location function
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $parameter
   * @return void
   */
  function redirectParent( $parameter ) {
    $url = $parameter;
    if ((ENABLE_ENCRYPT == 'yes') && (substr ( $parameter, 0, 9) == 'location:')) {
        $url = G::encryptUrl ( substr( $parameter, 10) , URL_KEY );
    }
    print $url;
    print "<script language=\"JavaScript\" >\n";
//    print " alert ( parent.window.location) ; ";
    print "  parent.window.location = \"$url\"; \n";
    print "</script>\n";

    return ;
  }



/**
   * Replace @@ symbol with blanks
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $text
   * @param  string $fields
   * @return void
   */
  /*******functions for xmlform template ***/
  function replace_values ( $text, $fields ) {
    if ( is_array ($fields) )
      foreach ( $fields as $key=>$val ) $text = str_replace ( '@@' . $key, $val, $text );
    return $text;
  }


/**
   * Copy whole directory
   *
   * @author Mauricio Veliz <mauricio@colosa.com>
   * @access public
   * @param  string $file
   * @param  string $path
   * @param  string $nameToSave
   * @param  string $permission
   * @return int
   */
  function copydirr($fromDir,$toDir,$chmod=0777,$verbose=true,$overwrite=1)
  {
    $errors=array();
    $messages=array();

    if(!is_dir($toDir)) G::verifyPath($toDir);

    if (!is_writable($toDir))
       $errors[]='target '.$toDir.' is not writable';
    if (!is_dir($toDir))
       $errors[]='target '.$toDir.' is not a directory';
    if (!is_dir($fromDir))
       $errors[]='source '.$fromDir.' is not a directory';
    if (!empty($errors))
       {
       if ($verbose)
           foreach($errors as $err)
               echo '<strong>Error</strong>: '.$err.'<br />';
       return false;
       }

    $exceptions=array('.','..');

    $handle=opendir($fromDir);
    while (false!==($item=readdir($handle)))
       if (!in_array($item,$exceptions))
           {
           //* limpiando slashes en los directorios de destino
           $from=str_replace('//','/',$fromDir.'/'.$item);
           $to=str_replace('//','/',$toDir.'/'.$item);
           //*/
           if (is_file($from))
               {
               if ($overwrite==0){
                 if (@copy($from,$to))
                     {
                     chmod($to,$chmod);
                     touch($to,filemtime($from));
                     $messages[]='File copied from '.$from.' to '.$to;
                     }
                 else
                     $errors[]='cannot copy file from '.$from.' to '.$to;
                 }
                 else
                 {
                  if(!is_file($to))
                  {
                   if (@copy($from,$to))
                       {
                       chmod($to,$chmod);
                       touch($to,filemtime($from));
                       $messages[]='File copied from '.$from.' to '.$to;
                       }
                   else
                       $errors[]='cannot copy file from '.$from.' to '.$to;
                  }
                 }
                }
           if (is_dir($from))
             {
               if ($overwrite==0){
                 if (@mkdir($to))
                     {
                     chmod($to,$chmod);
                     $messages[]='Directory created: '.$to;
                     }
                 else
                     $errors[]='cannot create directory '.$to;
                 G::copydirr($from,$to,$chmod,$verbose);
                }else{
                  if(!is_dir($to)){
                     if (@mkdir($to))
                         {
                         chmod($to,$chmod);
                         $messages[]='Directory created: '.$to;
                         }
                     else
                         $errors[]='cannot create directory '.$to;
                     G::copydirr($from,$to,$chmod,$verbose);
                  }
                }
             }
           }
    closedir($handle);

    if ($verbose)
       {
       foreach($errors as $err)
           echo '<strong>Error</strong>: '.$err.'<br />';
       foreach($messages as $msg)
           echo $msg.'<br />';
       }

    return true;
  }


/**
   * Copy a file to path.nameToSave
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $file
   * @param  string $path
   * @param  string $nameToSave
   * @param  string $permission = '0777'
   * @return int
   */
  function copyFile($file, $path ,$nameToSave, $permission='0777'){
    if(!is_dir($path)) G::verifyPath($path);
    copy( $file , $path . "/" . $nameToSave );
    chmod( $path . "/" . $nameToSave , 0666 );
    return 1;
  }
}
