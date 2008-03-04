<?php
  require_once ( "classes/model/Step.php" );
  require_once ( "classes/model/Content.php" );
  require_once ( "classes/model/AppDocumentPeer.php" );
  require_once ( "classes/model/InputDocumentPeer.php" );
  require_once ( "classes/model/OutputDocumentPeer.php" );
  require_once ( "classes/model/DynaformPeer.php" );

  $c = new Criteria();
  $c->add ( StepPeer::PRO_UID, $_SESSION['PROCESS'] );
  $c->add ( StepPeer::TAS_UID, $_SESSION['TASK'] );
  $c->addAscendingOrderByColumn ( StepPeer::STEP_POSITION );

  // class Tree
  G::LoadClass('tree');
  $oTree           = new Tree();
  $oTree->nodeType = "blank";
  $oTree->name     = 'Steps';
  $oTree->showSign = false;

  $rs = StepPeer::doSelect( $c );

  G::LoadClass('case');
  $oCase = new Cases();
  $Fields = $oCase->loadCase( $_SESSION['APPLICATION'] );
  G::LoadClass('pmScript');
  $oPMScript = new PMScript();
  $oPMScript->setFields($Fields['APP_DATA'] );

  foreach ( $rs as $key => $aRow  )
  {
	  $bAccessStep = false;
	  if ($aRow->getStepCondition() != '') {
		  $oPMScript->setScript( $aRow->getStepCondition() );
    	$bAccessStep = $oPMScript->evaluate();
	  }
	  else {
		  $bAccessStep = true;
    }

   if ($bAccessStep)
   {
     switch( $aRow->getStepTypeObj() )
     {
     	case 'DYNAFORM':
     	  $oDocument = DynaformPeer::retrieveByPK($aRow->getStepUidObj());
     	  $stepTitle = $oDocument->getDynTitle(); break;
     	case 'OUTPUT_DOCUMENT':
     	  $oDocument = OutputDocumentPeer::retrieveByPK($aRow->getStepUidObj());
     	  $stepTitle = $oDocument->getOutDocTitle(); break;
     	case 'INPUT_DOCUMENT':
     	  $oDocument = InputDocumentPeer::retrieveByPK($aRow->getStepUidObj());
     	  $stepTitle = $oDocument->getInpDocTitle();
     	  $sType     = $oDocument->getInpDocFormNeeded(); break;
     	default:
     	  $stepTitle = $aRow->getStepUid();
     }

     //$oNode        =& $oTree->addChild($aRow->getStepUid(), '&nbsp;' . $aRow->getStepNameObj(), array('nodeType'=>'parent') );
     $oNode        =& $oTree->addChild($aRow->getStepUid(), '&nbsp;' . $stepTitle, array('nodeType'=>'parent') );
     $oNode->plus  = '';
     $oNode->minus = '';
     switch( $aRow->getStepTypeObj() )
     {
     	  case 'DYNAFORM':
     	  $sOptions  = '<table width="70%" cellpadding="0" cellspacing="0" border="0"><tr>';
     	  $sOptions .= '<td width="100%" class="treeNode"><a style="' . (($_GET['TYPE'] == 'DYNAFORM') && ($_GET['UID'] == $aRow->getStepUidObj() && ($_GET['ACTION'] == 'EDIT')) ?'background-color:orange;color:white;padding-left:5px;padding-right:5px; ' : '') . '" href="../cases/cases_Step?TYPE=' . $aRow->getStepTypeObj() . '&UID=' . $aRow->getStepUidObj() . '&POSITION=' . $aRow->getStepPosition() . '&ACTION=EDIT">' . G::LoadTranslation('ID_EDIT') . '</a></td>';
     	  $sOptions .= '</tr></table>';
     	  $oAux =& $oNode->addChild($aRow->getStepUid() . '_node', $sOptions, array('nodeType'=>'child'));
     	  break;
     	case 'OUTPUT_DOCUMENT':
     	  $sOptions  = '<table width="70%" cellpadding="0" cellspacing="0" border="0"><tr>';
     	  $oCriteria = new Criteria('workflow');
     	  $oCriteria->add(AppDocumentPeer::APP_UID,      $_SESSION['APPLICATION']);
     	  $oCriteria->add(AppDocumentPeer::DEL_INDEX,    $_SESSION['INDEX']);
     	  $oCriteria->add(AppDocumentPeer::DOC_UID,      $aRow->getStepUidObj());
     	  $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, 'OUTPUT');
     	  $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        if ($aRow2 = $oDataset->getRow()) {
     	  	$sOptions .= '<td width="30%" class="treeNode"><a style="' . (($_GET['TYPE'] == 'OUTPUT_DOCUMENT') && ($_GET['UID'] == $aRow->getStepUidObj() && ($_GET['ACTION'] == 'VIEW')) ? 'background-color:orange;color:white;padding-left:5px;padding-right:5px;' : '') . '" href="../cases/cases_Step?TYPE=' . $aRow->getStepTypeObj() . '&UID=' . $aRow->getStepUidObj() . '&POSITION=' . $aRow->getStepPosition() . '&ACTION=VIEW&DOC=' . $aRow2['APP_DOC_UID'] . '">' . G::LoadTranslation('ID_VIEW') . '</a></td>';
     	    $sOptions .= '<td width="40%" class="treeNode"><a style="' . (($_GET['TYPE'] == 'OUTPUT_DOCUMENT') && ($_GET['UID'] == $aRow->getStepUidObj() && ($_GET['ACTION'] == 'GENERATE')) ? 'background-color:orange;color:white;padding-left:5px;padding-right:5px;' : '') . '" href="../cases/cases_Step?TYPE=' . $aRow->getStepTypeObj() . '&UID=' . $aRow->getStepUidObj() . '&POSITION=' . $aRow->getStepPosition() . '&ACTION=GENERATE">' . G::LoadTranslation('ID_GENERATE') . '</a></td>';
     	    $sOptions .= '<td width="30%" class="treeNode"><a style="' . (($_GET['TYPE'] == 'OUTPUT_DOCUMENT') && ($_GET['UID'] == $aRow->getStepUidObj() && ($_GET['ACTION'] == 'DELETE')) ? 'background-color:orange;color:white;padding-left:5px;padding-right:5px;' : '') . '" href="../cases/cases_DeleteDocument?TYPE=OUTPUT&DOC=' . $aRow2['APP_DOC_UID'] . '" onclick="return confirm(\'' . G::LoadTranslation('ID_CONFIRM_DELETE_ELEMENT') . '\');">' . G::LoadTranslation('ID_DELETE') . '</a></td>';
        }
        else {
        	$sOptions .= '<td width="100%" class="treeNode"><a style="' . (($_GET['TYPE'] == 'OUTPUT_DOCUMENT') && ($_GET['UID'] == $aRow->getStepUidObj() && ($_GET['ACTION'] == 'GENERATE')) ? 'background-color:orange;color:white;padding-left:5px;padding-right:5px;' : '') . '" href="../cases/cases_Step?TYPE=' . $aRow->getStepTypeObj() . '&UID=' . $aRow->getStepUidObj() . '&POSITION=' . $aRow->getStepPosition() . '&ACTION=GENERATE">' . G::LoadTranslation('ID_GENERATE') . '</a></td>';
        }
     	  $sOptions .= '</tr></table>';
     	  $oAux =& $oNode->addChild($aRow->getStepUid() . '_node', $sOptions, array('nodeType'=>'child'));
     	break;
     	case 'INPUT_DOCUMENT':
     	  $sOptions  = '<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr>';
     	  $sOptions .= '<td width="100%" class="treeNode"><a style="' . (($_GET['TYPE'] == 'INPUT_DOCUMENT') && ($_GET['UID'] == $aRow->getStepUidObj() && ($_GET['ACTION'] == 'ATTACH')) ? 'background-color:orange;color:white;padding-left:5px;padding-right:5px;' : '') . '" href="../cases/cases_Step?TYPE=' . $aRow->getStepTypeObj() . '&UID=' . $aRow->getStepUidObj() . '&POSITION=' . $aRow->getStepPosition() . '&ACTION=ATTACH">' . ($sType == 'REAL' ? G::LoadTranslation('ID_NEW') : G::LoadTranslation('ID_ATTACH')) . '</a></td>';
     	  $sOptions .= '</tr></table>';
     	  $oCri = new Criteria;
     	  $oCri->add( AppDocumentPeer::APP_UID , $_SESSION['APPLICATION'] );
     	  $oCri->add( AppDocumentPeer::DEL_INDEX , $_SESSION['INDEX'] );
     	  $oCri->add( AppDocumentPeer::DOC_UID , $aRow->getStepUidObj() );
     	  $oCri->add( AppDocumentPeer::APP_DOC_TYPE , 'INPUT' );
     	  $oCri->addAscendingOrderByColumn(AppDocumentPeer::APP_DOC_INDEX);
     	  $aDocuments = AppDocumentPeer::doSelect($oCri);
     	  if (sizeof($aDocuments) !== 0)
     	  {
     	    $i = 1;
     	    $sOptions .= '<table width="90%" align="center" cellpadding="0" cellspacing="0" border="0">';
     	    reset($aDocuments);
     	    while ($oDocument = current($aDocuments))
     	    {
     	      $aRow2 = $oDocument->toArray(BasePeer::TYPE_FIELDNAME);
     	      $oAux1 = new AppDocument();
     	      $aAux  = $oAux1->load($aRow2['APP_DOC_UID']);
     	      $sOptions .= '<tr>';
     	      if ($aAux['APP_DOC_FILENAME'] != '') {
     	        $sAux = $aAux['APP_DOC_FILENAME'];
     	      }
     	      else {
     	      	$sAux = $aAux['APP_DOC_COMMENT'];
     	      }
     	      $sOptions .= '<td width="5%">' . $i . '.</td><td width="55%" class="treeNodeAlternate"><input type="text" readonly="readonly" style="font:inherit;border:none;width:100%;" value="' . htmlentities( $sAux, ENT_QUOTES, "utf-8" ) . '" title="' . $sAux . '" /></td>';
     	      if (isset($_GET['DOC']))
     	      {
     	        $sOptions .= '<td width="20%" class="treeNode" align="center"><a style="' . (($_GET['TYPE'] == 'INPUT_DOCUMENT') && ($_GET['UID'] == $aRow->getStepUidObj() && ($_GET['ACTION'] == 'VIEW') && ($_GET['DOC'] == $aRow2['APP_DOC_UID'])) ? 'background-color:orange;color:shite;padding-left:5px;padding-right:5px;' : '') . '" href="../cases/cases_Step?TYPE=' . $aRow->getStepTypeObj() . '&UID=' . $aRow->getStepUidObj() . '&POSITION=' . $aRow->getStepPosition() . '&ACTION=VIEW&DOC=' . $aRow2['APP_DOC_UID'] . '">' . G::LoadTranslation('ID_VIEW') . '</a></td>';
     	      }
     	      else
     	      {
     	      	$sOptions .= '<td width="20%" class="treeNode" align="center"><a style="" href="../cases/cases_Step?TYPE=' . $aRow->getStepTypeObj() . '&UID=' . $aRow->getStepUidObj() . '&POSITION=' . $aRow->getStepPosition() . '&ACTION=VIEW&DOC=' . $aRow2['APP_DOC_UID'] . '">' . G::LoadTranslation('ID_VIEW') . '</a></td>';
     	      }
     	      $sOptions .= '<td width="20%" class="treeNode" align="center"><a href="../cases/cases_DeleteDocument?TYPE=INPUT&DOC=' . $aRow2['APP_DOC_UID'] . '" onclick="return confirm(\'' . G::LoadTranslation('ID_CONFIRM_DELETE_ELEMENT') . '\');">' . G::LoadTranslation('ID_DELETE') . '</a></td>';
     	      $sOptions .= '</tr>';
     	      $i++;
     	      next($aDocuments);
     	    }
     	    $sOptions .= '</table>';
     	  }
        $oAux =& $oNode->addChild($aRow->getStepUid() . '_node', $sOptions, array('nodeType'=>'child'));

     	break;
     	case 'MESSAGE':
     	  $sOptions  = '<table width="70%" cellpadding="0" cellspacing="0" border="0"><tr>';
     	  $sOptions .= '<td width="100%" class="treeNode"></td>';
     	  $sOptions .= '</tr></table>';
     	  $oAux =& $oNode->addChild($aRow['STEP_UID'] . '_node', $sOptions, array('nodeType'=>'child'));
     	break;
     }
     $oAux->point = '';
    }
  }

