<?php
/**
 * fields_Save.php
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

  $type=strtolower($_POST['form']['PME_TYPE']);
  if (!(isset($_POST['form']['PME_A']) && $_POST['form']['PME_A']!==''))  return;
  
  $file = G::decrypt( $_POST['form']['PME_A'] , URL_KEY );
  define('DB_XMLDB_HOST', PATH_DYNAFORM  . $file . '.xml' );
  define('DB_XMLDB_USER','');
  define('DB_XMLDB_PASS','');
  define('DB_XMLDB_NAME','');
  define('DB_XMLDB_TYPE','myxml');

  if (file_exists( PATH_XMLFORM . 'dynaforms/fields/' . $type . '.xml')) {
    $form=new Form('dynaforms/fields/' . $type , PATH_XMLFORM);
    $form->validatePost();
    if ($type==='checkbox') {
      if ($_POST['form']['PME_DEFAULTVALUE']===$form->fields['PME_DEFAULTVALUE']->value) {
        $_POST['form']['PME_DEFAULTVALUE']=$_POST['form']['PME_VALUE'];
      } else {
        $_POST['form']['PME_DEFAULTVALUE']=$_POST['form']['PME_FALSEVALUE'];
      }
    }
  }
  foreach($_POST['form'] as $key => $value){
    if (substr($key,0,4)==='PME_')
      $res[substr($key,4)]=$value;
    else
      $res[$key]=$value;
  }
  $_POST['form']=$res;

  $dbc = new DBConnection( PATH_DYNAFORM . $file . '.xml' ,'','','','myxml' );
  $ses = new DBSession($dbc);

  $fields = new DynaFormField( $dbc );

  if ($_POST['form']['XMLNODE_NAME']==='') return;
  
  
  $attributes = $_POST['form'];
  if (isset($attributes['CODE'])) $attributes['XMLNODE_VALUE'] = ($attributes['CODE']);
  
  $labels = array();
  if (isset($attributes['LABEL'])) $labels = array ( SYS_LANG => $attributes['LABEL'] );

  unset($attributes['A']);
  unset($attributes['ACCEPT']);
  unset($attributes['LABEL']);
  //if (!isset($attributes['ENABLEHTML'])) $attributes['ENABLEHTML'] = '0';

  $options = NULL;
  foreach($attributes as $key => $value ) {
    if ($key==='OPTIONS') {
      if (is_array($value)){
        if (is_array(reset($value))) {
          $langs = array();
          $options = array();
          $first = reset($value);
          foreach( $first as $optKey => $optValue ) {
            if (substr($optKey,0,6)==='LABEL_') {
              $langs[]=strtolower(substr($optKey,6));
              $options[strtolower(substr($optKey,6))]=array();
            }
          }
          foreach( $value as $row ) {
            foreach( $langs as $lang ) {
              $LANG = strtoupper($lang);
              if (isset($row['LABEL_'.$LANG])) 
                $options[$lang][$row['NAME']]=$row['LABEL_'.$LANG];
            }
          }
        }
      }
    } else {
      if (is_array($value)){
         //Is a list:
         if (is_string(reset($value))) {
          $attributes[$key] = implode(',',$value);
        } else {
          //Is a grid.
        }
      }
    }
  }
  $fields->Save( $attributes , $labels , $options );
  
?>