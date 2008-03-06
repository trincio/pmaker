<?php
  //add more security, and catch any error or exception
  $sFileName = $_GET['p'] . '.pm';
  $realPath = PATH_DOCUMENT . 'output'. PATH_SEP. $sFileName;
  G::streamFile ( $realPath, true );
