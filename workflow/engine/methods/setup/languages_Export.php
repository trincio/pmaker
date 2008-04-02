<?php
/**
 * languages_Export.php
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
require_once 'classes/model/Language.php';
require_once 'classes/model/Translation.php';
$aLabels = array();
$aMsgids = array();
$oCriteria = new Criteria('workflow');
$oCriteria->addSelectColumn(TranslationPeer::TRN_CATEGORY);
$oCriteria->addSelectColumn(TranslationPeer::TRN_ID);
$oCriteria->addSelectColumn(TranslationPeer::TRN_VALUE);
$oCriteria->add(TranslationPeer::TRN_LANG, 'en');
$oDataset = TranslationPeer::doSelectRS($oCriteria);
$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
$oDataset->next();
while ($aRow1 = $oDataset->getRow()) {
	$oCriteria = new Criteria('workflow');
	$oCriteria->addSelectColumn(TranslationPeer::TRN_VALUE);
	$oCriteria->add(TranslationPeer::TRN_CATEGORY, $aRow1['TRN_CATEGORY']);
	$oCriteria->add(TranslationPeer::TRN_ID, $aRow1['TRN_ID']);
	$oCriteria->add(TranslationPeer::TRN_LANG, $_GET['LAN_ID']);
	$oDataset2 = TranslationPeer::doSelectRS($oCriteria);
  $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
  $oDataset2->next();
  $aRow2 = $oDataset2->getRow();
  $msgid = $aRow1['TRN_VALUE'];
  if (in_array($msgid, $aMsgids)) {
    $msgid = '[' . $aRow1['TRN_CATEGORY'] . '/' . $aRow1['TRN_ID'] . '] ' . $msgid;
  }
	$aLabels[] = array(0 => '#: TRANSLATION/' . $aRow1['TRN_CATEGORY'] . '/' . $aRow1['TRN_ID'],
                     1 => '# TRANSLATION/' . $aRow1['TRN_CATEGORY'] . '/' . $aRow1['TRN_ID'],
                     2 => '#',
                     3 => 'msgid "' . $msgid . '"',
                     4 => 'msgstr "' . ($aRow2 ? $aRow2['TRN_VALUE'] : $aRow1['TRN_VALUE']) . '"');
  $aMsgids[] = $msgid;
	$oDataset->next();
}
G::LoadThirdParty('pake', 'pakeFinder.class');
$aExceptionFields = array('', 'javascript', 'hidden', 'phpvariable', 'private', 'toolbar', 'xmlmenu', 'toolbutton', 'cellmark', 'grid');
$aXMLForms = pakeFinder::type('file')->name( '*.xml' )->in(PATH_XMLFORM);
foreach ($aXMLForms as $sXmlForm) {
	$sXmlForm = str_replace(PATH_XMLFORM, '', $sXmlForm);
	$oForm = new Form($sXmlForm, '', 'en');
	foreach ($oForm->fields as $sNodeName => $oNode) {
		if (trim($oNode->label) != '') {
      $aEnglishLabel[$oNode->name] = str_replace('"', '\"', stripslashes(trim(str_replace(chr(10), '', $oNode->label))));
    }
	}
	unset($oForm->fields);
  unset($oForm->tree);
  unset($oForm);
  $oForm = new Form($sXmlForm, '', $_GET['LAN_ID']);
  $i = 1;
  $iNumberOfFields = count($oForm->fields);
  foreach ($oForm->fields as $sNodeName => $oNode) {
    if (is_object($oNode) && isset ($aEnglishLabel[$oNode->name])) {
      $msgid = $aEnglishLabel[$oNode->name];
      $oNode->label = str_replace('"', '\"', stripslashes(trim(str_replace(chr(10), '', $oNode->label))));
    }
    else {
      $msgid = '';
    }
    if ((trim($msgid) != '') && !in_array(strtolower($oNode->type), $aExceptionFields)) {
    	if ((strpos($msgid, '@G::LoadTranslation') === false) && (strpos($oNode->label, '@G::LoadTranslation') === false)) {
    	  if (in_array($msgid, $aMsgids)) {
          $msgid = '[' . $sXmlForm . '?' . $oNode->name . '] ' . $msgid;
        }
        $aLabels[] = array(0 => '#: ' . $sXmlForm . '?' . $sNodeName,
                           1 => '# ' . $sXmlForm,
                           2 => '# ' . $oNode->type . ' - ' . $sNodeName,
                           3 => 'msgid "' . $msgid . '"',
                           4 => 'msgstr "' . $oNode->label . '"');
        $aMsgids[] = $msgid;
      }
    }
    $i++;
  }
  unset($oForm->fields);
  unset($oForm->tree);
  unset($oForm);
}
$oCriteria = new Criteria('workflow');
$oCriteria->addSelectColumn(LanguagePeer::LAN_NAME);
$oCriteria->add(LanguagePeer::LAN_ID, $_GET['LAN_ID']);
$oDataset = LanguagePeer::doSelectRS($oCriteria);
$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
$oDataset->next();
$aRow = $oDataset->getRow();
$sPOFile = PATH_CORE . 'content' . PATH_SEP . 'translations' . PATH_SEP . MAIN_POFILE . '.' . $_GET['LAN_ID'] . '.po';
$oFile = fopen($sPOFile, 'w');
fprintf($oFile, "msgid \"\" \n");
fprintf($oFile, "msgstr \"\" \n");
fprintf($oFile, "\"Project-Id-Version: %s\\n\"\n", PO_SYSTEM_VERSION);
fprintf($oFile, "\"POT-Creation-Date: \\n\"\n");
fprintf($oFile, "\"PO-Revision-Date: %s \\n\"\n", date('Y-m-d H:i+0100'));
fprintf($oFile, "\"Last-Translator: Fernando Ontiveros<fernando@colosa.com>\\n\"\n");
fprintf($oFile, "\"Language-Team: Colosa Developers Team <developers@colosa.com>\\n\"\n");
fprintf($oFile, "\"MIME-Version: 1.0 \\n\"\n");
fprintf($oFile, "\"Content-Type: text/plain; charset=utf-8 \\n\"\n");
fprintf($oFile, "\"Content-Transfer_Encoding: 8bit\\n\"\n");
fprintf($oFile, "\"X-Poedit-Language: %s\\n\"\n", ucwords($aRow['LAN_NAME']));
fprintf($oFile, "\"X-Poedit-Country: %s\\n\"\n", '');//Obtain country if exists, next release
fprintf($oFile, "\"X-Poedit-SourceCharset: utf-8\\n\"\n\n");
foreach ($aLabels as $aLabel) {
	fwrite($oFile, $aLabel[0] . "\n");
	fwrite($oFile, $aLabel[1] . "\n");
	if ($aLabel[2] != '') {
	  fwrite($oFile, $aLabel[2] . "\n");
  }
	fwrite($oFile, $aLabel[3] . "\n");
	fwrite($oFile, $aLabel[4] . "\n\n");
}
fclose($oFile);
G::streamFile($sPOFile, true);