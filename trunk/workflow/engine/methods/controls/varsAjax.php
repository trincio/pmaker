<?php
/**
 * varsAjax.php
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
G::LoadClass('xmlfield_InputPM');
$aFields = getDynaformsVars($_POST['sProcess']);
$sHTML   = '<select name="_Var_Form_" id="_Var_Form_" size="' . count($aFields) . '" style="width:100%;height:50%;" ondblclick="insertFormVar(\'' . $_POST['sFieldName'] . '\', this.value);">';
foreach ($aFields as $aField) {
	$sHTML .= '<option value="' . $_POST['sSymbol'] . $aField['sName'] . '">' . $_POST['sSymbol'] . $aField['sName'] . ' (' . $aField['sType'] . ')</option>';
}
$sHTML .= '</select>';
$sHTML .= '<table>';
$sHTML .= '<tr><td align="center" class="module_app_input___gray">' . G::LoadTranslation('ID_DOCLICK') . '</td></tr>';
$sHTML .= '<tr><td class="module_app_input___gray">' . G::LoadTranslation('ID_ESC') . '</td></tr>';
$sHTML .= '<tr><td class="module_app_input___gray">' . G::LoadTranslation('ID_NONEC') . '</td></tr>';
$sHTML .= '<tr><td class="module_app_input___gray">' . G::LoadTranslation('ID_EURL') . '</td></tr>';
$sHTML .= '<tr><td class="module_app_input___gray">' . G::LoadTranslation('ID_EVAL') . '</td></tr>';
$sHTML .= '<tr><td class="module_app_input___gray">' . G::LoadTranslation('ID_ESCJS') . '</td></tr>';
$sHTML .= '<tr><td class="module_app_input___gray">' . G::LoadTranslation('ID_ESCSJS') . '</td></tr>';
$sHTML .= '<tr><td class="module_app_input___gray">' . G::LoadTranslation('ID_FUNCTION') . '</td></tr>';
$sHTML .= '</table>';
echo $sHTML;
?>