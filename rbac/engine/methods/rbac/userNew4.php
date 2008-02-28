<?php
$frm   = $_POST['form'];
$user  = $frm['USR_USERNAME'];
$pass1 = $frm['USR_PASSWORD'];
$pass2 = $frm['USR_PASSWORD2'];
$uid   = $_SESSION['CURRENT_USER'];

$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME);

G::LoadClassRBAC ('user');
$obj = new RBAC_User;
$obj->SetTo($dbc);
$repId = $obj->UserNameRepetido($uid, $user);

if ($repId != 0)
{
  G::SendMessage(6, 'error');
  header('location: userNew3.php');
  die;
}

if ($pass1 != $pass2)
{
  G::SendMessage(3, 'error');
  header('location: userNew3.php');
  die;
}

$obj->SetTo($dbc);
$obj->createUserName($uid, $user, $pass1);

header('location: userList.html');
?>
