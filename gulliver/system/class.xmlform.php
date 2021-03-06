<?php
/**
 * class.xmlform.php
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
/**
 * Class XmlForm_Field
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies Services_JSON
 */
class XmlForm_Field {
  var $name = '';
  var $type = 'field';
  var $label = '';
  var $owner;
  var $language;
  var $group = 0;
  var $mode = '';
  var $defaultValue = NULL;
  /*to change the presentation*/
  var $enableHtml = false;
  var $style = '';
  var $withoutLabel = false;
  var $className = '';
  /*attributes for paged table*/
  var $colWidth = 140;
  var $colAlign = 'left';
  var $colClassName = '';
  var $titleAlign = '';
  var $align = '';
  var $showInTable = '';
  /*Events*/
  var $onclick = '';
  /*attributes for data filtering*/
  /*dataCompareField = field to be compared with*/
  var $dataCompareField = '';
  /* $dataCompareType : '=' ,'<>' , 'like', ... , 'contains'(== ' like "%value%"')
   */
  var $dataCompareType = '=';
  var $sql = '';
  var $sqlConnection = '';
  //Attributes for PM Tables integration (only ProcessMaker for now)
  var $pmtable = '';
  var $keys = '';
  var $pmconnection = '';
  var $pmfield = '';

  /**
   * Function XmlForm_Field
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string xmlNode
   * @parameter string lang
   * @parameter string home
   * @return string
   */
  function XmlForm_Field($xmlNode, $lang = 'en', $home = '', $owner = NULL) {
    //Loads any attribute that were defined in the xmlNode
    //except name and label.
    $myAttributes = get_class_vars ( get_class ( $this ) );
    foreach ( $myAttributes as $k => $v )
      $myAttributes [$k] = strtoupper ( $k );
    //$data: Includes labels and options.
    $data = &$xmlNode->findNode ( $lang );
    @$this->label = $data->value;
    /*Loads the field attributes*/
    foreach ( $xmlNode->attributes as $k => $v ) {
      $key = array_search ( strtoupper ( $k ), $myAttributes );
      if ($key)
        eval ( '$this->' . $key . '=$v;' );
    }
    //Loads the main attributes
    $this->name = $xmlNode->name;
    $this->type = strtolower ( $xmlNode->attributes ['type'] );
    preg_match ( '/\s*([^\s][\w\W]*)?/m', $xmlNode->value, $matches );
    $this->sql = (isset ( $matches [1] )) ? $matches [1] : '';
    //List Options
    if (isset ( $data->children ))
      foreach ( $data->children as $k => $v ) {
        if ($v->type !== 'cdata')
          $this->{$v->name} [$v->attributes ["name"]] = $v->value;
      }
    $this->options = (isset ( $this->option )) ? $this->option : array ();
    //Sql Options : cause warning because values are not setted yet.
    //if ($this->sql!=='') $this->executeSQL();
    //maybe $ownerMode is not defined..
    $ownerMode = isset ( $owner->mode ) ? $owner->mode : 'edit';
    if ($this->mode === '')
      $this->mode = $ownerMode !== '' ? $ownerMode : 'edit';
  }
  function validateValue($value) {
    return isset ( $value );
  }
  private function executeXmlDB(&$owner, $row = -1) {
    if (! $this->sqlConnection)
      $dbc = new DBConnection ( );
    else {

      if (defined ( 'DB_' . $this->sqlConnection . '_USER' )) {
        if (defined ( 'DB_' . $this->sqlConnection . '_HOST' ))
          eval ( '$res[\'DBC_SERVER\'] = DB_' . $this->sqlConnection . '_HOST;' );
        else
          $res ['DBC_SERVER'] = DB_HOST;
        if (defined ( 'DB_' . $this->sqlConnection . '_USER' ))
          eval ( '$res[\'DBC_USERNAME\'] = DB_' . $this->sqlConnection . '_USER;' );
        if (defined ( 'DB_' . $this->sqlConnection . '_PASS' ))
          eval ( '$res[\'DBC_PASSWORD\'] = DB_' . $this->sqlConnection . '_PASS;' );
        else
          $res ['DBC_PASSWORD'] = DB_PASS;
        if (defined ( 'DB_' . $this->sqlConnection . '_NAME' ))
          eval ( '$res[\'DBC_DATABASE\'] = DB_' . $this->sqlConnection . '_NAME;' );
        else
          $res ['DBC_DATABASE'] = DB_NAME;
        if (defined ( 'DB_' . $this->sqlConnection . '_TYPE' ))
          eval ( '$res[\'DBC_TYPE\'] = DB_' . $this->sqlConnection . '_TYPE;' );
        else
          $res ['DBC_TYPE'] = defined ( 'DB_TYPE' ) ? DB_TYPE : 'mysql';
        $dbc = new DBConnection ( $res ['DBC_SERVER'], $res ['DBC_USERNAME'], $res ['DBC_PASSWORD'], $res ['DBC_DATABASE'], $res ['DBC_TYPE'] );
      } else {
        $dbc0 = new DBConnection ( );
        $dbs0 = new DBSession ( $dbc0 );
        $res = $dbs0->execute ( "select * from  DB_CONNECTION WHERE DBC_UID=" . $this->sqlConnection );
        $res = $res->read ();
        $dbc = new DBConnection ( $res ['DBC_SERVER'], $res ['DBC_USERNAME'], $res ['DBC_PASSWORD'], $res ['DBC_DATABASE'] );
      }
    }
    $query = G::replaceDataField ( $this->sql, $owner->values );
    $dbs = new DBSession ( $dbc );
    $res = $dbs->execute ( $query );
    $result = array ();
    while ( $row = $res->Read () ) {
      $result [] = $row;
    }
    return $result;
  }
  private function executePropel(&$owner, $row = -1) {
    if (! isset ( $owner->values [$this->name] )) {
      if ($row > - 1) {
        $owner->values [$this->name] = array ();
      } else {
        $owner->values [$this->name] = '';
      }
    }
    if (! is_array ( $owner->values [$this->name] )) {
      $query = G::replaceDataField ( $this->sql, $owner->values );
    } else {
      $aAux = array ();
      foreach ( $owner->values as $key => $data ) {
        if (is_array ( $data )) {
          $aAux [$key] = isset ( $data [$row] ) ? $data [$row] : '';
        } else {
          $aAux [$key] = $data;
        }
      }
      $query = G::replaceDataField ( $this->sql, $aAux );
    }

    $result = array ();
    if ($this->sqlConnection == 'dbarray') {
      try {
        $con = Propel::getConnection ( $this->sqlConnection );
        $stmt = $con->createStatement ();
        $rs = $con->executeQuery ( $query, ResultSet::FETCHMODE_NUM );
      }
      catch ( Exception $e ) {  //dismiss error because dbarray shouldnt be defined in some contexts.
        return $result;
      }
    }
    else {
      try {
        $con = Propel::getConnection ( $this->sqlConnection );
        $stmt = $con->createStatement ();
        $rs = $stmt->executeQuery ( $query, ResultSet::FETCHMODE_NUM );
      }
      catch  ( Exception $e ) {  //dismiss error because dbarray shouldnt be defined in some contexts.
        return $result;
      }
    }

    $rs->next ();
    $row = $rs->getRow ();
    while ( is_array ( $row ) ) {
      $result [] = $row;
      $rs->next ();
      $row = $rs->getRow ();
    }
    return $result;
  }
  /**
   * Function executeSQL
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string owner
   * @return string
   */
  function executeSQL(&$owner, $row = -1) {
    if (! isset ( $this->sql ))
      return 1;
    if ($this->sql === '')
      return 1;

    if (! $this->sqlConnection)
      $this->sqlConnection = 'workflow';

    //Read the result of the query
    if ($this->sqlConnection === "XMLDB") {
      $result = $this->executeXmlDB ( $owner, $row );
    } else {
      $result = $this->executePropel ( $owner, $row );
    }
    $this->sqlOption = array ();
    $this->options = array ();
    if (isset ( $this->option )) {
      foreach ( $this->option as $k => $v )
        $this->options [$k] = $v;
    }
    for($r = 0; $r < sizeof ( $result ); $r ++) {
      $key = reset ( $result [$r] );
      $this->sqlOption [$key] = next ( $result [$r] );
      $this->options [$key] = $this->sqlOption [$key];
    }

    if ($this->type != 'listbox') {
      if (isset ( $this->options ) && isset ( $this->owner ) && isset ( $this->owner->values [$this->name] )) {
        if ((! is_array ( $this->owner->values [$this->name] )) && ! ((is_string ( $this->owner->values [$this->name] ) || is_int ( $this->owner->values [$this->name] )) && array_key_exists ( $this->owner->values [$this->name], $this->options ))) {
          reset ( $this->options );
          $firstElement = key ( $this->options );
          if (isset ( $firstElement ))
            $this->owner->values [$this->name] = $firstElement;
          else
            $this->owner->values [$this->name] = '';
        }
      }
    }
    return 0;
  }

  function htmlentities($value, $flags = ENT_QUOTES, $encoding = 'utf-8') {
    if ($this->enableHtml)
      return $value;
    else
      return htmlentities ( $value, $flags, $encoding );
  }
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @return string
   */
  function render($value = NULL) {
    //this is an unknown field type.
    return $this->htmlentities ( $value != '' ? $value : $this->name, ENT_COMPAT, 'utf-8' );
  }
  /**
   * Function renderGrid
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string values
   * @return string
   */
  function renderGrid($values = array(), $owner = NULL, $onlyValue = false, $therow = -1) {
    $result = array ();
    $r = 1;
    foreach ( $values as $v ) {
      $result [] = $this->render ( $v, $owner, '[' . $owner->name . '][' . $r . ']', $onlyValue, $r, $therow );
      $r ++;
    }
    return $result;
  }
  function renderTable($values = '', $owner = NULL, $onlyValue = false) {
    $r = 1;
    $result = $this->render ( $values, $owner, '[' . $owner->name . '][' . $r . ']', $onlyValue );
    return $result;
  }

  /**
   * Function dependentOf
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return array
   */
  function dependentOf() {
    $fields = array ();
    if (isset ( $this->formula )) {
      preg_match_all ( "/\b[a-zA-Z][a-zA-Z_0-9]*\b/", $this->formula, $matches, PREG_PATTERN_ORDER );
      /*      if ($this->formula!=''){
        var_dump($this->formula);
        var_dump($matches);
        var_dump(array_keys($this->owner->fields));
        die;
      }*/
      foreach ( $matches [0] as $field ) {
        //if (array_key_exists( $this->owner->fields, $field ))
        $fields [] = $field;
      }
    }
    return $fields;
  }
  /**
   * Function mask
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string format
   * @parameter string value
   * @return string
   */
  function mask($format, $value) {
    $value = explode ( '', $value );
    for($j = 0; $j < strlen ( $format ); $j ++) {
      $result = '';
      $correct = TRUE;
      for($i = $j; $i < strlen ( $format ); $i ++) {
        $a = substr ( $format, $i, 1 );
        $e = $i < strlen ( $value ) ? substr ( $value, $i, 1 ) : '';
        //$e=$i<strlen($format)?substr($format, $i+1,1):'';
        switch ($a) {
          case '0' :
            if ($e === '')
              $e = '0';
          case '#' :
            if ($e === '')
              break 3;
            if (strpos ( '0123456789', $e ) !== FALSE) {
              $result .= $e;
            } else {
              $correct = FALSE;
              break 3;
            }
            break;
          case '.' :
            if ($e === '')
              break 3;
            if ($e === $a)
              break 1;
            if ($e !== $a)
              break 2;
          default :
            if ($e === '')
              break 3;
            $result .= $e;
        }
      }
    }
    if ($e !== '')
      $correct = FALSE;

    //##,###.##   --> ^...$ no parece pero no, o mejor si, donde # es \d?, en general todos
    // es valida cuando no encuentra un caracter que no deberia estar, puede no terminar la mascara
    // pero si sobran caracteres en el value entonces no se cumple la mascara.
    return $correct ? $result : $correct;
  }
  /**
   * Function getAttributes
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return string
   */
  function getAttributes() {
    $attributes = array ();
    $json = new Services_JSON ( );
    foreach ( $this as $attribute => $value ) {
      switch ($attribute) {
        case 'sql' :
        case 'sqlConnection' :
        case 'name' :
        case 'type' :
        case 'owner' :
          break;
        default :
          if (substr ( $attribute, 0, 2 ) !== 'on')
            $attributes [$attribute] = $value;
      }
    }
    if (sizeof ( $attributes ) < 1)
      return '{}';
    return $json->encode ( $attributes );
  }
  /**
   * Function getEvents
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return string
   */
  function getEvents() {
    $events = array ();
    $json = new Services_JSON ( );
    foreach ( $this as $attribute => $value ) {
      if (substr ( $attribute, 0, 2 ) === 'on')
        $events [$attribute] = $value;
    }
    if (sizeof ( $events ) < 1)
      return '{}';
    return $json->encode ( $events );
  }
  /**
   * Function attachEvents: Attaches events to a control using
   *   leimnud.event.add
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   */
  function attachEvents($elementRef) {
    $events = '';
    foreach ( $this as $attribute => $value ) {
      if (substr ( $attribute, 0, 2 ) == 'on') {
        $events = 'if (' . $elementRef . ') leimnud.event.add(' . $elementRef . ',"' . substr ( $attribute, 2 ) . '",function(){' . $value . '});' . "\n";
      }
    }
  }
  /**
   * Function createXmlNode: Creates an Xml_Node object storing
   *   the data of $this Xml_Field.
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return Xml_Node
   */
  function createXmlNode($includeDefaultValues = false) {
    /* Start Comment: Creates the corresponding XML Tag for $this
     *    object.
     */
    $attributesList = $this->getXmlAttributes ( $includeDefaultValues );
    $node = new Xml_Node ( $this->name, 'open', $this->sql, $attributesList );
    /* End Comment */
    /* Start Comment: Creates the languages nodes and options
     *   if exist.
     */
    $node->addChildNode ( new Xml_Node ( '', 'cdata', "\n" ) );
    $node->addChildNode ( new Xml_Node ( $this->language, 'open', $this->label ) );
    if (isset ( $this->option )) {
      foreach ( $this->option as $k => $v )
        $node->children [1]->addChildNode ( new Xml_Node ( 'option', 'open', $v, array ('name' => $k ) ) );
    }
    /* End Comment */
    return $node;
  }
  /**
   * Function updateXmlNode: Updates and existing Xml_Node
   *   with the data of $this Xml_Field.
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @return Xml_Node
   */
  function &updateXmlNode(&$node, $includeDefaultValues = false) {
    /* Start Comment: Modify the node's attributes and value.
     */
    $attributesList = $this->getXmlAttributes ( $includeDefaultValues );
    $node->name = $this->name;
    $node->value = $this->sql;
    $node->attributes = $attributesList;
    /* End Comment */
    /* Start Comment: Modifies the languages nodes
     */
    $langNode = & $node->findNode ( $this->language );
    $langNode->value = $this->label;
    if (isset ( $this->option )) {
      $langNode->children = array ();
      foreach ( $this->option as $k => $v )
        $langNode->addChildNode ( new Xml_Node ( 'option', 'open', $v, array ('name' => $k ) ) );
    }
    /* End Comment */
    return $node;
  }
  /**
   * Function getXmlAttributes: Returns an associative array
   *   with the attributes of $this Xml_field (only the modified ones).
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter boolean includeDefaultValues  Includes attributes
   *   with default values.
   * @return Xml_Node
   */
  function getXmlAttributes($includeDefaultValues = false) {
    $attributesList = array ();
    $class = get_class ( $this );
    $default = new $class ( new Xml_Node ( 'DEFAULT', 'open', '', array ('type' => $this->type ) ) );
    foreach ( $this as $k => $v ) {
      switch ($k) {
        case 'owner' :
        case 'name' :
        case 'type' :
        case 'language' :
        case 'sql' :
          break;
        default :
          if (($v !== $default->{$k}) || $includeDefaultValues)
            $attributesList [$k] = $v;
      }
    }
    return $attributesList;
  }
  /* Used in Form::validatePost
   */
  function maskValue($value, &$owner) {
    return $value;
  }
  /*Close this object*/
  function cloneObject() {
    //return unserialize( serialize( $this ) );//con este cambio los formularios ya no funcionan en php4
    return clone ($this);
  }

