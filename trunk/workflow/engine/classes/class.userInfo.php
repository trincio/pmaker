<?php
/**
 * class.userInfo.php
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
require_once 'classes/model/Users.php';

class UserInfo 
{
  function Load ( $uid )
  {
    global $RBAC;
    //Get the user information from RBAC DataBase
    $Fields = $RBAC->Load($uid);
    
    $oUser = new Users();
    $userFields = $oUser->Load ($uid);
    /* Start Comment: Get the user information from WF DataBase*/
    /*
		$stQry =  " SELECT *, ".
					    " CONCAT(USR_LASTNAME,', ',USR_FIRSTNAME) AS USR_NAME, ".
              " DEPARTMENT.DEP_UID AS DEP_ID  " .
              " FROM USERS LEFT JOIN DEPARTMENT ON (USR_DEPARTMENT = DEPARTMENT.DEP_UID) " .
              " WHERE USERS.USR_UID = '" . $uid."'";
    $dset = $this->_dbses->Execute( $stQry );
    $row = $dset->Read();
    */
    
    $Fields = G::array_merges ($userFields, $Fields);
    $this->Fields = $Fields;
//    krumo ( $userFields );
    
  }

}

?>