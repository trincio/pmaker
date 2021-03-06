<?php
/**
 * setup.php
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

  if ($RBAC->userCanAccess('PM_SETUP') == 1) {
    $G_TMP_MENU->AddIdRawOption('ADDITIONAL_TABLES', 'additionalTables/additionalTablesList', G::LoadTranslation('ID_ADDITIONAL_TABLES'));
  }
  if ($RBAC->userCanAccess('PM_SETUP_ADVANCE') == 1) {
    $G_TMP_MENU->AddIdRawOption('LANGUAGES',      'setup/languages',   G::LoadTranslation('ID_LANGUAGES'));
    $G_TMP_MENU->AddIdRawOption('PLUGINS',        'setup/pluginsList', 'Plugins');
    $G_TMP_MENU->AddIdRawOption('UPGRADE',        'setup/upgrade',     G::LoadTranslation('ID_UPGRADE'));
  }
  $G_TMP_MENU->AddIdRawOption('EMAILS',         'setup/emails',      G::LoadTranslation('ID_EMAIL'));
  $G_TMP_MENU->AddIdRawOption('WEBSERVICES',    'setup/webServices', G::LoadTranslation('ID_WEB_SERVICES') );
  $G_TMP_MENU->AddIdRawOption('SKINS',          'setup/skinsList', G::LoadTranslation('ID_SKINS') );
  //$G_TMP_MENU->AddIdRawOption('SELFSERVICE',    'setup/selfService', G::LoadTranslation('ID_SELF_SERVICE') );
  //$G_TMP_MENU->AddIdRawOption('TRANSLATION', 'tools/translations', 'Translations');
  //$G_TMP_MENU->AddIdRawOption('UPDATE_ALL',  'tools/updateTranslation', 'Update');