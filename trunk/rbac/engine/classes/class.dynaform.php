<?php
/**
 * class.dynaform.php
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

class Dynaform  extends DBTable
{
  var $buffer;
  var $bufIndex;
  var $size;
  var $XmlFields;
  var $curRow;
  var $varFields;
  var $tagLowerCase = 0;
  var $childNro = 1;
  var $vRow = array();

 function SetTo( $objConnection )
 {
  parent::SetTo( $objConnection, "DYNAMIC_FORM");
 }

 function replaceExpresionXmlFields ( $expresion )  {
   //before call this function call $Fields  = $dyna->ArrayFromXml( $defDyna );
   //in order to get the array in $this->XmlFields
  //$Aux = '-ABCDEFGHIJKLMNÑOPQRSTUVWXYZabcdefghijklmnñopqrstuvwxyz1234567890_';
  foreach ( $this->XmlFields as $key=>$value ){
    $expresion = str_replace("@@$key ", $value . ' ', $expresion . ' ');
    $expresion = str_replace("@@$key&nbsp;", $value . ' ', $expresion. ' ');
  }



  //for ($i;$i<strlen($expresion);$i++) echo substr($expresion, $i, 1) . ' = ' . ord(substr($expresion, $i, 1)) . ',<br>';die;

  $toEval = $expresion;
  return $expresion;
 }

 function ParseExpresionXmlFields ( $expresion )  {

   //before call this function call $Fields  = $dyna->ArrayFromXml( $defDyna );
   //in order to get the array in $this->XmlFields
   if ( $expresion == '' ) return '';
  //Parsing de la variable
  $aux = str_replace ( '+','.', $expresion );
  $aux = str_replace ( '==', ' == ' , $aux );

  $toParse = explode ( " ", $aux);  //aqui hay que mejorar el parsing.
//  print_r ($toParse);
  $toEval = "\$resultado = (";
  $i = 0;
  while ( $i < count ($toParse) ) {
    if ( substr ($toParse[$i],0,2) == "@@" ) {
      $aux = $this->XmlFields[ substr ($toParse[$i],2)];
      $toEval .= " " . ( trim($aux) == "" ? "''" : "'$aux'" );
    }
    else
      $toEval .= " " . $toParse[$i];
    $i++;
  }
  $toEval .= ");";
  //print $toEval;
  eval($toEval); //$title
  return $resultado;
 }

function ParseExpresionXmlFields_Alert ( $expresion )  {

  if ( $expresion == '' ) return '';
  $toParse = explode ( " ", $expresion);
  $toEval = "";
  $i = 0;
  while ( $i < count ($toParse) ) {
  	$toParse[$i] = ltrim($toParse[$i]);
    if ( substr ($toParse[$i],0,2) == "@@" ) {
      $aux = $this->XmlFields[ substr ($toParse[$i],2)];
      $toEval .= " " . ( trim($aux) == "" ? "" : "$aux" );
    }
    else
      $toEval .= " " . $toParse[$i];
    $i++;
  }

  return $toEval;

 }

  //para realizar el parsing...
  function getTag () {
    $token = "";
    $hasChild = 0;

    //PRIMER BUCLE buscar tag >
    while ($this->bufIndex <= $this->size && $car !='>') {
      $token .= $car;
      $car = $this->buffer [$this->bufIndex++];
    }

    $key = $token;

    //SEGUNDO BUCLE buscar VALUE
    $car = $this->buffer [$this->bufIndex++]; $token = "";
    while ($this->bufIndex <= $this->size && $car !='<') {
      $token .= $car;
      $car = $this->buffer [$this->bufIndex++];
    }
    $value = trim($token);
//    print "<b>$key</b> $value<br>";

    //TERCER BUCLE buscar </tag>
    $res = 0;
    while ($res == 0) {
      if ($car != "<") while ( ($car = trim( $this->buffer [$this->bufIndex++] )) == "");
      if ($car=="<") {
        $res = 0;
        $car = $this->buffer [$this->bufIndex++];
        //ADDCHILD nodo recien creado
        if ($car != "/") {
          $this->bufIndex --;

          //this code enable the sub-rows inside the same xml variable...
          $hasChild = 1;
          $antRow = &$this->curRow;
          $this->curRow = &$this->curRow[ $key ];
          $childRow     = $this->getTag ();
          $this->curRow = &$antRow;

        } else $res = 1;
      }
      else die ("se esperaba '<' y se encontro $car");
    }

    //se supone que se ha leido "</" y continuamos con el nombre
    $token = "";
    $car = $this->buffer [$this->bufIndex++];
    while ($this->bufIndex <= $this->size && $car !='>') {
      $token .= $car;
      $car = $this->buffer [$this->bufIndex++];
    }
    if ( strcmp ($key, trim($token)) != 0) die ("no corresponde fin de tag &lt;/$token> con &lt;$key>");

    //convertir los nombres de variables en minusculas solo si...
    if ($this->tagLowerCase)  $key = strtolower($key);

    if ( $hasChild == 0 ) {
      $this->curRow [$key] = $value;
     }

     $this->XmlFields = &$this->curRow ;
    return;
  }


function replaceTextWithFields ( $txt, $Fields, $default=NULL)
{
  $fieldName = array();
  $str = $txt;
  do {
    $found = ereg ( "@@[a-zA-Z0-9_]+", $str, $token);
    if ( $found ) {
    	$size = strlen ( $token[0] );
      $posstr = strpos( $str, $token[0] );
      $str = substr ( $str, 0, $posstr) . substr ($str, $posstr+$size) ;
    	$fieldNames[$size][] = $token[0];
    }
  } while ( $found );
  if (is_null($fieldNames)) $fieldNames = array();
  $keys = array_keys ($fieldNames); //se obtienen los tamaños
  rsort ($keys);  //se ordena en el mismo array

  foreach ( $keys as  $k=>$len ) { //se reemplaza de mayor a menor longitud de variables.
      foreach ( $fieldNames[$len] as $j=>$token ) {
        $value = $Fields[ substr($token,2) ];
        //var_dump($value);echo '<br>';
        if ((is_null($value) && !is_null($default)) || ($value == '')) {$value = "''";
        //echo "$value<br>";
        }
        if ($value == '') $value = "''";
        $txt = str_replace ( $token,  $value, $txt );
      }
  }
  return $txt;
}

function replaceTextWithFields2 ( $txt, $Fields, $default=NULL)
{
  $fieldName = array();
  $str = $txt;
  do {
    $found = ereg ( "@@[a-zA-Z0-9_]+", $str, $token);
    if ( $found ) {
    	$size = strlen ( $token[0] );
      $posstr = strpos( $str, $token[0] );
      $str = substr ( $str, 0, $posstr) . substr ($str, $posstr+$size) ;
    	$fieldNames[$size][] = $token[0];
    }
  } while ( $found );
  if (is_null($fieldNames)) $fieldNames = array();
  $keys = array_keys ($fieldNames); //se obtienen los tamaños
  rsort ($keys);  //se ordena en el mismo array

  foreach ( $keys as  $k=>$len ) { //se reemplaza de mayor a menor longitud de variables.
      foreach ( $fieldNames[$len] as $j=>$token ) {
        $value = $Fields[ substr($token,2) ];
        //var_dump($value);echo '<br>';
        //if ((is_null($value) && !is_null($default)) || ($value == '')) {$value = "''";
        //echo "$value<br>";
        //}
        $txt = str_replace ( $token,  $value, $txt );
      }
  }
  return $txt;
}

//reemplaza un string con las variables que se encuentran en el
//default Dynaform de una Application
//opcionalmente se puede a;adir otro array con variables temporales, util para
//campos dependientes, porque los valores todavia no estan en la BD
function replaceStringWithDefaultDynaform( $appid, $str, $temporalFields ='' )
{

	if($appid == "-1")	{
		$Fields = array();
	  if ( is_array ( $temporalFields ) )
    foreach ( $temporalFields as $key=>$val )
      $Fields[ $key ] = $val;

		return $this->replaceTextWithFields ( $str, $Fields);
	}

  if ( is_array ( $temporalFields ) )
  $stQry = "SELECT APP_DEF_DYNAFORM FROM APPLICATION WHERE UID = $appid";
  $dset = $this->_dbses->Execute( $stQry );
  $row = $dset->Read();
  $dynaid = $row['APP_DEF_DYNAFORM'];
  $Fields = $this->ArrayFromXml( $dynaid );
  if ( is_array ( $temporalFields ) )
    foreach ( $temporalFields as $key=>$val )
      $Fields[ $key ] = $val;
  return $this->replaceTextWithFields ( $str, $Fields);
}

//Added by Onti 02.02.2005
//obtiene un array con los valores por Default de una aplicacion,
//si todavia no tiene registro Dynaform, se creará automaticamente.
function getFieldsDefaultDynaform( $appid, $reqDyna )
{
  $stQry = "SELECT APP_DEF_DYNAFORM, APP_PROCESS FROM APPLICATION WHERE UID = $appid";
  $dset = $this->_dbses->Execute( $stQry );

  $row = $dset->Read();

  $appDefDynaform=0;
  if($row['APP_DEF_DYNAFORM']!='')
  {
  	$appDefDynaform = $row['APP_DEF_DYNAFORM'];
	}
  $proid          = $row['APP_PROCESS']; //para generar la clave

  //verificar que existe el "registro dynaform"
  $stQry = "SELECT * FROM DYNAMIC_FORM where DYN_APPLICATION = $appid and UID = $appDefDynaform ";
  $dset = $this->_dbses->Execute( $stQry );
  $row = $dset->Read();
  if ( ! is_array ( $row ) ) {
    $appDefDynaform = 0; //no existe registro, entonces se tiene que crear uno nuevo.
  }

  //si existe "registro dynaform" se devuelve el array de las variables
  if ($appDefDynaform != 0 ) {
    return ( $this->ArrayFromXml( $appDefDynaform ) );
  }
  //si no existe el registro se crea con las variables del sistema

  $nextIndex = 1; //es seteado en 1 porque el formulario es global por lo que el indice no cambiará y es igual a 1

  $Fields = array ();
  $Fields['UID']              = "0";
  $Fields['DYN_APPLICATION']  = $appid;
  $Fields['DYN_REQ_DYNAFORM'] = $reqDyna;
  $Fields['DYN_INDEX']        = $nextIndex;
  $Fields['DYN_CREATE_DATE']  = G::CurDate();
  $Fields['DYN_UPDATE_DATE']  = G::CurDate();
  $Fields['DYN_VARS']         = $xmlvar;
  $Fields['DYN_DELINDEX']      = 1; //delindex = 1, porque es la primera derivacion
  $this->is_new = true;
  $this->Fields = $Fields;
  $this->Save();

  $lastID = $this->_dbc->GetLastID();

  //actualizar tabla Application
  $stQry = "update APPLICATION SET APP_DEF_DYNAFORM = $lastID WHERE UID = $appid";
  $dset = $this->_dbses->Execute( $stQry );

  /* Cambiando la forma en la que se generan los passwords para el seguimiento de los casos */
  $Dataset = $this->_dbses->Execute('SELECT * FROM APPLICATION WHERE UID = ' . $appid);
  $Row     = $Dataset->Read();

  //autogenerate a password.
  //$plain = $proid . $appid;
  //la clave se auto-genera basado en los primeros 5 digitos despues de aplicar md5 ( plain )
  //$mda5  = substr ( ereg_replace( '([[:alpha:]]+)', '', md5($plain) )   , 0, 5 );
  //Here we can add system variables
  $xmlvar .= "<SYS_DATE>" . date("Y-m-d H:i:s") . "</SYS_DATE>\n";
  $xmlvar .= "<SYS_APPLICATION_ID>$appid</SYS_APPLICATION_ID>\n";
  //$xmlvar .= "<SYS_APPLICATION_CODE>$proid-$appid</SYS_APPLICATION_CODE>\n";
  //$xmlvar .= "<SYS_APPLICATION_PASSWORD>$mda5</SYS_APPLICATION_PASSWORD>\n";
  $xmlvar .= "<SYS_APPLICATION_CODE>" . substr($Row['APP_GUID'], 0, 7) . "</SYS_APPLICATION_CODE>\n";
  $xmlvar .= "<SYS_APPLICATION_PASSWORD>" . substr($Row['APP_GUID'], 7, strlen($Row['APP_GUID'])) . "</SYS_APPLICATION_PASSWORD>\n";
  $xmlvar .= $this->getSystemVariables();

  //actualizar las variables, porque el password se generó despues de obtener el lastID
  $stQry = "update DYNAMIC_FORM SET DYN_VARS = '$xmlvar' WHERE UID = $lastID";
  $this->_dbses->Execute( $stQry );

  if ( SHOW_DEBUG_INFO == '1' )
    print "creating default row for application $appid";

  return ( $this->ArrayFromXml( $lastID ) );

}


