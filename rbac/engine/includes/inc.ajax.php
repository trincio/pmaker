<?
  define ('AJAX_PAGE', 1);

  function get_ajax_value ( $varName ) {
    $_result = '';
  	if(isset($_GET[ $varName ]) || isset($_POST[ $varName ])) {
      $_result =(isset($_GET[ $varName ]))?$_GET[ $varName ]:$_POST[ $varName ];
    }
    //linea comentada porque cuando llegaba un valor cero, se convertia en la cadena nula
    //y eso provocaba muchos problemas....
    //$_result =(isset($_result) and !empty($_result)) ? $_result : "";  
    $_result =(isset($_result) ) ? $_result : NULL;  
    return $_result;
  }

  function ajax_show_xmlform ( $xmlform, $Fields, $template='xmlform') {
    global $G_FORM;
    $G_FORM = new Form;
    G::LoadSystem("xmlform");
    $xml = new XmlForm;
    $xml->home = PATH_DYNAFORM;
    $xml->parseFile ( $xmlform );
    $xml->renderForm ($G_FORM);
    $G_FORM->Values = $Fields;
    $G_FORM->SetUp( '' );   //currently is not supported the action feature
    $G_FORM->hideFormAction = 1; //to avoid the render of  <form action='xxx' >
    G::LoadTemplate( $template );
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
?>