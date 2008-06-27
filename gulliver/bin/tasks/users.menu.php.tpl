<?php
/**
 * users.php
 *  
 */
global $G_TMP_MENU;

  $G_TMP_MENU->AddIdRawOption('USERS',      'users/users_List');
  $G_TMP_MENU->AddIdRawOption('GROUPS',     'groups/groups');
  $G_TMP_MENU->AddIdRawOption('ROLES',      'roles/roles_List');
  $G_TMP_MENU->AddIdRawOption('PERMISSIONS','roles/permissions');

  $G_TMP_MENU->Labels = array ( G::LoadTranslation('ID_USERS_LIST'), 
  G::LoadTranslation('ID_GROUP_USERS'), G::LoadTranslation('ID_ROLES'), 
  G::LoadTranslation('ID_PERMISSIONS'), );
