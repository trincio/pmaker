<?php
try {
	G::LoadClass('tree');
  $oTree           = new Tree();
  $oTree->nodeType = 'blank';
  $oTree->name     = 'Triggers';
  $oTree->showSign = false;
	$oCriteria = new Criteria('workflow');
	$oCriteria->add(StepPeer::PRO_UID, $_SESSION['PROCESS']);
  $oCriteria->add(StepPeer::TAS_UID, $_SESSION['TASK']);
  $oCriteria->addAscendingOrderByColumn(StepPeer::STEP_POSITION);
  $oDataset = StepPeer::doSelectRS($oCriteria);
  $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
  $oDataset->next();
  $i = 0;
  while ($aRow = $oDataset->getRow()) {
  	switch ($aRow['STEP_TYPE_OBJ']) {
  		case 'DYNAFORM':
  		  require_once 'classes/model/Dynaform.php';
  		  $oObject           = new Dynaform();
  		  $aFields           = $oObject->load($aRow['STEP_UID_OBJ']);
  		  $aRow['STEP_NAME'] = $aFields['DYN_TITLE'];
  		break;
  		case 'INPUT_DOCUMENT':
  		  require_once 'classes/model/InputDocument.php';
  		  $oObject           = new InputDocument();
  		  $aFields           = $oObject->load($aRow['STEP_UID_OBJ']);
  		  $aRow['STEP_NAME'] = $aFields['INP_DOC_TITLE'];
  		break;
  		case 'OUTPUT_DOCUMENT':
  		  require_once 'classes/model/OutputDocument.php';
  		  $oObject           = new OutputDocument();
  		  $aFields           = $oObject->load($aRow['STEP_UID_OBJ']);
  		  $aRow['STEP_NAME'] = $aFields['OUT_DOC_TITLE'];
  		break;
  	}
    $oNode             =& $oTree->addChild($aRow['STEP_UID'], '&nbsp;&nbsp;<span onclick="tree.expand(this.parentNode);" style="cursor: pointer;">' . $aRow['STEP_NAME'] . '</span>', array('nodeType'=>'parent'));
    $oNode->contracted = true;
    $oAux1             =& $oNode->addChild('before_node', '<span onclick="tree.expand(this.parentNode);showTriggers(\'' . $aRow['STEP_UID'] . '\', \'BEFORE\');" style="cursor: pointer;">' . G::LoadTranslation('ID_BEFORE') . '</span>', array('nodeType'=>'parent'));
    $oAux1->plus       = "<span  style='cursor:pointer;display:block;width:15;height:10px;' onclick='tree.expand(this.parentNode);showTriggers(\"" . $aRow['STEP_UID'] . "\", \"BEFORE\");'></span>";
    $oAux1->contracted = true;
    $oAux2             =& $oAux1->addChild($aRow['STEP_UID'] . '_before_node', '<span id="triggersSpan_' . $aRow['STEP_UID'] . '_BEFORE"></span>', array('nodeType'=>'parentBlue'));
    $oAux1             =& $oNode->addChild('after_node', '<span onclick="tree.expand(this.parentNode);showTriggers(\'' . $aRow['STEP_UID'] . '\', \'AFTER\');" style="cursor: pointer;">' . G::LoadTranslation('ID_AFTER') . '</span>', array('nodeType'=>'parent'));
    $oAux1->plus       = "<span  style='cursor:pointer;display:block;width:15;height:10px;' onclick='tree.expand(this.parentNode);showTriggers(\"" . $aRow['STEP_UID'] . "\", \"AFTER\");'></span>";
    $oAux1->contracted = true;
    $oAux2             =& $oAux1->addChild($aRow['STEP_UID'] . '_after_node', '<span id="triggersSpan_' . $aRow['STEP_UID'] . '_AFTER"></span>', array('nodeType'=>'parentBlue'));
  	$oDataset->next();
  }
  $oNode             =& $oTree->addChild('-1', '&nbsp;&nbsp;<span onclick="tree.expand(this.parentNode);" style="cursor: pointer;">[<b> ' . G::LoadTranslation('ID_ASSIGN_TASK') . ' </b>]</span>', array('nodeType'=>'parent'));
  $oNode->contracted = true;
  $oAux1             =& $oNode->addChild('before_node', '<span onclick="tree.expand(this.parentNode);showTriggers(\'-1\', \'BEFORE\');" style="cursor: pointer;">' . G::LoadTranslation('ID_BEFORE_ASSIGNMENT') . '</span>', array('nodeType'=>'parent'));
  $oAux1->plus       = "<span  style='cursor:pointer;display:block;width:15;height:10px;' onclick='tree.expand(this.parentNode);showTriggers(\"-1\", \"BEFORE\");'></span>";
  $oAux1->contracted = true;
  $oAux2             =& $oAux1->addChild('-1_before_node', '<span id="triggersSpan_-1_BEFORE"></span>', array('nodeType'=>'parentBlue'));
  /*$oAux1             =& $oNode->addChild('after_node', '<span onclick="tree.expand(this.parentNode);showTriggers(\'-1\', \'AFTER\');" style="cursor: pointer;">' . G::LoadTranslation('ID_AFTER') . '</span>', array('nodeType'=>'parent'));
  $oAux1->plus       = "<span  style='cursor:pointer;display:block;width:15;height:10px;' onclick='tree.expand(this.parentNode);showTriggers(\"-1\", \"AFTER\");'></span>";
  $oAux1->contracted = true;
  $oAux2             =& $oAux1->addChild('-1_after_node', '<span id="triggersSpan_-1_AFTER"></span>', array('nodeType'=>'parentBlue'));*/
  $oAux1             =& $oNode->addChild('before_node', '<span onclick="tree.expand(this.parentNode);showTriggers(\'-2\', \'BEFORE\');" style="cursor: pointer;">' . G::LoadTranslation('ID_BEFORE_DERIVATION') . '</span>', array('nodeType'=>'parent'));
  $oAux1->plus       = "<span  style='cursor:pointer;display:block;width:15;height:10px;' onclick='tree.expand(this.parentNode);showTriggers(\"-2\", \"BEFORE\");'></span>";
  $oAux1->contracted = true;
  $oAux2             =& $oAux1->addChild('-2_before_node', '<span id="triggersSpan_-2_BEFORE"></span>', array('nodeType'=>'parentBlue'));
  $oAux1             =& $oNode->addChild('after_node', '<span onclick="tree.expand(this.parentNode);showTriggers(\'-2\', \'AFTER\');" style="cursor: pointer;">' . G::LoadTranslation('ID_AFTER_DERIVATION') . '</span>', array('nodeType'=>'parent'));
  $oAux1->plus       = "<span  style='cursor:pointer;display:block;width:15;height:10px;' onclick='tree.expand(this.parentNode);showTriggers(\"-2\", \"AFTER\");'></span>";
  $oAux1->contracted = true;
  $oAux2             =& $oAux1->addChild('-2_after_node', '<span id="triggersSpan_-2_AFTER"></span>', array('nodeType'=>'parentBlue'));
  echo $oTree->render();
}
catch (Exception $oException) {
	die($oException->getMessage());
}
unset($_SESSION['PROCESS']);
?>