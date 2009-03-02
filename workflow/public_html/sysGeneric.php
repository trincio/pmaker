<?php
/*** enable display_error On to caught even fatal errors ***/
ini_set('display_errors','On');
ini_set('error_reporting', E_ALL  );
ini_set('memory_limit', '80M');

$memAcum = 0;
function logMemory ($text=''){
	global $memAcum;
	$dif = memory_get_usage() - $memAcum;
	$memAcum = memory_get_usage();
	$b =  debug_backtrace();
  $aux = sprintf ( "[ usage:%8.1fK acum:%8.3fM] <b>$text</b> in line <b>%s</b> %s </br>",$dif/1024, memory_get_usage() / 1024 / 1024 , $b[0]["line"], $b[0]["file"]) ;
  return $aux;
}
function strip_slashes(&$vVar) {
  if (is_array($vVar)) {
    foreach($vVar as $sKey => $vValue) {
      if (is_array($vValue)) {
        strip_slashes($vVar[$sKey]);
      }
      else {
        $vVar[$sKey] = stripslashes($vVar[$sKey]);
      }
    }
  }
  else {
    $vVar = stripslashes($vVar);
  }
}

if (ini_get('magic_quotes_gpc') == '1') {
  strip_slashes($_POST);
}
$path = Array();
$sf = $_SERVER['SCRIPT_FILENAME'];
//sysGeneric, this file is used to redirect to workspace, the url should by encrypted or not

//***************** In the PATH_SEP we know if the the path separator symbol will be '\\' or '/' **************************
  if ( PHP_OS == 'WINNT' && !strpos ( $_SERVER['DOCUMENT_ROOT'], '/' ) )
   define('PATH_SEP','\\');
  else
   define('PATH_SEP', '/');


//***************** Defining the Home Directory *********************************
$docuroot = explode ( PATH_SEP , $_SERVER['DOCUMENT_ROOT'] );
//  array_pop($docuroot);
  array_pop($docuroot);
  $pathhome = implode( PATH_SEP, $docuroot )  . PATH_SEP;


  //try to find automatically the trunk directory where are placed the RBAC and Gulliver directories
  //in a normal installation you don't need to change it.
  array_pop($docuroot);
  $pathTrunk = implode( PATH_SEP, $docuroot ) . PATH_SEP ;

  array_pop($docuroot);
  $pathOutTrunk = implode( PATH_SEP, $docuroot ) . PATH_SEP ;
  // to do: check previous algorith for Windows  $pathTrunk = "c:/home/";

  define('PATH_HOME',     $pathhome );
  define('PATH_TRUNK',    $pathTrunk  );
  define('PATH_OUTTRUNK', $pathOutTrunk );

//***************** In this file we cant to get the PM paths , RBAC Paths and Gulliver Paths  ******************************
  require_once ( $pathhome . PATH_SEP . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'paths.php' );
//***************** In this file we cant to get the PM definitions  ******************************
  require_once ( $pathhome . PATH_SEP . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'defines.php' );


//******************* Error handler and log error *******************
  //to do: make different environments.  sys
  //G::setErrorHandler ( );
  //G::setFatalErrorHandler ( );

  /*** enable ERROR_SHOW_SOURCE_CODE to display the source code for any WARNING OR NOTICE ***/
  define ('ERROR_SHOW_SOURCE_CODE', true);


 /*** enable ERROR_LOG_NOTICE_ERROR to log Notices messages in default apache log ***/
  //  define ( 'ERROR_LOG_NOTICE_ERROR', true );

