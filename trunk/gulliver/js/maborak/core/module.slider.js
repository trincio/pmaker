leimnud.Package.Public({
	info	:{
		Class	:"maborak",
		File	:"module.slider.js",
		Name	:"slider",
		Type	:"module",
		Version	:"0.1"
	},
	content	:{
		create:function()
		{
			this.make=function(options)
			{
				this.options = {
					points:[],
					width:564,
					selected:0,
					onchange:function(){}
				}.concat(options || {});
				if(typeof this.options.points=='string'){
					this.options.points=this.parse_string_points(this.options.points);
				}
				this.current=0;
				this.dom = {};
				this.make_points();
				this.calculate(true);
				return this;
			};
			this.parse_string_points=function(s)
			{
				var a = s.split("-");
				var x = parseInt(a[0]) || 0;
				var y = parseInt(a[1]) || 1;
				var y = (y<=x)?(x+1):y;
				var i = 0;
				var a = [];
				for(x;x<=y;x++)
				{
					a.push({value:x,label:''});
				}
				return a;
			};
			this.make_points=function()
			{
				this.real_width = this.options.width-64;
				this.spaces();
				this.options.target.append(
					this.dom.a = new DOM('div',{},{position:'relative',height:30,width:this.options.width,border:'1px solid #EEE'}).append(
						new DOM('div',false,{position:'absolute',width:30,height:30,left:0,top:0,backgroundColor:'orange'}),
						    this.dom.dragbox = new DOM('div',false,{position:'absolute',width:this.real_width,height:30,left:31,top:0,border:'0px solid orange'}).append(
								this.dom.drag = new DOM('div',false,{position:'absolute',width:30,height:30,left:0,top:0,background:'black',cursor:'move'})
						),
						new DOM('div',false,{position:'absolute',width:30,height:30,right:0,top:0,border:'1px solid green',backgroundColor:'green'})
					)
				);
				this.drag = new this.parent.module.drag({
					elements:this.dom.drag,
					limit:"y",
					limitbox:this.dom.dragbox
				});
				this.drag.events={
					move:this.calculate	
				};
				this.drag.make();
			};
			this.spaces = function()
			{
				this.sep = parseInt(this.real_width/this.options.points.length);
				this.sep_dec = this.real_width/this.options.points.length;
				this.spaces = [];
				var a = 0;
				for(var i=0;i<this.options.points.length;i++)
				{
					//this.spaces.push([(i==0)?0:((this.sep*i)+1),(i==0)?this.sep*(i+1):(this.sep*(i+1))+1]);
					this.spaces.push([a,a+(this.sep-1)]);
					a = a+this.sep;
				}
			};
			this.calculate=function(manual)
			{
		//		this.current =
				if(manual)
				{
					var ix = this.spaces[this.options.selected][0];
					this.drag.options.elements[0].style.left=ix;
					var c = ix+(this.drag.options.elements[0].clientWidth/2);
				}
				else
				{
					var c = this.drag.currentX+(this.drag.currentElementDrag.clientWidth/2);
				}
				var t=this.options.selected;
				if(c<0)
				{
					t=0;
				}
				else if(c>=this.real_width)
				{
					t=(this.spaces.length-1);
				}
				else
				{
					for(var i=0;i<this.spaces.length;i++)
					{
						if(c>=this.spaces[i][0] && c <=this.spaces[i][1])						
						{
							t=i;
							break;
						}
						else if(i==(this.spaces.length-1) && c>=this.spaces[i][1])
						{
							t=this.spaces.length-1;
							break;
						}
					}
				}
				if(t!=this.current || manual)
				{
					this.current=t;
					return this.options.onchange();
				}
			};
			this.get=function(uid)
			{
				uid = uid || this.current;
				return this.options.points[uid] || this.options.points[0];
			};
			this.expand(this);
			return this;
		},
		eventor:function()
		{
			this.make=function(options)
			{
				this.options = {
					slides:[],
					operation:"+",
					result:function(){}
				}.concat(options || {});
				for(var i=0;i<this.options.slides.length;i++)
				{
					var s = this.options.slides[i];
					var o = s.options.onchange;
					s.options.onchange=function(o,op){
						op();
						return o();
					}.extend(s,o,this.operation)
				}
				return this.operation();
			};
			this.operation=function()
			{
				var t;
				if(this.operation==="+")
				{
					t=0;
				}
				else if(this.operation==="*")
				{
					t=1;
				}
				else
				{
					t=0;
				}
				for(var i=0;i<this.options.slides.length;i++)
				{
					var s = this.options.slides[i];
					if(this.options.operation==="+")
					{
						t=t+s.get().value;
					}
				}
				return this.options.result(t);
			};
			this.expand(this);
			return this;
		}
}});
