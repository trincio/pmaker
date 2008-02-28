
function save( panel, txt ) {
  commonDialog ( '', 'saved' , 'saved', {}, '' )  ;
  setTimeout ( leimnud.closure({instance:myDialog,method:function(panel){
  	
  	myDialog.remove();
//  	panel.tabLastSelected=false;
//  	panel.tabSelected=1;
//  	panel.makeTab();  
  },args:panel}) , 1000 );


//  panel.clearContent();
//  panel.addContent ( txt );  
  return false;
}

function testEmail () {
	panelTest=new leimnud.module.panel();
	panelTest.options={
		size:{w:450,h:150},
		position:{x:200,y:100,center:true},
		title:"",
		theme:"panel",
		control:{
			close:true, drag:false
			}
		};
	panelTest.make();
	var r = new leimnud.module.rpc.xmlhttp({url:"mailTest.html"});
	r.callback = leimnud.closure({Function:function(rpc){
		panelTest.addContent(rpc.xmlhttp.responseText);
	},args:r})
	r.make();

	
}

function sendEmail () {
	var testEmail = document.getElementById('form[TEST_EMAIL]');
	
	alert ( 'sending email' + testEmail.value );
}	
  	