// lee el registro $uid de la tabla Dynaforms, y convierte el campo DYN_VARS (text)
// y devuelve el array.
 function ArrayFromXml( $uid )
 {
  $this-> Load($uid);
  $this-> buffer = $this->Fields["DYN_VARS"];
  $this-> bufIndex = 0;
  $this-> size = strlen ($this->buffer);

  $this->XmlFields = array ();
  $this->curRow = &$this->XmlFields;
  while ( $this->bufIndex <= $this->size ) {
    $car = $this->buffer [$this->bufIndex++];
    if ($car == '<' )
      $this->getTag();
  }

  return $this->XmlFields;
 }

// Realiza el parsing de un string que contiene un XML
// y devuelve el array.
 function ArrayFromXmlStr( $buffer )
 {
  $this->buffer = $buffer;
  $this->bufIndex = 0;
  $this->size = strlen($this->buffer);

  $this->XmlFields = array();
  $this->curRow = &$this->XmlFields;
  while ( $this->bufIndex <= $this->size ) {
    $car = $this->buffer [$this->bufIndex++];
    if ($car == '<' )
      $this->getTag();
  }
  return $this->XmlFields;
 }


/* dangerous */

function UpdateGlobalXml( $appid, $xmlvar, $delid , $dynaid)
 {
  //si se usa las variables globales, recuperar APP_DEF_DYNAFORM
  //si existe realizar un SaveXml (APP_DEF_DYNAFORM)

  $stQry = "SELECT APP_DEF_DYNAFORM FROM APPLICATION WHERE UID = $appid";
  $dset = $this->_dbses->Execute( $stQry );

  $row = $dset->Read();
  $appDefDynaform = 0;
  if ( is_array ($row) ) $appDefDynaform = $row['APP_DEF_DYNAFORM'];

  if ($appDefDynaform != 0 ) {
    //si ya existe las variables, se realiza un update acumulativo con SaveXml
    //var_dump($xmlvar);die;
    //echo "$appDefDynaform, $xmlvar, $delid, $dynaid";die;
      $this->SaveXml ($appDefDynaform, $xmlvar, $delid, $dynaid );
  }
  else
  alert();
 }

 function lastDynIndex($app_id)
 {
 	  $stQry = "SELECT DYN_INDEX FROM DYNAMIC_FORM WHERE DYN_APPLICATION = $app_id ORDER BY DYN_INDEX DESC";
    $dset = $this->_dbses->Execute( $stQry );
    $row = $dset->Read();
    $DYN_INDEX = $row['DYN_INDEX']+1;
    return $DYN_INDEX;
 }


  function getSystemVariables() {
  	global $HTTP_SESSION_VARS;
  	$xmlvar = '';
  	$xmlvar .= '<SYS_DELEGATION_ID>'       . $HTTP_SESSION_VARS['CURRENT_DELEGATION'] . '</SYS_DELEGATION_ID>';
    $xmlvar .= "<SYS_USER_LOGGED>"         . $HTTP_SESSION_VARS['USER_LOGGED'] . "</SYS_USER_LOGGED>\n";
    $xmlvar .= "<SYS_USER_LOGGED_NAME>"    . $HTTP_SESSION_VARS['USER']        . "</SYS_USER_LOGGED_NAME>\n";
    $xmlvar .= "<SYS_LOGGED_NAME>"    		 . $HTTP_SESSION_VARS['USER_NAME']        . "</SYS_LOGGED_NAME>\n";

    //disable because this value is obsolete
    //$xmlvar .= "<SYS_USER_LOGGED_NAME2>"         . $HTTP_SESSION_VARS['USER']                 . "</SYS_USER_LOGGED_NAME2>\n";
    return $xmlvar;
  }

 function Save_global_NewXml( $appid, $reqDyna, $xmlvar , $useGlobalVars,  $delid, $systemVariables = 1)
 {
    $stQry = "SELECT APP_DEF_DYNAFORM, APP_PROCESS FROM APPLICATION WHERE UID = $appid";
    $dset = $this->_dbses->Execute( $stQry );
    $row = $dset->Read();
    $appDefDynaform = 0;
    $proid = $row['APP_PROCESS'];

    if ( is_array ($row) ) $appDefDynaform = $row['APP_DEF_DYNAFORM'];

    if ($appDefDynaform != 0 ) {
 			$Next_DynIndex = $this->lastDynIndex($appid);
      $this->is_new = true;
      $this->Fields['UID']              = 0;
      $this->Fields['DYN_APPLICATION']  = $appid;
      $this->Fields['DYN_REQ_DYNAFORM'] = $reqDyna;
      $this->Fields['DYN_INDEX']        = $Next_DynIndex;
      $this->Fields['DYN_CREATE_DATE']  = G::CurDate();
      $this->Fields['DYN_UPDATE_DATE']  = G::CurDate();
      $this->Fields['DYN_VARS']         = "";//este registro se graba en blanco porque los datos se registraran en el primer registro
      $this->Fields['DYN_DELINDEX']     = $delid;
      $this->is_new = true;
      $this->Save();
      $lastID = $this->_dbc->GetLastID();


      //si ya existe las variables, se realiza un update acumulativo con SaveXml
      $this->SaveXml ($appDefDynaform, $xmlvar, $delid, $lastID, $systemVariables);

      return $lastID;
    }

  $nextIndex = 1; //es seteado en 1 porque el formulario es global por lo que el indice no cambiará

  $Fields = array ();
  $Fields['UID']              = "0";
  $Fields['DYN_APPLICATION']  = $appid;
  $Fields['DYN_REQ_DYNAFORM'] = $reqDyna;
  $Fields['DYN_INDEX']        = $nextIndex;
  $Fields['DYN_CREATE_DATE']  = G::CurDate();
  $Fields['DYN_UPDATE_DATE']  = G::CurDate();
  $Fields['DYN_VARS']         = $xmlvar;
  $Fields['DYN_DELINDEX']      = $delid;
  //$this->is_new = false;
  $this->is_new = true;
  $this->Fields = $Fields;
  $this->Save();

  $lastID = $this->_dbc->GetLastID();

  $stQry = "update APPLICATION SET APP_DEF_DYNAFORM = $lastID WHERE UID = $appid";
  $dset = $this->_dbses->Execute( $stQry );

  /* Cambiando la forma en la que se generan los passwords para el seguimiento de los casos */
  $Dataset = $this->_dbses->Execute('SELECT * FROM APPLICATION WHERE UID = ' . $appid);
  $Row     = $Dataset->Read();

  //autogenerate a password.
  //$plain = $proid . $appid;
  //la clave se auto-genera basado en los primeros 5 digitos despues de aplicar md5 ( plain )
  //$mda5  = substr ( ereg_replace( '([[:alpha:]]+)', '', md5($plain) )   , 0, 5 );
  //Here we can add system variables
  $xmlvar .= "<SYS_DATE>" . date("Y-m-d H:i:s") . "</SYS_DATE>\n";
  $xmlvar .= "<SYS_APPLICATION_ID>$appid</SYS_APPLICATION_ID>";
  //$xmlvar .= "<SYS_APPLICATION_CODE>$proid-$appid</SYS_APPLICATION_CODE>";
  //$xmlvar .= "<SYS_APPLICATION_PASSWORD>$mda5</SYS_APPLICATION_PASSWORD>";
  $xmlvar .= "<SYS_APPLICATION_CODE>" . substr($Row['APP_GUID'], 0, 7) . "</SYS_APPLICATION_CODE>\n";
  $xmlvar .= "<SYS_APPLICATION_PASSWORD>" . substr($Row['APP_GUID'], 7, strlen($Row['APP_GUID'])) . "</SYS_APPLICATION_PASSWORD>\n";

  if ( $systemVariables == 1 )
    $xmlvar .= $this->getSystemVariables();

  $stQry = "update DYNAMIC_FORM SET DYN_VARS = '$xmlvar' WHERE UID = $lastID";
  $this->_dbses->Execute( $stQry );

  return $lastID;
 }

