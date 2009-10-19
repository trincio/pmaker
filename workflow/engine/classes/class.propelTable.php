<?php
/**
 * class.propelTable.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
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

  G::LoadClass('filterForm');
  G::LoadClass('xmlMenu');
  G::LoadClass("BasePeer" );
  G::LoadClass("ArrayPeer" );

/**
 * Class pagedTable
 * @author David S. Callizaya S. <davidsantos@colosa.com>
 * @package gulliver.system
 * @access public
 * @dependencies TemplatePower  Form  XmlForm
 */

class propelTable
{
  var $xmlFormFile;
  var $currentPage;
  var $orderBy = '';
  var $filter = array();
  var $filterType = array();
  var $searchBy = '';
  var $fastSearch='';
  var $order = '';
  var $template='templates/paged-table.html';
  var $tpl;
  var $style = array();
  var $rowsPerPage=25;
  var $ownerPage;
  var $popupPage;
  var $popupSubmit;
  var $popupWidth=450;
  var $popupHeight=200;
  var $ajaxServer;
  var $fields;
  var $query;
  var $totPages;
  var $totRows;

  //SQL QUERIES
  var $criteria;
  var $sql='';
  var $sqlWhere='';
  var $sqlGroupBy='';
  var $sqlSelect='SELECT 1';
  var $sqlDelete='';
  var $sqlInsert='';
  var $sqlUpdate='';
  var $fieldDataList='';

  //Configuration
  var $xmlPopup='';
  var $addRow=false;
  var $deleteRow=false;
  var $editRow=false;
  var $notFields='  title button linknew begingrid2 endgrid2 '; // These are not considered to build the sql queries (update,insert,delete)

  //JavaScript Object attributes
  var $onUpdateField="";
  var $onDeleteField="";
  var $afterDeleteField="";
  var $onInsertField="";

  //New gulliver
  var $xmlForm;
  var $menu='';
  var $filterForm='';
  var $filterForm_Id='';
  var $name='pagedTable';
  var $id='A1';
  var $disableFooter = false;
    //This attribute is used to set STYLES to groups of TD, using the field type "cellMark" (see XmlForm_Field_cellMark)
  var $tdStyle='';
  var $tdClass='';
  //Config Save definition
  var $__Configuration='orderBy,filter,fastSearch,style/*/showInTable';//order,rowsPerPage,disableFooter';

  //Variable for MasterDetail feature
  var $masterdetail='';
  
  var $title;

  /**
   * Function prepareQuery
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return string
   */
  function prepareQuery( $limitPage = false ) {
    // process the QuickSearch string and add the fields and expression needed to run the search
    if ( $this->searchBy !== '' ) {
      $aSB = explode('|', $this->searchBy);  //fields are separated by pipes
//subfilter
      $subFilter='';
      foreach($aSB as $sBy) {
        $subFilter .= ($subFilter !== '') ? ' OR ' : '';
        //TODO: Get DATABASE type from Criteria, I think sql delimeter is needed too
        $subFilter .= $sBy . ' LIKE "%'.
        G::sqlEscape($this->fastSearch ).'%"';
      }
      if ($subFilter !=='' ) {
        //Get the first defined table in Criteria.
        $aCurrentTables = $this->criteria->getTables();
        if ( isset($aCurrentTables[0]))  {
          $this->criteria->add ( $aCurrentTables[0] . ".*", '('. $subFilter. ')' , Criteria::CUSTOM );
        }
      }
    }

    //Merge sort array defined by USER with the array defined by SQL
    parse_str($this->order,   $orderFields);
    parse_str($this->orderBy, $orderFields2);
    //User sort is more important (first in merge).
    $orderFields3 = array_merge($orderFields2, $orderFields);
    //User sort is overwrites XMLs definition.
    $orderFields = array_merge($orderFields3, $orderFields2);
    //Order (BY SQL DEFINITION AND USER'S DEFINITION)
    $this->aOrder = array();
    $order='';
    foreach ($orderFields as $field => $fieldOrder) {
      $field = G::getUIDName($field,'');

      $fieldOrder = strtoupper($fieldOrder);
      if ($fieldOrder==='A')  $fieldOrder = 'ASC';
      if ($fieldOrder==='D')  $fieldOrder = 'DESC';
      switch ( $fieldOrder ) {
        case 'ASC':
        case 'DESC':
          if ( $order !== '' )
            $order.=', ';
          $order .= $field . ' '. $fieldOrder;
          $this->aOrder[$field] = $fieldOrder;
      }
    }
    //master detail :O
    if(count($this->masterdetail) > 0){
      $this->criteria->clearOrderByColumns();
      foreach($this->masterdetail as $idMasterDetail => $fieldMasterDetail){
        $this->criteria->addAscendingOrderByColumn( $fieldMasterDetail );
      }
    }

    if (!empty($this->aOrder)) {
      if(count($this->masterdetail) <= 0) {
        $this->criteria->clearOrderByColumns();
      }
      foreach ($this->aOrder as $field => $ascending ) {
        if ( $ascending == 'ASC' )
          $this->criteria->addAscendingOrderByColumn ( $field );
        else
          $this->criteria->addDescendingOrderByColumn( $field );
      }
    }

    /** Add limits */
    $this->criteria->setLimit( 0 );
    $this->criteria->setOffset( 0 );
    if ( $this->criteria->getDbName() == 'dbarray' ) {
      $this->totRows = ArrayBasePeer::doCount( $this->criteria );
    }
    else {
      $this->totRows = GulliverBasePeer::doCount( $this->criteria );
    }
    $this->totPages = ceil( $this->totRows / $this->rowsPerPage);
    if ( $limitPage ) {
      $this->criteria->setLimit ( $this->rowsPerPage );
      $this->criteria->setOffset( ($this->currentPage-1) * $this->rowsPerPage );
    }
    return;
  }

