<?php
/**
 * $Id$
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * You can contact Colosa Inc, 2655 Le Jeune Road, Suite 1112, Coral Gables,
 * FL 33134, USA or email info@colosa.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * ProcessMaker" logo and retain the original copyright notice. If the display
 * of the logo is not reasonably feasible for technical reasons, the
 * Appropriate Legal Notices must display the words "Powered by ProcessMaker"
 * and retain the original copyright notice.
 * -
 */
global $RBAC;
switch ($RBAC->userCanAccess('PM_FACTORY'))
{
	case -2:
	  G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels');
	  G::header('location: ../login/login');
	  die;
	break;
	case -1:
	  G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
	  G::header('location: ../login/login');
	  die;
	break;
}
$processUID = $_GET['PRO_UID'];
$_SESSION['PROCESS'] = $processUID;

$oTemplatePower = new TemplatePower(PATH_TPL . 'processes/processes_Map.html');
$oTemplatePower->prepare();

$G_MAIN_MENU        = 'processmaker';
$G_ID_MENU_SELECTED = 'PROCESSES';
$G_SUB_MENU         = 'processes';

$G_PUBLISH = new Publisher;
$G_PUBLISH->AddContent('template', '', '', '', $oTemplatePower);

$G_HEADER->clearScripts();
$G_HEADER->addScriptFile('/jscore/labels/en.js');
$G_HEADER->addScriptFile('/js/maborak/core/maborak.js');
$G_HEADER->addScriptFile('/js/form/core/pagedTable.js');
$G_HEADER->addScriptFile('/js/common/core/common.js');
$G_HEADER->addScriptFile('/js/common/core/webResource.js');
$G_HEADER->addScriptFile('/js/form/core/form.js');
$G_HEADER->addScriptFile('/js/grid/core/grid.js');
$G_HEADER->addScriptFile('/htmlarea/editor.js');
$G_HEADER->addScriptCode( '
	var leimnud = new maborak();
	leimnud.make();
	leimnud.Package.Load("rpc,drag,drop,panel,app,validator,fx,dom,abbr",{Instance:leimnud,Type:"module"});
	leimnud.Package.Load("json",{Type:"file"});
	leimnud.Package.Load("processmap",{Type:"file",Absolute:true,Path:"/jscore/processmap/core/processmap.js"});
	leimnud.Package.Load("processes_Map",{Type:"file",Absolute:true,Path:"/jscore/processmap/core/processes_Map.js"});
	leimnud.exec(leimnud.fix.memoryLeak);
	leimnud.event.add(window,"load",function(){
		var pb=leimnud.dom.capture("tag.body 0");
		Pm=new processmap();
		Pm.options={
			target		:"pm_target",
			dataServer	:"processes_Ajax.php",
			uid		:"' . $processUID . '",
			lang		:"' . SYS_LANG . '",
			theme		:"processmaker",
			size		:{w:pb.offsetWidth-10,h:pb.offsetHeight},
			images_dir	:"/jscore/processmap/core/images/"
		}
		Pm.make();
	});' );
G::RenderPage('publish');
?>
