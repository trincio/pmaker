<?php
/**
 * class.tasks.php
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
require_once 'classes/model/Session.php';
/**
 * Sessions - Sessions class
 * @package ProcessMaker
 * @author Everth S. Berrios Morales
 * @copyright 2008 COLOSA
 */

class Sessions {
  
  public function getSessionUser($sSessionId) {
  	try {  	    		
  	  $oCriteria = new Criteria('workflow');
  	  $oCriteria->addSelectColumn(SessionPeer::USR_UID);  	  
  	  $oCriteria->addSelectColumn(SessionPeer::SES_STATUS); 
  	  $oCriteria->addSelectColumn(SessionPeer::SES_DUE_DATE); 
      $oCriteria->add(SessionPeer::SES_UID,  $sSessionId);                     
      
      $oDataset = SessionPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $aRow = $oDataset->getRow();
      	
      return $aRow;
    }
  	catch (Exception $oError) {
    	throw($oError);
    }
  }
  
  public function verifySession($sSessionId) {
  	try {  	    		
  		$date=date('Y-m-d H:i:s');
  	  $oCriteria = new Criteria('workflow');
  	  $oCriteria->addSelectColumn(SessionPeer::USR_UID);  	  
  	  $oCriteria->addSelectColumn(SessionPeer::SES_STATUS); 
  	  $oCriteria->addSelectColumn(SessionPeer::SES_DUE_DATE); 
      $oCriteria->add(SessionPeer::SES_UID,  $sSessionId);                     
      $oCriteria->add(SessionPeer::SES_STATUS, 'ACTIVE');                     
      $oCriteria->add(SessionPeer::SES_DUE_DATE,  $date, Criteria::GREATER_EQUAL);                     
      
      $oDataset = SessionPeer::doSelectRS($oCriteria);
      $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
      $oDataset->next();
      $aRow = $oDataset->getRow();
      	
      return $aRow;
    }
  	catch (Exception $oError) {
    	throw($oError);
    }
  }

}
?>