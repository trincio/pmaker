<?php
/**
 * packCreator.php
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
G::LoadSystem('objectTemplate');

/* Start Block: Este bloque crea el listado de INPUT DOCUMENTS*/
  $pack = 'inputdocs';
  $dbTable = 'REQ_DOCUMENT';
  $dbPrefix = 'REQ_DOC';
  $key = 'REQ_DOC_UID';
  $PACK = strtoupper($pack);
  $oPack=array2Object(array(
    'name'=>$pack,
    'key'=>$key,
    'table'=>$dbTable,
    'classFile'=>'reqDocument',
    'class'=>'ReqDocument',
    'sql'=>"SELECT *, T.CON_VALUE AS {$dbPrefix}_TITLE, D.CON_VALUE AS {$dbPrefix}_DESCRIPTION FROM $dbTable LEFT JOIN CONTENT as T ON (T.CON_ID=$key AND T.CON_CATEGORY='{$dbPrefix}_TITLE') LEFT JOIN CONTENT AS D ON (D.CON_ID=$key AND D.CON_CATEGORY='{$dbPrefix}_DESCRIPTION')",
    'fields'=>array(
      newField($key,'hidden'),
      newField($dbPrefix.'_TITLE','text', newTableFieldAtt('200','left','T.CON_VALUE'),true,"Input document","Documento requerido",""),
      newField($dbPrefix.'_FORM_NEEDED','dropdown', newTableFieldAtt('80','left',$dbPrefix.'_FORM_NEEDED','='),true,"Type".newDD(array('1'=>'Real','2'=>'Virtual','3'=>'VReal')),"Tipo".newDD(array('1'=>'Real','2'=>'Virtual','3'=>'VReal')),""),
      newField($dbPrefix.'_ORIGINAL','dropdown', newTableFieldAtt('80','left',$dbPrefix.'_ORIGINAL','='),true,"Original".newDD(array('1'=>'Copy','2'=>'Original','3'=>'Legal copy','4'=>'Final')),"Original".newDD(array('1'=>'Copia','2'=>'Original','3'=>'Copia legalizada','4'=>'Final')),""),
      newField($dbPrefix.'_PUBLISHED','dropdown', newTableFieldAtt('80','left',$dbPrefix.'_PUBLISHED','='),true,"Access type".newDD(array('1'=>'Private','2'=>'Public')),"Tipo de acceso".newDD(array('1'=>'Privado','2'=>'Público')),""),
      newField($dbPrefix.'_DESCRIPTION','textarea', newTableTextAreaAtt('200','left','D.CON_VALUE'),true,"Description","Descripción","")
    )
  ));
/* End Block*/

/* Start Block: Este bloque crea el listado de DYNAFORMS*/
  $pack = 'fields';
  $dbTable = 'dynaForm';
  $dbPrefix = '';
  $key = 'XMLNODE_NAME';
  $PACK = strtoupper($pack);
  $oPack=array2Object(array(
    'name'=>$pack,
    'key'=>$key,
    'table'=>$dbTable,
    'classFile'=>'dynaForm',
    'class'=>'DynaForm',
    'sql'=>"SELECT * FROM $dbTable WHERE NOT( XMLNODE_NAME = '' OR TYPE = 'javascript' )",
    'fields'=>array(
      newField($key,'text', newTableFieldAtt('200','left','XMLNODE_NAME'),true,"Field name","Nombre de campo",""),
      newField('TYPE','dropdown', newTableFieldAtt('120','left','TYPE','='),true,"Type".newDD(array('1'=>'Real','2'=>'Virtual','3'=>'VReal')),"Tipo".newDD(array('1'=>'Real','2'=>'Virtual','3'=>'VReal')),""),
      newField('GROUP','text', newTableFieldAtt('80','left','GROUP'),true,"Group","Grupo",""),
      newField('DEFAULTVALUE','text', newTableFieldAtt('200','left','DEFAULTVALUE'),true,"Default value","Valor por defecto","")
    )
  ));
/* End Block*/

/* Start Block: Este bloque crea el listado de OUTPUT DOCUMENTS*/
  $pack = 'outputdocs';
  $dbTable = 'PROCESS_OUTPUT_DOCUMENT';
  $dbPrefix = 'PRO_OUT_DOC';
  $key = 'PRO_OUT_DOC_UID';
  $PACK = strtoupper($pack);
  $oPack=array2Object(array(
    'name'=>$pack,
    'key'=>$key,
    'table'=>$dbTable,
    'classFile'=>'processOutputDocument',
    'class'=>'ProcessOutputDocument',
    'sql'=>"SELECT *, T.CON_VALUE AS {$dbPrefix}_TITLE, D.CON_VALUE AS {$dbPrefix}_DESCRIPTION FROM $dbTable LEFT JOIN CONTENT as T ON (T.CON_ID=$key AND T.CON_CATEGORY='{$dbPrefix}_TITLE') LEFT JOIN CONTENT AS D ON (D.CON_ID=$key AND D.CON_CATEGORY='{$dbPrefix}_DESCRIPTION') WHERE PRO_UID=@@PRO_UID",
    'fields'=>array(
      newField($key,'hidden'),
      newField($dbPrefix.'_TITLE','text', newTableFieldAtt('200','left','T.CON_VALUE'),true,"Output document","Documento requerido",""),
      newField($dbPrefix.'_FILENAME','text', newTableFieldAtt('200','left',$dbPrefix.'_FILENAME'),true,"File Name","Nombre de Archivo",""),
      newField($dbPrefix.'_DESCRIPTION','textarea', newTableTextAreaAtt('200','left','D.CON_VALUE'),true,"Description","Descripción","")
    )
  ));
