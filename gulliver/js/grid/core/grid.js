var G_Grid = function(oForm, sGridName) {
	// G_Field integration - Start
	var oGrid = this;
	this.parent = G_Field;
	this.parent(oForm, '', sGridName);
	// G_Field integration - End
	this.sGridName = sGridName;
	this.sAJAXPage = oForm.ajaxServer || '';
	this.oGrid = document.getElementById(this.sGridName);
	this.aFields = [];
	this.aElements = [];
	this.aFunctions = [];
	this.aFormulas = [];
	this.setFields = function(aFields, iRow) {
		this.aFields = aFields;
		var i, j, k, aAux, oAux, sDependentFields;
		for (i = 0; i < this.aFields.length; i++) {
			j = iRow || 1;
			switch (this.aFields[i].sType) {
			case 'text':
				while (oAux = document.getElementById('form[' + this.sGridName + '][' + j + '][' + this.aFields[i].sFieldName + ']')) {
					this.aElements.push(new G_Text(oForm, document.getElementById('form[' + this.sGridName + '][' + j + '][' + this.aFields[i].sFieldName + ']'), this.sGridName + '][' + j + '][' + this.aFields[i].sFieldName));
					this.aElements[this.aElements.length - 1].validate = this.aFields[i].oProperties.validate;
					if (aFields[i].oProperties) {
						this.aElements[this.aElements.length - 1].mask = aFields[i].oProperties.mask;
					}
					j++;
				}
				break;
			case 'currency':
				while (oAux = document.getElementById('form[' + this.sGridName + '][' + j + '][' + this.aFields[i].sFieldName + ']')) {
					this.aElements.push(new G_Currency(oForm, document.getElementById('form[' + this.sGridName + '][' + j + '][' + this.aFields[i].sFieldName + ']'), this.sGridName + '][' + j + '][' + this.aFields[i].sFieldName));
					if (aFields[i].oProperties) {
						this.aElements[this.aElements.length - 1].mask = aFields[i].oProperties.mask;
					}
					j++;
				}
				break;
			case 'percentage':
				while (oAux = document.getElementById('form[' + this.sGridName + '][' + j + '][' + this.aFields[i].sFieldName + ']')) {
					this.aElements.push(new G_Percentage(oForm, document.getElementById('form[' + this.sGridName + '][' + j + '][' + this.aFields[i].sFieldName + ']'), this.sGridName + '][' + j + '][' + this.aFields[i].sFieldName));
					if (aFields[i].oProperties) {
						this.aElements[this.aElements.length - 1].mask = aFields[i].oProperties.mask;
					}
					j++;
				}
				break;
			case 'dropdown':
				while (oAux = document.getElementById('form[' + this.sGridName + '][' + j + '][' + this.aFields[i].sFieldName + ']')) {
					this.aElements.push(new G_DropDown(oForm, document.getElementById('form[' + this.sGridName + '][' + j + '][' + this.aFields[i].sFieldName + ']'), this.sGridName + '][' + j + '][' + this.aFields[i].sFieldName));
					if (aFields[i].oProperties) {
						this.aElements[this.aElements.length - 1].mask = aFields[i].oProperties.sMask;
					}
					j++;
				}
				break;
			}
		}
		// Set dependent fields
		for (i = 0; i < this.aFields.length; i++) {
			j = iRow || 1;
			while (oAux = document.getElementById('form[' + this.sGridName + '][' + j + '][' + this.aFields[i].sFieldName + ']')) {
				if (aFields[i].oProperties.dependentFields != '') {
					this.setDependents(j, this.getElementByName(j, this.aFields[i].sFieldName), aFields[i].oProperties.dependentFields);
				}
				j++;
			}
		}
	};
	this.setDependents = function(iRow, me, theDependentFields) {
		var i;
		var dependentFields = theDependentFields || '';
		dependentFields = dependentFields.split(',');
		for (i = 0; i < dependentFields.length; i++) {
			var oField = this.getElementByName(iRow, dependentFields[i]);
			if (oField) {
				me.dependentFields[i] = oField;
				me.dependentFields[i].addDependencie(me);
			}
		}
	};
	this.unsetFields = function() {
		var i, j = 0, k, l = 0;
		k = this.aElements.length / this.aFields.length;
		for (i = 0; i < this.aFields.length; i++) {
			j += k;
			l++;
			this.aElements.splice(j - l, 1);
		}
		/*
		 * for (i = 0; i < this.aElements.length ;i++) {
		 * alert(this.aElements[i].name); }
		 */
	};
	this.getElementByName = function(iRow, sName) {
		var i;
		for (i = 0; i < this.aElements.length; i++) {
			if (this.aElements[i].name === this.sGridName + '][' + iRow + '][' + sName) {
				return this.aElements[i];
			}
		}
		return null;
	};
	this.getElementValueByName = function(iRow, sName) {
		var oAux = document.getElementById('form[' + this.sGridName + '][' + iRow + '][' + sName + ']');
		if (oAux) {
			return oAux.value;
		} else {
			return 'Object not found!';
		}
	};
	this.getFunctionResult = function(sName) {
		var oAux = document.getElementById('form[SYS_GRID_AGGREGATE_' + this.sGridName + '_' + sName + ']');
		if (oAux) {
			return oAux.value;
		} else {
			return 'Object not found!';
		}
	};
	this.addGridRow = function() {
		var i, aObjects;
		var oRow = document.getElementById('firstRow_' + this.sGridName);
		var aCells = oRow.getElementsByTagName('td');
		var oNewRow = this.oGrid.insertRow(this.oGrid.rows.length - 1);
		
		for (i = 0; i < aCells.length; i++) {
			oNewRow.appendChild(aCells[i].cloneNode(true));
			
			if (i == 0) {
				oNewRow.getElementsByTagName('td')[i].innerHTML = this.oGrid.rows.length - 2;
			} else {
				if (i == (aCells.length - 1)) {
					oNewRow.getElementsByTagName('td')[i].innerHTML = oNewRow.getElementsByTagName('td')[i].innerHTML.replace(/\[1\]/g, '\[' + (this.oGrid.rows.length - 2) + '\]');
				} else {
					aObjects = oNewRow.getElementsByTagName('td')[i].getElementsByTagName('a');
					if (aObjects) {
						if (aObjects[0]) {
							if (aObjects[0].onclick) {
								sAux = new String(aObjects[0].onclick);
								eval('aObjects[0].onclick = ' + sAux.replace(/\[1\]/g, '\[' + (this.oGrid.rows.length - 2) + '\]') + ';');
							}
						}
					}
					
					/**
					 * Fixed, solve for case in 'a' elements
					 * 
					 * @By Neyek <erik@colosa.com, aortiz.erik@gmail.com>
					 * @Date Sep 3th, 2009 
					 */
					enodename = aCells[i].innerHTML.substring(aCells[i].innerHTML.indexOf('<')+1, aCells[i].innerHTML.indexOf(' '));
					
					switch (enodename) {
					case 'input':
						aObjects = oNewRow.getElementsByTagName('td')[i].getElementsByTagName('input');
						if (aObjects) {
							aObjects[0].name = aObjects[0].name.replace(/\[1\]/g, '\[' + (this.oGrid.rows.length - 2) + '\]');
							aObjects[0].id = aObjects[0].id.replace(/\[1\]/g, '\[' + (this.oGrid.rows.length - 2) + '\]');
							
							/** verify if is date element, then it has a two <a> elements, so we need to change its onclick references */
							aElement = oNewRow.getElementsByTagName('td')[i].getElementsByTagName('a');
							if(aElement.length > 0){
								sAux = new String(aElement[0].onclick);
								eval('aElement[0].onclick = ' + sAux.replace(/\[1\]/g, '\[' + (this.oGrid.rows.length - 2) + '\]') + ';');
								sAux = new String(aElement[1].onclick);
								eval('aElement[1].onclick = ' + sAux.replace(/\[1\]/g, '\[' + (this.oGrid.rows.length - 2) + '\]') + ';');
							}
							
							if (aObjects[0].type != 'checkbox') {
								aObjects[0].value = '';
							} else {
								aObjects[0].checked = false;
							}
							if (aObjects[1]) {
								if (aObjects[1].onclick) {
									sAux = new String(aObjects[1].onclick);
									eval('aObjects[1].onclick = ' + sAux.replace(/\[1\]/g, '\[' + (this.oGrid.rows.length - 2) + '\]') + ';');
								}
							}
						}
						aObjects = oNewRow.getElementsByTagName('td')[i].getElementsByTagName('span');
						if (aObjects) {
							if (aObjects[0]) {
								// aObjects[0].name =
								// aObjects[0].name.replace(/\[1\]/g, '\[' +
								// (this.oGrid.rows.length - 2) + '\]');
								aObjects[0].id = aObjects[0].id.replace(/\[1\]/g, '\[' + (this.oGrid.rows.length - 2) + '\]');
							}
							aObjects = oNewRow.getElementsByTagName('td')[i].getElementsByTagName('a');
							if (aObjects) {
								if (aObjects[0]) {
									if (aObjects[0].onclick) {
										sAux = new String(aObjects[0].onclick);
										eval('aObjects[0].onclick = ' + sAux.replace(/\[1\]/g, '\[' + (this.oGrid.rows.length - 2) + '\]') + ';');
									}
								}
							}
						}
						break;
					case 'select':
						aObjects = oNewRow.getElementsByTagName('td')[i].getElementsByTagName('select');
						if (aObjects) {
							var oAux = document.createElement(aObjects[0].tagName);
							oAux.name = aObjects[0].name.replace(/\[1\]/g, '\[' + (this.oGrid.rows.length - 2) + '\]');
							oAux.id = aObjects[0].id.replace(/\[1\]/g, '\[' + (this.oGrid.rows.length - 2) + '\]');
							for ( var j = 0; j < aObjects[0].options.length; j++) {
								var oOption = document.createElement('OPTION');
								oOption.value = aObjects[0].options[j].value;
								oOption.text = aObjects[0].options[j].text;
								oAux.options.add(oOption);
							}
							aObjects[0].parentNode.replaceChild(oAux, aObjects[0]);
							/*
							 * aObjects[0].name =
							 * aObjects[0].name.replace(/\[1\]/g, '\[' +
							 * (this.oGrid.rows.length - 2) + '\]');
							 * aObjects[0].id = aObjects[0].id.replace(/\[1\]/g,
							 * '\[' + (this.oGrid.rows.length - 2) + '\]');
							 * aObjects[0].selectedIndex = 0;
							 */
						}
						break;
					case 'textarea':
						aObjects = oNewRow.getElementsByTagName('td')[i].getElementsByTagName('textarea');
						if (aObjects) {
							aObjects[0].name = aObjects[0].name.replace(/\[1\]/g, '\[' + (this.oGrid.rows.length - 2) + '\]');
							aObjects[0].id = aObjects[0].id.replace(/\[1\]/g, '\[' + (this.oGrid.rows.length - 2) + '\]');
							aObjects[0].value = '';
						}
						break;
						
					case 'a':
						aObjects = oNewRow.getElementsByTagName('td')[i].getElementsByTagName('a');
						if (aObjects) {
							aObjects[0].name = aObjects[0].name.replace(/\[1\]/g, '\[' + (this.oGrid.rows.length - 2) + '\]');
							aObjects[0].id = aObjects[0].id.replace(/\[1\]/g, '\[' + (this.oGrid.rows.length - 2) + '\]');
							aObjects[0].value = '';
						}
						break;
						
					default:
						oNewRow.getElementsByTagName('td')[i].innerHTML = '&nbsp;';
						break;
					}
					aObjects = null;
				}
			}
		}
		if (this.aFields.length > 0) {
			this.setFields(this.aFields, this.oGrid.rows.length - 2);
		}
		if (this.aFunctions.length > 0) {
			this.assignFunctions(this.aFunctions, 'change', this.oGrid.rows.length - 2);
		}
		if (this.aFormulas.length > 0) {
			this.assignFormulas(this.aFormulas, 'change', this.oGrid.rows.length - 2);
		}
		if (this.onaddrow) {
			this.onaddrow(this.oGrid.rows.length - 2);
		}
	};
	this.deleteGridRow = function(sRow) {
		var i, iRow, iRowAux, oAux;
		if (this.oGrid.rows.length == 3) {
			new leimnud.module.app.alert().make( {
				label : G_STRINGS.ID_MSG_NODELETE_GRID_ITEM
			});
			return false;
		}
		new leimnud.module.app.confirm().make( {
			label : G_STRINGS.ID_MSG_DELETE_GRID_ITEM,
			action : function() {

				sRow = sRow.replace('[', '');
				sRow = sRow.replace(']', '');
				iRow = Number(sRow);

				/*
				 * delete the respective session row grid variables from
				 * Dynaform - by Nyeke <erik@colosa.com
				 */
				deleteRowOnDybaform(this, iRow);

				iRowAux = iRow + 1;
				while (iRowAux <= (this.oGrid.rows.length - 2)) {
					for (i = 1; i < this.oGrid.rows[iRowAux - 1].cells.length; i++) {
						var oCell1 = this.oGrid.rows[iRowAux - 1].cells[i];
						var oCell2 = this.oGrid.rows[iRowAux].cells[i];
						switch (oCell1.innerHTML.replace(/^\s+|\s+$/g, '').substr(0, 6).toLowerCase()) {
						case '<input':
							aObjects1 = oCell1.getElementsByTagName('input');
							aObjects2 = oCell2.getElementsByTagName('input');
							if (aObjects1 && aObjects2) {
								aObjects1[0].value = aObjects2[0].value;
							}
							break;
						case '<selec':
							aObjects1 = oCell1.getElementsByTagName('select');
							aObjects2 = oCell2.getElementsByTagName('select');
							if (aObjects1 && aObjects2) {
								var vValue = aObjects2[0].value;
								/*
								 * for (var j = (aObjects1[0].options.length-1);
								 * j >= 0; j--) { aObjects1[0].options[j] =
								 * null; }
								 */
								aObjects1[0].options.length = 0;
								for ( var j = 0; j < aObjects2[0].options.length; j++) {
									var optn = $dce("OPTION");
									optn.text = aObjects2[0].options[j].text;
									optn.value = aObjects2[0].options[j].value;
									aObjects1[0].options[j] = optn;
								}
								aObjects1[0].value = vValue;
							}
							break;
						case '<texta':
							aObjects1 = oCell1.getElementsByTagName('textarea');
							aObjects2 = oCell2.getElementsByTagName('textarea');
							if (aObjects1 && aObjects2) {
								aObjects1[0].value = aObjects2[0].value;
							}
							break;
						default:
							if (oCell2.innerHTML.toLowerCase().indexOf('deletegridrow') == -1) {
								oCell1.innerHTML = oCell2.innerHTML;
							}
							break;
						}
					}
					iRowAux++;
				}
				this.oGrid.deleteRow(this.oGrid.rows.length - 2);
				if (this.sAJAXPage != '') {
				}
				if (this.aFields.length > 0) {
					this.unsetFields();
				}
				if (this.aFunctions.length > 0) {
					for (i = 0; i < this.aFunctions.length; i++) {
						oAux = document.getElementById('form[' + this.sGridName + '][1][' + this.aFunctions[i].sFieldName + ']');
						if (oAux) {
							switch (this.aFunctions[i].sFunction) {
							case 'sum':
								this.sum(false, oAux);
								break;
							case 'avg':
								this.avg(false, oAux);
								break;
							}
						}
					}
				}
				if (this.ondeleterow) {
					this.ondeleterow();
				}
			}.extend(this)
		});
	};
	this.assignFunctions = function(aFields, sEvent, iRow) {
		iRow = iRow || 1;
		var i, j, oAux;
		for (i = 0; i < aFields.length; i++) {
			j = iRow;
			while (oAux = document.getElementById('form[' + this.sGridName + '][' + j + '][' + aFields[i].sFieldName + ']')) {
				switch (aFields[i].sFunction) {
				case 'sum':
					leimnud.event.add(oAux, sEvent, {
						method : this.sum,
						instance : this,
						event : true
					});
					break;
				case 'avg':
					leimnud.event.add(oAux, sEvent, {
						method : this.avg,
						instance : this,
						event : true
					});
					break;
				default:
					leimnud.event.add(oAux, sEvent, {
						method : aFields[i].sFunction,
						instance : this,
						event : true
					});
					break;
				}
				j++;
			}
		}
	};
	this.setFunctions = function(aFunctions) {
		this.aFunctions = aFunctions;
		this.assignFunctions(this.aFunctions, 'change');
	};
	this.sum = function(oEvent, oDOM) {
		oDOM = (oDOM ? oDOM : oEvent.target || window.event.srcElement);
		var i, aAux, oAux, fTotal, sMask;
		aAux = oDOM.name.split('][');
		i = 1;
		fTotal = 0;
		aAux[2] = aAux[2].replace(']', '');
		while (oAux = this.getElementByName(i, aAux[2])) {
			fTotal += parseFloat(G.cleanMask(oAux.value() || 0, oAux.mask).result.replace(/,/g, ''));
			sMask = oAux.mask;
			i++;
		}
		fTotal = fTotal.toFixed(2);
		oAux = document.getElementById('form[SYS_GRID_AGGREGATE_' + oGrid.sGridName + '_' + aAux[2] + ']');
		oAux.value = fTotal;
		oAux = document.getElementById('form[SYS_GRID_AGGREGATE_' + oGrid.sGridName + '__' + aAux[2] + ']');
		// oAux.innerHTML = G.toMask(fTotal, sMask).result;
		oAux.innerHTML = fTotal;
	};
	this.avg = function(oEvent, oDOM) {
		oDOM = (oDOM ? oDOM : oEvent.target || window.event.srcElement);
		var i, aAux, oAux, fTotal, sMask;
		aAux = oDOM.name.split('][');
		i = 1;
		fTotal = 0;
		aAux[2] = aAux[2].replace(']', '');
		while (oAux = this.getElementByName(i, aAux[2])) {
			fTotal += parseFloat(G.cleanMask(oAux.value() || 0, oAux.mask).result.replace(/,/g, ''));
			sMask = oAux.mask;
			i++;
		}
		i--;
		if (fTotal > 0) {
			fTotal = (fTotal / i).toFixed(2);
			oAux = document.getElementById('form[SYS_GRID_AGGREGATE_' + oGrid.sGridName + '_' + aAux[2] + ']');
			oAux.value = fTotal;
			oAux = document.getElementById('form[SYS_GRID_AGGREGATE_' + oGrid.sGridName + '__' + aAux[2] + ']');
			// oAux.innerHTML = G.toMask((fTotal / i), sMask).result;
			oAux.innerHTML = fTotal;
		} else {
			oAux = document.getElementById('form[SYS_GRID_AGGREGATE_' + oGrid.sGridName + '_' + aAux[2] + ']');
			oAux.value = 0;
			oAux = document.getElementById('form[SYS_GRID_AGGREGATE_' + oGrid.sGridName + '__' + aAux[2] + ']');
			// oAux.innerHTML = G.toMask(0, sMask).result;
			oAux.innerHTML = 0;
		}
	};
	this.assignFormulas = function(aFields, sEvent, iRow) {
		iRow = iRow || 1;
		var i, j, oAux;
		for (i = 0; i < aFields.length; i++) {
			j = iRow;
			while (oAux = document.getElementById('form[' + this.sGridName + '][' + j + '][' + aFields[i].sDependentOf + ']')) {
				leimnud.event.add(oAux, sEvent, {
					method : this.evaluateFormula,
					instance : this,
					args : [ oAux, aFields[i] ],
					event : true
				});
				j++;
			}
		}
	};
	this.setFormulas = function(aFormulas) {
		this.aFormulas = aFormulas;
		this.assignFormulas(this.aFormulas, 'change');
	};
	this.evaluateFormula = function(oEvent, oDOM, oField) {
		oDOM = (oDOM ? oDOM : oEvent.target || window.event.srcElement);
		var aAux, sAux, i, oAux;
		var oContinue = true;
		aAux = oDOM.name.split('][');
		sAux = oField.sFormula.replace(/\+|\-|\*|\/|\(|\)|\[|\]|\{|\}|\%|\$/g, ' ');
		sAux = sAux.replace(/^\s+|\s+$/g, '');
		sAux = sAux.replace(/      /g, ' ');
		sAux = sAux.replace(/     /g, ' ');
		sAux = sAux.replace(/    /g, ' ');
		sAux = sAux.replace(/   /g, ' ');
		sAux = sAux.replace(/  /g, ' ');
		aFields = sAux.split(' ');
		aFields = aFields.unique();
		sAux = oField.sFormula;
		for (i = 0; i < aFields.length; i++) {
			if (!isNumber(aFields[i])) {
				oAux = this.getElementByName(aAux[1], aFields[i]);
				sAux = sAux.replace(new RegExp(aFields[i], "g"), "parseFloat(G.cleanMask(this.getElementByName(" + aAux[1] + ", '" + aFields[i] + "').value() || 0, '" + (oAux.sMask ? oAux.sMask : '') + "').result.replace(/,/g, ''))");
				eval("if (!document.getElementById('" + aAux[0] + '][' + aAux[1] + '][' + aFields[i] + "]')) { oContinue = false; }");
			}
		}
		eval("if (!document.getElementById('" + aAux[0] + '][' + aAux[1] + '][' + oField.sFieldName + "]')) { oContinue = false; }");
		if (oContinue) {
			eval("document.getElementById('" + aAux[0] + '][' + aAux[1] + '][' + oField.sFieldName + "]').value = (" + sAux + ').toFixed(2);');
			if (this.aFunctions.length > 0) {
				for (i = 0; i < this.aFunctions.length; i++) {
					oAux = document.getElementById('form[' + this.sGridName + '][' + aAux[1] + '][' + this.aFunctions[i].sFieldName + ']');
					if (oAux) {
						if (oAux.name == aAux[0] + '][' + aAux[1] + '][' + oField.sFieldName + ']') {
							switch (this.aFunctions[i].sFunction) {
							case 'sum':
								this.sum(false, oAux);
								break;
							case 'avg':
								this.avg(false, oAux);
								break;
							}
							if (oAux.fireEvent) {
								oAux.fireEvent('onchange');
							} else {
								var evObj = document.createEvent('HTMLEvents');
								evObj.initEvent('change', true, true);
								oAux.dispatchEvent(evObj);
							}
						}
					}
				}
			}
		} else {
			new leimnud.module.app.alert().make( {
				label : "Check your formula!\n\n" + oField.sFormula
			});
		}
	};
};

