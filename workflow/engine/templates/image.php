<?php
  //usado en workflows...
  global $G_IMAGE_FILENAME;
  global $CURRENT_PAGE;
  global $links;
  global $G_CONTENT;
  $aux = explode ( '/', $_SERVER['REQUEST_URI'] );
  $aux[ count($aux) -1 ] = $G_IMAGE_FILENAME;
  $imgFile = implode ( '/', $aux ) ;
  print "<img id='$G_IMAGE_FILENAME' src='$imgFile' border='0' >";
?>

