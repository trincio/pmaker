<?php
//G::LoadSystem('json');
require_once(PATH_THIRDPARTY . 'pear/json/class.json.php');
$json=new Services_JSON();
$G_FORM=new form(G::getUIDName(urlDecode($_POST['form'])));
$G_FORM->id=urlDecode($_POST['form']);
$G_FORM->values=$_SESSION[$G_FORM->id];

$newValues=($json->decode(urlDecode(stripslashes($_POST['fields']))));
//Resolve dependencies
//Returns an array ($dependentFields) with the names of the fields
//that depends of fields passed through AJAX ($_GET/$_POST)
$dependentFields=array();
for($r=0;$r<sizeof($newValues);$r++) {
	$newValues[$r]=(array)$newValues[$r];
	$G_FORM->setValues($newValues[$r]);
	//Search dependent fields
	foreach($newValues[$r] as $k => $v) {
		$myDependentFields = explode( ',', $G_FORM->fields[$k]->dependentFields);
		$dependentFields=array_merge($dependentFields, $myDependentFields);
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
	$sendContent[$r]->value=$G_FORM->values[$d];
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
?>