  /**
   * Function setupFromXmlform
   *
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @editedby Hugo Loza <hugo@colosa.com>
   * @access public
   * @parameter string xmlForm
   * @return string
   */
  function setupFromXmlform($xmlForm)   {
    $this->xmlForm = $xmlForm;
    //Config
    $this->name = $xmlForm->name;
    $this->id   = $xmlForm->id;

    //$this->sqlConnection=((isset($this->xmlForm->sqlConnection))?$this->xmlForm->sqlConnection:'');

    if ( isset($_GET['page']))   $this->currentPage = $_GET['page']; else $this->currentPage = 1;
    if ( isset($_GET['order']))  $this->orderBy     = urldecode($_GET['order']); else $this->orderBy = "";
    if ( isset($_GET['filter'])) $this->filter      = urldecode($_GET['filter']); else $this->filter = "";

    if ($xmlForm->ajaxServer != '') {
      $this->ajaxServer = G::encryptLink( $xmlForm->ajaxServer );
    }
    else {
      $this->ajaxServer = G::encryptLink( '../gulliver/propelTableAjax' );
    }
    $this->ownerPage  = G::encryptLink( SYS_CURRENT_URI );

    // Config attributes from XMLFORM file
    $myAttributes = get_class_vars(get_class($this));
    foreach ($this->xmlForm->xmlform->tree->attribute as $atrib => $value)
    if (array_key_exists( $atrib, $myAttributes)) {
      eval('settype($value, gettype($this->' . $atrib.'));');
      if ($value !== '') eval( '$this->' . $atrib . '=$value;');
    }

    if($this->masterdetail!=""){
      $this->masterdetail=explode(",",$this->masterdetail);
      foreach($this->masterdetail as $keyMasterDetail => $valueMasterDetail){
        $this->masterdetail[$keyMasterDetail]=trim($valueMasterDetail);
      }
    }
    else{
      $this->masterdetail=array();
    }

    //Prepare the fields
    $this->style=array();$this->gridWidth="";$this->gridFields="";
    $this->fieldsType=array();
    foreach ($this->xmlForm->fields as $f => $v) {
      $r=$f;
      $this->fields[$r]['Name'] =$this->xmlForm->fields[$f]->name;
      $this->fields[$r]['Type'] =$this->xmlForm->fields[$f]->type;
      if (isset($this->xmlForm->fields[$f]->size)) $this->fields[$r]['Size'] = $this->xmlForm->fields[$f]->size;
      $this->fields[$r]['Label']=$this->xmlForm->fields[$f]->label;
    }

    //Set the default settings
    $this->defaultStyle();

    //continue with the setup
    $this->gridWidth=''; $this->gridFields='';

    foreach($this->xmlForm->fields as $f => $v)
    {
      $r=$f;
      //Parse the column properties
      foreach ($this->xmlForm->fields[$f] as $attribute => $value)
      {
        if (!is_object($value)) {
          $this->style[$r][$attribute] = $value;
        }
      }

      //Needed for javascript
      //only the visible columns's width and name are stored
      if ($this->style[$r]['showInTable']!='0')
      {
        $this->gridWidth.=','.$this->style[$r]['colWidth'];
        $this->gridFields.=',"form['.$this->fields[$r]['Name'].']"';
      }
    }

    $totalWidth=0;
    foreach($this->fields as $r => $rval)
    if ($this->style[$r]['showInTable']!='0')
      $totalWidth += $this->style[$r]['colWidth'];
    $this->totalWidth = $totalWidth;

  }

