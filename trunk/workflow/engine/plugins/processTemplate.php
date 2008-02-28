<?php 
  G::LoadClass( "plugin");
  
 class processTemplatePlugin extends PMPlugin 
 {
    function processTemplatePlugin($sNamespace, $sFilename = null) 
    {
        $res = parent::PMPlugin($sNamespace, $sFilename);
        $this->sFriendlyName = 'Process Map Templates';
        $this->sDescription  = 'This plugin includes various templates for quick and easy Process Map creation. Users can customize Process Maps based on pre-defined templates of common process designs (including Parallel, Dual Start Task, and Selection).';
        $this->sPluginFolder = 'processTemplate';
        $this->sSetupPage    = null;
        $this->iVersion = 0.78;
        return $res;
    }

    function setup()
    {
      $this->registerTrigger( PM_NEW_PROCESS_LIST, 'getNewProcessTemplateList' );
      $this->registerTrigger( PM_NEW_PROCESS_SAVE, 'saveNewProcess' );
    }
  }

 $oPluginRegistry =& PMPluginRegistry::getSingleton();
 $oPluginRegistry->registerPlugin('processTemplate', __FILE__);



  
  
