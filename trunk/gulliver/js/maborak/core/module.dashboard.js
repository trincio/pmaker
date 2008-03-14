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
				drag:true,
				panel:[],
				data:[]
			}.concat(options || {});
			this.drop = new this.parent.module.drop();
			this.drop.make();

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
					//position:'relative',
					verticalAlign:'top'
				});
			}
			this.parseData();
			this.drop.setArrayPositions(true);
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
				control:{resize:false,roll:true,drag:this.options.drag,close:false},
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
			if(options.noBg)
			{
				panel.setStyle.content.concat({
					backgroundColor:"#DFDFDF",
					borderWidth:0
				});
				panel.setStyle.containerWindow.concat({
					backgroundColor:"#DFDFDF"					
				});
				panel.setStyle.frontend={
					backgroundColor:"#DFDFDF"
				};
			}
			panel.events={
				init:[function(i){
					var e = this.options.panel[i].panel.elements.containerWindow;
					var p;
					this.currentPhantom = p = new DOM("div",false,{
						width:e.clientWidth,
						height:e.clientHeight,
						border:"1px dashed red",
						position:"relative",
						margin:3
					});
					if(e.nextSibling)
					{
						e.parentNode.insertBefore(p,e.nextSibling);
					}
					else
					{
						e.parentNode.appendChild(p);
					}
					//console.info(e.nextSibling)
					//console.info(e.clientWidth+":"+e.clientHeight)
				}.extend(this,this.options.panel.length)],
				move:function(i){
					var e = this.options.panel[i].panel.elements.containerWindow;
					this.drop.captureFromArray({currentElementDrag:e});
					if(this.drop.selected!==false)
					{
						
					}
					this.de = this.drop.selected;
					console.info(this.drop.selected)
				}.extend(this,this.options.panel.length),
				finish:function(i){
					var e = this.options.panel[i].panel.elements.containerWindow;
					this.currentPhantom.parentNode.replaceChild(e,this.currentPhantom);
					this.parent.dom.setStyle(e,{
						left:"auto",
						top:"auto",
						position:"relative"
					});
				}.extend(this,this.options.panel.length)

			};
			panel.make();
			if(options.open)
			{
				panel.open(options.open);
			}
			this.options.panel.push({
				panel:panel,
				index:this.options.panel.length-1
			});
			this.drop.register({
				element:panel.elements.containerWindow,
				value:this.options.panel.length-1
			});
			return panel;	
		};
		this.expand(this);
	}
});

