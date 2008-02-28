/***************************************************************************
*     				  		   module.rpc.js
*                        ------------------------
*   Copyleft	: (c) 2007 maborak.com <maborak@maborak.com>
*   Version		: 0.2
*
***************************************************************************/

/***************************************************************************
*
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
*
***************************************************************************/
/**
* @class rpc
*/
leimnud.Package.Public({
	info	:{
		Class	:"maborak",
		File	:"module.rpc.js",
		Name	:"rpc",
		Type	:"module",
		Version	:"0.1"
	},
	content	:{
		/*
		* @class xmlhttp
		* @param {Object} options Options
		* 	 @param {String} options.url Url to Open
		* 	 @param {String} options.method Method
		* 	 @param {String} options.arguments Arguments [Optional,Default=""]
		* 	 @param {String} options.async Asynchronous? [Optional,Default=true]
		*
		* @param {Function | Object | Virtual function} Instance.callback Callback for this process
		* @example:
		*	var process = new leimnud.module.rpc.xmlhttp({
		*				url		:"g.xml",
		*				method		:"POST",
		*				arguments	:"u=iuiu"
		*			});
		*	process.callback=functionCallback;
		*
		*		||
		*
		*	process.callback=leimnud.closure({method:Myinstance.callback,instance:MyInstance,arguments:[process,"demo",99]});
		*
		*		||
		*
		*	process.callback={Function:myFunction,arguments:[process,"demo"]};
		*	process.make();
		*
		*/
		xmlhttp:function(options)
		{
			this.options=options || {};
			this.headers=[];
			this.core = function()
			{
				try{
					var xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
				}
				catch(e)
				{
					try
					{
						var xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
					}
					catch(e)
					{
						var xmlhttp = false;
					}
				}
				return (!xmlhttp && typeof XMLHttpRequest!='undefined')?
				new XMLHttpRequest():xmlhttp || new function(){};
			};
			/*
			* Make this process
			*/
			this.make=function()
			{
				this.xmlhttp		= this.core();
				this.url		= this.options.url || false;
				if(!this.options.url || !this.xmlhttp){return false;}
				this.method		= (this.options.method || "POST").toUpperCase();
				this.args		= this.options.args || "";
				this.async		= (this.options.async===false)?false:true;
				if(this.method=="POST"){this.header("Content-Type","application/x-www-form-urlencoded");}
				this.open();
				return true;
			};

			/*
			*
			* Open this.options.url request
			*
			*/
			this.open=function()
			{
				this.url = ((this.url.substr(this.url.length-1,1)!=="?" && this.method==="GET")?this.url+"?":this.url);
				this.url = ((this.method=="GET")?this.url+this.args:this.url);
				this.xmlhttp.open(this.method,this.url+((this.options.nocache)?"&rand_rpc="+Math.random():""),this.async);
				this.applyHeaders();
				this.xmlhttp.send((this.method=="GET")?null:this.args);
				//this.xmlhttp.onreadystatechange=this.parent.closure({method:this.changes,instance:this,args:98989898});
				this.xmlhttp.onreadystatechange=this.changes;
			};
			/*
			*
			* Method for this.xmlhttp.onreadystatechange
			*
			*/
			this.changes=function(g)
			{
				if (this.xmlhttp.readyState==4)
				{
					if(this.callback)
					{
						//this.callback=(this.callback.isObject)?this.parent.closure(this.callback):this.callback;
						this.callback.args(this)();
					}
				}
			};
			/*
			*
			* Apply headers
			*
			*/
			this.applyHeaders=function()
			{
				for(var i=0;i<this.headers.length;i++)
				{
					this.xmlhttp.setRequestHeader(this.headers[i].param,this.headers[i].value);
				}
			};
			/*
			*
			* Set Request Headers
			* @param {String} param Variable header 
			* @param {String} value Value header
			*
			*/
			this.header=function(param,value)
			{
				this.headers.push({
					param:param,
					value:value
				});
			};
			this.expand(this);
//			return this;
		}
	}
});
