leimnud.Package.Public({
	info	:{
		Class	:"maborak",
		File	:"module.dom.js",
		Name	:"dom",
		Type	:"module",
		Version	:"0.1"
	},
	content	:{
		button:function(label,go,style)
		{
			this.make=function(label,go,style)
			{
				this.button = document.createElement("input");
				this.button.className="module_app_button___gray";
				this.button.type="button";
				this.button.value=label || "Button";
				this.button.disable=function()
				{
					this.button.disabled=true;
					this.button.className="module_app_button___gray module_app_buttonDisabled___gray";
					return this.button;
				}.extend(this);
				this.button.enable=function()
				{
					this.button.disabled=false;
					this.button.className="module_app_button___gray";
					return this.button;
				}.extend(this);
				this.parent.event.add(this.button,"mouseover",this.mouseover);
				this.parent.event.add(this.button,"mouseout",this.mouseout);
				this.parent.dom.setStyle(this.button,style || {});
				if(typeof go==="function"){this.parent.event.add(this.button,"mouseup",go.args(this.button));}
				return this.button;
			};
			this.mouseover=function()
			{
				this.button.className="module_app_button___gray module_app_buttonHover___gray";
				return false;
			};
			this.mouseout=function()
			{
				this.button.className="module_app_button___gray";
				return false;
			};
			this.expand();
			return this.make(label,go,style);
		},
		input:function(label,style)
		{
			this.make=function(label,style)
			{
				this.input = document.createElement("input");
				this.input.className="module_app_input___gray";
				this.input.type="text";
				this.input.value=label || "";
				this.input.disable=function()
				{
					this.input.disabled=true;
					this.input.className="module_app_input___gray module_app_inputDisabled___gray";
					return this.button;
				}.extend(this);
				this.input.enable=function()
				{
					this.input.disabled=false;
					this.input.className="module_app_input___gray";
					return this.button;
				}.extend(this);
				this.parent.event.add(this.input,"mouseover",this.mouseover);
				this.parent.event.add(this.input,"mouseout",this.mouseout);
				this.parent.dom.setStyle(this.input,style || {});
				return this.input;
			};
			this.mouseover=function()
			{
				this.input.className="module_app_input___gray module_app_inputHover___gray";
				return false;
			};
			this.mouseout=function()
			{
				this.input.className="module_app_input___gray";
				return false;
			};
			this.expand();
			return this.make(label,style);
		},
		create:function(dom,properties,style)
		{
			this.dom = document.createElement(dom);
			this.parent.dom.setProperties(this.dom,properties || {});
			this.parent.dom.setStyle(this.dom,style || {});
			return new this.parent.module.dom.methods(this.dom);
		},
		methods:function(dom)
		{
			this.dom = dom;
			this.dom.dom = this.dom;
			this.dom.append = function()
			{
				for(var i=0;i<arguments.length;i++)
				{
					if(arguments[i])
					{
						this.dom.appendChild(arguments[i]);
					}
				}
				return this.dom;
			}.extend(this);
			this.dom.remove = function()
			{
				this.dom.parentNode.removeChild(this.dom);
			}.extend(this);
			this.dom.setStyle = function(style)
			{
				this.parent.dom.setStyle(this.dom,style || {});
			}.extend(this);

			return this.dom;
		}
	}
});
