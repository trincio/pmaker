<?php
/**
 * $Id$
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * You can contact Colosa Inc, 2655 Le Jeune Road, Suite 1112, Coral Gables, 
 * FL 33134, USA or email info@colosa.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * ProcessMaker" logo and retain the original copyright notice. If the display
 * of the logo is not reasonably feasible for technical reasons, the
 * Appropriate Legal Notices must display the words "Powered by ProcessMaker"
 * and retain the original copyright notice.
 * -
 */

	G::LoadSystem('pagedTable');
	G::LoadInclude('ajax');

	$id=get_ajax_value('ptID');
	$ntable= unserialize($_SESSION['pagedTable['.$id.']']);
	$page=get_ajax_value('page');
	$function=get_ajax_value('function');

  if (isset($ntable->filterForm_Id) && ($ntable->filterForm_Id!=='')) {
    $filterForm=new filterForm(G::getUIDName( $ntable->filterForm_Id ));
    $filterForm->values=$_SESSION[$filterForm->id];
    parse_str( urldecode(get_ajax_value('filter')) , $newValues);
    if (isset($newValues['form'])) {
      $filterForm->setValues($newValues['form']);
      $filter = array();
      foreach($filterForm->fields as $fieldName => $field ){
        if (($field->dataCompareField!=='') && (isset($newValues['form'][$fieldName])))
          $filter[$field->dataCompareField] = $filterForm->values[$fieldName];
          $ntable->filterType[$field->dataCompareField] = $field->dataCompareType;
      }
      $ntable->filter = $filter;//G::http_build_query($filter);
    }
  }
  $fastSearch=get_ajax_value('fastSearch');
  if (isset($fastSearch)) $ntable->fastSearch= urldecode($fastSearch);
  $orderBy=get_ajax_value('order');
  if (isset($orderBy)) {
	  $orderBy=urldecode($orderBy);
  	$ntable->orderBy=$orderBy;
  }
	if (isset($page) && $page!=='') $ntable->currentPage=(int) $page;
	if (function_exists('pagedTable_BeforeQuery')) pagedTable_BeforeQuery($ntable);
	$ntable->prepareQuery();
	switch ($function)
	{
	case "showHideField":
	  $field=get_ajax_value('field');
	  $ntable->style[$field]['showInTable']=
	    ($ntable->style[$field]['showInTable']==='0')?'1':'0';
	  break;
	case "paint":
		break;
	case "delete":
		$ntable->prepareQuery();
		parse_str(get_ajax_value('field'),$field);
		foreach($field as $key => $value) $field[$key]=urldecode($value);
		$ntable->ses->execute($ntable->replaceDataField($ntable->sqlDelete,$field));
		break;
	case "update":
		$ntable->prepareQuery();
		parse_str(get_ajax_value('field'),$field);
		foreach($field as $key => $value) $field[$key]=urldecode($value);
		parse_str(get_ajax_value('update'),$fieldup);
		foreach($fieldup as $key => $value) $field['new'.$key]=urldecode($value); //join
		$ntable->ses->execute($ntable->replaceDataField($ntable->sqlUpdate,$field));
		break;
	case "insert":
		$ntable->prepareQuery();
		parse_str(get_ajax_value('field'),$field);
		foreach($field as $key => $value) $field[$key]=urldecode($value);
		$ntable->ses->execute($ntable->replaceDataField($ntable->sqlInsert,$field));
		break;
	case "printForm":
		parse_str(get_ajax_value('field'),$field);
		parse_str(get_ajax_value('field'),$field);
		foreach($field as $key => $value) $field[$key]=urldecode($value);
		$ntable->printForm(get_ajax_value('filename'),$field);
		return ;
	}
	$ntable->renderTable( 'content' );
  G::LoadClass('configuration');
  $conf = new Configurations();
  $conf->setConfig($ntable->__Configuration,$ntable,$conf->aConfig);
  $conf->saveConfig('pagedTable',$ntable->__OBJ_UID,'',$_SESSION['USER_LOGGED'],'');

?>
