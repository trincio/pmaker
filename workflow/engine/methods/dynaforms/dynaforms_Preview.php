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

if (($RBAC_Response=$RBAC->userCanAccess("PM_FACTORY"))!=1) return $RBAC_Response;

  //G::genericForceLogin( 'WF_MYINFO' , 'login/noViewPage', $urlLogin = 'login/login' );

  G::LoadClass('toolBar');
  G::LoadClass('dynaFormField');

  if (!(isset($_POST['A']) && $_POST['A']!==''))  return;

  $file = G::decrypt( $_POST['A'] , URL_KEY );

 	$G_PUBLISH = new Publisher;
 	$G_HEADER->clearScripts();
  $form = new Form( $file , PATH_DYNAFORM, SYS_LANG, true );
  switch(basename($form->template,'.html'))
  {
    case 'grid': $template='grid';break;
    default: $template='xmlform';
  }
  $G_PUBLISH->AddContent('dynaform', $template , $file, '', 
  	array(
  	  '__DYNAFORM_OPTIONS'=> array(
      		'PREVIOUS_STEP' => '#',
      		'NEXT_STEP' => '#',
      		'PREVIOUS_ACTION' => 'return false;',
      		'NEXT_ACTION' => 'return false;'
    		)
  	), '');
  G::RenderPage('publish','raw');


 /* $toolbar = new ToolBar( '/dynaforms/dynaforms_Toolbar' , PATH_XMLFORM, SYS_LANG, false );

  print($toolbar->render( $toolbar->template , $script ));*/


  //$form = new Form( $file , PATH_DYNAFORM, SYS_LANG, true );

  //print($form->render( $form->template , $script ));


?>