<?
  define ('AJAX_PAGE', 1);

  function get_ajax_value ( $varName ) {
    $_result = NULL;
  	if(isset($_GET[ $varName ]) || isset($_POST[ $varName ])) {
      $_result =(isset($_GET[ $varName ]))?urldecode($_GET[ $varName ]):$_POST[ $varName ];
    }
    //linea comentada porque cuando llegaba un valor cero, se convertia en la cadena nula
    //y eso provocaba muchos problemas....
    //$_result =(isset($_result) and !empty($_result)) ? $_result : "";
    $_result =(isset($_result) ) ? $_result : NULL;
    return $_result;
  }

  function urldecode_values($aVars)
  {
    foreach ($aVars as $sKey1 => $sValue1)
    {
    	if (is_array($sValue1))
    	{
    		foreach ($sValue1 as $sKey2 => $sValue2)
    		{
    			if (is_array($sValue2))
    	    {
    	    	foreach ($sValue2 as $sKey3 => $sValue3)
    		    {
    		    	$aVars[$sKey1][$sKey2][$sKey3] = urldecode($sValue3);
    		    }
    	    }
    	    else
    	    {
    	    	$aVars[$sKey1][$sKey2] = urldecode($sValue2);
    	    }
    		}
    	}
    	else
    	{
    		$aVars[$sKey1] = urldecode($sValue1);
    	}
    }
    return $aVars;
  }

  function ajax_show_xmlform($sFilename, $aFields = array(), $sAction = '') {
    $G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', $sFilename, '', $aFields, $sAction);
    G::RenderPage('publish', 'blank');
  }

  function ajax_show_menu ( $menu ) {
    if ( $menu == '' ) return;
    global $G_OP_MENU;
    $G_OP_MENU = new Menu;
    $G_OP_MENU->Load( $menu );
    G::LoadTemplate( 'submenu' );
  }

  function ajax_show_template ( $file ) {
    if ( $file == '' ) return;
    G::LoadTemplate( $file );
  }
	function ajax_show_image ( $file ) {
		global $G_IMAGE_FILENAME;
		global  $G_IMAGE_PARTS;

    if ( $file == '' ) return;

    $G_IMAGE_FILENAME = $file;
    //$G_IMAGE_PARTS = $Part['Data'];

    G::LoadTemplate( 'viewProcessMap' );
  }

  function ajax_show_table ( $dbc, $file , $template) {
    global $G_CONTENT;
    global $G_TABLE;
    $G_CONTENT = new Content;
    //global $G_TMP_TARGET;
    if ( $file == '' || $template == '' ) return;
    //$G_TMP_TARGET = $Part['Target'];
    $G_TABLE = G::LoadRawTable( $file, $dbc, $Fields );
    G::LoadTemplate( $template );
  }

  function ajax_LoadJavaScript( $phpMethod, $phpFile ) {
    print '  LoadPopJavaScript ( "/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/tools/loadJavaScript.html?method=' .$phpMethod . '&file=' . $phpFile . "\");\n";
  }
