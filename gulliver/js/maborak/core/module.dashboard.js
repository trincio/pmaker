/***************************************************************************
*     			      module.dashboard.js
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
* @class drag
*/
leimnud.Package.Public({
	info	:{
		Class	:"maborak",
		File	:"module.dashboard.js",
		Name	:"dashboard",
		Type	:"module",
		Version	:"0.1"
	},
	content	:function(){
		this.elements	= {};
		this.make=function(options)
		{
			this.options	= {
				data:[]
			}.concat(options || {});
			var width	= this.options.target.offsetWidth-50;
			this.columns	= this.options.data.length;
			this.widthColumn = (width/this.columns);
			this.elements.column=[];
			this.elements.table 	= document.createElement('table');
			$(this.elements.table).setStyle({
				width:width,
				borderCollapse:'collapse'
			})
			this.elements.tr	= this.elements.table.insertRow(-1);
			this.options.target.append(this.elements.table);
			for(var i=0;i<this.columns;i++)
			{
				this.elements.column[i]=this.elements.tr.insertCell(i);
				this.parent.dom.setStyle(this.elements.column[i],{
					width	:width/this.columns,
					border	:'0px solid red',
					position:'relative',
					verticalAlign:'top'
				});
			}
			this.parseData();
		};
		this.parseData=function()
		{
			for(var i=0;i<this.columns;i++)
			{
				var column = this.options.data[i];
				for(var j=0;j<column.length;j++)
				{
					var wd = column[j];
					this.panel({
						target:this.elements.column[i]
					}.concat(wd));
				}
			}
		};
		this.panel=function(options)
		{
			var panel = new this.parent.module.panel();
			panel.options={
				target:options.target,
				title	:options.title || "",
				size:{w:this.widthColumn,h:options.height || 300},
				position:{x:0,y:0},
				statusBar:false,
				control:{resize:false,roll:true,drag:false,close:false},
				fx:{shadow:false}
			};
			panel.setStyle={
				containerWindow:{
					position:'relative',
					border:"1px solid #afafaf",
					margin:3
				},
				content:{
					overflow:"hidden"
				},
				titleBar:{
					backgroundImage:"url("+this.parent.info.images+"grid.title.gray.gif)",
					backgroundPosition:"0pt 0px"
				}
			};
			panel.make();
			if(options.open)
			{
				panel.open(options.open);
			}
			return panel;	
		};
		this.expand(this);
	}
});

