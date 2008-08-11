<?php
/**
 * cases.php
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
 GLOBAL $RBAC;
  global $G_TMP_MENU;

  $G_TMP_MENU->AddIdRawOption('CASES_TO_DO', 'cases/cases_List?l=to_do', G::LoadTranslation('ID_TO_DO'));
  $G_TMP_MENU->AddIdRawOption('CASES_DRAFT', 'cases/cases_List?l=draft', G::LoadTranslation('ID_DRAFT'));
  $G_TMP_MENU->AddIdRawOption('CASES_PAUSED', 'cases/cases_List?l=paused', G::LoadTranslation('ID_PAUSED'));
  $G_TMP_MENU->AddIdRawOption('CASES_CANCELLED', 'cases/cases_List?l=cancelled', G::LoadTranslation('ID_CANCELLED'));
  $G_TMP_MENU->AddIdRawOption('CASES_COMPLETED', 'cases/cases_List?l=completed', G::LoadTranslation('ID_COMPLETED'));
  $G_TMP_MENU->AddIdRawOption('CASES_ALL', 'cases/cases_List?l=all', G::LoadTranslation('ID_ALL'));
  if($RBAC->userCanAccess('PM_ALLCASES') == 1) {
    $G_TMP_MENU->AddIdRawOption('CASES_GRAL', 'cases/cases_List?l=gral', G::LoadTranslation('ID_GENERAL'));
  }
  if($RBAC->userCanAccess('PM_SUPERVISOR') == 1) {
    $G_TMP_MENU->AddIdRawOption('CASES_TO_REVISE', 'cases/cases_List?l=to_revise', G::LoadTranslation('ID_TO_REVISE'));
  }
?>
