<?php
/**
 * class.xmlfield_InputPM.php
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
class XmlForm_Field_TextPM extends XmlForm_Field_SimpleText
{
  var $size=15;
	var $maxLength=64;
	var $validate='Any';
	var $mask = '';
	var $defaultValue='';
	var $required=false;
	var $dependentFields='';
	var $linkField='';
//Possible values:(-|UPPER|LOWER|CAPITALIZE)
	var $strTo='';
	var $readOnly=false;
	var $sqlConnection=0;
	var $sql='';
	var $sqlOption=array();
	//Atributes only for grids
	var $formula	   = '';
	var $function    = '';
	var $replaceTags = 0;
	var $showVars    = 0;
	var $process     = '';
	var $symbol      = '@@';
  /**
   * Function render
   * @author Julio Cesar Laura Avenda�o <juliocesar@colosa.com>
   * @access public
   * @parameter string value
   * @parameter string owner
   * @return string
   */
  function render( $value = NULL , $owner = NULL )
  {
		//$this->executeSQL();
		//if (isset($this->sqlOption)) {
		//  reset($this->sqlOption);
		//  $firstElement=key($this->sqlOption);
	  //	if (isset($firstElement)) $value = $firstElement;
	  //}
	  //NOTE: string functions must be in G class
	  if ($this->strTo==='UPPER') $value = strtoupper($value);
	  if ($this->strTo==='LOWER') $value = strtolower($value);
	  //if ($this->strTo==='CAPITALIZE') $value = strtocapitalize($value);
    $onkeypress = G::replaceDataField( $this->onkeypress, $owner->values );
    if ($this->replaceTags == 1) {
      $value = G::replaceDataField( $value, $owner->values );
    }
	  if ($this->showVars == 1) {
	  	$this->process = G::replaceDataField($this->process, $owner->values );
	  	//$sShowVars = '&nbsp;<a href="#" onclick="showDynaformsFormVars(\'form['.$this->name.']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;">' . $this->symbol . '</a>';
	  	$sShowVars = '&nbsp;<input type="button" value="' . $this->symbol . '" onclick="showDynaformsFormVars(\'form['.$this->name.']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;"/>';
	  }
	  else {
	  	$sShowVars = '';
	  }
	  if ($this->mode==='edit') {
	    if ($this->readOnly)
		    return '<input class="module_app_input___gray" id="form['.$this->name.']" name="form['.$this->name.']" type ="text" size="'.$this->size.'" maxlength="'.$this->maxLength.'" value=\''.$this->htmlentities( $value , ENT_COMPAT, 'utf-8').'\' readOnly="readOnly" style="'.htmlentities( $this->style , ENT_COMPAT, 'utf-8').'" onkeypress="'.htmlentities( $onkeypress , ENT_COMPAT, 'utf-8').'"/>' . $sShowVars;
		  else
		    return '<input class="module_app_input___gray" id="form['.$this->name.']" name="form['.$this->name.']" type ="text" size="'.$this->size.'" maxlength="'.$this->maxLength.'" value=\''.$this->htmlentities( $value , ENT_COMPAT, 'utf-8').'\' style="'.htmlentities( $this->style , ENT_COMPAT, 'utf-8').'" onkeypress="'.htmlentities( $onkeypress , ENT_COMPAT, 'utf-8').'"/>' . $sShowVars;
		} elseif ($this->mode==='view') {
		    return '<input class="module_app_input___gray" id="form['.$this->name.']" name="form['.$this->name.']" type ="text" size="'.$this->size.'" maxlength="'.$this->maxLength.'" value=\''.$this->htmlentities( $value , ENT_COMPAT, 'utf-8').'\' style="display:none;'.htmlentities( $this->style , ENT_COMPAT, 'utf-8').'" onkeypress="'.htmlentities( $onkeypress , ENT_COMPAT, 'utf-8').'"/>' .
		      $this->htmlentities( $value , ENT_COMPAT, 'utf-8');
		} else {
		  return $this->htmlentities( $value , ENT_COMPAT, 'utf-8');
		}
	}
  /**
   * Function renderGrid
   * @author Julio Cesar Laura Avenda�o <juliocesar@colosa.com>
   * @access public
   * @parameter string values
   * @parameter string owner
   * @return string
   */
  function renderGrid( $values=array() , $owner )
  {
    $result=array();$r=1;
    foreach($values as $v)  {
    	if ($this->replaceTags == 1) {
        $v = G::replaceDataField( $v, $owner->values );
      }
      if ($this->showVars == 1) {
	    	$this->process = G::replaceDataField($this->process, $owner->values );
	    	//$sShowVars = '&nbsp;<a href="#" onclick="showDynaformsFormVars(\'form['.$owner->name .']['.$r.']['.$this->name.']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;">' . $this->symbol . '</a>';
	    		$sShowVars = '&nbsp;<input type="button" value="' . $this->symbol . '" onclick="showDynaformsFormVars(\'form['.$owner->name .']['.$r.']['.$this->name.']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;"/>';
	    }
	    else {
	    	$sShowVars = '';
	    }
  	  if ($this->mode==='edit') {
  	    if ($this->readOnly)
  		    $result[] = '<input class="module_app_input___gray" id="form['. $owner->name .']['.$r.']['.$this->name.']" name="form['. $owner->name .']['.$r.']['.$this->name.']" type ="text" size="'.$this->size.'" maxlength="'.$this->maxLength.'" value="'.$this->htmlentities( $v , ENT_COMPAT, 'utf-8').'" readOnly="readOnly" style="'.htmlentities( $this->style , ENT_COMPAT, 'utf-8').'"/>' . $sShowVars;
  		  else
  		    $result[] = '<input class="module_app_input___gray" id="form['. $owner->name .']['.$r.']['.$this->name.']" name="form['. $owner->name .']['.$r.']['.$this->name.']" type ="text" size="'.$this->size.'" maxlength="'.$this->maxLength.'" value="'.$this->htmlentities( $v , ENT_COMPAT, 'utf-8').'" style="'.htmlentities( $this->style , ENT_COMPAT, 'utf-8').'"/>' . $sShowVars;
  		} elseif ($this->mode==='view') {
  		    $result[] = $this->htmlentities( $v , ENT_COMPAT, 'utf-8');
  		} else {
  		    $result[] = $this->htmlentities( $v , ENT_COMPAT, 'utf-8');
  		}
      $r++;
    }
    return $result;
	}

	function attachEvents($element) {
		return "myForm.aElements[i] = new G_Text(myForm, $element,'{$this->name}');
      myForm.aElements[i].setAttributes(" . $this->getAttributes() . ");";
	}
}