/* End Block*/

/* Start Block: Este bloque crea el listado de MESSAGES*/
  $pack = 'messages';
  $dbTable = 'MESSAGE';
  $dbPrefix = 'MESS';
  $key = 'MESS_UID';
  $PACK = strtoupper($pack);
  $oPack=array2Object(array(
    'name'=>$pack,
    'key'=>$key,
    'table'=>$dbTable,
    'prefix'=>$dbPrefix,
    'classFile'=>'message',
    'class'=>'Message',
    'sql'=>"SELECT *, T.CON_VALUE AS {$dbPrefix}_TITLE, D.CON_VALUE AS {$dbPrefix}_DESCRIPTION FROM $dbTable LEFT JOIN CONTENT as T ON (T.CON_ID=$key AND T.CON_CATEGORY='{$dbPrefix}_TITLE') LEFT JOIN CONTENT AS D ON (D.CON_ID=$key AND D.CON_CATEGORY='{$dbPrefix}_DESCRIPTION') WHERE PRO_UID=@@PRO_UID",
    'fields'=>array(
      newField($key,'hidden'),
      newField($dbPrefix.'_TITLE','text', newTableFieldAtt('200','left','T.CON_VALUE'),true,"Message Title","Título de Mensaje",""),
      newField($dbPrefix.'_TYPE','dropdown', newTableFieldAtt('120','left','TYPE','='),true,"Type".newDD(array('HTML'=>'HTML','TEXT'=>'Plain text','SMS'=>'SMS')),"Tipo".newDD(array('HTML'=>'HTML','TEXT'=>'Texto Plano','SMS'=>'SMS')),""),
      newField($dbPrefix.'_DESCRIPTION','textarea', newTableTextAreaAtt('200','left','D.CON_VALUE'),true,"Content","Contenido","")
    )
  ));
/* End Block*/

/* Start Block: Este bloque crea el listado de CASOS*/
  $pack = 'cases';
  $dbTable = 'APPLICATIONS';
  $dbPrefix = 'APP';
  $key = 'APP_UID';
  $PACK = strtoupper($pack);
  $oPack=array2Object(array(
    'name'=>$pack,
    'key'=>$key,
    'table'=>$dbTable,
    'prefix'=>$dbPrefix,
    'classFile'=>'derivation',
    'class'=>'Derivation',
    'sql'=>
'SELECT 
  APP_TITLE.CON_VALUE AS APP_TITLE,
  TAS_TITLE.CON_VALUE AS APP_TAS_TITLE,
  PRO_TITLE.CON_VALUE AS APP_PRO_TITLE,
  APP_LAST_USER.USR_USERNAME AS APP_DEL_PREVIOUS_USER,
  APP_DELEGATION.DEL_TASK_DUE_DATE AS APP_DEL_TASK_DUE_DATE
  APPLICATION.APP_UPDATE_DATE AS APP_UPDATE_DATE
  APP_DELEGATION.DEL_PRIORITY AS APP_DEL_PRIORITY
FROM
  APP_DELEGATION
  LEFT JOIN APPLICATION ON (APPLICATION.APP_UID = APP_DELEGATION.APP_UID AND APP_DELEGATION.DEL_FINISH_DATE IS NOT NULL)
  LEFT JOIN USERS ON (APP_DELEGATION.USR_UID = USERS.USR_UID)
  LEFT JOIN CONTENT APP_TITLE ON (APPLICATION.APP_UID = APP_TITLE.CON_ID AND APP_TITLE.CON_CATEGORY = "APP_TITLE")
  LEFT JOIN `PROCESS` ON (APPLICATION.PRO_UID = `PROCESS`.PRO_UID)
  LEFT JOIN CONTENT PRO_TITLE ON (`PROCESS`.PRO_UID = PRO_TITLE.CON_ID AND PRO_TITLE.CON_CATEGORY = "PRO_TITLE")
  LEFT JOIN APP_DELEGATION APP_PREV_DEL ON (APPLICATION.APP_UID = APP_PREV_DEL.APP_UID AND APP_PREV_DEL.DEL_INDEX = APP_DELEGATION.DEL_PREVIOUS)
  LEFT JOIN USERS APP_LAST_USER ON (APP_PREV_DEL.USR_UID = APP_LAST_USER.USR_UID)
  LEFT JOIN TASK ON (APP_DELEGATION.TAS_UID = TASK.TAS_UID)
  LEFT JOIN CONTENT TAS_TITLE ON (TASK.TAS_UID = TAS_TITLE.CON_ID AND TAS_TITLE.CON_CATEGORY = "TAS_TITLE")
  LEFT JOIN CONTENT APP_PRIORITY ON (APP_DELEGATION.DEL_PRIORITY = APP_PRIORITY.CON_ID AND APP_PRIORITY.CON_CATEGORY = "APP_PRIORITY")',
    'fields'=>array(
      newField($dbPrefix.'_TITLE','text', newTableFieldAtt('200','left','APP_TITLE.CON_VALUE'),true,"Case","Caso",""),
      newField($dbPrefix.'_TAS_TITLE','text', newTableFieldAtt('200','left','TAS_TITLE.CON_VALUE'),true,"Task","Tarea"),
      newField($dbPrefix.'_PRO_TITLE','text', newTableFieldAtt('200','left','PRO_TITLE.CON_VALUE'),true,"Process","Proceso"),
      newField($dbPrefix.'_DEL_PREVIOUS_USER','text', newTableFieldAtt('120','left','APP_LAST_USER.USR_USERNAME'),true,"Sent by","Derivado desde"),
      newField($dbPrefix.'_DEL_TASK_DUE_DATE','date', newTableTextAreaAtt('90','center','APP_DELEGATION.DEL_TASK_DUE_DATE'),true,"Due Date","Fecha vencimiento"),
      newField($dbPrefix.'_UPDATE_DATE','date', newTableTextAreaAtt('90','center','APPLICATION.APP_UPDATE_DATE'),true,"Last Modification","Última modificación"),
      newField($dbPrefix.'_DEL_PRIORITY','text', newTableTextAreaAtt('90','center','APP_DELEGATION.DEL_PRIORITY'),true,"Priority","Prioridad")
    )
  ));
