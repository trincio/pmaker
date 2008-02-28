<?php

/**
 * Default home page view
 *
 * @author MaBoRaK
 * @version 0.1
 */
echo '<?xml version="1.0" encoding="UTF-8" ?>';
?>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Processmaker Installer</title>
	<script type='text/javascript' src='/js/maborak/core/maborak.js'></script>
	<link rel="stylesheet" type="text/css" href="/js/maborak/samples/style.css" />

	<script type='text/javascript' src='/sys/en/green/install/install.js'></script>
	<script type='text/javascript'>
	var ifr;
	var grid,winGrill, leimnud = new maborak();
	var inWIN = <? echo (PHP_OS=="WINNT")?"true":"false";?>;
	leimnud.make();
	leimnud.Package.Load("validator,app,rpc,fx,drag,drop,panel,grid",{Instance:leimnud,Type:"module"});
	leimnud.Package.Load("json",{Type:"file"});
	leimnud.exec(leimnud.fix.memoryLeak);
	var inst;
	leimnud.event.add(window,'load',function(){
		inst = new leimnud.module.panel();
		inst.options={
			size:{w:810,h:680},
			title	:"",
			position:{x:2,y:2,center:true},
			statusBar:false,
			control:{
				roll	:false,
				close	:false
			},
			fx:{
				shadow:false,
				fadeIn:false
			}
		};
		inst.setStyle={
			content:{padding:2}
		};
		var classInstaller = new installer();
		inst.tab={
			optWidth:190,
			manualDisabled:true,
			step	:(leimnud.browser.isIE?-1:5),
			options:[{
				title	:"Configuration",
				content	:function()
				{
					classInstaller.make({
						server	:"installServer.php",
						path_data:"<?php echo defined('PATH_DATA')?PATH_DATA:'/opt/processmaker/shared';?>",
						path_compiled:"<?php echo defined('PATH_C')?PATH_C:'/opt/processmaker/compiled';?>",
						path_trunk:"<?php echo PATH_CORE;?>"
					});
				},
				selected:true
			},
			{
				title	:"Installation",
				content	:classInstaller.install
			}
			
			]
		};
		inst.make();
	});
	</script>
	<style>
	input{
		font:normal 8pt sans-serif,Tahoma,MiscFixed;
	}
	body{
		background-color:white;
		font:normal 8pt sans-serif,Tahoma;
	}
.inst_table
{
	width:100%;
border-collapse:collapse;
		font:normal 8pt Tahoma,sans-serif;
}
.inst_td0
{
	width:60%;
	text-align:right;
	border:1px solid #CCC;
	padding:5px;
}
.inst_td1
{

	width:40%;
	padding:5px;
	border:1px solid #CCC;
}
.tdNormal, .tdOk, .tdFailed
{
	border:1px solid #CCC;
	text-align:center;
}
.tdOk
{
	font-weight:bold;
	color:green;
	padding:6px;
}
.tdFailed
{
	font-weight:bold;
	color:red;
}
.title
{
	text-align:right;
padding-right:10px;
}
.inputNormal, .inputOk, .inputFailed
{
	width:100%;
	border:1px solid #666;
	border-left:3px solid #666;
	font:normal 8pt Tahoma,sans-serif;
	text-align:center;
}
.inputOk
{
	border:1px solid green;
	border-left:3px solid green;
}
.inputFailed
{
	border:1px solid red;
	border-left:3px solid red;
}
.button
{
	font:normal 8pt Tahoma,MiscFixed,sans-serif;
	border:1px solid #afafaf;
	margin-left:2px;
	color:black;
	cursor:pointer;
}
.buttonHover
{
	border:1px solid #666;
	background-position:0 -8;
}
.buttonDisabled
{
	border:1px solid #9f9f9f;
	background-position:0 -10;
	color:#9f9f9f;
	cursor:default;
}
	</style>
</head>

<body>
<?php
//exec("mkdir /var/www/html/asas",$console);
?>
</body>

</html>
