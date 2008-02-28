<?php
if ($_GET['UID'] != "")
  $_SESSION['CURRENT_APPLICATION'] = $_GET['UID'];
header('location: roleList.htm');
?>