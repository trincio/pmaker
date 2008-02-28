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
 * Class headPublisher
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 * @dependenciesnone
 */
class headPublisher
{
  var $scriptFiles  = array();
  var $leimnudLoad  = array();
  var $leimnudInitString = '  var leimnud = new maborak();
  leimnud.make();
  leimnud.Package.Load("panel,validator,app,rpc,fx,drag,drop,dom,abbr",{Instance:leimnud,Type:"module"});';
  var $headerScript = '
	  leimnud.exec(leimnud.fix.memoryLeak);
  	if(leimnud.browser.isIphone)
	{
		leimnud.iphone.make();
	}';
  var $disableHeaderScripts = false; 
  var $title='';
  /**
   * Function headPublisher
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return string
   */
  function headPublisher()
  {
    $this->addScriptFile("/js/maborak/core/maborak.js");
    if ( defined( 'SYS_LANG' ) )
    {
      $jslabel = 'labels/' . SYS_LANG . '.js';
      if ( ! file_exists( PATH_CORE . 'js' . PATH_SEP . $jslabel ) )
        $jslabel = 'labels/en.js';
    }
    else
      $jslabel = 'labels/en.js';
      
    if ( file_exists( PATH_CORE . 'js' . PATH_SEP . $jslabel ) ) {
      $this->addScriptFile( '/jscore/' . $jslabel , 1 );
    }
    $this->addScriptFile("/js/common/core/common.js",1);
    $this->addScriptFile("/js/common/core/webResource.js",1);
    $this->addScriptFile("/js/json/core/json.js",1);
    $this->addScriptFile("/js/form/core/form.js",1);
    //$this->addScriptFile("/js/grid/core/grid.js",2);
    //$this->addScriptFile("/skins/JSForms.js",1);
    //$this->addInstanceModule("leimnud", "drag");
    //$this->addInstanceModule("leimnud", "panel");
  }
  /**
   * Function setTitle
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string url
   * @parameter string LoadType
   * @return string
   */
  function setTitle( $title )
  {
    $this->title = $title;
  }
  /**
   * Function addScriptFile
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string url
   * @parameter string LoadType
   * @return string
   */
  function addScriptFile($url, $LoadType=1)
  {
    if ($LoadType==1) $this->scriptFiles[$url]=$url;
    if ($LoadType==2) $this->leimnudLoad[$url]=$url;
  }
  /**
   * Function addInstanceModule
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string instance
   * @parameter string module
   * @return string
   */
  function addInstanceModule( $instance , $module )
  {
    $this->headerScript .= "leimnud.Package.Load('".$module."',{Instance:".$instance.",Type:'module'});\n";
  }
  /**
   * Function addClassModule
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string class
   * @parameter string module
   * @return string
   */
  function addClassModule( $class , $module )
  {
    $this->headerScript .= "leimnud.Package.Load('".$module."',{Class:".$class.",Type:'module'});\n";
  }
  /**
   * Function addScriptCode
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string script
   * @return string
   */
  function addScriptCode( $script )
  {
    $this->headerScript .= $script;
  }
  /**
   * Function printHeader
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return string
   */
  function printHeader()
  {
    if ($this->disableHeaderScripts) return '';
    $head = '';
    $head .= '<TITLE>'.$this->title.'</TITLE>';
    foreach($this->scriptFiles as $file)
      $head .= "  <script type='text/javascript' src='" . $file . "'></script>\n";
    $head .= "<script type='text/javascript'>\n";
    $head .= $this->leimnudInitString;
    foreach($this->leimnudLoad as $file)
      $head .= "  leimnud.Package.Load(false, {Type: 'file', Path: '".$file."', Absolute : true});\n";
    $head .= $this->headerScript;
    $head .= "</script>\n";
    return $head;
  }
  /**
   * Function printRawHeader
   * Its prupose is to load el HEADs initialization javascript
   * into a single SCRIPT tag, it is usefull when it is needed
   * to load a page by leimnud floating panel of by another ajax
   * method. (See also RAW skin)
   *
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return string
   */
  function printRawHeader(){
    $head = '';
    //$head .= "<script language='javascript'>\n";
    foreach($this->scriptFiles as $file)
      $head .= "  eval(ajax_function('".$file."','',''));\n";
    foreach($this->leimnudLoad as $file)
      $head .= "  eval(ajax_function('".$file."','',''));\n";
    //Adapts the add events on load to simple javascript sentences.
    $this->headerScript = preg_replace('/\s*leimnud.event.add\s*\(\s*window\s*,\s*(?:\'|")load(?:\'|")\s*,\s*function\(\)\{(.+)\}\s*\)\s*;?/', '$1', $this->headerScript);
    $head .= $this->headerScript;
    //$head .= "</script>\n";
    return $head;
  }
  /**
   * Function clearScripts
   * Its prupose is to clear all the scripts of the header.
   *
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return string
   */
  function clearScripts(){
    $this->scriptFiles  = array();
    $this->leimnudLoad  = array();
    $this->leimnudInitString = '';
    $this->headerScript = '';
  }
}
?>
