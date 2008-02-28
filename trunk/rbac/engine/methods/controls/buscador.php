<?
  $frm = $HTTP_GET_VARS;
  
?>

<h1>demo de buscador</h1>
<form method=post action="buscador2.php">
<input type=hidden name=ticket value="<?= $frm['ticket'] ?>" >
<input type=hidden name=tipo value="<?= $frm['tipo'] ?>" >
Buscador tipo : <?= $frm['tipo'] ?><br>

<table><tr><td>
	curso</td><td>
		<select name=curso>
			<option value="curso1">Curso 1</option>
			<option value="curso2">Curso 2</option>
			<option value="curso3">Curso 3</option>
			<option value="curso4">Curso 4</4option>
			<option value="curso5">Curso 5</option>
</td></tr>
<tr><td colspan=2>
<input type=submit ></td></tr>
</table>
</form>
</body>
</html>

