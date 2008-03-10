<?php
/**
 * departments_Ajax.php
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
if (($RBAC_Response=$RBAC->userCanAccess("PM_USERS"))!=1) return $RBAC_Response;
G::LoadInclude('ajax');
G::LoadClass('department');
G::LoadClass('organizationalChart');
G::LoadClass('toolBar');
G::LoadClass('popupMenu');
$formId = "bDhpZ3phTG5vWlhlNktTYzVLZWcwNcKwV3JjU2sxWsKwaGxaeXozSkxmNldPeDM1MA______";
$G_FORM=new form(G::getUIDName($formId));
$G_FORM->id=$formId;
$G_FORM->values=$_SESSION[$G_FORM->id];
//Parse and update the new content
$newContent=$G_FORM->getFields($G_FORM->template);

$function = get_ajax_value( 'function' );
$dbc = new DBConnection();
$ses = new DBSession( $dbc );
switch ( $function ) {
  case 'addDepartment':
    $parent = get_ajax_value('parent');
    $depa = new Department( $dbc );
    $depa->Fields['DEP_TITLE'] = 'New department';
    $depa->Fields['DEP_PARENT'] = $parent;
    $depa->save();
    break;
  case 'delDepartment':
    $id = get_ajax_value('id');
    $depa = new Department( $dbc );
    $depa->Fields['DEP_UID'] = $id;
    echo($id);
    $depa->delete();
    break;
}
?>