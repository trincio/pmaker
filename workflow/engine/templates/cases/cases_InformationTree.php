<?php
G::LoadClass('tree');
$oTree           = new Tree();
$oTree->nodeType ="blank";
$oTree->name     = 'Information';
$oTree->showSign = false;

$oNode        =& $oTree->addChild('1', '<a class="linkInBlue" href="#" onclick="showProcessMap();return false;">' . G::LoadTranslation('ID_PROCESS_MAP') . '</a>', array('nodeType'=>'parentBlue'));
$oNode->plus  = '';
$oNode->minus = '';
$oNode->point = '';

$oNode        =& $oTree->addChild('1', '<a class="linkInBlue" href="#" onclick="showProcessInformation();return false;">' . G::LoadTranslation('ID_PROCESS_INFORMATION') . '</a>', array('nodeType'=>'parentBlue'));
$oNode->plus  = '';
$oNode->minus = '';
$oNode->point = '';

$oNode        =& $oTree->addChild('2', '<a class="linkInBlue" href="#" onclick="showTaskInformation();return false;">' . G::LoadTranslation('ID_TASK_INFORMATION') . '</a>', array('nodeType'=>'parentBlue'));
$oNode->plus  = '';
$oNode->minus = '';
$oNode->point = '';

$oNode        =& $oTree->addChild('3', '<a class="linkInBlue" href="#" onclick="showTransferHistory();return false;">' . G::LoadTranslation('ID_CASE_HISTORY') . '</a>', array('nodeType'=>'parentBlue'));
$oNode->plus  = '';
$oNode->minus = '';
$oNode->point = '';

echo $oTree->render();
?>
