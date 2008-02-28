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
  if (!isset($_GET['ext'])) {
    $ext = $info['extension'];
  }
  else {
  	if ($_GET['ext'] != '') {
  		$ext = $_GET['ext'];
  	}
  	else {
  		$ext = $info['extension'];
  	}
  }

  $realPath = PATH_DOCUMENT . $_SESSION['APPLICATION'] . '/outdocs/' . $info['basename'] . '.' . $ext ;
  G::streamFile ( $realPath, true );

?>