  function getPMTableValue($oOwner) {
    $sValue = '';
    if (isset($oOwner->fields[$this->pmconnection])) {
      if (defined('PATH_CORE')) {
        if (file_exists(PATH_CORE . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'AdditionalTables.php')) {
          require_once PATH_CORE . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'AdditionalTables.php';
          $oAdditionalTables = new AdditionalTables();
          try {
            $aData = $oAdditionalTables->load($oOwner->fields[$this->pmconnection]->pmtable, true);
          }
          catch (Exception $oError) {
            $aData = array('FIELDS' => array());
          }
          $aKeys   = array();
          $aValues = explode('|', $oOwner->fields[$this->pmconnection]->keys);
          $i       = 0;
          foreach ($aData['FIELDS'] as $aField) {
            if ($aField['FLD_KEY'] == '1') {
              $aKeys[$aField['FLD_NAME']] = (isset($aValues[$i]) ? G::replaceDataField($aValues[$i], $oOwner->values) : '');
              $i++;
            }
          }
          try {
            $aData = $oAdditionalTables->getDataTable($oOwner->fields[$this->pmconnection]->pmtable, $aKeys);
          }
          catch (Exception $oError) {
            $aData = array();
          }
          if (isset($aData[$this->pmfield])) {
            $sValue = $aData[$this->pmfield];
          }
        }
      }
    }
    return $sValue;
  }
}
/**
 * Class XmlForm_Field_Title
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field
 */
class XmlForm_Field_Title extends XmlForm_Field {
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @return string
   */
  function render($value = NULL, &$owner) {
    $this->label = G::replaceDataField ( $this->label, $owner->values );
    return '<span id=\'form[' . $this->name . ']\' name=\'form[' . $this->name . ']\' >' . $this->htmlentities ( $this->label ) . '</span>';
  }
  /* A title node has no value
   */
  function validateValue($value) {
    return false;
  }
}
/**
 * Class XmlForm_Field_Subtitle
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field
 */
class XmlForm_Field_Subtitle extends XmlForm_Field {
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @return string
   */
  function render($value = NULL) {
    return '<span id=\'form[' . $this->name . ']\' name=\'form[' . $this->name . ']\' >' . $this->htmlentities ( $this->label ) . '</span>';
  }
  /* A subtitle node has no value
   */
  function validateValue($value) {
    return false;
  }
}
/**
 * Class XmlForm_Field_SimpleText
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field
 */
class XmlForm_Field_SimpleText extends XmlForm_Field {
  var $size = 15;
  var $maxLength = '';
  var $validate = 'Any';
  var $mask = '';
  /* Additional events */
  var $onkeypress = '';
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @return string
   */
  function render($value = NULL, &$owner) {
    if (($this->pmconnection != '') && ($this->pmfield != '')) {
      $value = $this->getPMTableValue($owner);
    }
    $onkeypress = G::replaceDataField ( $this->onkeypress, $owner->values );
    if ($this->mode === 'edit') {
      if ($this->readOnly)
        return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" ' . (isset ( $this->maxLength ) ? ' maxlength="' . $this->maxLength . '"' : '') . ' value=\'' . htmlentities ( $value, ENT_COMPAT, 'utf-8' ) . '\' readOnly="readOnly" style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>';
      else
        return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" ' . (isset ( $this->maxLength ) ? ' maxlength="' . $this->maxLength . '"' : '') . ' value=\'' . htmlentities ( $value, ENT_COMPAT, 'utf-8' ) . '\' style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>';
    } elseif ($this->mode === 'view') {
      return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" ' . (isset ( $this->maxLength ) ? ' maxlength="' . $this->maxLength . '"' : '') . ' value=\'' . htmlentities ( $value, ENT_COMPAT, 'utf-8' ) . '\' style="display:none;' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>' . htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    } else {
      return $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    }
  }
  /**
   * Function renderGrid
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string values
   * @parameter string owner
   * @return string
   */
  function renderGrid($values = array(), $owner) {
    $result = array ();
    $r = 1;
    foreach ( $values as $v ) {
      if ($this->mode === 'edit') {
        if ($this->readOnly)
          $result [] = '<input class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . htmlentities ( $v, ENT_COMPAT, 'utf-8' ) . '\' readOnly="readOnly" style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '"/>';
        else
          $result [] = '<input class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . htmlentities ( $v, ENT_COMPAT, 'utf-8' ) . '\' style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '"/>';
      } elseif ($this->mode === 'view') {
        $result [] = '<input class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . htmlentities ( $v, ENT_COMPAT, 'utf-8' ) . '\' style="display:none;' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '"/>' . htmlentities ( $v, ENT_COMPAT, 'utf-8' );
      } else {
        $result [] = $this->htmlentities ( $v, ENT_COMPAT, 'utf-8' );
      }
      $r ++;
    }
    return $result;
  }
}
/**
 * Class XmlForm_Field_Text
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field_SimpleText
 */
class XmlForm_Field_Text extends XmlForm_Field_SimpleText {
  var $size = 15;
  var $maxLength = 64;
  var $validate = 'Any';
  var $mask = '';
  var $defaultValue = '';
  var $required = false;
  var $dependentFields = '';
  var $linkField = '';
  //Possible values:(-|UPPER|LOWER|CAPITALIZE)
  var $strTo = '';
  var $readOnly = false;
  var $sqlConnection = 0;
  var $sql = '';
  var $sqlOption = array ();
  //Attributes only for grids
  var $formula = '';
  var $function = '';
  var $replaceTags = 0;
  var $hints_message;
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @parameter string owner
   * @return string
   */
  function render($value = NULL, $owner = NULL) {
    if (($this->pmconnection != '') && ($this->pmfield != '')) {
      $value = $this->getPMTableValue($owner);
    }
    else {
      $this->executeSQL ( $owner );
      $firstElement = key ( $this->sqlOption );
      if (isset ( $firstElement ))
        $value = $firstElement;
    }

    //NOTE: string functions must be in G class
    if ($this->strTo === 'UPPER')
      $value = strtoupper ( $value );
    if ($this->strTo === 'LOWER')
      $value = strtolower ( $value );
      //if ($this->strTo==='CAPITALIZE') $value = strtocapitalize($value);
    $onkeypress = G::replaceDataField ( $this->onkeypress, $owner->values );
    if ($this->replaceTags == 1) {
      $value = G::replaceDataField ( $value, $owner->values );
    }
    if ($this->mode === 'edit') {
      if ($this->readOnly)
        return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_QUOTES, 'utf-8' ) . '\' readOnly="readOnly" style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>';
      else {
	 	 						
        $html = '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_QUOTES, 'utf-8' ) . '\' style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>';
        
        if($this->hints_message){
      		 $html .= '<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \''.$this->hints_message.'\');return false;">
	 	    					<image src="/images/help4.gif" width="15" height="15" border="0"/>
	 	 						</a>';
	 	 		}
        
        return $html;
      }
    } elseif ($this->mode === 'view') {
      return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_QUOTES, 'utf-8' ) . '\' style="display:none;' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>' . $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    } else {
      return $this->htmlentities ( $value, ENT_QUOTES, 'utf-8' );
    }
  }
  /**
   * Function renderGrid
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string values
   * @parameter string owner
   * @return string
   */
  function renderGrid($values = array(), $owner) {
    $result = $aux = array ();
    $r = 1;
    foreach ( $values as $v ) {
      $this->executeSQL ( $owner, $r );
      $firstElement = key ( $this->sqlOption );
      if (isset ( $firstElement ))
        $v = $firstElement;
      if ($this->replaceTags == 1) {
        $v = G::replaceDataField ( $v, $owner->values );
      }
      $aux [$r] = $v;
      if ($this->mode === 'edit') {
        if ($this->readOnly)
          $result [] = '<input class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value="' . $this->htmlentities ( $v, ENT_COMPAT, 'utf-8' ) . '" readOnly="readOnly" style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '"/>';
        else
          $result [] = '<input class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value="' . $this->htmlentities ( $v, ENT_COMPAT, 'utf-8' ) . '" style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '"/>';
      } elseif ($this->mode === 'view') {
        $result [] = '<input class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value="' . $this->htmlentities ( $v, ENT_COMPAT, 'utf-8' ) . '" style="display:none;' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '"/>' . htmlentities ( $v, ENT_COMPAT, 'utf-8' );
      } else {
        $result [] = $this->htmlentities ( $v, ENT_COMPAT, 'utf-8' );
      }
      $r ++;
    }
    $this->options = $aux;
    return $result;
  }

  function renderTable($values = '', $owner) {
    $result = $this->htmlentities ( $values, ENT_COMPAT, 'utf-8' );
    return $result;
  }

}

/**
 * Class XmlForm_Field_Suggest
 * @author Erik Amaru Ortiz <erik@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field_SimpleText
 */
class XmlForm_Field_Suggest extends XmlForm_Field_SimpleText //by neyek
{
  var $size = 15;
  var $maxLength = 64;
  var $validate = 'Any';
  var $mask = '';
  var $defaultValue = '';
  var $required = false;
  var $dependentFields = '';
  var $linkField = '';
  //Possible values:(-|UPPER|LOWER|CAPITALIZE)
  var $strTo = '';
  var $readOnly = false;
  var $sqlConnection = 0;
  var $sql = '';
  var $sqlOption = array ();
  //Atributes only for grids
  var $formula = '';
  var $function = '';
  var $replaceTags = 0;

  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @parameter string owner
   * @return string
   */
  function render($value = NULL, $owner = NULL) {

    //NOTE: string functions must be in G class
    if ($this->strTo === 'UPPER')
      $value = strtoupper ( $value );
    if ($this->strTo === 'LOWER')
      $value = strtolower ( $value );
      //if ($this->strTo==='CAPITALIZE') $value = strtocapitalize($value);
    $onkeypress = G::replaceDataField ( $this->onkeypress, $owner->values );
    if ($this->replaceTags == 1) {
      $value = G::replaceDataField ( $value, $owner->values );
    }
    if ($this->mode === 'edit') {
      if ($this->readOnly) {
        return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' ) . '\' readOnly="readOnly" style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>';
      } else {
        $str = '
                        <input class="module_app_input___gray" type="hidden" id="form[' . $this->name . ']" value="" disabled size=2 />
                        <input class="module_app_input___gray" type="text" id="form[' . $this->name . '_suggest]" value="" />
                        <script type="text/javascript">
                            var options = {
                                    script:"suggest.php?json=true&limit=6&",
                                    varname:"input",
                                    json:true,
                                    shownoresults:false,
                                    maxresults:6,
                                    callback: function (obj) {
                                      document.getElementById(\'form[' . $this->name . ']\').value = obj.id;
                                    }
                            };
                            var as_json = new bsn.AutoSuggest(\'form[' . $this->name . '_suggest]\', options);
                        </script>';

        return $str;
        //return '<input class="module_app_input___gray" id="form['.$this->name.']" name="form['.$this->name.']" type ="text" size="'.$this->size.'" maxlength="'.$this->maxLength.'" value=\''.$this->htmlentities( $value , ENT_COMPAT, 'utf-8').'\' style="'.htmlentities( $this->style , ENT_COMPAT, 'utf-8').'" onkeypress="'.htmlentities( $onkeypress , ENT_COMPAT, 'utf-8').'"/>';
      }
    } elseif ($this->mode === 'view') {
      return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' ) . '\' style="display:none;' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>' . $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    } else {
      return $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    }
  }
  /**
   * Function renderGrid
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string values
   * @parameter string owner
   * @return string
   */
  function renderGrid($values = array(), $owner) {
    $result = array ();
    $r = 1;
    foreach ( $values as $v ) {
      if ($this->replaceTags == 1) {
        $v = G::replaceDataField ( $v, $owner->values );
      }
      if ($this->mode === 'edit') {
        if ($this->readOnly)
          $result [] = '<input class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value="' . $this->htmlentities ( $v, ENT_COMPAT, 'utf-8' ) . '" readOnly="readOnly" style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '"/>';
        else
          $result [] = '<input class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value="' . $this->htmlentities ( $v, ENT_COMPAT, 'utf-8' ) . '" style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '"/>';
      } elseif ($this->mode === 'view') {
        $result [] = $this->htmlentities ( $v, ENT_COMPAT, 'utf-8' );
      } else {
        $result [] = $this->htmlentities ( $v, ENT_COMPAT, 'utf-8' );
      }
      $r ++;
    }
    return $result;

  }

  function renderTable($values = '', $owner) {
    $result = $this->htmlentities ( $values, ENT_COMPAT, 'utf-8' );
    return $result;
  }

}

/*DEPRECATED*/
class XmlForm_Field_Caption extends XmlForm_Field {
  var $sqlConnection = 0;
  var $sql = '';
  var $sqlOption = array ();
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @return string
   */
  function render($value = NULL) {
    return $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
  }
}
/**
 * Class XmlForm_Field_Password
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field
 */
class XmlForm_Field_Password extends XmlForm_Field {
  var $size = 15;
  var $maxLength = 15;
  var $required = false;
  var $readOnly = false;
  var $hints_message;
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @return string
   */
  function render($value = NULL) {
    if ($this->mode === 'edit') {
      if ($this->readOnly)
        return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="password" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' ) . '\' readOnly="readOnly"/>';
      else{
      	   $html='<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="password" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' ) . '\'/>';
      	   if($this->hints_message){
      		 $html .= '<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \''.$this->hints_message.'\');return false;">
	 	    					<image src="/images/help4.gif" width="15" height="15" border="0"/>
	 	 						</a>';
	 	 		    }
         return $html;
        }
    } elseif ($this->mode === 'view') {
      return $this->htmlentities ( str_repeat ( '*', 10 ), ENT_COMPAT, 'utf-8' );
    } else {
      return $this->htmlentities ( str_repeat ( '*', 10 ), ENT_COMPAT, 'utf-8' );
    }
  }
}
/**
 * Class XmlForm_Field_Textarea
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field
 */
class XmlForm_Field_Textarea extends XmlForm_Field {
  var $rows = 12;
  var $cols = 40;
  var $required = false;
  var $readOnly = false;
  var $wrap = 'OFF';
  var $hints_message='';
  var $className;
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @return string
   */
  function render($value = NULL, $owner) {
    if (($this->pmconnection != '') && ($this->pmfield != '')) {
      $value = $this->getPMTableValue($owner);
    }
    else {
      $this->executeSQL ( $owner );
      if (isset ( $this->sqlOption ))
        $firstElement = key ( $this->sqlOption );
      if (isset ( $firstElement ))
        $value = $firstElement;
    }
    
    $className = isset($this->className) ?  $this->className : 'module_app_input___gray';
    
    if ($this->mode === 'edit') {
      if ($this->readOnly)
        return '<textarea id="form[' . $this->name . ']" name="form[' . $this->name . ']" wrap="hard" cols="' . $this->cols . '" rows="' . $this->rows . '" style="' . $this->style . '" wrap="' . htmlentities ( $this->wrap, ENT_QUOTES, 'UTF-8' ) . '" class=" ' . $className . '" readOnly>' . $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' ) . '</textarea>';
      else{
        $html = '<textarea id="form[' . $this->name . ']" name="form[' . $this->name . ']" wrap="hard" cols="' . $this->cols . '" rows="' . $this->rows . '" style="' . $this->style . '" wrap="' . htmlentities ( $this->wrap, ENT_QUOTES, 'UTF-8' ) . '" class=" ' . $className . '" >' . $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' ) . '</textarea>';
          if($this->hints_message){
      		 $html .= '<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \''.$this->hints_message.'\');return false;">
	 	    					<image src="/images/help4.gif" width="15" height="15" border="0"/>
	 	 						</a>';
	 	 		  }        
        return $html;
      }
    } elseif ($this->mode === 'view') {
      return '<textarea id="form[' . $this->name . ']" name="form[' . $this->name . ']" wrap="hard" cols="' . $this->cols . '" rows="' . $this->rows . '" readOnly style="border:0px;backgroud-color:inherit;' . $this->style . '" wrap="' . htmlentities ( $this->wrap, ENT_QUOTES, 'UTF-8' ) . '"  class="FormTextArea" >' . $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' ) . '</textarea>';
    } else {
      return '<textarea id="form[' . $this->name . ']" name="form[' . $this->name . ']" wrap="hard" cols="' . $this->cols . '" rows="' . $this->rows . '" style="' . $this->style . '" wrap="' . htmlentities ( $this->wrap, ENT_QUOTES, 'UTF-8' ) . '"  class="module_app_input___gray" >' . $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' ) . '</textarea>';
    }
  }
  /**
   * Function renderGrid
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @parameter string owner
   * @return string
   */
  function renderGrid($values = NULL, $owner) {
    $result = array ();
    $r = 1;
    foreach ( $values as $v ) {
      if ($this->mode === 'edit') {
        if ($this->readOnly)
          $result [] = '<textarea class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']"  wrap="hard" rows="' . $this->rows . '"cols="'.$this->cols.'" readOnly="readOnly">'.$this->htmlentities ( $v, ENT_COMPAT, 'utf-8' ).'</textarea>';
        else
          $result [] = '<textarea class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']"  wrap="hard" rows="' . $this->rows . '"cols="'.$this->cols.'" >'.$this->htmlentities ( $v, ENT_COMPAT, 'utf-8' ).'</textarea>';
      } elseif ($this->mode === 'view') {
        if (stristr ( $_SERVER ['HTTP_USER_AGENT'], 'iPhone' )) {
          //$result[] = '<div style="overflow:hidden;height:25px;padding:0px;margin:0px;">'.$this->htmlentities( $v , ENT_COMPAT, 'utf-8').'</div>';
          $result [] = $this->htmlentities ( $v, ENT_COMPAT, 'utf-8' );
        } else {
          //$result[] = '<div style="overflow:hidden;width:inherit;height:2em;padding:0px;margin:0px;">'.$this->htmlentities( $v , ENT_COMPAT, 'utf-8').'</div>';
          $result [] = $this->htmlentities ( $v, ENT_COMPAT, 'utf-8' );
        }

      } else {
        $result [] = $this->htmlentities ( $v, ENT_COMPAT, 'utf-8' );
      }
      $r ++;
    }
    return $result;
  }
}
/**
 * Class XmlForm_Field_Currency
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field_SimpleText
 */
