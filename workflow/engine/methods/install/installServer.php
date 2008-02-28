<?php
print " ";
$isWindows = PHP_OS == 'WINNT' ? true : false;

$oJSON   = new Services_JSON();
$action = $_POST['action'];
$dataClient   = $oJSON->decode(stripslashes($_POST['data']));

function generatePassword ($length = 8)
{
  $password = "";
  $possible = "0123456789bcdfghjkmnpqrstvwxyz"; 
  $i = 0; 
  while ($i < $length) { 
    $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
    if (!strstr($password, $char)) { 
      $password .= $char;
      $i++;
    }
  }
  return $password;
}

function checkMysqlConnection($h='',$u='',$p='')
{
	if(!function_exists("mysql_connect"))
	{
		return Array(
			'connection'=>false,
			'grant'=>false,
			'message'=>"php-mysql is Not Installed"
		);
	}
	$con = @mysql_connect($h,$u,$p);
	$rt = Array();
	if(!$con)
	{
		$rt['connection']=false;
		$rt['grant']=false;
		$rt['message']="Mysql error: ".mysql_error();
	}
	else
	{
		$rt['connection']=true;
		$dbNameTest = "PROCESSMAKERTESTDC";
		$db = @mysql_query("CREATE DATABASE ".$dbNameTest,$con);
		if(!$db)
		{
			$rt['grant']=false;
			$rt['message']="Db GRANTS error:  ".mysql_error();
		}
		else
		{
			//@mysql_drop_db("processmaker_testGA");
			$usrTest = "wfrbtest";
			$chkG = "GRANT ALL PRIVILEGES ON `".$dbNameTest."`.* TO ".$usrTest."@'%' IDENTIFIED BY 'sample' WITH GRANT OPTION";
			$ch   = @mysql_query($chkG,$con);
			if(!$ch)
			{
				$rt['grant']=false;
				$rt['message']="USER PRIVILEGES ERROR ";
			}
			else
			{
				@mysql_query("DROP USER ".$usrTest."@'%'",$con);
				$rt['grant']=true;
				$rt['message']="Successful connection";
			}
			@mysql_query("DROP DATABASE ".$dbNameTest,$con);
		}
	}
	return $rt;
}
function find_SQL_Version($my = 'mysql',$infExe)
{
	if(PHP_OS=="WINNT" && !$infExe)
	{
		return false;
	}
	$output = shell_exec($my.' -V');
	preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version);
	return $version[0];
}
function findRootPath($path)
{
	$i =0 ; //prevent loop inifinity
	while(!is_dir($path) && ($path = dirname($path)) && ((strlen($path)>1) && $i<10))
	{
		$i++;
	}
	return $path;
}

