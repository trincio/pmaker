<?php
/**
 * class.plugin.php
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

class folderData {
 	var $sProcessUid;
	var $sProcessTitle;
 	var $sApplicationUid;
 	var $sApplicationTitle;
 	var $sUserUid;
 	var $sUserLogin;
 	var $sUserFullName;
 	
  function __construct( $sProcessUid, $sProcessTitle, $sApplicationUid, $sApplicationTitle, $sUserUid, $sUserLogin = '', $sUserFullName ='') {
   	$this->sProcessUid       = $sProcessUid;
   	$this->sProcessTitle     = $sProcessTitle;
   	$this->sApplicationUid   = $sApplicationUid;
   	$this->sApplicationTitle = $sApplicationTitle;
   	$this->sUserUid          = $sUserUid;
   	$this->sUserLogin        = $sUserLogin;
   	$this->sUserFullName     = $sUserFullName;
	 }
}


class uploadDocumentData {
 	var $sApplicationUid;
 	var $sUserUid;
 	var $sFilename;
 	var $sFileTitle;
 	
  function __construct( $sApplicationUid, $sUserUid, $sFilename, $sFileTitle ) {
   	$this->sApplicationUid = $sApplicationUid;
   	$this->sUserUid        = $sUserUid;
   	$this->sFilename       = $sFilename;
   	$this->sFileTitle     = $sFileTitle;
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
