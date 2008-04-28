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
    $pluginFilename = PATH_PLUGINS . $fName;
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
  $connectionDSN = $configuration['datasources']['propel']['connection'];
  printf("using DSN Connection %s \n", pakeColor::colorize( $connectionDSN, 'INFO'));

  $pluginDirectory = PATH_PLUGINS . $class;
  
  G::verifyPath ( $pluginDirectory, true );
  
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

function run_new_project ( $task, $args)
{
//the class filename in the first argument
  $projectName = $args[0];

  if ( trim ($projectName ) == '' )  {
    printf("invalid Project Name\n", pakeColor::colorize( $class, 'ERROR'));
    exit (0);
  }  

  //create folder and structure
  //create project.conf for httpd conf
  //create schema.xml with empty databases
  //create welcome page
  
  printf("Gulliver version %s\n", pakeColor::colorize(trim(file_get_contents( PATH_GULLIVER . 'VERSION')), 'INFO'));
  exit(0);
}