  /**
   * Function count
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return string
   */
  function count()
  {
    $this->prepareQuery();
    return $this->totRows;
  }

  /**
   * Function renderTitle
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return string
   */
  function renderTitle()
  {
    //Render headers
    $this->colCount=0;
    $this->shownFields='[';
    foreach($this->fields as $r => $rval)
    if (($this->style[$r]['showInTable'] != '0' )&&(!(in_array($this->fields[$r]['Name'],$this->masterdetail))))
    //if (($this->style[$r]['showInTable'] != '0' ))
    {
      $this->tpl->newBlock( "headers" );
      $sortOrder = (((isset($this->aOrder[$this->fields[$r]['Name']])) && ($this->aOrder[$this->fields[$r]['Name']]==='ASC'))?'DESC':'ASC');
      $sortOrder = (((isset($this->aOrder[$this->fields[$r]['Name']])) && ($this->aOrder[$this->fields[$r]['Name']]==='DESC'))?'':$sortOrder);
      $this->style[$r]['href'] = $this->ownerPage . '?order=' .
         ( $sortOrder !=='' ? ( G::createUID('',$this->fields[$r]['Name'] ) . '=' . $sortOrder):'')
         . '&page=' . $this->currentPage;
      $this->style[$r]['onsort'] = $this->id . '.doSort("'.G::createUID('',$this->fields[$r]['Name']).'" , "' . $sortOrder.'");return false;';

      if (isset($this->style[$r]['href']))       $this->tpl->assign( "href" ,    $this->style[$r]['href']);
      if (isset($this->style[$r]['onsort']))     $this->tpl->assign( "onsort" , htmlentities( $this->style[$r]['onsort'] , ENT_QUOTES, 'UTF-8' ) );
      if (isset($this->style[$r]['onclick']))    $this->tpl->assign( "onclick" , htmlentities( $this->style[$r]['onclick'] , ENT_QUOTES, 'UTF-8' ) );
      if (isset($this->style[$r]['colWidth']))   $this->tpl->assign( "width" ,   $this->style[$r]['colWidth'] );
      if (isset($this->style[$r]['colWidth']))   $this->tpl->assign( "widthPercent" , ($this->style[$r]['colWidth']*100 / $this->totalWidth) . "%" );
      if (isset($this->style[$r]['titleAlign'])) $this->tpl->assign( "align" , 'text-align:'.$this->style[$r]['titleAlign'].';');
      if ($this->style[$r]['titleVisibility']!='0') {
        $sortOrder = (((isset($this->aOrder[$this->fields[$r]['Name']])) && ($this->aOrder[$this->fields[$r]['Name']]==='ASC'))?'▲':'');
        $sortOrder = (((isset($this->aOrder[$this->fields[$r]['Name']])) && ($this->aOrder[$this->fields[$r]['Name']]==='DESC'))?'▼':$sortOrder);
        $this->tpl->assign( "header" , $this->fields[$r]['Label'] . $sortOrder );
        $this->tpl->assign('displaySeparator',
          (($this->colCount==0)||(!isset($this->fields[$r]['Label']))||($this->fields[$r]['Label']===''))?'display:none;':'');
      } else {
        $this->tpl->assign('displaySeparator','display:none;');
      }
      $this->colCount+=2;
      $this->shownFields.=($this->shownFields!=='[')?',':'';
      $this->shownFields.='"'.$r.'"';
    }
    $this->shownFields.=']';
  }