/* End Block*/

  print('<form action="packCreator_Save" method="POST">');
  $methods = glob(PATH_TPL . 'packCreator/methods/packCreator*');
  foreach($methods as $m) {
    $m = substr($m, strlen(PATH_TPL));
    $ot = new ObjectTemplate( $m );
    $save = substr( $m , strlen('packCreator/methods/'));
    $save = str_replace( 'packCreator', $pack , $save );
    print('<input type="TEXT" name="file[]" value="'.htmlentities(PATH_METHODS . $save).'" size="80"/><br/>');
    print('<textarea name="code[]" cols="80" rows="25">');
    print($ot->printObject(array('pack'=>$oPack)));
    print('</textarea><br/>');
  }

  $xmlforms = glob(PATH_TPL . 'packCreator/xmlforms/packCreator*');
  foreach($xmlforms as $m) {
    $m = substr($m, strlen(PATH_TPL));
    $ot = new ObjectTemplate( $m );
    $save = substr( $m , strlen('packCreator/xmlforms/'));
    $save = str_replace( 'packCreator', $pack , $save );
    print('<input type="TEXT" name="file[]" value="'.htmlentities(PATH_XMLFORM . $save).'" size="80"/><br/>');
    print('<textarea name="code[]" cols="80" rows="25">');
    print($ot->printObject(array('pack'=>$oPack)));
    print('</textarea><br/>');
  }

  print('<INPUT TYPE="submit" VALUE="SAVE"/>');
  print('</form>');

  function newField( $name, $type, $attributes=array(), $showInTable=true, $labelsEN="", $labelsES="", $sql="" ) {
    return array2Object(array('name'=>$name,'type'=>$type,'attributes'=>$attributes,'showInTable'=>$showInTable,'labelEN'=>$labelsEN,'labelES'=>$labelsES,'sql'=>$sql));
  }
  function newTableFieldAtt( $width, $align, $compareWith, $compareType='contains' ){
    return array('colWidth'=>$width,'titleAlign'=> $align, 'align'=> $align, 'dataCompareField'=>$compareWith, 'dataCompareType'=>$compareType);
  }
  function newTableTextAreaAtt( $width, $align, $compareWith, $compareType='contains', $rows=3, $cols=32 ){
    return array('rows' => $rows, 'cols'=> $cols, 'colWidth'=>$width,'titleAlign'=> $align, 'align'=> $align, 'dataCompareField'=>$compareWith, 'dataCompareType'=>$compareType);
  }
  function newDD( $options ){
    $res = '';
    foreach($options as $key => $value) {
      $res .= "  <option name ='$key'>$value</option>\n";
    }
    return $res;
  }
  function array2Object( $arr ){
    $obj = '';
    foreach($arr as $key => $value ){
      $obj->{$key} = $value;
    }
    return $obj;
  }
?>