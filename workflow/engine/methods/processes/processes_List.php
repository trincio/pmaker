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
global $RBAC;
switch ($RBAC->userCanAccess('PM_FACTORY'))
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

  $G_MAIN_MENU            = 'processmaker';
  $G_ID_MENU_SELECTED     = 'PROCESSES';


  $aLabels['LANG']     = SYS_LANG;
  $aLabels['PRO_EDIT']     = G::LoadTranslation('ID_EDIT');
  $aLabels['PRO_DELETE']   = G::LoadTranslation('ID_DELETE');
  $aLabels['PRO_STATUSx']   = 'link pro_status';
  $aLabels['ACTIVE']   = G::LoadTranslation('ID_ACTIVE');
  $aLabels['INACTIVE'] = G::LoadTranslation('ID_INACTIVE');
  $aLabels['CONFIRM']  = G::LoadTranslation('ID_MSG_CONFIRM_DELETE_PROCESS');


  G::LoadClass ( 'processMap');
  $oProcess = new processMap();
  $c = $oProcess->getConditionProcessList();

function activeFalse($value)
{
    return $value=="ACTIVE"?"ID_ACTIVE":"ID_INACTIVE";
}

$G_PUBLISH = new Publisher;
$G_PUBLISH->AddContent('propeltable', 'paged-table', 'processes/processes_List', $c, $aLabels, '' );
G::RenderPage('publish');
