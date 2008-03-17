<?
/**
 * processes_ImportFile.php
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
 
 try {
  //load the variables
  G::LoadClass('plugin');

  //save the file
  if ($_FILES['form']['error']['PLUGIN_FILENAME'] == 0) {
    $filename = $_FILES['form']['name']['PLUGIN_FILENAME'];
    $path     = PATH_DOCUMENT . 'input' . PATH_SEP ;
    $tempName = $_FILES['form']['tmp_name']['PLUGIN_FILENAME'];
    G::uploadFile($tempName, $path, $filename );
  }
  
  if ( ! $_FILES['form']['type']['PLUGIN_FILENAME'] == 'application/octet-stream') 
    throw ( new Exception ( "the uploaded files is invalid, expected 'application/octect-stream mime type file "));
    
  
  G::LoadThirdParty( 'pear/Archive','Tar');
  $tar = new Archive_Tar ( $path. $filename);
  $sFileName  = substr($filename,0,strrpos($filename, '.' ));
  $sClassName = substr($filename,0,strpos($filename, '-' ));

  $aFiles = $tar->listContent();
  $bMainFile = false;
  $bClassFile = false;
  foreach ( $aFiles as $key => $val ) {
    if ( $val['filename'] == $sClassName . '.php' ) $bMainFile = true;
    if ( $val['filename'] == $sClassName . PATH_SEP . 'class.' . $sClassName . '.php' ) $bClassFile = true;
  }
  if ( $bMainFile && $bClassFile ) {
    $res = $tar->extract ( PATH_PLUGINS );
  }  
  else
    throw ( new Exception ( "The file $filename doesn't contain class: $sClassName ") ) ;
    
  //print "change to ENABLED";
  $oPluginRegistry =& PMPluginRegistry::getSingleton();

  $pluginFile = $sClassName . '.php';
  if ( !file_exists ( PATH_PLUGINS . $sClassName . '.php' ) ) throw ( new Exception( "File '$pluginFile' doesn't exists ") );
  
  require_once ( PATH_PLUGINS . $pluginFile );
  $details = $oPluginRegistry->getPluginDetails( $pluginFile );
  
  $oPluginRegistry->installPlugin( $details->sNamespace);
  $oPluginRegistry->setupPlugins(); //get and setup enabled plugins
  $size = file_put_contents  ( PATH_DATA_SITE . 'plugin.singleton', $oPluginRegistry->serializeInstance() );
    
  G::header ( 'Location: pluginsList');

}
catch ( Exception $e ){
  $G_PUBLISH = new Publisher;
	$aMessage['MESSAGE'] = $e->getMessage();
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
  G::RenderPage('publish');
}
