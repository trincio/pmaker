<?php

  G::GenericForceLogin ('RBAC_LOGIN','login/noViewPage','login/login');
  
  $G_MAIN_MENU         = 'rbac';
  $G_SUB_MENU          = 'rbac.userView';
  $G_MENU_SELECTED     = 0;
  $G_SUB_MENU_SELECTED = 2;

//$uid = $_SESSION['CURRENT_USER'];

$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME);
/*$ses = new DBSession ( $dbc );

$stQry = 'SELECT UID, USR_USE_LDAP FROM USERS where UID = ' . $uid;
$dset = $ses->Execute ( $stQry );
$row  = $dset->Read();
$useLdap = $row['USR_USE_LDAP'] == 'Y';*/

$access = $RBAC->userCanAccess ('RBAC_CREATE_USERS' );

$G_PUBLISH = new Publisher;
//$G_PUBLISH->SetTo ($dbc);
//$G_PUBLISH->AddContent ( 'table', 'paged-table', 'rbac.users.role', 'rbac/myApp', '', '');
//$content = G::LoadContent( 'rbac/myApp' );
$G_PUBLISH->AddContent('xmlform', 'pagedTable', 'rbac/usersRolesList', '', array('CURRENT_USER' => $_SESSION['CURRENT_USER']), '');
G::RenderPage( 'publish');

?>