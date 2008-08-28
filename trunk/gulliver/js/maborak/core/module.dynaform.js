/*

1.- leer un template
- Crear array de posiciones.   Cada ID encontrado una posision. (WTF. como encontrar todos los elementos con ID)
2.- accordeonasdasd.... en DIV  panel...
3.- * <name campo='nombre' x='11' y='11'>

* <name campo='nombre' x='11' y='11'>

4.-  arrastrar soltar NO. right click Elementos.




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
                dom      :{},
				drop	 :{},
				drag	 :{},
				menu	 :{},
				debug	 :false,
				observers:{}
            }.concat(options || {});
			this.db=[];
			this.debug = new this.parent.module.debug(this.options.debug || false);
			this.options.observers['menu'] = new this.parent.pattern.observer();
            this.options.target.setStyle({
				textAlign:'center'
            });
			this.options.target.append(
				table =new DOM('table',{align:'center'},{width:'99%'})
			);
			var tr = table.insertRow(-1);
			$(tr).append(
				new DOM('td').append(
					new DOM('div',{className:'boxTop'}).append(
						new DOM('div',{className:'a'}),
						new DOM('div',{className:'b'}),
							new DOM('div',{className:'c'})
						),
						this.options.dom.body = new DOM('div',{className:'boxContentNormal'},{minHeight:this.options.target.clientHeight-50,paddingBottom:20}),
						new DOM('div',{className:'boxBottom'}).append(
							new DOM('div',{className:'a'}),
							new DOM('div',{className:'b'}),
							new DOM('div',{className:'c'})
					)
				)
			);
//			this.options.dom.body = this.options.target;
/*            this.options.target.append(
	            this.options.dom.body   = new DOM('div')
            );*/
