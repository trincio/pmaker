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

  //to do: improve the way to pass two or more parameters in the paged-table ( link )
    
  $aux = explode ( '|', $_GET['id'] );
  $category = str_replace ( '"', '', $aux[0] );
  $id       = str_replace ( '"', '', $aux[1] );
  

  require_once ( "classes/model/Translation.php" );
  //if exists the row in the database propel will update it, otherwise will insert.
  $tr = TranslationPeer::retrieveByPK( $category, $id, 'en' );
  
  if ( ( is_object ( $tr ) &&  get_class ($tr) == 'Translation' ) ) { 
     $fields['trn_category'] = $tr->getTrnCategory();
     $fields['trn_id']       = $tr->getTrnId();
     $fields['trn_value']    = $tr->getTrnValue();
  }
  else
    $fields = array();  
  
  $G_MAIN_MENU = 'tools';
  $G_SUB_MENU = 'toolsTranslations';
  $G_ID_MENU_SELECTED = 'TOOLS';
  $G_ID_SUB_MENU_SELECTED = 'ADD_TRANSLATION';

  $G_PUBLISH = new Publisher;
  $dbc = new DBConnection;
  $ses = new DBSession($dbc);
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->SetTo( $dbc ); 
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'tools/translationAdd', '', $fields, 'translationsSave' );
  G::RenderPage('publish');   
?>