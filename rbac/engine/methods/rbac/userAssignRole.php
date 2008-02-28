<?php
//$G_MAIN_MENU         = 'rbac';
//$G_SUB_MENU          = 'rbac.userView';
//$G_MENU_SELECTED     = 0;
//$G_SUB_MENU_SELECTED = 3;

unset($_SESSION['CURRENT_APPLICATION']);
G::LoadClassRBAC('user');
$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME);

$obj = new RBAC_user;
$obj->SetTo($dbc);
$access = $RBAC->userCanAccess('RBAC_CREATE_USERS');

$obj->SetTo($dbc);
$obj->Load($_SESSION['CURRENT_USER']);
$useLdap = $obj->Fields['USR_USE_LDAP'] == 'Y';

$ses = new DBSession;
$ses->SetTo ($dbc);

$stQry  = 'SELECT ROL_APPLICATION FROM USER_ROLE LEFT JOIN ROLE AS R ON (ROL_UID = R.UID) WHERE USR_UID = ' . $_SESSION['CURRENT_USER'];
$dset   = $ses->Execute($stQry);
$row    = $dset->Read();
$inApps = '(0';
while (is_array($row))
{
  $inApps .= ', ' . (int)$row['ROL_APPLICATION'];
  $row = $dset->Read();
}
$inApps .= ')';
$obj->Fields['INAPPS'] = $inApps;

$stQry = 'SELECT COUNT(*) AS CANT FROM APPLICATION WHERE UID NOT IN ' . $inApps;
$dset  = $ses->Execute($stQry);
$row   = $dset->Read();

$G_PUBLISH = new Publisher;
$G_PUBLISH->SetTo ($dbc);

if ( $row['CANT'] > 0 )
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'rbac/userAssignRole', '', $obj->Fields, 'userAssignRole2');
else
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'rbac/noMoreRolesAvailable', '', $obj->Fields, 'userViewRole');

G::RenderPage( 'publish', 'blank');
?>