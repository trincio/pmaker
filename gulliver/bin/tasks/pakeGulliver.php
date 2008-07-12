<?php
/**
 * pakeGulliver.php
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
//dont work mb_internal_encoding('UTF-8');

pake_desc('gulliver version');
pake_task('version',  'project_exists');

pake_desc('create poedit file for system labels');
pake_task('create-poedit-file',  'project_exists');

pake_desc('generate a unit test php file for an existing class');
pake_task('generate-unit-test-class',  'project_exists');

pake_desc('generate basic CRUD files for an existing class');
pake_task('generate-crud',  'project_exists');

pake_desc('build new project');
pake_task('new-project',  'project_exists');

pake_desc('build new plugin');
pake_task('new-plugin',  'project_exists');


function run_version( $task, $args)
{
  printf("Gulliver version %s\n", pakeColor::colorize(trim(file_get_contents( PATH_GULLIVER . 'VERSION')), 'INFO'));
  exit(0);
}

function isUTF8($str) {
  if ($str === mb_convert_encoding(mb_convert_encoding($str, "UTF-32", "UTF-8"), "UTF-8", "UTF-32")) {
    return true;
  } else {
    return false;
  }
}
    
function strip_quotes( $text )
{
  if ( !isUTF8 ( $text ) ) 
    $text = utf8_encode ( $text ); 
  return str_replace('"',"", $text );
}

function prompt ( $text ) {
    printf("$text %s ", pakeColor::colorize( ':', 'INFO'));
    # 4092 max on win32 fopen

    //$fp=fopen("php://stdin", "r");
    $fp=fopen("/dev/tty", "r");
    $in=fgets($fp,4094);
    fclose($fp);

    # strip newline
    (PHP_OS == "WINNT") ? ($read = str_replace("\r\n", "", $in)) : ($read = str_replace("\n", "", $in));

    return $read;
}

function createPngLogo (  $filePng, $text ) {
  $im = imagecreatetruecolor (162,50);
  $orange = imagecolorallocate($im, 140, 120, 0);
  $white  = imagecolorallocate($im,255,255,255);
  $black  = imagecolorallocate($im,0,0,0);
  $grey   = imagecolorallocate($im,100,100,100);
  $yellow = imagecolorallocatealpha($im, 255, 255, 10, 95);
  $red    = imagecolorallocatealpha($im, 255, 10, 10, 95);
  $blue   = imagecolorallocatealpha($im, 10, 10, 255, 95);
  $transparent = imagecolorallocatealpha($im, 0, 0, 0, 127);
  
  imagefill($im, 0, 0, $white);
  imagestring($im, 4, 50, 14, $text, $orange);

  // drawing 3 overlapped circle
  imagefilledellipse($im, 25, 20, 27, 25, $yellow);
  imagefilledellipse($im, 15, 30, 27, 25, $red);
  imagefilledellipse($im, 30, 30, 27, 25, $blue);
  
  imagefill($im, 0, 0, $transparent);
  imagesavealpha($im, true);  
  imagepng($im, $filePng);

  $aux = explode (PATH_SEP, $filePng );
  $auxName = $aux[ count($aux)-2 ] . PATH_SEP . $aux[ count($aux)-1 ];
  $iSize = filesize ( $filePng );
  printf("saved %s bytes in file %s [%s]\n", pakeColor::colorize( $iSize, 'INFO'), pakeColor::colorize( $auxName, 'INFO'), pakeColor::colorize( $aux[ count($aux)-1 ], 'INFO') );    
  
}
    
function run_generate_unit_test_class ( $task, $args) 
{
  //the class filename in the first argument
  $class = $args[0];

  //try to find the class in classes directory
  $classFilename = PATH_CORE . 'classes' . PATH_SEP . 'class.' . $args[0] . '.php';
  if ( file_exists ( $classFilename ) )  
    printf("class found in %s \n", pakeColor::colorize( $classFilename, 'INFO'));
  else {
    printf("class %s not found \n", pakeColor::colorize( $class, 'ERROR'));
    exit (0);
  }  

  include( 'test' . PATH_SEP . 'bootstrap' . PATH_SEP . 'unit.php');
  G::LoadThirdParty('smarty/libs','Smarty.class');
  G::LoadSystem ( 'error');
  G::LoadSystem ( 'xmlform');
  G::LoadSystem ( 'xmlDocument');
  G::LoadSystem ( 'form');
  G::LoadSystem ( 'dbconnection');
  G::LoadSystem ( 'dbsession');
  G::LoadSystem ( 'dbrecordset');
  G::LoadSystem ( 'dbtable');
  G::LoadClass ( 'application');

  require_once ( $classFilename );

  $unitFilename = PATH_GULLIVER_HOME . 'bin' . PATH_SEP . 'tasks' . PATH_SEP . 'unitTest.tpl';
    
  $smarty = new Smarty();
  
  $smarty->template_dir = PATH_GULLIVER . 'bin' . PATH_SEP . 'tasks';
  $smarty->compile_dir  = PATH_SMARTY_C;
  $smarty->cache_dir    = PATH_SMARTY_CACHE;
  $smarty->config_dir   = PATH_THIRDPARTY . 'smarty/configs';
  
  printf("using unit file in %s \n", pakeColor::colorize( $unitFilename, 'INFO'));
  $smarty->assign('className', ucwords ($class)  );
  $smarty->assign('classFile', $class  );


  //get the method list
  $reflect = new ReflectionClass( $class );
  $methods = array();
  
	$method = array ( );
	$testItems = 0;
  foreach($reflect->getMethods() as $reflectmethod) {
  	$params = '';
  	foreach ( $reflectmethod->getParameters() as $key => $row ) {
  	  if ( $params != '' ) $params .= ', ';
  	  $params .= '$' . $row->name;  
  	}
 		$testItems++;
  	$methods[ $reflectmethod->getName() ] = $params;
  }
  $smarty->assign('methods', $methods );
  $smarty->assign('testItems', ( count($methods)*2) + 2 );

  // fetch smarty output
  $content = $smarty->fetch( $unitFilename );

  //saving the content in the output file
  if ( defined ('MAIN_POFILE') && MAIN_POFILE != '' ) 
    $unitFilename = PATH_CORE . 'test' . PATH_SEP . 'unit' . PATH_SEP . MAIN_POFILE . PATH_SEP . 'class' . ucwords( $class ) . 'Test.php';
  else
    $unitFilename = PATH_CORE . 'test' . PATH_SEP . 'unit' . PATH_SEP . 'class' . ucwords ($class) . 'Test.php';
  printf("creating unit file in %s \n", pakeColor::colorize( $unitFilename, 'INFO'));
  $fp = fopen ( $unitFilename, 'w' );
  fprintf ( $fp, $content );
  fclose ( $fp );
  
  exit (0);
}

  function convertPhpName ($f ) {
    $upper = true;
    $res = '';
    for ( $i =0;  $i < strlen ($f); $i++ ) {
      $car = substr ( $f, $i, 1);
      if ( $car == '_' ) 
        $upper = true;
      else {
        if ( $upper ) {
          $res .= strtoupper ( $car );
          $upper = false;
        }
        else
          $res .= strtolower ( $car );
      }
    }
    return $res;
  }
  function savePluginFile ( $fName, $tplName, $class, $tableName, $fields = null ) {
    $pluginOutDirectory = PATH_OUTTRUNK . 'plugins' . PATH_SEP . $class . PATH_SEP;
    
    $pluginFilename = $pluginOutDirectory . $fName;
    
    $pluginTpl      = PATH_GULLIVER_HOME . 'bin' . PATH_SEP . 'tasks' . PATH_SEP . $tplName . '.tpl';
    $template = new TemplatePower( $pluginTpl );
    $template->prepare();
    $template->assign ( 'className', $class );
    $template->assign ( 'tableName', $tableName );
    $template->assign ( 'menuId', 'ID_' . strtoupper($class) );
    
    if ( is_array ($fields) ) {
      foreach ( $fields as $block => $data ) {
        $template->gotoBlock( "_ROOT" );
        if ( is_array( $data) )
          foreach ( $data as $rowId => $row ) {
            $template->newBlock( $block );
            foreach ( $row as $key => $val ) 
              $template->assign( $key, $val );
          }
        else
          $template->assign( $block, $data );
      }
    }
        
    $content = $template->getOutputContent();
    $iSize = file_put_contents ( $pluginFilename, $content );
    printf("saved %s bytes in file %s [%s]\n", pakeColor::colorize( $iSize, 'INFO'), pakeColor::colorize( $fName, 'INFO'), pakeColor::colorize( $tplName, 'INFO') );    
  }  

function run_generate_crud ( $task, $args) 
{
  ini_set('display_errors','on');
  ini_set('error_reporting', E_ERROR);

  // the environment for poedit always is Development
  define ( 'G_ENVIRONMENT', G_DEV_ENV );

  //the class filename in the first argument
  if ( !isset($args[0]) ) {
    printf("Error: %s\n", pakeColor::colorize( 'you must specify a valid classname ', 'ERROR'));
    exit (0);
  }  
  $class = $args[0];
  //second parameter is the table name, by default is the same classname in uppercase.
  $tableName = isset($args[1])? $args[1] : strtoupper ($class) ;

  //try to find the class in classes directory
  $classFilename = PATH_CORE . 'classes' . PATH_SEP . 'model' . PATH_SEP . $args[0] . '.php';
  if ( file_exists ( $classFilename ) )  
    printf("class found in %s \n", pakeColor::colorize( $classFilename, 'INFO'));
  else {
    printf("class %s not found \n", pakeColor::colorize( $class, 'ERROR'));
    exit (0);
  }  

  require_once ( "propel/Propel.php" );
  require_once ( $classFilename );
  G::LoadSystem ('templatePower');

  Propel::init(  PATH_CORE . "config/databases.php");  
  $configuration = Propel::getConfiguration();
  $connectionDSN = $configuration['datasources']['workflow']['connection'];
  printf("using DSN Connection %s \n", pakeColor::colorize( $connectionDSN, 'INFO'));

  $dirs = explode ( PATH_SEP, PATH_HOME);
  $projectName = $dirs[ count($dirs) -1 ];

  if ( strlen ( trim( $projectName) ) == 0 )  {
    printf("Project name not found \n", pakeColor::colorize( $class, 'ERROR'));
    exit (0);
  }  
  
  printf("using Project Name %s \n", pakeColor::colorize( $projectName, 'INFO'));
die;
  $pluginDirectory = PATH_PLUGINS . $class;
  $pluginOutDirectory = PATH_OUTTRUNK . 'plugins' . PATH_SEP . $class;

  G::verifyPath ( $pluginOutDirectory, true );
  G::verifyPath ( $pluginOutDirectory. PATH_SEP . $class, $pluginDirectory );
  
  //G::verifyPath ( $pluginDirectory, true );
  
  //main php file 
  savePluginFile ( $class . '.php', 'pluginMainFile', $class, $tableName );

  //menu  
  savePluginFile ( $class . PATH_SEP . 'menu' . $class . '.php', 'pluginMenu', $class, $tableName );

  //default list
  savePluginFile ( $class . PATH_SEP . $class . 'List.php', 'pluginList', $class, $tableName );


  //parse the schema file in order to get Table definition
  $schemaFile = PATH_CORE . 'config' . PATH_SEP . 'schema.xml';
  $xmlContent = file_get_contents ( $schemaFile );
  $s = simplexml_load_file( $schemaFile );

  //default xmlform 
  //load the $fields array with fields data for an xmlform.
  $fields= array();
  foreach ($s->table as $key=>$table ) {
    if ( $table['name'] == $tableName  )
      foreach ( $table->column as $kc => $column ) {
        //print $column['name'] . ' ' .$column['type'] . ' ' .$column['size'] . ' ' .$column['required'] . ' ' .$column['primaryKey'];
        //print "\n";
        $maxlength = $column['size'];
        $size      = ( $maxlength > 60 ) ? 60 : $maxlength;
        $type      = $column['type'];
        $field = array ( 'name' => $column['name'], 'type' => $type, 'size' => $size, 'maxlength' => $maxlength );
        $fields['fields'][] = $field;
      }
  }
  savePluginFile ( $class . PATH_SEP . $class . '.xml', 'pluginXmlform', $class, $tableName, $fields );


  //xmlform for list
  //load the $fields array with fields data for PagedTable xml.
  $fields= array();
  $primaryKey ='';
  foreach ($s->table as $key=>$table ) {
    if ( $table['name'] == $tableName  )
      foreach ( $table->column as $kc => $column ) {
        //print $column['name'] . ' ' .$column['type'] . ' ' .$column['size'] . ' ' .$column['required'] . ' ' .$column['primaryKey'];
        //print "\n";
        $size      = ( $column['size'] > 30 ) ? 30 : $column['size'];
        $type      = $column['type'];
        if ( $column['primaryKey'] ) 
          if ( $primaryKey == '' ) 
            $primaryKey .= '@@' . $column['name'];
          else
            $primaryKey .= '|@@' . $column['name'];
            
        $field = array ( 'name' => $column['name'], 'type' => $type, 'size' => $size );
        $fields['fields'][] = $field;
      }
  }
  $fields['primaryKey'] = $primaryKey;
  savePluginFile ( $class . PATH_SEP . $class . 'List.xml', 'pluginXmlformList', $class, $tableName, $fields );

  //default edit
  $fields= array();$index =0;
  $keylist = '';
  foreach ($s->table as $key=>$table ) {
    if ( $table['name'] == $tableName  )
      foreach ( $table->column as $kc => $column ) {
        $name =  $column['name'];
        $phpName = convertPhpName ($name);
        $field = array ( 'name' => $name, 'phpName' => $phpName, 'index' => $index++ );
        if ( $column['primaryKey'] ) {
          if ( $keylist == '' ) 
            $keylist .= '$' .$phpName;
          else
            $keylist .= ', $' . $phpName;
          $fields['keys'][] = $field;
        }
        $fields['fields'][] = $field;
        $fields['fields2'][] = $field;
      }
  }
  $fields['keylist'] = $keylist;
  savePluginFile ( $class . PATH_SEP . $class . 'Edit.php', 'pluginEdit', $class, $tableName, $fields );
  savePluginFile ( $class . PATH_SEP . $class . 'Save.php', 'pluginSave', $class, $tableName, $fields );

  printf("creting symlinks %s \n", pakeColor::colorize( $pluginDirectory, 'INFO'));
  symlink ($pluginOutDirectory. PATH_SEP . $class. '.php', PATH_PLUGINS . $class . '.php');
  symlink ($pluginOutDirectory. PATH_SEP . $class,         $pluginDirectory);

  exit (0);
}

function run_new_plugin ( $task, $args) 
{
  ini_set('display_errors','on');
  ini_set('error_reporting', E_ERROR);

  // the environment for poedit always is Development
  define ( 'G_ENVIRONMENT', G_DEV_ENV );

  //the plugin name in the first argument
  if ( !isset($args[0]) ) {
    printf("Error: %s\n", pakeColor::colorize( 'you must specify a valid name for the plugin', 'ERROR'));
    exit (0);
  }  
  $pluginName = $args[0];
  
  //second parameter is the table name, by default is the same classname in uppercase.
  //$tableName = isset($args[1])? $args[1] : strtoupper ($class) ;

  require_once ( "propel/Propel.php" );
  G::LoadSystem ('templatePower');

  Propel::init(  PATH_CORE . "config/databases.php");  
  $configuration = Propel::getConfiguration();
  $connectionDSN = $configuration['datasources']['workflow']['connection'];
  printf("using DSN Connection %s \n", pakeColor::colorize( $connectionDSN, 'INFO'));

  $pluginDirectory = PATH_PLUGINS . $pluginName;
  $pluginOutDirectory = PATH_OUTTRUNK . 'plugins' . PATH_SEP . $pluginName;
  $pluginHome = PATH_OUTTRUNK . 'plugins' . PATH_SEP . $pluginName. PATH_SEP . $pluginName;

  //verify if plugin exists, and then ask for overwrite
  $pluginClassFilename = PATH_PLUGINS . $pluginName . PATH_SEP . 'class.' . $pluginName . '.php';
  if ( is_file ( $pluginClassFilename ) ) { 
    printf("The plugin %s exists in this file %s \n", pakeColor::colorize( $pluginName, 'ERROR'), pakeColor::colorize( $pluginClassFilename, 'INFO') );
    $overwrite = strtolower ( prompt ( 'Do you want to create a new plugin? [Y/n]' ));
    if ( $overwrite == 'n' ) die ;
  }

  printf("creating plugin directory %s \n", pakeColor::colorize( $pluginOutDirectory, 'INFO'));
  
  G::verifyPath ( $pluginOutDirectory, true );
  G::verifyPath ( $pluginHome . PATH_SEP . 'public_html', true );
  G::verifyPath ( $pluginHome . PATH_SEP . 'config', true );
  G::verifyPath ( $pluginHome . PATH_SEP . 'data', true );
  
  //config
  savePluginFile ( $pluginName . PATH_SEP . 'config' .PATH_SEP . 'schema.xml', 'pluginSchema.xml', $pluginName, $pluginName );
  savePluginFile ( $pluginName . PATH_SEP . 'config' .PATH_SEP . 'propel.ini', 'pluginPropel.ini', $pluginName, $pluginName );
  savePluginFile ( $pluginName . PATH_SEP . 'config' .PATH_SEP . 'propel.mysql.ini', 'pluginPropel.mysql.ini', $pluginName, $pluginName );
  
  //create a logo to use instead the Workspace logo
  $changeLogo = strtolower ( prompt ( 'Change system logo [y/N]' ));

  $fields = array();
  if ( $changeLogo == 'y' ) {
    $filePng = $pluginHome . PATH_SEP . 'public_html' . PATH_SEP . $pluginName . '.png';
    createPngLogo ( $filePng, $pluginName );
    $fields['changeLogo'][] = array( 'className' => $pluginName);
  }

  //menu  
  $menu = strtolower ( prompt ( 'Create an example Page [Y/n]' ));
  if ( $menu == 'y' ) {
    $fields['menu'][] = array( 'className' => $pluginName );
    savePluginFile ( $pluginName . PATH_SEP . 'menu' . $pluginName . '.php', 'pluginMenu', $pluginName, $pluginName );
    savePluginFile ( $pluginName . PATH_SEP . $pluginName . 'List.php', 'pluginWelcome.php', $pluginName, $pluginName );
    savePluginFile ( $pluginName . PATH_SEP . 'welcome.xml', 'welcome.xml', $pluginName, $pluginName );
  }

  $externalStep = strtolower ( prompt ( 'Create external step for Processmaker[y/N]' ));
  if ( $externalStep == 'y' ) {
    $fields['externalStep'][] = array( 'className' => $pluginName, 'GUID' => G::generateUniqueID() );
    savePluginFile ( $pluginName . PATH_SEP . 'step' . $pluginName . '.php', 'pluginStep', $pluginName, $pluginName );
  }


  $dashboard = strtolower ( prompt ( 'Create an element for the Processmaker Dashboard [y/N]' ));
  if ( $dashboard == 'y' ) {
    $fields['dashboard'][] = array( 'className' => $pluginName);
    savePluginFile ( $pluginName . PATH_SEP . 'drawChart.php', 'pluginDrawChart.php', $pluginName, $pluginName, $fields );
  }

  $report = strtolower ( prompt ( 'Create a Report for Processmaker [y/N]' ));
  if ( $report == 'y' ) {
    $fields['report'][] = array( 'className' => $pluginName);
    savePluginFile ( $pluginName . PATH_SEP . 'report.xml', 'pluginReport.xml', $pluginName, $pluginName, $fields );
  }


  //main php file 
  savePluginFile ( $pluginName . '.php', 'pluginMainFile', $pluginName, $pluginName, $fields );
  savePluginFile ( $pluginName . PATH_SEP . 'class.' . $pluginName . '.php', 'pluginClass', $pluginName, $pluginName, $fields );

  printf("creating symlinks %s \n", pakeColor::colorize( $pluginDirectory, 'INFO'));
  symlink ($pluginOutDirectory. PATH_SEP . $pluginName. '.php', PATH_PLUGINS . $pluginName . '.php');
  symlink ($pluginOutDirectory. PATH_SEP . $pluginName,         $pluginDirectory);

  exit (0);
}


function run_create_poedit_file( $task, $args)
{
  // the environment for poedit always is Development
  define ( 'G_ENVIRONMENT', G_DEV_ENV );

  //the output language .po file
  $lgOutId = isset ( $args[0] ) ? $args[0] : 'en';
  $countryOutId = isset ( $args[1] ) ? strtoupper ( $args[1] ) : 'US';
  $verboseFlag = isset ($args[2]) ? $args[2] == true : false;
  
  require_once ( "propel/Propel.php" );
  require_once ( "classes/model/Translation.php" );
  require_once ( "classes/model/Language.php" );
  require_once ( "classes/model/IsoCountry.php" );


  Propel::init(  PATH_CORE . "config/databases.php");  
  $configuration = Propel::getConfiguration();
  $connectionDSN = $configuration['datasources']['propel']['connection'];
  printf("using DSN Connection %s \n", pakeColor::colorize( $connectionDSN, 'INFO'));

  printf("checking Language table \n");
  $c = new Criteria();
  $c->add ( LanguagePeer::LAN_ENABLED, "1" );
  $c->addor ( LanguagePeer::LAN_ENABLED, "0" );
  
  $languages = LanguagePeer::doSelect ( $c );
  $langs = array ();
  $lgIndex = 0;
  $findLang = false;
  $langDir = 'english';
  $langId  = 'en';
  foreach ( $languages as $rowid => $row )  {
    $lgIndex ++;
    $langs[ $row->getLanId() ] =  $row->getLanName() ; 
    if ( $lgOutId != '' && $lgOutId == $row->getLanId() ) {
      $findLang = true;
      $langDir = strtolower ( $row->getLanName() );
      $langId  = $row->getLanId();
    }
  }
  printf("read %s entries from language table\n", pakeColor::colorize( $lgIndex, 'INFO'));

  printf("checking iso_country table \n");
  $c = new Criteria();
  $c->add ( IsoCountryPeer::IC_UID, NULL, Criteria::ISNOTNULL );
  
  $countries = IsoCountryPeer::doSelect ( $c );
  $countryIndex = 0;
  $findCountry = false;
  $countryDir = 'UNITED STATES';
  $countryId  = 'US';
  foreach ( $countries as $rowid => $row )  {
    $countryIndex ++;
    if ( $countryOutId != '' && $countryOutId == $row->getICUid() ) {
      $findCountry = true;
      $countryDir = strtoupper ( $row->getICName() );
      $countryId  = $row->getICUid();
    }
  }
  printf("read %s entries from iso_country table\n", pakeColor::colorize( $countryIndex, 'INFO'));

  if ( $findLang == false && $lgOutId != '' ) {
    printf("%s \n", pakeColor::colorize( "'$lgOutId' is not a valid language ", 'ERROR'));
    die();
  }
  else {
    printf("language: %s\n", pakeColor::colorize( $langDir, 'INFO'));
  }

  if ( $findCountry == false && $countryOutId != '' ) {
    printf("%s \n", pakeColor::colorize( "'$countryOutId' is not a valid country ", 'ERROR'));
    die();
  }
  else {
    printf("country: [%s] %s\n", pakeColor::colorize( $countryId, 'INFO'), pakeColor::colorize( $countryDir, 'INFO'));
  }

  if ( $findCountry && $countryId != '' ) 
    $poeditOutFile = PATH_CORE . 'content' . PATH_SEP . 'translations' . 
                PATH_SEP . $langDir .  PATH_SEP . MAIN_POFILE . '.' . $langId . '_' . $countryId .'.po';
  else
    $poeditOutFile = PATH_CORE . 'content' . PATH_SEP . 'translations' . 
                PATH_SEP . $langDir . PATH_SEP . MAIN_POFILE . '.' . $langId . '.po';

  printf("poedit file: %s\n", pakeColor::colorize( $poeditOutFile, 'INFO'));


  $poeditOutPathInfo = pathinfo ( $poeditOutFile );
  G::verifyPath ( $poeditOutPathInfo[ 'dirname' ] , true);
  $lf = "\n";
  $fp = fopen ( $poeditOutFile, 'w' );
  fprintf ( $fp, "msgid \"\" \n" );
  fprintf ( $fp, "msgstr \"\" \n" );
  fprintf ( $fp, "\"Project-Id-Version: %s\\n\"\n", PO_SYSTEM_VERSION );
  fprintf ( $fp, "\"POT-Creation-Date: \\n\"\n" );
  fprintf ( $fp, "\"PO-Revision-Date: %s \\n\"\n", date('Y-m-d H:i+0100') ); 
  fprintf ( $fp, "\"Last-Translator: Fernando Ontiveros<fernando@colosa.com>\\n\"\n" );
  fprintf ( $fp, "\"Language-Team: Colosa Developers Team <developers@colosa.com>\\n\"\n" );
  fprintf ( $fp, "\"MIME-Version: 1.0 \\n\"\n" );
  fprintf ( $fp, "\"Content-Type: text/plain; charset=utf-8 \\n\"\n" );
  fprintf ( $fp, "\"Content-Transfer_Encoding: 8bit\\n\"\n" );
  fprintf ( $fp, "\"X-Poedit-Language: %s\\n\"\n", ucwords($langDir) );
  fprintf ( $fp, "\"X-Poedit-Country: %s\\n\"\n", $countryDir );
  fprintf ( $fp, "\"X-Poedit-SourceCharset: utf-8\\n\"\n" );


  printf("checking translation table\n");

  $c = new Criteria();
  $c->add ( TranslationPeer::TRN_LANG, "en" );
  
  $translation = TranslationPeer::doSelect ( $c );
  $trIndex = 0;
  $trError = 0;
  $langIdOut  = $langId;  //the output language, later we'll include the country too.

  $arrayLabels = array ();
  foreach ( $translation as $rowid => $row )  {
    $keyid = 'TRANSLATION/' . $row->getTrnCategory() . '/' . $row->getTrnId();
    if ( trim ( $row->getTrnValue() ) == '' ) {
      printf ( "warning the key %s is empty.\n", pakeColor::colorize( $keyid, 'ERROR') );
      $trError ++;
    }
    else {
      $trans = TranslationPeer::retrieveByPK ( $row->getTrnCategory(), $row->getTrnId(),$langIdOut  );
      if ( is_null ($trans ) ) {
        $msgStr = $row->getTrnValue();
      }
      else {
        $msgStr = $trans->getTrnValue();
      }

      $msgid = $row->getTrnValue();
      if (in_array( $msgid, $arrayLabels)) {
        $newMsgid = '[' . $row->getTrnCategory() . '/' . $row->getTrnId() . '] ' . $msgid; 
        printf ( "duplicated key %s is renamed to %s.\n", pakeColor::colorize( $msgid, 'ERROR'), pakeColor::colorize( $newMsgid, 'INFO')  );
        $trError ++;
        $msgid = $newMsgid; 
      }
      
      $arrayLabels [] = $msgid;
      sort($arrayLabels);
      
      $trIndex ++;
      fprintf ( $fp, "\n");
      fprintf ( $fp, "#: %s \n",  $keyid );
      //fprintf ( $fp, "#, php-format \n" );
      fprintf ( $fp, "# %s \n",  strip_quotes( $keyid ));
      fprintf ( $fp, "msgid \"%s\" \n",   strip_quotes( $msgid ));
      fprintf ( $fp, "msgstr \"%s\" \n",  strip_quotes( $msgStr ));
    }
  }
  

  printf("checking xmlform\n");
  printf("using directory %s \n", pakeColor::colorize( PATH_XMLFORM, 'INFO'));

  G::LoadThirdParty('pear/json','class.json');
  G::LoadThirdParty('smarty/libs','Smarty.class');
  G::LoadSystem('xmlDocument');
  G::LoadSystem('xmlform');
  G::LoadSystem('xmlformExtension');
  G::LoadSystem('form');

  $langIdOut  = $langId;  //the output language, later we'll include the country too.
  $exceptionFields = array ( 'javascript','hidden','phpvariable','private','toolbar',
     'xmlmenu', 'toolbutton', 'cellmark', 'grid' );
 
  $xmlfiles = pakeFinder::type('file')->name( '*.xml' )->in( PATH_XMLFORM );
  $xmlIndex = 0;
  $xmlError = 0;
  $fieldsIndexTotal = 0;
  $exceptIndexTotal = 0;
  foreach ($xmlfiles as $xmlfileComplete ) {
    $xmlIndex ++;
    $xmlfile = str_replace( PATH_XMLFORM,'', $xmlfileComplete );

    //english version of dynaform
    $form = new Form ( $xmlfile , '', 'en' );
    $englishLabel = array();
    foreach ($form->fields as $nodeName => $node) {
      if ( trim($node->label) != '' )
        $englishLabel[ $node->name ] = $node->label;
    }
    unset ( $form->fields );
    unset ( $form->tree );
    unset ( $form );
    
    //in this second pass, we are getting the target language labels
    $form = new Form ( $xmlfile , '', $langIdOut );
    $fieldsIndex = 0;
    $exceptIndex = 0;
    foreach ($form->fields as $nodeName => $node) {
      if ( is_object($node) && isset ( $englishLabel [ $node->name ] ) ) {
        $msgid = trim ($englishLabel [ $node->name ]);
        $node->label = trim ( str_replace ( chr(10) , '', $node->label )) ;
      }
      else 
        $msgid = '';
      if ( trim($msgid) != '' && ! in_array ( $node->type , $exceptionFields )) {
        //$msgid = $englishLabel [ $node->name ];
        $keyid = $xmlfile. '?' . $node->name ;
        if (in_array( $msgid, $arrayLabels)) {
          $newMsgid = '[' . $keyid . '] ' . $msgid; 
          if ( $verboseFlag ) 
            printf ( "duplicated key %s is renamed to %s.\n", pakeColor::colorize( $msgid, 'ERROR'), pakeColor::colorize( $newMsgid, 'INFO')  );
          $xmlError ++;
          $msgid = $newMsgid; 
        }
      
        $arrayLabels [] = $msgid;
        sort($arrayLabels);

        $comment1 = $xmlfile;
        $comment2 = $node->type . ' - ' . $node->name;
        fprintf ( $fp, "\n");
        fprintf ( $fp, "#: %s \n",  $keyid );
//        fprintf ( $fp, "#, php-format \n" );
        fprintf ( $fp, "# %s \n",  strip_quotes( $comment1 ));
        fprintf ( $fp, "# %s \n",  strip_quotes( $comment2 ));
        fprintf ( $fp, "msgid \"%s\" \n",   strip_quotes( $msgid ));
        fprintf ( $fp, "msgstr \"%s\" \n",  strip_quotes( $node->label ));
        //fprintf ( $fp, "msgstr \"%s\" \n",  strip_quotes( utf8_encode( trim($node->label) ) ));
        $fieldsIndex ++;
        $fieldsIndexTotal ++;
      }

      else {
        if ( is_object($node) && ! in_array ( $node->type , $exceptionFields )) {
          if ( isset($node->value) && strpos ( $node->value, 'G::LoadTranslation' ) !== false ) {
            $exceptIndex ++;
            //print ($node->value); 
          }
          else {
            printf ( "Error: xmlform %s has no english definition for %s [%s]\n", pakeColor::colorize( $xmlfile, 'ERROR'), pakeColor::colorize(  $node->name , 'INFO') , pakeColor::colorize(  $node->type , 'INFO') );
            $xmlError ++;
          }
        }
        else {
          $exceptIndex ++;
          if ( $verboseFlag ) 
            printf ("%s %s in %s\n", $node->type, pakeColor::colorize( $node->name, 'INFO'), pakeColor::colorize( $xmlfile , 'INFO') ); 
        }
      }
    }
    unset ( $form->fields );
    unset ( $form->tree );
    unset ( $form );
    printf ( "xmlform: %s has %s fields and %s exceptions \n" ,  pakeColor::colorize( $xmlfile, 'INFO'), pakeColor::colorize( $fieldsIndex, 'INFO'), pakeColor::colorize( $exceptIndex, 'INFO') );
    $exceptIndexTotal += $exceptIndex;
  }
  

  fclose ( $fp );
  printf("added %s entries from translation table\n", pakeColor::colorize( $trIndex, 'INFO'));
  printf("added %s entries from %s xmlforms  \n" ,  pakeColor::colorize( $fieldsIndexTotal, 'INFO') , pakeColor::colorize( $xmlIndex, 'INFO'));

  if ( $trError > 0 ) {
    printf("there are %s errors in tranlation table\n", pakeColor::colorize( $trError, 'ERROR'));
  }
  if ( $xmlError > 0 ) {
    printf("there are %s errors and %s exceptions in xmlforms\n", pakeColor::colorize( $xmlError, 'ERROR'), pakeColor::colorize( $exceptIndexTotal, 'ERROR') );
  }

  exit(0);
  
  //to do: leer los html templates
}

function create_file_from_tpl ( $tplName, $newFilename )
{
  global $pathHome;
  global $projectName;
   
  $httpdTpl = PATH_GULLIVER_HOME . 'bin' . PATH_SEP . 'tasks' . PATH_SEP . $tplName . '.tpl';
  if ( substr ( $newFilename, 0,1 ) == PATH_SEP ) 
    $httpFilename = $newFilename;
  else  
    $httpFilename = $pathHome . PATH_SEP . $newFilename;
  $template = new TemplatePower( $httpdTpl );
  $template->prepare();
  $template->assign ( 'pathHome', $pathHome);
  $template->assign ( 'projectName', $projectName);
  $template->assign ( 'rbacProjectName', strtoupper( $projectName)) ;
  $template->assign ( 'siglaProjectName', substr ( strtoupper( $projectName),0,3) ) ;
  
  $content = $template->getOutputContent();
  $iSize = file_put_contents ( $httpFilename, $content );
  printf("saved %s bytes in file %s \n", pakeColor::colorize( $iSize, 'INFO'), pakeColor::colorize( $tplName, 'INFO') );    
}

function copy_file_from_tpl ( $tplName, $newFilename )
{
  global $pathHome;
  global $projectName;
  $httpdTpl = PATH_GULLIVER_HOME . 'bin' . PATH_SEP . 'tasks' . PATH_SEP . $tplName . '.tpl';
  $httpFilename = $pathHome . PATH_SEP . $newFilename;
  $content = file_get_contents ( $httpdTpl );
  $iSize = file_put_contents ( $httpFilename, $content );
  printf("saved %s bytes in file %s \n", pakeColor::colorize( $iSize, 'INFO'), pakeColor::colorize( $tplName, 'INFO') );    
}

function copy_file( $newFilename )
{
  global $pathHome;
  $httpdTpl = PATH_HOME . $newFilename;
  $httpFilename = $pathHome . PATH_SEP . $newFilename;
  $content = file_get_contents ( $httpdTpl );
  $iSize = file_put_contents ( $httpFilename, $content );
  printf("saved %s bytes in file %s \n", pakeColor::colorize( $iSize, 'INFO'), pakeColor::colorize( $newFilename , 'INFO') );    
}

function run_new_project ( $task, $args)
{
  global $pathHome;
  global $projectName;
  //the class filename in the first argument
  $projectName = $args[0];

  if ( trim ($projectName ) == '' )  {
    printf("invalid Project Name\n", pakeColor::colorize( $class, 'ERROR'));
    exit (0);
  }  
  $createProject = strtolower ( prompt ( "Do you want to create the project '$projectName' ? [Y/n]" ));
  if ( $createProject == 'n' ) die;

  G::LoadSystem ('templatePower');
	define ( 'PATH_SHARED', PATH_SEP . 'shared' . PATH_SEP . $projectName . '_data' . PATH_SEP );
  $pathHome = PATH_TRUNK . $projectName;
  printf("creating project %s in %s\n", pakeColor::colorize($projectName, 'INFO'), pakeColor::colorize($pathHome, 'INFO'));

  define ( 'G_ENVIRONMENT', G_DEV_ENV );
  require_once ( "propel/Propel.php" );
  Propel::init(  PATH_CORE . "config/databases.php");  
  $configuration = Propel::getConfiguration();
  $connectionDSN = $configuration['datasources']['workflow']['connection'];
  printf("using DSN Connection %s \n", pakeColor::colorize( $connectionDSN, 'INFO'));

  $rbacProjectName = strtoupper ( $projectName );
          
  G::LoadSystem ('rbac');
  $RBAC = RBAC::getSingleton() ;
  $RBAC->sSystem = $rbacProjectName;
  $RBAC->initRBAC();
  $RBAC->createSystem ($rbacProjectName);
  $RBAC->createPermision ( substr( $rbacProjectName,0,3) . '_LOGIN' );
  $RBAC->createPermision ( substr( $rbacProjectName,0,3) . '_ADMIN' );
  $RBAC->createPermision ( substr( $rbacProjectName,0,3) . '_OPERATOR' );
  $permData = $RBAC->permissionsObj->LoadByCode (substr( $rbacProjectName,0,3) . '_LOGIN') ;
  $permissionId = $permData['PER_UID'];
  $systemData = $RBAC->systemObj->LoadByCode ($rbacProjectName) ;
  $roleData['ROL_UID'] = G::GenerateUniqueId();
  $roleData['ROL_PARENT'] = '';
  $roleData['ROL_SYSTEM'] = $systemData['SYS_UID'];
  $roleData['ROL_CODE'] = substr( $rbacProjectName,0,3) . '_ADMIN';
  $roleData['ROL_CREATE_DATE'] = date('Y-m-d H:i:s');
  $roleData['ROL_UPDATE_DATE'] = date('Y-m-d H:i:s');
  $roleData['ROL_STATUS'] = '1';
  $RBAC->createRole ( $roleData );

  $roleData['ROL_UID'] = G::GenerateUniqueId();
  $roleData['ROL_PARENT'] = '';
  $roleData['ROL_SYSTEM'] = $systemData['SYS_UID'];
  $roleData['ROL_CODE'] = substr( $rbacProjectName,0,3) . '_OPERATOR';
  $roleData['ROL_CREATE_DATE'] = date('Y-m-d H:i:s');
  $roleData['ROL_UPDATE_DATE'] = date('Y-m-d H:i:s');
  $roleData['ROL_STATUS'] = '1';
  $RBAC->createRole ( $roleData );
  $roleData = $RBAC->rolesObj->LoadByCode ( $roleData['ROL_CODE'] ) ;
  
  $RBAC->assignPermissionToRole($roleData['ROL_UID'], $permissionId); 
  $userRoleData['ROL_UID'] = $roleData['ROL_UID'];
  $userRoleData['USR_UID'] = '00000000000000000000000000000001';
  $RBAC->assignUserToRole( $userRoleData );
               
  //create folder and structure
  G::mk_dir ($pathHome );
  G::mk_dir ($pathHome . PATH_SEP . 'public_html' );
  G::mk_dir ($pathHome . PATH_SEP . 'public_html' . PATH_SEP . 'images');
  G::mk_dir ($pathHome . PATH_SEP . 'public_html' . PATH_SEP . 'skins');
  G::mk_dir ($pathHome . PATH_SEP . 'public_html' . PATH_SEP . 'skins'. PATH_SEP . 'green');
  G::mk_dir ($pathHome . PATH_SEP . 'public_html' . PATH_SEP . 'skins'. PATH_SEP . 'green'. PATH_SEP . 'images');
  G::mk_dir ($pathHome . PATH_SEP . 'engine' );
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'classes' );
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'model');  
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'model'. PATH_SEP . 'map');  
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'model'. PATH_SEP . 'om');  
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'config' );
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'content' );
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'content' . PATH_SEP . 'languages');
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'content' . PATH_SEP . 'translations');
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'data' );
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'data' . PATH_SEP . 'mysql');
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'js' );
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'js' . PATH_SEP . 'labels');
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'menus' );
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'methods' );
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'methods' . PATH_SEP . 'login');
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'skins' );
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'templates' );
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'templates' . PATH_SEP . 'roles' );
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'test' );
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'test' . PATH_SEP . 'bootstrap');
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'test' . PATH_SEP . 'fixtures');
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'test' . PATH_SEP . 'unit');
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'xmlform' );
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'xmlform' . PATH_SEP . 'login');
  G::mk_dir ($pathHome . PATH_SEP . 'engine' . PATH_SEP . 'xmlform' . PATH_SEP . 'gulliver');
  G::mk_dir (PATH_SHARED . 'sites' . PATH_SEP );
  G::mk_dir (PATH_SHARED . 'sites' . PATH_SEP . $projectName );

  //create project.conf for httpd conf
  create_file_from_tpl ( 'httpd.conf',       $projectName . '.conf' );
  create_file_from_tpl ( 'sysGeneric.php',  'public_html' . PATH_SEP . 'sysGeneric.php' );
  copy_file_from_tpl   ( 'bm.jpg',          'public_html' . PATH_SEP . 'skins' . PATH_SEP . 'green' . PATH_SEP . 'images' . PATH_SEP . 'bm.jpg' );
  copy_file_from_tpl   ( 'bsm.jpg',         'public_html' . PATH_SEP . 'skins' . PATH_SEP . 'green' . PATH_SEP . 'images' . PATH_SEP . 'bsm.jpg' );
  create_file_from_tpl ( 'index.html',      'public_html' . PATH_SEP . 'index.html' );
  create_file_from_tpl ( 'paths.php',       'engine' . PATH_SEP . 'config' . PATH_SEP . 'paths.php' );
  create_file_from_tpl ( 'defines.php',     'engine' . PATH_SEP . 'config' . PATH_SEP . 'defines.php' );
  create_file_from_tpl ( 'databases.php',   'engine' . PATH_SEP . 'config' . PATH_SEP . 'databases.php' );
  create_file_from_tpl ( 'sysLogin.php',    'engine' . PATH_SEP . 'methods' . PATH_SEP . 'login' . PATH_SEP . 'sysLogin.php' );
  create_file_from_tpl ( 'login.php',       'engine' . PATH_SEP . 'methods' . PATH_SEP . 'login' . PATH_SEP . 'login.php' );
  create_file_from_tpl ( 'authentication.php','engine'.PATH_SEP . 'methods' . PATH_SEP . 'login' . PATH_SEP . 'authentication.php' );
  create_file_from_tpl ( 'welcome.php',     'engine' . PATH_SEP . 'methods' . PATH_SEP . 'login' . PATH_SEP . 'welcome.php' );
  create_file_from_tpl ( 'dbInfo.php' ,     'engine' . PATH_SEP . 'methods' . PATH_SEP . 'login' . PATH_SEP . 'dbInfo.php' );
  create_file_from_tpl ( 'sysLogin.xml',    'engine' . PATH_SEP . 'xmlform' . PATH_SEP . 'login' . PATH_SEP . 'sysLogin.xml' );
  create_file_from_tpl ( 'login.xml',       'engine' . PATH_SEP . 'xmlform' . PATH_SEP . 'login' . PATH_SEP . 'login.xml' );
  create_file_from_tpl ( 'showMessage.xml', 'engine' . PATH_SEP . 'xmlform' . PATH_SEP . 'login' . PATH_SEP . 'showMessage.xml' );
  create_file_from_tpl ( 'welcome.xml',     'engine' . PATH_SEP . 'xmlform' . PATH_SEP . 'login' . PATH_SEP . 'welcome.xml' );
  copy_file_from_tpl   ( 'xmlform.html',    'engine' . PATH_SEP . 'templates' . PATH_SEP . 'xmlform.html' );
  copy_file_from_tpl   ( 'publish.php',     'engine' . PATH_SEP . 'templates' . PATH_SEP . 'publish.php' );
  copy_file_from_tpl   ( 'publish-treeview.php','engine'.PATH_SEP.'templates' . PATH_SEP . 'publish-treeview.php' );
  create_file_from_tpl ( 'dbInfo.xml',      'engine' . PATH_SEP . 'xmlform'. PATH_SEP . 'login' . PATH_SEP . 'dbInfo.xml' );
  create_file_from_tpl ( 'mainmenu.php',    'engine' . PATH_SEP . 'menus'. PATH_SEP . $projectName . '.php' );
  create_file_from_tpl ( 'users.menu.php',    'engine' . PATH_SEP . 'menus'. PATH_SEP . 'users.php' );
  create_file_from_tpl ( 'db.php',          PATH_SEP . PATH_SHARED . 'sites' . PATH_SEP . $projectName . PATH_SEP . 'db.php' );
  copy_file ( 'public_html' . PATH_SEP . 'skins' . PATH_SEP . 'green' . PATH_SEP . 'style.css' );
  copy_file ( 'public_html' . PATH_SEP . 'skins' . PATH_SEP . 'green' . PATH_SEP . 'images' . PATH_SEP . 'bsms.jpg' );
  copy_file ( 'public_html' . PATH_SEP . 'skins' . PATH_SEP . 'green' . PATH_SEP . 'images' . PATH_SEP . 'ftl.png' );
  copy_file ( 'public_html' . PATH_SEP . 'skins' . PATH_SEP . 'green' . PATH_SEP . 'images' . PATH_SEP . 'ftr.png' );
  copy_file ( 'public_html' . PATH_SEP . 'skins' . PATH_SEP . 'green' . PATH_SEP . 'images' . PATH_SEP . 'fbl.png' );
  copy_file ( 'public_html' . PATH_SEP . 'skins' . PATH_SEP . 'green' . PATH_SEP . 'images' . PATH_SEP . 'fbr.png' );
  copy_file ( 'public_html' . PATH_SEP . 'skins' . PATH_SEP . 'green' . PATH_SEP . 'images' . PATH_SEP . 'fbc.png' );
  copy_file ( 'public_html' . PATH_SEP . 'images' . PATH_SEP . 'favicon.ico' );
  copy_file ( 'public_html' . PATH_SEP . 'images' . PATH_SEP . 'bulletButton.gif' );
  copy_file ( 'public_html' . PATH_SEP . 'images' . PATH_SEP . 'bulletSubMenu.jpg' );
  copy_file ( 'public_html' . PATH_SEP . 'images' . PATH_SEP . 'users.png' );
  copy_file ( 'public_html' . PATH_SEP . 'images' . PATH_SEP . 'trigger.gif' );

  copy_file ( 'engine' . PATH_SEP . 'skins' . PATH_SEP . 'green.html' );
  copy_file ( 'engine' . PATH_SEP . 'skins' . PATH_SEP . 'green.php' );
  copy_file ( 'engine' . PATH_SEP . 'skins' . PATH_SEP . 'raw.html' );
  copy_file ( 'engine' . PATH_SEP . 'skins' . PATH_SEP . 'raw.php' );
  copy_file ( 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'class.ArrayPeer.php' );
  copy_file ( 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'class.BasePeer.php' );
  copy_file ( 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'class.configuration.php' );
  copy_file ( 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'class.plugin.php' );
  copy_file ( 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'class.pluginRegistry.php' );
  copy_file ( 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'class.popupMenu.php' );
  copy_file ( 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'class.propelTable.php' );
  copy_file ( 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'Application.php' );
  copy_file ( 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'ApplicationPeer.php' );
  copy_file ( 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'Content.php' );
  copy_file ( 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'ContentPeer.php' );
  copy_file ( 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'Configuration.php' );
  copy_file ( 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'ConfigurationPeer.php' );
  copy_file ( 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'om' . PATH_SEP .  'BaseApplication.php' );
  copy_file ( 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'om' . PATH_SEP .  'BaseApplicationPeer.php' );
  copy_file ( 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'om' . PATH_SEP .  'BaseContent.php' );
  copy_file ( 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'om' . PATH_SEP .  'BaseContentPeer.php' );
  copy_file ( 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'om' . PATH_SEP .  'BaseConfiguration.php' );
  copy_file ( 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'om' . PATH_SEP .  'BaseConfigurationPeer.php' );
  copy_file ( 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'map' . PATH_SEP .  'ApplicationMapBuilder.php' );
  copy_file ( 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'map' . PATH_SEP .  'ContentMapBuilder.php' );
  copy_file ( 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'map' . PATH_SEP .  'ConfigurationMapBuilder.php' );
  copy_file ( 'engine' . PATH_SEP . 'config' . PATH_SEP . 'environments.php' );
  copy_file ( 'engine' . PATH_SEP . 'xmlform' . PATH_SEP . 'login' . PATH_SEP . 'login.xml' );
  copy_file ( 'engine' . PATH_SEP . 'xmlform' . PATH_SEP . 'gulliver' . PATH_SEP . 'pagedTable_PopupMenu.xml' );
  copy_file ( 'engine' . PATH_SEP . 'templates' . PATH_SEP . 'popupMenu.html' );
  copy_file ( 'engine' . PATH_SEP . 'templates' . PATH_SEP . 'paged-table.html' );
  copy_file ( 'engine' . PATH_SEP . 'templates' . PATH_SEP . 'xmlmenu.html' );
  copy_file ( 'engine' . PATH_SEP . 'templates' . PATH_SEP . 'filterform.html' );
  copy_file ( 'engine' . PATH_SEP . 'templates' . PATH_SEP . 'tree.html' );
  copy_file ( 'engine' . PATH_SEP . 'templates' . PATH_SEP . 'roles' . PATH_SEP . 'roles_permissionsTree.php' );

    $filePng = $pathHome . PATH_SEP . 'public_html' . PATH_SEP . 'images' . PATH_SEP . 'processmaker.logo.jpg';
  createPngLogo ( $filePng, $projectName );

  printf("creating symlinks %s \n", pakeColor::colorize( $pathHome . PATH_SEP . 'engine' . PATH_SEP . 'gulliver', 'INFO'));
  symlink (PATH_GULLIVER_HOME . 'bin' . PATH_SEP . 'gulliver', $pathHome . PATH_SEP . 'engine' . PATH_SEP . 'gulliver');

  //create schema.xml with empty databases
  
  exit(0);
}
