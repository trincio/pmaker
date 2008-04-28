<?php
/**
 * cases_ActionsTree.php
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
$APP_UID = $_SESSION['APPLICATION'];
require_once 'classes/model/AppThread.php';
$c = new Criteria('workflow');
$c->clearSelectColumns();
$c->addSelectColumn( AppThreadPeer::APP_THREAD_PARENT );
$c->add(AppThreadPeer::APP_UID, $APP_UID );
$c->add(AppThreadPeer::APP_THREAD_STATUS , 'OPEN' );
$cant = AppThreadPeer::doCount($c);

G::LoadClass('tree');
$oTree = new Tree();
$oTree->nodeType = "blank";
$oTree->name = 'Actions';
$oTree->showSign = false;
if($cant==1)
 $oNode = & $oTree->addChild('1', '<a class="linkInBlue" href="#" onclick="cancelCase();return false;">' . G::LoadTranslation('ID_CANCEL') . '</a>', array('nodeType'=>'parentBlue'));
else
 echo G::LoadTranslation('ID_CASECANCEL');
$oNode = &$oTree->addChild('1', '<a class="linkInBlue" href="#" onclick="pauseCase();return false;">' . G::LoadTranslation('ID_PAUSED_CASE') . '</a>', array('nodeType' => 'parentBlue'));

$oNode->plus = '';
$oNode->minus = '';
$oNode->point = '';

echo $oTree->render();

?>
