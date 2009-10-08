<?php
/**
 * users_ReassignCases.php
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
try {
  global $G_PUBLISH;
  $G_PUBLISH = new Publisher();
  $_GET['iStep'] = (int)$_GET['iStep'];
  switch ($_GET['iStep']) {
    case 1:
      $G_PUBLISH->AddContent('xmlform', 'xmlform', 'users/users_ReassignSelectType', '', array('USR_UID' => $_GET['USR_UID']), '');
    break;
    case 2:
      switch ($_POST['TYPE']) {
        case 'ANY_USER':
          $G_PUBLISH->AddContent('xmlform', 'xmlform', 'users/users_ReassignSelectSubType', '', $_POST, '');
        break;
      }
    break;
    case 3:
      switch ($_POST['SUB_TYPE']) {
        case 'PROCESS':
          //
          //$G_PUBLISH->AddContent('propeltable', 'paged-table', 'users/users_', $oCriteria, '');
        break;
      }
    break;
  }
  G::RenderPage('publish', 'raw');
}
catch (Exception $oException) {
	die($oException->getMessage());
}
?>