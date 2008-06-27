<?php
/**
 * dashboardAjax.php
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

G::LoadClass('dashboards');
$oDashboards = new Dashboards();

switch ($_POST['action']) {
	case 'showAvailableDashboards':
	  $aConfiguration     = $oDashboards->getConfiguration($_SESSION['USER_LOGGED']);
	  $aAvailableCharts   = array();
    $aAvailableCharts[] = array('DASH_CODE'  => 'char',
                                'DASH_LABEL' => 'char');
	  //Load available ProcessMaker reports
	  G::LoadClass('report');
	  $oReport  = new Report();
	  $aReports = $oReport->getAvailableReports();
    foreach ($aReports as $sReport) {
      $bFree = true;
      foreach ($aConfiguration as $aDashboard) {
        if (($aDashboard['class'] == 'PM_Reports') && ($aDashboard['type'] == $sReport)) {
          $bFree = false;
        }
      }
      if ($bFree) {
        $aAvailableCharts[] = array('DASH_CODE'  => 'PM_Reports^' . $sReport,
                                    'DASH_LABEL' => 'PM_Reports - ' . G::LoadTranslation($sReport));
      }
    }
    //Load available charts
    $oPluginRegistry      = &PMPluginRegistry::getSingleton();
    $aAvailableDashboards = $oPluginRegistry->getDashboards();
    foreach ($aAvailableDashboards as $sDashboardClass) {
      require_once PATH_PLUGINS. $sDashboardClass  . PATH_SEP . 'class.' . $sDashboardClass . '.php';
      $sClassName = $sDashboardClass . 'Class';
      $oInstance  = new $sClassName();
      $aCharts    = $oInstance->getAvailableCharts();
      foreach ($aCharts as $sChart) {
        $bFree = true;
        foreach ($aConfiguration as $aDashboard) {
          if (($aDashboard['class'] == $sDashboardClass) && ($aDashboard['type'] == $sChart)) {
            $bFree = false;
          }
        }
        if ($bFree) {
          $oChart = $oInstance->getChart($sChart);
          $aAvailableCharts[] = array('DASH_CODE'  => $sDashboardClass . '^' . $sChart,
                                      'DASH_LABEL' => $sDashboardClass . ' - ' . $oChart->title);
        }
      }
    }
    //Set DBArray
    global $_DBArray;
    $_DBArray['AvailableCharts'] = $aAvailableCharts;
    $_SESSION['_DBArray']        = $_DBArray;
    //Show form
    global $G_PUBLISH;
  	$G_PUBLISH = new Publisher();
    if (count($aAvailableCharts) > 1) {
      $G_PUBLISH->AddContent('xmlform', 'xmlform', 'dashboard/dashboard_AvailableDashboards');
    }
    else {
      $G_PUBLISH->AddContent('xmlform', 'xmlform', 'dashboard/dashboard_NoAvailableDashboards');
    }
    G::RenderPage('publish', 'raw');
	break;
	case 'addDashboard':
	  if ($_POST['sDashboardClass'] == 'PM_Reports') {
	    $oObject            = new StdClass();
	    $oObject->title     = G::LoadTranslation($_POST['sChart']);
	    $oObject->height    = 220;
	    $oObject->open      = new StdClass();
	    $oObject->open->url = '/sys' . SYS_SYS . '/' . SYS_LANG . '/blank/reports/reports_Dashboard?sType=' . $_POST['sChart'];
	    $aConfiguration = $oDashboards->getConfiguration($_SESSION['USER_LOGGED']);
	    $aConfiguration[] = array('class'  => $_POST['sDashboardClass'],
	                              'type'   => $_POST['sChart'],
	                              'object' => $oObject,
	                              'config' => '');
	  }
	  else {
	    require_once PATH_PLUGINS. $_POST['sDashboardClass']  . PATH_SEP . 'class.' . $_POST['sDashboardClass'] . '.php';
      $sClassName = $_POST['sDashboardClass'] . 'Class';
      $oInstance  = new $sClassName();
	    $aConfiguration = $oDashboards->getConfiguration($_SESSION['USER_LOGGED']);
	    $aConfiguration[] = array('class'  => $_POST['sDashboardClass'],
	                              'type'   => $_POST['sChart'],
	                              'object' => $oInstance->getChart($_POST['sChart']),
	                              'config' => '');
    }
    $oDashboards->saveConfiguration($_SESSION['USER_LOGGED'], $aConfiguration);
	  echo 'oDashboards = ' . $oDashboards->getDashboardsObject($_SESSION['USER_LOGGED']) . ';';
	break;
	case 'removeDashboard':
	  $aConfiguration    = $oDashboards->getConfiguration($_SESSION['USER_LOGGED']);
	  $aNewConfiguration = array();
	  foreach ($aConfiguration as $aDashboard) {
	    if (($aDashboard['class'] == $_POST['sDashboardClass']) && ($aDashboard['type'] == $_POST['sChart'])) {
        //Nothing
      }
      else {
        $aNewConfiguration[] = $aDashboard;
      }
	  }
	  $oDashboards->saveConfiguration($_SESSION['USER_LOGGED'], $aNewConfiguration);
	  echo 'oDashboards = ' . $oDashboards->getDashboardsObject($_SESSION['USER_LOGGED']) . ';';
	break;
}