/**
 * Delete the respective session row grid variables from Dynaform
 * 
 * @Param grid [object: grid]
 * @Param sRow [integer: row index]
 * @author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@mail.com>
 */
function deleteRowOnDybaform(grid, sRow) {
	// alert(grid.sGridName + ' ' + sRow);
	var oRPC = new leimnud.module.rpc.xmlhttp( {
		url : '../gulliver/genericAjax',
		args : 'request=deleteGridRowOnDynaform&gridname=' + grid.sGridName + '&rowpos=' + sRow
	});
	oRPC.callback = function(rpc) {
		oPanel.loader.hide();
		scs = rpc.xmlhttp.responseText.extractScript();
		scs.evalScript();

		/**
		 * We verify if the debug panel is open, if it is-> update its content
		 */
		if (oDebuggerPanel != null) {
			oDebuggerPanel.clearContent();
			oDebuggerPanel.loader.show();
			var oRPC = new leimnud.module.rpc.xmlhttp( {
				url : 'cases_Ajax',
				args : 'action=showdebug'
			});
			oRPC.callback = function(rpc) {
				oDebuggerPanel.loader.hide();
				var scs = rpc.xmlhttp.responseText.extractScript();
				oDebuggerPanel.addContent(rpc.xmlhttp.responseText);
				scs.evalScript();
			}.extend(this);
			oRPC.make();
		}
	}.extend(this);
	oRPC.make();
}