//  ************* creat headPublisher singleton *****************
  G::LoadSystem('headPublisher');
  $oHeadPublisher =& headPublisher::getSingleton();
  $oHeadPublisher->addMaborakFile( PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'maborak/core/maborak.js' );
  $oHeadPublisher->addMaborakFile( PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'common/core/common.js' );
  $oHeadPublisher->addMaborakFile( PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'common/core/webResource.js' );
  $oHeadPublisher->addMaborakFile( PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'dveditor/core/dveditor.js' );
  $oHeadPublisher->addMaborakFile( PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'common/tree/tree.js' );
  $oHeadPublisher->addMaborakFile( PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'json/core/json.js' );
  $oHeadPublisher->addMaborakFile( PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'form/core/form.js' );
  $oHeadPublisher->addMaborakFile( PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'form/core/pagedTable.js' );
  $oHeadPublisher->addMaborakFile( PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'grid/core/grid.js' );
  $oHeadPublisher->addMaborakFile( PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'maborak/core/module.panel.js'    , true );
  $oHeadPublisher->addMaborakFile( PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'maborak/core/module.validator.js', true );
  $oHeadPublisher->addMaborakFile( PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'maborak/core/module.app.js'      , true );
  $oHeadPublisher->addMaborakFile( PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'maborak/core/module.rpc.js'      , true );
  $oHeadPublisher->addMaborakFile( PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'maborak/core/module.fx.js'       , true );
  $oHeadPublisher->addMaborakFile( PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'maborak/core/module.drag.js'     , true );
  $oHeadPublisher->addMaborakFile( PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'maborak/core/module.drop.js'     , true );
  $oHeadPublisher->addMaborakFile( PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'maborak/core/module.dom.js'      , true );
  $oHeadPublisher->addMaborakFile( PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'maborak/core/module.abbr.js'     , true );
  $oHeadPublisher->addMaborakFile( PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'maborak/core/module.dashboard.js', true );
  $oHeadPublisher->addMaborakFile( PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'widgets/jscalendar/calendar.js' );
  $oHeadPublisher->addMaborakFile( PATH_GULLIVER_HOME . 'js' . PATH_SEP . 'widgets/suggest/bsn.AutoSuggest_2.1.3.js' );
  $oHeadPublisher->addMaborakFile( PATH_CORE . 'js' . PATH_SEP . 'cases/core/cases.js' , true );
  $oHeadPublisher->addMaborakFile( PATH_CORE . 'js' . PATH_SEP . 'cases/core/cases_Step.js', true );
  $oHeadPublisher->addMaborakFile( PATH_CORE . 'js' . PATH_SEP . 'processmap/core/processmap.js', true );
  $oHeadPublisher->addMaborakFile( PATH_THIRDPARTY . 'htmlarea/editor.js', true );


//************ defining Virtual URLs ****************/
  $virtualURITable = array();
  $virtualURITable['/plugin/(*)']                    = 'plugin';
  $virtualURITable['/(sys*)/(*.js)']                 =  'jsMethod';
  $virtualURITable['/js/(*)']                        = PATH_GULLIVER_HOME . 'js/';
  $virtualURITable['/jscore/(*)']                    = PATH_CORE . 'js/';
  //$virtualURITable['/fckeditor/(*)']               = PATH_THIRDPARTY . 'fckeditor/';
  $virtualURITable['/htmlarea/(*)']                  = PATH_THIRDPARTY . 'htmlarea/';
  $virtualURITable['/sys[a-zA-Z][a-zA-Z0-9]{0,}()/'] = 'sysNamed';
  $virtualURITable['/(sys*)']                        = FALSE;
  $virtualURITable['/errors/(*)']                    = PATH_GULLIVER_HOME . 'methods/errors/';
  $virtualURITable['/controls/(*)']                  = PATH_GULLIVER_HOME . 'methods/controls/';
  $virtualURITable['/html2ps_pdf/(*)']               = PATH_THIRDPARTY . 'html2ps_pdf/';
  $virtualURITable['/Krumo/(*)']                     = PATH_THIRDPARTY . 'krumo/';
  $virtualURITable['/codepress/(*)']                 = PATH_THIRDPARTY . 'codepress/';

  if(defined('PATH_C'))
 {
	  $virtualURITable['/jsform/(*.js)']                 = PATH_C . 'xmlform/';
 }
  /*To sysUnnamed*/
  $virtualURITable['/[a-zA-Z][a-zA-Z0-9]{0,}()'] = 'sysUnnamed';
  $virtualURITable['/(*)'] = PATH_HTML;