class XmlForm_Field_TextareaPM extends XmlForm_Field
{
  var $rows     = 12;
	var $cols     = 40;
	var $required = false;
	var $readOnly = false;
	var $wrap     = 'OFF';
	var $showVars = 0;
	var $process  = '';
	var $symbol   = '@@';
  /**
   * Function render
   * @author Julio Cesar Laura Avendao <juliocesar@colosa.com>
   * @access public
   * @parameter string value
   * @return string
   */
  function render( $value = NULL, $owner )
  {
    if ($this->showVars == 1) {
    	$this->process = G::replaceDataField($this->process, $owner->values );
	  	$sShowVars = '&nbsp;<input type="button" value="' . $this->symbol . '" onclick="showDynaformsFormVars(\'form['.$this->name.']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;"/>';
	  }
	  else {
	  	$sShowVars = '';
	  }
    if ($this->mode==='edit') {
	    if ($this->readOnly)
    		return '<textarea id="form['.$this->name.']" name="form['.$this->name.']" cols="'.$this->cols.'" rows="'.$this->rows.'" style="'.$this->style.'" wrap="'.htmlentities($this->wrap,ENT_QUOTES,'UTF-8').'" class="FormTextPM" readOnly>'.$this->htmlentities( $value ,ENT_COMPAT,'utf-8').'</textarea>' . $sShowVars;
	    else
    		return '<textarea id="form['.$this->name.']" name="form['.$this->name.']" cols="'.$this->cols.'" rows="'.$this->rows.'" style="'.$this->style.'" wrap="'.htmlentities($this->wrap,ENT_QUOTES,'UTF-8').'" class="FormTextPM" >'.$this->htmlentities( $value ,ENT_COMPAT,'utf-8').'</textarea>' . $sShowVars;
		} elseif ($this->mode==='view') {
  		return '<textarea id="form['.$this->name.']" name="form['.$this->name.']" cols="'.$this->cols.'" rows="'.$this->rows.'" readOnly style="border:0px;backgroud-color:inherit;'.$this->style.'" wrap="'.htmlentities($this->wrap,ENT_QUOTES,'UTF-8').'"  class="FormTextPM" >'.$this->htmlentities( $value ,ENT_COMPAT,'utf-8').'</textarea>';
		} else {
  		return '<textarea id="form['.$this->name.']" name="form['.$this->name.']" cols="'.$this->cols.'" rows="'.$this->rows.'" style="'.$this->style.'" wrap="'.htmlentities($this->wrap,ENT_QUOTES,'UTF-8').'"  class="FormTextArea" >'.$this->htmlentities( $value ,ENT_COMPAT,'utf-8').'</textarea>';
		}
	}
  /**
   * Function renderGrid
   * @author Julio Cesar Laura Avenda�o <juliocesar@colosa.com>
   * @access public
   * @parameter string value
   * @parameter string owner
   * @return string
   */
  function renderGrid( $values = NULL , $owner )
  {
    $result=array();$r=1;
    foreach($values as $v)  {
    	if ($this->showVars == 1) {
      	$this->process = G::replaceDataField($this->process, $owner->values );
	    //$sShowVars = '&nbsp;<a href="#" onclick="showDynaformsFormVars(\'form['.$owner->name .']['.$r.']['.$this->name.']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;">' . $this->symbol . '</a>';
	    	$sShowVars = '&nbsp;<input type="button" value="' . $this->symbol . '" onclick="showDynaformsFormVars(\'form['.$owner->name .']['.$r.']['.$this->name.']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;"/>';
	    }
	    else {
	    	$sShowVars = '';
	    }
  	  if ($this->mode==='edit') {
  	    if ($this->readOnly)
  		    $result[] = '<input class="module_app_input___gray" id="form['. $owner->name .']['.$r.']['.$this->name.']" name="form['. $owner->name .']['.$r.']['.$this->name.']" type ="text" size="'.$this->size.'" maxlength="'.$this->maxLength.'" value=\''.$this->htmlentities( $v , ENT_COMPAT, 'utf-8').'\' readOnly="readOnly"/>' . $sShowVars;
  		  else
  		    $result[] = '<input class="module_app_input___gray" id="form['. $owner->name .']['.$r.']['.$this->name.']" name="form['. $owner->name .']['.$r.']['.$this->name.']" type ="text" size="'.$this->size.'" maxlength="'.$this->maxLength.'" value=\''.$this->htmlentities( $v , ENT_COMPAT, 'utf-8').'\' />' . $sShowVars;
	  } elseif ($this->mode==='view') {
			if(stristr($_SERVER['HTTP_USER_AGENT'], 'iPhone'))
			{
				//$result[] = '<div style="overflow:hidden;height:25px;padding:0px;margin:0px;">'.$this->htmlentities( $v , ENT_COMPAT, 'utf-8').'</div>';
				$result[] =  $this->htmlentities( $v , ENT_COMPAT, 'utf-8');
			}
			else
			{
				//$result[] = '<div style="overflow:hidden;width:inherit;height:2em;padding:0px;margin:0px;">'.$this->htmlentities( $v , ENT_COMPAT, 'utf-8').'</div>';
				$result[] =  $this->htmlentities( $v , ENT_COMPAT, 'utf-8');
			}

  		} else {
  		    $result[] = $this->htmlentities( $v , ENT_COMPAT, 'utf-8');
  		}
      $r++;
    }
    return $result;
	}
}