class XmlForm_Field_Currency extends XmlForm_Field_SimpleText {
  var $group = 0;
  var $size = 15;
  var $required = false;
  var $linkField = '';
  var $readOnly = false;
  var $maxLength = 15;

  var $mask = '_###,###,###,###;###,###,###,###.##';
  var $currency = '$';
  //Atributes only for grids
  var $formula = '';
  var $function = '';
  var $hints_message;
  
  function render( $value = NULL, $owner = NULL) {
  	$onkeypress = G::replaceDataField ( $this->onkeypress, $owner->values );
    
    if ($this->mode === 'edit') {
      if ($this->readOnly)
        return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_QUOTES, 'utf-8' ) . '\' readOnly="readOnly" style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>';
      else {
	 	 						
        $html = '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_QUOTES, 'utf-8' ) . '\' style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>';
        
        if($this->hints_message){
      		 $html .= '<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \''.$this->hints_message.'\');return false;">
	 	    					<image src="/images/help4.gif" width="15" height="15" border="0"/>
	 	 						</a>';
	 	 		}
        
        return $html;
      }
    } elseif ($this->mode === 'view') {
      return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_QUOTES, 'utf-8' ) . '\' style="display:none;' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>' . $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    } else {
      return $this->htmlentities ( $value, ENT_QUOTES, 'utf-8' );
    }

  	
}
  //function render( $value = NULL )
//{
//  return '<input id="form['.$this->name.']" name="form['.$this->name.']" type=\'text\' size="'.$this->size.'" value=\''. $this->htmlentities($value, ENT_QUOTES, 'utf-8') .'\'>';
//}
//function renderGrid( $values=array() , $owner )
//{
//  $result=array();$r=1;
//  foreach($values as $v)  {
//    if ($this->mode==='edit') {
//      if ($this->readOnly)
//        $result[] = '<input id="form['. $owner->name .']['.$r.']['.$this->name.']" name="form['. $owner->name .']['.$r.']['.$this->name.']" type ="text" size="'.$this->size.'" maxlength="'.$this->maxLength.'" value=\''.$this->htmlentities( $v , ENT_COMPAT, 'utf-8').'\' readOnly="readOnly"/>';
//      else
//        $result[] = '<input id="form['. $owner->name .']['.$r.']['.$this->name.']" name="form['. $owner->name .']['.$r.']['.$this->name.']" type ="text" size="'.$this->size.'" maxlength="'.$this->maxLength.'" value=\''.$this->htmlentities( $v , ENT_COMPAT, 'utf-8').'\' />';
//    } elseif ($this->mode==='view') {
//        $result[] = $this->htmlentities( $value , ENT_COMPAT, 'utf-8');
//    } else {
//        $result[] = $this->htmlentities( $value , ENT_COMPAT, 'utf-8');
//    }
//    $r++;
//  }
//  return $result;
//}
}

/*DEPRECATED*/
class XmlForm_Field_CaptionCurrency extends XmlForm_Field {
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @return string
   */
  function render($value = NULL) {
    return '$ ' . $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
  }
}
/**
 * Class XmlForm_Field_Percentage
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field_SimpleText
 */
class XmlForm_Field_Percentage extends XmlForm_Field_SimpleText {
  var $size = 15;
  var $required = false;
  var $linkField = '';
  var $readOnly = false;
  var $maxLength = 15;
  var $mask = '###.## %';
  //Atributes only for grids
  var $formula = '';
  var $function = '';
  var $hints_message;
  
   
  function render( $value = NULL, $owner = NULL) {
  	$onkeypress = G::replaceDataField ( $this->onkeypress, $owner->values );
    
    if ($this->mode === 'edit') {
      if ($this->readOnly)
        return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_QUOTES, 'utf-8' ) . '\' readOnly="readOnly" style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>';
      else {
	 	 						
        $html = '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_QUOTES, 'utf-8' ) . '\' style="' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>';
        
        if($this->hints_message){
      		 $html .= '<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \''.$this->hints_message.'\');return false;">
	 	    					<image src="/images/help4.gif" width="15" height="15" border="0"/>
	 	 						</a>';
	 	 		}
        
        return $html;
      }
    } elseif ($this->mode === 'view') {
      return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities ( $value, ENT_QUOTES, 'utf-8' ) . '\' style="display:none;' . htmlentities ( $this->style, ENT_COMPAT, 'utf-8' ) . '" onkeypress="' . htmlentities ( $onkeypress, ENT_COMPAT, 'utf-8' ) . '"/>' . $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    } else {
      return $this->htmlentities ( $value, ENT_QUOTES, 'utf-8' );
    }

  	
}
  
  //function render( $value = NULL )
//{
//  return '<input id="form['.$this->name.']" name="form['.$this->name.']" type=\'text\' value=\''. $value .'\'>';
//}
//function renderGrid( $values=array() , $owner )
//{
//  $result=array();$r=1;
//  foreach($values as $v)  {
//    if ($this->mode==='edit') {
//      if ($this->readOnly)
//        $result[] = '<input id="form['. $owner->name .']['.$r.']['.$this->name.']" name="form['. $owner->name .']['.$r.']['.$this->name.']" type ="text" size="'.$this->size.'" maxlength="'.$this->maxLength.'" value=\''.$this->htmlentities( $v , ENT_COMPAT, 'utf-8').'\' readOnly="readOnly"/>';
//      else
//        $result[] = '<input id="form['. $owner->name .']['.$r.']['.$this->name.']" name="form['. $owner->name .']['.$r.']['.$this->name.']" type ="text" size="'.$this->size.'" maxlength="'.$this->maxLength.'" value=\''.$this->htmlentities( $v , ENT_COMPAT, 'utf-8').'\' />';
//    } elseif ($this->mode==='view') {
//        $result[] = $this->htmlentities( $value , ENT_COMPAT, 'utf-8');
//    } else {
//        $result[] = $this->htmlentities( $value , ENT_COMPAT, 'utf-8');
//    }
//    $r++;
//  }
//  return $result;
//}
}

/*DEPRECATED*/
class XmlForm_Field_CaptionPercentage extends XmlForm_Field {
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @return string
   */
  function render($value = NULL) {
    return $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
  }
}
/**
 * Class XmlForm_Field_Date
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field_SimpleText
 */
class XmlForm_Field_Date2 extends XmlForm_Field_SimpleText {
  //Instead of size --> startDate
  var $startDate = '';
  //Instead of maxLength --> endDate
  var $endDate = '';
  //for dinamically dates,   beforeDate << currentDate << afterDate
  // beforeDate='1y' means one year before,  beforeDate='3m' means 3 months before
  // afterDate='5y' means five year after,  afterDate='15d' means 15 days after
  // startDate and endDate have priority over beforeDate and AfterDate.
  var $afterDate = '';
  var $beforeDate = '';
  var $defaultValue = NULL;
  var $format = 'Y-m-d';
  var $required = false;
  var $readOnly = false;
  var $mask = 'yyyy-mm-dd';
  var $dependentFields = '';

  function verifyDateFormat($date) {
    $aux = explode ( '-', $date );
    if (count ( $aux ) != 3)
      return false;
    if (! (is_numeric ( $aux [0] ) && is_numeric ( $aux [1] ) && is_numeric ( $aux [2] )))
      return false;
    if ($aux [0] < 1900 || $aux [0] > 2100)
      return false;
    return true;
  }

  function isvalidBeforeFormat($date) {
    $part1 = substr ( $date, 0, strlen ( $date ) - 1 );
    $part2 = substr ( $date, strlen ( $date ) - 1 );
    if ($part2 != 'd' && $part2 != 'm' && $part2 != 'y')
      return false;
    if (! is_numeric ( $part1 ))
      return false;
    return true;
  }