function Save_local_NewXml( $appid, $reqDyna, $xmlvar , $useGlobalVars, $delid, $systemVariables = 1)
 {
  //obtener siguiente indice
  $nextIndex = 1;
  $stQry = "SELECT if( ISNULL(MAX(DYN_INDEX)), 1, MAX(DYN_INDEX) + 1) AS NEXT_INDEX " .
           " FROM DYNAMIC_FORM WHERE DYN_APPLICATION = $appid AND DYN_REQ_DYNAFORM = '".$reqDyna."' ";
  $this->_dbses->Query( $stQry );
  $dset = new DBRecordSet( $this->_dbses->result );
  if( $dset->Count() > 0 ){
   $row = $dset->Read();
   $nextIndex = $row['NEXT_INDEX'];
  }

  $Fields = array ();
  $Fields['UID']              = "0";
  $Fields['DYN_APPLICATION']  = $appid;
  $Fields['DYN_REQ_DYNAFORM'] = $reqDyna;
  $Fields['DYN_INDEX']        = $nextIndex;
  $Fields['DYN_CREATE_DATE']  = G::CurDate();
  $Fields['DYN_UPDATE_DATE']  = G::CurDate();
  $Fields['DYN_VARS']         = $xmlvar;
  $Fields['DYN_DELINDEX']     = $delid;

  $this->Fields = $Fields;
  $this->is_new = true;
  $this->Save();

  return ;
 }


/* dangerous */

 function SaveXml( $dynaid,  $xmlvar, $delid , $dynaid_original = 0, $systemVariables = 1)
 {

 	// dynaid ES EL UID DE LA TABLA dynamic_form

 	/*
 	echo "\n <br> dynaid = $dynaid";
 	echo "\n <br> xmlvarf  $xmlvar";
 	echo "\n <br> dynaidoriginal $dynaid_original";
 	die;
 	*/

  // Added by JC
  // Si el el parametro xmlvar es un array lo convierte a un string
  // con formato xml

  if (is_array($xmlvar)) {
  	$xmlvarkeys = array_keys($xmlvar);
  	foreach ($xmlvarkeys as $key) {
  		if (is_array($xmlvar[$key])) {
  			$xmlvar1 .= '<' . $key . '>';
  			$i = 1;
  			foreach ($xmlvar[$key] as $key2=>$val2) {
  				if (is_array($val2)) {
  					$xmlvar1 .= '<' . $key2 . '>';
  					foreach ($val2 as $key3=>$val3) {
  						if (is_array($val3)) {
  						  foreach ($val3 as $key4=>$val4) {
  						  	if (is_array($val4)) {
  						  		foreach ($val4 as $key5=>$val5) {
  						  			$xmlvar1 .= '<' . $key5 . '>' . G::unhtmlentities(htmlentities(str_replace("'", "´", $val5))) . '</' . $key5 . '>';
  						  		}
  						  	}
  						  	else {
  						  		$xmlvar1 .= '<' . $key4 . '>' . G::unhtmlentities(htmlentities(str_replace("'", "´", $val4))) . '</' . $key4 . '>';
  						  	}
  						  }
  						}
  						else {
  						  $xmlvar1 .= '<' . $key3 . '>' . G::unhtmlentities(htmlentities(str_replace("'", "´", $val3))) . '</' . $key3 . '>';
  						}
  				  }
  				  $xmlvar1 .= '</' . $key2 . '>';
  				}
  				else {
  					$xmlvar1 .= '<' . $key2 . '>' . G::unhtmlentities(htmlentities(str_replace("'", "´", $val2))) . '</' . $key2 . '>';
  				}
  			}
  			$xmlvar1 .= '</' . $key . '>';
  		}
  		else {
  			$xmlvar1 .= '<' . $key . '>' . G::unhtmlentities(htmlentities(str_replace("'", "´", $xmlvar[$key]))) . '</' . $key . '>';
  		}
  	}
  	$xmlvar = $xmlvar1;
  }
  //End by JC




  //var_dump($xmlvar);die;

  if ( $systemVariables == 1 )
    $xmlvar .= $this->getSystemVariables();

  //var_dump($xmlvar);die;

  $oldFields = $this->ArrayFromXml( $dynaid );
  $newFields = $this->ArrayFromXmlStr($xmlvar);

  //var_dump($oldFields);echo '<br><br>';
  //var_dump($newFields);die;


  $this->Load($dynaid);

  //esto era para formularios multiples
  /*if ($NroMultiple != 0){
  	if (is_array($newFields)){
    	foreach ($newFields as $key=>$val) {
    	  $count = 1;
    	  	if (is_array($val)){
    	  		foreach ($val as $key2=>$val2)
    	  				if ($key2 == $NroMultiple)
    	  						$oldFields[$key][$NroMultiple] = $val2;
    	  	}else{
    	  		$oldFields[$key] = $val;
    	  	}
    	}
   }
  }
  else {*/

  	// Se asigna los nuevos valores al array oldfields

    if (is_array($newFields)) {
    	foreach ($newFields as $key=>$val) {
    		if (is_array($val)) {
    		  foreach ($val as $key2=>$val2) {
        		if (is_array($val2)) {
    		      foreach ($val2 as $key3=>$val3) {
    		      	if (is_array($val3)) {
    		      		foreach ($val3 as $key4=>$val4) {
    		      			if (is_array($val4)) {
    		      				foreach ($val4 as $key5=>$val5) {
    		      					if (!is_array($oldFields[$key][$key2][$key3][$key4]))
    		      					  $oldFields[$key][$key2][$key3][$key4] = array();
    		      					if (!is_array($oldFields[$key][$key2][$key3][$key4][$key5]))
    		      					  $oldFields[$key][$key2][$key3][$key4][$key5] = $val5;
    		      				}
    		      			}
    		      			else {
    		      				if (!is_array($oldFields[$key][$key2][$key3]))
    		    	          $oldFields[$key][$key2][$key3] = array();
  	  	              $oldFields[$key][$key2][$key3][$key4] = $val4;
    		      			}
    		      		}
    		      	}
    		      	else {
    		      		if (!is_array($oldFields[$key][$key2]))
    		    	      $oldFields[$key][$key2] = array();
  	  	          $oldFields[$key][$key2][$key3] = $val3;
    		      	}
    		      }
    		    }
    		    else {
    		    	if (!is_array($oldFields[$key]))
    		    	  $oldFields[$key] = array();
  	  	      $oldFields[$key][$key2] = $val2;
  	  	    }
    	  	}
    	  }
    	  else {
    	  	$oldFields[$key] = $val;
    	  }
    	}
    }
  //}

  //Added By JC - Actualizar título y mensajes de la aplicación
  $Dataset = $this->_dbses->Execute('SELECT DYN_APPLICATION FROM DYNAMIC_FORM WHERE UID = ' . $dynaid);
  $Row     = $Dataset->Read();
  $App     = $Row['DYN_APPLICATION'];
  $Dataset = $this->_dbses->Execute('SELECT APP_PROCESS, APP_ROUTE_STEP FROM APPLICATION WHERE UID = ' . $App);
  $Row     = $Dataset->Read();
  $process_id=$Row['APP_PROCESS'];
  $Dataset = $this->_dbses->Execute("SELECT ROU_TASK FROM ROUTE WHERE ROU_PROCESS = '" . $Row['APP_PROCESS'] . "' AND ROU_STEP = " . $Row['APP_ROUTE_STEP']);
  $Row     = $Dataset->Read();
  G::LoadClass('task');
  $Task    = new Task($this->_dbc);
  $Task->Load($Row['ROU_TASK']);
  G::LoadClass('application');
  $Application = new Application($this->_dbc);
  $Application->Load($App);
  if ($Task->Fields['TAS_DEF_TITLE'] != '')
    $Application->Fields['APP_TITLE'] = $this->ReplaceTextWithFields($Task->Fields['TAS_DEF_TITLE'], $oldFields);
  if ($Task->Fields['TAS_DEF_DESCRIPTION'] != '')
    $Application->Fields['APP_DESCRIPTION'] = $this->ReplaceTextWithFields($Task->Fields['TAS_DEF_DESCRIPTION'], $oldFields);
  if ($Task->Fields['TAS_DEF_PROC_CODE'] != '')
    $Application->Fields['APP_PROC_CODE'] = $this->ReplaceTextWithFields($Task->Fields['TAS_DEF_PROC_CODE'], $oldFields);
  $Application->is_new = false;
  $Application->Save();
  //End

  $xmlvar = '';

  /*var_dump($oldFields);echo '<br><br>';
  var_dump($newFields);die;*/

  foreach($oldFields as $key=>$val){
  	// To do: averiguar que es ERROR_MESSAGE
  	if (strtoupper($key) != "ERROR_MESSAGE") {
      $xmlvar .= "<$key>";
      if (is_array($val)) {
      	foreach ($val as $key2=>$val2) {
      		if (!isset($newFields[$key]) || isset($newFields[$key][$key2])) {
      		  $xmlvar .= "\n  <$key2>";
      		  if ( is_array ($val2) ) {
      			  foreach ($val2 as $key3=>$val3) {
      			  	$xmlvar .= "\n  <$key3>";
      			  	if (is_array($val3)) {
      			  		foreach ($val3 as $key4=>$val4) {
      			  			$xmlvar .= "\n  <$key4>";
      			  			if (is_array($val4))
      			  			 {
      			  				foreach ($val4 as $key5=>$val5)
      			  				{
      			  					$xmlvar .= "\n  <$key5>";
      			  					$xmlvar .= $val5;
      			  					$xmlvar .= "\n  </$key5>";
      			  				}
      			  			}
      			  			else
      			  				$xmlvar .= $val4;
      			  			$xmlvar .="</$key4>\n";
      			  		}
      			  	}
      			  	else
      			  		$xmlvar .= $val3;
      			  	$xmlvar .="</$key3>\n";
   					  }
   					}
 					  else
    				   $xmlvar .= $val2;
  				     $xmlvar .="</$key2>\n";
  			  }
      	}
      }
      else
      {
      	$xmlvar.= $val;
      }
      	$xmlvar.= "</$key>\n";
    }

  }


/************ Nuevo Maui ********/
  //actualizar la tabla dynamic_form

  $this->Fields['DYN_UPDATE_DATE']  = G::CurDate();
  $this->Fields['DYN_VARS']         = $xmlvar;

  if ($dynaid_original==0)
    $this->Fields['DYN_DELINDEX']     = $delid;

  $this->is_new = false;
  $this->Save();

  if($dynaid_original !=0)
  {
  	$this-> Load ($dynaid_original);
    $this-> Fields['DYN_DELINDEX'] = $delid;
    $this-> is_new = false;
    $this-> Save();
  }

//Added  DOC David

 $Connection 					= new DBConnection(DB_HOST, DB_WIZARD_REPORT_USER, DB_WIZARD_REPORT_PASS, DB_WIZARD_REPORT_SYS);
 $Connection_wf 			= new DBConnection(DB_HOST);

 //global $HTTP_SESSION_VARS;


 $uid_application =  $App;
 //echo "$Connection,$process_id,$uid_application,$dynaid_original,$Connection_wf";die;
 $oDBdynaform  = new DB_dynaform($Connection,$process_id,$uid_application,$dynaid_original,$Connection_wf);
 $oDBdynaform -> setXMLArray($oldFields);
 $oDBdynaform -> buildTable();


 // END DOC

}


