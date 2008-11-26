<?php
/**
 * processmaker.php
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
global $G_TMP_MENU;
global $RBAC;

//$G_TMP_MENU->AddIdRawOption('MY_ACCOUNT', 'users/myInfo');
$G_TMP_MENU->AddIdRawOption('USERS',      'users/users_List');
$G_TMP_MENU->AddIdRawOption('CASES',      'cases/cases_List');
$G_TMP_MENU->AddIdRawOption('PROCESSES',  'processes/processes_List');
$G_TMP_MENU->AddIdRawOption('REPORTS',    'reports/reportsList');
$G_TMP_MENU->AddIdRawOption('SETUP',      'setup/emails');
$G_TMP_MENU->AddIdRawOption('DASHBOARD',  'dashboard/dashboard');

$G_TMP_MENU->Labels = array(
  //G::LoadTranslation('ID_MY_ACCOUNT'),
  G::LoadTranslation('ID_USERS'),
  G::LoadTranslation('ID_CASES'),
  G::LoadTranslation('ID_APPLICATIONS'),
  G::LoadTranslation('ID_REPORTS'),
  G::LoadTranslation('ID_SETUP'),
  G::LoadTranslation('ID_DASHBOARD')

);

if ( file_exists ( PATH_CORE . 'menus/plugin.php' ) ) {
	require_once ( PATH_CORE . 'menus/plugin.php' );
}


if ($RBAC->userCanAccess('PM_LOGIN') != 1)
{
  $G_TMP_MENU->DisableOptionId('MY_ACCOUNT');
}

if ($RBAC->userCanAccess('PM_USERS') != 1)
{
  $G_TMP_MENU->DisableOptionId('USERS');
}

if ($RBAC->userCanAccess('PM_CASES') != 1)
{
  $G_TMP_MENU->DisableOptionId('CASES');
}

if ($RBAC->userCanAccess('PM_FACTORY') != 1)
{
  $G_TMP_MENU->DisableOptionId('PROCESSES');
}

if ($RBAC->userCanAccess('PM_SETUP') != 1)
{
  $G_TMP_MENU->DisableOptionId('SETUP');
}

if ($RBAC->userCanAccess('PM_REPORTS') != 1)
{
  $G_TMP_MENU->DisableOptionId('REPORTS');
}

if ($RBAC->userCanAccess('PM_DASHBOARD') != 1)
{
  $G_TMP_MENU->DisableOptionId('DASHBOARD');
}