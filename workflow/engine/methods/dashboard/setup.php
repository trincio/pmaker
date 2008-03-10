<?php
/**
 * setup.php
 *  
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd., 
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 * 
 */
if (($RBAC_Response=$RBAC->userCanAccess("PM_SETUP"))!=1) return $RBAC_Response;
//  G::GenericForceLogin( 'WF_PROCESS'  ,'login-login', 'login/noViewPage' );
//  G::GenericForceLogin( 'WF_ARCHITECT','login-login',  "process/noAccess" );
  
  $G_MAIN_MENU = "processmaker";
  $G_SUB_MENU  = "setup";
  
  $G_ID_MENU_SELECTED     = "SETUP";
  $G_ID_SUB_MENU_SELECTED = "ENVIRONMENT";
  
  
  $dbc = new DBConnection;
  $G_PUBLISH = new Publisher;
  $G_PUBLISH->SetTo( $dbc );
  $G_PUBLISH->AddContent( "view", "setup/tree_setupEnvironment" );
//  $G_PUBLISH->AddContent( "xmlform", "paged-table2", "setup/Holiday","", "" , "../gulliver/paged-TableAjax.php" );
  
  G::RenderPage( 'publish-treeview' );
// ? >
/*
<script type="text/javascript">
	  var leimnud = new maborak();
	  leimnud.make();
	  leimnud.Package.Load("rpc,drag,drop,panel,app",{Instance:leimnud,Type:"module"});
	  leimnud.exec(leimnud.fix.memoryLeak);
	  leimnud.exec(leimnud.fix.mouse);
</script>
	  
<script type="text/javascript">
var setupClass=function(){};

setupClass.prototype={
	parent:leimnud,
	info:{
		name		: "setup",
		images_dir	: "../../processmap/core/images/"
	},
	panels:{},dragables:{},dropables:{},
	make:function()
	{
		this.options = this.options || {};
		this.observers = {
			menu 		: this.parent.factory(this.parent.pattern.observer)
		};

		leimnud.dom.loadJs("setup.js");
		leimnud.dom.loadJs("/js/form/core/pagedTable.js");
		
		// Panel control 
		this.panels.control=new leimnud.module.panel();
		this.panels.control.options={
		  size:{w:this.options.size.w, h:this.options.size.h},
			position:{x:this.options.position.x, y:this.options.position.y},
			title:"Setup",
			theme:"panel",
				target:this.options.target
			};
			this.panels.control.setStyle={
			  shadow:{backgroundColor:"black"}
			};
			this.panels.control.styles.fx.opacityShadow.Static=100;
			this.panels.control.styles.fx.opacityShadow.Move=5;
			this.panels.control.tab={
			  width	:140,
				optWidth:130,
				step	:0, 
				options:[
					{
					title	:"<u>H</u>olidays",
					content	:this.parent.closure({instance:this,method:function(){
						var panel = this.panels.control;
						panel.command(panel.loader.show);
						var r = new leimnud.module.rpc.xmlhttp({url:"holiday.html",method:"post"});
						r.callback=leimnud.closure({instance:this,method:function(rpc){
							panel.command(panel.loader.hide);
							this.panels.control.addContent(rpc.xmlhttp.responseText);

							},args:r})
						r.make();
						}})  ,
						selected:true
					},
					{
						title	:"<u>W</u>ork Schedule",
						content	:this.parent.closure({instance:this,method:function(){
						leimnud.dom.loadJs("workPeriod.js");
						var panel = this.panels.control;
						panel.command(panel.loader.show);
						var r = new leimnud.module.rpc.xmlhttp({url:"workPeriod.html",method:"post"});
						r.callback=leimnud.closure({instance:this,method:function(rpc){
							panel.command(panel.loader.hide);
							this.panels.control.addContent(rpc.xmlhttp.responseText);

						sub = new leimnud.module.app.submit({
							form	: document.forms["cHNpazRhQ2lxNcKwaTM0SFM1NTZvMWzCsGxvTTg___"]
						});
						sub.callback = leimnud.closure ( { Function:function( panel ){
							abc(panel, sub.rpc.xmlhttp.responseText);
							 
							
						} , args:this.panels.control} );

							},args:r})
						r.make();
						}}),
						selected:false
					},
					{
						title	:"<u>L</u>ocation",
						content	:this.parent.closure({instance:this,method:function(){
						var panel = this.panels.control;
						panel.command(panel.loader.show);
						var r = new leimnud.module.rpc.xmlhttp({url:"location.html",method:"post"});
						r.callback=leimnud.closure({instance:this,method:function(rpc){
							panel.command(panel.loader.hide);
							this.panels.control.addContent(rpc.xmlhttp.responseText);
							},args:r})
						r.make();
						}}),
						selected:false
					},
					{
						title	:"<u>L</u>anguages",
						content	:this.parent.closure({instance:this,method:function(){
						var panel = this.panels.control;
						panel.command(panel.loader.show);
						var r = new leimnud.module.rpc.xmlhttp({url:"language.html",method:"post"});
						r.callback=leimnud.closure({instance:this,method:function(rpc){
							panel.command(panel.loader.hide);
							this.panels.control.addContent(rpc.xmlhttp.responseText);
							},args:r})
						r.make();
						}}),
						selected:false
					},
					{
						title	:"<u>A</u>ppearance",
						content	:this.parent.closure({instance:this,method:function(){
						var panel = this.panels.control;
						panel.command(panel.loader.show);
						var r = new leimnud.module.rpc.xmlhttp({url:"appearance.html",method:"post"});
						r.callback=leimnud.closure({instance:this,method:function(rpc){
							panel.command(panel.loader.hide);
							this.panels.control.addContent(rpc.xmlhttp.responseText);
							},args:r})
						r.make();
						}}),
						selected:false
					},
					{
						title	:"<u>E</u>mail Server",
						content	:this.parent.closure({instance:this,method:function(){
						var panel = this.panels.control;
						panel.command(panel.loader.show);
						var r = new leimnud.module.rpc.xmlhttp({url:"mail",method:"post"});
						r.callback=leimnud.closure({instance:this,method:function(rpc){
							panel.command(panel.loader.hide);
							this.panels.control.addContent(rpc.xmlhttp.responseText);
							},args:r})
						r.make();
						}}),
						selected:false
					},
					{
						title	:"<u>W</u>elcome Page",
						content	:this.parent.closure({instance:this,method:function(){
						var panel = this.panels.control;
						panel.command(panel.loader.show);
						var r = new leimnud.module.rpc.xmlhttp({url:"customPage.html",method:"post"});
						r.callback=leimnud.closure({instance:this,method:function(rpc){
							panel.command(panel.loader.hide);
							this.panels.control.addContent(rpc.xmlhttp.responseText);
							},args:r})
						r.make();
						}}),
						selected:false
					},
					{
						title	:"<u>D</u>B Connections",
						content	:this.parent.closure({instance:this,method:function(){
						var panel = this.panels.control;
						panel.command(panel.loader.show);
						var r = new leimnud.module.rpc.xmlhttp({url:"connectionDB.html",method:"post"});
						r.callback=leimnud.closure({instance:this,method:function(rpc){
							panel.command(panel.loader.hide);
							this.panels.control.addContent(rpc.xmlhttp.responseText);
							},args:r})
						r.make();
						}}),
						selected:false
					},
					{
						title	:"<u>W</u>eb Services",
						content	:this.parent.closure({instance:this,method:function(){
						var panel = this.panels.control;
						panel.command(panel.loader.show);
						var r = new leimnud.module.rpc.xmlhttp({url:"connectionWS.html",method:"post"});
						r.callback=leimnud.closure({instance:this,method:function(rpc){
							panel.command(panel.loader.hide);
							this.panels.control.addContent(rpc.xmlhttp.responseText);
							},args:r})
						r.make();
						}}),
						selected:false
					},
					{
						title	:"<u>C</u>ustom Functions",
						content	:this.parent.closure({instance:this,method:function(){
						var panel = this.panels.control;
						panel.command(panel.loader.show);
						var r = new leimnud.module.rpc.xmlhttp({url:"customFunctions.html",method:"post"});
						r.callback=leimnud.closure({instance:this,method:function(rpc){
							panel.command(panel.loader.hide);
							this.panels.control.addContent(rpc.xmlhttp.responseText);
							},args:r})
						r.make();
						}}),
						selected:false
					},
					{
						title	:"<u>M</u>iscelaneous",
						content	:this.parent.closure({instance:this,method:function(){
						var panel = this.panels.control;
						panel.command(panel.loader.show);
						var r = new leimnud.module.rpc.xmlhttp({url:"debug.html",method:"post"});
						r.callback=leimnud.closure({instance:this,method:function(rpc){
							panel.command(panel.loader.hide);
							this.panels.control.addContent(rpc.xmlhttp.responseText);
							},args:r})
						r.make();
						}}),
						selected:false
					},
					{
						title	:"<u>T</u>emplates",
						content	:this.parent.closure({instance:this,method:function(){
						var panel = this.panels.control;
						panel.command(panel.loader.show);
						var r = new leimnud.module.rpc.xmlhttp({url:"connectionWS.html",method:"post"});
						r.callback=leimnud.closure({instance:this,method:function(rpc){
							panel.command(panel.loader.hide);
								this.panels.control.addContent(rpc.xmlhttp.responseText);
							},args:r})
						r.make();
						}}),
						selected:false
					}
					]
			};
				this.panels.control.make();
	}
};			
	</script>

<script language="JavaScript">
	var pb=leimnud.dom.capture("tag.body 0");
	setupPanel=new setupClass();
	//heightPanel = ( pb.clientHeight-90 < 16*30 ? 16*30 : pb.clientHeight-90 ); 
	heightPanel = 16*30; 
	setupPanel.options={
		target		:pb,
		dataServer	:"pm.xml",
		lang		:"en",
		size		:{w:pb.clientWidth-50,h:heightPanel},
		position	:{x:15,y:85}
	}
	setupPanel.make();

</script>
*/