function file_permisions($file,$def=777)
{
	if ( PHP_OS == 'WINNT' ) 
	  return $def;
	else
	  return (int)substr(sprintf('%o',@fileperms($file)),-4);
}
function isDirWritable($dir='')
{
	global $isWindows;
	if ( $isWindows ) {
  	$dir = findRootPath($dir);
  	//print $dir;
  	//return $dir . ' '.file_exists ( $dir ) ;
  	return file_exists ( $dir );
	}
	else {
	  $dir = findRootPath($dir);		
  	return (is_writable($dir) && ( file_permisions($dir)==777));
	}
}
function createSite($con,$site,$dataClient,$passwordSite="sample",$my)
{
	$wf = "wf_".$site;
	$rb = "rbac_".$site;
	$schema	="schema.sql";
	$values	="insert.sql";   //noe existe
	/* Create databases & users  */
	$q = "DROP DATABASE IF EXISTS ".$wf;
	$ac = @mysql_query($q,$con);
	print_r($q.": => ".((!$ac)?mysql_error():"OK")."\n");

	$q = "DROP DATABASE IF EXISTS ".$rb;
	$ac = @mysql_query("DROP DATABASE IF EXISTS ".$rb,$con);
	print_r($q.": => ".((!$ac)?mysql_error():"OK")."\n");

	$q	= "CREATE DATABASE ".$wf." DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
	$ac = @mysql_query($q,$con);
	print_r($q.": => ".((!$ac)?mysql_error():"OK")."\n");

	$q	= "CREATE DATABASE ".$rb." DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
	$ac = @mysql_query($q,$con);
	print_r($q.": => ".((!$ac)?mysql_error():"OK")."\n");

	//$priv_wf = "GRANT ALL PRIVILEGES ON `".$wf.".* TO ".$wf."@`".$dataClient->mysqlH."` IDENTIFIED BY '".$passwordSite."' WITH GRANT OPTION";
	if($dataClient->mysqlH==="localhost")
	{
		$priv_wf = "GRANT ALL PRIVILEGES ON `".$wf."`.* TO ".$wf."@'localhost' IDENTIFIED BY '".$passwordSite."' WITH GRANT OPTION";
	}
	else
	{
		$priv_wf = "GRANT ALL PRIVILEGES ON `".$wf."`.* TO ".$wf."@'%' IDENTIFIED BY '".$passwordSite."' WITH GRANT OPTION";
	}
	$ac = @mysql_query($priv_wf,$con);
	print_r($priv_wf.": => ".((!$ac)?mysql_error():"OK")."\n");

	
	if($dataClient->mysqlH==="localhost")
	{
		$priv_rb = "GRANT ALL PRIVILEGES ON `".$rb."`.* TO ".$rb."@'localhost' IDENTIFIED BY '".$passwordSite."' WITH GRANT OPTION";
	}
	else
	{
		$priv_rb = "GRANT ALL PRIVILEGES ON `".$rb."`.* TO ".$rb."@'%' IDENTIFIED BY '".$passwordSite."' WITH GRANT OPTION";
	}
	$ac = @mysql_query($priv_rb,$con);
	print_r($priv_rb.": => ".((!$ac)?mysql_error():"OK")."\n");


	/* Dump schema workflow && data  */

	print_r("Dump schema workflow/rbac && data\n====================================\n");

	print_r("Mysql client: ".$my."\n");

	$sh_sc = $my." ".$wf." < ".PATH_WORKFLOW_MYSQL_DATA.$schema." -h ".$dataClient->mysqlH." --user=".$wf." --password=".$passwordSite;
	$result_shell = exec($sh_sc);
	print_r($sh_sc."  => ".(($result_shell)?$result_shell:"OK")."\n");

	$sh_in = $my." ".$wf." < ".PATH_WORKFLOW_MYSQL_DATA.$values." -h ".$dataClient->mysqlH." --user=".$wf." --password=".$passwordSite;
	$result_shell = exec($sh_in);
	print_r($result_shell."\n");
	print_r($sh_in."  => ".(($result_shell)?$result_shell:"OK")."\n");


	/* Dump schema rbac && data  */
	$sh_rbsc = $my." ".$rb." < ".PATH_RBAC_MYSQL_DATA.$schema." -h ".$dataClient->mysqlH." --user=".$rb." --password=".$passwordSite;
	$result_shell = exec($sh_rbsc,$err_sh);
	print_r($result_shell."\n");
	print_r($sh_rbsc."  => ".(($result_shell)?$result_shell:"OK")."\n");

	$sh_in = $my." ".$rb." < ".PATH_RBAC_MYSQL_DATA.$values." -h ".$dataClient->mysqlH." --user=".$rb." --password=".$passwordSite;
	$result_shell = exec($sh_in);
	print_r($sh_in."  => ".(($result_shell)?$result_shell:"OK")."\n");

	/* Creating site structure  */
	$path_site 	= $dataClient->path_data."/sites/".$site."/";
	$db_file	= $path_site."db.php";
	mkdir($path_site,0777,true);
	mkdir($path_site."customFunctions",0777,true);
	mkdir($path_site."rtfs/",0777,true);
	mkdir($path_site."xmlForms",0777,true);
	mkdir($path_site."processesImages/",0777,true);
	mkdir($path_site."files/",0777,true);

	/* Creating db.php  */
	$db_text = "<?php\n" .
	"// Processmaker configuration\n" .
	"define ('DB_ADAPTER', 'mysql' );\n" .
	"define ('DB_HOST', '" . $dataClient->mysqlH . "' );\n" .
	"define ('DB_NAME', '" . $wf. "' );\n" .
	"define ('DB_USER', '" . $wf . "' );\n" .
	"define ('DB_PASS', '" . $passwordSite . "' );\n" .
	"define ('DB_RBAC_HOST', '". $dataClient->mysqlH . "' );\n" .
	"define ('DB_RBAC_NAME', '". $rb . "' );\n" .
	"define ('DB_RBAC_USER', '".$rb . "' );\n" .
	"define ('DB_RBAC_PASS', '". $passwordSite . "' );\n" .
/*	"define ('DB_WIZARD_REPORT_SYS', '" . $DBreport  . "' );\n" .
	"define ('DB_WIZARD_REPORT_USER', 'rep_".$DBsufix . "' );\n" .
	"define ('DB_WIZARD_REPORT_PASS', '" . $DBPassReport . "' );\n" .*/
	"?>";
	$fp =  @fopen($db_file, "w");
	print_r("Creating: ".$db_file."  => ".((!$fp)?$fp:"OK")."\n");
	$ff =  @fputs( $fp, $db_text, strlen($db_text));
	print_r("Write: ".$db_file."  => ".((!$ff)?$ff:"OK")."\n");

	fclose( $fp );

}
if($action==="check")
{
	$data=null;
	$mex=$dataClient->mysqlE;
	$smex=substr($mex,-9);
	
	$data->mysqlExe		=((file_exists($mex) && $smex=="mysql.exe") || PHP_OS!=="WINNT")? true:false;
	$data->phpVersion	=(version_compare(PHP_VERSION,"5.1.0",">="))?true:false;
	$data->mysqlVersion	=(version_compare(find_SQL_Version((PHP_OS=='WINNT')?$mex:'mysql',$data->mysqlExe),"4.1.20",">=")) ?true:false;

	$con = checkMysqlConnection($dataClient->mysqlH,$dataClient->mysqlU,$dataClient->mysqlP);
	if(trim($dataClient->mysqlH)=='' || trim($dataClient->mysqlU)=='')
	{
		$con = array('connection'=>false,'grant'=>false,'message'=>'Please complete the input fields (Hostname/Username)');
	}
	$data->mysqlConnection	=($con['connection'])?true:false;
	$data->grantPriv	=($con['grant'])?true:false;
	$data->databaseMessage	=$con['message'];
	$data->path_data	=isDirWritable($dataClient->path_data);
	$data->path_compiled	=isDirWritable($dataClient->path_compiled);
	$data->checkMemory	=(((int)ini_get("memory_limit"))>=40)?true:false;
	$data->checkmqgpc	=(get_magic_quotes_gpc())?false:true;
	$data->checkPI		=((int)file_permisions(PATH_CORE."config/paths_installed.php",666)==666 || ! file_exists(PATH_CORE."config/paths_installed.php"))?true:false;
	$data->checkDL		=((int)file_permisions(PATH_CORE."content/languages/",777)==777)?true:false;
	$data->checkDLJ		=((int)file_permisions(PATH_CORE."js/labels/",777)==777)?true:false;
	echo $oJSON->encode($data);
/*	echo $dataClient->mysqlE;
	$nombre_archivo = $dataClient->mysqlE;

	if (file_exists($nombre_archivo)) {
		    echo "El archivo $nombre_archivo existe";
	} else {
		    echo "El archivo $nombre_archivo no existe";
	}*/
}
else if($action==="install")
{
	print_r("POST: ".$_POST)."\n";
	/*
	 * Instalación son SIMPLE POST
	 *
	 * Datos necesarios por POST:
	 *
	 *
	 * 	action=install
	 * 	data=	{"mysqlE":"Path/to/mysql.exe",
	 * 		"mysqlH":"Mysqlhostname",
	 * 		"mysqlU":"mysqlUsername",
	 * 		"mysqlP":"mysqlPassword",
	 * 		"path_data":"/path/to/workflow_data/",
	 * 		"path_compiled":"/path/to/compiled/"}
	 *
	 *--------------------------------------------------------------------------------------------------------------
	 *
	 * Pasos para instalar.
	 * 1) Se necesita los datos:
	 * 	$HOSTNAME
	 * 	$USERNAME
	 * 	$PASSWORD
	 * 	$PATH_TO_WORKFLOW_DATA
	 * 	$PATH_TO_COMPILED DATA
	 * 2) crear $PATH_TO_WORKFLOW_DATA
	 * 3) crear $PATH_TO_COMPILED_DATA
	 * 4) Crear el sitio workflow
	 *
	 * 	4.1 Crear el usuario (mysql) wf_workflow , password: sample
	 *		4.1.1 Crear base de datos wf_workflow con el actual usuario
	 *		4.1.2 Darle todos los privilegios sobre la base de datos wf_workflow al usuario wf_workflow
	 *		4.1.3 Dump del archivo processmaker/workflow/engine/data/mysql/schema.sql
	 *		4.1.4 Dump del archivo processmaker/workflow/engine/data/mysql/insert.sql
	 *
	 * 	4.2 Crear el usuario (mysql) wf_rbac, password: sample
	 *		4.2.1 Crear base de datos wf_rbac con el actual usuario
	 *		4.2.2 Darle todos los privilegios sobre la base de datos wf_rbac al usuario wf_rbac
	 *		4.2.3 Dump del archivo processmaker/rbac/engine/data/mysql/schema.sql
	 *		4.2.4 Dump del archivo processmaker/rbac/engine/data/mysql/insert.sql
	 *
	 *	4.3 Crear archivo de configuración y directorios para el sitio workflow
	 *
	 *		4.3.1 Crer los directorios:
	 *			
	 *			$PATH_TO_WORKFLOW_DATA./sites/workflow/
	 *			$PATH_TO_WORKFLOW_DATA./sites/workflow/cutomFunctions/
	 *			$PATH_TO_WORKFLOW_DATA./sites/workflow/rtfs/
	 *			$PATH_TO_WORKFLOW_DATA./sites/workflow/xmlforms/
	 *			$PATH_TO_WORKFLOW_DATA./sites/workflow/processesImages/
	 *			$PATH_TO_WORKFLOW_DATA./sites/workflow/files/
	 *		4.3.2 Crear el archivo.
	 *
	 *			$PATH_TO_WORKFLOW_DATA./sites/workflow/db.php
	 *
	 *			con el contenido reemplazando $HOSTNAME por el valor definido.
	 *
				<?php
				// Processmaker configuration
				define ('DB_ADAPTER', 'mysql' );
				define ('DB_HOST', $HOSTNAME );
				define ('DB_NAME', 'wf_workflow' );
				define ('DB_USER', 'wf_workflow' );
				define ('DB_PASS', 'sample' );
				define ('DB_RBAC_HOST', $HOSTNAME );
				define ('DB_RBAC_NAME', 'rbac_workflow' );
				define ('DB_RBAC_USER', 'rbac_workflow' );
				define ('DB_RBAC_PASS', 'sample' );
			?>
			
	*	4.4 Crear el archivo workflow/engine/config/paths_installed.php con el contenido.
	*
	*		<?php
			define( 'PATH_DATA', '$PATH_TO_WORKFLOW_DATA' );
			define( 'PATH_C', '$PATH_TO_COMPILED_DATA' );
			?>

			Reemplazando:
	* 	$PATH_TO_WORKFLOW_DATA
	* 	$PATH_TO_COMPILED DATA
	*
	*	4.2 Actualizar archivos de idiomas abriendo la página (background)
	*
	*		http://ProcessmakerHostname/sysworkflow/en/green/tools/updateTranslation
	*
 	*
	* */
	$sp		= "/";
	$dir_data	= $dataClient->path_data;
	$dir_compiled	= $dataClient->path_compiled;

	$dir_data	= (substr($dir_data,-1)==$sp)?$dir_data:$dir_data."/";
	$dir_compiled	= (substr($dir_compiled,-1)==$sp)?$dir_compiled:$dir_compiled."/";
	global $isWindows;
	$my = $isWindows ? $dataClient->mysqlE : 'mysql';

	@mkdir($dir_data."sites",0777,true);
	@mkdir($dir_compiled,0777,true);

	$create_db	="create-db.sql";
	$schema		="schema.sql";
	/*
	$sh_db = $my . " < ".PATH_WORKFLOW_MYSQL_DATA.$create_db." -h ".$dataClient->mysqlH." -u ".$dataClient->mysqlU." --password=".$dataClient->mysqlP;
	exec($sh_db,$err_sh);
	$sh_sc = $my . " wf_workflow < ".PATH_WORKFLOW_MYSQL_DATA.$schema." -h ".$dataClient->mysqlH." -u ".$dataClient->mysqlU." --password=".$dataClient->mysqlP;
	exec($sh_sc,$err_sh);
*/

	$con = mysql_connect($dataClient->mysqlH, $dataClient->mysqlU, $dataClient->mysqlP);

	createSite($con, 'workflow', $dataClient,generatePassword(12), $my);
	
	$db_text = "<?php\n" .
	"define( 'PATH_DATA', '".$dir_data."' );\n" .
	"define( 'PATH_C',    '".$dir_compiled."' );\n" .
	"?>";
	$fp = fopen(FILE_PATHS_INSTALLED, "w");
	fputs( $fp, $db_text, strlen($db_text));
	fclose( $fp );

	/* Update languages */
	$update = file_get_contents("http://".$_SERVER['SERVER_NAME']."/sysworkflow/en/green/tools/updateTranslation");
	print_r("Update language:  => ".((!$update)?$update:"OK")."\n");
}
?>