/*****************************/
//creacion de rtf
  function makeDir ($destination) {
    $continue = false;

    //si no existe archivo de directorio upload, hay que crearlo.
    if( !file_exists( $destination ) )
    {
      $continue = mkdir( $destination, 0777 );
      chmod( $destination, 0777 );
    }
    else
    {
      $continue = true;
    }
    return $continue;
  }

/*****************************/
//crea directorios: se le envia toda una direción y crea los directorios que no existen
  function makeDirs ($destination) {
    $dir = explode("/", $destination);
    $directorio = "";
    if(is_array ($dir))
    foreach($dir as $key => $value){
    	$directorio .= $value."/";
    	if( !is_dir( $directorio ))
    	{
    		mkdir( $directorio, 0777);
      	chmod( $directorio, 0777);
    	}

		}
  }


function createFileRTF ($New_File, $Filename, $Default_Dynaform, $Process) {
  G::LoadSystem('xmlform');
	$Vars       = $this->varFields;
	$VarKeys    = array_keys($Vars);
	foreach ($VarKeys as $VarKey) {
		if (!is_array($Vars[$VarKey]))
	    if (($Vars[$VarKey] === '0/0/0') || ($Vars[$VarKey] === '0-0-0'))
		    $Vars[$VarKey] = '';
	  /*if (!is_array($Vars[$VarKey]))
		  $Vars[$VarKey] = G::unhtmlentities($Vars[$VarKey]);
		else {
			$AuxKeys = array_keys($Vars[$VarKey]);
			foreach ($AuxKeys as $AuxKey)
			  if (!is_array($Vars[$VarKey][$AuxKey]))
			    $Vars[$VarKey][$AuxKey] = G::unhtmlentities($Vars[$VarKey][$AuxKey]);
			  else {
			  	$AuxKeys1 = array_keys($Vars[$VarKey][$AuxKey]);
			  	foreach ($AuxKeys1 as $AuxKey1)
			  	  if (!is_array($Vars[$VarKey][$AuxKey]))
			        $Vars[$VarKey][$AuxKey][] = G::unhtmlentities($Vars[$VarKey][$AuxKey]);
			      else {
			  	    $Vars[$VarKey][$AuxKey][$AuxKey1] = G::unhtmlentities($Vars[$VarKey][$AuxKey][$AuxKey1]);
			  	  }
			  }
		}*/
	}
	$Connection = new DBConnection;
	$Session    = new DBSession($Connection);
	$Dataset    = $Session->Execute("SELECT REQ_FILENAME FROM REQ_DYNAFORM WHERE UID = '" . $this->Fields['DYN_REQ_DYNAFORM'] . "'");
	$Row        = $Dataset->Read();
	if ($Row['REQ_FILENAME'] != '') {
	  $Form       = new XmlForm();
	  $Form->home = PATH_DYNAFORM;
	  $ArrayForm  = $Form->parseXmlformToArray( $Process . '/' . $Row['REQ_FILENAME']);
	  foreach ($VarKeys as $VarKey) {
		  switch ($ArrayForm[$VarKey]['type']) {
  			case 'currency2':
	  		  $Vars[$VarKey] = G::NumberToCurrency($Vars[$VarKey]);
		  	break;
			  case 'percentage2':
			    $Vars[$VarKey] = G::NumberToPercentage($Vars[$VarKey]);
			  break;
	    }
	  }
	}
	$this->fp   = fopen($Filename, 'rb');
  $this->gp   = fopen($New_File, 'wb');
  $Content    = fread($this->fp, filesize($Filename));
  if (SYS_SYS == 'sij')
    $Content = str_replace('{' .  chr(92). 'rtf1' . chr(92), '{' . chr(92) . 'rtf1' . chr(92) . 'annotprot' . chr(92), $Content);
  $Forms      = array();
  $NRF        = array();
  //@@IF
  $Initial_Position = 0;
  if (strpos($Content, ' \v @@IF ', $Initial_Position) !== false) {
  	G::LoadClass('webbot');
  	$Webbot = new Webbot;
    while (strpos($Content, ' \v @@IF ', $Initial_Position) !== false) {
    	$AuxIniPos        = strpos($Content, ' \v @@IF ', $Initial_Position);
  	  $Initial_Position = strpos($Content, ' \v @@IF ', $Initial_Position) + strlen(' \v @@IF ');
  	  $Final_Position   = strpos($Content, '\par', $Initial_Position);
  	  $Condition        = str_replace('\v ', '', substr($Content, $Initial_Position, $Final_Position - $Initial_Position));
  	  $Initial_Position = $Final_Position + strlen('\par');
  	  $Final_Position   = strpos($Content, '\v @@ELSE\par', $Initial_Position);
  	  $THEN             = substr($Content, $Initial_Position, $Final_Position - $Initial_Position);
  	  $Initial_Position = $Final_Position + strlen('\v @@ELSE\par');
  	  $Final_Position   = strpos($Content, '\v @@END-IF\par', $Initial_Position);
  	  $ELSE             = substr($Content, $Initial_Position, $Final_Position - $Initial_Position);
  	  $Final_Position   = $Final_Position + strlen('\v @@END-IF\par');
  	  $Condition        = $this->replaceTextWithFields($Condition, $Vars);
  	  $Response         = $Webbot->Evaluate($Condition, $Vars, 1);
  	  if ($Response[2]) {
  	    $Content = str_replace(substr($Content, $AuxIniPos, $Final_Position - $AuxIniPos), $THEN, $Content);
  	    $Initial_Position -= strlen($ELSE);
  	  }
  	  else {
  	    $Content = str_replace(substr($Content, $AuxIniPos, $Final_Position - $AuxIniPos), $ELSE, $Content);
  	    $Initial_Position -= strlen($THEN);
  	  }
    }
  }
  //@@BEGIN-MULTIPLE
  $Initial_Position = 0;
  while (strpos($Content, ' \v @@BEGIN-MULTIPLE', $Initial_Position) !== false) {
    $Initial_Position = strpos($Content, ' \v @@BEGIN-MULTIPLE', $Initial_Position) + strlen(' \v @@BEGIN-MULTIPLE');
    $Form_Name        = trim(substr($Content, $Initial_Position, strpos($Content, Chr(92) . 'par', $Initial_Position) - $Initial_Position));
    $Forms[]          = $Form_Name;
    $Initial_Position = $Initial_Position + strlen($Form_Name) + strlen(Chr(92) . 'par') + 1;
    $Final_Position   = strpos($Content, ' \v @@END-MULTIPLE', $Initial_Position);
    $String_To_Repeat = substr($Content, $Initial_Position, $Final_Position - $Initial_Position);
    $Content          = str_replace($String_To_Repeat, '/--*--/', $Content);
    $Number_Registers = count($Vars[$Form_Name]);
    $String_Aux_1     = '';
    for ($i = 1; $i <= $Number_Registers; $i++) {
      $String_Aux_2 = $String_To_Repeat;
      $Keys         = array_keys($Vars[$Form_Name][$i]);
      $Show         = false;
      foreach ($Keys as $Key)
        if ($Vars[$Form_Name][$i][$Key] != '') {
          $Show = true;
        }
      if ($Show) {
        foreach ($Keys as $Key) {
          $String_Aux_2 = str_replace('@@' . $Key . ' ', $Vars[$Form_Name][$i][$Key] . ' ', $String_Aux_2);
          $String_Aux_2 = str_replace('@@' . $Key . chr(92), $Vars[$Form_Name][$i][$Key] . chr(92), $String_Aux_2);
        }
        $String_Aux_1 .= $String_Aux_2;
      }
    }
    $Content = str_replace('/--*--/', $String_Aux_1, $Content);
    if ($String_Aux_1 != '') $NRF[] = $Number_Registers;
    else $NRF[] = 0;
  }
  //@@BEGIN-INE
  $i = 0;
  foreach ($Forms as $Form) {
    if ($NRF[$i] == 0) {
      $Initial_Position = 0;
      while (strpos($Content, ' \v @@BEGIN-INE-' . $Form, $Initial_Position) !== false) {
        $Initial_Position = strpos($Content, ' \v @@BEGIN-INE-' . $Form, $Initial_Position);
        while ((substr($Content, $Initial_Position, strlen('\par ')) <> '\par ') && $Initial_Position <> 0) $Initial_Position--;
        $Final_Position   = strpos($Content, ' \v @@END-INE', $Initial_Position) + strlen(' \v @@END-INE');
        $Final_Position   = strpos($Content, '\par', $Final_Position) + strlen('\par');
        $String_Aux_1     = substr($Content, $Initial_Position, $Final_Position - $Initial_Position);
        $Content          = str_replace($String_Aux_1, '', $Content);
      }
    }
    $i++;
    $Content = str_replace($Form . '\par', '', $Content);
    $Content = str_replace($Form . ' ', '', $Content);
    $Content = str_replace($Form . chr(92), chr(92), $Content);
  }
  $Content = str_replace(' \v @@BEGIN-MULTIPLE', '', $Content);
  $Content = str_replace('\v @@BEGIN-MULTIPLE', '', $Content);
  $Content = str_replace(' \v @@END-MULTIPLE', '', $Content);
  $Content = str_replace('\v @@END-MULTIPLE', '', $Content);
  $Content = str_replace(' \v @@BEGIN-INE-', '', $Content);
  $Content = str_replace('\v @@BEGIN-INE-', '', $Content);
  $Content = str_replace(' \v @@END-INE', '', $Content);
  $Content = str_replace('\v @@END-INE', '', $Content);
  $Content = str_replace(' \v', '', $Content);
  $Content = str_replace('\v', '', $Content);
  $Content = str_replace('  \pard', '\pard', $Content);
  $Keys    = array_keys($Vars);

  foreach ($Keys as $Key) {
    if (!is_array($Vars[$Key])) {
  	  $Vars[$Key] = str_replace(Chr(13) . Chr(10), '\par ', $Vars[$Key]);
      $Content = str_replace('@@' . $Key . ' ', $Vars[$Key] . ' ', $Content);
      $Content = str_replace('@@' . $Key . ';', $Vars[$Key] . ';', $Content);
      $Content = str_replace('@@' . $Key . ',', $Vars[$Key] . ',', $Content);
      $Content = str_replace('@@' . $Key . ':', $Vars[$Key] . ':', $Content);
      $Content = str_replace('@@' . $Key . '.', $Vars[$Key] . '.', $Content);
      $Content = str_replace('@@' . $Key . '-', $Vars[$Key] . '-', $Content);
      $Content = str_replace('@@' . $Key . '>', $Vars[$Key] . '>', $Content);
      $Content = str_replace('@@' . $Key . '<', $Vars[$Key] . '<', $Content);
      $Content = str_replace('@@' . $Key . '{', $Vars[$Key] . '{', $Content);
      $Content = str_replace('@@' . $Key . '}', $Vars[$Key] . '}', $Content);
      $Content = str_replace('@@' . $Key . '[', $Vars[$Key] . '[', $Content);
      $Content = str_replace('@@' . $Key . ']', $Vars[$Key] . ']', $Content);
      $Content = str_replace('@@' . $Key . '¡', $Vars[$Key] . '¡', $Content);
      $Content = str_replace('@@' . $Key . '!', $Vars[$Key] . '!', $Content);
      $Content = str_replace('@@' . $Key . '¿', $Vars[$Key] . '¿', $Content);
      $Content = str_replace('@@' . $Key . '?', $Vars[$Key] . '?', $Content);
      $Content = str_replace('@@' . $Key . '=', $Vars[$Key] . '=', $Content);
      $Content = str_replace('@@' . $Key . '(', $Vars[$Key] . '(', $Content);
      $Content = str_replace('@@' . $Key . ')', $Vars[$Key] . ')', $Content);
      $Content = str_replace('@@' . $Key . '&', $Vars[$Key] . '&', $Content);
      $Content = str_replace('@@' . $Key . '%', $Vars[$Key] . '%', $Content);
      $Content = str_replace('@@' . $Key . '$', $Vars[$Key] . '$', $Content);
      $Content = str_replace('@@' . $Key . '·', $Vars[$Key] . '·', $Content);
      $Content = str_replace('@@' . $Key . '#', $Vars[$Key] . '#', $Content);
      $Content = str_replace('@@' . $Key . '"', $Vars[$Key] . '"', $Content);
      $Content = str_replace('@@' . $Key . '@', $Vars[$Key] . '@', $Content);
      $Content = str_replace('@@' . $Key . '|', $Vars[$Key] . '|', $Content);
      $Content = str_replace('@@' . $Key . 'º', $Vars[$Key] . 'º', $Content);
      $Content = str_replace('@@' . $Key . 'ª', $Vars[$Key] . 'ª', $Content);
      $Content = str_replace('@@' . $Key . '/', $Vars[$Key] . '/', $Content);
      $Content = str_replace('@@' . $Key . '*', $Vars[$Key] . '*', $Content);
      $Content = str_replace('@@' . $Key . '+', $Vars[$Key] . '+', $Content);
      $Content = str_replace('@@' . $Key . '-', $Vars[$Key] . '-', $Content);
      $Content = str_replace('@@' . $Key . chr(39), $Vars[$Key] . chr(39), $Content);
      $Content = str_replace('@@' . $Key . chr(92), $Vars[$Key] . chr(92), $Content);
    }
  }
  $Content = $this->replaceTextWithFields2($Content, $Vars);
  fwrite($this->gp, $Content);
  fclose($this->fp);
  fclose($this->gp);

  if ($this->Fields['DYN_APPLICATION'] != '')
  	$this->UpdateGlobalXml($this->Fields['DYN_APPLICATION'], $Vars, $this->Fields['DYN_DELINDEX'] , $this->Fields['DYN_REQ_DYNAFORM']);
  exec('/usr/local/Ted/rtf2pdf.sh ' . str_replace('//', '/', $New_File) . ' ' . str_replace('//', '/', str_replace('.rtf', '.pdf', $New_File)));
}

