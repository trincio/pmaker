<?php
/**
 * languages_Import.php
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
try {
  $sMaxExecutionTime = ini_get('max_execution_time');
  ini_set('max_execution_time', '0');
  G::LoadClass('languages');
  $oLanguages = new languages();
  $oLanguages->importLanguage($_FILES['form']['tmp_name']['LANGUAGE_FILENAME']);
  ini_set('max_execution_time', $sMaxExecutionTime);
  die('<script type="text/javascript">parent.window.location = parent.window.location.href;</script>');
}
catch (Exception $oError) {
	var_dump($oError->getMessage());
	die();
}