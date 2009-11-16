<?php
/**
 * fields_Edit.php
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
if (($RBAC_Response=$RBAC->userCanAccess("PM_FACTORY"))!=1) return $RBAC_Response;

  //G::genericForceLogin( 'WF_MYINFO' , 'login/noViewPage', $urlLogin = 'login/login' );

  G::LoadClass('dynaFormField');

  if (!(isset($_GET['A']) && $_GET['A']!==''))  return;

  $file = G::decrypt( $_GET['A'] , URL_KEY );

  $dbc = new DBConnection( PATH_DYNAFORM . $file . '.xml' ,'','','','myxml' );
  $ses = new DBSession($dbc);

  //TODO: Improve how to obtain the PRO_UID.
  $aFile=explode('/',str_replace('\\','/',$file));
  $proUid=$aFile[0];
  $dynUid=str_replace("_tmp0","",$aFile[1]);
    
  require_once 'classes/model/Dynaform.php';
  $k=new Criteria('workflow');
  $k->addSelectColumn(DynaformPeer::DYN_TYPE);
  $k->add(DynaformPeer::DYN_UID,$dynUid);
  $ods=DynaformPeer::doSelectRS($k);
  $ods->next();
  $row=$ods->getRow();
  $dynType=$row[0];

  //session_start(); 
  $_SESSION['PME_DYN_TYPE']=$dynType; 
  //$Fields['PME_DYN_TYPE']=$dynType;
  
  
  
  $fields = new DynaFormField( $dbc );
  $fields->Fields['XMLNODE_NAME']=(isset($_GET['XMLNODE_NAME'])) ? urldecode($_GET['XMLNODE_NAME']):'';
  $fields->Load( $fields->Fields['XMLNODE_NAME'] );

  /* Start Comment: Modify the options grid to set dynamically the language
   * label columns.
   */
//  $dbc2 = new DBConnection( PATH_XMLFORM . 'dynaforms/fields/_options.xml' ,'','','','myxml' );
//  $ses2 = new DBSession($dbc2);
//  $ses2->execute("DELETE FROM dynaForm WHERE XMLNODE_NAME like 'LABEL_%' ");
//  $ses2->execute("DELETE FROM dynaForm WHERE XMLNODE_NAME = '' ");
//  $langs=array(SYS_LANG/*,'es','fa'*/);
//  foreach( $langs as $lang ) {
//    $LANG = strtoupper($lang);
//    $Label = 'Label';
//    $ses2->execute("INSERT INTO dynaForm (XMLNODE_NAME,XMLNODE_TYPE,XMLNODE_VALUE) VALUES ('', 'cdata', '\n') ");
//    $ses2->execute("INSERT INTO dynaForm (XMLNODE_NAME,TYPE) VALUES ('LABEL_{$LANG}', 'text') ");
//    $ses2->execute("INSERT INTO dynaForm.LABEL_{$LANG} (XMLNODE_NAME,XMLNODE_VALUE) VALUES ('".SYS_LANG."', '{$Label} ({$lang})') ");
//    $ses2->execute("INSERT INTO dynaForm (XMLNODE_NAME,XMLNODE_TYPE,XMLNODE_VALUE) VALUES ('', 'cdata', '\n') ");
//  }
  /* End Comment: */


  define('DB_XMLDB_HOST', PATH_DYNAFORM  . $file . '.xml' );
  define('DB_XMLDB_USER','');
  define('DB_XMLDB_PASS','');
  define('DB_XMLDB_NAME','');
  define('DB_XMLDB_TYPE','myxml');

  $form = new Form( $file , PATH_DYNAFORM, SYS_LANG, true );

  if (is_array($fields->Fields)) {
	  foreach( $fields->Fields as $key => $value ) {
	    $Fields['PME_'.$key] = $value;
	  }
	}

  $Fields['PME_A'] = $_GET['A'];
  $Fields['PME_PRO_UID'] = $proUid;
  $Fields['PME_XMLNODE_NAME_OLD'] = (isset($Fields['PME_XMLNODE_NAME']) ? $Fields['PME_XMLNODE_NAME'] : '');
  $G_PUBLISH = new Publisher();

  if ( !( isset($fields->Fields['XMLNODE_NAME']) &&
      ($fields->Fields['XMLNODE_NAME']!=='') ) ) {
    $type = strtolower( $_GET['TYPE'] );
    $Fields['PME_TYPE'] = $type;
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'dynaforms/fields/' . $type, '', $Fields , SYS_URI.'dynaforms/fields_Save', SYS_URI.'dynaforms/fields_Ajax');
  } else {
    $Fields['PME_LABEL'] = $form->fields[$fields->Fields['XMLNODE_NAME']]->label;
    if (isset($form->fields[$fields->Fields['XMLNODE_NAME']]->code)) $Fields['PME_CODE'] = $form->fields[$fields->Fields['XMLNODE_NAME']]->code;
    $options=isset($form->fields[$fields->Fields['XMLNODE_NAME']]->option)?
      $form->fields[$fields->Fields['XMLNODE_NAME']]->option:array();
    if (!is_array($options)) $options =array();
    $Fields['PME_OPTIONS'] = array(
      'NAME' => array_keys($options),
      'LABEL' => array_values($options)
       );
    $type = strtolower( $fields->Fields['TYPE'] );
    if ($type==='checkbox') {
      if ($Fields['PME_DEFAULTVALUE']===$Fields['PME_VALUE']) {
        $Fields['PME_DEFAULTVALUE']='On';
      } else {
        $Fields['PME_DEFAULTVALUE']='Off';
      }
    }

    if (file_exists( PATH_XMLFORM . 'dynaforms/fields/' . $type . '.xml')) {
      $G_PUBLISH->AddContent('xmlform', 'xmlform', 'dynaforms/fields/' . $type, '', $Fields , SYS_URI.'dynaforms/fields_Save', SYS_URI.'dynaforms/fields_Ajax');
    } else {
      print(G::LoadTranslation('ID_UNKNOWN_FIELD_TYPE'));
    }
  }

  G::RenderPage( "publish" , "raw" );
?>