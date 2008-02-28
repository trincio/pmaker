<?php
G::LoadClass('tree');
$oTree           = new Tree();
$oTree->nodeType	 = "blank";
$oTree->name     = 'Actions';
$oTree->showSign = false;

$oNode        =& $oTree->addChild('1', '<a class="linkInBlue" href="#" onclick="cancelCase();return false;">' . G::LoadTranslation('ID_CANCEL_CASE') . '</a>', array('nodeType'=>'parentBlue'));
$oNode->plus  = '';
$oNode->minus = '';
$oNode->point = '';

echo $oTree->render();
?>
