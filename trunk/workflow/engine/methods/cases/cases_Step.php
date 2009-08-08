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
  global $_DBArray;
  if (!isset($_DBArray)) {
    $_DBArray = array();
  }

  /* Includes */
  G::LoadClass('case');
  G::LoadClass('derivation');

  /* GET , POST & $_SESSION Vars */
  if(isset($_GET['POSITION'])) {
    $_SESSION['STEP_POSITION'] = (int)$_GET['POSITION'];
  }

  /* Menues */
  $G_MAIN_MENU            = 'processmaker';
  $G_ID_MENU_SELECTED     = 'CASES';
  $G_SUB_MENU             = 'caseOptions';
  $G_ID_SUB_MENU_SELECTED = '_';

  /* Prepare page before to show */
  $oTemplatePower = new TemplatePower(PATH_TPL . 'cases/cases_Step.html');
  $oTemplatePower->prepare();
  $G_PUBLISH = new Publisher;
  $oHeadPublisher =& headPublisher::getSingleton();
  $oHeadPublisher->addScriptCode('
  var Cse = {};
  Cse.panels = {};
  var leimnud = new maborak();
  leimnud.make();
  leimnud.Package.Load("rpc,drag,drop,panel,app,validator,fx,dom,abbr",{Instance:leimnud,Type:"module"});
  leimnud.exec(leimnud.fix.memoryLeak);
  leimnud.event.add(window,"load",function(){
    '.(isset($_SESSION['showCasesWindow'])?'try{'.$_SESSION['showCasesWindow'].'}catch(e){}':'').'
  });
  ');
  $G_PUBLISH->AddContent('template', '', '', '', $oTemplatePower);

  $oCase = new Cases();
  $oCase->thisIsTheCurrentUser($_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['USER_LOGGED'], 'REDIRECT', 'cases_List');
  $Fields = $oCase->loadCase( $_SESSION['APPLICATION'] );
  $Fields['APP_DATA'] = array_merge($Fields['APP_DATA'], G::getSystemConstants());
  $sStatus = $Fields['APP_STATUS'];

  $APP_NUMBER = $Fields['APP_NUMBER'];
  $APP_TITLE = $Fields['TITLE'];

  $oProcess = new Process();
  $oProcessFieds = $oProcess->Load($_SESSION['PROCESS']);

  #trigger debug routines...

  if( isset($oProcessFieds['PRO_DEBUG']) && $oProcessFieds['PRO_DEBUG'] ) { #here we must verify if is a debugg session
    $_SESSION['TRIGGER_DEBUG']['ISSET'] = 1;
  } 
  else {
    $_SESSION['TRIGGER_DEBUG']['ISSET'] = 0;
  }
  
  //cleaning debug variables
  if( !isset($_GET['breakpoint']) ) {
    $_SESSION['TRIGGER_DEBUG']['ERRORS'] = Array();
    $_SESSION['TRIGGER_DEBUG']['DATA'] = Array();
    $_SESSION['TRIGGER_DEBUG']['TRIGGERS_NAMES'] = Array();
    $_SESSION['TRIGGER_DEBUG']['TRIGGERS_VALUES'] = Array();
    
    $triggers = $oCase->loadTriggers( $_SESSION['TASK'], $_GET['TYPE'], $_GET['UID'], 'BEFORE');
    
    $_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] = count($triggers);
    $_SESSION['TRIGGER_DEBUG']['TIME'] = 'BEFORE';
    if($_SESSION['TRIGGER_DEBUG']['NUM_TRIGGERS'] != 0) {
      $_SESSION['TRIGGER_DEBUG']['TRIGGERS_NAMES'] = $oCase->getTriggerNames($triggers);
      $_SESSION['TRIGGER_DEBUG']['TRIGGERS_VALUES'] = $triggers;
    }
    
    //Execute before triggers - Start
    $Fields['APP_DATA'] = $oCase->ExecuteTriggers ( $_SESSION['TASK'], $_GET['TYPE'], $_GET['UID'], 'BEFORE', $Fields['APP_DATA'] );
    $Fields['DEL_INDEX']= $_SESSION['INDEX'];
    $Fields['TAS_UID']  = $_SESSION['TASK'];
    //Execute before triggers - End
  }

  if( isset($_GET['breakpoint']) ) {
    $_POST['NextStep'] = $_SESSION['TRIGGER_DEBUG']['BREAKPAGE'];
  }

  if( $_SESSION['TRIGGER_DEBUG']['ISSET'] ){
    $G_PUBLISH->AddContent('view', 'cases/showDebugFrame');
  }

  if ( isset($_GET['breakpoint']) ) {
    G::RenderPage('publish');
    exit();
  }
  #end trigger debug session.......

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
    G::header('location: cases_List' );       // why this header doesn't have a die
  }
  //Obtain previous and next step - End

  try {
  //Add content content step - Start
  $array['APP_NUMBER'] = $APP_NUMBER;
  $array['APP_TITLE'] = $APP_TITLE;
  $array['CASE'] = G::LoadTranslation('ID_CASE');
  $array['TITLE'] = G::LoadTranslation('ID_TITLE');
  $G_PUBLISH->AddContent('smarty', 'cases/cases_title', '', '', $array);
  
  switch ($_GET['TYPE'])
  {
    case 'DYNAFORM':
      if (!$aPreviousStep) {
        //$Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP'] = "javascript:alert('" . G::LoadTranslation('ID_YOU_ARE_FIRST_STEP') . "');";
        $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = '';
      }
      else {
        $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP'] = $aPreviousStep['PAGE'];
        $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = G::loadTranslation("ID_PREVIOUS_STEP");
      }
      $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP'] = $aNextStep['PAGE'];
   
      $oStep = new Step();
      $oStep = $oStep->loadByProcessTaskPosition($_SESSION['PROCESS'], $_SESSION['TASK'], $_GET['POSITION']);
  
      /** Added By erik  16-05-08
      * Description: this was added for the additional database connections */
      G::LoadClass ('dbConnections');
      $oDbConnections = new dbConnections($_SESSION['PROCESS']);
      $oDbConnections->loadAdditionalConnections();
  
      $G_PUBLISH->AddContent('dynaform', 'xmlform', $_SESSION['PROCESS']. '/' . $_GET['UID'], '', $Fields['APP_DATA'], 'cases_SaveData?UID=' . $_GET['UID'], '', (strtolower($oStep->getStepMode()) != 'edit' ? strtolower($oStep->getStepMode()) : ''));
      break;
  
    case 'INPUT_DOCUMENT':
      $oInputDocument = new InputDocument();
      $Fields = $oInputDocument->load($_GET['UID']);
      if (!$aPreviousStep) {
        //$Fields['__DYNAFORM_OPTIONS']['PREVIOUS_STEP'] = "javascript:alert('" . G::LoadTranslation('ID_YOU_ARE_FIRST_STEP') . "');";
        $Fields['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = '';
      }
      else {
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
      if (!$aPreviousStep) {
        $aOD['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = '';
      }
      else {
        $aOD['__DYNAFORM_OPTIONS']['PREVIOUS_STEP'] = $aPreviousStep['PAGE'];
        $aOD['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = G::loadTranslation("ID_PREVIOUS_STEP");
      }  
      $aOD['__DYNAFORM_OPTIONS']['NEXT_STEP'] = $aNextStep['PAGE'];
  
      $javaInput  = PATH_C . 'javaBridgePM' . PATH_SEP . 'input'  . PATH_SEP;
      $javaOutput = PATH_C . 'javaBridgePM' . PATH_SEP . 'output' . PATH_SEP;
      G::mk_dir ( $javaInput );
      G::mk_dir ( $javaOutput );

      switch ($_GET['ACTION'])
      {
        case 'GENERATE':
          $sFilename = ereg_replace('[^A-Za-z0-9_]', '_', G::replaceDataField($aOD['OUT_DOC_FILENAME'], $Fields['APP_DATA']));
          if ( $sFilename == '' ) $sFilename='_';
          $pathOutput = PATH_DOCUMENT . $_SESSION['APPLICATION'] . PATH_SEP . 'outdocs'. PATH_SEP ;
          G::mk_dir ( $pathOutput );
          switch ( $aOD['OUT_DOC_TYPE'] ) {
            case 'HTML' : $oOutputDocument->generate( $_GET['UID'], $Fields['APP_DATA'], $pathOutput, 
                            $sFilename, $aOD['OUT_DOC_TEMPLATE'], (boolean)$aOD['OUT_DOC_LANDSCAPE'] );
                          break;
            case 'JRXML' : 

//creating the xml with the application data;
  $xmlData = "<dynaform>\n";
  foreach ( $Fields['APP_DATA'] as $key => $val ) {
    $xmlData .= "  <$key>$val</$key>\n";
  }
  $xmlData .= "</dynaform>\n";
  $iSize = file_put_contents ( $javaOutput .  'addressBook.xml' , $xmlData );
 
  G::LoadClass ('javaBridgePM');
  $JBPM = new JavaBridgePM();
  $JBPM->checkJavaExtension();
  
  $util = new Java("com.processmaker.util.pmutils");
  $util->setInputPath( $javaInput );
  $util->setOutputPath( $javaOutput );

  //$content = file_get_contents ( PATH_DYNAFORM . $aOD['PRO_UID'] . PATH_SEP . $aOD['OUT_DOC_UID'] . '.jrxml' );
  //$iSize = file_put_contents ( $javaInput .  $aOD['OUT_DOC_UID'] . '.jrxml', $content );
  copy ( PATH_DYNAFORM . $aOD['PRO_UID'] . PATH_SEP . $aOD['OUT_DOC_UID'] . '.jrxml', $javaInput .  $aOD['OUT_DOC_UID'] . '.jrxml' );


  $outputFile = $javaOutput . $sFilename . '.pdf' ;
  print $util->jrxml2pdf( $aOD['OUT_DOC_UID'] . '.jrxml' , basename($outputFile) );

  //$content = file_get_contents ( $outputFile );
  //$iSize = file_put_contents ( $pathOutput .  $sFilename . '.pdf' , $content );
  copy ( $outputFile, $pathOutput .  $sFilename . '.pdf' );
//die;
                          break;
            case 'ACROFORM' : 

//creating the xml with the application data;
  $xmlData = "<dynaform>\n";
  foreach ( $Fields['APP_DATA'] as $key => $val ) {
    $xmlData .= "  <$key>$val</$key>\n";
  }
  $xmlData .= "</dynaform>\n";
  //$iSize = file_put_contents ( $javaOutput .  'addressBook.xml' , $xmlData );
 
  G::LoadClass ('javaBridgePM');
  $JBPM = new JavaBridgePM();
  $JBPM->checkJavaExtension();
  
  $util = new Java("com.processmaker.util.pmutils");
  $util->setInputPath( $javaInput );
  $util->setOutputPath( $javaOutput );

  copy ( PATH_DYNAFORM . $aOD['PRO_UID'] . PATH_SEP . $aOD['OUT_DOC_UID'] . '.pdf', $javaInput .  $aOD['OUT_DOC_UID'] . '.pdf' );

  $outputFile = $javaOutput . $sFilename . '.pdf' ;
  print $util->writeVarsToAcroFields( $aOD['OUT_DOC_UID'] . '.pdf' , $xmlData );

  copy ( $javaOutput. $aOD['OUT_DOC_UID'] . '.pdf', $pathOutput .  $sFilename . '.pdf' );

                          break;
            default :
              throw ( new Exception ('invalid output document' ));
          }                            

          //save row in AppDocument  ( this code should be moved to AppDocument Class )
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
            $oAppDocument = new AppDocument();
            $sDocUID = $oAppDocument->create($aFields);
          }

          //Execute after triggers - Start
          $Fields['APP_DATA'] = $oCase->ExecuteTriggers ( $_SESSION['TASK'], 'OUTPUT_DOCUMENT', $_GET['UID'], 'AFTER', $Fields['APP_DATA'] );
          $Fields['DEL_INDEX']= $_SESSION['INDEX'];
          $Fields['TAS_UID']  = $_SESSION['TASK'];
          //Execute after triggers - End
  
          //Save data - Start
          $oCase->updateCase ( $_SESSION['APPLICATION'], $Fields );
          //Save data - End

          //Plugin Hook PM_UPLOAD_DOCUMENT for upload document
          $oPluginRegistry =& PMPluginRegistry::getSingleton();
          if ( $oPluginRegistry->existsTrigger ( PM_UPLOAD_DOCUMENT ) && class_exists ('uploadDocumentData' ) ) {
            $oData['APP_UID']   = $_SESSION['APPLICATION'];
            $oData['ATTACHMENT_FOLDER'] = true;
            $documentData = new uploadDocumentData (
                              $_SESSION['APPLICATION'],
                              $_SESSION['USER_LOGGED'],
                              $pathOutput . $sFilename . '.pdf',
                              $sFilename. '.pdf',
                              $sDocUID
                              );
            $documentData->bUseOutputFolder = true;
            $oPluginRegistry->executeTriggers ( PM_UPLOAD_DOCUMENT , $documentData );
            unlink ( $sPathName . $sFileName );
          }
  
          $outputNextStep = 'cases_Step?TYPE=OUTPUT_DOCUMENT&UID=' . $_GET['UID'] . '&POSITION=' . $_SESSION['STEP_POSITION'] . '&ACTION=VIEW&DOC=' . $sDocUID;
  
          G::header('location: '.$outputNextStep);
  
          break;
        case 'VIEW':
          require_once 'classes/model/AppDocument.php';
          $oAppDocument = new AppDocument();
          $aFields = $oAppDocument->load($_GET['DOC']);
  
          require_once 'classes/model/OutputDocument.php';
          $oOutputDocument = new OutputDocument();
          $aGields = $oOutputDocument->load($aFields['DOC_UID']);
  
          $aFields['VIEW'] = G::LoadTranslation('ID_OPEN');
  
          $aFields['FILE1'] = 'cases_ShowOutputDocument?a=' . $aFields['APP_DOC_UID'] . '&ext=doc&random=' . rand();
  
          $aFields['FILE2'] = 'cases_ShowOutputDocument?a=' . $aFields['APP_DOC_UID'] . '&ext=pdf&random=' . rand();
  
          if(($aGields['OUT_DOC_GENERATE']=='BOTH')||($aGields['OUT_DOC_GENERATE']==''))
              $G_PUBLISH->AddContent('xmlform', 'xmlform', 'cases/cases_ViewOutputDocument1', '', G::array_merges($aOD, $aFields), '');
  
          if($aGields['OUT_DOC_GENERATE']=='DOC')
              $G_PUBLISH->AddContent('xmlform', 'xmlform', 'cases/cases_ViewOutputDocument2', '', G::array_merges($aOD, $aFields), '');
  
          if($aGields['OUT_DOC_GENERATE']=='PDF')
              $G_PUBLISH->AddContent('xmlform', 'xmlform', 'cases/cases_ViewOutputDocument3', '', G::array_merges($aOD, $aFields), '');
  
          //call plugin
          if ( $oPluginRegistry->existsTrigger ( PM_CASE_DOCUMENT_LIST ) ) {
            $folderData = new folderData (null, null, $_SESSION['APPLICATION'], null, $_SESSION['USER_LOGGED'] );
            $oPluginRegistry =& PMPluginRegistry::getSingleton();
            $oPluginRegistry->executeTriggers ( PM_CASE_DOCUMENT_LIST , $folderData );
          }
          /*else
            $G_PUBLISH->AddContent('propeltable', 'paged-table', 'cases/cases_InputdocsList', $oCase->getInputDocumentsCriteria($_SESSION['APPLICATION'], $_SESSION['INDEX'], $_GET['UID']), '');//$aFields
            */
          //end plugin
          break;
        }
      break;
  
    case 'ASSIGN_TASK':
      $oDerivation = new Derivation();
      $oProcess    = new Process();
      $aData       = $oCase->loadCase($_SESSION['APPLICATION']);
      $aFields['PROCESS']              = $oProcess->load($_SESSION['PROCESS']);
      $aFields['PREVIOUS_PAGE']        = $aPreviousStep['PAGE'];
      $aFields['PREVIOUS_PAGE_LABEL']  = G::LoadTranslation('ID_PREVIOUS_STEP');
      $aFields['ASSIGN_TASK']          = G::LoadTranslation('ID_ASSIGN_TASK');
      $aFields['END_OF_PROCESS']       = G::LoadTranslation('ID_END_OF_PROCESS');
      $aFields['NEXT_TASK_LABEL']      = G::LoadTranslation('ID_NEXT_TASK');
      $aFields['EMPLOYEE']             = G::LoadTranslation('ID_EMPLOYEE');
      $aFields['LAST_EMPLOYEE']        = G::LoadTranslation('ID_LAST_EMPLOYEE');
      $aFields['OPTION_LABEL']         = G::LoadTranslation('ID_OPTION');
      $aFields['CONTINUE']             = G::LoadTranslation('ID_CONTINUE');
      $aFields['CONTINUE_WITH_OPTION'] = G::LoadTranslation('ID_CONTINUE_WITH_OPTION');
      $aFields['FINISH_WITH_OPTION']   = G::LoadTranslation('ID_FINISH_WITH_OPTION');
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
          if (isset($aData['APP_DATA'][str_replace('@@', '', $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_PRIORITY_VARIABLE'])]))
          {
            $sPriority = $aData['APP_DATA'][str_replace('@@', '', $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_PRIORITY_VARIABLE'])];
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
              $Aux = array();
              foreach ($aValues['NEXT_TASK']['USER_ASSIGNED'] as $aUser)
              {
                $Aux[$aUser['USR_UID']] = $aUser['USR_FULLNAME'];
              }
              asort($Aux);
              $sAux      = '<select name="form[TASKS][' . $sKey . '][USR_UID]" id="form[TASKS][' . $sKey . '][USR_UID]">';
              foreach ($Aux as $key => $value)
              {
                $sAux .= '<option value="' . $key . '">' . $value . '</option>';
              }
              $sAux .= '</select>';
  
              $aFields['TASK'][$sKey]['NEXT_TASK']['USR_UID'] = $sAux;
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
        $aFields['TASK'][$sKey]['NEXT_TASK']['TAS_PARENT']        = '<input type="hidden" name="' . $hiddenName . '[TAS_PARENT]"        id="' . $hiddenName . '[TAS_PARENT]"        value="' . $aValues['NEXT_TASK']['TAS_PARENT'] . '">';
      }
  
      $G_PUBLISH->AddContent('smarty', 'cases/cases_ScreenDerivation', '', '', $aFields);
      break;
    case 'EXTERNAL':
      $oPluginRegistry = &PMPluginRegistry::getSingleton();
      $externalSteps   = $oPluginRegistry->getSteps();
  
      $sNamespace = '';
      $sStepName  = '';
      foreach ( $externalSteps as $key=>$val ) {
        if ( $val->sStepId == $_GET['UID'] ) {
          $sNamespace = $val->sNamespace;
          $sStepName  = $val->sStepName;
        }
      }
      if (!$aPreviousStep) {
        $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = '';
      }
      else {
        $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP'] = $aPreviousStep['PAGE'];
        $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = G::loadTranslation("ID_PREVIOUS_STEP");
      }
      $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP'] = $aNextStep['PAGE'];
  
      /** Added By erik date: 16-05-08
      * Description: this was added for the additional database connections */
      G::LoadClass ('dbConnections');
      $oDbConnections = new dbConnections($_SESSION['PROCESS']);
      $oDbConnections->loadAdditionalConnections();
      $stepFilename = "$sNamespace/$sStepName";
      $G_PUBLISH->AddContent('content', $stepFilename );
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
  $oHeadPublisher =& headPublisher::getSingleton();
  $oHeadPublisher->addScriptCode('
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
