<?php

  //to do: improve the way to pass two or more parameters in the paged-table ( link )
    
  $aux = explode ( '|', $_GET['id'] );
<!-- START BLOCK : keys -->
  ${phpName} = str_replace ( '"', '', $aux[{index}] );
<!-- END BLOCK : keys --> 
  

  require_once ( "classes/model/{className}.php" );
  //if exists the row in the database propel will update it, otherwise will insert.
  $tr = {className}Peer::retrieveByPK( {keylist}  );
  
  if ( ( is_object ( $tr ) &&  get_class ($tr) == '{className}' ) ) { 
<!-- START BLOCK : fields -->
     $fields['{name}'] = $tr->get{phpName}();
<!-- END BLOCK : fields -->
  }
  else
    $fields = array();  
  
  $G_MAIN_MENU = '{className}/menu{className}';
  $G_SUB_MENU = '{className}';
  $G_ID_MENU_SELECTED = '{menuId}';
  $G_ID_SUB_MENU_SELECTED = '{menuId}';


  $G_PUBLISH = new Publisher;
  $dbc = new DBConnection;
  $ses = new DBSession($dbc);
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', '{className}/{className}', '', $fields, '{className}Save' );
  G::RenderPage('publish');   
?>