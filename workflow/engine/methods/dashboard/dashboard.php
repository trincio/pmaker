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
if (($RBAC_Response=$RBAC->userCanAccess("PM_SETUP"))!=1) return $RBAC_Response;
  
  $G_MAIN_MENU = "processmaker";
  $G_SUB_MENU  = "dashboard";
  
  $G_ID_MENU_SELECTED     = "DASHBOARD";
  
  
  $dbc = new DBConnection;
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->SetTo( $dbc );
  $G_PUBLISH->AddContent( "view", "setup/tree_setupEnvironment" );
  
  $oTemplatePower = new TemplatePower(PATH_TPL . 'dashboard/frontend.html');
  
  $oTemplatePower->prepare();

$G_PUBLISH = new Publisher;
$G_PUBLISH->AddContent('template', '', '', '', $oTemplatePower);
$G_HEADER->addInstanceModule('leimnud', 'dashboard');
$G_HEADER->addScriptCode( '
	leimnud.event.add(window,"load",function(){
		var Da=new leimnud.module.dashboard();
		Da.make({
			target:$("dashboard"),
data:[
	[{title:"My info - Page editor",open:{url:"http://wilmer.opensource.colosa.net/sysos/en/blank/users/myInfo"},height:730}],
	[{title:"esto",url:"http://maborak.com"},{title:"otro",url:"http://rss.maborak.com"}]
]
		});
	});' );



//  $G_PUBLISH->AddContent( "xmlform", "paged-table2", "setup/Holiday","", "" , "../gulliver/paged-TableAjax.php" );
  
  G::RenderPage( 'publish' );