$oNode         =& $oTree->addChild('-1', '<span class="treeNode"><a style="' . (($_GET['TYPE'] == 'ASSIGN_TASK') && ($_GET['UID'] == '-1' && ($_GET['ACTION'] == 'ASSIGN')) ? 'background-color:orange;padding-left:5px;padding-right:5px;color:white;' : '') . '" href="../cases/cases_Step?TYPE=ASSIGN_TASK&UID=-1&POSITION=10000&ACTION=ASSIGN">[ ' . G::LoadTranslation('ID_ASSIGN_TASK') . ' ]</a></span>', array('nodeType'=>'parent'));
$oNode->plus   =  '';
$oNode->minus  =  '';
/*$sOptions      =  '<table width="70%" cellpadding="0" cellspacing="0" border="0"><tr>';
$sOptions     .=  '<td width="100%" class="treeNode"><a style="' . (($_GET['TYPE'] == 'ASSIGN_TASK') && ($_GET['UID'] == '-1' && ($_GET['ACTION'] == 'ASSIGN')) ? 'background-color:#006699;padding-left:5px;padding-right:5px;color:white;' : '') . '" href="../cases/cases_Step?TYPE=ASSIGN_TASK&UID=-1&POSITION=10000&ACTION=ASSIGN">' . G::LoadTranslation('ID_ASSIGN_SCREEN') . '</a></td>';
$sOptions     .=  '</tr></table>';
$oAux          =& $oNode->addChild('-1_node', $sOptions, array('nodeType'=>'child'));
$oAux->point   =  '';*/
echo $oTree->render();
?>