//            this.options.dom.header = new DOM('div');
/*            this.setStyles();
            this.dynas=[];
            for(var i=0;i<12;i++)
            {
                var d;
                this.options.dom.header.append(
     	           d = new DOM('input',{type:'button',value:i},{backgroundColor:'#'+$b(),width:30,margin:2,border:'1px solid red'})
                );
                this.dynas.push(d);
            }*/
            this.load({
				template:this.options.template,
				xmlform:this.options.xmlform
			});
            return this;
        };
        this.setStyles=function()
        {
/*            this.options.dom.actions.setStyle({
                border:'1px solid red',
                position:'absolute',
                width:200,
                height:400
            });
            this.options.dom.header.setStyle({
                border:'1px solid red',
                position:'relative',
                top:100,
                left:250,
                width:300,
                height:100
            });*/
            this.options.dom.body.setStyle({
                border:'1px solid red',
                position:'relative',
                top:0,
                left:250,
                width:600,
                height:400
            });
        };
        this.load=function(options)
        {
            var r = new this.parent.module.rpc.xmlhttp({
                url:options.template
            });
            r.callback=function(rpc){
				this.xmlform = new this.parent.module.xmlform();
				this.xmlform.make({
					file	:options.xmlform,
					target	:this.options.target_info,
					debug	:this.options.debug,
					onload	:function(){
						this.xmlform.tag_edit(this.xmlform.show_dyna(),'dyna_root');
						this.parse_elements();
					}.extend(this)
				});
                this.build({
                    template:rpc.xmlhttp.responseText
                });
            }.extend(this);
            r.make();
        };
        this.build=function(o)
        {
            this.options.dom.body.innerHTML=o.template;
            //this.options.dom.body.append(this.options.dom.header);
            var t = this.tplFirstChild();
            this.tplSetPoints({
                html:t
            });
            this.options.drop['groups'] = new this.parent.module.drop().make();
/*
            this.drag = new this.parent.module.drag({
                elements:this.dynas,
                fx:{
                    type	: "clone",
                    target  : this.options.dom.body,
                    zIndex	: 11
                }
            });
            this.drag.events={
                move:this.drop.captureFromArray.args(this.drag),
                finish:function(){
                    //alert([parseInt(this.drag.currentElementDrag.style.top)+2,parseInt(this.drag.currentElementDrag.style.left)+2])
                    //this.parent.dom.remove(this.drag.currentElementDrag);
                    //console.info(this.drop.selected);
                    if(this.drop.selected!==false)
                    {
                        //						this.dropables.derivation.launchEvents(this.dropables.derivation.elements[this.dropables.derivation.selected].events.out);
                        //						vAux = this.dropables.derivation.launchEvents(this.dropables.derivation.elements[this.dropables.derivation.selected].events.click);

                        var c,t = this.drop.elements[this.drop.selected].element,d = $(this.drag.currentElementDrag);
                        d.setStyle({
                            position:'relative',
                            left:'auto',
                            top:'auto',
                            opacity:1
                        });

                        $(t).append(
                        c = new DOM('div')
                        );
                        t.replaceChild(d,c);
                        var men = new this.parent.module.app.menuRight();
                        men.make({
                            //target:this.panels.editor.elements.content,
                            target:d,
                            width:201,
                            //theme:this.options.theme,
                            menu:[
                            {text:'b',launch:function(){}},
                            {text:'b',launch:function(){}},
                            ]
                        });
                        //						this.parent.dom.remove(this.dragables.currentElementDrag);
                        //this.drop.setArrayPositions(true);
                    }
                    else
                    {
                        this.parent.dom.remove(this.drag.currentElementDrag);
                    }
                }.extend(this)
            };

            this.drag.make();*/


            this.tplSetDropables();

            this.options.drop['groups'].setArrayPositions(true);

            this.menu = new this.parent.module.app.menuRight();
			this.options.observers['menu'].register(this.menu.remove,this.menu);
            this.menu.make({
                target:this.options.dom.body,
                width:201,
//                theme:'processmaker',
                menu:[
	                {text:'Templates',launch:function(){}},
   		            {text:'Properties',launch:function(){}}
                ]
            });
        };
        this.tplFirstChild=function()
        {
            //return this.options.dom.body.firstChild;
            return this.options.dom.body;
        };
        this.tplSetPoints=function(o)
        {
            var t = o.html;
            var c = t.childNodes.length || 0;
			//alert(o.html.childNodes)
            for(var i=0;i<c;i++)
            {
                var e = $(t.childNodes[i]);
                if(e.id)
                {
					e.setStyle({
						position:'relative'
					});
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
		this.tpl_default=function()
		{
			return this.options.points.get_by_key(0,true);
		};
        this.tplSetDropables=function()
        {
            var obj = this.options.points;
            for (var i in obj)
            {
                if(obj.propertyIsEnumerable(i))
                {
					var dom = obj[i];
                    this.options.drop['groups'].register({
                        element:dom,
                        value:i,
                        events:{
                            over:function(e)
                            {

                            }.extend(this),
                            out:function(e)
                            {

                            }.extend(this)
                        }});
					this.menu.group(dom);
                }
            }
        };
		this.menu={
			/**
			*	Menu para Grupos (points)
			*/
			group:function(dom)
			{
				var menu = new this.parent.module.app.menuRight();
		        menu.make({
		        	target:dom,
		            width:150,
		            menu:this.group.elements.concat(
					[
		   		        {separator:true},
		   		        {text:'Delete element',launch:function(){}}
		            ])
		        });
				this.options.observers['menu'].register(menu.remove,menu);
			},
			/**
			*	Menu Principal
			*/
			principal:function(dom)
			{
				
			}
		}.expand(this);
		this.group={
			elements:[
				{text:'New Input',launch:function(){}},
				{text:'New Input',launch:function(){}},
				{text:'New Input',launch:function(){}},
				{text:'New Input',launch:function(){}}
			]
		};
		this.parse_elements=function()
		{
			//alert(this.xmlform.db.length)
			for(var i=0;i<this.xmlform.db.length;i++)
			{
				var e = {
					type:'other',
					group:this.tpl_default()
				}.concat(this.xmlform.tag_attributes_to_object(this.xmlform.db[i]));
				e.group=(this.options.points.isset_key(e.group))?e.group:this.tpl_default();
				var d = this.dynaform_dom[((this.dynaform_dom.isset_key(e.type))?e.type:'other')](e);
				pd = d.dom;
				pd.setStyle({
					margin:1,
					border:'1px solid #EEE',
					font:'normal 8pt Tahoma,MiscFixed',
					padding:5
				});
				pd.onmousedown=function(evt,db_uid)
				{
					var event = window.event || evt;
					var t = this.xmlform.db[db_uid];
					var d = this.db[db_uid];
					if(event.ctrlKey)
					{
						if(this.inDragProcess===true)
						{
							return false;
						}
						if(this.phantom_static)
						{
							this.phantom_static.remove();
						}
						this.inDragProcess=true;
						var j = this.parent.dom.position(d,false,true);
						this.options.points[d.group].append(
							this.phantom_static = new DOM('div',{},{
								position:"absolute",
								width	:d.clientWidth,
								height	:d.clientHeight,
								border	:"1px solid orange",
								backgroundColor	:"orange",
								top		:j.y,
								left	:j.x
							}).opacity(40),
							this.imageAddRow = new DOM('img',{
								src:this.parent.info.images+"nr.gif"
							})
						);
			
						this.options.drop.elements = new this.parent.module.drop();
						this.options.drop.elements.make();
						this.register_elements_drop(d.group);
						this.setImageAddRow(this.key_in_group(db_uid));

						this.options.drag.phantom = new this.parent.module.drag({
							elements:this.phantom_static,
							limit:"x",
							limitbox:this.options.points[d.group]
						});
						this.options.drag.phantom.events={
							move	:this.options.drop.elements.captureFromArray.args(this.options.drag.phantom,false,true),
							finish  :this.drag_elements_onfinish.args(db_uid)
						};
						this.options.drag.phantom.make();
						this.options.drag.phantom.onInit(event,0);

						//return false;
					}
					
					d.setStyle({
						border:'1px solid orange'
					});
					try{
						if(this.xmlform.current_edit!==db_uid){
							this.db[this.xmlform.current_edit].setStyle({
								border:'1px solid #EEE'
							});
						}
					}
					catch(e){
					
					}
						this.xmlform.tag_edit(t,db_uid,this.sync_xml_node.args(db_uid));
					return false;
				}.extend(this,d.db_uid,e.group);
				pd.onmouseover=function(event,db_uid){
					var d = this.db[db_uid];
					if(this.xmlform.current_edit!==db_uid)
					{
						d.setStyle({border:'1px solid orange'});
						var event = window.event || event;
						if(event.ctrlKey)
						{
							d.setStyle({cursor:'move'});
						}
					}
				}.extend(this,d.db_uid);
				pd.onmouseout=function(event,db_uid){
					var d = this.db[db_uid];
					if(this.xmlform.current_edit!==db_uid)
					{
						d.setStyle({border:'1px solid #EEE',cursor:'default'});
					}
				}.extend(this,d.db_uid);

				//this.debug.log(e);
			}
		};
		this.sync_xml_node=function(data,db_uid)
		{
			var cd = this.xmlform.current_xml_edit.save('object');
			this.xmlform.sync_node(db_uid,cd);
			this.sync_dom(db_uid,cd);
		};
		this.sync_dom=function(db_uid,obj)
		{
			
		};
		this.register_elements_drop=function(group)
		{
			for(var i=0;i<this.db.length;i++)
			{
				if(this.db[i].group==group)
				{
					var c = this.db[i].db_uid;
					this.options.drop.elements.register({
						element	: this.db[i],
						value	: i,
						events	: {
							over:function(i)
							{
								//this.setImageAddRow(this.drop.lastSelected);
								if(this.options.drop.elements.selected!==false)
								{
									this.setImageAddRow(this.options.drop.elements.selected);
								}
							}.extend(this,c),
							out		:function(i)
							{
								/*if(this.drop.selected===false)
								{
									var uid=this.drop.arrayPositions.length-1;
									if(this.drop.position.y > this.drop.arrayPositions[uid].y2)
									{
										this.setImageAddRow(uid,true);
									}
								}*/
							}.extend(this,c)
						}
					});
				}
			}
			this.options.drop.elements.setArrayPositions(true);
		};
		this.setImageAddRow=function(drop_uid,last)
		{
			this.imageAddRow.setStyle({
				position:"absolute",
				zIndex	:100,
				top:((last)?this.options.drop.elements.arrayPositions[drop_uid].y2:this.options.drop.elements.arrayPositions[drop_uid].y1)-7,
				left:this.options.drop.elements.arrayPositions[drop_uid].x1-3
			});
		};
		this.drag_elements_onfinish=function(db_uid)
		{
			this.imageAddRow.remove();
			delete this.imageAddRow;

			var insertRowIn,begin;

			var drag = this.options.drag.phantom;
			var drop = this.options.drop.elements;

			if(drag.moved)
			{
				if(drop.selected===false)
				{
					var uid=drop.arrayPositions.length-1;
					if((drop.position.y > drop.arrayPositions[uid].y2)/* && this.lastSelected===uid*/)
					{
						insertRowIn	= uid;
						begin		= false;
					}
					else
					{
						insertRowIn	= 0;
						begin		= true;
					}
				}
				else
				{
					insertRowIn	= drop.selected;
					begin		= true;				
				}
				var t = this.db[drop.elements[insertRowIn].value];
//				t.parentNode.insertBefore(new DOM('input').replace(this.db[db_uid]));	
				var n = t.before(new DOM('div')).replace(this.db[db_uid]);
				drag.flush();
				new this.parent.module.fx.move().make({
					duration:((drag.moved)?500:0),
					//end		:{x:this.drop.arrayPositions[insertRowIn].x1,y:this.drop.arrayPositions[insertRowIn].y1},
					end		:this.parent.dom.position(n,false,true),
					dom		:this.phantom_static,
					onFinish	:function()
					{
						new this.parent.module.fx.fade().make({
							duration:500,
							end		:0,
							dom		:this.phantom_static,
							onFinish	:function(){
								this.inDragProcess=false;
								this.phantom_static.remove();
								delete this.phantom_static;
							}.extend(this)
						});
					}.extend(this)
				});


				//var newRow=this.db[insertRowIn];
				//newRow.parentNode.replaceChild(domRow,newRow);
				//alert(movedUID+":"+this.options.data.rows[movedUID].info.rowIndex+":"+domRow.rowIndex)
				//alert(domRow.rowIndex)
			}
			else
			{
				/*insertRowIn	= drag.currentElementDrag.db_uid-1;
				begin		= true;*/
			}
			
		};
		/*	Devuelve el Key actual de un objeto dentro del grupo	*/
		this.key_in_group=function(db_uid)
		{
			var a = this.db[db_uid];
			var j=0;
			for(var i=0;i<db_uid;i++)
			{
				if(this.db[i].group==a.group)
				{
					j+=1;
				}
			}
			return j;
		};
		this.dynaform_dom={
			text:function(options)
			{
				options={
					
				}.concat(options || {});
				var pd;
				this.options.points[options.group].append(
					pd = new DOM('div',{innerHTML:options.nodeName,db_uid:this.db.length,group:options.group})
				);
				this.db.push(pd);
				return {
					dom:pd,
					db_uid:pd.db_uid
				};
			},
			other:function(options)
			{
				options={
					
				}.concat(options || {});
				var pd;
				this.options.points[options.group].append(
					pd = new DOM('div',{innerHTML:options.nodeName,group:options.group,db_uid:this.db.length})
				);
				this.db.push(pd);
				return {
					dom:pd,
					db_uid:pd.db_uid
				};
			},
			title:function(options)
			{
				options={
					
				}.concat(options || {});
				var pd;
				this.options.points[options.group].append(
					pd = new DOM('div',{innerHTML:options.nodeName,db_uid:this.db.length,group:options.group})
				);
				this.db.push(pd);
				return {
					dom:pd,
					db_uid:pd.db_uid
				};
			}

		}.expand(this)
        this.expand(this);
        return this;
    }
});
