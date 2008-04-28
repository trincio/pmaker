var installer=function()
{
	this.make=function(options)
	{
		this.options={
			target:inst.elements.content
		}.concat(options || {});
		this.html();
		this.check();
	};
	this.html=function()
	{
		this.titleBar = document.createElement("div");
		this.titleBar.className="app_grid_headerBar___gray";
		leimnud.dom.setStyle(this.titleBar,{
			height:"auto",
			textAlign:"right"
		});
		this.options.target.appendChild(this.titleBar);

		this.options.button0 = document.createElement("input");
		this.options.button0.type="button";
		this.options.button0.value="Test";
		this.titleBar.appendChild(this.options.button0);

		this.options.button1 = document.createElement("input");
		this.options.button1.type="button";
		this.options.button1.value="Install";
		this.titleBar.appendChild(this.options.button1);

		this.options.button2 = document.createElement("input");
		this.options.button2.type="button";
		this.options.button2.value="Reset";
		this.titleBar.appendChild(this.options.button2);

		this.options.button1.disabled=true;
		this.options.button0.onmouseup=this.check;
		this.options.button1.onmouseup=function(){inst.selectTab(1);}.extend(this);
		this.options.button2.onmouseup=this.reset;

		this.buttonFun(this.options.button0);
		this.buttonFun(this.options.button1);
		this.buttonFun(this.options.button2);


		//this.phpVersion =
		this.table = document.createElement("table");
		this.table.className="inst_table";

		var tr = this.table.insertRow(-1);
		var tdtitle = tr.insertCell(0);
		tdtitle.colSpan=2;
		tdtitle.innerHTML="Requirements";
		tdtitle.className="app_grid_title___gray title";

		var tr = this.table.insertRow(-1);
		var td0 = tr.insertCell(0);
		td0.innerHTML="PHP Version >= 5.1.x";
		td0.className="inst_td0";
		this.phpVersion = tr.insertCell(1);		
		this.phpVersion.className="inst_td1";

		var tr = this.table.insertRow(-1);
		var td0 = tr.insertCell(0);
		td0.innerHTML="Mysql  >= 4.1.20.x ";
		td0.className="inst_td0";
		this.mysqlVersion = tr.insertCell(1);		
		this.mysqlVersion.className="inst_td1";

		var tr = this.table.insertRow(-1);
		var td0 = tr.insertCell(0);
		td0.innerHTML="Maximum amount of memory a script may consume (memory_limit) >= 40M ";
		td0.className="inst_td0";
		this.checkMemory = tr.insertCell(1);		
		this.checkMemory.className="inst_td1";

		var tr = this.table.insertRow(-1);
		var td0 = tr.insertCell(0);
		td0.innerHTML="Set magic quotes gpc to <b>false</b>";
		td0.className="inst_td0";
		this.checkmqgpc = tr.insertCell(1);		
		this.checkmqgpc.className="inst_td1";


		var tr = this.table.insertRow(-1);
		var td0 = tr.insertCell(0);
		td0.innerHTML="File "+this.options.path_trunk+"config/paths_installed.php<br> permissions: <b>0666</b>";
		td0.innerHTML+="<br>OR<br>";
		td0.innerHTML+="Directory "+this.options.path_trunk+"config/<br> permissions: <b>0777</b>";
		td0.className="inst_td0";
		this.checkPI = tr.insertCell(1);		
		this.checkPI.className="inst_td1";

		var tr = this.table.insertRow(-1);
		var td0 = tr.insertCell(0);
		td0.innerHTML="Directory "+this.options.path_trunk+"content/languages/<br> permissions: <b>0777</b>";
		td0.className="inst_td0";
		this.checkDL = tr.insertCell(1);		
		this.checkDL.className="inst_td1";

		var tr = this.table.insertRow(-1);
		var td0 = tr.insertCell(0);
		td0.innerHTML="File "+this.options.path_trunk+"js/labels/<br> permissions: <b>0777</b>";
		td0.className="inst_td0";
		this.checkDLJ = tr.insertCell(1);		
		this.checkDLJ.className="inst_td1";

		/* Database  */
		var tr = this.table.insertRow(-1);
		var tdtitle = tr.insertCell(0);
		tdtitle.colSpan=2;
		tdtitle.innerHTML="Database";
		tdtitle.className="app_grid_title___gray title";
		
		var tr = this.table.insertRow(-1);
		var td0 = tr.insertCell(0);
		td0.innerHTML="Database server hostname: ";
		td0.className="inst_td0";
		var td1 = tr.insertCell(1);
		td1.className="inst_td1";
		this.databaseHostname = document.createElement("input");
		this.databaseHostname.type="text";
		this.databaseHostname.onkeyup=this.submit;
		this.databaseHostname.className="inputNormal";
		td1.appendChild(this.databaseHostname);

/*		var tr = this.table.insertRow(-1);
		var td0 = tr.insertCell(0);
		td0.innerHTML="Port: ";
		td0.className="inst_td0";
		var td1 = tr.insertCell(1);
		td1.className="inst_td1";
		this.port = document.createElement("input");
		this.port.onkeyup=this.submit;
		this.port.type="text";
		this.port.value="3306";
		this.port.className="inputNormal";
		td1.appendChild(this.port);*/


		var tr = this.table.insertRow(-1);
		var td0 = tr.insertCell(0);
		td0.innerHTML="Username: ";
		td0.className="inst_td0";
		var td1 = tr.insertCell(1);
		td1.className="inst_td1";
		this.databaseUsername = document.createElement("input");
		this.databaseUsername.onkeyup=this.submit;
		this.databaseUsername.type="text";
		this.databaseUsername.className="inputNormal";
		td1.appendChild(this.databaseUsername);

		var tr = this.table.insertRow(-1);
		var td0 = tr.insertCell(0);
		td0.innerHTML="Password: ";
		td0.className="inst_td0";
		var td1 = tr.insertCell(1);
		td1.className="inst_td1";
		this.databasePassword = document.createElement("input");
		this.databasePassword.onkeyup=this.submit;
		this.databasePassword.type="password";
		this.databasePassword.className="inputNormal";
		td1.appendChild(this.databasePassword);

		var tr = this.table.insertRow(-1);
		var td0 = tr.insertCell(0);
		td0.innerHTML="Grant All Access";
		td0.className="inst_td0";

		this.databaseGrant = tr.insertCell(1);
		this.databaseGrant.className="inst_td1";

		var tr = this.table.insertRow(-1);
		this.databaseStatus = tr.insertCell(0);
		this.databaseStatus.colSpan = 2;
		this.databaseStatus.innerHTML="";
		this.databaseStatus.className="tdNormal";

		/* Database End  */

		/* Directories Begin  */
		var tr = this.table.insertRow(-1);
		var tdtitle = tr.insertCell(0);
		tdtitle.colSpan=2;
		tdtitle.innerHTML="Directories";
		tdtitle.className="app_grid_title___gray title";

		var tr = this.table.insertRow(-1);
		var td0 = tr.insertCell(0);
		td0.innerHTML="Workflow Data: ";
		td0.className="inst_td0";
		var td1 = tr.insertCell(1);
		td1.className="inst_td1";
		this.workflowData = document.createElement("input");
		this.workflowData.onkeyup=this.submit;
		this.workflowData.type="text";
		this.workflowData.value=this.options.path_data;
		this.workflowData.className="inputNormal";
		td1.appendChild(this.workflowData);

		var tr = this.table.insertRow(-1);
		var td0 = tr.insertCell(0);
		td0.innerHTML="Compiled templates: ";
		td0.className="inst_td0";
		var td1 = tr.insertCell(1);
		td1.className="inst_td1";
		this.compiled = document.createElement("input");
		this.compiled.onkeyup=this.submit;
		this.compiled.type="text";
		this.compiled.value=this.options.path_compiled;
		this.compiled.className="inputNormal";
		td1.appendChild(this.compiled);

		leimnud.dom.setStyle([this.workflowData,this.compiled],{
			textAlign:"left"
		});
	
		this.options.target.appendChild(this.table);
	};
	this.formData=function()
	{
		//alert(this.databaseExe.value.eplace("\\","/"))
		return {
				mysqlH	:this.databaseHostname.value,
				mysqlU	:this.databaseUsername.value,
				mysqlP	:this.databasePassword.value,
//				port	:this.port.value,
				path_data:this.workflowData.value,
				path_compiled:this.compiled.value
			};
	};
	this.check=function()
	{
		inst.loader.show();
		this.disabled(true);	
		var r = new leimnud.module.rpc.xmlhttp({
			url	:this.options.server,
			method	:"POST",
			args	:"action=check&data="+this.formData().toJSONString()
		});
		r.callback=function(rpc)
		{
			try
			{
				this.cstatus = rpc.xmlhttp.responseText.parseJSON();
			}
			catch(e)
			{
				this.cstatus={};
			}
			this.phpVersion.className = (!this.cstatus.phpVersion)?"inst_td1 tdFailed":"inst_td1 tdOk";
			this.phpVersion.innerHTML = (!this.cstatus.phpVersion)?"FAILED":"PASSED";

			this.mysqlVersion.className = (!this.cstatus.mysqlVersion)?"inst_td1 tdFailed":"inst_td1 tdOk";
			this.mysqlVersion.innerHTML = (!this.cstatus.mysqlVersion)?"FAILED":"PASSED";

			this.checkMemory.className = (!this.cstatus.checkMemory)?"inst_td1 tdFailed":"inst_td1 tdOk";
			this.checkMemory.innerHTML = (!this.cstatus.checkMemory)?"FAILED":"PASSED";

			this.checkmqgpc.className = (!this.cstatus.checkmqgpc)?"inst_td1 tdFailed":"inst_td1 tdOk";
			this.checkmqgpc.innerHTML = (!this.cstatus.checkmqgpc)?"FAILED":"PASSED";

			this.checkPI.className = (!this.cstatus.checkPI)?"inst_td1 tdFailed":"inst_td1 tdOk";
			this.checkPI.innerHTML = (!this.cstatus.checkPI)?"FAILED":"PASSED";

			this.checkDL.className = (!this.cstatus.checkDL)?"inst_td1 tdFailed":"inst_td1 tdOk";
			this.checkDL.innerHTML = (!this.cstatus.checkDL)?"FAILED":"PASSED";

			this.checkDLJ.className = (!this.cstatus.checkDLJ)?"inst_td1 tdFailed":"inst_td1 tdOk";
			this.checkDLJ.innerHTML = (!this.cstatus.checkDLJ)?"FAILED":"PASSED";

			this.databaseHostname.className = (!this.cstatus.mysqlConnection)?"inputFailed":"inputOk";
			this.databaseUsername.className = (!this.cstatus.mysqlConnection)?"inputFailed":"inputOk";
			this.databasePassword.className = (!this.cstatus.mysqlConnection)?"inputFailed":"inputOk";
			
			this.databaseGrant.className = (!this.cstatus.grantPriv)?"inst_td1 tdFailed":"inst_td1 tdOk";
			this.databaseGrant.innerHTML = (!this.cstatus.grantPriv)?"FAILED":"PASSED";

			this.databaseStatus.className = (!this.cstatus.grantPriv || !this.cstatus.mysqlConnection)?"tdFailed":"tdOk";
			this.databaseStatus.innerHTML = this.cstatus.databaseMessage;


			this.workflowData.className = (!this.cstatus.path_data)?"inputFailed":"inputOk";
			this.compiled.className = (!this.cstatus.path_compiled)?"inputFailed":"inputOk";

			if(this.cstatus.checkmqgpc && this.cstatus.checkMemory && this.cstatus.checkPI && this.cstatus.checkDL && this.cstatus.checkDLJ && this.cstatus.phpVersion && this.cstatus.mysqlVersion && this.cstatus.mysqlConnection && this.cstatus.grantPriv && this.cstatus.path_data && this.cstatus.path_compiled)
			{
				this.options.button0.disabled=true;
				this.options.button1.disabled=false;
			}
			else
			{
				this.options.button0.disabled=false;
				this.options.button1.disabled=true;
				this.disabled(false);
			}
			this.buttonFun(this.options.button0);
			this.buttonFun(this.options.button1);

			inst.loader.hide();

		}.extend(this);
		r.make();
	};
	this.reset=function()
	{
		this.options.button1.disabled=true;
		this.buttonFun(this.options.button1);
		this.disabled(false);
	};
	this.disabled=function(dis)
	{
		this.databaseHostname.disabled=dis;
		//this.databaseHostname.focus();
		this.databaseUsername.disabled=dis;
		//this.databaseUsername.focus();
		this.databasePassword.disabled=dis;
		//this.databasePassword.focus();
		this.workflowData.disabled=dis;
		//this.workflowData.focus();
		this.compiled.disabled=dis;
		if(this.compiled.disabled===false)
		{
			this.compiled.focus();
		}
		this.options.button0.disabled=dis;
		this.buttonFun(this.options.button0);
	};
	this.submit=function(evt)
	{
		var evt = (window.event)?window.event:evt;
		var key = (evt.which)?evt.which:evt.keyCode;
		if(key==13)
		{
			this.check();
		}
		return false;
	};
	this.install=function()
	{
		this.values = this.formData();
		inst.clearContent();
		inst.loader.show();
		this.options.button2.disabled=true;
		this.options.button1.disabled=true;
		var r = new leimnud.module.rpc.xmlhttp({
			url	:this.options.server,
			method	:"POST",
			args	:"action=install&data="+this.values.toJSONString()
		});
		r.callback=this.installation;
		r.make();
	};
	this.installation=function(rpc)
	{
/*		var r = new leimnud.module.rpc.xmlhttp({
			url	:"/sysworkflow/en/green/tools/updateTranslation",
			method	:"GET"
		});
		r.callback=function(rpc)
		{*/
			inst.loader.hide();
			this.table = document.createElement("table");
			this.table.className="inst_table";
	
			var tr = this.table.insertRow(-1);
			var tdtitle = tr.insertCell(0);
			tdtitle.innerHTML="Directories";
			tdtitle.className="app_grid_title___gray title";
	
			var tr = this.table.insertRow(-1);
			var td0 = tr.insertCell(0);
			td0.innerHTML="SUCCESS";
			td0.className="tdOk";
	
			var tr = this.table.insertRow(-1);
			var tdtitle = tr.insertCell(0);
			tdtitle.innerHTML="New Workspace";
			tdtitle.className="app_grid_title___gray title";
	
			var tr = this.table.insertRow(-1);
			var td0 = tr.insertCell(0);
			td0.innerHTML="SUCCESS";
			td0.className="tdOk";
			this.options.target.appendChild(this.table);

			var tr = this.table.insertRow(-1);
			var tdS = tr.insertCell(0);
			tdS.colSpan = 2;
			tdS.innerHTML="<br><br>";
			tdS.className="tdNormal";
	
	
			this.options.buttong = document.createElement("input");
			this.options.buttong.type="button";
			this.options.buttong.value="Finish Installation";
			this.options.buttong.onmouseup=function()
			{
				window.location = "/sysworkflow/en/green/login/login";
			}.extend(this);
			tdS.appendChild(this.options.buttong);
			this.buttonFun(this.options.buttong);
			tdS.appendChild(document.createElement("br"));
			tdS.appendChild(document.createElement("br"));

			var tr = this.table.insertRow(-1);
			var tdtitle = tr.insertCell(0);
			tdtitle.innerHTML="Installation Log";
			tdtitle.className="app_grid_title___gray title";

			var tr = this.table.insertRow(-1);
			var td0 = tr.insertCell(0);
			var pre = document.createElement('pre');
			pre.innerHTML=rpc.xmlhttp.responseText;
			td0.appendChild(pre);
//		}.extend(this);
//		r.make();
	};
	this.buttonFun=function(but)
	{
		if(but.disabled==true)
		{
			but.className="app_grid_title___gray button buttonDisabled";
			but.onmouseover=function(){ this.className="app_grid_title___gray button buttonDisabled"};
			but.onmouseout=function(){ this.className="app_grid_title___gray button buttonDisabled"};
			but.onblur=function(){ this.className="app_grid_title___gray button buttonDisabled"};
		}
		else
		{
			but.className="app_grid_title___gray button";
			but.onmouseover=function(){ this.className="app_grid_title___gray button buttonHover"};
			but.onmouseout=function(){ this.className="app_grid_title___gray button"};
			but.onblur=function(){ this.className="app_grid_title___gray button"};
		}
	};
	this.expand(this);
}
