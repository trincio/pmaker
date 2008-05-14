<?php
/**
 * cases_Step.php
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

  /* Permissions */
  switch ($RBAC->userCanAccess('PM_CASES'))
  {
    case -2:
      G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels');
      G::header('location: ../login/login');
      die;
    break;
    case -1:
      G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
      G::header('location: ../login/login');
      die;
    break;
  }

  if ( (int)$_SESSION['INDEX'] < 1 )
  {
    G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
    G::header('location: ' . $_SERVER['HTTP_REFERER']);
    die;
  }
  /* Includes */
  G::LoadClass('case');
  G::LoadClass('derivation');

  /* GET , POST & $_SESSION Vars */
  $_SESSION['STEP_POSITION'] = (int)$_GET['POSITION'];

  /* Menues */
  $G_MAIN_MENU            = 'processmaker';
  $G_ID_MENU_SELECTED     = 'CASES';
  $G_SUB_MENU             = 'caseOptions';
  $G_ID_SUB_MENU_SELECTED = '_';

  /* Prepare page before to show */
  $oTemplatePower = new TemplatePower(PATH_TPL . 'cases/cases_Step.html');
  $oTemplatePower->prepare();
  $G_PUBLISH = new Publisher;
  $G_HEADER->clearScripts();
    if ( defined( 'SYS_LANG' ) )
    {
      $jslabel = 'labels/' . SYS_LANG . '.js';
      if ( ! file_exists( PATH_CORE . 'js' . PATH_SEP . $jslabel ) )
        $jslabel = 'labels/en.js';
    }
    else
      $jslabel = 'labels/en.js';

    if ( file_exists( PATH_CORE . 'js' . PATH_SEP . $jslabel ) ) {
      $G_HEADER->addScriptFile( '/jscore/' . $jslabel , 1 );
    }
  $G_HEADER->addScriptFile('/js/maborak/core/maborak.js');
  $G_HEADER->addScriptFile('/js/maborak/core/maborak.js');
  $G_HEADER->addScriptFile('/js/common/core/common.js');
  $G_HEADER->addScriptFile('/js/common/core/webResource.js');
  $G_HEADER->addScriptFile('/js/form/core/form.js');
  $G_HEADER->addScriptFile('/js/grid/core/grid.js');

  $G_HEADER->addScriptCode('
  var Cse = {};
  Cse.panels = {};
  var leimnud = new maborak();
  leimnud.make();
  leimnud.Package.Load("rpc,drag,drop,panel,app,validator,fx,dom,abbr",{Instance:leimnud,Type:"module"});
  leimnud.Package.Load("json",{Type:"file"});
  leimnud.Package.Load("cases",{Type:"file",Absolute:true,Path:"/jscore/cases/core/cases.js"});
  leimnud.Package.Load("cases_Step",{Type:"file",Absolute:true,Path:"/jscore/cases/core/cases_Step.js"});
  leimnud.Package.Load("processmap",{Type:"file",Absolute:true,Path:"/jscore/processmap/core/processmap.js"});
  leimnud.exec(leimnud.fix.memoryLeak);
  leimnud.event.add(window,"load",function(){
	  '.(isset($_SESSION['showCasesWindow'])?'try{'.$_SESSION['showCasesWindow'].'}catch(e){}':'').'
});
  ');
  $G_PUBLISH->AddContent('template', '', '', '', $oTemplatePower);

  $oCase = new Cases();
  $Fields = $oCase->loadCase( $_SESSION['APPLICATION'] );

  //Execute before triggers - Start
  $Fields['APP_DATA'] = $oCase->ExecuteTriggers ( $_SESSION['TASK'], $_GET['TYPE'], $_GET['UID'], 'BEFORE', $Fields['APP_DATA'] );
  $Fields['DEL_INDEX']= $_SESSION['INDEX'];
  $Fields['TAS_UID']  = $_SESSION['TASK'];
  //Execute before triggers - End

  //Save data - Start
  $oCase->updateCase ( $_SESSION['APPLICATION'], $Fields );
  //Save data - End

  //Obtain previous and next step - Start
  try {
    $oCase         = new Cases();
    $aNextStep     = $oCase->getNextStep(    $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION']);
    $aPreviousStep = $oCase->getPreviousStep($_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION']);
  }
  catch ( Exception $e ) {
    $_SESSION['G_MESSAGE']      = $e->getMessage();
    $_SESSION['G_MESSAGE_TYPE'] = 'error';
    G::header('location: cases_List' );
  }
  //Obtain previous and next step - End

try {
//Add content content step - Start
switch ($_GET['TYPE'])
{
  case 'DYNAFORM':
    if (!$aPreviousStep)
    {
//      $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP'] = "javascript:alert('" . G::LoadTranslation('ID_YOU_ARE_FIRST_STEP') . "');";
      $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = '';
    }
    else
    {
      $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP'] = $aPreviousStep['PAGE'];
      $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = G::loadTranslation("ID_PREVIOUS_STEP");
    }
    $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP'] = $aNextStep['PAGE'];
    
    /** ******************************************************************************* init  erik task
      aqui deberia existir la validacion por proceso 
      que exista el archivo de configuraciones,
      si exista hacer: 
      PROPEL::Init ( PATH_DATA . ..... dbconections/GUI_PROCESS.php); 
    */
    
    require_once PATH_HOME."engine/classes/class.dbConnections.php";
    $dbs = new dbConnections($_SESSION['PROCESS']);
    
    $_SESSION['HAVE_A_MYSQL_CONNECTION'] = (($_SESSION['cnn_mysql'] = $dbs->getConnections("mysql")) != false)?true: false; 
	$_SESSION['HAVE_A_PGSQL_CONNECTION'] = (($_SESSION['cnn_pgsql'] = $dbs->getConnections("pgsql")) != false)?true: false; 
	$_SESSION['HAVE_A_MSSQL_CONNECTION'] = (($_SESSION['cnn_mssql'] = $dbs->getConnections("mssql")) != false)?true: false;
    
    if($_SESSION['HAVE_A_MYSQL_CONNECTION'] or $_SESSION['HAVE_A_PGSQL_CONNECTION'] or $_SESSION['HAVE_A_MSSQL_CONNECTION'] )
    {
		PROPEL::Init ( PATH_METHODS.'dbConnections/genericDbConnectios.php'); 
	}
    /*//this commented lines is just for testing,.. remove later
	$for_test_the_array =  include (PATH_METHODS.'dbConnections/genericDbConnectios.php');
	echo '<pre>';
	print_r($for_test_the_array);
	echo '</pre>';
	*/
    /** ******************************************************************************** end erik task*/
    
    $G_PUBLISH->AddContent('dynaform', 'xmlform', $_SESSION['PROCESS']. '/' . $_GET['UID'], '', $Fields['APP_DATA'], 'cases_SaveData?UID=' . $_GET['UID']);
    break;
  case 'INPUT_DOCUMENT':
    $oInputDocument = new InputDocument();
    $Fields = $oInputDocument->load($_GET['UID']);
    if (!$aPreviousStep)
    {
      //$Fields['__DYNAFORM_OPTIONS']['PREVIOUS_STEP'] = "javascript:alert('" . G::LoadTranslation('ID_YOU_ARE_FIRST_STEP') . "');";
      $Fields['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = '';
    }
    else
    {
      $Fields['__DYNAFORM_OPTIONS']['PREVIOUS_STEP'] = $aPreviousStep['PAGE'];
      $Fields['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = G::loadTranslation("ID_PREVIOUS_STEP");
    }
    $Fields['__DYNAFORM_OPTIONS']['NEXT_STEP'] = $aNextStep['PAGE'];
    switch ($_GET['ACTION'])
    {
      case 'ATTACH':
        switch ($Fields['INP_DOC_FORM_NEEDED']) {
          case 'REAL':
            $Fields['TYPE_LABEL'] = G::LoadTranslation('ID_NEW');
            $sXmlForm = 'cases/cases_AttachInputDocument2';
            break;
          case 'VIRTUAL':
            $Fields['TYPE_LABEL'] = G::LoadTranslation('ID_ATTACH');
            $sXmlForm = 'cases/cases_AttachInputDocument1';
            break;
          case 'VREAL':
            $Fields['TYPE_LABEL'] = G::LoadTranslation('ID_ATTACH');
            $sXmlForm = 'cases/cases_AttachInputDocument3';
            break;
        }
        $Fields['MESSAGE1'] = G::LoadTranslation('ID_PLEASE_ENTER_COMMENTS');
        $Fields['MESSAGE2'] = G::LoadTranslation('ID_PLEASE_SELECT_FILE');
        $G_PUBLISH->AddContent('xmlform', 'xmlform', $sXmlForm, '', $Fields, 'cases_SaveDocument?UID=' . $_GET['UID']);

    //call plugin
    if ( $oPluginRegistry->existsTrigger ( PM_CASE_DOCUMENT_LIST ) ) {
      $folderData = new folderData (null, null, $_SESSION['APPLICATION'], null, $_SESSION['USER_LOGGED'] );
      $oPluginRegistry =& PMPluginRegistry::getSingleton();
      $oPluginRegistry->executeTriggers ( PM_CASE_DOCUMENT_LIST , $folderData );
    }
    else
      $G_PUBLISH->AddContent('propeltable', 'paged-table', 'cases/cases_InputdocsList', $oCase->getInputDocumentsCriteria($_SESSION['APPLICATION'], $_SESSION['INDEX'], $_GET['UID']), '');//$aFields
    //end plugin

      break;
      case 'VIEW':
        require_once 'classes/model/AppDocument.php';
        require_once 'classes/model/Users.php';
        $oAppDocument = new AppDocument();
        $oAppDocument->Fields = $oAppDocument->load($_GET['DOC']);
        $Fields['POSITION']   = $_SESSION['STEP_POSITION'];
        $oUser = new Users();
        $aUser = $oUser->load($oAppDocument->Fields['USR_UID']);
        $Fields['CREATOR'] = $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'];
        switch ($Fields['INP_DOC_FORM_NEEDED'])
        {
          case 'REAL':
            $sXmlForm = 'cases/cases_ViewInputDocument2';
          break;
          case 'VIRTUAL':
            $sXmlForm = 'cases/cases_ViewInputDocument1';
          break;
          case 'VREAL':
            $sXmlForm = 'cases/cases_ViewInputDocument3';
          break;
        }
        $oAppDocument->Fields['VIEW'] = G::LoadTranslation('ID_OPEN');
        $oAppDocument->Fields['FILE'] = 'cases_ShowDocument?a=' . $_GET['DOC'] . '&r=' . rand();
        $G_PUBLISH->AddContent('xmlform', 'xmlform', $sXmlForm, '', G::array_merges($Fields, $oAppDocument->Fields), '');
      break;
    }
  break;
  case 'OUTPUT_DOCUMENT':
    require_once 'classes/model/OutputDocument.php';
    $oOutputDocument = new OutputDocument();
    $aOD = $oOutputDocument->load($_GET['UID']);
    if (!$aPreviousStep)
    {
      $aOD['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = '';
    }
    else
    {
      $aOD['__DYNAFORM_OPTIONS']['PREVIOUS_STEP'] = $aPreviousStep['PAGE'];
      $aOD['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = G::loadTranslation("ID_PREVIOUS_STEP");
    }
    $aOD['__DYNAFORM_OPTIONS']['NEXT_STEP'] = $aNextStep['PAGE'];
    switch ($_GET['ACTION'])
    {
      case 'GENERATE':
        /*require_once 'classes/model/Application.php';
        $oApplication = new Application();
        $aApplication = $oApplication->load($_SESSION['APPLICATION']);
        if (!is_array($aApplication['APP_DATA'])) {
          $aApplication['APP_DATA'] = unserialize($aApplication['APP_DATA']);
          if (is_null($aApplication['APP_DATA'])) {
            $aApplication['APP_DATA'] = array();
          }
        }
        $sFilename = G::replaceDataField($aOD['OUT_DOC_FILENAME'], $aApplication['APP_DATA']);*/
        $sFilename = ereg_replace('[^A-Za-z0-9_]', '_', G::replaceDataField($aOD['OUT_DOC_FILENAME'], $Fields['APP_DATA']));
        $pathOutput = PATH_DOCUMENT . $_SESSION['APPLICATION'] . PATH_SEP . 'outdocs'. PATH_SEP ;
        //$oOutputDocument->generate($_GET['UID'], $aApplication['APP_DATA'], PATH_DOCUMENT . $_SESSION['APPLICATION'] . '/outdocs/', $sFilename, $aOD['OUT_DOC_TEMPLATE']);
        $oOutputDocument->generate($_GET['UID'], $Fields['APP_DATA'], $pathOutput, $sFilename, $aOD['OUT_DOC_TEMPLATE']);
        require_once 'classes/model/AppDocument.php';
        $oCriteria = new Criteria('workflow');
        $oCriteria->add(AppDocumentPeer::APP_UID,      $_SESSION['APPLICATION']);
        $oCriteria->add(AppDocumentPeer::DEL_INDEX,    $_SESSION['INDEX']);
        $oCriteria->add(AppDocumentPeer::DOC_UID,      $_GET['UID']);
        $oCriteria->add(AppDocumentPeer::APP_DOC_TYPE, 'OUTPUT');
        $oDataset = AppDocumentPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        if ($aRow = $oDataset->getRow()) {
          $aFields = array('APP_DOC_UID'         => $aRow['APP_DOC_UID'],
                           'APP_UID'             => $_SESSION['APPLICATION'],
                           'DEL_INDEX'           => $_SESSION['INDEX'],
                           'DOC_UID'             => $_GET['UID'],
                           'USR_UID'             => $_SESSION['USER_LOGGED'],
                           'APP_DOC_TYPE'        => 'OUTPUT',
                           'APP_DOC_CREATE_DATE' => date('Y-m-d H:i:s'),
                           'APP_DOC_FILENAME'    => $sFilename);
          //Execute after triggers - Start
          $Fields['APP_DATA'] = $oCase->ExecuteTriggers ( $_SESSION['TASK'], 'OUTPUT_DOCUMENT', $_GET['UID'], 'AFTER', $Fields['APP_DATA'] );
          $Fields['DEL_INDEX']= $_SESSION['INDEX'];
          $Fields['TAS_UID']  = $_SESSION['TASK'];
          //Execute after triggers - End
          //Save data - Start
          $oCase->updateCase ( $_SESSION['APPLICATION'], $Fields );
          //Save data - End
          $oAppDocument = new AppDocument();
          $oAppDocument->update($aFields);
          $sDocUID = $aRow['APP_DOC_UID'];
        }
        else {
          $aFields = array('APP_UID'             => $_SESSION['APPLICATION'],
                           'DEL_INDEX'           => $_SESSION['INDEX'],
                           'DOC_UID'             => $_GET['UID'],
                           'USR_UID'             => $_SESSION['USER_LOGGED'],
                           'APP_DOC_TYPE'        => 'OUTPUT',
                           'APP_DOC_CREATE_DATE' => date('Y-m-d H:i:s'),
                           'APP_DOC_FILENAME'    => $sFilename);
          //Execute after triggers - Start
          $Fields['APP_DATA'] = $oCase->ExecuteTriggers ( $_SESSION['TASK'], 'OUTPUT_DOCUMENT', $_GET['UID'], 'AFTER', $Fields['APP_DATA'] );
          $Fields['DEL_INDEX']= $_SESSION['INDEX'];
          $Fields['TAS_UID']  = $_SESSION['TASK'];
          //Execute after triggers - End
          //Save data - Start
          $oCase->updateCase ( $_SESSION['APPLICATION'], $Fields );
          //Save data - End
          $oAppDocument = new AppDocument();
          $sDocUID = $oAppDocument->create($aFields);
        }
        //Plugin Hook PM_UPLOAD_DOCUMENT for upload document
    	  $oPluginRegistry =& PMPluginRegistry::getSingleton();
        if ( $oPluginRegistry->existsTrigger ( PM_UPLOAD_DOCUMENT ) && class_exists ('uploadDocumentData' ) ) {
          $oData['APP_UID']	  = $_SESSION['APPLICATION'];
          $documentData = new uploadDocumentData (
                            $_SESSION['APPLICATION'],
                            $_SESSION['USER_LOGGED'],
                            $pathOutput . $sFilename . '.pdf',
                            $sFilename. '.pdf',
                            $sDocUID
                            );

  	      $oPluginRegistry->executeTriggers ( PM_UPLOAD_DOCUMENT , $documentData );
  	      unlink ( $sPathName . $sFileName );
        }
        G::header('location: cases_Step?TYPE=OUTPUT_DOCUMENT&UID=' . $_GET['UID'] . '&POSITION=' . $_SESSION['STEP_POSITION'] . '&ACTION=VIEW&DOC=' . $sDocUID);
        break;
      case 'VIEW':
        require_once 'classes/model/AppDocument.php';
        $oAppDocument = new AppDocument();
        $aFields = $oAppDocument->load($_GET['DOC']);
        $aFields['VIEW'] = G::LoadTranslation('ID_OPEN');
        $aFields['FILE1'] = 'cases_ShowOutputDocument?a=' . $aFields['APP_DOC_UID'] . '&ext=doc&random=' . rand();
        $aFields['FILE2'] = 'cases_ShowOutputDocument?a=' . $aFields['APP_DOC_UID'] . '&ext=pdf&random=' . rand();
        $G_PUBLISH->AddContent('xmlform', 'xmlform', 'cases/cases_ViewOutputDocument1', '', G::array_merges($aOD, $aFields), '');
    //call plugin
    if ( $oPluginRegistry->existsTrigger ( PM_CASE_DOCUMENT_LIST ) ) {
      $folderData = new folderData (null, null, $_SESSION['APPLICATION'], null, $_SESSION['USER_LOGGED'] );
      $oPluginRegistry =& PMPluginRegistry::getSingleton();
      $oPluginRegistry->executeTriggers ( PM_CASE_DOCUMENT_LIST , $folderData );
    }
    else
      $G_PUBLISH->AddContent('propeltable', 'paged-table', 'cases/cases_InputdocsList', $oCase->getInputDocumentsCriteria($_SESSION['APPLICATION'], $_SESSION['INDEX'], $_GET['UID']), '');//$aFields
    //end plugin
      break;
    }
    break;

  case 'ASSIGN_TASK':
    $oDerivation = new Derivation();
    $oProcess    = new Process();
    $aFields['PROCESS']        = $oProcess->load($_SESSION['PROCESS']);
    $aFields['PREVIOUS_PAGE'] = $aPreviousStep['PAGE'];
    $aFields['TASK'] = $oDerivation->prepareInformation(
                       array( 'USER_UID'  => $_SESSION['USER_LOGGED'],
                              'APP_UID'   => $_SESSION['APPLICATION'],
                              'DEL_INDEX' => $_SESSION['INDEX'])
                       );
    if ( empty($aFields['TASK']) )  {
      throw ( new Exception ( G::LoadTranslation ( 'ID_NO_DERIVATION_RULE')  ) );
    }

    //take the first derivation rule as the task derivation rule type.
    $aFields['PROCESS']['ROU_TYPE'] = $aFields['TASK'][1]['ROU_TYPE'];
    $aFields['PROCESS']['ROU_FINISH_FLAG'] = false;

    foreach ( $aFields['TASK'] as $sKey => &$aValues)
    {
      $sPriority = '';//set priority value
      if ($aFields['TASK'][$sKey]['NEXT_TASK']['TAS_PRIORITY_VARIABLE'] != '') {
        //TO DO: review this type of assignment
        if (isset($oApplication->Fields[ str_replace('@@', '', $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_PRIORITY_VARIABLE'])]) )
        {
          $sPriority = $oApplication->Fields[$aFields['TASK'][$sKey]['NEXT_TASK']['TAS_PRIORITY_VARIABLE']];
        }
      }//set priority value

      $sTask = $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_UID'];

      //TAS_UID has a hidden field to store the TAS_UID
      $hiddenName = "form[TASKS][" . $sKey . "][TAS_UID]";
      $hiddenField = '<input type="hidden" name="' . $hiddenName . '" id="' . $hiddenName . '" value="' . $aValues['NEXT_TASK']['TAS_UID'] . '">';
      $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_HIDDEN_FIELD'] = $hiddenField;
      switch ($aValues['NEXT_TASK']['TAS_ASSIGN_TYPE']) {
        case 'EVALUATE':
        case 'BALANCED':
            $hiddenName = "form[TASKS][" . $sKey . "][USR_UID]";
            $aFields['TASK'][$sKey]['NEXT_TASK']['USR_UID'] = $aFields['TASK'][$sKey]['NEXT_TASK']['USER_ASSIGNED']['USR_FULLNAME'];
            $aFields['TASK'][$sKey]['NEXT_TASK']['USR_HIDDEN_FIELD'] = '<input type="hidden" name="' . $hiddenName . '" id="' . $hiddenName . '" value="' . $aValues['NEXT_TASK']['USER_ASSIGNED']['USR_UID'] . '">';
            break;
        case 'MANUAL':
            $sAux      = '<select name="form[TASKS][' . $sKey . '][USR_UID]" id="form[TASKS][' . $sKey . '][USR_UID]">';
            foreach ($aValues['NEXT_TASK']['USER_ASSIGNED'] as $aUser)
            {
              $sAux .= '<option value="' . $aUser['USR_UID'] . '">' . $aUser['USR_FULLNAME'] . '</option>';
            }
            $sAux .= '</select>';
            $aFields['TASK'][$sKey]['NEXT_TASK']['USR_UID'] = $sAux;
            break;
        case 'SELFSERVICE':
            //Next release
            break;
        case '':  //when this task is the Finish process
            $userFields = $oDerivation->getUsersFullNameFromArray ( $aFields['TASK'][$sKey]['USER_UID'] );
            $aFields['TASK'][$sKey]['NEXT_TASK']['USR_UID'] = $userFields['USR_FULLNAME'];
            $aFields['TASK'][$sKey]['NEXT_TASK']['ROU_FINISH_FLAG'] = true;
            $aFields['PROCESS']['ROU_FINISH_FLAG'] = true;
            break;
      }
      $hiddenName = 'form[TASKS][' . $sKey . ']';
      $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_ASSIGN_TYPE']   = '<input type="hidden" name="' . $hiddenName . '[TAS_ASSIGN_TYPE]"   id="' . $hiddenName . '[TAS_ASSIGN_TYPE]"   value="' . $aValues['NEXT_TASK']['TAS_ASSIGN_TYPE'] . '">';
      $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_DEF_PROC_CODE'] = '<input type="hidden" name="' . $hiddenName . '[TAS_DEF_PROC_CODE]" id="' . $hiddenName . '[TAS_DEF_PROC_CODE]" value="' . $aValues['NEXT_TASK']['TAS_DEF_PROC_CODE'] . '">';
      $aFields['TASK'][$sKey]['NEXT_TASK']['DEL_PRIORITY']      = '<input type="hidden" name="' . $hiddenName . '[DEL_PRIORITY]"      id="' . $hiddenName . '[DEL_PRIORITY]"      value="' . $sPriority . '">';
    }
                    
    $G_PUBLISH->AddContent('smarty', 'cases/cases_ScreenDerivation', '', '', $aFields);
  break;
}
//Add content content step - End
}
catch ( Exception $e ) {
    $aMessage = array();
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH          = new Publisher;
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish' );
}

/* Render page */
$G_HEADER->addScriptCode('
  var showSteps = function()
  {
    if (!Cse.panels.step)
    {
      Cse=new cases();
      Cse.options = {
        target     : "cases_target",
        dataServer : "cases_Ajax?TYPE=' . (isset($_GET['TYPE']) ? $_GET['TYPE'] : '') . '&UID=' . (isset($_GET['UID']) ? $_GET['UID'] : '') . '&POSITION=' . (isset($_GET['POSITION']) ? $_GET['POSITION'] : '') . '&ACTION=' . (isset($_GET['ACTION']) ? $_GET['ACTION'] : '') . '&DOC=' . (isset($_GET['DOC']) ? $_GET['DOC'] : '') . '",
        action     : "steps",
        title      : "Steps",
        lang       : "' . SYS_LANG . '",
        theme      : "processmaker",
        images_dir :leimnud.path_root + "cases/core/images/"
      }
      Cse.make();
    }
    else
    {
      Cse.panels.step.elements.title.innerHTML = "Steps";
      Cse.panels.step.clearContent();
      Cse.panels.step.loader.show();
      var oRPC = new leimnud.module.rpc.xmlhttp({
        url:  "cases_Ajax?TYPE=' . (isset($_GET['TYPE']) ? $_GET['TYPE'] : '') . '&UID=' . (isset($_GET['UID']) ? $_GET['UID'] : '') . '&POSITION=' . (isset($_GET['POSITION']) ? $_GET['POSITION'] : '') . '&ACTION=' . (isset($_GET['ACTION']) ? $_GET['ACTION'] : '') . '&DOC=' . (isset($_GET['DOC']) ? $_GET['DOC'] : '') . '",
        args: "action=steps&showWindow=steps"
      });
      oRPC.callback = function(rpc){
        Cse.panels.step.loader.hide();
        var scs=rpc.xmlhttp.responseText.extractScript();
        Cse.panels.step.addContent(rpc.xmlhttp.responseText);
        scs.evalScript();
      }.extend(this);
      oRPC.make();
    }
  };
');

G::RenderPage('publish');
?>
