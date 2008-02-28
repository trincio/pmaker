<?php
  $_GET['UID'] = (int)$_GET['UID'];
  $_SESSION['CURRENT_USER'] = $_GET['UID'];
  header('location: userEdit');
//  header('location: userViewRole');
?>