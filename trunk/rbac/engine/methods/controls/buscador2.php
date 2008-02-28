demo de buscador<br>
<?
  $frm = $HTTP_POST_VARS;
  
  $dbc = new dbconnection();
  $ses = new DBSession($dbc);
  $sql = "update tickets set tipo = ' " . $frm['tipo'] ."', resultado = ' " . $frm['curso'] . "' where ticket =  '" . $frm['ticket'] ."' ";
  $ses->Execute ( $sql );
?>
<script language = "JavaScript">
  window.close();
 
</script>


