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

require_once ( 'class.pluginRegistry.php');

define ( 'G_PLUGIN_CLASS',     1 );

define ( 'PM_CREATE_CASE',     1001 );
define ( 'PM_UPLOAD_DOCUMENT', 1002 );
define ( 'PM_BROWSE_CASE',     1003 );
define ( 'PM_NEW_PROCESS_LIST',1004 );
define ( 'PM_NEW_PROCESS_SAVE',1005 );


class menuDetail {
	var $sNamespace;
	var $sMenuId;
	var $sFilename;

  function __construct( $sNamespace, $sMenuId, $sFilename ) {
  	$this->sNamespace = $sNamespace;
  	$this->sMenuId    = $sMenuId;
  	$this->sFilename  = $sFilename;
	}
}

class triggerDetail {
	var $sNamespace;
	var $sTriggerId;
	var $sTriggerName;

  function __construct( $sNamespace, $sTriggerId, $sTriggerName ) {
  	$this->sNamespace   = $sNamespace;
  	$this->sTriggerId   = $sTriggerId;
  	$this->sTriggerName = $sTriggerName;
	}
}

class folderDetail {
 	var $sNamespace;
	var $sFolderId;
 	var $sFolderName;

  function __construct( $sNamespace, $sFolderId, $sFolderName ) {
   	$this->sNamespace  = $sNamespace;
   	$this->sFolderId   = $sFolderId;
   	$this->sFolderName = $sFolderName;
	 }
}

class PMPlugin {
	var $sNamespace;
	var $sClassName;
  var $sFilename = null;
  var $iVersion = 0;
  var $sFriendlyName = null;
  var $sPluginFolder = '';

  function PMPlugin($sNamespace, $sFilename = null) {
    $this->sNamespace  = $sNamespace;
    $this->sClassName  = $sNamespace . 'Plugin';
    $this->sPluginFolder = $sNamespace;
    $this->sFilename = $sFilename;
  }

  function registerMenu( $menuId, $menuFilename ) {
    $oPluginRegistry =& PMPluginRegistry::getSingleton();
  	$sMenuFilename = PATH_PLUGINS . $this->sPluginFolder . PATH_SEP . $menuFilename;
    $oPluginRegistry->registerMenu ( $this->sNamespace, $menuId, $sMenuFilename);
  }

  /**
   * Register a folder for methods
   *
   * @param unknown_type $sFolderName
   */
  function registerFolder($sFolderId, $sFolderName ) {
    $oPluginRegistry =& PMPluginRegistry::getSingleton();
    $oPluginRegistry->registerFolder( $this->sNamespace, $sFolderId, $sFolderName );
  }

  function registerTrigger( $sTriggerId, $sTriggerName ) {
    $oPluginRegistry =& PMPluginRegistry::getSingleton();
    $oPluginRegistry->registerTrigger ( $this->sNamespace, $sTriggerId, $sTriggerName );
  }


}
