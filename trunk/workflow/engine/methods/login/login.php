<?php
/**
 * login.php
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
if (!isset($_SESSION['G_MESSAGE']))
{
	$_SESSION['G_MESSAGE'] = '';
}
if (!isset($_SESSION['G_MESSAGE_TYPE']))
{
	$_SESSION['G_MESSAGE_TYPE'] = '';
}

$msg     = $_SESSION['G_MESSAGE'];
$msgType = $_SESSION['G_MESSAGE_TYPE'];



//log by Everth

  require_once 'classes/model/LoginLog.php';
  require_once 'classes/model/Configuration.php';
  $oConfiguration = new Configuration();																																																		
	$oCriteria      = new Criteria('workflow');
	$oCriteria->addSelectColumn(ConfigurationPeer::CFG_VALUE);																											
	$oCriteria->add(ConfigurationPeer::CFG_UID, 'Language');		  
	$oCriteria->add(ConfigurationPeer::OBJ_UID, '');																																																						
  $oCriteria->add(ConfigurationPeer::PRO_UID, '');																											
  $oCriteria->add(ConfigurationPeer::USR_UID, '');																											
  $oCriteria->add(ConfigurationPeer::APP_UID, ''); 	 
  $oDataset1 = ConfigurationPeer::doSelectRS($oCriteria);
  $oDataset1->setFetchmode(ResultSet::FETCHMODE_ASSOC);
  $oDataset1->next();   		    																											
  $aRow1 = $oDataset1->getRow();
  $aFields['USER_LANG'] = $aRow1['CFG_VALUE'];
  
  $oCriteria = new Criteria('workflow');
  $oCriteria->add(LoginLogPeer::LOG_SID, session_id());
  $oCriteria->add(LoginLogPeer::USR_UID, isset($_SESSION['USER_LOGGED']) ? $_SESSION['USER_LOGGED'] : '-');
  $oCriteria->add(LoginLogPeer::LOG_STATUS, 'ACTIVE');
  $oCriteria->add(LoginLogPeer::LOG_END_DATE, NULL, Criteria::ISNULL);
  $oDataset = LoginLogPeer::doSelectRS($oCriteria);
  $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
  $oDataset->next();
  $aRow = $oDataset->getRow();   
  if($aRow)
  {	if($aRow['LOG_STATUS']!='CLOSED' && $aRow['LOG_END_DATE']==NULL)
   	{  
   	  $weblog=new LoginLog();
  		$aLog['LOG_UID']            = $aRow['LOG_UID'];
  		$aLog['LOG_STATUS']					= 'CLOSED';  		  		  		  		  		
  		$aLog['LOG_IP']             = $aRow['LOG_IP'];
  		$aLog['LOG_SID']            = session_id();
  		$aLog['LOG_INIT_DATE']			= $aRow['LOG_INIT_DATE'];
  		$aLog['LOG_END_DATE']			  = date('Y-m-d H:i:s'); 
  		$aLog['LOG_CLIENT_HOSTNAME']= $aRow['LOG_CLIENT_HOSTNAME']; 
  		$aLog['USR_UID']						= $aRow['USR_UID']; 		
  		$weblog->update($aLog);
	 	}
	} 	

//end log

session_destroy();
session_start();
session_regenerate_id();

//$G_MAIN_MENU     = 'wf.login';
//$G_MENU_SELECTED = '';
if (strlen($msg) > 0 )
{
	$_SESSION['G_MESSAGE'] = $msg;
}
if (strlen($msgType) > 0 )
{
	$_SESSION['G_MESSAGE_TYPE'] = $msgType;
}

$G_PUBLISH = new Publisher;
$G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/login', '', $aFields, SYS_URI.'login/authentication.php');

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