//************** verify if we need to redirect or stream the file **************
  if ( G::virtualURI($_SERVER['REQUEST_URI'], $virtualURITable , $realPath )) {
  	// review if the file requested belongs to public_html plugin

    if ( substr ( $realPath, 0,6) == 'plugin' ) {
      /*
       * By JHL Jul 14, 08
       * Another way to get the path of Plugin public_html and stream the correspondent file
       * TODO: $pathsQuery will be used?
       */
      $pathsQuery="";
      //Get the query side
      /*
       * Did we use this variable $pathsQuery for something??
       */
      $forQuery=explode("?",$realPath);
      if(isset($forQuery[1])){
      	$pathsQuery=$forQuery[1];
      }

      //Get that path in array
      $paths = explode ( PATH_SEP, $forQuery[0] );
      //remove the "plugin" word from
      $paths[0] = substr ( $paths[0],6);
      //Get the Plugin Folder, always the first element
      $pluginFolder=array_shift($paths);
      //The other parts are the realpath into public_html (no matter how many elements)
      $filePath=implode(PATH_SEP,$paths);
      $pluginFilename = PATH_PLUGINS . $pluginFolder . PATH_SEP . 'public_html'. PATH_SEP . $filePath;
      if ( file_exists ( $pluginFilename ) ) {
        	G::streamFile ( $pluginFilename );
      }

      die;
	  }

  	switch ( $realPath  ) {
    case 'sysUnnamed' :
      require_once('sysUnnamed.php'); die;
    	break;
    case 'sysNamed' :
      header('location : ' . $_SERVER['REQUEST_URI'] . 'en/green/login/login' );
      die;
	    break;
    case 'jsMethod' :
		  G::parseURI ( getenv( "REQUEST_URI" ) );
		  $filename = PATH_METHODS . SYS_COLLECTION . '/' . SYS_TARGET . '.js';
    	G::streamFile ( $filename );
    	die;
	    break;
    default :
      $realPath = explode('?', $realPath);
    	G::streamFile ( $realPath[0] );
    	die;
    }
  }

//************** verify if the URI is encrypted or not **************
  G::parseURI ( getenv( "REQUEST_URI" ) );
  $oHeadPublisher->addMaborakFile( PATH_GULLIVER_HOME . 'js' . PATH_SEP . "widgets/jscalendar/lang/calendar-" . SYS_LANG . ".js");
  define( 'SYS_URI' , '/sys' .  SYS_TEMP . '/' . SYS_LANG . '/' . SYS_SKIN . '/' );

  require_once ( PATH_THIRDPARTY . 'krumo' . PATH_SEP . 'class.krumo.php' );

