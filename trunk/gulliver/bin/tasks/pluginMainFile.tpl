<?php 
  G::LoadClass( "plugin");
  
 class {className}Plugin extends PMPlugin 
 {
    function {className}Plugin($sNamespace, $sFilename = null) 
    {
        $res = parent::PMPlugin($sNamespace, $sFilename);
        $this->sFriendlyName = 'Test {className} Plugin';
        $this->sPluginFolder = '{className}';
        $this->iVersion = 0.78;
        return $res;
    }

    function setup()
    {
      $this->registerMenu( 'cases', 'menu{className}.php');
      $this->registerTrigger( 1000, 'create{className}' );
    }
  }

 $oPluginRegistry =& PMPluginRegistry::getSingleton();
 $oPluginRegistry->registerPlugin('{className}', __FILE__);



  
    
    
  