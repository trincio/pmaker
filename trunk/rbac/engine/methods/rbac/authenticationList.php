<?php
$G_MAIN_MENU     = 'rbac';
$G_SUB_MENU      = 'rbac.authentication';
$G_MENU_SELECTED = 2;
$canCreateApp    = $RBAC->userCanAccess('RBAC_CREATE_APPLICATION' );

$G_PUBLISH = new Publisher;
$G_PUBLISH->AddContent('xmlform', 'pagedTable', 'rbac/authenticationsList', '', array('DELETE' => G::LoadMessageXml('ID_DELETE')), '');
G::RenderPage( 'publish');
?>