  function calculateBeforeFormat($date, $sign) {
    $part1 = $sign * substr ( $date, 0, strlen ( $date ) - 1 );
    $part2 = substr ( $date, strlen ( $date ) - 1 );
    switch ($part2) {
      case 'd' :
        $res = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ) + $part1, date ( 'Y' ) ) );
        break;
      case 'm' :
        $res = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ) + $part1, date ( 'd' ), date ( 'Y' ) ) );
        break;
      case 'y' :
        $res = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ), date ( 'Y' ) + $part1 ) );
        break;

    }
    return $res;
  }

  function render($value = NULL, $owner = NULL) {

    $value = G::replaceDataField ( $value, $owner->values );
    $startDate = G::replaceDataField ( $this->startDate, $owner->values );
    $endDate = G::replaceDataField ( $this->endDate, $owner->values );
    $beforeDate = G::replaceDataField ( $this->beforeDate, $owner->values );
    $afterDate = G::replaceDataField ( $this->afterDate, $owner->values );
    //for backward compatibility size and maxlength
    if ($startDate != '') {
      if (! $this->verifyDateFormat ( $startDate ))
        $startDate = '';
    }
    if (isset ( $beforeDate ) && $beforeDate != '') {
      if ($this->isvalidBeforeFormat ( $beforeDate ))
        $startDate = $this->calculateBeforeFormat ( $beforeDate, - 1 );
    }

    if ($startDate == '' && isset ( $this->size ) && is_numeric ( $this->size ) && $this->size >= 1900 && $this->size <= 2100) {
      $startDate = $this->size . '-01-01';
    }

    if ($startDate == '') {
      $startDate = date ( 'Y-m-d' ); // the default is the current date
    }

    //for backward compatibility maxlength
    //if ( $this->endDate == '')   $this->finalYear = date('Y') + 8;
    //for backward compatibility size and maxlength
    if ($endDate != '') {
      if (! $this->verifyDateFormat ( $endDate ))
        $endDate = '';
    }

    if (isset ( $afterDate ) && $afterDate != '') {
      if ($this->isvalidBeforeFormat ( $afterDate ))
        $endDate = $this->calculateBeforeFormat ( $afterDate, + 1 );
    }

    if (isset ( $this->maxlength ) && is_numeric ( $this->maxlength ) && $this->maxlength >= 1900 && $this->maxlength <= 2100) {
      $endDate = $this->maxlength . '-01-01';
    }
    if ($endDate == '') {
      //$this->endDate = mktime ( 0,0,0,date('m'),date('d'),date('y') );  // the default is the current date + 2 years
      $endDate = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ), date ( 'Y' ) + 2 ) ); // the default is the current date + 2 years
    }
    if ($value == '') {
      $value = date ( 'Y-m-d' );
    }
    $html = "<input type='hidden' id='form[" . $this->name . "]' name='form[" . $this->name . "]' value='" . $value . "'>";
    $html .= "<span id='span[" . $owner->id . "][" . $this->name . "]' name='span[" . $owner->id . "][" . $this->name . "]' style='border:1;border-color:#000;width:100px;'>" . $value . " </span> ";
    if ($this->mode == 'edit')
      $html .= "<a href='#' onclick=\"showDatePicker(event,'" . $owner->id . "', '" . $this->name . "', '" . $value . "', '" . $startDate . "', '" . $endDate . "'); return false;\" ><img src='/controls/cal.gif' border='0'></a>";
    return $html;
  }

  function renderGrid($values = NULL, $owner = NULL, $onlyValue = false) {
    $result = array ();
    $r = 1;
    foreach ( $values as $v ) {
      $v = G::replaceDataField ( $v, $owner->values );
      $startDate = G::replaceDataField ( $this->startDate, $owner->values );
      $endDate = G::replaceDataField ( $this->endDate, $owner->values );
      $beforeDate = G::replaceDataField ( $this->beforeDate, $owner->values );
      $afterDate = G::replaceDataField ( $this->afterDate, $owner->values );
      //for backward compatibility size and maxlength
      if ($startDate != '') {
        if (! $this->verifyDateFormat ( $startDate ))
          $startDate = '';
      }
      if ($startDate == '' && isset ( $beforeDate ) && $beforeDate != '') {
        if ($this->isvalidBeforeFormat ( $beforeDate ))
          $startDate = $this->calculateBeforeFormat ( $beforeDate, - 1 );
      }

      if ($startDate == '' && isset ( $this->size ) && is_numeric ( $this->size ) && $this->size >= 1900 && $this->size <= 2100) {
        $startDate = $this->size . '-01-01';
      }

      if ($startDate == '') {
        $startDate = date ( 'Y-m-d' ); // the default is the current date
      }

      //for backward compatibility maxlength
      //if ( $this->endDate == '')   $this->finalYear = date('Y') + 8;
      //for backward compatibility size and maxlength
      if ($endDate != '') {
        if (! $this->verifyDateFormat ( $endDate ))
          $endDate = '';
      }

      if ($endDate == '' && isset ( $afterDate ) && $afterDate != '') {
        if ($this->isvalidBeforeFormat ( $afterDate ))
          $endDate = $this->calculateBeforeFormat ( $afterDate, + 1 );
      }

      if ($endDate == '' && isset ( $this->maxlength ) && is_numeric ( $this->maxlength ) && $this->maxlength >= 1900 && $this->maxlength <= 2100) {
        $endDate = $this->maxlength . '-01-01';
      }
      if ($endDate == '') {
        //$this->endDate = mktime ( 0,0,0,date('m'),date('d'),date('y') );  // the default is the current date + 2 years
        $endDate = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ), date ( 'Y' ) + 2 ) ); // the default is the current date + 2 years
      }
      if ($v == '') {
        $v = date ( 'Y-m-d' );
      }
      if (! $onlyValue) {
        $html = "<input type='hidden' id='form[" . $owner->name . '][' . $r . '][' . $this->name . "]' name='form[" . $owner->name . '][' . $r . '][' . $this->name . "]' value='" . $v . "'>";
        if (isset ( $owner->owner->id )) {
          $html .= "<span id='span[" . $owner->owner->id . "][" . $owner->name . '][' . $r . '][' . $this->name . "]' name='span[" . $owner->owner->id . "][" . $owner->name . '][' . $r . '][' . $this->name . "]' style='border:1;border-color:#000;width:100px;'>" . $v . " </span> ";
        } else {
          $html .= "<span id='span[" . $owner->id . "][" . $owner->name . '][' . $r . '][' . $this->name . "]' name='span[" . $owner->id . "][" . $owner->name . '][' . $r . '][' . $this->name . "]' style='border:1;border-color:#000;width:100px;'>" . $v . " </span> ";
        }
        if ($this->mode == 'edit') {
          $html .= "<a href='#' onclick=\"showDatePicker(event,'" . (isset ( $owner->owner ) ? $owner->owner->id : $owner->id) . "', '" . $owner->name . '][' . $r . '][' . $this->name . "', '" . $v . "', '" . $startDate . "', '" . $endDate . "'); return false;\" ><img src='/controls/cal.gif' border='0'></a>";
        }
      } else {
        $html = $v;
      }
      $result [] = $html;
      $r ++;
    }
    return $result;
  }
}

/*DEPRECATED*/
class XmlForm_Field_DateView extends XmlForm_Field {
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @return string
   */
  function render($value = NULL) {
    return $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
  }
}
/**
 * Class XmlForm_Field_YesNo
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field
 */
class XmlForm_Field_YesNo extends XmlForm_Field {
  var $required = false;
  var $readonly = false;
  var $hints_message;
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @return string
   */
  function render($value = NULL, $owner = NULL) {
    if (($this->pmconnection != '') && ($this->pmfield != '')) {
      $value = $this->getPMTableValue($owner);
    }
    $html = ($this->mode == 'view' ? ($value === '0' ? 'NO' : 'YES') : '') . '<select id="form[' . $this->name . ']" name="form[' . $this->name . ']"' . ($this->mode == 'view' ? ' style="display:none;"' : '') . '>';
    $html .= '<option value="' . '0' . '"' . ($value === '0' ? ' selected' : '') . '>' . 'NO' . '</input>';
    $html .= '<option value="' . '1' . '"' . ($value === '1' ? ' selected' : '') . '>' . 'YES' . '</input>';
    $html .= '</select>';
    
    if($this->hints_message){
      		 $html .= '<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \''.$this->hints_message.'\');return false;">
	 	    					<image src="/images/help4.gif" width="15" height="15" border="0"/>
	 	 						</a>';
	 	}        
    
    return $html;
  }

  function renderGrid($values = array(), $owner) {
    $result = array ();
    $r = 1;
    foreach ( $values as $v ) {
      $html = ($this->mode == 'view' ? ($v === '0' ? 'NO' : 'YES') : '') . '<select id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']"' . ($this->mode == 'view' ? ' style="display:none;"' : '') . '>';
      $html .= '<option value="' . '0' . '"' . ($v === '0' ? ' selected' : '') . '>' . 'NO' . '</input>';
      $html .= '<option value="' . '1' . '"' . ($v === '1' ? ' selected' : '') . '>' . 'YES' . '</input>';
      $html .= '</select>';
      $result [] = $html;
      $r ++;
    }
    return $result;
  }
}
/**
 * Class XmlForm_Field_Link
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field
 */
class XmlForm_Field_Link extends XmlForm_Field {
  //Instead of var --> link
  var $link = '';
  var $value = '';
  var $target = '';
  var $colClassName = 'RowLink';
  var $hints_message;
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @return string
   */
  function render($value = NULL, $owner = NULL) {
    $onclick = G::replaceDataField ( $this->onclick, $owner->values );
    $link = G::replaceDataField ( $this->link, $owner->values );
    $target = G::replaceDataField ( $this->target, $owner->values );
    $value = G::replaceDataField ( $this->value, $owner->values );
    $label = G::replaceDataField ( $this->label, $owner->values );
    $html = '<a class="tableOption" href=\'' . $this->htmlentities ( $link, ENT_QUOTES, 'utf-8' ) . '\'' . 'id="form[' . $this->name . ']" name="form[' . $this->name . ']"' . (($this->onclick) ? ' onclick="' . htmlentities ( $onclick, ENT_QUOTES, 'utf-8' ) . '"' : '') . (($this->target) ? ' target="' . htmlentities ( $target, ENT_QUOTES, 'utf-8' ) . '"' : '') . '>' . $this->htmlentities ( $this->value === '' ? $label : $value, ENT_QUOTES, 'utf-8' ) . '</a>';

    if($this->hints_message){
      		 $html .= '<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \''.$this->hints_message.'\');return false;">
	 	    					<image src="/images/help4.gif" width="15" height="15" border="0"/>
	 	 						</a>';
 		}
    
    return $html;
  }

  function renderGrid($values = array(), $owner = NULL) {
    $result = array ();
    $r = 1;
    foreach ( $values as $v ) {
      $_aData_ = (isset($owner->values[$owner->name][$r]) ? $owner->values[$owner->name][$r] : array());
      $onclick = G::replaceDataField ( $this->onclick, $_aData_ );
      $link = G::replaceDataField ( $this->link, $_aData_ );
      $target = G::replaceDataField ( $this->target, $_aData_ );
      $value = G::replaceDataField ( $this->value, $_aData_ );
      $label = G::replaceDataField ( $this->label, $_aData_ );
      $html = '<a class="tableOption" href=\'' . $this->htmlentities ( $link, ENT_QUOTES, 'utf-8' ) . '\'' . 'id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']"' . (($this->onclick) ? ' onclick="' . htmlentities ( $onclick, ENT_QUOTES, 'utf-8' ) . '"' : '') . (($this->target) ? ' target="' . htmlentities ( $target, ENT_QUOTES, 'utf-8' ) . '"' : '') . '>' . $this->htmlentities ( $this->value === '' ? $label : $value, ENT_QUOTES, 'utf-8' ) . '</a>';
      $result [] = $html;
      $r ++;
    }
    return $result;
  }

  function renderTable($value = NULL, $owner = NULL) {
    $onclick = $this->htmlentities ( G::replaceDataField ( $this->onclick, $owner->values ), ENT_QUOTES, 'utf-8' );
    $link = $this->htmlentities ( G::replaceDataField ( $this->link, $owner->values ), ENT_QUOTES, 'utf-8' );
    $target = G::replaceDataField ( $this->target, $owner->values );
    $value = G::replaceDataField ( $this->value, $owner->values );
    $label = G::replaceDataField ( $this->label, $owner->values );
    $aLabel = $this->htmlentities ( $this->value === '' ? $label : $value, ENT_QUOTES, 'utf-8' );
    return '<a class="tableOption" href=\'' . $link . '\'' . (($this->onclick) ? ' onclick="' . $onclick . '"' : '') . (($this->target) ? ' target="' . htmlentities ( $target, ENT_QUOTES, 'utf-8' ) . '"' : '') . '>' . $aLabel . '</a>';
  }
}
/**
 * Class XmlForm_Field_File
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field
 */
class XmlForm_Field_File extends XmlForm_Field {
  var $required = false;
  var $input    = '';
  var $hints_message;
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @return string
   */
  function render($value = NULL) {
    $mode = ($this->mode == 'view') ? ' disabled="disabled"' : '';
    
    $html = '<input class="module_app_input___gray_file" ' . $mode . 'id="form[' . $this->name . ']" name="form[' . $this->name . ']" type=\'file\' value=\'' . $value . '\'/>';
    
    if( isset($this->input) && $this->input != ''){
      require_once 'classes/model/InputDocument.php';
      $oiDoc = new InputDocument;
      $aDoc = $oiDoc->load($this->input);
      $aDoc['INP_DOC_TITLE'] = isset($aDoc['INP_DOC_TITLE'])? $aDoc['INP_DOC_TITLE']: '';
      $html .= '<label><img src="/images/inputdocument.gif" width="22px" width="22px"/><font size="1">('.trim($aDoc['INP_DOC_TITLE']).')</font></label>';
    }
     
    if($this->hints_message){
      		 $html .= '<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \''.$this->hints_message.'\');return false;">
	 	    					<image src="/images/help4.gif" width="15" height="15" border="0"/>
	 	 				   </a>';
 	}
    return $html;
  }
}
/*
//DEPRECATED
class XmlForm_Field_BasicForm extends XmlForm_Field
{
  //Instead of size --> dynaform
  var $dynaform='';
  var $times=1;
  //Possible values: "[ADD][,DELETE][,EDIT]"
  var $configurationgrid2='';
  function render( $value = NULL )
  {
    return $this->htmlentities( $value ,ENT_COMPAT,'utf-8').' this is a basicform';
  }
}

class XmlForm_Field_BasicFormView extends XmlForm_Field
{
  //Instead of size --> dynaform
  var $dynaform;
  var $times=1;
  var $required=false;
  function render( $value = NULL )
  {
    return $this->htmlentities( $value ,ENT_COMPAT,'utf-8');
  }
}
*/

/*hook, dropdown field for Propel table @By <erik@colosa.com>*/
class XmlForm_Field_Dropdownpt extends XmlForm_Field {
  var $value;
  
  function render($value = NULL, $owner = NULL) {
      $this->value = $value;
    
  	  //G::pr($this->value); die;
  	  $id = $this->value->id;
	  $value = isset($this->value->value)? $this->value->value: '';
	  $items = $this->value->items;
  
  	  $res = '<select id="form['.$id.']" name="form['.$this->name.']" class="module_app_input___gray"><option value="0"></option>';
      foreach($items as $k=>$v) {
          $res .= '<option value="'.$k.'">'.$v.'</option>';
      }
      $res .= "</select>";
      return $res;
  }

  /* Used in Form::validatePost
   */
  function maskValue($value, &$owner) {
    return ($value === $this->value) ? $value : $this->falseValue;
  }
}

/*hook, checkbox field for Propel table @By <erik@colosa.com>*/
class XmlForm_Field_Checkboxpt extends XmlForm_Field {
  var $required = false;
  var $value = 'on';
  var $falseValue = 'off';
  var $labelOnRight = true;

  function render($value = NULL, $owner = NULL) {
   if (($this->pmconnection != '') && ($this->pmfield != '')) {
      $value = $this->getPMTableValue($owner);
    }
    $checked = (isset ( $value ) && ($value == $this->value)) ? 'checked' : '';
    $res = "<input id='form[" . $this->name . "][{$this->value}]' value='{$this->value}' name='form[" . $this->name . "][{$this->value}]' type='checkbox' />";
    return $res;
  }

  function renderGrid($values = array(), $owner) {
    $result = array ();
    $r = 1;
    foreach ( $values as $v ) {
      $checked = (($v == $this->value) ? 'checked="checked"' : '');
      $disabled = (($this->value == 'view') ? 'disabled="disabled"' : '');
      $html = $res = "<input id='form[" . $owner->name . "][" . $r . "][" . $this->name . "]' value='{$this->value}' name='form[" . $owner->name . "][" . $r . "][" . $this->name . "]' type='checkbox' $checked $disabled />";
      $result [] = $html;
      $r ++;
    }
    return $result;
  }
  /* Used in Form::validatePost
   */
  function maskValue($value, &$owner) {
    return ($value === $this->value) ? $value : $this->falseValue;
  }
}