//***************** Call Gulliver Classes **************************

  G::LoadThirdParty('pear/json','class.json');
  G::LoadThirdParty('smarty/libs','Smarty.class');
  G::LoadSystem('error');
  G::LoadSystem('dbconnection');
  G::LoadSystem('dbsession');
  G::LoadSystem('dbrecordset');
  G::LoadSystem('dbtable');
  G::LoadSystem('rbac' );
  G::LoadSystem('publisher');
  G::LoadSystem('templatePower');
  G::LoadSystem('xmlDocument');
  G::LoadSystem('xmlform');
  G::LoadSystem('xmlformExtension');
  G::LoadSystem('form');
  G::LoadSystem('menu');
  G::LoadSystem("xmlMenu");
  G::LoadSystem('dvEditor');
  G::LoadSystem('table');
  G::LoadSystem('pagedTable');
  //G::LoadSystem("fckEditor");
  //G::LoadSystem("htmlArea");

  /***************** Installer  ******************************/
  if(!defined('PATH_DATA') || !file_exists(PATH_DATA))
  {
	  if((SYS_TARGET==='installServer'))
	  {
		   $phpFile = G::ExpandPath('methods') ."install/installServer.php";
		  require_once($phpFile);
		   die();
	  }
	  else
	  {
		   $phpFile = G::ExpandPath('methods') ."install/install.php";
		  require_once($phpFile);
		   die();
	  }
  }

  /***************** Installer  ******************************/

  //***************** database and workspace definition  ************************
  //if SYS_TEMP exists, the URL has a workspace, now we need to verify if exists their db.php file
  if ( defined('SYS_TEMP') && SYS_TEMP != '')
  {
	  //this is the default, the workspace db.php file is in /shared/workflow/sites/SYS_SYS
    if ( file_exists( PATH_DB .  SYS_TEMP . '/db.php' ) ) {
      require_once( PATH_DB .  SYS_TEMP . '/db.php' );
      define ( 'SYS_SYS' , SYS_TEMP );
    }
    else {
      $sysParts = explode('-',SYS_TEMP);
      //try to find the definition in the module-file
      if ( count($sysParts) == 3) {
        $fileName = 'dbmodule_'.$sysParts[1].'.php';
        $DB_INDEX = 0;
        $DB_MODULE = array();

        if ( !file_exists( PATH_DB . $fileName)) {
          header ("location: errors/error701.php");
          die;
        }
        require_once ( PATH_DB . $fileName );
        $moduleName = $DB_MODULE[$DB_INDEX]['name'];
        $modulePath = $DB_MODULE[$DB_INDEX]['path'];
        $moduleType = $DB_MODULE[$DB_INDEX]['type'];
        if ( !file_exists( $modulePath )) {
          header ("location: /errors/error704.php"); die;
        }
        if ( $moduleType == 'single-file' ) {
          $workspaceDB = $modulePath. 'db_'. $sysParts[2] . '.php';
        }
        else {
          $workspaceDB = $modulePath.  $sysParts[2] . '/db.php';
        }
        if ( !file_exists( $workspaceDB )) {
          header ("location: /errors/error704.php"); die;
        }
        require_once( $workspaceDB ) ;
        define ( 'SYS_SYS', $sysParts[2]);
      }
      else {
       $aMessage['MESSAGE'] = G::LoadTranslation ('ID_NOT_WORKSPACE');
       $G_PUBLISH          = new Publisher;
       $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
       G::RenderPage( 'publish' );
       die;
      }
    }
  }
  else {  //when we are in global pages, outside any valid workspace


    if ((SYS_TARGET==='sysLoginVerify') || (SYS_TARGET==='sysLogin') || (SYS_TARGET==='newSite')) {
  	  $phpFile = G::ExpandPath('methods') . SYS_COLLECTION . "/" . SYS_TARGET.'.php';
  	  require_once($phpFile);
  	  die();
    }
    else {
      require_once( PATH_METHODS . "login/sysLogin.php" ) ;
      die();
    }
  }

//***************** PM Paths DATA **************************
  define( 'PATH_DATA_SITE',                 PATH_DATA      . 'sites/' . SYS_SYS . '/');
  define( 'PATH_DOCUMENT',                  PATH_DATA_SITE . 'files/' );
  define( 'PATH_DATA_MAILTEMPLATES',        PATH_DATA_SITE . 'mailTemplates/' );
  define( 'PATH_DATA_PUBLIC',               PATH_DATA_SITE . 'public/' );
  define( 'PATH_DATA_REPORTS',              PATH_DATA_SITE . 'reports/' );
  define( 'PATH_DYNAFORM',                  PATH_DATA_SITE . 'xmlForms/' );
  define( 'PATH_IMAGES_ENVIRONMENT_FILES',  PATH_DATA_SITE . 'usersFiles'.PATH_SEP);
  define( 'PATH_IMAGES_ENVIRONMENT_USERS',  PATH_DATA_SITE . 'usersPhotographies'.PATH_SEP);


//***************** Plugins **************************
	G::LoadClass('plugin');
  //here we are loading all plugins registered
  //the singleton has a list of enabled plugins

  $sSerializedFile = PATH_DATA_SITE . 'plugin.singleton';
  $oPluginRegistry =& PMPluginRegistry::getSingleton();
  if ( file_exists ($sSerializedFile) )
    $oPluginRegistry->unSerializeInstance( file_get_contents  ( $sSerializedFile ) );


