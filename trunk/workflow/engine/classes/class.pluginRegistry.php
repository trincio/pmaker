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

class pluginDetail {
	var $sNamespace;
	var $sClassName;
  var $sFriendlyName = null;
  var $sDescription = null;
  var $sSetupPage = null;
	var $sFilename;
	var $sPluginFolder = '';
	var $iVersion = 0;
	var $enabled = false;

  function __construct( $sNamespace, $sClassName, $sFilename, $sFriendlyName = '', $sPluginFolder ='', $sDescription ='', $sSetupPage ='', $iVersion = 0) {
  	$this->sNamespace = $sNamespace;
  	$this->sClassName = $sClassName;
  	$this->sFriendlyName = $sFriendlyName;
  	$this->sDescription  = $sDescription;
  	$this->sSetupPage    = $sSetupPage;
  	$this->iVersion      = $iVersion;
  	$this->sFilename     = $sFilename;
  	if ( $sPluginFolder == '') 
  	  $this->sPluginFolder = $sNamespace;
  	else
  	  $this->sPluginFolder = $sPluginFolder ;
	}
}

class PMPluginRegistry {
  private $_aPluginDetails = array();
  private $_aPlugins = array();
  private $_aMenus = array();
  private $_aFolders = array();
  private $_aTriggers = array();

  static private $instance = NULL;

  private function __construct() {}

  function &getSingleton() {
    if (self::$instance == NULL) {
      self::$instance = new PMPluginRegistry ();
    }
    return self::$instance;
  }
  
  function serializeInstance() {
    return serialize ( self::$instance); 
  }

  function unSerializeInstance( $serialized ) {
    if (self::$instance == NULL) {
      self::$instance = new PMPluginRegistry ();
    }

    $instance = unserialize ( $serialized );
    self::$instance = $instance;
  }

  //delete this function, it was here, only for test and debug purposes
  function showArrays () { 
  krumo ( $this->_aPlugins);
  krumo ( $this->_aMenus);
  krumo ( $this->_aFolders);
  krumo ( $this->_aTriggers);  
  }
  
  /**
   * Register the plugin in the singleton
   *
   * @param unknown_type $sClassName
   * @param unknown_type $sNamespace
   * @param unknown_type $sFilename
   */
  function registerPlugin( $sNamespace, $sFilename = null) {
    $sClassName = $sNamespace . 'plugin';
    if ( isset( $this->_aPluginDetails[$sNamespace] ) )
      return;

    require_once ( $sFilename );
    $plugin = new $sClassName ($sNamespace, $sFilename);
    $detail = new pluginDetail (
            $sNamespace, 
            $sClassName, 
            $sFilename, 
            $plugin->sFriendlyName, 
            $plugin->sPluginFolder, 
            $plugin->sDescription,
            $plugin->sSetupPage,
            $plugin->iVersion  );
    $this->_aPluginDetails[$sNamespace] = $detail;
  }

  /**
   * get the plugin details, by filename
   *
   * @param unknown_type $sFilename
   */
  function getPluginDetails( $sFilename ) {
    foreach ( $this->_aPluginDetails as $key => $row ) {
      if ( $sFilename == baseName ( $row->sFilename ) )
        return $row;
    }
  }

  /**
   * Enable the plugin in the singleton
   *
   * @param unknown_type $sNamespace
   */
  function enablePlugin($sNamespace ) {
  	foreach ( $this->_aPluginDetails as $namespace=>$detail ) {
  		if ( $sNamespace == $namespace ) {
  		  $this->registerFolder($sNamespace, $sNamespace, $detail->sPluginFolder ); //register the default directory, later we can have more
  		  $this->_aPluginDetails[$sNamespace]->enabled = true;
  		}
  	}
  }

