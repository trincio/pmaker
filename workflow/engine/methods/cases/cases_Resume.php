<?php
/**
 * cases_Resume.php
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
 /* Permissions */
  switch ($RBAC->userCanAccess('PM_CASES'))
  {
  	case -2:
  	  G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels');
  	  G::header('location: ../login/login');
  	  die;
  	break;
  	case -1:
  	  G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
  	  G::header('location: ../login/login');
  	  die;
  	break;
  }

  /* Includes */
  G::LoadClass('case');

  /* GET , POST & $_SESSION Vars */

  /* Menues */
  $_SESSION['bNoShowSteps'] = true;
  $G_MAIN_MENU              = 'processmaker';
  $G_SUB_MENU               = 'caseOptions';
  $G_ID_MENU_SELECTED       = 'CASES';
  $G_ID_SUB_MENU_SELECTED   = '_';

 /* Prepare page before to show */
  $oCase = new Cases();
  $Fields = $oCase->loadCase( $_SESSION['APPLICATION'], $_SESSION['INDEX'] );
  if (isset($aRow['APP_TYPE'])) {
    switch ($aRow['APP_TYPE']) {
      case 'PAUSE':
        $Fields['STATUS'] = ucfirst(strtolower(G::LoadTranslation('ID_PAUSED')));
      break;
      case 'CANCEL':
        $Fields['STATUS'] = ucfirst(strtolower(G::LoadTranslation('ID_CANCELLED')));
      break;
    }
    //$Fields['STATUS'] = $aRow['APP_TYPE'];
  }

  /* Render page */
  $oHeadPublisher =& headPublisher::getSingleton();
  $oHeadPublisher->addScriptCode('
  var Cse = {};
  Cse.panels = {};
  var leimnud = new maborak();
  leimnud.make();
  leimnud.Package.Load("rpc,drag,drop,panel,app,validator,fx,dom,abbr",{Instance:leimnud,Type:"module"});
  leimnud.Package.Load("json",{Type:"file"});
  leimnud.Package.Load("cases",{Type:"file",Absolute:true,Path:"/jscore/cases/core/cases.js"});
  leimnud.Package.Load("cases_Step",{Type:"file",Absolute:true,Path:"/jscore/cases/core/cases_Step.js"});
  leimnud.Package.Load("processmap",{Type:"file",Absolute:true,Path:"/jscore/processmap/core/processmap.js"});
  leimnud.exec(leimnud.fix.memoryLeak);
  /*leimnud.event.add(window,"load",function(){
	  '.(isset($_SESSION['showCasesWindow'])?'try{'.$_SESSION['showCasesWindow'].'}catch(e){}':'').'
});*/
  ');
  $oHeadPublisher =& headPublisher::getSingleton();
  $oHeadPublisher->addScriptFile('/jscore/cases/core/cases_Step.js');
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'cases/cases_Resume.xml', '', $Fields, '');
  G::RenderPage( 'publish' );