  /**
   * Function renderField
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @parameter string row
   * @parameter string r
   * @parameter string result
   * @return string
   */
  function renderField( $row, $r, $result)
  {
    global $G_DATE_FORMAT;
//to do: special content??
    //$result['row__']            = $row;   //Special content:

    $styleData = $this->style[$r];
    $fielDataName = $styleData['data'];
    $fieldClassName = isset( $styleData['colClassName']) && ($styleData['colClassName']) ? $styleData['colClassName'] : $this->tdClass;

    if ( $fielDataName != '' )
      $value = ((isset($result[ $fielDataName ])) ? $result[ $fielDataName ] : '' );
    else
      $value = $this->fields[$r]['Label'];

    $this->tpl->newBlock( "field" );

    $this->tpl->assign('width', $this->style[$r]['colWidth']);
    $classAttr = ( trim($fieldClassName) != '' ) ? " class=\"$fieldClassName\"" : '';
    $this->tpl->assign('classAttr', $classAttr );
    //to do: style is needed or not?
    //$this->tpl->assign('style', $this->tdStyle);

    $alignAttr = ( isset($this->style[$r]['align']) && strlen($this->style[$r]['align']>0) ) ? " align=\"" . $this->style[$r]['align'] . "\"" : '';
    $this->tpl->assign( "alignAttr" , $alignAttr);

    $fieldName  = $this->fields[$r]['Name'];
    $fieldClass = get_class( $this->xmlForm->fields[ $fieldName ] );

    /*** BEGIN : Reeplace of @@, @%,... in field's attributes like onclick, link,  */
    if (isset($this->xmlForm->fields[ $this->fields[$r]['Name'] ]->link)) {
      $this->xmlForm->fields[ $this->fields[$r]['Name'] ]->link
        = G::replaceDataField($this->style[$r]['link'],$result);
    }
    if (isset($this->xmlForm->fields[ $fieldName ]->value)) {
      $this->xmlForm->fields[ $fieldName ]->value = G::replaceDataField($styleData['value'],$result);
    }
    /*** END : Reeplace of @@, @%,...    */

    /*** Rendering of the field  */
    $this->xmlForm->fields[ $fieldName ]->mode = 'view';
    $this->xmlForm->setDefaultValues();
    $this->xmlForm->setValues( $result );
    //var_dump($fieldName, $fieldClass );echo '<br /><br />';

    if ( array_search( 'renderTable', get_class_methods( $fieldClass ) )!== FALSE ) {
      $htmlField = $this->xmlForm->fields[ $fieldName ]->renderTable( $value, $this->xmlForm, true );
      $this->tpl->assign( "value" , $htmlField );
    }

    return $this->fields[$r]['Type'];
  }

  /**
   * Function defaultStyle
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @access public
   * @return string
   */
  function defaultStyle()
  {
    foreach($this->fields as $r => $rval) {
      $this->style[$r]=array( 'showInTable' => '1',
             'titleVisibility' => '1',
             'colWidth' => '150','onclick' => '',
             'event' => ''   );
      //Some widths
      if (!(strpos('  date linknew ',  ' ' . $this->fields[$r]['Type']. ' ')===FALSE))
        $this->style[$r]['colWidth']='70';
      //Data source:
      if (!(strpos('  title button linknew image-text jslink ',  ' ' . $this->fields[$r]['Type']. ' ')===FALSE))
        $this->style[$r]['data']='';   //If the control is a link it shows the label
      else
        $this->style[$r]['data']=$this->fields[$r]['Name']; //ELSE: The data value for that field
      //Hidden fields
      if (!isset($this->style[$r]['showInTable'])) {
        if (!(strpos('  title button endgrid2 submit password ',  ' ' . $this->fields[$r]['Type']. ' ')===FALSE))
        {
          $this->style[$r]['showInTable']='0';
        }
        else
        {
          $this->style[$r]['showInTable']='1';
        }
      }
      //Hidden titles
      if (!(strpos('  linknew button endgrid2 ',  ' ' . $this->fields[$r]['Type']. ' ')===FALSE)) {
        $this->style[$r]['titleVisibility']='0';
      }
      //Align titles
      $this->style[$r]['titleAlign']='center';
      //Align fields
      if (isset($_SESSION['SET_DIRECTION']) && (strcasecmp($_SESSION['SET_DIRECTION'],'rtl')===0))
        $this->style[$r]['align']='right';
      else
        $this->style[$r]['align']='left';

      if (!(strpos(' linknew date ',  ' ' . $this->fields[$r]['Type']. ' ')===FALSE)) {
        $this->style[$r]['align']='center';
      }
    }

    // Adjust the columns width to prevent overflow the page width
    //Render headers
    $totalWidth=0;
    foreach($this->fields as $r => $rval)
      if ($this->style[$r]['showInTable']!='0')
        $totalWidth += $this->style[$r]['colWidth'];
    $this->totalWidth = $totalWidth;
    $maxWidth=1800;
    $proportion=$totalWidth/$maxWidth;
    if ($proportion>1) $this->totalWidth = 1800;
    if ($proportion>1)
      foreach($this->fields as $r => $rval)
        if ($this->style[$r]['showInTable']!='0')
          $this->style[$r]['colWidth']=$this->style[$r]['colWidth']/$proportion;

  }

