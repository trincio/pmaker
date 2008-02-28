<?php
$_SESSION['CURRENT_USER'] = $_GET['UID'];
header('location: userEdit.htm');
?>