<?php
/**
 * dashboard.php
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

if (($RBAC_Response=$RBAC->userCanAccess('PM_LOGIN'))!=1) return $RBAC_Response;

try {
  $G_MAIN_MENU        = 'processmaker';
  $G_SUB_MENU         = 'dashboard';
  $G_ID_MENU_SELECTED = 'DASHBOARD';

  //Obtain user dashboards configuration
  require_once 'classes/model/Configuration.php';
  $oConfiguration = new Configuration();
  $sDelimiter     = DBAdapter::getStringDelimiter();
  $oCriteria      = new Criteria('workflow');
  $oCriteria->add(ConfigurationPeer::CFG_UID, 'Dashboards');
  $oCriteria->add(ConfigurationPeer::OBJ_UID, '');
  $oCriteria->add(ConfigurationPeer::PRO_UID, '');
  $oCriteria->add(ConfigurationPeer::USR_UID, $_SESSION['USER_LOGGED']);
  $oCriteria->add(ConfigurationPeer::APP_UID, '');
  if (ConfigurationPeer::doCount($oCriteria) == 0) {
    $oConfiguration->create(array('CFG_UID' => 'Dashboards', 'OBJ_UID' => '', 'CFG_VALUE' => '', 'PRO_UID' => '', 'USR_UID' => $_SESSION['USER_LOGGED'], 'APP_UID' => ''));
    $aConfiguration = array();
  }
  else {
    $aConfiguration = $oConfiguration->load('Dashboards', '', '', $_SESSION['USER_LOGGED'], '');
    if ($aConfiguration['CFG_VALUE'] != '') {
      $aConfiguration = unserialize($aConfiguration['CFG_VALUE']);
    }
    else {
      $aConfiguration = array();
    }
  }

  //Load dashboards
  $oPluginRegistry      = &PMPluginRegistry::getSingleton();
  $aAvailableDashboards = $oPluginRegistry->getDashboards();
  $aLeftColumn          = array ();
  $aRightColumn         = array ();
  $iColumn              = 0;
  foreach ($aAvailableDashboards as $sDashboardClass) {
    require_once PATH_PLUGINS. $sDashboardClass  . PATH_SEP . 'class.' . $sDashboardClass . '.php';
    $sClassName = $sDashboardClass . 'Class';
    $oInstance  = new $sClassName();
    $aCharts    = $oInstance->getAvailableCharts();
    $iColumn    = 0;
    foreach ($aCharts as $sChart) {
      $bFree = false;
      foreach ($aConfiguration as $aDashboard) {
        if (($aDashboard['class'] == $sDashboardClass) && ($aDashboard['type'] == $sChart)) {
          $bFree = true;
        }
      }
      if ($bFree) {
        $oChart = $oInstance->getChart($sChart);
        if ($iColumn === 0) {
          $aLeftColumn[] = $oChart;
        }
        else {
          $aRightColumn[] = $oChart;
        }
        $iColumn = 1- $iColumn;
      }
    }
  }
  $aDashboards = array($aLeftColumn, $aRightColumn);
  //Show dashboards
  $oJSON       = new Services_JSON();
  $G_PUBLISH   = new Publisher;
  $G_PUBLISH->AddContent('smarty', 'dashboard/frontend', '', '', array('ID_NEW' => G::LoadTranslation('ID_NEW')));
  $G_HEADER->addScriptFile('/jscore/dashboard/core/dashboard.js');
  $G_HEADER->addInstanceModule('leimnud', 'dashboard');
  $G_HEADER->addScriptCode('leimnud.event.add(window,"load",function(){window.Da=new leimnud.module.dashboard();Da.make({target:$("dashboard"),data:' . $oJSON->encode($aDashboards) . '});});');
  G::RenderPage('publish');
}
catch ( Exception $e ) {
  $aMessage = array();
  $aMessage['MESSAGE'] = $e->getMessage();
  $G_PUBLISH           = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage);
  G::RenderPage('publish');
}