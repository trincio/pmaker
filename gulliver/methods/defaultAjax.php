<?php
/**
 * defaultAjax.php
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
/*NEXT LINE: Runs any configuration defined to be executed before dependent fields recalc*/
if (isset($_SESSION['CURRENT_PAGE_INITILIZATION'])) eval($_SESSION['CURRENT_PAGE_INITILIZATION']);
G::LoadThirdParty('pear/json','class.json');
$json=new Services_JSON();
if (!defined('XMLFORM_AJAX_PATH')) define('XMLFORM_AJAX_PATH',PATH_XMLFORM);
$_DBArray = array();
if (isset($_SESSION['_DBArray'])) {
  $_DBArray = $_SESSION['_DBArray'];
}
$G_FORM=new form(G::getUIDName(urlDecode($_POST['form'])),XMLFORM_AJAX_PATH);
$G_FORM->id=urlDecode($_POST['form']);
$G_FORM->values=isset($_SESSION[$G_FORM->id]) ? $_SESSION[$G_FORM->id] : array();

$newValues=($json->decode(urlDecode(stripslashes($_POST['fields']))));
//Resolve dependencies
//Returns an array ($dependentFields) with the names of the fields
//that depends of fields passed through AJAX ($_GET/$_POST)
$dependentFields=array(); $aux=array();
for($r=0;$r<sizeof($newValues);$r++) {
	$newValues[$r]=(array)$newValues[$r];
	$G_FORM->setValues($newValues[$r]);
	//Search dependent fields
	foreach($newValues[$r] as $k => $v) {
		$myDependentFields = subDependencies( $k , $G_FORM , $aux );
		$dependentFields=array_merge($dependentFields, $myDependentFields);
		$_SESSION[$G_FORM->id][$k] = $v;
	}
}
$dependentFields=array_unique($dependentFields);

//Parse and update the new content
$template = PATH_CORE . 'templates/xmlform.html';
$newContent=$G_FORM->getFields($template);

//Returns the dependentFields's content
$sendContent=array();
$r=0;
foreach($dependentFields as $d) {
	$sendContent[$r]->name=$d;
	$sendContent[$r]->content=NULL;
	foreach($G_FORM->fields[$d] as $attribute => $value) {
	  switch($attribute) {
	    case 'type':
	    $sendContent[$r]->content->{$attribute}=$value; break;
	    case 'options':
	    $sendContent[$r]->content->{$attribute}=toJSArray($value); break;
	  }
	}
	$sendContent[$r]->value=isset($G_FORM->values[$d]) ? $G_FORM->values[$d] : '';
	$r++;
}
echo($json->encode($sendContent));

function toJSArray($array)
{
  $result=array();
  foreach($array as $k => $v){
    $o=NULL;
    $o->key=$k;
    $o->value=$v;
    $result[]=$o;
  }
  return $result;
}


function subDependencies( $k , &$G_FORM , &$aux ) {
  if (array_search( $k, $aux )!==FALSE) return array();
  if (!array_key_exists( $k , $G_FORM->fields )) return array();
  if (!isset($G_FORM->fields[$k]->dependentFields)) return array();
  $aux[] = $k;
  $myDependentFields = explode( ',', $G_FORM->fields[$k]->dependentFields);
  for( $r=0 ; $r < sizeof($myDependentFields) ; $r++ ) {
    if ($myDependentFields[$r]=="") unset($myDependentFields[$r]);
  }
  $mD = $myDependentFields;
  foreach( $mD as $ki) {
    $myDependentFields = array_merge( $myDependentFields , subDependencies( $ki , $G_FORM , $aux ) );
  }
  return $myDependentFields;
}
?>