//***************** create $G_ENVIRONMENTS dependent of SYS_SYS **************************

  define ( 'G_ENVIRONMENT', G_DEV_ENV );
  $G_ENVIRONMENTS = array (
    G_PRO_ENV => array (
      'dbfile' => PATH_DB . 'production' . PATH_SEP . 'db.php' ,
      'cache' => 1,
      'debug' => 0,
    ) ,
    G_DEV_ENV => array (
      'dbfile' => PATH_DB . SYS_SYS . PATH_SEP . 'db.php',
      'datasource' => 'workflow',
      'cache' => 0,
      'debug' => 0,  //<--- change this value to 1, to have a detailed sql log in PATH_DATA . 'log' . PATH_SEP . 'workflow.log'
    ) ,
    G_TEST_ENV => array (
      'dbfile' => PATH_DB . 'test' . PATH_SEP . 'db.php' ,
      'cache' => 0,
      'debug' => 0,
    )
 );

  // setup propel definitions and logging
  //try {
    require_once ( "propel/Propel.php" );
    require_once ( "creole/Creole.php" );

    if ( $G_ENVIRONMENTS[ G_ENVIRONMENT ]['debug'] ) {
      require_once ( "Log.php" );

      // register debug connection decorator driver
      Creole::registerDriver('*', 'creole.contrib.DebugConnection');

      // itialize Propel with converted config file
      Propel::init( PATH_CORE . "config/databases.php" );

      //log file for workflow database
      $logFile = PATH_DATA . 'log' . PATH_SEP . 'workflow.log';
      $logger = Log::singleton('file', $logFile, 'wf ' . SYS_SYS, null, PEAR_LOG_INFO);
      Propel::setLogger($logger);
      $con = Propel::getConnection('workflow');
      if ($con instanceof DebugConnection) $con->setLogger($logger);

      //log file for rbac database
      $logFile = PATH_DATA . 'log' . PATH_SEP . 'rbac.log';
      $logger = Log::singleton('file', $logFile, 'rbac ' . SYS_SYS, null, PEAR_LOG_INFO);
      Propel::setLogger($logger);
      $con = Propel::getConnection('rbac');
      if ($con instanceof DebugConnection) $con->setLogger($logger);
    }
    else
      Propel::init( PATH_CORE . "config/databases.php" );

    Creole::registerDriver('dbarray', 'creole.contrib.DBArrayConnection');

    //***************** Session Initializations **************************/
      ini_alter( 'session.auto_start', '1' );
      ini_alter( 'register_globals',   'Off' );
      session_start();
      ob_start();

    //********* Log Page Handler *************
    //  logPage ( $URL , SYS_CURRENT_PARMS);

    //setup plugins
  $oPluginRegistry->setupPlugins(); //get and setup enabled plugins

    //*********jump to php file in methods directory *************
    $collectionPlugin = '';
    if ( $oPluginRegistry->isRegisteredFolder( SYS_COLLECTION ) ) {
      $phpFile = PATH_PLUGINS . SYS_COLLECTION . PATH_SEP . SYS_TARGET.'.php';
      $targetPlugin = explode( '/', SYS_TARGET );
      $collectionPlugin = $targetPlugin[0];
    }
    else
      $phpFile = G::ExpandPath('methods') . SYS_COLLECTION . PATH_SEP . SYS_TARGET.'.php';

    //the index.php file, this new feature will allow automatically redirects to valid php file inside the methods directory
    if ( SYS_TARGET == '' ) {
      $phpFile = str_replace ( '.php', 'index.php', $phpFile );
      $phpFile = include ( $phpFile );
    }
	$bWE = false;
    if ( substr(SYS_COLLECTION , 0,8) === 'gulliver' ) {
      $phpFile = PATH_GULLIVER_HOME . 'methods/' . substr( SYS_COLLECTION , 8) . SYS_TARGET.'.php';
    }
    else {
    	//when the file is part of the public directory of any PROCESS
      if (preg_match('/^[0-9][[:alnum:]]+$/', SYS_COLLECTION) == 1)
      { //the pattern is /sysSYS/LANG/SKIN/PRO_UID/file
        $auxPart = explode ( '/' ,  $_SERVER['REQUEST_URI']);
        $extPart = explode ( '.' , $auxPart[ count($auxPart)-1] );
        $extension = $extPart[ count($extPart)-1 ];
        $phpFile = PATH_DATA_SITE . 'public' . PATH_SEP .  SYS_COLLECTION . PATH_SEP . urldecode ($auxPart[ count($auxPart)-1]);
        if ( $extension != 'php' ) {
          G::streamFile ( $phpFile );
          die;
        }
        $bWE = true;
        //$phpFile = PATH_DATA_SITE . 'public' . PATH_SEP .  SYS_COLLECTION . PATH_SEP . $auxPart[ count($auxPart)-1];
      }
      if ( ! file_exists( $phpFile ) ) {
          $_SESSION['phpFileNotFound'] = $phpFile;
          header ("location: /errors/error404.php");
          die;
      }
    }


