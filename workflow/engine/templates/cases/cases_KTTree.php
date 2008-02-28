<?php
G::LoadClass('tree');
$oTree           = new Tree();
$oTree->name     = 'KT';
$oTree->showSign = false;

/*$oNode        =& $oTree->addChild('1', '<a href="#" onclick="showProcessInformation();return false;">' . G::LoadTranslation('ID_PROCESS_INFORMATION') . '</a>');
$oNode->plus  = '';
$oNode->minus = '';
$oNode->point = '';*/

echo $oTree->render();
?>