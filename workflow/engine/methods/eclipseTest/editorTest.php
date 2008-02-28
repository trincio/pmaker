<?php
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
  $editor->dyn_type=$dynaform->getDynType();
  $editor->dyn_title=$dynaform->getDynTitle();
  $editor->dyn_description=$dynaform->getDynDescription();
  $editor->_render();

?>