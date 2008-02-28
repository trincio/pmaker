var cases=function()
{
	this.parent = leimnud;
	this.panels = {};
	this.make=function(options)
	{
		this.options.target = this.parent.dom.element(this.options.target);
		var panel;
		/* Panel list Begin */
		this.panels.list = panel = new leimnud.module.panel();
		panel.options={
			size	:{w:310,h:250},
			position:{x:50,y:50},
			title	:"List",
			theme	:"processmaker",
			target	:this.options.target,
			statusBar:true,
			limit	:true,
			control	:{resize:false,close:true,roll:false},
			fx	:{opacity:true,rollWidth:150,fadeIn:false}
		};

		/* Panel list End */
		/* Panel step Begin */
			this.panels.step = panel = new this.parent.module.panel();
			this.panels.step.options={
				size:{w:260,h:550},
				title	:this.options.title,
				//headerBar:true,
				//titleBar:false,
				//elementToDrag:"headerBar",
				target:this.options.target,
				cursorToDrag:"move",
				position:{x:5,y:5},
				limit:true,
				fx:{shadow:false,modal:false,opacity:false}
			};
			this.panels.step.setStyle={
/*				containerWindow:{
					border:"0px solid red"
				},
				frontend:{
					backgroundColor:"transparent"
				},
				content:{
					margin:0,
					border:"0px solid red",
					borderLeft:"1px solid #DADADA",
					borderRight:"1px solid #DADADA",
					backgroundColor:"white",
					paddingTop:15
				},
				headerBar:{
					display:''
					//height:16,
					//border:"1px solid red"
				},
				titleBar:{
					background:"none",
					backgroundColor:"transparent",
					height:18
				},
				close:{
					display:"none"
				},*/
				statusBar:{
				}
			};
			this.panels.step.styles.fx.opacityModal.Static=0;
			this.panels.step.make();
			this.panels.step.events = {
				remove: function() { delete(this.panels.step); }.extend(this)
			};

//			this.panels.step.elements.headerBar.className="boxTopPanel";
//			this.panels.step.elements.headerBar.innerHTML="<div class='a'></div><div class='b'></div><div class='c'></div>";

//			this.panels.step.elements.statusBar.className="boxBottom";
//			this.panels.step.elements.statusBar.innerHTML="<div class='a'></div><div class='b'></div><div class='c'></div>";
			
/*			var cl = document.createElement("div");
			cl.onmouseup=this.panels.step.remove;
			this.parent.dom.setStyle(cl,{
				width:13,
				height:13,
				top:30,
				right:5
			});
			cl.className=this.panels.step.elements.close.className;
			this.panels.step.elements.frontend.appendChild(cl);*/


/*		this.panels.step = panel = new leimnud.module.panel();
		panel.options={
			size	:{w:310,h:550},
			position:{x:5,y:5},
			title	:this.options.title,
			theme	:"processmaker",
			target	:this.options.target,
//			limit	:true,
			statusBar:true,
			control	:{resize:true,roll:false},
			fx	:{opacity:false,blinkToFront:false,fadeIn:false}
		};
		this.panels.step.events = {
			remove: function() { delete(this.panels.step); }.extend(this)
		};*/

		/* Panel step End */
		/* Panel history Begin */
		/* Panel history End */
//		this.panels.step.make();
		/* Load data BEGIN */
		panel.loader.show();
		var r = new this.parent.module.rpc.xmlhttp({
			url:this.options.dataServer,
			args:"action="+this.options.action
		});
		r.callback=function(rpc){
			this.panels.step.loader.hide();
			var scs=rpc.xmlhttp.responseText.extractScript();
			this.panels.step.addContent(rpc.xmlhttp.responseText);
			scs.evalScript();
		}.extend(this);
		r.make();
		/* Load data END */
		//this.panels.list.make();
	}
	this.expand(this);
}
