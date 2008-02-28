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
/*
 * Created on 21/12/2007
 *
 */
  G::LoadClass('dynaformEditor');
  G::LoadClass('toolBar');
  G::LoadClass('dynaFormField');

  //G::LoadClass('configuration');
  $G_MAIN_MENU            = 'processmaker';
  $G_SUB_MENU             = 'processes';
  $G_ID_MENU_SELECTED     = 'PROCESSES';
  $G_ID_SUB_MENU_SELECTED = 'FIELDS';

  $PRO_UID=isset($_GET['PRO_UID'])?$_GET['PRO_UID']:'0';
  $DYN_UID=(isset($_GET['DYN_UID'])) ? urldecode($_GET['DYN_UID']):'0';

  if ($PRO_UID==='0') return;
  $process = new Process;
  if ($process->exists($PRO_UID))
  {
    $process->load( $PRO_UID );
  }
  else
  {
    //TODO
    print("$PRO_UID doesnt exists, continue? yes");
  }

  $dynaform = new dynaform;

  if ($dynaform->exists($DYN_UID))
  {
    $dynaform->load( $DYN_UID );
  }
  else
  {
  	/* New Dynaform
  	 *
  	 */
    $dynaform->create(array('PRO_UID'=>$PRO_UID));
  }

  $editor=new dynaformEditor($_POST);
  $editor->file=$dynaform->getDynFilename();
  $editor->home=PATH_DYNAFORM;
  $editor->title=$dynaform->getDynTitle();
  $editor->dyn_uid=$dynaform->getDynUid();
  $editor->pro_uid=$dynaform->getProUid();
  $editor->dyn_type=$dynaform->getDynType();
  $editor->dyn_title=$dynaform->getDynTitle();
  $editor->dyn_description=$dynaform->getDynDescription();
  $editor->_setUseTemporalCopy(true);
  $editor->_render();

?>