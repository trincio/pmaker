<?php
/*------------------------------------------------------------------------------------------------
| dynaformhandler.class.php
| By Erik Amaru Ortiz
| CopyLeft (f) 2008
| Email: aortiz.erik@gmai.com
+--------------------------------------------------
| Email bugs/suggestions to aortiz.erik@gmai.com
+--------------------------------------------------
| This script has been created and released under
| the GNU GPL and is free to use and redistribute
| only if this copyright statement is not removed
| You can see www.gnu.org the GPL lisence reference
+-------------------------------------------------------------------------------------------------*/

/**
* @Author Erik Amaru Ortiz
* @CreationDate 2008-11-20
* @Description This class is a Dynaform handler for modify directly into file
*/

class dynaFormHandler{

	private $xmlfile;
	private $dom;
	private $root;

	function __construct($file=null){
		if( !isset($file) ) throw new Exception('Class::dynaFormHandler::ERROR-> xml file was not especified!!');

		$this->xmlfile = $file;
		$this->dom = new DOMDocument();
		$this->dom->preserveWhiteSpace = false;
		$this->dom->formatOutput = true;

		if( isset($file) && is_file($file) ){
			$this->dom->load($this->xmlfile);
			$this->root = $this->dom->firstChild;
		} else {
			throw new Exception('Class::dynaFormHandler::ERROR-> xml file doesn\'t exits!!');
		}
	}

	function __cloneEmpty(){
		//$cloneObj = clone $this;
		//$cloneObj->xmlfile = '__Clone__' . $cloneObj->xmlfile;
		$xPath = new DOMXPath($this->dom);
		$nodeList = $xPath->query('/dynaForm/*');

		foreach ($nodeList as $domElement){
			//echo $domElement->nodeName.'<br>';
			$elements[] = $domElement->nodeName;
		}
		$this->remove($elements);

		//return $cloneObj;
	}

	function toString($op=''){
		switch($op){
			case 'html': return htmlentities(file_get_contents($this->xmlfile));
			default: return file_get_contents($this->xmlfile);
		}
	}

	function getNode($nodename){
		return $this->root->getElementsByTagName($nodename)->item(0);
	}

	function setNode($node){
		$newnode = $this->root->appendChild($node);
		$this->save();
		return $newnode;
	}



 	/*
 		$child_childs = Array(
			Array(name=>'option', value=>'uno', 'attributes'=>Array('name'=>1)),
			Array(name=>'option', value=>'dos', 'attributes'=>Array('name'=>2)),
			Array(name=>'option', value=>'tres', 'attributes'=>Array('name'=>3)),
 		)
 	*/
 	/**
 	*	Add Function
 	*  attributes (String node-name, Array attributes(atribute-name =>attribute-value, ..., ...), Array childs(child-name=>child-content), Array Child-childs())
 	*/
	function add($name, $attributes, $childs, $childs_childs=null){
		$newnode = $this->root->appendChild($this->dom->createElement($name));
		foreach($attributes as $att_name => $att_value) {
			$newnode->setAttribute($att_name, $att_value);
		}
		if(is_array($childs)){
			foreach($childs as $child_name => $child_text) {
				$newnode_child = $newnode->appendChild($this->dom->createElement($child_name));
				$newnode_child->appendChild($this->dom->createTextNode($child_text));

				if($childs_childs != null and is_array($childs_childs)){
					foreach($childs_childs as $cc) {
						$ccmode = $newnode_child->appendChild($this->dom->createElement($cc['name']));
						$ccmode->appendChild($this->dom->createTextNode($cc['value']));
						foreach($cc['attributes'] as $cc_att_name => $cc_att_value) {
							$ccmode->setAttribute($cc_att_name, $cc_att_value);
						}
					}
				}
			}
		} else {
			$text_node = $childs;
			$newnode->appendChild($this->dom->createTextNode($text_node));
		}
		$this->save();
	}

	function replace($replaced, $name, $attributes, $childs, $childs_childs=null){
		$element = $this->root->getElementsByTagName($replaced)->item(0);

		$this->root->replaceChild($this->dom->createElement($name), $element);
		$newnode = $element = $this->root->getElementsByTagName($name)->item(0);
		foreach($attributes as $att_name => $att_value) {
			$newnode->setAttribute($att_name, $att_value);
		}

		if(is_array($childs)){
			foreach($childs as $child_name => $child_text) {
				$newnode_child = $newnode->appendChild($this->dom->createElement($child_name));
				$newnode_child->appendChild($this->dom->createTextNode($child_text));

				if($childs_childs != null and is_array($childs_childs)){
					foreach($childs_childs as $cc) {
						$ccmode = $newnode_child->appendChild($this->dom->createElement($cc['name']));
						$ccmode->appendChild($this->dom->createTextNode($cc['value']));
						foreach($cc['attributes'] as $cc_att_name => $cc_att_value) {
							$ccmode->setAttribute($cc_att_name, $cc_att_value);
						}
					}
				}
			}
		} else {
			$text_node = $childs;
			$newnode->appendChild($this->dom->createTextNode($text_node));
		}
		$this->save();
	}