class XmlForm_Field_hours extends XmlForm_Field_SimpleText
{
  var $size=15;
	var $maxLength=64;
	var $validate='Any';
	var $mask = '';
	var $defaultValue='';
	var $required=false;
	var $dependentFields='';
	var $linkField='';
//Possible values:(-|UPPER|LOWER|CAPITALIZE)
	var $strTo='';
	var $readOnly=false;
	var $sqlConnection=0;
	var $sql='';
	var $sqlOption=array();
	//Atributes only for grids
	var $formula	   = '';
	var $function    = '';
	var $replaceTags = 0;
	var $showVars    = 0;
	var $process     = '';
	var $symbol      = '@@';

  /**
   * Function render
   * @author Julio Cesar Laura Avenda?o <juliocesar@colosa.com>
   * @access public
   * @parameter string value
   * @parameter string owner
   * @return string
   */
  function render( $value = NULL , $owner = NULL )
  {
	  if ($this->strTo==='UPPER') $value = strtoupper($value);
	  if ($this->strTo==='LOWER') $value = strtolower($value);
	  //if ($this->strTo==='CAPITALIZE') $value = strtocapitalize($value);
    $onkeypress = G::replaceDataField( $this->onkeypress, $owner->values );
    if ($this->replaceTags == 1) {
      $value = G::replaceDataField( $value, $owner->values );
    }
	  if ($this->showVars == 1) {
	  	$this->process = G::replaceDataField($this->process, $owner->values );
	  //$sShowVars = '&nbsp;<a href="#" onclick="showDynaformsFormVars(\'form['.$this->name.']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;">' . $this->symbol . '</a>';
	  	$sShowVars = '&nbsp;<input type="button" value="' . $this->symbol . '" onclick="showDynaformsFormVars(\'form['.$this->name.']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;"/>';
	  }
	  else {
	  	$sShowVars = '';
	  }
	  if ($this->mode==='edit') {
	    if ($this->readOnly)
		    return '<input class="module_app_input___gray" id="form['.$this->name.']" name="form['.$this->name.']" type ="text" size="'.$this->size.'" maxlength="'.$this->maxLength.'" value=\''.$this->htmlentities( $value , ENT_COMPAT, 'utf-8').'\' readOnly="readOnly" style="'.htmlentities( $this->style , ENT_COMPAT, 'utf-8').'" onkeypress="'.htmlentities( $onkeypress , ENT_COMPAT, 'utf-8').'"/>' . $sShowVars;
		  else
		    return '<input class="module_app_input___gray" id="form['.$this->name.']" name="form['.$this->name.']" type ="text" size="'.$this->size.'" maxlength="'.$this->maxLength.'" value=\''.$this->htmlentities( $value , ENT_COMPAT, 'utf-8').'\' style="'.htmlentities( $this->style , ENT_COMPAT, 'utf-8').'" onkeypress="'.htmlentities( $onkeypress , ENT_COMPAT, 'utf-8').'"/>' . $sShowVars;
		} elseif ($this->mode==='view') {
		    return '<input class="module_app_input___gray" id="form['.$this->name.']" name="form['.$this->name.']" type ="text" size="'.$this->size.'" maxlength="'.$this->maxLength.'" value=\''.$this->htmlentities( $value , ENT_COMPAT, 'utf-8').'\' style="display:none;'.htmlentities( $this->style , ENT_COMPAT, 'utf-8').'" onkeypress="'.htmlentities( $onkeypress , ENT_COMPAT, 'utf-8').'"/>' .
		      $this->htmlentities( $value , ENT_COMPAT, 'utf-8');
		} else {
		  return $this->htmlentities( $value , ENT_COMPAT, 'utf-8');
		}
	}
  /**
   * Function renderGrid
   * @author Julio Cesar Laura Avenda?o <juliocesar@colosa.com>
   * @access public
   * @parameter string values
   * @parameter string owner
   * @return string
   */
  function renderGrid( $values=array() , $owner )
  {
    $result=array();$r=1;
    foreach($values as $v)  {
    	if ($this->replaceTags == 1) {
        $v = G::replaceDataField( $v, $owner->values );
      }
      if ($this->showVars == 1) {
	    	$this->process = G::replaceDataField($this->process, $owner->values );
	    	//$sShowVars = '&nbsp;<a href="#" onclick="showDynaformsFormVars(\'form['.$owner->name .']['.$r.']['.$this->name.']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;">' . $this->symbol . '</a>';
	    	$sShowVars = '&nbsp;<input type="button" value="' . $this->symbol . '" onclick="showDynaformsFormVars(\'form['.$owner->name .']['.$r.']['.$this->name.']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;"/>';
	    }
	    else {
	    	$sShowVars = '';
	    }
  	  if ($this->mode==='edit') {
  	    if ($this->readOnly)
  		    $result[] = '<input class="module_app_input___gray" id="form['. $owner->name .']['.$r.']['.$this->name.']" name="form['. $owner->name .']['.$r.']['.$this->name.']" type ="text" size="'.$this->size.'" maxlength="'.$this->maxLength.'" value="'.$this->htmlentities( $v , ENT_COMPAT, 'utf-8').'" readOnly="readOnly" style="'.htmlentities( $this->style , ENT_COMPAT, 'utf-8').'"/>' . $sShowVars;
  		  else
  		    $result[] = '<input class="module_app_input___gray" id="form['. $owner->name .']['.$r.']['.$this->name.']" name="form['. $owner->name .']['.$r.']['.$this->name.']" type ="text" size="'.$this->size.'" maxlength="'.$this->maxLength.'" value="'.$this->htmlentities( $v , ENT_COMPAT, 'utf-8').'" style="'.htmlentities( $this->style , ENT_COMPAT, 'utf-8').'"/>' . $sShowVars;
  		} elseif ($this->mode==='view') {
  		    $result[] = '<p align="'.$this->align.'">'.$this->htmlentities( number_format($v, 2), ENT_COMPAT, 'utf-8').'</p>';
  		} else {
  		    $result[] = '<p align="'.$this->align.'">'.$this->htmlentities( number_format($v, 2), ENT_COMPAT, 'utf-8').'</p>';
  		}
      $r++;
    }
    return $result;
	}

