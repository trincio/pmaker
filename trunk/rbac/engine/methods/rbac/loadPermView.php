<?php
if ($_GET['UID'] != "")
  $_SESSION['CURRENT_APPLICATION'] = isset($_GET['UID'])?$_GET['UID']:'';//$URI_VARS[0]; 
header ("location: permList.htm");
?>