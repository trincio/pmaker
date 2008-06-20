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
			this.options = {}.concat(options || {});

			this.options.target.append(new DOM('div').append(
				this.domI = new DOM('input',{type:'text'}),
				this.domB = new DOM('input',{type:'button',value:'drag me'})
			));

			this.table = new DOM('table',{border:1},{width:'100%',borderCollapse:'collapse'});


			var tr = this.table.insertRow(-1);
			$(tr).append(
				this.T1 = new DOM('td',{innerHTML:'<br><br>'},{width:"50%",border:'1px solid black'}),
				this.T2 = new DOM('td',{innerHTML:'<br><br>'},{width:"50%",border:'1px solid black'})
			);
			
			var tr = this.table.insertRow(-1);
			$(tr).append(
				this.T3 = new DOM('td',{innerHTML:'<br><br>'},{width:"50%",border:'1px solid black'}),
				this.T4 = new DOM('td',{innerHTML:'<br><br>'},{width:"50%",border:'1px solid black'})
			);

			var tr = this.table.insertRow(-1);
			$(tr).append(
				this.T5 = new DOM('td',{innerHTML:'<br><br>'},{width:"50%",border:'1px solid black'}),
				this.T6 = new DOM('td',{innerHTML:'<br><br>'},{width:"50%",border:'1px solid black'})
			);


			this.options.target.append(this.table);

			this.dragables = new this.parent.module.drag({
				elements:[this.domI,this.domB],
				fx:{
					type	: "clone",
					target	: this.options.target,
					zIndex	: 11
				}
			});
			this.dropables = new this.parent.module.drop();
			this.dropables.make();

			this.dropables.register({element:this.T5,value:1,events:{
				over:function(e)
				{
					e.style.backgroundColor="red";
				}.extend(this,this.T5),
				out:function(e)
				{
					e.style.backgroundColor="transparent";
				}.extend(this,this.T5)

			}});
			this.dropables.register({element:this.T1,value:1,events:{
/*				over:function()
				{
					this.dropables.elements[this.dropables.selected].element.style.backgroundColor="red";
				}.extend(this),
				out:function()
				{
					this.dropables.elements[this.dropables.selID].element.style.backgroundColor="transparent";
				}.extend(this)*/

			}});
			this.dropables.register({element:this.T2,value:1,events:{
/*				over:function()
				{
					this.dropables.elements[this.dropables.selected].element.style.backgroundColor="red";
				}.extend(this),
				out:function()
				{
					this.dropables.elements[this.dropables.selID].element.style.backgroundColor="transparent";
				}.extend(this)*/

			}});
			this.dropables.register({element:this.T3,value:1,events:{
/*				over:function()
				{
					this.dropables.elements[this.dropables.selected].element.style.backgroundColor="red";
				}.extend(this),
				out:function()
				{
					this.dropables.elements[this.dropables.selID].element.style.backgroundColor="transparent";
				}.extend(this)*/

			}});
			this.dropables.register({element:this.T4,value:1,events:{
/*				over:function()
				{
					this.dropables.elements[this.dropables.selected].element.style.backgroundColor="red";
				}.extend(this),
				out:function()
				{
					this.dropables.elements[this.dropables.selID].element.style.backgroundColor="transparent";
				}.extend(this)*/

			}});

			this.dragables.events={

				move:this.dropables.capture.args(this.dragables),
				finish	: function(){
					if(this.dropables.selected!==false)
					{
//						this.dropables.derivation.launchEvents(this.dropables.derivation.elements[this.dropables.derivation.selected].events.out);
//						vAux = this.dropables.derivation.launchEvents(this.dropables.derivation.elements[this.dropables.derivation.selected].events.click);
						
						var c,t = this.dropables.elements[this.dropables.selected].element,d = $(this.dragables.currentElementDrag);
						d.setStyle({
							position:'relative',
							left:'auto',
							top:'auto',
							opacity:1
						});

						t.append(
							c = new DOM('div')
						);
						t.replaceChild(d,c);
//						this.parent.dom.remove(this.dragables.currentElementDrag);
					}
					else
					{
						this.parent.dom.remove(this.dragables.currentElementDrag);
					}
				}.extend(this)
			};
			this.dragables.make();

		};
		this.expand(this);
	}
});