	function attachEvents($element) {
		return "myForm.aElements[i] = new G_Text(myForm, $element,'{$this->name}');
      myForm.aElements[i].setAttributes(" . $this->getAttributes() . ");";
	}
}

function getDynaformsVars($sProcessUID, $bSystemVars = true) {
	$aFields = array();
	$aFieldsNames = array();
	if ($bSystemVars) {
		$aAux = G::getSystemConstants();
		foreach ($aAux as $sName => $sValue) {
			$aFields[] = array('sName' => $sName, 'sType' => 'system', 'sLabel'=> 'System variable');
		}
	}
	require_once 'classes/model/Dynaform.php';
	$oCriteria = new Criteria('workflow');
	$oCriteria->addSelectColumn(DynaformPeer::DYN_FILENAME);
	$oCriteria->add(DynaformPeer::PRO_UID, $sProcessUID);
	$oCriteria->add(DynaformPeer::DYN_TYPE, 'xmlform');
	$oDataset = DynaformPeer::doSelectRS($oCriteria);
	$oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
	$oDataset->next();
	while ($aRow = $oDataset->getRow()) {
		if (file_exists(PATH_DYNAFORM . PATH_SEP . $aRow['DYN_FILENAME'] . '.xml')) {
			$G_FORM  = new Form($aRow['DYN_FILENAME'], PATH_DYNAFORM, SYS_LANG);
			if (($G_FORM->type == 'xmlform') || ($G_FORM->type == '')) {
				foreach($G_FORM->fields as $k => $v) {
					if (($v->type != 'title')  && ($v->type != 'subtitle') && ($v->type != 'link')       &&
						($v->type != 'file')   && ($v->type != 'button')   && ($v->type != 'reset')      &&
						($v->type != 'submit') && ($v->type != 'listbox')  && ($v->type != 'checkgroup') &&
						($v->type != 'grid')   && ($v->type != 'javascript')) {
						if (!in_array($k, $aFieldsNames)) {
							$aFields[] = array(
								'sName' => $k,
								'sType' => $v->type,
								'sLabel'=> $v->label
							);
							$aFieldsNames[] = $k;
						}
					}
				}
			}
		}
		$oDataset->next();
	}
	return $aFields;
}

