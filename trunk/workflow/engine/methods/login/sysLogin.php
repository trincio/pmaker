<?php
/**
 * sysLogin.php
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
if (isset($_POST['form']['USER_ENV'])) {
  session_start();
  $_SESSION['sysLogin'] = $_POST['form'];
  G::header('location: /sys' . $_POST['form']['USER_ENV'] . '/'.SYS_LANG.'/'.SYS_SKIN.'/login/sysLoginVerify'); 
  die;
}

//Required classes for dbArray work
require_once ( "propel/Propel.php" );
require_once ( "creole/Creole.php" );
G::LoadThirdParty("pake","pakeColor.class");
Propel::init( PATH_CORE . "config/databases.php" );
Creole::registerDriver('dbarray', 'creole.contrib.DBArrayConnection');


function getLangFiles(){
    $dir=PATH_LANGUAGECONT;
	  $filesArray=array();
	  if (file_exists($dir)){
  	  if ($handle = opendir($dir)) {
          while (false !== ($file = readdir($handle))) {
              
              $fileParts=explode(".",$file);
              if($fileParts[0]=="translation"){
                $filesArray[$fileParts[1]]=$file;
              }
          }
          closedir($handle);
      }
    }
    return $filesArray;
}

function getWorkspacesAvailable(){
    $dir=PATH_DB;
	  $filesArray=array();
	  if (file_exists($dir)){
  	  if ($handle = opendir($dir)) {
          while (false !== ($file = readdir($handle))) {
              if(($file!=".")&&($file!="..")){
              	if(file_exists(PATH_DB .  $file . '/db.php')){
                	$filesArray[]=$file;
              	}
              }
          }
          closedir($handle);
      }
    }
    sort($filesArray,SORT_STRING );
    return $filesArray;
}
$availableWorkspace=getWorkspacesAvailable();

$availableLang=getLangFiles();

$langISONames["en"]="English";
$langISONames["es"]="Spanish";

$availableLangArray = array();
$availableLangArray[] = array('LANG_ID' => 'char', 'LANG_NAME' => 'char');
foreach($availableLang as $langKey => $langFile){
    $aFields = array('LANG_ID' => $langKey, 'LANG_NAME' => isset($langISONames[$langKey])?$langISONames[$langKey]:$langKey);
    $availableLangArray[] = $aFields;
}

$availableWorkspaceArray = array();
$availableWorkspaceArray[] = array('ENV_ID' => 'char', 'ENV_NAME' => 'char');
foreach($availableWorkspace as $envKey => $envName){
    $aFields = array('ENV_ID' => $envName, 'ENV_NAME' => $envName);
    $availableWorkspaceArray[] = $aFields;
}

global $_DBArray;

$_DBArray['langOptions'] = $availableLangArray;
$_DBArray['availableWorkspace'] = $availableWorkspaceArray;

$_SESSION['_DBArray'] = $_DBArray;


$aField['LOGIN_VERIFY_MSG'] = G::loadTranslation('LOGIN_VERIFY_MSG'); 

$G_PUBLISH = new Publisher;
$G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/sysLogin', '', $aField, 'sysLogin');

G::RenderPage( "publish" );

?>
<script type="text/javascript">
    var openInfoPanel = function()
    {
      var oInfoPanel = new leimnud.module.panel();
      oInfoPanel.options = {
        size    :{w:500,h:424},
        position:{x:0,y:0,center:true},
        title   :'System Information',
        theme   :'processmaker',
        control :{
          close :true,
          drag  :false
        },
        fx:{
          modal:true
        }
      };
      oInfoPanel.setStyle = {modal: {
        backgroundColor: 'white'
      }};
      oInfoPanel.make();
      var oRPC = new leimnud.module.rpc.xmlhttp({
        url   : '../login/dbInfo',
        async : false,
        method: 'POST',
        args  : ''
      });
      oRPC.make();
      oInfoPanel.addContent(oRPC.xmlhttp.responseText);
    };
</script>