  /**
   * Function renderTable
   * @author David S. Callizaya S. <davidsantos@colosa.com>
   * @parameter $block : = 'content'(Prints contentBlock only)
   * @access public
   * @return string
   */
  function renderTable( $block = '', $fields = '' )
  {
  	
      //Render Title
    $thereisnotitle=true;
    foreach($this->fields as $r => $rval)
    if ($this->fields[$r]['Type']==='title')
    {
      $this->title = $this->fields[$r]['Label'];
      unset($this->fields[$r]);
      $thereisnotitle=false;
    }
    if ($thereisnotitle)
    {
      $this->title = '';
    }
    
    $oHeadPublisher =& headPublisher::getSingleton();
    $oHeadPublisher->addInstanceModule('leimnud', 'panel');

$time_start = microtime(true);
    $this->prepareQuery( true );
$time_end = microtime(true);  $time = $time_end - $time_start;

    // verify if there are templates folders registered, template and method folders are the same
    $folderTemplate = explode ( '/',$this->template );
    $oPluginRegistry =& PMPluginRegistry::getSingleton();
    if ( $oPluginRegistry->isRegisteredFolder( $folderTemplate[0] ) )
      $templateFile = PATH_PLUGINS . $this->template. '.html';
    else
      $templateFile = PATH_TPL . $this->template. '.html';

    // Prepare the template
    $this->tpl = new TemplatePower( $templateFile );
    $this->tpl->prepare();
    if(is_array($fields)) {
      foreach ( $fields as $key =>$val ) {
        $this->tpl->assign( $key , $val );
      }
    }

    /********** HEAD BLOCK ***************/
    if (($block ==='') || ($block==='head')) {
      $this->tpl->newBlock('headBlock');
      $this->tpl->assign( 'pagedTable_Id' , $this->id );
      $this->tpl->assign( 'pagedTable_Name' , $this->name );
      $this->tpl->assign( 'pagedTable_Height' , $this->xmlForm->height );
      $this->tpl->assign( "title", $this->title);
      
      if (file_exists($this->xmlForm->home . $this->filterForm . '.xml')) {
        $filterForm = new filterForm( $this->filterForm , $this->xmlForm->home );
        if ($this->menu==='') $this->menu= 'gulliver/pagedTable_Options';
      }
      if (file_exists($this->xmlForm->home . $this->menu . '.xml')) {
        $menu = new xmlMenu( $this->menu , $this->xmlForm->home );
        $this->tpl->newBlock('headerBlock');
        $template = PATH_CORE . 'templates' . PATH_SEP . $menu->type . '.html';
        $menu->setValues($this->xmlForm->values);
        $menu->setValues(array( 'PAGED_TABLE_ID' => $this->id ));

        $menu->setValues(array( 'PAGED_TABLE_FAST_SEARCH' => $this->fastSearch ));
        if (isset($filterForm->name)) {
          $menu->setValues(array('SEARCH_FILTER_FORM' => $filterForm->name));
        }
        $this->tpl->assign( 'content' ,  $menu->render( $template , $scriptCode ) );
        $oHeadPublisher->addScriptFile( $menu->scriptURL );
        $oHeadPublisher->addScriptCode( $scriptCode );
      }
      if (file_exists($this->xmlForm->home . $this->filterForm . '.xml')) {
        $this->tpl->newBlock('headerBlock');
        $this->filterForm_Id = $filterForm->id;
        $filterForm->type = 'filterform';
        $filterForm->ajaxServer = '../gulliver/defaultAjax';
        $template = PATH_CORE . 'templates/'  . $filterForm->type . '.html';
        $filterForm->setValues($this->xmlForm->values);
        $filterForm->setValues(array('PAGED_TABLE_ID' => $this->id ));
        $filterForm->setValues(array( 'PAGED_TABLE_FAST_SEARCH' => $this->fastSearch ));
        $this->tpl->assign( 'content' ,  $filterForm->render( $template , $scriptCode ) );
        $oHeadPublisher->addScriptFile( $filterForm->scriptURL );
        $oHeadPublisher->addScriptCode( $scriptCode );
        if (isset($_SESSION)) $_SESSION[$filterForm->id]=$filterForm->values;
      }
    }

    /********** CONTENT BLOCK ***************/
    if (($block ==='') || ($block==='content')) {
      $this->tpl->newBlock('contentBlock');
      $this->tpl->assign('gridWidth','=['. substr($this->gridWidth,1) .']');
      $this->tpl->assign('fieldNames','=['. substr($this->gridFields,1) .']');
      $this->tpl->assign('ajaxUri','="'. addslashes($this->ajaxServer) . '"');
      $this->tpl->assign('currentUri','="'. addslashes($this->ownerPage) . '"');
      $this->tpl->assign('currentOrder','="'. addslashes($this->orderBy) . '"');
      $this->tpl->assign('currentPage','='. $this->currentPage );
      $this->tpl->assign('currentFilter','="' . '"');
      $this->tpl->assign('totalRows','=' . $this->totRows );
      $this->tpl->assign('rowsPerPage','='.$this->rowsPerPage);
      $this->tpl->assign('popupPage','="'. addslashes($this->popupPage) . '"');
      $this->tpl->assign('popupWidth','='.$this->popupWidth);
      $this->tpl->assign('popupHeight','='.$this->popupHeight);
      $this->tpl->assign('pagedTable_Id', $this->id );
      $this->tpl->assign('pagedTable_Name', $this->name );
      $this->tpl->assign("pagedTable_JS" , "{$this->id}.element=document.getElementById('pagedtable[{$this->id}]');");
      $this->renderTitle();

      //Render rows
      if ( $this->criteria->getDbName() == 'dbarray' ) {
        $rs = ArrayBasePeer::doSelectRs ( $this->criteria);
      }
      else {
        $rs = GulliverBasePeer::doSelectRs ( $this->criteria);
      }
      $rs->setFetchmode (ResultSet::FETCHMODE_ASSOC);

      /*
      print "<div class='pagedTableDefault'><table  class='default'>";
      $rs->next();
      $row = $rs->getRow();
      while ( is_array ( $row ) ) {
        print "<tr  class='Row1'>";
        foreach ( $row as $k=>$v ) print "<td>$v</td>";
        print "</tr>";
        $rs->next();
        $row = $rs->getRow();
      }
      print "</table></div>";  die;*/

      $gridRows=0;
      $rs->next();


      //Initialize the array of breakFields for Master Detail View
      foreach($this->masterdetail as $keyMasterDetail => $fieldMasterDetail){
        $breakField[$fieldMasterDetail]="novaluehere";
      }
      $breakFieldKeys=array_flip($this->masterdetail);

      for($j=0;$j< $rs->getRecordCount() ;$j++) {
        $result = $rs->getRow();
        $rs->next();

        $gridRows++;
        $this->tpl->newBlock( "row" );
        $this->tpl->assign( "class" , "Row".(($j%2)+1));
        $this->tdStyle='';
        $this->tdClass='';

        //Start Master Detail: This enable the MasterDEtail view. By JHL November 2008
        if(count($this->masterdetail)>0){
          //TODO: Validate if there is a Field that not exists
          //TODO: Style
          //TODO: Improve Collapse function....
          foreach($this->masterdetail as $keyMasterDetail => $fieldMasterDetail){
            if($breakField[$fieldMasterDetail]!=$result[$fieldMasterDetail]){
              $this->tpl->newBlock( "rowMaster" );
              $this->tpl->newBlock( "fieldMaster" );
              $this->tpl->assign( "alignAttr" , " colspan=".(count($this->fields)*2));
              $this->tpl->assign( "value" , $this->fields[$fieldMasterDetail]['Label'].": ".$this->xmlForm->fields[ $fieldMasterDetail ]->renderTable( $result[$fieldMasterDetail], $this->xmlForm, true ));

              $breakField[$fieldMasterDetail]=$result[$fieldMasterDetail];

              for($i=$breakFieldKeys[$fieldMasterDetail]+1;$i<count($breakField);$i++){
                $breakField[$this->masterdetail[$i]]="novaluehere";
              }
              $rowName=array();
              foreach($breakField as $key => $value){
                if($value!="novaluehere"){
                  $rowName[$key]=$key."_".$value;
                }
              }
              $this->tpl->assign( "masterRowName" , implode(",",$rowName));
              $this->tpl->assign( 'pagedTable_Name' , $this->name );
              $many="";
              $this->tpl->assign( "value1" ,str_pad($many, count($rowName)-1 , "-") );
              $this->tpl->gotoblock("rowMaster");
              $this->tpl->assign( "masterRowName" , "_MD_".implode(",",$rowName));
              $this->tpl->assign( "masterRowClass" , $keyMasterDetail==0?"masterDetailMain":"masterDetailOther");
            }
          }
          $this->tpl->gotoblock("row");
          $this->tpl->assign( "rowName" , implode(",",$rowName));
        }
        //End Master Detail: This enable the MasterDEtail view


        //Merge $result with $xmlForm values (for default valuesSettings)
        if ( is_array ( $this->xmlForm->values ) )
        $result = array_merge($this->xmlForm->values, $result);
        foreach($this->fields as $r => $rval)
        {
          if (strcasecmp($this->fields[$r]['Type'],'cellMark')==0)
          {
            $result1 = $result;
            $result1['row__'] = $j+1;
            $result1 = array_merge($this->xmlForm->values, $result1);
            $this->xmlForm->setDefaultValues();
            $this->xmlForm->setValues( $result1 );
            $this->tdStyle = $this->xmlForm->fields[ $this->fields[$r]['Name'] ]->tdStyle( $result1 , $this->xmlForm );
            $this->tdClass = $this->xmlForm->fields[ $this->fields[$r]['Name'] ]->tdClass( $result1 , $this->xmlForm );
          }
          elseif ($this->style[$r]['showInTable'] != '0' )
          {
            if (($this->style[$r]['showInTable'] != '0' )&&(!(in_array($this->fields[$r]['Name'],$this->masterdetail))))
            $this->renderField($j+1,$r,$result);

          }
        }
      }

      $this->tpl->assign('_ROOT.gridRows','='. $gridRows);  //number of rows in the current page
      $this->tpl->newBlock('rowTag');
      $this->tpl->assign('rowId','insertAtLast');

      if( $this->currentPage > 1 ) {
        $firstUrl = $this->ownerPage . '?order=' . $this->orderBy . '&page=1';
        $firstAjax = $this->id . ".doGoToPage(1);return false;";
        $prevpage = $this->currentPage - 1;
        $prevUrl  = $this->ownerPage . '?order=' . $this->orderBy . '&page=' . $prevpage;
        $prevAjax = $this->id . ".doGoToPage(".$prevpage.");return false;";
        $first = "<a href=\"" . htmlentities( $firstUrl  , ENT_QUOTES , 'utf-8' ) . "\" onclick=\"".$firstAjax."\" class='firstPage'>&nbsp;</a>";
        $prev  = "<a href=\"" . htmlentities( $prevUrl  , ENT_QUOTES , 'utf-8' ) . "\"  onclick=\"".$prevAjax."\" class='previousPage'>&nbsp;</a>";
      }
      else
      {
        $first = "<a class='noFirstPage'>&nbsp;</a>";
        $prev  = "<a class='noPreviousPage'>&nbsp;</a>";
      }
      if( $this->currentPage < $this->totPages ) {
        $lastUrl = $this->ownerPage . '?order=' . $this->orderBy . '&page=' . $this->totPages;
        $lastAjax = $this->id . ".doGoToPage(" .$this->totPages.");return false;";
        $nextpage = $this->currentPage + 1;
        $nextUrl  = $this->ownerPage . '?order=' . $this->orderBy . '&page=' . $nextpage;
        $nextAjax = $this->id . ".doGoToPage(" .$nextpage.");return false;";
        $next = "<a href=\"" . htmlentities( $nextUrl   , ENT_QUOTES , 'utf-8' ) . "\" onclick=\"".$nextAjax."\" class='nextPage'>&nbsp;</a>";
        $last = "<a href=\"" . htmlentities( $lastUrl   , ENT_QUOTES , 'utf-8' ) . "\" onclick=\"".$lastAjax."\" class='lastPage'>&nbsp;</a>";
      }
      else
      {
        $next = "<a class='noNextPage'>&nbsp;</a>";
        $last = "<a class='noLastPage'>&nbsp;</a>";
      }
      $pagesEnum='';
      for ($r=1;$r<=$this->totPages;$r++)
      if (($r>=($this->currentPage-5))&&($r<=($this->currentPage+5)))
      {
        $pageAjax = $this->id . ".doGoToPage(" .$r.");return false;";
        if ($r!=$this->currentPage)
          $pagesEnum.="&nbsp;<a href=\"" . htmlentities( $this->ownerPage . '?order=' . $this->orderBy . '&page=' . $r  , ENT_QUOTES , 'utf-8' ) . "\" onclick=\"".$pageAjax."\">".$r."</a>";
        else
          $pagesEnum.="&nbsp;<a>".$r."</a>";
      }
      if ($this->totRows === 0) {
        $this->tpl->newBlock( 'norecords' );
        $this->tpl->assign( "columnCount", $this->colCount);
        $noRecordsFound='ID_NO_RECORDS_FOUND';
        if (G::LoadTranslation($noRecordsFound)) $noRecordsFound = G::LoadTranslation($noRecordsFound);
        $this->tpl->assign( "noRecordsFound", $noRecordsFound);

      }
      if (!$this->disableFooter) {
        $this->tpl->newBlock( "bottomFooter" );
        $this->tpl->assign( "columnCount", $this->colCount);
        $this->tpl->assign( "pagedTableId" , $this->id );
        if (($this->totRows !== 0)) {
          if ($this->totPages>1)
          {
            $this->tpl->assign( "first" , $first );
            $this->tpl->assign( "prev" ,  $prev );
            $this->tpl->assign( "next" , $next );
            $this->tpl->assign( "last" , $last );
          }
          $this->tpl->assign( "currentPage" , $this->currentPage );
          $this->tpl->assign( "totalPages" , $this->totPages );
          $firstRow = ($this->currentPage-1) * $this->rowsPerPage+1;
          $lastRow = $firstRow+$rs->getRecordCount()-1;
          $this->tpl->assign( "firstRow" , $firstRow );
          $this->tpl->assign( "lastRow" , $lastRow );
          $this->tpl->assign( "totalRows" , $this->totRows );
        } else {
          $this->tpl->assign( "indexStyle", 'visibility:hidden;');
        }
        if ($this->searchBy) {
          $this->tpl->assign( "fastSearchValue" , $this->fastSearch );
        } else {
          $this->tpl->assign( "fastSearchStyle" , 'visibility:hidden;' );
        }
        if ($this->addRow)  if($this->sqlInsert!='')$this->tpl->assign( "insert" , '<a href="#" onclick="pagedTable.event=\'Insert\';popup(\''.$this->popupPage.'\');return false;">'./*G::LoadXml('labels','ID_ADD_NEW')*/ 'ID_ADD_NEW' .'</a>' );
        $this->tpl->assign("pagesEnum", $pagesEnum);
    }

?>

<script language='JavaScript' >
var <?=$this->id?><?=($this->name != '' ? '='.$this->name : '')?>=new G_PagedTable();
<?=$this->id?>.id<?='="'. addslashes($this->id) . '"'?>;
<?=$this->id?>.name<?='="'. addslashes($this->name) . '"'?>;
<?=$this->id?>.ajaxUri<?='="'. addslashes($this->ajaxServer) . '?ptID='.$this->id.'"'?>;
<?=$this->id?>.currentOrder<?='="'. addslashes($this->orderBy) . '"'?>;
<?=$this->id?>.currentFilter;
<?=$this->id?>.currentPage<?='='. $this->currentPage?>;
<?=$this->id?>.totalRows<?='='.$this->totRows ?>;
<?=$this->id?>.rowsPerPage<?='='.$this->rowsPerPage?>;
<?=$this->id?>.popupPage<?='="'. addslashes($this->popupPage) . '?ptID='.$this->id.'"'?>;
<?=$this->id?>.onUpdateField<?='="'. addslashes($this->onUpdateField) . '"'?>;
<?=$this->id?>.shownFields<?='='.$this->shownFields ?>;

var panelPopup;
var popupWidth<?='='.$this->popupWidth?>;
var popupHeight<?='='.$this->popupHeight?>;
</script>
<?php
    }
    /********** CLOSE BLOCK ***************/
    if (($block ==='') || ($block==='close')) {
      $this->tpl->newBlock( "closeBlock" );
    }

    //By JHL
    //Put the content of the table in a variable to be used for other puposes
    //Like rendering as PDF
    global $_TABLE_CONTENT_;
    $_TABLE_CONTENT_=$this->tpl->getOutputContent();

    $this->tpl->printToScreen();
    unset($this->tpl);
    //unset($this->dbc);
    //unset($this->ses);

    $_SESSION['pagedTable['.$this->id.']']= serialize($this);
    return;
  }

  function printForm($filename,$data=array())
  {
    global $G_PUBLISH;
    $G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', $filename, '', $data , $this->popupSubmit);

    G::RenderPage( "publish" , "blank" );
  }
}
