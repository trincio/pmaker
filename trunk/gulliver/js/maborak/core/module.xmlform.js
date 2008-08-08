leimnud.Package.Public({
	info	:{
		Class	:"maborak",
		File	:"module.xmlform.js",
		Name	:"xmlform",
		Type	:"module",
		Version	:"0.1"
	},
	content	:function()
	{
		this.make=function(options)
		{
			this.options = {
				file:'myInfo.xml',
				debug:false
			}.concat(options || {});
			this.db=[];
			this.debug = new this.parent.module.debug(this.options.debug || false);
			/*Samples load XML*/
			this.loadXML(this.options.file);
		};
		this.loadXML=function(xml)
		{
			var r = new this.parent.module.rpc.xmlhttp({
				url:xml
			});
			r.callback=this.parse;
			r.make();
		};
		this.parse=function(rpc)
		{
				window.d = this.xml = rpc.xmlhttp.responseXML;
				//alert(XMLSerializer)
				/*var w = d.createElement('wilmer');
				//crear CDATA
				var f = d.createCDATASection('Secci�n CDATA');
				w.appendChild(f);
				//crear ATRIBUTO
				var af = d.createAttribute("mi_atributo");
				af.nodeValue="valor de mi atributo";
				w.setAttributeNode(af);
				
				//d.documentElement.appendChild(w);
				d.documentElement.insertBefore(w,d.documentElement.childNodes.item(0));
				
				var s = new XMLSerializer();
				var str = s.serializeToString(d);
				$(document.body).append(
					new DOM('textarea',{value:str},{width:'100%',height:400})
				);
							
				//alert(d.documentElement.childNodes.length);
				
				var table = new DOM('table',{border:1},{width:'100%',borderCollapse:'collapse'});

				var tr = table.insertRow(-1);
				$(tr).append(
					new DOM('td',{innerHTML:'<b>CAMPO</b>'},{width:"50%",border:'1px solid black'}),
					new DOM('td',{innerHTML:'<b>TIPO</b>'},{width:"50%",border:'1px solid black'})
				);
				for(var i=0;i<d.documentElement.childNodes.length;i++)
				{
					var c = d.documentElement.childNodes[i];
					try{
						var at = c.getAttribute('type');
					}catch(e){
						var at = '';
					}
					var tr = table.insertRow(-1);
					$(tr).append(
						new DOM('td',{innerHTML:c.nodeName},{width:"50%",border:'1px solid black'}),
						new DOM('td',{innerHTML:at},{width:"50%",border:'1px solid black'})
					);
				}
				document.body.appendChild(table);*/
				for(var i=0;i<this.xml.documentElement.childNodes.length;i++)
				{
					var c = this.xml.documentElement.childNodes[i];
					try{
						var at = c.getAttribute('type');
					}catch(e){
						var at = '';
					}
					if(c.nodeName!=='#text' && c.nodeName!=='#comment')
					{
						this.add_to_db(c);
					}
					//console.info(c.nodeName+"="+c.nodeValue);
					/*var tr = table.insertRow(-1);
					$(tr).append(
						new DOM('td',{innerHTML:c.nodeName},{width:"50%",border:'1px solid black'}),
						new DOM('td',{innerHTML:at},{width:"50%",border:'1px solid black'})
					);*/
				}
				return (this.options.onload || function(){})();
		};
		this.add_to_db=function(tag)
		{
//			this.debug.log(tag);
			this.db.push(tag);
		};
		this.serialize=function()
		{
			var s = new XMLSerializer();
			return s.serializeToString(this.xml);
		};
		this.show_dyna_elements=function(num)
		{
			return this.db[num];
		};
		this.show_dyna=function()
		{
			return this.xml.documentElement;
		};
		this.tag_attributes_to_object=function(tag)
		{
			var o = {
				nodeName:tag.nodeName
			};
			//this.debug.log(tag.nodeName+":"+tag.attributes)
			for(var i=0;i<tag.attributes.length;i++)
			{
				var atr = tag.attributes[i];
				o[atr.nodeName]=atr.nodeValue;
			}
			return o;
		};
		this.tag_edit=function(tag,ce)
		{
			if(!this.options.target || this.current_edit===ce){return false;}
			this.options.target.innerHTML='';
			this.current_edit=ce;
			//this.debug.log(tag);
			//tag=tag.documentElement;
			var data=[];
			for(var i=0;i<tag.attributes.length;i++)
			{
				var atr = tag.attributes[i];
				data.push({
					data:[{value:atr.nodeName},{value:atr.nodeValue}]
				});
			}
			new this.parent.module.grid().make({
				target	:this.options.target,
				paginator	:{
					limit	:10
				},
				title	:"<b>"+tag.nodeName+"</b>",
				data	:{
					column:[
					{
						title:"Attribute",
						type:"text",
						edit:false,
						paint:'bg1',
						width:"40%",
						onchange	:function(data)
						{
							//alert(data.index+":"+data.dom)
						},
						style:{
							fontWeight:"bold"
						},
						styleValues:{
							textAlign:"right"							
						}
					},
					{
						title	: "Value",
						type	: "text",
						edit	: true,
						style:{
							fontWeight:"bold"
						},
						onchange	:function(data)
						{
							//alert(data.index+":"+data.dom)
						},
						width	: "60%"
					}
				],
				rows:data
			}
			});
			return true;
		};
		this.expand(this);
	}
});
