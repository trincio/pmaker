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