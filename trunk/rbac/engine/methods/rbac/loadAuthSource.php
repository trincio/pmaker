<?php
$_SESSION['CURRENT_AUTH_SOURCE'] = $_GET['UID'];
header('location: authEdit.htm');
?>