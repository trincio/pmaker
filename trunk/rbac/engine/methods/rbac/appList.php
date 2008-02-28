<?php
/*class XmlForm_Field_Menu extends XmlForm_Field
{
  function render( $value = NULL )
  {
    return "·asdsad";
  }
}
class filterForm extends form
{
  var $cols = 3;
  var $type = 'filterform';
  var $ajaxServer = '...';
}
class xmlMenu extends form
{
  var $type = 'xmlmenu';
}*/
  G::LoadClass("dynaform");
  G::GenericForceLogin ('RBAC_LOGIN','login/noViewPage','login/login');
  $G_MAIN_MENU = "rbac";
  $G_SUB_MENU  = "rbac.application";
  $G_MENU_SELECTED = 1;
  
  //$RBAC->userCanAccess("RBAC_LOGIN");
  //$RBAC->userCanAccess("RBAC_READONLY" );
  //$RBAC->userCanAccess("RBAC_CREATE_ROLE" );
  //$RBAC->userCanAccess("RBAC_CREATE_PERMISSION" );
  $canCreateApp = $RBAC->userCanAccess("RBAC_CREATE_APPLICATION" );
  
  $dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->SetTo ($dbc);
  //$G_PUBLISH->AddContent ( "table", "paged-table", "rbac.applications.list", "rbac/myApp", "", "load");
  //$G_PUBLISH->AddContent ( "xmlform", "xmlmenu", "rbac/appMenu", "", "", "load");
  //$G_PUBLISH->AddContent ( "xmlform", "filterform", "rbac/applicationsList", "", "", "load");
  $G_PUBLISH->AddContent ( "xmlform", "pagedTable", "rbac/applicationsList", "", "", "", "../gulliver/pagedTableAjax.php");
  $content = '';
  G::RenderPage( "publish" );

?>