class XmlForm_Field_Checkbox extends XmlForm_Field {
  var $required = false;
  var $value = 'on';
  var $falseValue = 'off';
  var $labelOnRight = true;
  var $hints_message;
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @return string
   */
  function render($value = NULL, $owner = NULL) {
    if (($this->pmconnection != '') && ($this->pmfield != '')) {
      $value = $this->getPMTableValue($owner);
    }
    $checked = (isset ( $value ) && ($value == $this->value)) ? 'checked' : '';
    if ($this->mode === 'edit') {
      $readOnly = isset ( $this->readOnly ) && $this->readOnly ? 'disabled' : '';
      if ($this->labelOnRight) {
        $res = "<input id='form[" . $this->name . "]' value='{$this->value}' name='form[" . $this->name . "]' type='checkbox' $checked $readOnly ><span class='FormCheck'>" . $this->label . '</span></input>';
      } else {
        $res = "<input id='form[" . $this->name . "]' value='{$this->value}' name='form[" . $this->name . "]' type='checkbox' $checked $readOnly />";
      }
      if($this->hints_message){
      	     $res .= '<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \''.$this->hints_message.'\');return false;">
	 	    		 <image src="/images/help4.gif" width="15" height="15" border="0"/>
	 	 				 </a>';
        	 	 }
      
      //      $res = "<input id='form[" . $this->name . "]' value='" . $this->name . "' name='form[" .$this->name . "]' type='checkbox' $checked $readOnly >" . $this->label ;
      return $res;
    } elseif ($this->mode === 'view') {
      if ($this->labelOnRight) {
        $html = "<input id='form[" . $this->name . "]' value='{$this->value}' name='form[" . $this->name . "]' type='checkbox' '. $checked disabled><span class='FormCheck'>" . $this->label . '</span></input>';
      } else {
        $html = "<input id='form[" . $this->name . "]' value='{$this->value}' name='form[" . $this->name . "]' type='checkbox' '. $checked disabled/>";
      }
      if($this->hints_message){
      		 $html .= '<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \''.$this->hints_message.'\');return false;">
	 	    					<image src="/images/help4.gif" width="15" height="15" border="0"/>
	 	 						</a>';
 		  }
      return $html;
      
     } else {
      return $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    }
  }

  function renderGrid($values = array(), $owner) {
    $result = array ();
    $r = 1;
    foreach ( $values as $v ) {
      $checked = (($v == $this->value) ? 'checked="checked"' : '');
      $disabled = (($this->value == 'view') ? 'disabled="disabled"' : '');
      $html = $res = "<input id='form[" . $owner->name . "][" . $r . "][" . $this->name . "]' value='{$this->value}' name='form[" . $owner->name . "][" . $r . "][" . $this->name . "]' type='checkbox' $checked $disabled />";
      $result [] = $html;
      $r ++;
    }
    return $result;
  }
  /* Used in Form::validatePost
   */
  function maskValue($value, &$owner) {
    return ($value === $this->value) ? $value : $this->falseValue;
  }
}

/*DEPRECATED*/
class XmlForm_Field_Checkbox2 extends XmlForm_Field {
  var $required = false;
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @return string
   */
  function render($value = NULL) {
    return '<input class="FormCheck" name="' . $this->name . '" type ="checkbox" disabled>' . $this->label . '</input>';
  }
}
/**
 * Class XmlForm_Field_Button
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field
 */
class XmlForm_Field_Button extends XmlForm_Field {
  var $onclick = '';
  var $align = 'center';
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @return string
   */
  function render($value = NULL, $owner = NULL) {
    $onclick = G::replaceDataField ( $this->onclick, $owner->values );
    $label = G::replaceDataField ( $this->label, $owner->values );
    if ($this->mode === 'edit') {
      $re = "<input style=\"{$this->style}\" class='module_app_button___gray {$this->className}' id=\"form[{$this->name}]\" name=\"form[{$this->name}]\" type='button' value=\"{$label}\" " . (($this->onclick) ? 'onclick="' . htmlentities ( $onclick, ENT_COMPAT, 'utf-8' ) . '"' : '') . " />";
      return $re;
    } elseif ($this->mode === 'view') {
      return "<input style=\"{$this->style}\" disabled='disabled' class='module_app_button___gray module_app_buttonDisabled___gray {$this->className}' id=\"form[{$this->name}]\" name=\"form[{$this->name}]\" type='button' value=\"{$label}\" " . (($this->onclick) ? 'onclick="' . htmlentities ( $onclick, ENT_COMPAT, 'utf-8' ) . '"' : '') . " />";
    } else {
      return $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    }
  }
}
/**
 * Class XmlForm_Field_Reset
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field
 */
class XmlForm_Field_Reset extends XmlForm_Field {
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @return string
   */
  function render($value = NULL, $owner) {
    $onclick = G::replaceDataField ( $this->onclick, $owner->values );
    $mode = ($this->mode == 'view') ? ' disabled="disabled"' : '';
    //return '<input name="'.$this->name.'" type ="reset" value="'.$this->label.'"/>';
    return "<input style=\"{$this->style}\" $mode class='module_app_button___gray {$this->className}' id=\"form[{$this->name}]\" name=\"form[{$this->name}]\" type='reset' value=\"{$this->label}\" " . (($this->onclick) ? 'onclick="' . htmlentities ( $onclick, ENT_COMPAT, 'utf-8' ) . '"' : '') . " />";
  }
}
/**
 * Class XmlForm_Field_Submit
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field
 */
class XmlForm_Field_Submit extends XmlForm_Field {
  var $onclick = '';
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @return string
   */
  function render($value = NULL, $owner) {
    $onclick = G::replaceDataField ( $this->onclick, $owner->values );
    if ($this->mode === 'edit') {
      //      if ($this->readOnly)
      //        return '<input id="form['.$this->name.']" name="form['.$this->name.']" type=\'submit\' value=\''. $this->label .'\' disabled/>';
      return "<input style=\"{$this->style}\" class='module_app_button___gray {$this->className}' id=\"form[{$this->name}]\" name=\"form[{$this->name}]\" type='submit' value=\"{$this->label}\" " . (($this->onclick) ? 'onclick="' . htmlentities ( $onclick, ENT_COMPAT, 'utf-8' ) . '"' : '') . " />";
    } elseif ($this->mode === 'view') {
      return "<input style=\"{$this->style}\" disabled='disabled' class='module_app_button___gray module_app_buttonDisabled___gray {$this->className}' id=\"form[{$this->name}]\" name=\"form[{$this->name}]\" type='submit' value=\"{$this->label}\" " . (($this->onclick) ? 'onclick="' . htmlentities ( $onclick, ENT_COMPAT, 'utf-8' ) . '"' : '') . " />";
    } else {
      return $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    }
  }
}
/**
 * Class XmlForm_Field_Hidden
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field
 */
class XmlForm_Field_Hidden extends XmlForm_Field {
  var $sqlConnection = 0;
  var $sql = '';
  var $sqlOption = array ();
  var $dependentFields = '';
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @parameter string owner
   * @return string
   */
  function render($value = NULL, $owner) {
    if (($this->pmconnection != '') && ($this->pmfield != '')) {
      $value = $this->getPMTableValue($owner);
    }
    else {
      $this->executeSQL ( $owner );

      if (isset ( $this->sqlOption )) {
        reset ( $this->sqlOption );
        $firstElement = key ( $this->sqlOption );
        if (isset ( $firstElement ))
          $value = $firstElement;
      }
    }
    if ($this->mode === 'edit') {
      return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type=\'hidden\' value=\'' . $value . '\'/>';
    } elseif ($this->mode === 'view') {
      return '<input class="module_app_input___gray" id="form[' . $this->name . ']" name="form[' . $this->name . ']" type=\'submit\' value=\'' . $value . '\'/>';
    } else {
      return $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    }
  }

  function renderGrid($values = NULL, $owner) {
    $result = array ();
    $r = 1;
    foreach ( $values as $v ) {
       $result [] = '<input type="hidden" value="'.$this->htmlentities ( $v, ENT_COMPAT, 'utf-8' ).'" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" />';
    }
    $r ++;

    return $result;
  }
}
/**
 * Class XmlForm_Field_Dropdown
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field
 */
class XmlForm_Field_Dropdown extends XmlForm_Field {
  var $defaultValue = '';
  var $required = false;
  var $dependentFields = '';
  var $readonly = false;
  var $option = array ();
  var $sqlConnection = 0;
  var $sql = '';
  var $sqlOption = array ();
  var $saveLabel = 0;
  var $hints_message;
  function validateValue($value, &$owner) {
    /*$this->executeSQL( $owner );
    return isset($value) && ( array_key_exists( $value , $this->options ) );*/
    return true;
  }
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @parameter string owner
   * @return string
   */
  function render($value = NULL, $owner = NULL, $rowId = '', $onlyValue = false, $row = -1, $therow = -1) {
    if (($this->pmconnection != '') && ($this->pmfield != '')) {
      $value = $this->getPMTableValue($owner);
    }
    if ($therow == - 1) {
      $this->executeSQL ( $owner, $row );
    } else {
      if ($row == $therow) {
        $this->executeSQL ( $owner, $row );
      }
    }
    $html = '';
    if (! $onlyValue) {
      if ($this->mode === 'edit') {
        $html = '<select class="module_app_input___gray" id="form' . $rowId . '[' . $this->name . ']" name="form' . $rowId . '[' . $this->name . ']" ' . (($this->style) ? 'style="' . $this->style . '"' : '') . '>';
      } elseif ($this->mode === 'view') {
        $html = $this->htmlentities ( isset ( $this->options [$value] ) ? $this->options [$value] : '', ENT_COMPAT, 'utf-8' );
        $html .= '<select class="module_app_input___gray" id="form' . $rowId . '[' . $this->name . ']" name="form' . $rowId . '[' . $this->name . ']" style="display:none" ' . (($this->style) ? 'style="' . $this->style . '"' : '') . '>';
      }
      foreach ( $this->option as $optionName => $option ) {
        $html .= '<option value="' . $optionName . '" ' . ($optionName == $value ? 'selected="selected"' : '') . '>' . $option . '</option>';
      }
      foreach ( $this->sqlOption as $optionName => $option ) {
        $html .= '<option value="' . $optionName . '" ' . ($optionName == $value ? 'selected="selected"' : '') . '>' . $option . '</option>';
      }
      $html .= '</select>';
    } else {
      foreach ( $this->option as $optionName => $option ) {
        if ($optionName == $value) {
          $html = $option;
        }
      }
      foreach ( $this->sqlOption as $optionName => $option ) {
        if ($optionName == $value) {
          $html = $option;
        }
      }
    }
    if($this->hints_message){
      		 $html .= '<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \''.$this->hints_message.'\');return false;">
	 	    					<image src="/images/help4.gif" width="15" height="15" border="0"/>
	 	 						</a>';
	 	 		}        
    return $html;
  }
}
/**
 * Class XmlForm_Field_Listbox
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field
 */
class XmlForm_Field_Listbox extends XmlForm_Field {
  var $defaultValue = '';
  var $required = false;
  var $option = array ();
  var $sqlConnection = 0;
  var $size = 4;
  var $sql = '';
  var $sqlOption = array ();
  var $hints_message;
  function validateValue($value, $owner) {
    $this->executeSQL ( $owner );
    return true; // isset($value) && ( array_key_exists( $value , $this->options ) );
  }
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @parameter string owner
   * @return string
   */
  function render($value = NULL, $owner = NULL) {
    if (($this->pmconnection != '') && ($this->pmfield != '')) {
      $value = $this->getPMTableValue($owner);
    }
    $this->executeSQL ( $owner );
    if (! is_array ( $value ))
      $value = explode ( '|', $value );
    if ($this->mode === 'edit') {
      $html = '<select multiple="multiple" id="form[' . $this->name . ']" name="form[' . $this->name . '][]" size="' . $this->size . '">';
      foreach ( $this->option as $optionName => $option ) {
        $html .= '<option value="' . $optionName . '" ' . ((in_array ( $optionName, $value )) ? 'selected' : '') . '>' . $option . '</option>';
      }
      foreach ( $this->sqlOption as $optionName => $option ) {
        $html .= '<option value="' . $optionName . '" ' . ((in_array ( $optionName, $value )) ? 'selected' : '') . '>' . $option . '</option>';
      }     
      $html .= '</select>';
      
      if($this->hints_message){
      		 $html .= '<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \''.$this->hints_message.'\');return false;">
	 	    					<image src="/images/help4.gif" width="15" height="15" border="0"/>
	 	 						</a>';
	 	  }        
      
      return $html;
    } elseif ($this->mode === 'view') {
      $html = '<select multiple id="form[' . $this->name . ']" name="form[' . $this->name . '][]" size="' . $this->size . '" disabled>';
      foreach ( $this->option as $optionName => $option ) {
        $html .= '<option value="' . $optionName . '" ' . ((in_array ( $optionName, $value )) ? 'selected' : '') . '>' . $option . '</option>';
      }
      foreach ( $this->sqlOption as $optionName => $option ) {
        $html .= '<option value="' . $optionName . '" ' . ((in_array ( $optionName, $value )) ? 'selected' : '') . '>' . $option . '</option>';
      }
      $html .= '</select>';
      return $html;
    } else {
      return $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    }
  }
  function renderGrid($value = NULL, $owner = NULL) {
    return $this->render ( $value, $owner );
  }
}
/**
 * Class XmlForm_Field_RadioGroup
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field
 */
class XmlForm_Field_RadioGroup extends XmlForm_Field {
  var $defaultValue = '';
  var $required = false;
  var $option = array ();
  var $sqlConnection = 0;
  var $sql = '';
  var $sqlOption = array ();
  var $hints_message;
  function validateValue($value, $owner) {
    $this->executeSQL ( $owner );
    return isset ( $value ) && (array_key_exists ( $value, $this->options ));
  }
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @parameter string owner
   * @return string
   */
  function render($value = NULL, $owner) {
    if (($this->pmconnection != '') && ($this->pmfield != '')) {
      $value = $this->getPMTableValue($owner);
    }
    $this->executeSQL ( $owner );
    if ($this->mode === 'edit') {
      $html = '';
      $i=0;
      foreach ( $this->options as $optionName => $option ) {
        $html .= '<input id="form[' . $this->name . '][' . $optionName . ']" name="form[' . $this->name . ']" type=\'radio\' value="' . $optionName . '" ' . (($optionName == $value) ? ' checked' : '') . '><span class="FormCheck">' . $option . '</span></input>';
        if(++$i==count($this->options)){
             if($this->hints_message){
      	     $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \''.$this->hints_message.'\');return false;">
	 	    		 <image src="/images/help4.gif" width="15" height="15" border="0"/>
	 	 				 </a>';
        	 	 }
        	
        	}
         
        $html .='<br>';
        
      }
      return $html;
    } elseif ($this->mode === 'view') {
      $html = '';
      foreach ( $this->options as $optionName => $option ) {
        $html .= '<input class="module_app_input___gray" id="form[' . $this->name . '][' . $optionName . ']" name="form[' . $this->name . ']" type=\'radio\' value="' . $optionName . '" ' . (($optionName == $value) ? 'checked' : '') . ' disabled><span class="FormCheck">' . $option . '</span></input><br>';
      }
      return $html;
    } else {
      return $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    }
  }
}

/*DEPRECATED*/
class XmlForm_Field_RadioGroupView extends XmlForm_Field {
  var $defaultValue = '';
  var $required = false;
  var $option = array ();
  var $sqlConnection = 0;
  var $sql = '';
  var $sqlOption = array ();
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @parameter string owner
   * @return string
   */
  function render($value = NULL, $owner = NULL) {
    $this->executeSQL ( $owner );
    $html = '';
    foreach ( $this->option as $optionName => $option ) {
      $html .= '<input type=\'radio\'`disabled/><span class="FormCheck">' . $option . '</span><br>';
    }
    return $html;
  }
}

