<?php
/**
 * $Id$
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * You can contact Colosa Inc, 2655 Le Jeune Road, Suite 1112, Coral Gables, 
 * FL 33134, USA or email info@colosa.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * ProcessMaker" logo and retain the original copyright notice. If the display
 * of the logo is not reasonably feasible for technical reasons, the
 * Appropriate Legal Notices must display the words "Powered by ProcessMaker"
 * and retain the original copyright notice.
 * -
 */
if (($RBAC_Response=$RBAC->userCanAccess("PM_CASES"))!=1) return $RBAC_Response;
/*$_SESSION['PROCESS']     = '546E6B2FFDA1BF';
$_SESSION['APPLICATION'] = 1;
$_SESSION['DELEGATION']  = 1;
$_SESSION['STEP']        = 1;
G::LoadClass('step');
$oStep = new Step(new DBConnection());
var_dump($oStep->getNextStep($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['DELEGATION'], $_SESSION['STEP']));*/
/*G::LoadClass('appDocument');
$appDocument = new AppDocument(new DBConnection());
$aFields['APP_DOC_UID']      = '246EEB4A78D562';
$aFields['PRO_UID']          = '1';
$aFields['APP_UID']          = '1';
$aFields['DEL_INDEX']        = 1;
$aFields['DOC_UID']          = '1';
$aFields['DOC_TYPE']         = 'INPUT';
$aFields['CREATE_DATE']      = date('Y-m-d');
$aFields['APP_DOC_TITLE']    = 'TITLE';
$aFields['APP_DOC_FILENAME'] = 'FILENAME';*/
//var_dump($appDocument->save($aFields));
//var_dump($appDocument->load('246EEB4A78D562'));
//var_dump($appDocument->delete('246EEB4A78D562'));*/
/*G::LoadClass('pmScript');
$oPMScript = new PMScript();
$oPMScript->setFields(array('A' => 1, 'B' => 2));*/
//$oPMScript->setScript('@@C = @@A + @@B;');
//$oPMScript->setScript('@%C = @%A + @%B;');
//$oPMScript->setScript('@#C = @#A + @#B;');
//$oPMScript->setScript('@#C = @?A;');
//$oPMScript->setScript('@#C = @$A;');
//$oPMScript->setScript("@%C = @%A + @%B;\n@%C = @%C * 2;");
//$oPMScript->execute();
//var_dump($oPMScript->aFields);
/*$oPMScript->setScript("@@A != @@B");
var_dump($oPMScript->evaluate());*/
G::LoadClass('appDelegation');
$oAppDelegation = new AppDelegation(new DBConnection());
//var_dump($oAppDelegation->load(1, 1));
//var_dump($oAppDelegation->delete(1, 1));
$aFields['APP_UID']           = '2';
$aFields['PRO_UID']           = '1';
//$aFields['DEL_INDEX']         = '1';
$aFields['DEL_PREVIOUS']      = '1';
$aFields['TAS_UID']           = '1';
$aFields['USR_UID']           = '1';
$aFields['DEL_TYPE']          = '1';
$aFields['DEL_PRIORITY']      = '1';
$aFields['DEL_THREAD']        = '1';
$aFields['DEL_THREAD_STATUS'] = '1';
$aFields['DEL_DELEGATE_DATE'] = date('Y-m-d');
$aFields['DEL_INIT_DATE']     = date('Y-m-d');
$aFields['DEL_TASK_DUE_DATE'] = date('Y-m-d');
$aFields['DEL_FINISH_DATE']   = date('Y-m-d');
$aFields['APP_MESS_FAILED']   = '1';
var_dump($oAppDelegation->save($aFields));
?>