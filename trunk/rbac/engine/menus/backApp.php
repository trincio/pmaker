<?php

global $G_TMP_MENU;
global $G_BACK_PAGE;

$G_TMP_MENU->AddRawOption( $G_BACK_PAGE);

switch ( SYS_LANG ) {
  case "es" : 
    $G_TMP_MENU->Labels = Array (
    "Retornar al trámite"
    );
    break;
  default : 
    $G_TMP_MENU->Labels = Array (
    "Back to Application"
    );
}
?>
