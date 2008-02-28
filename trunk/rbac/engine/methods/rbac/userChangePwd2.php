<?php
  $frm   = $_POST['form'];
  $pass1 = $frm['PASSWORD'];
  $pass2 = $frm['PASSWORD2'];
  
  $dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME);
  G::LoadClassRBAC('user');
  $obj = new RBAC_User;
  $obj->SetTo($dbc);
  if ($pass1 != $pass2)
  {
    G::SendMessage(3, 'error');
    header('location: userChangePwd.php');
    die;
  }
  $obj->SetTo($dbc);
  $obj->changePassword($_SESSION['CURRENT_USER'], $pass1);
  header('location: userEdit.html');
  //header('location: userViewRole.html');
?>