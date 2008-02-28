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
//
// It works with the table TRIGGER
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * Trigger - Trigger class
 * @package ProcessMaker
 * @author Julio Cesar Laura Avendaño
 * @copyright 2007 COLOSA
 */

G::LoadClass('pmObject');

class Trigger extends PmObject
{
	/*
	* Constructor
	* @param object $oConnection
	* @return variant
	*/
  function setTo( $oConnection = null)
  {
  	if ($oConnection)
		{
			return parent::setTo($oConnection, 'TRIGGER', array('TRI_UID', 'PRO_UID'));
		}
		else
		{
			return;
		}
	}

  /*
	* Load the trigger information
	* @param string $sUID
	* @return variant
	*/
	function load($sUID = '')
  {
    if ($sUID !== '')
  	{
  		$this->table_keys	= array('TRI_UID' );
  		parent::load($sUID);
  		$aFields = $this->Fields;

  		/** Start Comment: Charge TRI_TITLE and TRI_DESCRIPTION */
  	  $this->content->load(array('CON_CATEGORY' => 'TRI_TITLE', 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG));
			$aFields['TRI_TITLE'] = $this->content->Fields['CON_VALUE'];

			$this->content->load(array('CON_CATEGORY' => 'TRI_DESCRIPTION', 'CON_ID' => $sUID, 'CON_LANG' => SYS_LANG));
			$aFields['TRI_DESCRIPTION'] = $this->content->Fields['CON_VALUE'];

			$this->Fields = $aFields;
			/** End Comment*/

			$this->table_keys = array('TRI_UID', 'PRO_UID');
  	  return $this->Fields;
  	}
  	else
  	{
  		return PEAR::raiseError(null,
    	                        G_ERROR_TRIGGER_UID,
    	                        null,
    	                        null,
    	                        'You tried to call to a load method without send the Trigger UID!',
    	                        'G_Error',
    	                        true);
  	}
  }

  /*
	* Save the trigger information
	* @param string $sUID
	* @return variant
	*/
  function save ($aData)
  {
		$this->Fields = array('PRO_UID'    => (isset($aData['PRO_UID'])    ? $aData['PRO_UID']              : $this->Fields['PRO_UID']),
													'TRI_TYPE'   => (isset($aData['TRI_TYPE'])   ? strtoupper($aData['TRI_TYPE']) : (isset($this->Fields['TRI_TYPE']) ? $this->Fields['TRI_TYPE'] : 'SCRIPT')),
													'TRI_WEBBOT' => (isset($aData['TRI_WEBBOT']) ? $aData['TRI_WEBBOT']           : $this->Fields['TRI_WEBBOT']));

    if($aData['TRI_UID'] != '')
    {
    	$sUID                    = $aData['TRI_UID'];
    	$this->Fields['TRI_UID'] = $aData['TRI_UID'];
			$aData['CON_ID']         = $aData['TRI_UID'];
			$this->is_new            = false;
		}
		else
		{
			$sUID                    = G::generateUniqueID();
			$this->Fields['TRI_UID'] = $sUID;
			$aData['CON_ID']         = $sUID;
			$this->is_new            = true;
		}

  	parent::save();

		/** Start Comment: Save in the table CONTENT */
  	$this->content->saveContent('TRI_TITLE',$aData);
		$this->content->saveContent('TRI_DESCRIPTION',$aData);
		/** End Comment */

		return $sUID;
  }

  /*
	* Delete a trigger
	* @param string $sUID
	* @return variant
	*/
  function delete($sUID)
  {
    if (isset($sUID))
    {
      $this->table_keys  = array('TRI_UID');
      $this->Fields['TRI_UID'] = $sUID;
      parent::delete();
      $this->table_keys = array('TRI_UID', 'PRO_UID');
      $this->content->table_keys= array('CON_ID');
      $this->content->Fields['CON_ID'] = $sUID;
      $this->content->delete();
      return;
    }
    else
    {
      return PEAR::raiseError(null,
                              G_ERROR_TRIGGER_UID,
                              null,
                              null,
                              'You tried to call to a delete method without send the Trigger UID!',
                              'G_Error',
                              true);
    }
  }
}

?>
