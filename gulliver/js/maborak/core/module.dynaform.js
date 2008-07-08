/*

1.- leer un template
        - Crear array de posiciones.   Cada ID encontrado una posision. (WTF. como encontrar todos los elementos con ID)
2.- accordeonasdasd.... en DIV  panel...



*/
var $a='0123456789ABCDEF';
var $b=function()
{
    $g = $a.split('');
    return $g.random()+$g.random()+$g.random()+$g.random()+$g.random()+$g.random();
};
leimnud.Package.Public({
	info	:{
		Class	:"maborak",
		File	:"module.dynaform.js",
		Name	:"dynaform",
		Type	:"module",
		Version	:"0.1"
	},
	content	:function()
	{
		this.make=function(options)
		{
			this.options = {
			    template :'default.tpl',
			    target   :document.body,
			    points   :{},
			    dom      :{}
			}.concat(options || {});
			this.options.target.setStyle({
			    backgroundColor:'transparent',
			    position:'relative',
			    borderWidth:0
			});
			this.options.target.append(
                this.options.dom.actions = new DOM('div'),
                this.options.dom.body   = new DOM('div')
			);			
			this.options.dom.header = new DOM('div');
			this.setStyles();
			this.dynas=[];
			for(var i=0;i<12;i++)
			{
			    var d;
			    this.options.dom.header.append(
			          d = new DOM('input',{type:'button',value:i},{backgroundColor:'#'+$b(),width:30,margin:2,border:'1px solid red'})
			    );
			    this.dynas.push(d);
			}
			this.loadTemplate(this.options.template);			
			return this;
        };
        this.setStyles=function()
        {
            this.options.dom.actions.setStyle({
                border:'1px solid red',
                position:'absolute',
                width:200,
                height:400
            });
            this.options.dom.header.setStyle({
                border:'1px solid red',
                position:'absolute',
                top:100,
                left:250,
                width:300,
                height:100
            });
            this.options.dom.body.setStyle({
                border:'1px solid red',
                position:'relative',
                top:0,
                left:250,
                width:600,
                height:400
            });
        };
        this.loadTemplate=function(f)
        {
            var r = new this.parent.module.rpc.xmlhttp({
                url:f                
            });
            r.callback=function(rpc){
                    this.build({
                        template:rpc.xmlhttp.responseText
                    });
            }.extend(this);
            r.make();
        };
        this.build=function(o)
        {
            this.options.dom.body.innerHTML=o.template;
            this.options.dom.body.append(this.options.dom.header);
            var t = this.tplFirstChild();
            this.tplSetPoints({
                html:t
            });
            this.drop = new this.parent.module.drop().make();
            this.drag = new this.parent.module.drag({
				elements:this.dynas,
				fx:{
					type	: "clone",
					target  : this.options.dom.body,
					zIndex	: 11
				}
			});
			this.drag.events={
			    finish:function(){
			        alert([parseInt(this.drag.currentElementDrag.style.top)+2,parseInt(this.drag.currentElementDrag.style.left)+2])
			        //this.parent.dom.remove(this.drag.currentElementDrag);
			    }.extend(this)
			};
			this.drag.make();
            this.tplSetDropables();
        };
        this.tplFirstChild=function()
        {
            return this.options.target.firstChild;
        };
        this.tplSetPoints=function(o)
        {
            var t = o.html;
            var c = t.childNodes.length || 0;
            for(var i=0;i<c;i++)
            {
                var e = t.childNodes[i];
                if(e.id)
                {
                    this.options.points[e.id]=e;
                }
                if(e.childNodes && e.childNodes.length >0)
                {
                    this.tplSetPoints({
                        html:e
                    });
                }
            }
        };
        this.tplSetDropables=function()
        {
            var obj = this.options.points;
            for (var i in obj)
			{
				if(obj.propertyIsEnumerable(i))
				{
            	   this.drop.register({
                        element:obj[i],
            		    value:i,
            		    events:{
            			    over:function(e)
            				{
            				}.extend(this),
            				out:function(e)
            				{
            				}.extend(this)            
            			}});
				}
			}
            //this.drop.register
        };
		this.expand(this);
		return this;
	}
});