//generar los RTF Docs asociados a una aplicacion
function getDynaformNro ($name, $proid ) {
//  $dbc = new DBConnection;

	//Verificando la existencia de Web_bot
	$zes2 = new DBSession ($this->_dbc);
	$sql = "select * from REQ_DYNAFORM WHERE REQ_FILENAME = '$name' and UID_PROCESS = '".$proid ."'";
	$dset = $zes2->Execute ( $sql );
	$row  = $dset->Read();
	If ( is_array ( $row ) ) {
		return $row ['UID'];
	}
	return 0;
}

//generar los RTF Docs asociados a una aplicacion
function makeRTFDoc ($appid, $rtfid, $rouStep) {

  global $frm;
  global $HTTP_SESSION_VARS;

  $dbc = new DBConnection;
  $this->SetTo ($dbc);

	//Verificando la existencia de Web_bot
	$zes2 = new DBSession ($dbc);
	$sql = "show tables like 'ROUTE_WEB_BOT' ";
	$dset = $zes2->Execute ( $sql );
	$rowW  = $dset->Read();

	if ( is_array ( $rowW ) ) {

	  $sql = " SELECT * FROM ROUTE_WEB_BOT ".
	  			 " WHERE RWB_PROCESS = '".$HTTP_SESSION_VARS['CURRENT_PROCESS']."' AND RWB_STEP = ".$rouStep.
	  			 " AND RWB_UID_DYNAFORM = '".$rtfid."' AND RWB_TYPE = 'RTF'";
	  $dset = $zes2->Execute ($sql);
	  $rowW2  = $dset->Read();
	}

	$this->varFields = array ();

	if ( is_array($rowW2) ) {

		G::LoadClass("webbot");

		$ses0 				= new DBSession($dbc);
		$wb  					= new Webbot( $dbc );


		$fieldsIN['DATE']= "0";
		$fieldsIN['USER_LOGGED'] = $HTTP_SESSION_VARS['USER_LOGGED'];
		$fieldsIN['USER_NAME'] = $HTTP_SESSION_VARS['USER_NAME'];
		$fieldsIN['USER_DEPARTMENT'] = $HTTP_SESSION_VARS['USER_DEPARTMENT'];
		$fieldsIN['USER_TYPE'] = $HTTP_SESSION_VARS['USER_TYPE'];
		$fieldsIN['CURRENT_APPLICATION'] = $HTTP_SESSION_VARS['CURRENT_APPLICATION'];
		$fieldsIN['CURRENT_DELEGATION'] = $HTTP_SESSION_VARS['CURRENT_DELEGATION'];
		$fieldsIN['CURRENT_PROCESS'] = $HTTP_SESSION_VARS['CURRENT_PROCESS'];
		$fieldsIN['APP_TITLE'] = $HTTP_SESSION_VARS['APP_TITLE'];
		$fieldsIN['COD_DOC_RTF'] = $rtfid;

		$fields = $this->getFieldsDefaultDynaform($appid, 0);

		$fieldsIN = G::array_merges($fields, $fieldsIN);

		$proid  = $HTTP_SESSION_VARS['CURRENT_PROCESS'];
		$delid  = $HTTP_SESSION_VARS['CURRENT_DELEGATION'];

		$FieldsBoots	= $wb->bot('RTF',$proid,$appid,$rouStep,$rtfid, $fieldsIN);
		$this->varFields['WEB_BOT'] = $FieldsBoots;
	}


  // leer datos de la tabla REQ_RTF_DOCS
  $rtf = new DBTable;
  $rtf->SetTo ($dbc, "REQ_RTF_DOCS");
  $rtf->Load ($rtfid);

  if (strpos($rtf->Fields["RTF_FILENAME"], '@@') !== false) {
    if (!is_array($fields))
      $fields = $this->getFieldsDefaultDynaform($appid, 0);
    $File4Export = str_replace("'", '', str_replace('"', '', str_replace(' ' ,'', $this->replaceTextWithFields($rtf->Fields["RTF_FILENAME"], $fields))));
  }
  else
    $File4Export = str_replace ( ' ' ,'', $rtf->Fields["RTF_FILENAME"] );
  $FileWRTF    = $rtf->Fields["RTF_FILENAME"];   //file rtf with extension ".rtf"
  $filename    = PATH_RTFDOCS . $HTTP_SESSION_VARS['CURRENT_PROCESS']."/". $FileWRTF;

  $DefDynaform = $rtf->Fields["RTF_DYNAFORM"];

  //load DEFAULT Dynaforms filename
  $rtf->SetTo ($dbc, "REQ_DYNAFORM");
  $rtf->Load ($DefDynaform);

  //Verificar que existe el archivo rtf  (template)
  if (  strlen($filename) <= 0) die ("invalid filename");
  if ( ! file_exists ( $filename) ) die ("This file $filename doesn't exist");
  if ( ! is_file ( $filename) )     die ("$filename is not a file");

  //verificar que existe directorio para upload files   //si hay error salir.
  $destination1 = SYS_UPLOAD_PATH;
  $this->makeDir ($destination1);
  $destination = $destination1. PATH_SEP. $appid . PATH_SEP;
  $continue = $this->makeDir ($destination);
  if( !$continue ) {  print "error in makedir";    die;  }

  //obtener el Uid del Default dynaforms de la aplicacion actual.
  $ses     = new DBSession ($dbc);
  $session = new DBSession ($dbc);
  $stQry = "select APP_DEF_DYNAFORM FROM APPLICATION WHERE UID = $appid ";
  $dset = $ses->Execute ($stQry);
  $row = $dset->Read();
  $defDynaformId = $row['APP_DEF_DYNAFORM'];

  //$this->tagLowerCase = 1;       //todos los nombres de variables en minusculas

  //obtener las variables de todos los dynaforms de la aplicacion actual.
  $this->varFields = $this->ArrayFromXml( $defDynaformId );

  $this->varFields['literal_today'] = $literal_today;

  //falta revisar las variables WEB_BOT ???
  $meses = array ("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
  $literal_today = date("d") . " de " . $meses[ date("m") - 1] . " del año " . date("Y");
  $this->varFields["app_uid"] = $appid;
  $this->varFields["literal_today"] = $literal_today;


	$filenew = $destination . $File4Export;
  print "<hr> <a href='$filenew'>$File4Export </a>";
  if (is_array($FieldsBoots))
  	foreach ( $FieldsBoots as $key=>$val )
    	$this->varFields[ $key ] = $val;
//echo "$filenew, $filename, $DefaultDynaform2";
  $this->createFileRTF ($filenew, $filename, $DefaultDynaform2, $HTTP_SESSION_VARS['CURRENT_PROCESS']);
	$size = filesize ($filenew);

  //registrar como documento ADICIONAL
    $doc = New DBTable;
    $doc->SetTo ($dbc, "DOCUMENT");

    //si se está regenerando el archivo obtener el UID del documento adicional
    $docid = 0;
    $stQry = "select UID FROM DOCUMENT LEFT JOIN TASK_REQ_RTF_DOCS AS T ON(TAS_REQ_RTF_DOCS = DOC_REQ_DOCUMENT) " .
         "where DOC_APPLICATION = $appid AND DOC_FILENAME = '" . $File4Export . "' " .
         " AND DOC_TYPE = 'RTF_FORM' AND T.TAS_TASK = '".$HTTP_SESSION_VARS['CURRENT_APPLICATION_TASK']."' AND DOC_REQ_DOCUMENT = '".$rtfid."'";
    $recset = $session->Execute ($stQry );

    if( $recset->Count() > 0 ){
       $row = $recset->Read();
       $docid = $row["UID"];
       $doc->Load ($docid);
       $doc->is_new = false;
    }
    else
       $doc->is_new = true;

    $docFields["UID"]             = $docid;
    $docFields["DOC_APPLICATION"] = $appid;
    $docFields["DOC_STATUS"]      = "ACTIVE";
    $docFields["DOC_TYPE"]        = "RTF_FORM";
    //$docFields["DOC_NRO"]         = $DOC_NRO;
    //$docFields["DOC_NRO2"]        = $DOC_NRO2;
    $docFields["DOC_REQ_DOCUMENT"]= $rtfid;
    $docFields["DOC_TITLE"]       = str_replace ( '.rtf', '', $File4Export);
    $docFields["DOC_FILENAME"]    = $File4Export;
    $docFields["DOC_FILESIZE"]    = $size;
    $docFields["DOC_USER"]        = $HTTP_SESSION_VARS['USER_LOGGED'];
    $docFields["DOC_SOURCE"]      = $HTTP_SESSION_VARS['USER_LOGGED'];;
    $docFields["DOC_ORIGINAL"]    = "ORIGINAL";
    $docFields["DOC_FORM"]        = "VIRTUAL";
    $docFields["DOC_LOCATION"]    = "n/a";
    $docFields["DOC_VIA_SENT"]    = "n/a";
    $docFields["DOC_RECIBED"]     = "FALSE";
    $docFields["DOC_VALIDATED"]   = "FALSE";
    $docFields["DOC_UPLOAD_DATE"] = G::CurDate();
    $docFields["DOC_VERIFIED"]    = "FALSE";
    $docFields["DOC_SIGNED"]      = "FALSE";
    $docFields["DOC_DELINDEX"]    = $HTTP_SESSION_VARS['CURRENT_DELEGATION'];

    $doc->Fields = $docFields;
    $doc->Save();

    $stQry = "select UID FROM DOCUMENT LEFT JOIN TASK_REQ_RTF_DOCS AS T ON(TAS_REQ_RTF_DOCS = DOC_REQ_DOCUMENT) " .
         "where DOC_APPLICATION = $appid AND DOC_FILENAME = '" . $File4Export . "' " .
         " AND DOC_TYPE = 'RTF_FORM' AND T.TAS_TASK = '".$HTTP_SESSION_VARS['CURRENT_APPLICATION_TASK']."' AND DOC_REQ_DOCUMENT = '".$rtfid."'";
    $recset2 = $session->Execute ($stQry );

    if( $recset2->Count() > 0 ) {
      $row2 = $recset2->Read();
    	$docid = $row2["UID"];
    }
    return $docid;
 }

 function VerifiedRequired ($filename, $Fields_DB){
		G::LoadSystem ("xmlform");
		$xml = new Xmlform;
		$xml->home = PATH_DYNAFORM;

		$fieldXmlform 	= $xml->parseXmlformToArray ($filename);
		$formulario['REQUIRED']				= array();
		$formulario['CAPTION']				= array();
		$formulario['LABEL'] 					= array();
		$formulario['NAME'] 					= array();
		$formulario['DATATYPE'] 			= array();
		$formulario['InputType']			= array();
		$requeridos = array();
		foreach($fieldXmlform as $key => $value){
			$varSearch = array_search($fieldXmlform[$key]['name'], $formulario['NAME']);
			if (!is_numeric($varSearch)) {
		  	   $varInputType 	= trim($fieldXmlform[$key]['type']);
		  	   if($varInputType == "radio"){
		  				$varInputType 			= 'radiogroup';
		  	   }
			 if( ($varInputType != 'grid') && ($varInputType != 'submit')&& ($varInputType != 'reset') && ($varInputType != 'button')
			 	&& ($varInputType != 'title')&& ($varInputType != 'subtitle')&& ($varInputType != 'caption')
			 	&& ($varInputType != 'password')&& ($varInputType != 'hidden')&& ($varInputType != 'file')
			 	&& ($varInputType != 'checkbox2')&& ($varInputType != 'linknew')&& ($varInputType != 'dateview')
			 	&& ($varInputType != 'captioncurrency')&& ($varInputType != 'captionpercentage') && ($key != 'initPHP')
			 	&& ($fieldXmlform[$key]['required'] == '1')) {
		  	    $nombre = $fieldXmlform[$key]['name'];
						$requeridos[$nombre]=$fieldXmlform[$key]['Caption'];
			 }
			}
		}
		$REQUIRED_FIELD = '';
		$CantReq = count($requeridos);
		foreach ($requeridos as $key => $value){
			if ($Fields_DB[$key] == '')
				 $REQUIRED_FIELD .= $value.' , ';
			else
				$CantReq--;
		}

		if ($REQUIRED_FIELD!=''){
			$pos = strrpos($REQUIRED_FIELD,',');
			$REQUIRED_FIELD = substr($REQUIRED_FIELD, 0, $pos);
		}

		$value = '';

		if ($CantReq == 0){
			$value = TRUE;
			return $value;		 }
		else{
			$value = $REQUIRED_FIELD;
			return $value;		 }

	}

function FormfieldRequired ($proid, $appid, $taskid, $environment){


 	  $dbc 	= new DBConnection;
  	$this->SetTo ($dbc);
 		$ses  = new DBSession ($dbc);
 		$ses2 = new DBSession ($dbc);

		$sql = " SELECT UID ".
					 " FROM `DYNAMIC_FORM` WHERE DYN_APPLICATION = ".$appid.
					 " ORDER BY UID ASC";
		$dset = $ses->Execute ($sql);
		$row3  = $dset->Read();
		$FieldsDB = '';



		G::LoadClass("interface");
		$int = new Interface;
		$int->SetTo( $dbc );


		if ($row3['UID'] != '')
							$FieldsDB = $this->ArrayFromXml( $row3['UID'] );

		$sql = " SELECT R.UID, REQ_FILENAME, REQ_TITLE, STEP_CONDITION  ".
					 " FROM TASK_REQ_DYNAFORM ".
					 " LEFT JOIN REQ_DYNAFORM AS R ON (R.UID=TAS_REQ_DYNAFORM) ".
					 " LEFT JOIN STEPS AS S ON (STEP_UID_OBJ=TAS_REQ_DYNAFORM AND STEP_TYPE_OBJ='DYNAFORM' AND STEP_PROCESS= UID_PROCESS AND STEP_TASK = TAS_TASK) ".
					 " WHERE TAS_TASK = '".$taskid."' ORDER BY TAS_DYNAFORM_POSITION ASC";
		$dset2 = $ses2->Execute ($sql);
		$row2  = $dset2->Read();

		$FormFieldReq = array();

		while(is_array($row2)){

		//print_r($row2);
			$ShowDyna	= $this->VerifiedRequired("$proid/".$row2['REQ_FILENAME'], $FieldsDB);
			$condition = 1 ;
			if ($row2['STEP_CONDITION'] != '' ){
				$condition = $int->Verify_Condition($row2['STEP_CONDITION'], $appid);
			}
			if(($ShowDyna != 1) and ($condition == 1)){
				$FormFieldReq[$row2['REQ_TITLE']]=$ShowDyna;
			}
			$row2 = $dset2->Read();
			}

			return $FormFieldReq;
	}


function DocRequired ($proid, $appid, $taskid, $environment){


 	  $dbc 	= new DBConnection;
  	$this->SetTo ($dbc);
 		$ses  = new DBSession ($dbc);
 		$ses2 = new DBSession ($dbc);

		$sql = " SELECT UID ".
					 " FROM `DYNAMIC_FORM` WHERE DYN_APPLICATION = ".$appid.
					 " ORDER BY UID ASC";
		$dset = $ses->Execute ($sql);
		$row3  = $dset->Read();
		$FieldsDB = '';

		G::LoadClass("interface");
		$int = new Interface;
		$int->SetTo( $dbc );


		if ($row3['UID'] != '')
							$FieldsDB = $this->ArrayFromXml( $row3['UID'] );

		$sql = " SELECT REQ_TITLE, REQ_DESCRIPTION, REQ_FORM_NEEDED, REQ_ORIGINAL, STEP_CONDITION   " .
           " FROM TASK_REQ_DOCUMENT LEFT JOIN REQ_DOCUMENT AS RQ ON (RQ.UID = TAS_REQ_DOCUMENT) " .
           " LEFT JOIN STEPS AS S ON (STEP_UID_OBJ=TAS_REQ_DOCUMENT AND STEP_TYPE_OBJ='DOC' AND STEP_PROCESS= UID_PROCESS AND STEP_TASK = TAS_TASK) ".
           " LEFT JOIN DOCUMENT AS D ON ( D.DOC_REQ_DOCUMENT = TAS_REQ_DOCUMENT AND DOC_APPLICATION = $appid and DOC_TYPE = 'REQUIRED' ) " .
           " WHERE  TAS_TASK = '$taskid' AND ISNULL(DOC_STATUS) AND TAS_OPTIONAL='FALSE'";
		$dset2 = $ses2->Execute ($sql);
		$row2  = $dset2->Read();

		$FormFieldReq = array();
		$i=0;
		while(is_array($row2)){
			//print"<br>";
			//print_r($row2);
			$condition = 1 ;
			if ($row2['STEP_CONDITION'] != '' ){
				$condition = $int->Verify_Condition($row2['STEP_CONDITION'], $appid);
				//print "<br>".$condition;
			}

			if($condition == 1){
				$FormFieldReq[$i]=$row2;
				$i++;
			}
			$row2 = $dset2->Read();
			}
			//print_r ($FormFieldReq);
			return $FormFieldReq;
	}


function RTFRequired ($proid, $appid, $taskid, $environment){


 	  $dbc 	= new DBConnection;
  	$this->SetTo ($dbc);
 		$ses  = new DBSession ($dbc);
 		$ses2 = new DBSession ($dbc);

		$sql = " SELECT UID ".
					 " FROM `DYNAMIC_FORM` WHERE DYN_APPLICATION = ".$appid.
					 " ORDER BY UID ASC";
		$dset = $ses->Execute ($sql);
		$row3  = $dset->Read();
		$FieldsDB = '';

		G::LoadClass("interface");
		$int = new Interface;
		$int->SetTo( $dbc );


		if ($row3['UID'] != '')
							$FieldsDB = $this->ArrayFromXml( $row3['UID'] );

		$sql = " SELECT RTF_TITLE, RTF_DESCRIPTION, RTF_FILENAME, STEP_CONDITION  " .
           " FROM TASK_REQ_RTF_DOCS LEFT JOIN REQ_RTF_DOCS AS RQ ON (RQ.UID = TAS_REQ_RTF_DOCS) " .
           "LEFT JOIN DOCUMENT AS D ON ( D.DOC_REQ_DOCUMENT = TAS_REQ_RTF_DOCS AND DOC_APPLICATION = $appid) ".
          "AND ((DOC_TYPE = 'RTF_FORM') OR (DOC_TYPE = 'PDF_FORM'))" .
           " LEFT JOIN STEPS AS S ON (STEP_UID_OBJ=TAS_REQ_RTF_DOCS AND (STEP_TYPE_OBJ='RTF'  OR STEP_TYPE_OBJ='PDF' ) AND STEP_PROCESS= UID_PROCESS AND STEP_TASK = TAS_TASK) ".
           " WHERE TAS_TASK = '$taskid' AND ISNULL(DOC_TITLE) AND TAS_OPTIONAL = 'FALSE'" ;
		$dset2 = $ses2->Execute ($sql);
		$row2  = $dset2->Read();

		$RTFReq = array();
		$i=0;
		while(is_array($row2)){
			$condition = 1 ;
			//print_r ($row2);
			if ($row2['STEP_CONDITION'] != '' ){
				$condition = $int->Verify_Condition($row2['STEP_CONDITION'], $appid);
			}
			if($condition == 1){
				$RTFReq[$i]=$row2;
				$i++;
			}
			$row2 = $dset2->Read();
			}
			return $RTFReq;
	}



function PrintXml($xmlvar){
	print "<textarea cols='100' rows='20'>";
	print_r($xmlvar);
	print "</textarea>";
}

function XmlStructur($reqDyna, $Multiple, $frm, $newfile) {
global $HTTP_SESSION_VARS;
 $delid = $HTTP_SESSION_VARS['CURRENT_DELEGATION'];
 $appid = $HTTP_SESSION_VARS['CURRENT_APPLICATION'];
 $userLogged = $HTTP_SESSION_VARS['USER_LOGGED'];
if (($Multiple == 'FALSE') || $Multiple == '') {

	$xmlFile = "";
	if ($newfile != '')
		foreach($newfile as $tag => $value){
			if ($value['name'] != ''){
				$this->uploadFile($tag,$value,$delid,$appid,$userLogged);
				$xmlFile.="<$tag>".$value['name']."</$tag>";
			}
		}
    $xmlvar = $frm;
    if (is_array($frm)) {
  	  $xmlvarkeys = array_keys($xmlvar);
  	  foreach ($xmlvarkeys as $key) {
  		  if (is_array($xmlvar[$key])) {
  			  $xmlvar1 .= '<' . $key . '>';
  			  $i = 1;
  			  foreach ($xmlvar[$key] as $key2=>$val2) {
  				  if (is_array($val2)) {
  					  $xmlvar1 .= '<' . $key2 . '>';
  					  foreach ($val2 as $key3=>$val3) {
  					  	if (is_array($val3)) {
  					  		foreach ($val3 as $key4=>$val4) {
  					  			if (is_array($val4)) {
  					  				foreach ($val4 as $key5=>$val5)
  					  				  $xmlvar1 .= '<' . $key5 . '>' . G::unhtmlentities(htmlentities(str_replace("'", "´", $val5))) . '</' . $key5 . '>';
  					  			}
  					  			else {
  					  				$xmlvar1 .= '<' . $key4 . '>' . G::unhtmlentities(htmlentities(str_replace("'", "´", $val4))) . '</' . $key4 . '>';
  					  			}
  					  		}
  					  	}
  					  	else {
  						    $xmlvar1 .= '<' . $key3 . '>' . G::unhtmlentities(htmlentities(str_replace("'", "´", $val3))) . '</' . $key3 . '>';
  						  }
  				    }
  				    $xmlvar1 .= '</' . $key2 . '>';
  				  }
  				  else {
  					  $xmlvar1 .= '<' . $key2 . '>' . G::unhtmlentities(htmlentities(str_replace("'", "´", $val2))) . '</' . $key2 . '>';
  				  }
  			  }
  			  $xmlvar1 .= '</' . $key . '>';
  		  }
  		  else {
  		  	if ((strpos($xmlvar[$key], '><') === false) && (strpos($xmlvar[$key], '</') === false))
  			    $xmlvar1 .= '<' . $key . '>' . G::unhtmlentities(htmlentities(str_replace("'", "´", $xmlvar[$key]))) . '</' . $key . '>';
  			  else
  			    $xmlvar1 .= '<' . $key . '>' . str_replace("'", "´", $xmlvar[$key]) . '</' . $key . '>';
  		  }
  	  }
  	  $xmlvar = $xmlvar1;
    }
	  /*while ( list ($key, $val) = each($frm) ) {
	    $xmlvar .= "<" . trim($key) . ">" . htmlentities(str_replace("'", "´", $val)) . "</" . trim($key) . ">";
	  }*/
	if ($xmlFile != '')
		$xmlvar .= $xmlFile;
}
else {
  global $HTTP_SESSION_VARS;
  $app_id = $HTTP_SESSION_VARS['CURRENT_APPLICATION'];
  $stQry = "select APP_DEF_DYNAFORM FROM APPLICATION WHERE UID = $app_id";
  $this->_dbses->Query( $stQry );
  $dset = new DBRecordSet( $this->_dbses->result );
  $row = $dset->Read();
  $dynaid = 0;
  if (is_array($row)) $dynaid = $row["APP_DEF_DYNAFORM"];
  $count = 1;
  $cant = 0;
	$oldFields = $this->ArrayFromXml( $dynaid );
	foreach($oldFields as $key => $value){
			if ($key == "$reqDyna")
				$cant = count($value);
	}
	$count = $count+$cant;
	$xmlFile = "";
	if ($newfile != '')
		foreach($newfile as $tag => $value){
			if ($value['name'] != ''){
				$this->uploadFile($tag,$value,$delid,$appid,$userLogged);
				$xmlFile.="<$tag>".$value['name']."</$tag>";
			}
		}

	$xmlvar = "";
	$xmlvar .= "<" . $reqDyna . ">\n" ;
	$xmlvar .= "<" . $count . ">\n" ;
	while ( list ($key, $val) = each($frm) ) {
	  $xmlvar .= "<" . trim($key) . ">" . G::unhtmlentities(htmlentities(str_replace("'", "´", $val))) . "</" . trim($key) . ">\n";
	}
	$xmlvar .= "</" . $count . ">\n" ;
	$xmlvar .= "</" . $reqDyna . ">\n";

	if ($xmlFile != '')
		$xmlvar .= $xmlFile;

  $newFields = $this->ArrayFromXmlStr ($xmlvar);
/*
  print "información antigua:";
  print_r ( $oldFields);
  print "<br> información nueva:";
  print_r ( $newFields);    */

  if (is_array($newFields)){
  	foreach ($newFields as $key=>$val) {
  	  	if (is_array($val)){
  	  		foreach ($val as $key2=>$val2) {
  	  			$oldFields[$key][$count] = $val2;
  	  		}
  	  	}else{
  	  	  	$oldFields[$key] = $val;
  		}
  	}
  }
  $xmlvar = "";
  foreach ($oldFields as $key=>$val) {
    $xmlvar .= "<$key>";
    if (is_array($val)){
    	foreach ($val as $key2=>$val2) {
    		$xmlvar .= "<$key2>";
    			foreach ($val2 as $key3=>$val3) {
    				$xmlvar .= "<$key3>";
    				$xmlvar .= $val3;
    				$xmlvar .= "</$key3>";
    			}
		$xmlvar .="</$key2>";
    	}
    }else{
    	$xmlvar .= $val;
    }
    $xmlvar .="</$key>";
  }
}

return $xmlvar;
}

function DatoMultiple($Fields,$reqDyna,$NroMultiple){
$xmlvar="";
if (is_array($Fields)){
  foreach ($Fields as $key=>$val) {
    if ($key == $reqDyna){
	    if (is_array($val)){
	    	foreach ($val as $key2=>$val2) {
	    		if ($key2 == $NroMultiple){
	    			if (is_array($val2)){
		    			foreach ($val2 as $key3=>$val3) {
		    				$xmlvar .= "<$key3>";
		    				$xmlvar .= $val3;
		    				$xmlvar .= "</$key3>";
		    			}
	    			}
	    		}
	    	}
	    }
    }
  }
}
$xmlvar = $this->ArrayFromXmlStr($xmlvar);
return $xmlvar;
}

  function normalizeForm( $proid, $frm, $vector_pre, $path, $envid ) {
    //si no hay un formulario $frm, entonces no hay nada que normalizar...
    //print_r ($frm);
    if ( !is_array ( $frm ) ) return;


    $array_otrosForm = array ();
    $replaceTo = array();

    $array_chk = array();
    $array_ond = array(); //ondemand field
    $array_chkGroup = array(); //checkgroup field

    foreach ( $vector_pre as $key => $value) {
    	if ( $value['type'] == 'checkbox' ) {
    		$array_chk[$key]= $key;
    	}
    	if ( $value['type'] == 'checkgroup' ) {
    		$array_chkGroup[$key]= $key;
    	}
    	if ( $value['type'] == 'multipleform') {
    		$array_otrosForm[$key]= $value[ size];
      }
    	if ( $value['type'] == 'ondemand') {
    		$array_ond[$key]= $key;
    	}
    	if ( $value['type'] == 'file') {
    	  $replaceTo[ $value['name'] ] = $value['replaceto'];
      }
    }

    //para revisar otros formularios
    foreach ( $array_otrosForm as $key=>$val ) {
      $filexml->home = $path;
      //$oxmlpath    = "dynaform/$envid/$proid/$val";
      //$oxmlpath    = "xmlform/$envid/$proid/$val";
      $oxmlpath    = "$proid/$val";
      $ovector_pre = $filexml->parseXmlformToArray( $oxmlpath );
      foreach ( $ovector_pre as $key => $value) {
      	if ( $value['type'] == 'checkbox'  ) {
      		$array_chk[$key]= $key;
      	}
      	if ( $value['type'] == 'checkgroup') {
      		$array_chkGroup[$key]= $key;
      	}
      	if ( $value['type'] == 'ondemand') {
      		$array_ond[$key]= $key;
        }
      }
    }

    //para desabilitar los valores de los checks cuando estos no han sido seleccionados, deben ser cereados.
    $frm_aux=$frm;

    if ($array_chk != ''){
      foreach ( $array_chk as $key2 ) {
        $flag1 = 0;
        foreach ( $frm_aux as $key => $val )
          if  ($key2==$key) $flag1=1;

        if ($flag1==0)  $frm[$key2]=0;
      }
    }

    //de todos los campos que son del tipo ondemand, si un campo especifico no tiene valor entonces se debe borrar
    //y recorrer los demas valores
    foreach ( $array_ond as $key_ond=>$val_ond ) {
    	$ind = 1;
    	$newFrm = array ();
      foreach ( $frm[$key_ond] as $key=>$val ) {
      	if ($val != '' )
      	  $newFrm[ $ind++ ] = $val;
      }
      $frm[$key_ond] = $newFrm;
    }

    //Check group, los valores iran separados por guiones....
    foreach ( $array_chkGroup as $key_grp=>$val_grp ) {
    	$ind = 1;
    	$newValue = '';
    	//si existe el valor.. entonces el formulario viene con informacion para el checkgroup...
    	//caso contrario no se toca este campo...
    	if ( is_array ( $frm[$key_grp]) )  {
        foreach ( $frm[$key_grp] as $key=>$val ) {
      	  if ($val != '' )
      	    $newValue .= $val . '-';
        }
        if ( substr ( $newValue, -1 ) == '-' ) $newValue = substr ( $newValue, 0, strlen ($newValue) -1);
        $frm[$key_grp] = $newValue;
      }
      else {
    	  $frm[$key_grp] = '';
        //var_dump ( $frm[$key_grp] ) ;
      }
    }//fin cuando es checkgroup

    return $frm;
  } //function x

  function uploadFile($titleFile,$newfile,$iddel,$appId,$Logged){
  global $replaceTo;
  global $proid;

  G::LoadClass( "document" );
  $dbc = new DBConnection;
  $doc = new Document;
  $doc->SetTo( $dbc );

	if( is_array( $newfile ) ){
	  $fnewname = $newfile['tmp_name'];
	  if ( file_exists ( $fnewname) ) {
	    $fnewsize = filesize( $fnewname );
	    $fp = fopen( $fnewname, "r" );
	    $content = fread( $fp, $fnewsize );
	    fclose( $fp );
	  }
	}

  if ($fnewsize == 0 ) {
    G::SendMessageXml ('ID_ERROR_UPLOAD_FILE', "error");
    G::header ("location: viewWidgets_New.html");
    die;
  }

  $hasReplaceTo = ( $replaceTo[ $titleFile ] != '' );
  if ( $hasReplaceTo )  {
    $filename = $replaceTo[ $titleFile ];
    $newfile['name'] = $filename;
  }
  else
    $filename = $newfile['name'];

  if ( strlen (trim($filename)) == 0 )  $filename = "@";
  $filesize = round( $fnewsize / 1024, 1 );

  $Fields["UID"]							= 0;
  $Fields["DOC_APPLICATION"]	= $appId;
  $Fields["DOC_STATUS"]     	= 'ACTIVE';
  $Fields["DOC_TYPE"]       	= "ADDITIONAL";
  $Fields["DOC_REQ_DOCUMENT"]	= -1;
  $Fields["DOC_TITLE"]      	= $titleFile;
  $Fields["DOC_FILENAME"]   	= '@';
  $Fields["DOC_FILESIZE"]   	= 0;
  $Fields["DOC_FILENAME"]    	= $filename;
  $Fields["DOC_FILESIZE"]    	= $fnewsize ;
  $Fields["DOC_USER"]       	= $Logged;
  $Fields["DOC_SOURCE"]     	= $Logged;
  $Fields["DOC_ORIGINAL"]   	= "";
  $Fields["DOC_FORM"]       	= "VIRTUAL";
  $Fields["DOC_LOCATION"]   	= "";
  $Fields["DOC_VIA_SENT"]   	= 'n/a';
  $Fields["DOC_RECIBED"]    	= 'FALSE';
  $Fields["DOC_VALIDATED"]  	= 'FALSE';
  $Fields["DOC_UPLOAD_DATE"]	= G::CurDate();
  $Fields["DOC_VERIFIED"]   	= 'FALSE';
  $Fields["DOC_SIGNED"]     	= 'FALSE';
  $Fields["DOC_DELINDEX"]   	= $iddel;

  //if this is a normal upload document, save a record in the table DOCUMENTS
  if ( ! $hasReplaceTo ) {
    $doc->Fields = $Fields;
    $doc->Save();
  }

  $dbses = new DBSession($dbc);
  $stQry = "update APPLICATION SET APP_UPDATE_DATE = '". G::CurDate() . "' where UID = $appId";
  $dbses->Execute($stQry);

  if ( $hasReplaceTo )
    $doc->AddFileToProcess( $newfile, $proid);
  else
    $doc->AddFile( $newfile, $appId);

  }
}

?>