function getGridsVars($sProcessUID) {
	$aFields = array();
	$aFieldsNames = array();
	require_once 'classes/model/Dynaform.php';
	$oCriteria = new Criteria('workflow');
	$oCriteria->addSelectColumn(DynaformPeer::DYN_FILENAME);
	$oCriteria->add(DynaformPeer::PRO_UID, $sProcessUID);
	$oDataset = DynaformPeer::doSelectRS($oCriteria);
  $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
  $oDataset->next();
  while ($aRow = $oDataset->getRow()) {
  	$G_FORM  = new Form($aRow['DYN_FILENAME'], PATH_DYNAFORM, SYS_LANG);
  	if ($G_FORM->type == 'xmlform') {
  	  foreach($G_FORM->fields as $k => $v) {
	    	if ($v->type == 'grid') {
	    	  if (!in_array($k, $aFieldsNames)) {
	    	    $aFields[] = array('sName' => $k, 'sXmlForm' => str_replace($sProcessUID . '/', '', $v->xmlGrid));
	    	    $aFieldsNames[] = $k;
	    	  }
	      }
	    }
	  }
  	$oDataset->next();
  }
	return $aFields;
}

class XmlForm_Field_CheckBoxTable extends XmlForm_Field_Checkbox
{
  /**
   * Function render
   * @author The Answer
   * @access public
   * @parameter string value
   * @parameter string owner
   * @return string
   */
  function render( $value = NULL , $owner = NULL )
  {
  	//$optionName = $owner->values['USR_UID'];
  	$optionName = $value;
  	$onclick = (($this->onclick)? ' onclick="' . G::replaceDataField( $this->onclick, $owner->values )  . '" ' : '');

    $html ='<input class="FormCheck" id="form['.$this->name.']['.$optionName.']" name="form['.$this->name.
                ']['.$optionName.']" type=\'checkbox\' value="'. $value . '"' . $onclick .
                '> <span class="FormCheck"></span></input>';
    return $html;
  }

}
?>