/**
 * Class XmlForm_Field_CheckGroup
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field
 */
class XmlForm_Field_CheckGroup extends XmlForm_Field {
  var $required = false;
  var $option = array ();
  var $sqlConnection = 0;
  var $sql = '';
  var $sqlOption = array ();
  var $hints_message;
  /*function validateValue( $value , $owner )
  {
    $this->executeSQL( $owner );
    return isset($value) && ( array_key_exists( $value , $this->options ) );
  }*/
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @parameter string owner
   * @return string
   */
  function render($value = NULL, $owner = NULL) {
    if (($this->pmconnection != '') && ($this->pmfield != '')) {
      $value = $this->getPMTableValue($owner);
    }
    $this->executeSQL ( $owner );
    if (! is_array ( $value ))
      $value = explode ( '|', $value );
    if ($this->mode === 'edit') {
    	$i=0;
      $html = '';
      foreach ( $this->options as $optionName => $option ) {
        $html .= '<input id="form[' . $this->name . '][' . $optionName . ']" name="form[' . $this->name . '][]" type=\'checkbox\' value="' . $optionName . '"' . (in_array ( $optionName, $value ) ? 'checked' : '') . '><span class="FormCheck">' . $option . '</span></input>';
        if(++$i==count($this->options)){
             if($this->hints_message){
      	     	 $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \''.$this->hints_message.'\');return false;">
	 	         					<image src="/images/help4.gif" width="15" height="15" border="0"/>
	 	 		     				</a>';
 	 	 		     }
        	}                
        $html .= '<br>';
      }//fin for
      return $html;
    } elseif ($this->mode === 'view') {
      $html = '';
      foreach ( $this->options as $optionName => $option ) {
        $html .= '<input class="FormCheck" id="form[' . $this->name . '][' . $optionName . ']" name="form[' . $this->name . '][]" type=\'checkbox\' value="' . $optionName . '"' . (in_array ( $optionName, $value ) ? 'checked' : '') . ' disabled><span class="FormCheck">' . $option . '</span></input><br>';
      }
      return $html;
    } else {
      return $this->htmlentities ( $value, ENT_COMPAT, 'utf-8' );
    }

  }
}

/* TODO: DEPRECATED */
class XmlForm_Field_CheckGroupView extends XmlForm_Field {
  var $option = array ();
  var $sqlConnection = 0;
  var $sql = '';
  var $sqlOption = array ();
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @return string
   */
  function render($value = NULL) {
    $html = '';
    foreach ( $this->option as $optionName => $option ) {
      $html .= '<input type=\'checkbox\' disabled/><span class="FormCheck">' . $option . '</span><br>';
    }
    return $html;
  }
}
/**
 * Class XmlForm_Field_Grid
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field  XmlForm  xmlformTemplate
 */
class XmlForm_Field_Grid extends XmlForm_Field {
  var $xmlGrid = '';
  var $initRows = 1;
  var $group = 0;
  var $addRow = "1";
  var $deleteRow = "1";
  var $editRow = "0";
  var $sql = '';
  //TODO: 0=doesn't excecute the query, 1=Only the first time, 2=Allways
  var $fillType = 0;
  var $fields = array ();
  var $scriptURL;
  var $id = '';
  /**
   * Function XmlForm_Field_Grid
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string xmlnode
   * @parameter string language
   * @parameter string home
   * @return string
   */
  function XmlForm_Field_Grid($xmlnode, $language, $home) {
    parent::XmlForm_Field ( $xmlnode, $language );
    $this->parseFile ( $home, $language );
  }
  /**
   * Function parseFile
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string home
   * @parameter string language
   * @return string
   */
  function parseFile($home, $language) {
    if (file_exists ( $home . $this->xmlGrid . '.xml' )) {
      $this->xmlform = new XmlForm ( );
      $this->xmlform->home = $home;
      $this->xmlform->parseFile ( $this->xmlGrid . '.xml', $language, false );
      $this->fields = $this->xmlform->fields;
      $this->scriptURL = $this->xmlform->scriptURL;
      $this->id = $this->xmlform->id;
      unset ( $this->xmlform );
    }
  }
  function render($values, $owner = NULL) {
    $arrayKeys = array_keys ( $this->fields );
    $emptyRow = array ();
    foreach ( $arrayKeys as $key )
      $emptyRow [$key] = array ('' );
    return $this->renderGrid ( $emptyRow, $owner );
  }
  /**
   * Function renderGrid
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string values
   * @return string
   */
  function renderGrid($values, $owner = NULL, $therow = -1) {
    $this->id = $this->owner->id . $this->name;

      $using_template = "grid";

	### added by Erik <erik@colosa.com>
	### For throw the preview view
	if( isset($this->owner->visual_frontend) ){
	 $using_template = "grid_{$this->owner->visual_frontend}";
	}

	if( $this->mode == 'view' ){
	 $using_template = "grid_view";
	}

    $tpl = new xmlformTemplate ( $this, PATH_CORE . "templates/{$using_template}.html" );
    if (! isset ( $values ) || ! is_array ( $values ) || sizeof ( $values ) == 0) {
      $values = array_keys ( $this->fields );
    }
    $aValuekeys = array_keys ( $values );
    if (count ( $aValuekeys ) > 0 && ( int ) $aValuekeys [0] == 1)
      $values = $this->flipValues ( $values );
    $this->rows = count ( reset ( $values ) );
    if (isset ( $owner->values )) {
      foreach ( $owner->values as $key => $value ) {
        if (! isset ( $values [$key] )) {
          $values [$key] = array ();
          //for($r=0; $r < $this->rows ; $r++ ) {
          $values [$key] = $value;
          //}
        }
      }
    }
    foreach ( $this->fields as $k => $v ) {
      if (isset ( $values ['SYS_GRID_AGGREGATE_' . $this->name . '_' . $k] )) {
        $this->fields [$k]->aggregate = $values ['SYS_GRID_AGGREGATE_' . $this->name . '_' . $k];
      } else {
        $this->fields [$k]->aggregate = '0';
      }
    }
    $this->values = $values;
    $this->NewLabel = G::LoadTranslation('ID_NEW');
    $this->DeleteLabel = G::LoadTranslation('ID_DELETE');

    $tpl->template = $tpl->printTemplate ( $this );
    //In the header
    $oHeadPublisher = & headPublisher::getSingleton ();
    $oHeadPublisher->addScriptFile ( $this->scriptURL );
    $oHeadPublisher->addScriptCode ( $tpl->printJavaScript ( $this ) );
    return $tpl->printObject ( $this, $therow );
  }
  function flipValues($values) {
    $flipped = array ();
    foreach ( $values as $rowKey => $row ) {
      foreach ( $row as $colKey => $cell ) {
        if (! isset ( $flipped [$colKey] ) || ! is_array ( $flipped [$colKey] ))
          $flipped [$colKey] = array ();
        $flipped [$colKey] [$rowKey] = $cell;
      }
    }
    return $flipped;
  }
}
/**
 * Class XmlForm_Field_JavaScript
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system.xmlform
 * @access public
 * @dependencies XmlForm_Field
 */
class XmlForm_Field_JavaScript extends XmlForm_Field {
  var $code = '';
  var $replaceTags = true;
  /**
   * Function XmlForm_Field_JavaScript
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string xmlNode
   * @parameter string lang
   * @parameter string home
   * @return string
   */
  function XmlForm_Field_JavaScript($xmlNode, $lang = 'en', $home = '') {
    //Loads any attribute that were defined in the xmlNode
    //except name and label.
    $myAttributes = get_class_vars ( get_class ( $this ) );
    foreach ( $myAttributes as $k => $v )
      $myAttributes [$k] = strtoupper ( $k );
    foreach ( $xmlNode->attributes as $k => $v ) {
      $key = array_search ( strtoupper ( $k ), $myAttributes );
      if ($key)
        eval ( '$this->' . $key . '=$v;' );
    }
    //Loads the main attributes
    $this->name = $xmlNode->name;
    $this->type = strtolower ( $xmlNode->attributes ['type'] );
    //$data: Includes labels and options.
    $this->code = $xmlNode->value;
  }
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @return string
   */
  function render($value = NULL, $owner = NULL) {
    $code = ($this->replaceTags) ? G::replaceDataField ( $this->code, $owner->values ) : $this->code;
    return $code;
  }
  /**
   * Function renderGrid
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string value
   * @parameter string owner
   * @return string
   */
  function renderGrid($value, $owner) {
    return array ('' );
  }
  /* A javascript node has no value
   */
  function validateValue($value) {
    return false;
  }
}

/**
 * @Description   Calendar Widget with Javascript Routines
 * @Author      Erik amaru Ortiz <erik@colosa.com>
 * @creation date   2008-07-25
 * @Last Modification 2008-07-29
 * @Modification 2008-07-29 Comment Working for after and before date attributes
 */
class XmlForm_Field_Date3 extends XmlForm_Field_SimpleText {
  public $required = false;
  public $readOnly = false;

  public $startDate = '';
  public $endDate = '';
  /*
  * for dinamically dates,   beforeDate << currentDate << afterDate
  * beforeDate='1y' means one year before,  beforeDate='3m' means 3 months before
  * afterDate='5y' means five year after,  afterDate='15d' means 15 days after
  * startDate and endDate have priority over beforeDate and AfterDate
  */
  public $afterDate = '';
  public $beforeDate = '';
  public $defaultValue = NULL;
  public $format = '%Y-%m-%d';

  public $mask = '%Y-%m-%d';
  public $dependentFields = '';

  function verifyDateFormat($date) {
    $dateTime=explode(" ",$date); //To accept the Hour part
    $aux = explode ( '-', $dateTime[0] );
    if (count ( $aux ) != 3)
      return false;
    if (! (is_numeric ( $aux [0] ) && is_numeric ( $aux [1] ) && is_numeric ( $aux [2] )))
      return false;
    if ($aux [0] < 1900 || $aux [0] > 2100)
      return false;
    return true;
  }

  function isvalidBeforeFormat($date) {
    $part1 = substr ( $date, 0, strlen ( $date ) - 1 );
    $part2 = substr ( $date, strlen ( $date ) - 1 );
    if ($part2 != 'd' && $part2 != 'm' && $part2 != 'y')
      return false;
    if (! is_numeric ( $part1 ))
      return false;
    return true;
  }

  function calculateBeforeFormat($date, $sign) {
    $part1 = $sign * substr ( $date, 0, strlen ( $date ) - 1 );
    $part2 = substr ( $date, strlen ( $date ) - 1 );
    switch ($part2) {
      case 'd' :
        $res = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ) + $part1, date ( 'Y' ) ) );
        break;
      case 'm' :
        $res = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ) + $part1, date ( 'd' ), date ( 'Y' ) ) );
        break;
      case 'y' :
        $res = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ), date ( 'Y' ) + $part1 ) );
        break;

    }
    return $res;
  }

  function render($value = NULL, $owner = NULL) {
    if (($this->pmconnection != '') && ($this->pmfield != '')) {
      $value = $this->getPMTableValue($owner);
    }
    else {
      $value = G::replaceDataField ( $value, $owner->values );
    }
    //$this->defaultValue = G::replaceDataField( $this->defaultValue, $owner->values);
    $id = "form[$this->name]";
    return $this->__draw_widget ( $id, $value, $owner );
  }

  function renderGrid($values = NULL, $owner = NULL, $onlyValue = false) {
    $result = array ();
    $r = 1;
    foreach ( $values as $v ) {
      $v = G::replaceDataField ( $v, $owner->values );
      if (! $onlyValue) {
        $id = 'form[' . $owner->name . '][' . $r . '][' . $this->name . ']';
        $html = $this->__draw_widget ( $id, $v, $owner );
      } else {
        $html = $v;
      }
      $result [] = $html;
      $r ++;
    }
    return $result;
  }

  function __draw_widget($pID, $value, $owner = '') {
    $startDate = G::replaceDataField ( $this->startDate, $owner->values );
    $endDate = G::replaceDataField ( $this->endDate, $owner->values );

    $beforeDate = G::replaceDataField ( $this->beforeDate, $owner->values );
    $afterDate = G::replaceDataField ( $this->afterDate, $owner->values );

    if ($startDate != '') {
      if (! $this->verifyDateFormat ( $startDate ))
        $startDate = '';
    }
    if (isset ( $beforeDate ) && $beforeDate != '') {
      if ($this->isvalidBeforeFormat ( $beforeDate ))
        $startDate = $this->calculateBeforeFormat ( $beforeDate, 1 );
    }

    if ($startDate == '' && isset ( $this->size ) && is_numeric ( $this->size ) && $this->size >= 1900 && $this->size <= 2100) {
      $startDate = $this->size . '-01-01';
    }

    if ($startDate == '') {
      //$startDate = date ( 'Y-m-d' ); // the default is the current date
    }

    if ($endDate != '') {
      if (! $this->verifyDateFormat ( $endDate ))
        $endDate = '';
    }

    if (isset ( $afterDate ) && $afterDate != '') {
      if ($this->isvalidBeforeFormat ( $afterDate ))
        $endDate = $this->calculateBeforeFormat ( $afterDate, + 1 );
    }

    if (isset ( $this->maxlength ) && is_numeric ( $this->maxlength ) && $this->maxlength >= 1900 && $this->maxlength <= 2100) {
      $endDate = $this->maxlength . '-01-01';
    }
    if ($endDate == '') {
      //$this->endDate = mktime ( 0,0,0,date('m'),date('d'),date('y') );  // the default is the current date + 2 years
      $endDate = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ), date ( 'Y' ) + 2 ) ); // the default is the current date + 2 years
    }

    if( isset($this->mask) && $this->mask != '' ){
      $mask = $this->mask;
    } else {
      $mask = '%Y-%m-%d';
    }
    //echo '---->'.$value;
	$tmp = str_replace("%", "", $mask);
    if ( trim ($value) == '' or $value == NULL ) {
      $value = ''; //date ($tmp);
    } else {
    	switch(strtolower($value)){
    		case 'today':
    			$value = date($tmp);
    		break;
    		default:
    			if(!$this->verifyDateFormat($value))
    				$value='';
    		break;
    	}
    }

    /*for old mask definitions...*/
    if($mask == 'yyyy-mm-dd'){
      $mask = '%Y-%m-%d';
    }

    #the validations field was moved to javascript routines ;)
    if ($this->mode == 'edit') {
      $html = "<input size=9  class='module_app_input___gray' readonly=true type='text' id='" . $pID . "' name='" . $pID . "' value='" . $value . "'>";
      $html .= "<a title='Show calendar' href='#' onclick=\"return showCalendar('$pID', '$mask', '24', true, '$startDate', '$endDate');\" >
      			  <img src='/controls/Calendar-32x32.png' border='0' width='12' height='14'>
      			</a>
      			<a title='Reset date field' href='#' onclick=\"document.getElementById('$pID').value=''; return false;\"><img src='/controls/TrashIcon.jpg' border='0' width='12' height='15'></a>";
    } else {
      $html = "<span style='border:1;border-color:#000;width:100px;' name='" . $pID . "'>$value</span>";
    }
    return $html;
  }
}



