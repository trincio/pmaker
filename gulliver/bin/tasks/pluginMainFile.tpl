<?php 
  G::LoadClass( "plugin");
  
 class {className}Plugin extends PMPlugin 
 {
    function {className}Plugin($sNamespace, $sFilename = null) 
    {
        $res = parent::PMPlugin($sNamespace, $sFilename);
        $this->sFriendlyName = '{className} Plugin';
        $this->sDescription  = 'Autogenerated plugin for class {className}';
        $this->sPluginFolder = '{className}';
        $this->sSetupPage    = '{className}';
        $this->iVersion = 0.78;
        $this->aWorkspaces = null;
        //$this->aWorkspaces = array ( 'os' );
        return $res;

       
    }

    function setup()
    {
<!-- START BLOCK : changeLogo -->
      $this->setCompanyLogo ('/plugin/{className}/{className}.png');
<!-- END BLOCK : changeLogo --> 

<!-- START BLOCK : menu -->
      $this->registerMenu( 'setup', 'menu{className}.php');
<!-- END BLOCK : menu --> 
      
<!-- START BLOCK : externalStep -->
      $this->registerStep( '{GUID}', 'step{className}', '{className} external step' );
<!-- END BLOCK : externalStep --> 

<!-- START BLOCK : dashboard -->
      $this->registerDashboard();
<!-- END BLOCK : dashboard --> 

<!-- START BLOCK : report -->
      $this->registerReport();
<!-- END BLOCK : report --> 
        
    }
  }

 $oPluginRegistry =& PMPluginRegistry::getSingleton();
 $oPluginRegistry->registerPlugin('{className}', __FILE__);



  
    
    
  