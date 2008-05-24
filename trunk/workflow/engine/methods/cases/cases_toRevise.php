<?
  
if (($RBAC_Response = $RBAC->userCanAccess("PM_USERS")) != 1)
            return $RBAC_Response;


	$G_MAIN_MENU            = 'processmaker';
	$G_SUB_MENU             = 'cases';
	$G_ID_MENU_SELECTED     = 'CASES';
	$G_ID_SUB_MENU_SELECTED = 'CASES_TO_REVISE';

    $G_HEADER->addScriptFile('/js/common/tree/tree.js');
    $G_HEADER->addInstanceModule('leimnud', 'rpc');

    $G_PUBLISH = new Publisher;
    $G_PUBLISH->AddContent('view', 'cases/cases_toRevise');
    $G_PUBLISH->AddContent('smarty', 'cases/cases_toReviseIn', '', '', array());
    //$G_HEADER->addScriptFile('/js/form/core/pagedTable.js');

    G::RenderPage("publish-treeview");
?>        