/**
 * @Description   Calendar Widget with Javascript Routines
 * @Author      Erik amaru Ortiz <aortiz@gmail.com, erik@colosa.com>
 * @creation date   Oct 5th, 2009
 */
class XmlForm_Field_Date extends XmlForm_Field_SimpleText {
  public $required = false;
  public $readOnly = false;

  public $startDate = '';
  public $endDate = '';
  /*
  * for dinamically dates,   beforeDate << currentDate << afterDate
  * beforeDate='1y' means one year before,  beforeDate='3m' means 3 months before
  * afterDate='5y' means five year after,  afterDate='15d' means 15 days after
  * startDate and endDate have priority over beforeDate and AfterDate
  */
  public $afterDate = '';
  public $beforeDate = '';
  public $defaultValue = NULL;
  public $format = '%Y-%m-%d';

  public $mask = '%Y-%m-%d';
  public $dependentFields = '';

  public $showtime;
  var $hints_message;

  function verifyDateFormatk($date,$msk) {
    $dateTime=explode(" ",$date); //To accept the Hour part
    $aux = explode ( '-', $dateTime[0] );
    $axmsk = explode ( '-', $msk );
    if (count ( $aux ) != 3)
      return false;
    if (! (is_numeric ( $aux [0] ) && is_numeric ( $aux [1] ) && is_numeric ( $aux [2] )))
      return false;
      
      $c=0;$sw=1;
    		while($c<count($axmsk) && $sw){
    			if(strtoupper($axmsk[$c++])=="Y")
    				$sw=0;
    			}
    if ($aux [$c-1] < 1900 || $aux [$c-1] > 2100)
      return false;
    return true;
  }

  function verifyDateFormat($date) {
    $dateTime=explode(" ",$date); //To accept the Hour part
    $aux = explode ( '-', $dateTime[0] );
    if (count ( $aux ) != 3)
      return false;
    if (! (is_numeric ( $aux [0] ) && is_numeric ( $aux [1] ) && is_numeric ( $aux [2] )))
      return false;
    if ($aux [0] < 1900 || $aux [0] > 2100)
      return false;
    return true;
  }

  function isvalidBeforeFormat($date) {
    $part1 = substr ( $date, 0, strlen ( $date ) - 1 );
    $part2 = substr ( $date, strlen ( $date ) - 1 );
    if ($part2 != 'd' && $part2 != 'm' && $part2 != 'y')
      return false;
    if (! is_numeric ( $part1 ))
      return false;
    return true;
  }