  /**
   * disable the plugin in the singleton
   *
   * @param unknown_type $sNamespace
   */
  function disablePlugin($sNamespace ) {
  	 foreach ( $this->_aPluginDetails as $namespace=>$detail ) {
  		if ( $sNamespace == $namespace )
//  		  $this->_aPluginDetails[$sNamespace]->enabled = false;
  		  unset ($this->_aPluginDetails[$sNamespace]);
  	 }

  	 foreach ( $this->_aMenus as $key=>$detail ) {
  	   if ( $detail->sNamespace == $sNamespace )
  	     unset ( $this->_aMenus[ $key ] ); 
    }
  	 foreach ( $this->_aFolders as $key=>$detail ) {
  	   if ( $detail->sNamespace == $sNamespace )
  	     unset ( $this->_aFolders[ $key ] ); 
     }

  	 foreach ( $this->_aTriggers as $key=>$detail ) {
  	   if ( $detail->sNamespace == $sNamespace )
  	     unset ( $this->_aTriggers[ $key ] ); 
    }
    
  }
  
  /**
   * disable the plugin in the singleton
   *
   * @param unknown_type $sNamespace
   */
  function getStatusPlugin($sNamespace ) {
  	foreach ( $this->_aPluginDetails as $namespace=>$detail ) {
  		if ( $sNamespace == $namespace )
        if ( $this->_aPluginDetails[$sNamespace]->enabled )
          return 'enabled';
        else 
          return 'disabled';
  	}
  	return 0;
  }
  
  
  /**
   * Register a menu in the singleton
   *
   * @param unknown_type $sNamespace
   * @param unknown_type $sMenuId
   * @param unknown_type $sFilename
   */
  function registerMenu($sNamespace, $sMenuId, $sFilename ) {
    $found = false;
  	 foreach ( $this->_aMenus as $row=>$detail ) {
  		if ( $sMenuId == $detail->sMenuId && $sNamespace == $detail->sNamespace ) 
  		  $found = true;
  	 }	
    if ( !$found ) {
  	  $menuDetail = new menuDetail ($sNamespace, $sMenuId, $sFilename);
      $this->_aMenus[] = $menuDetail;
    }
  }

  /**
   * Register a folder for methods
   *
   * @param unknown_type $sFolderName
   */
  function registerFolder($sNamespace, $sFolderId, $sFolderName ) {
    $found = false;
  	foreach ( $this->_aFolders as $row=>$detail ) 
  		if ( $sFolderId == $detail->sFolderId && $sNamespace == $detail->sNamespace ) 
  		  $found = true;
  		
    if ( !$found ) {
      $this->_aFolders[] = new folderDetail ( $sNamespace, $sFolderId, $sFolderName);
    }
  }

  /**
   * return true if the $sFolderName is registered in the singleton
   *
   * @param unknown_type $sFolderName
   */
  function isRegisteredFolder( $sFolderName ) {
  	foreach ( $this->_aFolders as $row => $folder ) {
  		if ( $sFolderName  == $folder->sFolderName && is_dir ( PATH_PLUGINS . $folder->sFolderName ) ) {
  			return true;
  		}
  	}
  	return false;
  }

  /**
   * return all menus related to a menuId
   *
   * @param unknown_type $menuId
   */
  function getMenus( $menuId ) {
  	foreach ( $this->_aMenus as $row=>$detail ) {
  		if ( $menuId == $detail->sMenuId && file_exists ( $detail->sFilename ) ) {
  			include ( $detail->sFilename );
  		}
  	}
  }

  /**
   * execute all triggers related to a triggerId
   *
   * @param unknown_type $menuId
   */
  function executeTriggers( $triggerId, $oData ) {
  	foreach ( $this->_aTriggers as $row=>$detail ) {
  		if ( $triggerId == $detail->sTriggerId  ) { 
 		  
  		  //review all folders registered for this namespace
  		  $found = false;
        $classFile = '';

       	foreach ( $this->_aFolders as $row=>$folder ) 
     	    $fname = PATH_PLUGINS . $folder->sFolderName . PATH_SEP . 'class.' . $folder->sFolderName  .'.php';
  		    if ( $detail->sNamespace == $folder->sNamespace && file_exists ( $fname ) ) {
  		      $found = true;
  		      $classFile = $fname;
  		    }

        if ( $found ) {
    			require_once ( $classFile );
    			$sClassName = str_replace ( 'plugin', 'class', $this->_aPluginDetails[ $detail->sNamespace ]->sClassName);
          $obj = new $sClassName( );
          $methodName = $detail->sTriggerName;
          $response = $obj->{$methodName}( $oData );
          if (PEAR::isError($response) ) {
         	  print $response->getMessage(); 	return;
          }
        }
        else
          print "error in call method " . $detail->sTriggerName;
  		}
  	}
  }

