<?php
if (defined('SYS_SYS')) $_SESSION['ENVIRONMENT']= SYS_SYS;
else $_SESSION['ENVIRONMENT']= 'vacio';

$frm = $_POST['form'];
$usr = strtolower(trim($frm['USER_NAME']));
$pwd = trim($frm['USER_PASS']);

$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );

$_SESSION['USER_LOGGED']   = 0;
$_SESSION['VALID_SESSION'] = session_id();
$_SESSION['USER']          = $usr;

$res = $RBAC->VerifyLogin($usr, $pwd);

switch ($res)
{
  case -1://don't exist
    G::SendMessageXml('ID_USER_NOT_REGISTERED', 'warning');
    break;
  case -2://password incorrect
    G::SendMessageXml('ID_WRONG_PASS', 'warning');
    break;
  case -3: //inactive
  case -4: //due
    G::SendMessageXml('ID_USER_INACTIVE', 'warning');
    break;
}
if ($res < 0 )
{
  header('location: login.html');
  die;
}

$uid = $res;
$_SESSION['USER_LOGGED'] = $uid;
$res = $RBAC->userCanAccess('RBAC_LOGIN');
if ($res != 1 )
{
  G::SendMessageXml('ID_USER_HAVENT_RIGHTS_PAGE', 'error');
  header('location: login.html');
  die;
}

$_SESSION['USER_NAME'] = $usr;

$file = PATH_RBAC . PATH_SEP . 'class.authentication.php';
require_once($file);
$obj = new authenticationSource;
$obj->SetTo($dbc);
$res = $obj->verifyStructures();

if ($RBAC->userCanAccess("RBAC_READONLY") == 1)
{
  header('location: ../rbac/userList');
}
else
{
  header('location: ../rbac/appList');
}
?>