//  ***************** enable rbac **************************

    $RBAC =& RBAC::getSingleton();
    $RBAC->sSystem = 'PROCESSMAKER';

//  ***************** Headers **************************
    if ( ! defined('EXECUTE_BY_CRON') ) {
      header("Expires: Tue, 19 Jan 1999 04:30:00 GMT");
      header("Last-Modified: Tue, 19 Jan 1999 04:30:00 GMT");
      header('Cache-Control: no-cache, must-revalidate, post-check=0,pre-check=0 ');
      header('P3P: CP="CAO PSA OUR"');

      if(isset( $_SESSION['USER_LOGGED'] )) {
        $RBAC->initRBAC();
        $RBAC->loadUserRolePermission( $RBAC->sSystem, $_SESSION['USER_LOGGED'] );
      }
      else {
        //This sentence is used when you lost the Session
        if ( SYS_TARGET != 'authentication' and  SYS_TARGET != 'login' and  SYS_TARGET != 'login_Ajax'
        and  SYS_TARGET != 'dbInfo'         and  SYS_TARGET != 'sysLoginVerify' and SYS_TARGET != 'processes_Ajax'
        and  SYS_TARGET != 'updateTranslation'
        and  SYS_COLLECTION != 'services' and SYS_COLLECTION != 'tracker' and $collectionPlugin != 'services'
        and $bWE != true and SYS_TARGET != 'defaultAjaxDynaform' and SYS_TARGET != 'cases_ShowDocument') {
          $bRedirect = true;
          if (isset($_GET['sid'])) {
            G::LoadClass('sessions');
            $oSessions = new Sessions();
            if ($aSession = $oSessions->verifySession($_GET['sid'])) {
              require_once 'classes/model/Users.php';
              $oUser = new Users();
              $aUser = $oUser->load($aSession['USR_UID']);
              $_SESSION['USER_LOGGED']  = $aUser['USR_UID'];
              $_SESSION['USR_USERNAME'] = $aUser['USR_USERNAME'];
              $bRedirect = false;
              $RBAC->initRBAC();
              $RBAC->loadUserRolePermission( $RBAC->sSystem, $_SESSION['USER_LOGGED'] );
            }
          }
          if ($bRedirect) {
            if (empty($_POST)) {
              header('location: ' . SYS_URI . 'login/login?u=' . urlencode($_SERVER['REQUEST_URI']));
            }
            else {
              header('location: ' . SYS_URI . 'login/login');
            }
            die();
          }
        }
      }

      require_once( $phpFile );
      if ( defined('SKIP_HEADERS') ) {
	    header("Expires: " . gmdate("D, d M Y H:i:s", mktime( 0,0,0,date('m'),date('d'),date('Y') + 1) ) . " GMT");
        header('Cache-Control: public');
        header('Pragma: ');
      }
      ob_end_flush();
    }
/*
  }
  catch ( Exception $e ) {
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH  = new Publisher;
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish' );
    die;
  }
*/

//$auxMem = sprintf ( "memory_limit:%s  usage:%4.2fM \$_SESSION: %4.2fKb=%d",
//  ini_get("memory_limit"), memory_get_usage() / 1024 / 1024,strlen( serialize($_SESSION)) / 1024 , count($_SESSION) );
//error_log($auxMem);