  /**
   * Register a trigger in the Singleton
   *
   * @param unknown_type $sTriggerId
   * @param unknown_type $sMethodFunction
   */
  function registerTrigger($sNamespace, $sTriggerId, $sTriggerName ) {
    $found = false;
  	foreach ( $this->_aTriggers as $row=>$detail ) {
  		if ( $sTriggerId == $detail->sTriggerId && $sNamespace == $detail->sNamespace ) 
  		  $found = true;
  	}	
    if ( !$found ) {
    	$triggerDetail = new triggerDetail ($sNamespace, $sTriggerId, $sTriggerName);
      $this->_aTriggers[] = $triggerDetail;
    }
  }

  function &getPlugin($sNamespace) {
    if (array_key_exists($sNamespace, $this->_aPlugins)) {
        return $this->_aPlugins[$sNamespace];
    }
/*
    $aDetails = KTUtil::arrayGet($this->_aPluginDetails, $sNamespace);
    if (empty($aDetails)) {
        return null;
    }
    $sFilename = $aDetails[2];
    if (!empty($sFilename)) {
        require_once($sFilename);
    }
    $sClassName = $aDetails[0];
    $oPlugin =& new $sClassName($sFilename);
    $this->_aPlugins[$sNamespace] =& $oPlugin;
    return $oPlugin;
*/
  }

  function setupPlugins() {
  	$iPlugins = 0;
    foreach ( $this->_aPluginDetails as $namespace=>$detail ) {
  		if ( isset($detail->enabled ) && $detail->enabled ) {
        if ( !empty( $detail->sFilename) && file_exists ($detail->sFilename) ) {
          require_once( $detail->sFilename);
          $oPlugin =& new $detail->sClassName( $detail->sNamespace, $detail->sFilename );
          $this->_aPlugins[$detail->sNamespace] =& $oPlugin;
          
    		  $iPlugins++;
    		  //print ( "$iPlugins $namespace <br>");
    		  $oPlugin->setup();
        }
  		}
  	}
  	return $iPlugins;
  }
  
  function executeMethod( $sNamespace, $methodName, $oData ) {
  	try {
  	$details = $this->_aPluginDetails[$sNamespace];
  	$pluginFolder = $details->sPluginFolder;
  	$className    = $details->sClassName;  	
    $classFile = PATH_PLUGINS . $pluginFolder . PATH_SEP . 'class.' . $pluginFolder .'.php';
  	if ( file_exists ( $classFile ) ) {
    	require_once ( $classFile );
    	$sClassName = str_replace ( 'plugin', 'class', $className );
      $obj = new $sClassName( );
      if ( !in_array ( $methodName, get_class_methods ($obj) ) ) {
      	throw ( new Exception ( "The method '$methodName' doesn't exists in class '$sClassName' ") );
      }
      $obj->sNamespace    = $details->sNamespace;
      $obj->sClassName    = $details->sClassName;
      $obj->sFilename     = $details->sFilename;
      $obj->iVersion      = $details->iVersion;
      $obj->sFriendlyName = $details->sFriendlyName;
      $obj->sPluginFolder = $details->sPluginFolder;
      $response = $obj->{$methodName}( $oData );
    }
    return $response;
    }
    catch ( Exception $e ) {
    	throw ($e);
    }
  }
  function getFieldsForPageSetup( $sNamespace ) {
  	$oData = NULL;
    return $this->executeMethod ( $sNamespace, 'getFieldsForPageSetup', $oData);
  }
  
  
  function updateFieldsForPageSetup( $sNamespace, $oData ) {
  	if ( !isset ($this->_aPluginDetails[$sNamespace] ) ) {
  		throw ( new Exception ( "The namespace '$sNamespace' doesn't exists in plugins folder." ) ); 
  	};
    return $this->executeMethod ( $sNamespace, 'updateFieldsForPageSetup', $oData);
  }
  
}