  function calculateBeforeFormat($date, $sign) {
    $part1 = $sign * substr ( $date, 0, strlen ( $date ) - 1 );

    $part2 = substr ( $date, strlen ( $date ) - 1 );

    #TODO
    # neyek
    /*
     * Because mktime has the restriccion for:
     * The number of the year, may be a two or four digit value, with values between 0-69 mapping to 2000-2069 and 70-100 to 1970-2000.
     * On systems where time_t is a 32bit signed integer, as most common today, the valid range for year  is somewhere
     * between 1901 and 2038. However, before PHP 5.1.0 this range was limited from 1970 to 2038 on some systems (e.g. Windows). */
    # improving required

    switch ($part2) {
      case 'd' :
      	/**
      	 * fixed for new calendar, this allows to select today setting the property to  e.g "0d"
      	 * this means that is zero days before so it is today. that Why -1 for days 
      	 */
      	$part1--;   

        $res = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ) + $part1 - 1, date ( 'Y' ) ) );
        break;
      case 'm' :
        $res = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ) + $part1, date ( 'd' ) - 1, date ( 'Y' ) ) );
        break;
      case 'y' :
        //$res = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ), date ( 'Y' ) + $part1) );
        //hook
        $res = (intVal(date ( 'Y' )) + $part1) . '-' . date ( 'm' ) . '-' . date ( 'd' );
        break;
    }

    return $res;
  }

  function render($value = NULL, $owner = NULL) {
    if (($this->pmconnection != '') && ($this->pmfield != '')) {
      $value = $this->getPMTableValue($owner);
    }
    else {
      $value = G::replaceDataField ( $value, $owner->values );
    }
    //$this->defaultValue = G::replaceDataField( $this->defaultValue, $owner->values);
    $id = "form[$this->name]";
    return $this->__draw_widget ( $id, $value, $owner );
  }

  function renderGrid($values = NULL, $owner = NULL, $onlyValue = false) {
    $result = array ();
    $r = 1;
    foreach ( $values as $v ) {
      $v = G::replaceDataField ( $v, $owner->values );
      if (! $onlyValue) {
        $id = 'form[' . $owner->name . '][' . $r . '][' . $this->name . ']';
        $html = $this->__draw_widget ( $id, $v, $owner );
      } else {
        $html = $v;
      }
      $result [] = $html;
      $r ++;
    }
    return $result;
  }

  function __draw_widget($pID, $value, $owner = '') {

    $startDate = G::replaceDataField ( $this->startDate, $owner->values );
    $endDate = G::replaceDataField ( $this->endDate, $owner->values );

    $beforeDate = G::replaceDataField ( $this->beforeDate, $owner->values );
    $afterDate = G::replaceDataField ( $this->afterDate, $owner->values );

    if ($startDate != '') {
      if (! $this->verifyDateFormat ( $startDate ))
        $startDate = '';
    }
    if (isset ( $beforeDate ) && $beforeDate != '') {
      if ($this->isvalidBeforeFormat ( $beforeDate ))
        $startDate = $this->calculateBeforeFormat ( $beforeDate, 1 );
    }

    if ($startDate == '' && isset ( $this->size ) && is_numeric ( $this->size ) && $this->size >= 1900 && $this->size <= 2100) {
      $startDate = $this->size . '-01-01';
    }

    if ($startDate == '') {
      //$startDate = date ( 'Y-m-d' ); // the default is the current date
    }

    if ($endDate != '') {
      if (! $this->verifyDateFormat ( $endDate ))
        $endDate = '';
    }

    if (isset ( $afterDate ) && $afterDate != '') {
      if ($this->isvalidBeforeFormat ( $afterDate ))
        $endDate = $this->calculateBeforeFormat ( $afterDate, + 1 );
    }

    if (isset ( $this->maxlength ) && is_numeric ( $this->maxlength ) && $this->maxlength >= 1900 && $this->maxlength <= 2100) {
      $endDate = $this->maxlength . '-01-01';
    }
    if ($endDate == '') {
      //$this->endDate = mktime ( 0,0,0,date('m'),date('d'),date('y') );  // the default is the current date + 2 years
      $endDate = date ( 'Y-m-d', mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ), date ( 'Y' ) + 2 ) ); // the default is the current date + 2 years
    }

    if( isset($this->mask) && $this->mask != '' ){
      $mask = $this->mask;
    } else {
      $mask = '%Y-%m-%d';
    }
    //echo '---->'.$value;
	$tmp = str_replace("%", "", $mask);
    if ( trim ($value) == '' or $value == NULL ) {
      $value = '';//date ($tmp);
    } else {
    	switch(strtolower($value)){
    		case 'today':
    			$value = date($tmp);
    		break;
    		default:
    		    $mask=($mask == 'yyyy-mm-dd')?$mask = 'Y-m-d':str_replace('%', '', $mask);
    			if(!$this->verifyDateFormatk($value,$mask))
    				$value='';
    		break;
    	}
    }

    /*for old mask definitions...*/
    if($mask == 'yyyy-mm-dd'){
      $mask = '%Y-%m-%d';
    }

    /* modeled for new mask*/
  	$mask = str_replace('%', '', $mask);

  	if( $value == ''){
  		$valueDate = Array(date('Y'), date('m'), date('d'));
  	} else {
  		$valueDate = $this->getSplitDate($value, $mask);
  	}

    $startDate = $this->getSplitDate($startDate, 'Y-m-d');
	$endDate = $this->getSplitDate($endDate, 'Y-m-d');

	if(isset($this->showtime) && $this->showtime){
		$mask .= ' h:i';
		$cClass = 'calendar_picker';
		$showTime = 'true';
	} else {
		$cClass = 'calendar_picker_min';
		$showTime = 'false';
	}

   

    #the validations field was moved to javascript routines ;)
    $html = '<input type="hidden" id="'.$pID.'" name="'.$pID.'" value="'.$value.'"/>';
    if ($this->mode == 'edit') {
      $html .= '
      <div id="'.$pID.'[div]"
      		name="'.$pID.'[div]"
      		onclick="var oc=new NeyekCalendar(\''.$pID.'\');
      			oc.picker(
      				{\'year\':\''.$valueDate[0].'\',\'month\':\''.$valueDate[1].'\',\'day\':\''.$valueDate[2].'\'},
	    			\''.$mask.'\',
	    			\''.SYS_LANG.'\',
	    			{\'year\':\''.$startDate[0].'\',\'month\':\''.$startDate[1].'\',\'day\':\''.$startDate[2].'\'},
	    			{\'year\':\''.$endDate[0].'\',\'month\':\''.$endDate[1].'\',\'day\':\''.$endDate[2].'\'},
	    			'.$showTime.'
	    		);
	    		return false;"
      		class="'.$cClass.'"
      >&nbsp;'.$value.'</div>';
    } else {
      $html .= "<span style='border:1;border-color:#000;width:100px;' name='" . $pID . "'>$value</span>";
    }
    if($this->hints_message){
      		 $html .= '<a href="#" onmouseout="hideTooltip()" onmouseover="showTooltip(event, \''.$this->hints_message.'\');return false;">
	 	    					<image src="/images/help4.gif" width="15" height="15" border="0"/>
	 	 						</a>';
	 	}  
    return $html;
  }

  function getSplitDate($date, $mask){
	$sw1 = false;
	for($i=0; $i<3; $i++){
		$item = substr($mask, $i*2, 1);
		switch($item){
			case 'Y':
				switch($i){
					case 0: $d1 = substr($date, 0, 4); break;
					case 1: $d1 = substr($date, 3, 4); break;
					case 2: $d1 = substr($date, 6, 4); break;
				}
				$sw1 = true;
			break;
			case 'y':
				switch($i){
					case 0: $d1 = substr($date, 0, 2); break;
					case 1: $d1 = substr($date, 3, 2); break;
					case 2: $d1 = substr($date, 6, 2); break;
				}
			break;
			case 'm':
				switch($i){

					case 0: $d2 = substr($date, 0, 2); break;
					case 1: $d2 = ($sw1)? substr($date, 5, 2): substr($date, 3, 2); break;
					case 2: $d2 = ($sw1)? substr($date, 8, 2): substr($date, 5, 2); break;
				}
			break;
			case 'd':
				switch($i){
					case 0: $d3 = substr($date, 0, 2); break;
					case 1: $d3 = ($sw1)? substr($date, 5, 2): substr($date, 3, 2); break;
					case 2: $d3 = ($sw1)? substr($date, 8, 2): substr($date, 5, 2); break;
				}
			break;
		}
	}
	return Array($d1, $d2, $d3);
  }
}

/**  AVOID TO ENTER HERE : EXPERIMENTAL !!!
 *  by Caleeli.
 */
class XmlForm_Field_Xmlform extends XmlForm_Field {
  var $xmlfile = '';
  var $initRows = 1;
  var $group = 0;
  var $addRow = true;
  var $deleteRow = false;
  var $editRow = false;
  var $sql = '';
  //TODO: 0=doesn't excecute the query, 1=Only the first time, 2=Allways
  var $fillType = 0;
  var $fields = array ();
  var $scriptURL;
  var $id = '';
  /**
   * Function XmlForm_Field_Xmlform
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string xmlnode
   * @parameter string language
   * @parameter string home
   * @return string
   */
  function XmlForm_Field_Xmlform($xmlnode, $language, $home) {
    parent::XmlForm_Field ( $xmlnode, $language );
    $this->parseFile ( $home, $language );
  }
  /**
   * Function parseFile
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string home
   * @parameter string language
   * @return string
   */
  function parseFile($home, $language) {
    $this->xmlform = new XmlForm ( );
    $this->xmlform->home = $home;
    $this->xmlform->parseFile ( $this->xmlfile . '.xml', $language, false );
    $this->fields = $this->xmlform->fields;
    $this->scriptURL = $this->xmlform->scriptURL;
    $this->id = $this->xmlform->id;
    unset ( $this->xmlform );
  }
  /**
   * Function render
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string values
   * @return string
   */
  function render($values) {
    $html = '';
    foreach ( $this->fields as $f => $v ) {
      $html .= $v->render ( '' );
    }
    $this->id = $this->owner->id . $this->name;
    $tpl = new xmlformTemplate ( $this, PATH_CORE . 'templates/xmlform.html' );
    $this->values = $values;
    //$this->rows=count(reset($values));
    $tpl->template = $tpl->printTemplate ( $this );
    //In the header
    $oHeadPublisher = & headPublisher::getSingleton ();
    $oHeadPublisher->addScriptFile ( $this->scriptURL );
    $oHeadPublisher->addScriptCode ( $tpl->printJavaScript ( $this ) );
    return $tpl->printObject ( $this );
  }
}
/**
 * Class XmlForm
 * Main Class
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 * @dependencies Xml_document  XmlForm_Field  xmlformTemplate
 */
class XmlForm {
  var $tree;
  var $id = '';
  var $name = '';
  var $language;
  /* @attribute string version 0.xxx = Previous to pre-open source
  */
  var $version = '0.3';
  var $fields = array ();
  var $title = '';
  var $home = '';
  var $parsedFile = '';
  var $type = 'xmlform';
  var $fileName = '';
  var $scriptFile = '';
  var $scriptURL = '';
  /* Special propose attributes*/
  var $sql;
  var $sqlConnection;
  /*Attributes for the xmlform template*/
  var $width = 600;
  var $height = "100%";
  var $border = 1;
  var $mode = '';
  var $labelWidth = 140;
  var $onsubmit = '';
  var $requiredFields = array ();
  /**
   * Function xmlformTemplate
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string form
   * @parameter string templateFile
   * @return string
   */
  function parseFile($filename, $language, $forceParse) {
    $this->language = $language;
    $filename = $this->home . $filename;
    $this->fileName = $filename;
    $parsedFile = dirname ( $filename ) . PATH_SEP . basename ( $filename, 'xml' ) . $language;
    $parsedFile = (defined ( 'PATH_C' ) ? PATH_C : PATH_DATA) . 'xmlform/' . substr ( $parsedFile, strlen ( $this->home ) );
    $this->parsedFile = $parsedFile;
    //Note that scriptFile must be public URL.
    $realPath = substr ( realpath ( $this->fileName ), strlen ( realpath ( $this->home ) ), - 4 );
    if (substr ( $realPath, 0, 1 ) != PATH_SEP)
      $realPath = PATH_SEP . $realPath;
    $this->scriptURL = '/jsform' . $realPath . '.js';
    $this->scriptFile = substr ( (defined ( 'PATH_C' ) ? PATH_C : PATH_DATA) . 'xmlform/', 0, - 1 ) . substr ( $this->scriptURL, 7 );
    $this->id = G::createUID ( '', substr ( $this->fileName, strlen ( $this->home ) ) );
    $this->scriptURL = str_replace ( '\\', '/', $this->scriptURL );

    $newVersion = false;
    if ($forceParse || ((! file_exists ( $this->parsedFile )) || (filemtime ( $filename ) > filemtime ( $this->parsedFile )) || (filemtime ( __FILE__ ) > filemtime ( $this->parsedFile ))) || (! file_exists ( $this->scriptFile )) || (filemtime ( $filename ) > filemtime ( $this->scriptFile ))) {
      $this->tree = new Xml_Document ( );
      $this->tree->parseXmlFile ( $filename );
      //$this->tree->unsetParent();
      if (! is_object ( $this->tree->children [0] ))
        throw new Exception ( 'Failure loading root node.' );
      $this->tree = &$this->tree->children [0]->toTree ();
      //ERROR CODE [1] : Failed to read the xml document
      if (! isset ( $this->tree ))
        return 1;
      $xmlNode = & $this->tree->children;

      //Set the form's attributes
      $myAttributes = get_class_vars ( get_class ( $this ) );
      foreach ( $myAttributes as $k => $v )
        $myAttributes [$k] = strtolower ( $k );
      foreach ( $this->tree->attributes as $k => $v ) {
        $key = array_search ( strtolower ( $k ), $myAttributes );
        if (($key !== FALSE) && (strtolower ( $k ) !== 'fields') && (strtolower ( $k ) !== 'values'))
          $this->{$key} = $v;
      }
      //Reeplace non valid characters in xmlform name with "_"
      $this->name = preg_replace ( '/\W/', '_', $this->name );
      //Create fields


      foreach ( $xmlNode as $k => $v ) {
        if (($xmlNode [$k]->type !== 'cdata') && isset ( $xmlNode [$k]->attributes ['type'] )) {
          if (class_exists ( 'XmlForm_Field_' . $xmlNode [$k]->attributes ['type'] )) {
            $x = '$field = new XmlForm_Field_' . $xmlNode [$k]->attributes ['type'] . '( $xmlNode[$k], $language, $this->home, $this);';

            eval ( $x );
          } else
            $field = new XmlForm_Field ( $xmlNode [$k], $language, $this->home, $this );
          $field->language = $this->language;
          $this->fields [$field->name] = $field;
        }

        if (isset ( $xmlNode [$k]->attributes ['required'] )) {
          if ($xmlNode [$k]->attributes ['required'] == 1)
            $this->requiredFields [] = array ('name' => $field->name, 'type' => $xmlNode [$k]->attributes ['type'], 'label' => trim ( $field->label ) );
        }

      }
      //var_dump($this->requiredFields);
      $oJSON = new Services_JSON ( );
      $this->objectRequiredFields = addslashes ( $oJSON->encode ( $this->requiredFields ) );

      //Load the default values
      //$this->setDefaultValues();
      //Save the cache file
      if (! is_dir ( dirname ( $this->parsedFile ) ))
        G::mk_dir ( dirname ( $this->parsedFile ) );
      $f = fopen ( $this->parsedFile, 'w+' );
      //ERROR CODE [2] : Failed to open cache file
      if ($f === FALSE)
        return 2;
      fwrite ( $f, "<?\n" );
      /*  fwrite ($f, '$this = unserialize( \'' .
                  addcslashes( serialize ( $this ), '\\\'' ) . '\' );' . "\n" );*/
      foreach ( $this as $key => $value ) {
        switch ($key) {
          case 'home' :
          case 'fileName' :
          case 'parsedFile' :
          case 'scriptFile' :
          case 'scriptURL' :
            break;
          default :
            switch (true) {
              case is_string ( $this->{$key} ) :
                fwrite ( $f, '$this->' . $key . '=\'' . addcslashes ( $this->{$key}, '\\\'' ) . '\'' . ";\n" );
                break;
              case is_bool ( $this->{$key} ) :
                fwrite ( $f, '$this->' . $key . '=' . (($this->{$key}) ? 'true;' : 'false') . ";\n" );
                break;
              case is_null ( $this->{$key} ) :
                fwrite ( $f, '$this->' . $key . '=NULL' . ";\n" );
                break;
              case is_float ( $this->{$key} ) :
              case is_int ( $this->{$key} ) :
                fwrite ( $f, '$this->' . $key . '=' . $this->{$key} . ";\n" );
                break;
              default :
                fwrite ( $f, '$this->' . $key . ' = unserialize( \'' . addcslashes ( serialize ( $this->{$key} ), '\\\'' ) . '\' );' . "\n" );
            }
        }
      }
      fwrite ( $f, "?>" );
      fclose ( $f );
      $newVersion = true;
    } //if $forceParse
    //Loads the parsedFile.
    require ($this->parsedFile);
    $this->fileName = $filename;
    $this->parsedFile = $parsedFile;

    //RECREATE LA JS file
    //Note: Template defined with publisher doesn't affect the .js file
    //created at this point.
    if ($newVersion) {
      $template = PATH_CORE . 'templates/' . $this->type . '.html';
      //If the type is not the correct template name, use xmlform.html
      //if (!file_exists($template)) $template = PATH_CORE . 'templates/xmlform.html';
      if (($template !== '') && (file_exists ( $template ))) {
        if (! is_dir ( dirname ( $this->scriptFile ) ))
          G::mk_dir ( dirname ( $this->scriptFile ) );
        $f = fopen ( $this->scriptFile, 'w' );
        $o = new xmlformTemplate ( $this, $template );
        $scriptContent = $o->printJSFile ( $this );
        unset ( $o );
        fwrite ( $f, $scriptContent );
        fclose ( $f );
      }
    }
    return 0;
  }
  /* Generic function to set values for the current object.
   */
  function setValues($newValues = array()) {
    foreach ( $this->fields as $k => $v ) {
      if (array_key_exists ( $k, $newValues ))
        $this->values [$k] = $newValues [$k];
    }
    foreach ( $this->fields as $k => $v ) {
      $this->fields [$k]->owner = & $this;
    }
  }
  /* Generic function to print the current object.
  */
  function render($template, &$scriptContent) {
    $o = new xmlformTemplate ( $this, $template );
    if (is_array ( reset ( $this->values ) ))
      $this->rows = count ( reset ( $this->values ) );
    $o->template = $o->printTemplate ( $this );
    $scriptContent = $o->printJavaScript ( $this );
    return $o->printObject ( $this );
  }
  function cloneObject() {
    return unserialize ( serialize ( $this ) );
  }
}
/**
 * Class xmlformTemplate
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 * @dependencies Smarty
 */
class xmlformTemplate extends Smarty {
  var $template;
  var $templateFile;
  /**
   * Function xmlformTemplate
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string form
   * @parameter string templateFile
   * @return string
   */
  function xmlformTemplate(&$form, $templateFile) {
    $this->template_dir = PATH_XMLFORM;
    $this->compile_dir = PATH_SMARTY_C;
    $this->cache_dir = PATH_SMARTY_CACHE;
    $this->config_dir = PATH_THIRDPARTY . 'smarty/configs';
    $this->caching = false;

    // register the resource name "db"
    $this->templateFile = $templateFile;
  }
  /**
   * Function printTemplate
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string form
   * @parameter string target
   * @return string
   */
  function printTemplate(&$form, $target = 'smarty') {
    if (strcasecmp ( $target, 'smarty' ) === 0)
      $varPrefix = '$';
    if (strcasecmp ( $target, 'templatePower' ) === 0)
      $varPrefix = '';

    $ft = new StdClass ( );
    foreach ( $form as $name => $value ) {
      if (($name !== 'fields') && ($value !== ''))
        $ft->{$name} = '{$form_' . $name . '}';
      if ($name === 'cols')
        $ft->{$name} = $value;
      if ($name === 'owner')
        $ft->owner = & $form->owner;
      if ($name === 'deleteRow')
        $ft->deleteRow = $form->deleteRow;
      if ($name === 'addRow')
        $ft->addRow = $form->addRow;
      if ($name === 'editRow')
        $ft->editRow = $form->editRow;
    }
    if (! isset ( $ft->action )) {
      $ft->action = '{$form_action}';
    }
    $hasRequiredFields = false;
    foreach ( $form->fields as $k => $v ) {
      $ft->fields [$k] = $v->cloneObject ();
      $ft->fields [$k]->label = '{' . $varPrefix . $k . '}';

      if ($form->type === 'grid') {
        if (strcasecmp ( $target, 'smarty' ) === 0)
          $ft->fields [$k]->field = '{' . $varPrefix . 'form.' . $k . '[row]}';
        if (strcasecmp ( $target, 'templatePower' ) === 0)
          $ft->fields [$k]->field = '{' . $varPrefix . 'form[' . $k . '][row]}';
      } else {
        if (strcasecmp ( $target, 'smarty' ) === 0)
          $ft->fields [$k]->field = '{' . $varPrefix . 'form.' . $k . '}';
        if (strcasecmp ( $target, 'templatePower' ) === 0)
          $ft->fields [$k]->field = '{' . $varPrefix . 'form[' . $k . ']}';
      }

      $hasRequiredFields = $hasRequiredFields | (isset ( $v->required ) && ($v->required == '1') && ($v->mode == 'edit'));
    }
    $this->assign ( 'hasRequiredFields', $hasRequiredFields );
    $this->assign ( 'form', $ft );
    $this->assign ( 'printTemplate', true );
    $this->assign ( 'printJSFile', false );
    $this->assign ( 'printJavaScript', false );
    $this->assign ( 'dynaformSetFocus', "try {literal}{{/literal} dynaformSetFocus();}catch(e){literal}{{/literal}}" );
    return $this->fetch ( $this->templateFile );
  }
  /**
   * Function printJavaScript
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string form
   * @return string
   */
  function printJavaScript(&$form) {
    $this->assign ( 'form', $form );
    $this->assign ( 'printTemplate', false );
    $this->assign ( 'printJSFile', false );
    $this->assign ( 'printJavaScript', true );
    return $this->fetch ( $this->templateFile );
  }
  /**
   * Function printJSFile
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string form
   * @return string
   */
  function printJSFile(&$form) {
    $this->assign ( 'form', $form );
    $this->assign ( 'printTemplate', false );
    $this->assign ( 'printJSFile', true );
    $this->assign ( 'printJavaScript', false );
    return $this->fetch ( $this->templateFile );
  }
  /**
   * Function getFields
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string form
   * @return string
   */
  function getFields(&$form, $therow = -1) {

    $result = array ();
    foreach ( $form->fields as $k => $v ) {
      if ($form->mode != '') { #@ last modification: erik
        $v->mode = $form->mode; #@
      } #@
      //if (isset($form->fields[$k]->sql)) $form->fields[$k]->executeSQL( $form );
      $value = (isset ( $form->values [$k] )) ? $form->values [$k] : NULL;
      $result [$k] = G::replaceDataField ( $form->fields [$k]->label, $form->values );
      if (! is_array ( $value )) {
        if ($form->type == 'grid') {
          $aAux = array ();
          for($i = 0; $i < count ( $form->owner->values [$form->name] ); $i ++) {
            $aAux [] = '';
          }
          $result ['form'] [$k] = $form->fields [$k]->renderGrid ( $aAux, $form );
        } else {
          $result ['form'] [$k] = $form->fields [$k]->render ( $value, $form );
        }
      } else {
        if (isset ( $form->owner )) {
          if (count ( $value ) < count ( $form->owner->values [$form->name] )) {
            $i = count ( $value );
            $j = count ( $form->owner->values [$form->name] );
            for($i; $i < $j; $i ++) {
              $value [] = '';
            }
          }
        }
        if ($v->type == 'grid') {
          $result ['form'] [$k] = $form->fields [$k]->renderGrid ( $value, $form, $therow );
        } else {
          if ($v->type == 'dropdown') {
            $result ['form'] [$k] = $form->fields [$k]->renderGrid ( $value, $form, false, $therow );
          } else {
            $result ['form'] [$k] = $form->fields [$k]->renderGrid ( $value, $form );
          }
        }
      }
    }
    foreach ( $form as $name => $value ) {
      if ($name !== 'fields')
        $result ['form_' . $name] = $value;
    }
    return $result;
  }
  /**
   * Function printObject
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string form
   * @return string
   */
  function printObject(&$form, $therow = -1) {
    //to do: generate the template for templatePower.
    //DONE: The template was generated in printTemplate, to use it
    // is necesary to load the file with templatePower and send the array
    //result
    $this->register_resource ( 'mem', array (array (&$this, '_get_template' ), array ($this, '_get_timestamp' ), array ($this, '_get_secure' ), array ($this, '_get_trusted' ) ) );
    $result = $this->getFields ( $form, $therow );

    $this->assign ( array ('PATH_TPL' => PATH_TPL ) );
    $this->assign ( $result );
    if( defined('SYS_LANG_DIRECTION') && SYS_LANG_DIRECTION == 'R' ){
        switch( $form->type ){
        	case 'toolbar':
                
                $form->align = 'right';
                
        		break;
        }	
    }
    
    
    
    $this->assign ( array ('_form' => $form ) );
    //'mem:defaultTemplate'.$form->name obtains the template generated for the
    //current "form" object, then this resource y saved by Smarty in the
    //cache_dir. To avoiding troubles when two forms with the same id are being
    //drawed in a same page with different templates, add an . rand(1,1000)
    //to the resource name. This is because the process of creating templates
    //(with the method "printTemplate") and painting takes less than 1 second
    //so the new template resource generally will had the same timestamp.
    $output = $this->fetch ( 'mem:defaultTemplate' . $form->name );
    return $output;
  }

  /*
   * Smarty plugin
   * -------------------------------------------------------------
   * Type:     resource
   * Name:     mem
   * Purpose:  Fetches templates from this object
   * -------------------------------------------------------------
   */
  function _get_template($tpl_name, &$tpl_source, &$smarty_obj) {
    $tpl_source = $this->template;
    return true;
  }
  /**
   * Function _get_timestamp
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string tpl_name
   * @parameter string tpl_timestamp
   * @parameter string smarty_obj
   * @return string
   */
  function _get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj) {
    //NOTE: +1 prevents to load the smarty cache instead of this resource
    $tpl_timestamp = time () + 1;
    return true;
  }
  /**
   * Function _get_secure
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string tpl_name
   * @parameter string smarty_obj
   * @return string
   */
  function _get_secure($tpl_name, &$smarty_obj) {
    // assume all templates are secure
    return true;
  }
  /**
   * Function _get_trusted
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string tpl_name
   * @parameter string smarty_obj
   * @return string
   */
  function _get_trusted($tpl_name, &$smarty_obj) {
    // not used for templates
  }
}