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

/**
 * @package home.gulliver.system2
*/


class G
{
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

  /*public static*/ function &setErrorHandler( $newCustomErrorHandler = null )
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
    if ( defined ( 'ENABLE_ENCRYPT' ) && ENABLE_ENCRYPT == 'yes' ) {
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
    }
    else
      $result = $string;

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

   if ( defined ( 'ENABLE_ENCRYPT' ) && ENABLE_ENCRYPT == 'yes' ) {

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
   }
   else
     $result = $string;
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
    $folder_path = array(
      strstr($strPath, '.') ? dirname($strPath) : $strPath);

  $oldumask = umask(0);
  while(!@is_dir(dirname(end($folder_path)))
            && dirname(end($folder_path)) != '/'
            && dirname(end($folder_path)) != '.'
            && dirname(end($folder_path)) != '')
      array_push($folder_path, dirname(end($folder_path)));
    while($parent_folder_path = array_pop($folder_path))
      if(!@mkdir($parent_folder_path, $rights))
        //trigger_error ("Can't create folder \"$parent_folder_path\".", E_USER_WARNING);

    umask($oldumask);
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
    G::LoadSkin( $strSkin );

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
        trigger_error ( $text , E_USER_WARNING);
        die;
      }
    }

// quitar todo este codigo
/*
    $aux = explode ('-', $strSkinName);
    $skinName = $aux[0];
    $tmp = $skinName . ".php";
    $styleFile = PATH_HTML . 'skins/styles/' . $aux[0] . '/style.css';
    $file = G::ExpandPath( "skins" ) . $tmp;

    //when we are using a normal skin
    if (file_exists ($file) ) {
      print $file;
      require_once( $file );
      return;
    }
    //if the file exists in styles directory, we are using the styles.php file
    //when we are using a style with the generic skin styles.php
    if (file_exists ( $styleFile) ) {
      define ( 'STYLE_CSS', $skinName );
      $file = G::ExpandPath( "skins" ) . 'styles.php';
      require_once( $file );
      return;
    }

    if (file_exists ( PATH_HTML . 'errors/error703.php') ) {
      header ( 'location: /errors/error703.php' );
      die;
    }
    else   {
      $text = "The Skin $file is not exist, please review the Skin Definition";
      trigger_error ( $text , E_USER_WARNING);
      die;
    }
*/
// quitar hasta aqui.

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
      switch ( $typefile ) {
        case 'js' :
          G::sendHeaders ( $filename , 'text/javascript', $download, $downloadFileName ); break;
        case 'htm' :
          G::sendHeaders ( $filename , 'text/html', $download, $downloadFileName ); break;
        case 'html' :
          G::sendHeaders ( $filename , 'text/html', $download, $downloadFileName ); break;
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
          G::sendHeaders ( $filename , 'application/octet-stream', $download, $downloadFileName ); break;
        case 'pdf' :
          G::sendHeaders ( $filename , 'application/octet-stream', $download, $downloadFileName ); break;
        case 'php' :
          require_once( $filename  );
          return;
          break;
        default :
            print "missing $typefile type.";
            trigger_error ( "Unknown type of file '$file'. " , E_USER_ERROR);
            die;
      }
    }
    else {
      trigger_error ( "file '$file' doesn't exists. " , E_USER_ERROR);
      die;
    }

    readfile($filename);
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
        return str_replace(array('"',"'"),array('""',"''"),stripslashes($sqlString));
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
    $count=preg_match_all('/\@(?:([\@\%\#\!Qq])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*(?:[\\\\][\w\W])?)*)\))/',$sqlString,$match,PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);

    if ($count)
    {
      for($r=0;$r<$count;$r++)
      {
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
//        trigger_error('Warning: '.$match[1][$r][0].' parameter not found.', E_USER_NOTICE);
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
    $_SESSION['G_MESSAGE'] = nl2br (G::LoadMessage($msgID));
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
   * Function LoadMessageXml
   * @author Aldo Mauricio Veliz Valenzuela. <mauricio@colosa.com>
   * @access public
   * @parameter string msgID
   * @parameter string file
   * @return string
   */
  function LoadTranslation( $msgID , $lang = SYS_LANG )
  {
    global $translation;
    if ( file_exists (PATH_LANGUAGECONT . 'translation.' . $lang) )
      require_once( PATH_LANGUAGECONT . 'translation.' . $lang );
    if ( isset ( $translation[$msgID] ) )
      return $translation[$msgID];
    else
      return '**' . $msgID . '**';

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
    //return uniqid(rand(), true);
    return strtoupper(substr(uniqid(rand(0, 9), false),0,14));
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
  function CurDate()
  {
    return date( 'Y-m-d H:i:s' );

  }

  /*
   * Return the System defined constants and Application variables
   *   Constants: SYS_*
   *   Sessions : USER_* , URS_*
   */
  function getSystemConstants()
  {
    $sysCon = array();
    $constants=get_defined_constants();
    foreach( $constants as $cons => $value )
    {
      if ( substr($cons,0,4)==='SYS_' ) $sysCon[$cons] = $value;
    }
    $_SESSION = isset($_SESSION) ? $_SESSION : Array();
    foreach( $_SESSION as $name => $value )
    {
      if ( substr($name,0,4)==='USER_' ) $sysCon[$name] = $value;
      if ( substr($name,0,4)==='USR_' ) $sysCon[$name] = $value;
    }

    return $sysCon;
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
   * Function logError
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string text
   * @return string
   */
  function logError( $text )
  {
  if (getenv('HTTP_CLIENT_IP')) {
    $ip = getenv('HTTP_CLIENT_IP');
  }
  elseif(getenv('HTTP_X_FORWARDED_FOR')) {
    $ip = getenv('HTTP_X_FORWARDED_FOR');
  }
  else {
    $ip = getenv('REMOTE_ADDR');
  }

  $pathData = ( defined ( PATH_DATA ) ? PATH_DATA : '/tmp/') ;

  $f =fopen ( PATH_DATA . 'gulliver-error.log', "a+" );
  fwrite ( $f, date("Y-m-d h:i:s") . " $ip $text\n" );
  fclose ($f);
}

/**
   * Load a form
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strForm
   * @return object
   */
  function &LoadForm( $strForm )
  {
    G::LoadSystem( "form" );
    global $G_FORM;
    $G_FORM = NULL;
    $G_FORM = new Form;
    require_once( G::ExpandPath( "forms" ) . $strForm . ".php" );
    return $G_FORM;
  }

/**
   * Loads a table
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strTable
   * @param  object $objConn
   * @param  string $strWhere
   * @param  string $strTarget
   * @return object
   */
  function &LoadTable( $strTable, $objConn, $strWhere, $strTarget )
  {
    global $G_TMP_TARGET;
    global $G_TMP_TABLE;
    $G_TMP_TARGET = $strTarget;
    $G_TMP_TABLE = NULL;
    $G_TMP_TABLE = new Table;
    $G_TMP_TABLE->SetTo( $objConn );
    require_once( G::ExpandPath( "tables" ) . $strTable . ".php" );
    if( $strWhere != "" )
    {
      if( $G_TMP_TABLE->WhereClause == "" )
      {
        $G_TMP_TABLE->WhereClause = $strWhere;
      }
      else
      {
        $G_TMP_TABLE->WhereClause .= " AND " . $strWhere;
      }
    }
    $G_TMP_TABLE->orderprefix = "";
    return $G_TMP_TABLE;
  }

/**
   * Load a table
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strTable
   * @param  object $objConnection
   * @param  array $arrData
   * @return object
   */
  function &LoadRawTable( $strTable, $objConnection, $arrData = NULL )
  {
    global $G_TMP_TABLE;
    $G_TMP_TABLE = new Table;
    $G_TMP_TABLE->SetTo( $objConnection );
    include( G::ExpandPath( "tables" ) . $strTable . ".php" );
    if( isset ( $arrData['Where'] ) )
    {
      if( $G_TMP_TABLE->WhereClause == "" )
      {
        $G_TMP_TABLE->WhereClause = $arrData['Where'];
      }
      else
      {
        $G_TMP_TABLE->WhereClause .= " AND " . $arrData['Where'];
      }
    }
    $G_TMP_TABLE->orderprefix = isset( $arrData['Prefix'] ) ? $arrData['Prefix'] : '';
    return $G_TMP_TABLE;
  }
/**
   * Load a content
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $stQry
   * @param  string $sqlconnection
   * @return array
   */
  function LoadOptions( $stQry , $sqlconnection = '' )
  {

    $stQry = str_replace("''''", "''", $stQry);

    global $dbc;
    $result = NULL;
    if ( is_null($dbc))
      $dbc = new DBConnection;
      //return;

    //si tiene conexion a diferentes Data Sources...
    if ( $sqlconnection != '' ) {

      $dsesConn = new DBSession;
      $dsesConn->SetTo( $dbc );
      $dsesConn->UseDB( DB_NAME );
      //verificar que existe la tabla
      $dsetConn = $dsesConn->Execute ( "SHOW TABLES LIKE 'DB_CONNECTION' " );
      $row = $dsetConn->Read();
      if ( is_array ( $row ) ) {
        $dsetConn = $dsesConn->Execute ( "SELECT * FROM DB_CONNECTION WHERE DBC_UID = $sqlconnection " );
        $row = $dsetConn->Read();

        //catch the error in this dbconnection call, see the last parameter = 3
        if(!is_array($row)) return array('ERROR');
        $dbcSQL = new DBConnection ($row['DBC_SERVER'], $row['DBC_USERNAME'], $row['DBC_PASSWORD'],$row['DBC_DATABASE'], $row['DBC_TYPE'], $row['DBC_PORT'] , 3) ;
        $dsesSQL = new DBSession ( $dbcSQL );

        //****By JHL
        $squery = explode("WHERE",$stQry);
        $found = ereg ( "@@[a-zA-Z0-9_]+", $squery[1], $token);
        if($found)
          $stQry=$squery[0];
        //die;

        $dsetSQL = $dsesSQL->Execute ($stQry, false, 3 );
        $data = $dsetSQL->ReadAbsolute();
        while( $data )
        {
          $key = $data[0];
          $val = $data[1];
          if ( count($data) > 1 )
            $result[$key] = $val;
          else
            $result[ 0] = $key;

          $data = $dsetSQL->ReadAbsolute();
        }
        return $result;
      }
    }

    //cuando la conexion es normal... se intenta realizar el query
    $dses = new DBSession;
    $dses->SetTo( $dbc );
    $dses->UseDB( DB_NAME );
    $dset = $dses->Execute($stQry, false, 3 );
    $data = $dset->ReadAbsolute();

    while( $data )
    {
      $key = $data[0];
      $val = $data[1];
      $result[$key] = $val;
      $data = $dset->ReadAbsolute();
    }
    return $result;
  }

  /* Load a content
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strFile
   * @param  string $strScollection
   * @param  string $strTarget
   * @return string
   */
   /*
  function ExpandURI( $strFile = '', $strScollection = '', $strTarget = '' )
  {
    $res = '/sys';
    if ( $strCollection != '' ) $res .= '/' . $strCollection;
    if ( $strTarget != '' ) $res .= '/' . $strTarget;
    if ( $strFile != '' ) $res .= '/'. $strFile;
    $res .= '.html';
    return $res;
  }
  */

/**
   * Load a content
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strFile
   * @return array
   */
  function LoadFile( $strFile = '' )
  {
    $res = '';
    if ( $strFile != '' )
    {
      $file = fopen( $strFile , 'r');
      $res  = fread( $file , filesize( $strFile ) );
      fclose( $file );
    }
    return $res;
  }

/**
   * Load a content
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strFile
   * @return object
   */
  function ReadFile( $strFile )
  {
    return G::LoadFile( $strFile );
  }

/**
   * Load a content
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strFile
   * @param  string $strContent
   * @return void
   */
  function SaveFile( $strFile = '', $strContent = '' )
  {
    if ( $strFile != '' )
    {
      $file = fopen( $strFile, 'w' );
      fwrite( $file, $strContent );
      fclose( $file );
    }
  }

/**
   * Loads a WDDX content
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strFile
   * @return object
   */
  function LoadWDDX( $strFile = '' )
  {
    $content = G::LoadFile( $strFile );
    return wddx_deserialize( $content );
  }

/**
   * Saves a WDDX content
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strFile
   * @param  string $uValue
   * @return void
   */
  function SaveWDDX( $strFile = '', $uValue = '' )
  {
    $content = wddx_serialize_Value( $uValue );
    G::SaveFile( $strFile, $content );
  }

/**
   * Loads a Class
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strClass
   * @return void
   */
  function LoadClass( $strClass )
  {
    $classfile = G::ExpandPath( "classes" ) . 'class.' . $strClass . '.php';
    require_once( $classfile );
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
    $classfile = PATH_CORE . "classes2/class.$strClass"  . '.php';
    require_once( $classfile );
  }

/**
   * Load an external template
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strTemplateName
   * @return void
   */
  function LoadTemplateExternal( $strTemplateName )
  {
    if ( $strTemplateName == '' ) return;
    $temp = $strTemplateName . ".php";
    $file = PATH_DATA . $temp;

    // Check if its a user template
    if ( file_exists($file) ) {
        //require_once( $file );
        include( $file );
    } else {
        // Try to get the global system template
        $file = PATH_TEMPLATE . PATH_SEP . $temp;
        //require_once( $file );
        include( $file );
    }
  }

/**
   * Load a TPL template
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strTemplateName
   * @param  string $arrFields
   * @return void
   */
  function LoadTemplateTPL( $strTemplateName, $arrFields )
  {
    if ( $strTemplateName == '' ) return;
    $temp = $strTemplateName . ".html";
    $file = G::ExpandPath( 'templates' ) . $temp;
    $fileName = '';
    // Check if its a user template
    if ( file_exists($file) ) {
        $fileName = $file;
    } else {
        // Try to get the global system template
        $file = PATH_TEMPLATE . PATH_SEP . $temp;
        //require_once( $file );
        if ( file_exists($file) )
        $fileName = $file;
    }

    if ( $fileName != '' ) {
      G::LoadSystem ( 'template' );
      $template = new Template();
      $template->set_filenames(array('body' => $fileName ) );

      if ( is_array ( $arrFields ) ) {
        foreach ( $arrFields as $key => $val ) //1st level
          if ( is_array ( $val ) ) {
            foreach ( $val as $key_2 => $val_2 ) {
              //remove arrays for this "iteration" and keep them in $nestedArrays
              $nestedArray = array();
              if ( is_array ( $val_2 ) )
                foreach ( $val_2 as $key_3 => $val_3 ) {
                  $nestedArrayTwo = array();
                  if ( is_array ( $val_3 ) ) {
                    foreach ( $val_3 as $key_4 => $val_4 ) {
                      if ( is_array($val_4) ) {
                        foreach ( $val_4 as $key_5 => $val_5 ) {
                          //print_r ($val_5);
                        }
                      }
                    }
                    $nestedArray[$key_3] = $val_3;
                    unset ( $val_2[$key_3] );
                  } //if ( is_array ( $val_3 ) )
                }

              //add the block for items without nested arrays
              $template->assign_block_vars( $key, $val_2 );

              foreach( $nestedArray as $nest_key => $nest_val ) {
                foreach ( $nest_val as $nest_key2 => $nest_array ) {
                  $template->assign_block_vars( $key . '.' . $nest_key, $nest_array );
                }
              }
            }
          }
          else
            $template->assign_vars( array ( $key => $val ) );
       }
       $template->pparse('body' );
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
    $aux = explode ('-', $strSkinName);
    $skinName = $aux[0];
    $tmp = $skinName . ".php";
    $styleFile = PATH_HTML . 'skins/styles/' . $aux[0] . '/Style.css';
    $file = G::ExpandPath( "skins" );
    $file .= $tmp;

    //when we are using a simple skin
    if (file_exists ($file) ) {
      require_once( $file );
      return;
    }

    //when we are using a style with the generic skin styles.php
    if (file_exists ( $styleFile) ) {
      define ( 'STYLE_CSS', $skinName );
      $file = G::ExpandPath( "skins" ) . 'styles.php';
      require_once( $file );
      return;
    }

    if (file_exists ( PATH_HTML . 'errors/error703.php') ) {
      header ( 'location: /errors/error703.php' );
      die;
    }
    else   {
      print "The Skin <b>$file</b> is not exist, please review the Skin Definition<br>";
      print "<a href='JavaScript:close();' >Close Window</a>";
      die;
    }


  }

/**
   * Loads a content
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strLayoutID
   * @return void
   */
  function SkinLayout( $strLayoutID = "a" )
  {
    $tmp = $strLayoutID . ".php";
    $file = G::ExpandPath( "layouts" );
    $file .= $tmp;
    require_once( $file );
  }

/**
   * Load a Menu Content
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strMenuName
   * @return void
   */
  function &LoadMenu( $strMenuName = "" )
  {
    global $G_TMP_MENU;
    $G_TMP_MENU = new Menu;
    $tmp = $strMenuName . ".php";
    $file = G::ExpandPath( 'menus' );
    $file .= $tmp;
    print $file;
    require( $file );
    return( $G_TMP_MENU );
  }

/**
   * Render default menu
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $strTemplateName
   * @return void
   */
  function RenderMenu( $strTemplateName = "vmenu" )
  {
    $tmp = $strTemplateName . ".php";
    $file = G::ExpandPath( 'templates' );
    $file .= $tmp;
    require( $file );
  }

/**
   * Render Help File
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  object $objContent
   * @param  string $strTemplate
   * @param  string $strSkin
   * @return void
   */
  function RenderHelp( $objContent = NULL, $strTemplate = "default", $strSkin = SYS_SKIN )
  {
    global $G_CONTENT;
    $G_CONTENT = $objContent;
    G::LoadSkin( "help" );
  }


/**
   * Stablish header to text/plain
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @return void
   */
  function PlainText()
  {
    header( "content-type: text/plain" );
  }

/**
   * Return date in Y-m-d format
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @return void
   */
  function CurDate()
  {
    return date( 'Y-m-d H:i:s' );

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
   * Load a XML file
   *
   * @author Mauricio Veliz <mauricio@colosa.com>
   * @access public
   * @param  string $msgID
   * @return void
   */
  function LoadXml( $file, $msgID) {
    global $HTTP_SESSION_VARS;
    global $arrayXmlMessages;

    //if ( !is_array ($arrayXmlMessages) ) {
    //$HTTP_SESSION_VARS['LABELS_XML'] = array();
    $filename = G::ExpandPath( 'content' ) . "languages/$file." . SYS_LANG;

    G::loadSystem( "labels" );
    $lab = new LabelsXml;
    $lab->parseFile( "$file", "arrayXmlMessages");

    require_once ( $filename );

    if ( defined ( 'SYS_SYS' )  ) {

        $filenameEnvDir = G::ExpandPath( 'content' ) . "languages/" . SYS_SYS . "/";
        $filenameEnv = $filenameEnvDir . "$file." . SYS_LANG;
        $filenameXml = $filenameEnvDir . "$file.xml";
        $continue = true;
    //byOnti 2/28/2007 disable the automatic-creation of local languages files for each workspace
/*
        if( !file_exists( $filenameEnvDir ) )
        {
          $continue = mkdir( $filenameEnvDir, 0770 );
          chmod( $filenameEnvDir, 0770 );
        }

        if ( $continue && !file_exists( $filenameXml ) ) {
          $f = fopen( $filenameXml, "w" );
          fwrite ( $f, "<?xml version=\"1.0\"?>\n<dynaForm name=\"$file.xml\" >\n</dynaForm>" );
          fclose ($f);
          chmod( $filenameXml, 0660 );
        }
*/
        if ( $continue && file_exists( $filenameXml ))
        {
          $lab = new LabelsXml;
          $lab->parseFile( SYS_SYS."/$file", "arrayXmlMessages");
          require_once ( $filenameEnv );
        }
      }
    //}


    //disable by Onti. this LABELS_XML was for info purposes only, none programs had rely on it.
    //$HTTP_SESSION_VARS['LABELS_XML'][] = $msgID;

    //anadir variable de session como parte del mensaje.
    $aux = '';
    if ( ! isset ( $arrayXmlMessages[ $msgID ] ) )
      G::logError ( "The $msgID is not defined in the languages files.");
    else
      $aux = $arrayXmlMessages[$msgID];

    $msg = "";
    for ($i = 0; $i < strlen($aux); $i++) {
      if ( $aux[$i] == "$") {
        $token = ""; $i++;
        while ($i < strlen ($aux) && $aux[$i]!=" " && $aux[$i]!="."  && $aux[$i]!="'" && $aux[$i]!='"')
          $token.= $aux[$i++];
        eval ( "\$msg.= \$HTTP_SESSION_VARS['".$token."'] ; ");
        $msg .= $aux[$i];
      }
      else
        $msg = $msg . $aux[$i];
    }
    return stripslashes($msg);
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
    global $HTTP_SESSION_VARS;
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
        eval ( "\$msg.= \$HTTP_SESSION_VARS['".$token."'] ; ");
        $msg .= $aux[$i];
      }
      else
        $msg = $msg . $aux[$i];
    }
    return $msg;
  }

/**
   * Render message from XML file
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $msgID
   * @return void
   */
  function LoadMessageXml( $msgID ) {
    global $HTTP_SESSION_VARS;
    global $arrayXmlMessages;
    if ( !is_array ( $arrayXmlMessages) ) {
      $HTTP_SESSION_VARS['LABELS_XML'] = array();
      $filename = G::ExpandPath( 'content' ) . "languages/labels." . SYS_LANG;

      G::loadSystem( "labels" );
      $lab = new LabelsXml;
      $lab->parseFile( "labels", "arrayXmlMessages");

      require_once ( $filename );
      //Del. by Onti 24/01/2007
      //if the SYS_SYS variable is defined, search the file in the directory languages/SYS_SYS
      //this issue is deactivated, until think in a better directory
      /*
      if ( defined ( 'SYS_SYS' )  ) {
        $filenameEnvDir = G::ExpandPath( 'content' ) . "languages/" . SYS_SYS . "/";
        $filenameEnv = $filenameEnvDir . "labels." . SYS_LANG;
        $filenameXml = $filenameEnvDir . "labels.xml";
        $continue = true;
        if( !file_exists( $filenameEnvDir ) )
        {
          $continue = mkdir( $filenameEnvDir, 0770 );
          chmod( $filenameEnvDir, 0770 );
        }

        if( $continue && !file_exists( $filenameXml ) )
        {
          $f = fopen( $filenameXml, "w" );
          fwrite ( $f, "<?xml version=\"1.0\"?>\n<dynaForm name=\"labels.xml\" >\n</dynaForm>" );
          fclose ($f);
          chmod( $filenameXml, 0660 );
        }

        if( $continue && file_exists( $filenameXml ))
        {
          $lab = new LabelsXml;
          $lab->parseFile( SYS_SYS."/labels", "arrayXmlMessages");
          require_once ( $filenameEnv );
        }
      }
      */ //del by Onti.
    }
    //disable by Onti. this LABELS_XML was for info purposes only, none programs had rely on it.
    //$HTTP_SESSION_VARS['LABELS_XML'][] = $msgID;

    //anadir variable de session como parte del mensaje.
    $aux = '';
    if ( ! isset ( $arrayXmlMessages[ $msgID ] ) )
      G::logError ( "The $msgID is not defined in the languages files.");
    else
      $aux = $arrayXmlMessages[$msgID];

    $msg = "";
    for ($i = 0; $i < strlen($aux); $i++) {
      if ( $aux[$i] == "$") {
        $token = ""; $i++;
        while ($i < strlen ($aux) && $aux[$i]!=" " && $aux[$i]!="."  && $aux[$i]!="'" && $aux[$i]!='"')
          $token.= $aux[$i++];
        //print "\$msg.= \$HTTP_SESSION_VARS['".$token."'] ; ";
        eval ( "\$msg.= \$HTTP_SESSION_VARS['".$token."'] ; ");
        $msg .= $aux[$i];
      }
      else
        $msg = $msg . $aux[$i];
    }
    return $msg;
  }

/**
   * Load Menu content from XML file
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $msgID
   * @return void
   */
  function LoadMenuXml( $msgID ) {
    global $HTTP_SESSION_VARS;
    global $arrayMenus;

    if ( !is_array ($arrayMenus) ) {
      $filename = G::ExpandPath( 'content' ) . "languages/menus." . SYS_LANG;

      G::loadSystem( "labels" );
      $lab = new LabelsXml;
      $lab->parseFile( "menus", "arrayMenus");

      require_once ( $filename );

      if ( defined ( 'SYS_SYS' )  ) {
        $filenameEnvDir = G::ExpandPath( 'content' ) . "languages/" . SYS_SYS . "/";
        $filenameEnv = $filenameEnvDir . "menus." . SYS_LANG;
        $filenameXml = $filenameEnvDir . "menus.xml";
        $continue = true;
    //byOnti 2/28/2007 disable the automatic-creation of local languages files for each workspace
/*
        if( !file_exists( $filenameEnvDir ) )
        {
          $continue = mkdir( $filenameEnvDir, 0770 );
          chmod( $filenameEnvDir, 0770 );
        }

        if( $continue && !file_exists( $filenameXml ) )
        {
          $f = fopen( $filenameXml, "w" );
          fwrite ( $f, "<?xml version=\"1.0\"?>\n<dynaForm name=\"menus.xml\" >\n</dynaForm>" );
          fclose ($f);
          chmod( $filenameXml, 0660 );
        }
*/
        if( $continue && file_exists( $filenameXml ))
        {
          $lab = new LabelsXml;
          $lab->parseFile( SYS_SYS."/menus", "arrayMenus");
          require_once ( $filenameEnv );
        }
      }


    }

    //in HTTP SESSION VARS, you can be able to pass some parameters to be included in the menu.
    $aux = $arrayMenus[$msgID];
    $msg = "";
    for ($i = 0; $i < strlen($aux); $i++) {
      if ( $aux[$i] == "$") {
        $token = ""; $i++;
        while ($i < strlen ($aux) && $aux[$i]!=" " && $aux[$i]!="."  && $aux[$i]!="'" && $aux[$i]!='"')
          $token.= $aux[$i++];
        eval ( "\$msg.= \$HTTP_SESSION_VARS['".$token."'] ; ");
        $msg .= $aux[$i];
      }
      else
        $msg = $msg . $aux[$i];
    }
    return $msg;
  }

/**
   * Set SESSION message
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $msgID
   * @param  string $strType
   * @param  string $msg
   * @return void
   */
  function SendMessage( $msgD, $strType, $msg)
  {
    global $HTTP_SESSION_VARS;
    global $arrayXmlMessages;

    if ($msg == '')
      $msg = G::LoadMessage($msgID);

    $HTTP_SESSION_VARS['G_MESSAGE_TYPE'] = $strType;
    $HTTP_SESSION_VARS['G_MESSAGE'] = nl2br ($msg);
  }

/**
   * Send SESSION message from xml file
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $msgID
   * @param  string $strType
   * @return void
   */
  function SendMessageXml( $msgID, $strType ,$file="labels")
  {
    global $HTTP_SESSION_VARS;
    global $arrayXmlMessages;

    $msg = G::LoadXml($file, $msgID);
    $HTTP_SESSION_VARS['G_MESSAGE_TYPE'] = $strType;
    $HTTP_SESSION_VARS['G_MESSAGE'] = nl2br ($msg);
  }


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
   * Redirect URL using javascript location function
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $location
   * @param  string $parameter
   * @return void
   */
  //similar a header2, toma 2 parametros
  function redirectParent2( $location, $parameter ) {
    $link = (substr ( $location, 0, 9) == 'location:' ? substr ( $location, 10 ) : $location ) ;
    if ((ENABLE_ENCRYPT == 'yes') ) {
        $url = G::encryptUrl ( $link , URL_KEY ). '/' . $parameter;;
    }
    else
      $url = $link . '/' . $parameter;
    print "<script language=\"JavaScript\" >\n";
    print "  parent.window.location = \"$url\"; \n";
    print "</script>\n";

    return ;
  }

/**
   * Redirect URL using PHP header function
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $location
   * @param  string $parameter
   * @return void
   */
  function header2($location, $parameters) {
    if ((ENABLE_ENCRYPT == 'yes') && (substr($location, 0, 9) == 'location:')) {
        $url = G::encryptUrl (substr( $location, 10), URL_KEY);
        header('location:' . $url . '/' . $parameters);
    }
    else {
      header($location . '/' . $parameters);
    }
    return ;
  }

/**
   * Redirect URL using PHP header function
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $location
   * @param  string $parameter
   * @return void
   */
  function header3($location, $parameters) {
    if ((ENABLE_ENCRYPT == 'yes') && (substr ($location, 0, 9) == 'location:')) {
        $location = trim(substr( $location, 10));
        $url = G::encryptUrl ($location, URL_KEY);
        header('location:' . $url . '?' . $parameters);
    }
    else header($location . '?' . $parameters);
    return ;
  }

/**
   * Encrypt URL
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $urlLink
   * @return string
   */
  function encryptLink( $urlLink ) {
    if ( defined ( 'ENABLE_ENCRYPT' ) && ENABLE_ENCRYPT == 'yes'  )   {
      $urlLink = G::encryptUrl ( $urlLink , URL_KEY );
    }
    return $urlLink;
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
   * convert text in fields without @@
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $txt
   * @param  string $Fields
   * @return string
   */
  function replaceTextWithFields ( $txt, $Fields )
  {
    $fieldName = array();
    $str = $txt;
    do {
      $found = ereg ( "@@[a-zA-Z0-9_]+", $str, $token);
      if ( $found ) {
        $size = strlen ( $token[0] );
        $posstr = strpos( $str, $token[0] );
        $str = substr ( $str, 0, $posstr) . substr ($str, $posstr+$size) ;
        $fieldNames[$size][] = $token[0];
      }
    } while ( $found );
    if (is_null($fieldNames)) $fieldNames = array();
    $keys = array_keys ($fieldNames); //se obtienen los tamanos
    rsort ($keys);  //se ordena en el mismo array

    foreach ( $keys as  $k=>$len ) { //se reemplaza de mayor a menor longitud de variables.
        foreach ( $fieldNames[$len] as $j=>$token ) {
          $value = $Fields[ substr($token,2) ];
          $txt = str_replace ( $token,  $value, $txt );
        }
    }
    return $txt;
  }


/**
   * Load a options arrays with data from an web service call
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $wsdl
   * @param  string $method
   * @param  string $xmlParams
   * @return void
   */
  function LoadWSOptions ( $wsdl, $method, $xmlParams )
  {
    if (strpos($wsdl, '@@ThisServer') !== false)
      $wsdl = str_replace('@@ThisServer', 'http://' . $_SERVER['HTTP_HOST'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/services', $wsdl);
    $options = array();
    //require_once ("class.SOAP_parser2.php");
    require_once('nusoap.php');
    $Fields = unserialize (stripslashes ($xmlParams));
    $opts = array();
    $p = new myParser;
    $reqFields = $p->getInputFields ( $wsdl, $method );
    if ( is_array ( $reqFields) )
      foreach ( $reqFields as $key=>$val ) {
          $opts[$key] = $Fields[$key];
      }
    else
      $opts = $Fields;
    //$mysoap = new SOAP_Client( $wsdl );
    $mysoap = new soapclient( $wsdl );
    $ret = $mysoap->call( $method, $opts, array( 'trace' => 1) );
    if ( gettype($ret) == 'object' && get_class($ret) == 'soap_fault' ) {
      print "<center class='sendMsgRojo'>Web Service Error : " . $ret->message . "</center>";
      $options[] = 'Web Service Error ' . $ret->message;
      return;
    }
    if ( is_array ( $ret ) )
      foreach ( $ret as $key => $val ) {
        $values = array_values ( get_object_vars ( $val ) );
        $options[ $values[0] ] = $values[1];
      }
    else
      $options = $ret;
    return $options;
  }


/**
   * Set Web Service
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $WS_Connection
   * @param  string $Method
   * @param  string $Parameters
   * @return void
   */
  function UseWebService($WS_Connection, $Method, $Parameters = '') {
    global $HTTP_SESSION_VARS;
    $Connection = new DBConnection;
    $Session    = new DBSession($Connection);

    $Dataset    = $Session->Execute('SELECT * FROM WS_CONNECTION WHERE WSC_UID = ' . $WS_Connection);
    $Row        = $Dataset->Read();

    if (strpos($Row['WSC_WSDL'], '/') === false) {
           if (strpos($Row['WSC_WSDL'], '.') === false)
        $Row['WSC_WSDL'] = $Row['WSC_WSDL'] . '.php';
           if (strpos($Row['WSC_WSDL'], '?wsdl') === false)
        $Row['WSC_WSDL'] = $Row['WSC_WSDL'] . '?wsdl';
       $Row['WSC_WSDL'] = 'http://' . $_SERVER['HTTP_HOST'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/services/' . $Row['WSC_WSDL'];
    }
    require_once ("class.SOAP_parser.php");
    $Parser = new myParser;

    $RequiredFields = $Parser->getInputFields($Row['WSC_WSDL'], $Method);
    if (!is_array($Parameters))
      $Parameters = unserialize($Parameters);
    foreach ($RequiredFields as $Key=>$Value) {
      if (strpos($Parameters[$Key], '@@') !== false) {
        if ($HTTP_SESSION_VARS['CURRENT_APPLICATION'] != '') {
          if (empty($allfields)) {
            $dyna = new Dynaform($Connection);
            $allfields = $dyna->getFieldsDefaultDynaform($HTTP_SESSION_VARS['CURRENT_APPLICATION'], 0);
            $Parameters[$Key] = $dyna->replaceTextWithFields($Parameters[$Key], $allfields);
          }
          else
            $Parameters[$Key] = $dyna->replaceTextWithFields($Parameters[$Key], $allfields);
        }
      }
      $Values[$Key] = $Parameters[$Key];
    }
    if (($Row['WSC_USERNAME_VAR'] != '') && ($Row['WSC_USERNAME'] != '') && ($Row['WSC_PASSWORD_VAR'] != '') && ($Row['WSC_PASSWORD'] != '')) {
      $Values[$Row['WSC_USERNAME_VAR']] = $Row['WSC_USERNAME'];
      $Values[$Row['WSC_PASSWORD_VAR']] = $Row['WSC_PASSWORD'];
    }
    $Options['trace'] = 1;
    if ($Row['WSC_URN'] != '')
      $Options['namespace'] = 'urn:' . $Row['WSC_URN'];
    $MySOAP = new SOAP_Client($Row['WSC_WSDL']);
    $Results = $MySOAP->call($Method, $Values, $Options);
    if (is_object($Results))
      $Results = get_object_vars($Results);
    if (is_array($Results)) {
      foreach ($Results as $Key=>$Value) {
        if (is_object($Value))
          $Response[] = get_object_vars($Value);
        else
          $Response[$Key] = $Value;
      }
      $Results = $Response;
    }
    return $Results;
  }

/**
   * Copy an array to other array
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $fields
   * @return array
   */
  function copyArray($fields){
    if(is_array($fields)){
      $contenedor = array ();
      foreach ($fields as $key => $val){
          if(is_array($val)){
            $contenedor[$key] = G::copyArray($val);
          }else
            $contenedor[$key] = $val;
      }
    }else
      $contenedor = $fields;
    return $contenedor;
  }

/**
   * Upload a file and then copy to path+ nameToSave
   *
   * @author Mauricio Veliz <mauricio@colosa.com>
   * @access public
   * @param  string $file
   * @param  string $path
   * @param  string $nameToSave
   * @param  string $permission
   * @return true
   */
  function uploadFile($file, $path ,$nameToSave, $permission='0777')
  {
    if(!is_dir($path)) G::verifyPath($path);
    move_uploaded_file( $file , $path . "/" . $nameToSave );
    chmod( $path . "/" . $nameToSave , 0666 );
    return 1;
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

/**
   * Creates a directory if it doesn't exist
   * It create all path
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public
   * @param  string $file
   * @param  string $path
   * @return int
   */
  function verifyPath($path){
    $var = explode('/',substr($path,1));
    $create = '';
    if(is_array($var))
      foreach($var as $key => $val ){
        $create .= "/".$val;
        if(!is_dir($create)){
          mkdir( $create, 0777 );
          chmod( $create, 0777 );
        }
      }
    return 1;
  }
/**
   * Convert Farsi text to render with ImageTTFText
   *
   * @author Alireza Abedini <a_r_abedini@yahoo.com>
   * @return string (utf-8)
   */
  function ToIpt($arabicStr, $utfStr="")
  {
      //Set Array
      //               I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I    I�I   I�I   I�I   I��I
      $ch[0] = array ( 199,  200,  129,  202,  203,  204,  141,  205,  206,  207,  208,  209,  210,  142,  211,  212,  213,  214,  216,  217,  218,  219,  221,  222,  223,  144,  225,  227,  228,  230,  229,  237,  198,  195,  194,  250);
      $ch[1] = array (  72,   77,   81,   85,   89,   93,   97,  101,  105,  106,  108,  110,  112,  114,  119,  123,  130,  134,  138,  146,  150,  154,  160,  164,  168,  172,  178,  182,  186,  187,  192,  196,   71,   67,   65,  175);
      $ch[2] = array (  73,   76,   80,   84,   88,   92,   96,  100,  104,  107,  109,  111,  113,  115,  118,  122,  126,  133,  137,  145,  149,  153,  159,  163,  167,  171,  177,  181,  185,  188,  191,  195,   70,   68,   66,  176);
      $ch[3] = array (  73,   75,   79,   83,   87,   91,   95,   99,  103,  107,  109,  111,  113,  115,  117,  121,  125,  132,  136,  140,  148,  152,  156,  162,  166,  170,  174,  180,  184,  188,  190,  194,   68,   68,   66,  176);
      $ch[4] = array (  72,   74,   78,   82,   86,   90,   94,   98,  102,  106,  108,  110,  112,  114,  116,  120,  124,  131,  135,  139,  147,  151,  155,  161,  165,  169,  173,  179,  183,  187,  189,  193,   69,   67,   65,  175);
      $specialDelimiters = array (' ', '.', ')', '(', ',', ';', '<', '>', '}', '{',
                                  '=', '-', '*', '&', '^', '%', '$', '#', '@', '!',
                                  '~', '`', '"', ':', '\'');
      if ($arabicStr!="")
      {
          $addBy = 1;
          $unicStr = $arabicStr;
          $delimiters = array ('�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', ' ');
      }
      else
      {
          $addBy = 2;
          $unicStr = $utfStr;
          $delimiters = array ( chr(167),chr(175),chr(176),chr(177),chr(178),chr(152),chr(136),chr(162),chr(166),chr(32));
          //               I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I   I�I    I�I   I�I   I�I   I��I
          $ch[0] = array ( 167,  168,  190,  170,  171,  172,  231,  173,  174,  175,  176,  177,  178,  152,  179,  180,  181,  182,  183,  184,  185,  186,  129,  130,  131,  230,  132,  133,  134,  136,  135,  138,  166,  163,  162,  250);
      }

//BEGIN:By David Santos: Splitby numbers, words an signs to prevent malfunctions with the original ToIpt function
//Only with utf8  :| no arabicStr
      $sp=preg_split('/(?:[0-9]+)|(?:[\-]+)|(?:[., \(\)]+)/u',$unicStr);
      preg_match_all('/(?:[0-9]+)|(?:[\-]+)|(?:[., \(\)]+)/u',$unicStr,$ma);
      $rr='';
      if (sizeof($sp)>1)
      for($r=0;$r<sizeof($sp);$r++)
      {
        if ($sp[$r]!='') $rr=G::ToIpt('',$sp[$r]).$rr;
        if ($r==(sizeof($sp)-1)) return $rr;
        $rr=$ma[0][$r].$rr;
      }
//:END
      //Convert
      $cursor= $KindChr= $Chr = 0;
      $iptStr = "";
      for ($i=($addBy-1);$i<strlen($unicStr);$i+=$addBy)
      {
          if ($addBy==2 && ord($unicStr[$i-1])==32 && ord($unicStr[$i])!=32)
          {
              $i++;
              $iptStr = $iptStr." ";
              $cursor=0;
          }
          if ($addBy==2 && ord($unicStr[$i])==175 && ord($unicStr[$i-1])==218)//it is Ge
              $unicStr[$i]=chr(230);

          if ($addBy==2 && ord($unicStr[$i])==134 && ord($unicStr[$i-1])!=217)//it is CHe
              $unicStr[$i]=chr(231);

          if (in_array(ord($unicStr[$i]), $ch[0]))
              $Chr = array_search(ord($unicStr[$i]), $ch[0]);
          else
              $Chr = -1;

          $condition = ($i==strlen($unicStr)-1 || in_array($unicStr[$i+1], $specialDelimiters));
          if (($cursor == 0) && !($condition))
              $KindChr = 1;
          elseif (($cursor != 0) && !($condition))
              $KindChr = 2;
          elseif (($cursor != 0) && $condition)
              $KindChr = 3;
          else
              $KindChr = 4;

          if ($Chr == -1)
              $iptStr = $unicStr[$i].$iptStr;
          else
              $iptStr = chr($ch[$KindChr][$Chr]).chr(228).$iptStr;

          $cursor++;
          if (in_array($unicStr[$i],$delimiters))
              $cursor = 0;
      }

  return($iptStr);
  }
/**
   * Cheqs if a string has has other letters like persian. (for PHP 4.3 ..., it hasn't /\pLo/)
   *
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @return boolean
   */
  function hasOtherLetters($sText)
  {
    return (preg_match('/[^\w\W]/u',$sText)!==0);
  }

function encrypt_urls($url) //Added By JHL. To simplify the encryption
{
  //print "$url = ";print_r(ENABLE_ENCRYPT=='yes'?G::encryptUrl(urldecode($url), URL_KEY):$url);
  return (ENABLE_ENCRYPT=='yes'?G::encryptUrl(urldecode($url), URL_KEY):$url);
}

}
?>