	function save($fname=null){
		if( !isset($fname) ){
			$this->dom->save($this->xmlfile);
		} else {
			$this->xmlfile = $fname;
			$this->dom->save($this->xmlfile);
		}
		//$this->fixXmlFile();
	}

	function fixXmlFile(){
		$newxml = '';
		$content = file($this->xmlfile);
		foreach($content as $line){
			if( trim($line) != ''){
				$newxml .= $line;
			}
		}
		file_put_contents($this->xmlfile, $newxml);
	}

	function setHeaderAttribute($att_name, $att_value){
		 $this->root->setAttribute($att_name, $att_value);
		 $this->save();
	}

	function modifyHeaderAttribute($att_name, $att_new_value){
		 $this->root->removeAttribute($att_name);
		 $this->root->setAttribute($att_name, $att_new_value);
		 $this->save();
	}

	function updateAttribute($node_name, $att_name, $att_new_value){

		$xpath = new DOMXPath($this->dom);
		$nodeList = $xpath->query("/dynaForm/$node_name");
		$node = $nodeList->item(0);
		$node->removeAttribute($att_name);
		$node->setAttribute($att_name, $att_new_value);
		$this->save();
	}

	function remove($v){
		if(!is_array($v)){
			$av[0] = $v;
		} else{
			$av = $v;
		}

		foreach($av as $e){
			$xnode = $this->root->getElementsByTagName($e)->item(0);
			if ( $xnode->nodeType == XML_ELEMENT_NODE ) {
        		$dropednode = $this->root->removeChild($xnode);

        		/*evaluation field aditional routines*/
        		$xpath = new DOMXPath($this->dom);
				$nodeList = $xpath->query("/dynaForm/JS_$e");
				if($nodeList->length != 0){
					$tmp_node = $nodeList->item(0);
					$this->root->removeChild($tmp_node);
				}
 	    	} else {
				print("Class::dynaFormHandler::ERROR-> The \"$e\" element doesn't exist!<br>");
 	    	}
		}
		$this->save();
	}

	//new features from december 2008

	function moveUp($selected_node){
		/*DOMNode DOMNode::insertBefore  ( DOMNode $newnode  [, DOMNode $refnode  ] )
		This function inserts a new node right before the reference node. If you plan
		to do further modifications on the appended child you must use the returned node. */

		$xpath = new DOMXPath($this->dom);
		$nodeList = $xpath->query("/dynaForm/*");

		$flag = false;
		for($i = 0; $i < $nodeList->length; $i++) {
			$xnode = $nodeList->item($i);

			if($selected_node == $xnode->nodeName){
				//if is a first node move it to final with a circular logic
				if( $flag === false ){
					$removed_node = $this->root->removeChild($xnode);
					$this->root->appendChild($removed_node);
					break;
				} else {
					$removed_node = $this->root->removeChild($xnode);
					$predecessor_node = $nodeList->item($i-1);
					$this->root->insertBefore($removed_node, $predecessor_node);
					break;
				}
			}
			$flag = true;
		}
		$this->save();
	}

	function moveDown($selected_node){
		/*DOMNode DOMNode::insertBefore  ( DOMNode $newnode  [, DOMNode $refnode  ] )
		This function inserts a new node right before the reference node. If you plan
		to do further modifications on the appended child you must use the returned node. */

		$xpath = new DOMXPath($this->dom);
		$nodeList = $xpath->query("/dynaForm/*");

		$real_length = $nodeList->length;

		for($i = 0; $i < $nodeList->length; $i++) {
			$xnode = $nodeList->item($i);

			if($selected_node == $xnode->nodeName){
				//if is a last node move it to final with a circular logic
				if( ($i+1) == $real_length){
					if($real_length != 1){
						$first_node = $nodeList->item(0);
						$removed_node = $this->root->removeChild($xnode);
						$this->root->insertBefore($removed_node, $first_node);
					}
					break;
				} else {
					if( ($i+3) <= $real_length ){
						$removed_node = $this->root->removeChild($xnode);
						$predecessor_node = $nodeList->item($i+2);
						$this->root->insertBefore($removed_node, $predecessor_node);
						break;
					} else {
						$removed_node = $this->root->removeChild($xnode);
						$this->root->appendChild($removed_node);
						break;
					}
				}
			}

		}
		$this->save();
	}
}


//examples...........
//$o = new dynaFormHandler('usersList.xml');
/* for($i=1; $i<=5; $i++){
 	$o->add('lastnamex'.$i, Array('type'=>'text', 'defaultvalue'=>'Ortiz'), Array('es'=>'Apellido'));
}*/

/*
$child_childs = Array(
	Array('name'=>'option', 'value'=>'uno', 'attributes'=>Array('name'=>111)),
	Array('name'=>'option', 'value'=>'tres', 'attributes'=>Array('name'=>333)),
);
$o->replace('antiguedad', 'antiguedad_replaced', Array('type'=>'dropdown', 'required'=>'0'), Array('es'=>'Antiguedad !!'), $child_childs);
*/
//$o->remove('usr_email');

//$o->replace('usr_uid', 'usr_uid222', Array('type'=>'text', 'defaultvalue'=>'111'), Array('es'=>'fucking id'));
