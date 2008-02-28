<?php
/*
 * Created on 13-02-2008
 *
 * @author David Callizaya <davidsantos@colosa.com>
 */

  require_once ( "classes/model/AppDocumentPeer.php" );

  $oAppDocument = new AppDocument();
  $oAppDocument->Fields = $oAppDocument->load($_GET['a']);

  $sAppDocUid = $oAppDocument->getAppDocUid();
  $info = pathinfo( $oAppDocument->getAppDocFilename() );
  $ext = $info['extension'];

  $realPath = PATH_DOCUMENT . $_SESSION['APPLICATION'] . '/' . $sAppDocUid . '.' . $ext ;
  G::streamFile ( $realPath, true, $oAppDocument->Fields['APP_DOC_FILENAME'] );

?>