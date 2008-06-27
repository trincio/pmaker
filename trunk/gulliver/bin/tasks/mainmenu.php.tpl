<?php
/**
 * {projectName}.php
 *
 */
global $G_TMP_MENU;
global $RBAC;

$G_TMP_MENU->AddIdRawOption('USERS',    'users/users_List');
$G_TMP_MENU->AddIdRawOption('REPORTS',  'reports/reportsList');
$G_TMP_MENU->AddIdRawOption('SETUP',    'setup/pluginsList');
$G_TMP_MENU->AddIdRawOption('WELCOME',  'login/welcome');

$G_TMP_MENU->Labels = array(
  G::LoadTranslation('ID_USERS'),
  G::LoadTranslation('ID_REPORTS'),
  G::LoadTranslation('ID_SETUP'),
  G::LoadTranslation('ID_WELCOME')

);

if ( file_exists ( PATH_CORE . 'menus/plugin.php' ) ) {
	require_once ( PATH_CORE . 'menus/plugin.php' );
}

/*
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

*/