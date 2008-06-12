<?php
/**
 * class.user.php
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
G::LoadSystem('error');

////////////////////////////////////////////////////
// RBAC_User - Users class
//
// Class to manipulate the users in RBAC
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * RBAC_User - Users class
 * @package RBAC
 * @author Julio Cesar Laura Avendaño
 * @copyright 2007 COLOSA
 */

class RBAC_User extends DBTable
{
	/**
   * Auxiliar object
   */
  var $oObject = null;

	/*
	* Constructor
	* @param object $oConnection
	* @return variant
	*/
	function RBAC_User($oConnection = null)
	{
		if ($oConnection)
		{
			return parent::setTo($oConnection, 'USERS', array('USR_UID'));
		}
		else
		{
			return;
		}
	}

	/*
	* Set the Data Base connection
	* @param object $oConnection
	* @return variant
	*/
	function setTo($oConnection = null)
  {
  	if ($oConnection)
		{
      return parent::setTo($oConnection, 'USERS', array('USR_UID'));
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_DBCONNECTION,
    	                        null,
    	                        null,
    	                        'You tried to call to a setTo method without send an instance of DBConnection!',
    	                        'G_Error',
    	                        true);
    }
  }

  /*
	* Load the user information
	* @param string $sUID
	* @return variant
	*/
	function load($sUID = '')
  {
    if ($sUID !== '')
  	{
  	  return parent::load($sUID);
  	}
  	else
  	{
  		return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a load method without send the User UID!',
    	                        'G_Error',
    	                        true);
  	}
  }

  /*
	* Insert or update a user data
	* @param string $sUID
	* @return variant
	*/
	function save($sUID = '')
  {
  	if ($sUID !== '')
  	{
  		$this->Fields['USR_UID'] = $sUID;
  		$this->is_new = false;
  	}
    return parent::save();
  }



  /*
	* Delete a user
	* @param string $sUID
	* @return variant
	*/
	function delete($sUID = '')
  {
  	if ($sUID !== '')
  	{
  	  $this->Fields['USR_UID'] = $sUID;
  	  $this->is_new = false;
  	  return parent::delete();
    }
    else
    {
    	return PEAR::raiseError(null,
    	                        G_ERROR_USER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a delete method without send the User UID!',
    	                        'G_Error',
    	                        true);
    }
  }


  /*
	* Load the user information using the username
	* @param string $sUsername
	* @return variant
	*/
	function loadByUsername($sUsername = '')
  {
    if ($sUsername !== '')
  	{
  		$this->table_keys = array('USR_USERNAME');
  	  $this->oObject    = parent::load($sUsername);
  		$this->table_keys = array('USR_UID');
  		return $this->oObject;
  	}
  	else
  	{
  		return PEAR::raiseError(null,
    	                        G_ERROR_USER_USERNAME,
    	                        null,
    	                        null,
    	                        'You tried to call to a loadByCode method without send the Username!',
    	                        'G_Error',
    	                        true);
  	}
  }
  /*
	* Verify the username and password
	* @param string $sUsername
	* @param string $sPassword
	* @return variant
	*/
  function verifyLogin($sUsername, $sPassword)
  {
  	if ($sUsername !== '')
  	{
  		if ($sPassword !== '')
  	  {
  	    $this->table_keys = array('USR_USERNAME');
  	    $this->oObject    = parent::load($sUsername);
  	    $this->table_keys = array('USR_UID');
  	    if (is_array($this->Fields))
        {
        	if ($this->Fields['USR_PASSWORD'] === md5($sPassword))
          {
          	if ($this->Fields['USR_STATUS'] == 1)
          	{
          		if ($this->Fields['USR_DUE_DATE'] >= date('Y-m-d'))
          		{
          			$sUserUID     = $this->Fields['USR_UID'];
	      				$this->Fields = array();
	      				return $sUserUID;
          		}
          		else
          		{
          			$this->Fields = array();
          			return -4;
          			/*return PEAR::raiseError(null,
        	                              G_ERROR_DUE_DATE,
        	                              null,
        	                              null,
        	                              'Due date has expired!',
        	                              'G_Error',
        	                              true);*/
          		}
          	}
          	else
          	{
          		$this->Fields = array();
          		return -3;
          		/*return PEAR::raiseError(null,
        	                            G_ERROR_USER_INACTIVE,
        	                            null,
        	                            null,
        	                            'User inactive!',
        	                            'G_Error',
        	                            true);*/
          	}
          }
          else
          {
          	$this->Fields = array();
          	return -2;
          	/*return PEAR::raiseError(null,
        	                          G_ERROR_PASSWORD_INCORRECT,
        	                          null,
        	                          null,
        	                          'Password incorrect!',
        	                          'G_Error',
        	                          true);*/
          }
        }
        else
        {
        	return $this->oObject;
        }
      }
      else
      {
      	$this->Fields = array();
      	return -2;
        /*return PEAR::raiseError(null,
                                G_ERROR_PASSWORD_EMPTY,
                                null,
                                null,
                                'Password empty!',
                                'G_Error',
                                true);*/
      }
    }
    else
    {
    	$this->Fields = array();
    	return -1;
      /*return PEAR::raiseError(null,
                              G_ERROR_USERNAME_EMPTY,
                              null,
                              null,
                              'Username empty!',
                              'G_Error',
                              true);*/
    }
  }


	 /*
	* Verify the username 
	* @param string $sUsername	
	* @return variant
	*/
  function verifyUser($sUsername)
  {
  	if ($sUsername !== '')
  	{  		
  	    $this->table_keys = array('USR_USERNAME');
  	    $this->oObject    = parent::load($sUsername);
  	    $this->table_keys = array('USR_UID');
  	    if (is_array($this->Fields))
        {
        	return 1;
        }
        else
        {
        	return 0;
        }            
    }
    else
    {
    	return 0;
    }  
}
?>