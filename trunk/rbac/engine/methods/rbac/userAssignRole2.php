<?php
$frm = $_POST['form'];
$rolid = $frm['USR_ROLE'];
$dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );
G::LoadClassRBAC('user');
$obj = new RBAC_User;
$obj->SetTo($dbc);
$obj->assignUserRole($_SESSION['CURRENT_USER'], $rolid);
//header('location: userViewRole.html');
?>
<script language='Javascript'>
  //parent.myPanel.remove();
  parent.window.location = 'userEdit.html';  
</script>