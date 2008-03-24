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
if (($RBAC_Response=$RBAC->userCanAccess("PM_SETUP"))!=1) return $RBAC_Response;
  
  $G_MAIN_MENU = "processmaker";
  //$G_SUB_MENU  = "dashboard";
  
  $G_ID_MENU_SELECTED     = "DASHBOARD";
  
  $prePath = '/sys' . SYS_SYS . '/' . SYS_LANG . '/blank/';

  $oJSON   = new Services_JSON();
  $obj = new stdClass();
  $obj->title = 'my first chart';
  $obj->height = 220;
  $obj->open->image = $prePath . 'charts/genericCharts?chart=2&type=4';
  $aColumn1[] = $obj;
  
  $obj = new stdClass();
  $obj->title = 'my first chart';
  $obj->height = 220;
  $obj->open->image = $prePath . 'charts/genericCharts?chart=1&type=4';
  $aColumn1[] = $obj;

  $obj = new stdClass();
  $obj->title = 'my first chart';
  $obj->height = 300;
  $obj->open->image = $prePath . 'charts/genericCharts?chart=2&type=3';
  $aColumn1[] = $obj;

  $obj = new stdClass();
  $obj->title = 'my first chart';
  $obj->height = 300;
  $obj->open->image = $prePath . 'charts/genericCharts?chart=0';
  $aColumn1[] = $obj;

  $obj = new stdClass();
  $obj->title = 'my first chart';
  $obj->height = 220;
  $obj->open->image = $prePath . 'charts/genericCharts?type=2';
  $aColumn1[] = $obj;
  
  $obj = new stdClass();
  $obj->title = 'my first chart';
  $obj->height = 220;
  $obj->open->url = $prePath . 'openFlash/chart?type=1&chart=1&u=';
  $aColumn2[] = $obj;

  $obj = new stdClass();
  $obj->title = 'my first chart';
  $obj->height = 220;
  $obj->open->url = $prePath . 'openFlash/chart?type=2&chart=2&u=';
  $aColumn2[] = $obj;

  $obj = new stdClass();
  $obj->title = 'my first chart';
  $obj->height = 220;
  $obj->open->url = $prePath . 'openFlash/chart?type=1&chart=1&u=';
  $aColumn2[] = $obj;

  $obj = new stdClass();
  $obj->title = 'my first chart';
  $obj->height = 220;
  $obj->open->url = $prePath . 'openFlash/chart?type=1&chart=2&u=';
  $aColumn2[] = $obj;

  $aDashboard = array ( $aColumn1, $aColumn2 );
  $oData   = $oJSON->encode( $aDashboard );
  
  $oTemplatePower = new TemplatePower(PATH_TPL . 'dashboard/frontend.html');
  
  $oTemplatePower->prepare();

/*
[
	[{title:"My info - Page editor",open:{url:"/sysos/en/blank/users/myInfo"},height:730,noBg:true}],
[
{title:"My pending Process",open:{html:"<img src=\"/sysos/en/blank/dashboard/chart\" />"},height:400},
{title:"Status4",url:"http://rss.maborak.com",height:100,noBg:true}
]

]*/

$scriptCode = 'leimnud.event.add(window,"load",function(){
		window.Da=new leimnud.module.dashboard();
		Da.make({
			target:$("dashboard"),
      data: ' . $oData . ' });
	  });';

$G_PUBLISH = new Publisher;
$G_PUBLISH->AddContent('template', '', '', '', $oTemplatePower);
$G_HEADER->addInstanceModule('leimnud', 'dashboard');
$G_HEADER->addScriptCode( $scriptCode );



//  $G_PUBLISH->AddContent( "xmlform", "paged-table2", "setup/Holiday","", "" , "../gulliver/paged-TableAjax.php" );
  
  G::RenderPage( 'publish' );
