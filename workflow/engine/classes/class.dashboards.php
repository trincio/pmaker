<?php
/**
 * class.dashboards.php
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

/**
 * Dashboards - Dashboard class
 * @package ProcessMaker
 * @author Julio Cesar Laura Avendaño
 * @copyright 2007 COLOSA
 */

class Dashboards {
	function getConfiguration($sUserUID) {
	  require_once 'classes/model/Configuration.php';
    $oConfiguration = new Configuration();
    $sDelimiter     = DBAdapter::getStringDelimiter();
    $oCriteria      = new Criteria('workflow');
    $oCriteria->add(ConfigurationPeer::CFG_UID, 'Dashboards');
    $oCriteria->add(ConfigurationPeer::OBJ_UID, '');
    $oCriteria->add(ConfigurationPeer::PRO_UID, '');
    $oCriteria->add(ConfigurationPeer::USR_UID, $sUserUID);
    $oCriteria->add(ConfigurationPeer::APP_UID, '');
    if (ConfigurationPeer::doCount($oCriteria) == 0) {
      $oConfiguration->create(array('CFG_UID' => 'Dashboards', 'OBJ_UID' => '', 'CFG_VALUE' => '', 'PRO_UID' => '', 'USR_UID' => $sUserUID, 'APP_UID' => ''));
      $aConfiguration = array();
    }
    else {
      $aConfiguration = $oConfiguration->load('Dashboards', '', '', $sUserUID, '');
      if ($aConfiguration['CFG_VALUE'] != '') {
        $aConfiguration = unserialize($aConfiguration['CFG_VALUE']);
      }
      else {
        $aConfiguration = array();
      }
    }
    return $aConfiguration;
	}

	function saveConfiguration($sUserUID, $aConfiguration) {
    require_once 'classes/model/Configuration.php';
    $oConfiguration = new Configuration();
    $oConfiguration->update(array('CFG_UID'   => 'Dashboards',
                                  'OBJ_UID'   => '',
                                  'CFG_VALUE' => serialize($aConfiguration),
                                  'PRO_UID'   => '',
                                  'USR_UID'   => $sUserUID,
                                  'APP_UID'   => ''));
  }

  function getDashboardsObject($sUserUID) {
    $aConfiguration       = $this->getConfiguration($sUserUID);
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
          $oChart->class = $sDashboardClass;
          $oChart->chart = $sChart;
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
    $oJSON       = new Services_JSON();
    return $oJSON->encode($aDashboards);
  }
}