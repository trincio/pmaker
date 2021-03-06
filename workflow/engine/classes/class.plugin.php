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

define ( 'PM_CREATE_CASE',       1001 );
define ( 'PM_UPLOAD_DOCUMENT',   1002 );
define ( 'PM_CASE_DOCUMENT_LIST',1003 );
define ( 'PM_BROWSE_CASE',       1004 );
define ( 'PM_NEW_PROCESS_LIST',  1005 );
define ( 'PM_NEW_PROCESS_SAVE',  1006 );
define ( 'PM_NEW_DYNAFORM_LIST', 1007 );
define ( 'PM_NEW_DYNAFORM_SAVE', 1008 );
define ( 'PM_EXTERNAL_STEP',     1009 );
define ( 'PM_CASE_DOCUMENT_LIST_ARR', 1010 );

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

class stepDetail {
 	var $sNamespace;
	var $sStepId;
 	var $sStepName;
  var $sStepTitle;
  function __construct( $sNamespace, $sStepId, $sStepName, $sStepTitle ) {
   	$this->sNamespace  = $sNamespace;
   	$this->sStepId     = $sStepId;
   	$this->sStepName   = $sStepName;
   	$this->sStepTitle  = $sStepTitle;
	 }
}

class redirectDetail {
 	var $sNamespace;
	var $sRoleCode;
 	var $sPathMethod;

  function __construct( $sNamespace, $sRoleCode, $sPathMethod ) {
   	$this->sNamespace  = $sNamespace;
   	$this->sRoleCode   = $sRoleCode;
   	$this->sPathMethod = $sPathMethod;
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
 	var $sDocumentUid;
 	var $bUseOutputFolder;

  function __construct( $sApplicationUid, $sUserUid, $sFilename, $sFileTitle, $sDocumentUid ) {
   	$this->sApplicationUid = $sApplicationUid;
   	$this->sUserUid        = $sUserUid;
   	$this->sFilename       = $sFilename;
   	$this->sFileTitle      = $sFileTitle;
   	$this->sDocumentUid    = $sDocumentUid;
   	$this->bUseOutputFolder = false;
	 }
}

class PMPlugin {
	var $sNamespace;
	var $sClassName;
  var $sFilename = null;
  var $iVersion = 0;
  var $sFriendlyName = null;
  var $sPluginFolder = '';
  var $aWorkspaces = null;

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

  function registerDashboard( ) {
    $oPluginRegistry =& PMPluginRegistry::getSingleton();
    $oPluginRegistry->registerDashboard ( $this->sNamespace);
  }

  function registerReport( ) {
    $oPluginRegistry =& PMPluginRegistry::getSingleton();
    $oPluginRegistry->registerReport ( $this->sNamespace);
  }

  function registerPmFunction( ) {
    $oPluginRegistry =& PMPluginRegistry::getSingleton();
    $oPluginRegistry->registerPmFunction ( $this->sNamespace);
  }

  function setCompanyLogo( $filename ) {
    $oPluginRegistry =& PMPluginRegistry::getSingleton();
    $oPluginRegistry->setCompanyLogo( $this->sNamespace, $filename);
  }

  function redirectLogin( $role, $pathMethod ) {
    $oPluginRegistry =& PMPluginRegistry::getSingleton();
    $oPluginRegistry->registerRedirectLogin( $this->sNamespace, $role, $pathMethod );
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

  function registerStep($sStepId, $sStepName, $sStepTitle ) {
    $oPluginRegistry =& PMPluginRegistry::getSingleton();
    $oPluginRegistry->registerStep( $this->sNamespace, $sStepId, $sStepName, $sStepTitle );
  }

  function registerTrigger( $sTriggerId, $sTriggerName ) {
    $oPluginRegistry =& PMPluginRegistry::getSingleton();
    $oPluginRegistry->registerTrigger ( $this->sNamespace, $sTriggerId, $sTriggerName );
  }

  function delete($sFilename, $bAbsolutePath = false) {
    if (!$bAbsolutePath) {
      $sFilename = PATH_PLUGINS . $this->sPluginFolder . PATH_SEP . $sFilename;
    }
    @unlink($sFilename);
  }

  function copy($sSouce, $sTarget, $bSourceAbsolutePath = false, $bTargetAbsolutePath = false) {
    if (!$bSourceAbsolutePath) {
      $sSouce = PATH_PLUGINS . $this->sPluginFolder . PATH_SEP . $sSouce;
    }
    if (!$bTargetAbsolutePath) {
      $sTarget = PATH_PLUGINS . $this->sPluginFolder . PATH_SEP . $sTarget;
    }
    G::verifyPath(dirname($sTarget), true);
    @copy($sSouce, $sTarget);
  }

  function rename($sSouce, $sTarget, $bSourceAbsolutePath = false, $bTargetAbsolutePath = false) {
    if (!$bSourceAbsolutePath) {
      $sSouce = PATH_PLUGINS . $this->sPluginFolder . PATH_SEP . $sSouce;
    }
    if (!$bTargetAbsolutePath) {
      $sTarget = PATH_PLUGINS . $this->sPluginFolder . PATH_SEP . $sTarget;
    }
    G::verifyPath(dirname($sTarget), true);
    @chmod(dirname($sTarget), 0777);
    @rename($sSouce, $sTarget);
  }
}
