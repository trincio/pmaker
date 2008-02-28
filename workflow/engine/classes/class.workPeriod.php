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
class workPeriod extends DBTable
{

	/*
	* Constructor
	* @param object $oConnection
	* @return variant
	*/
  function SetTo( $oConnection = null)
  {
  	if ($oConnection)
		{
			return parent::setTo($oConnection, 'LEXICO', array('LEX_TOPIC', 'LEX_KEY'));
		}
		else
		{
			return;
		}
	}

  function Load ( )
  {
  	$this->debug = false;

  	//load the first period
  	parent::Load ( 'WORK_PERIOD', 'FIRST' );
  	$row['initPeriod1'] = $this->Fields['LEX_VALUE'];
  	$row['endPeriod1']  = $this->Fields['LEX_CAPTION'];
  	
  	//load the second period
  	parent::Load ( 'WORK_PERIOD', 'SECOND' );
  	$row['initPeriod2'] = $this->Fields['LEX_VALUE'];
  	$row['endPeriod2']  = $this->Fields['LEX_CAPTION'];
  	
  	//load the working days
  	$noWorkingDays = array (false,false,false, false,false,false, false );
  	//to load multiple rows, the second key is empty, so this line will return all rows with TOPIC = NO_WORKING_DAY
  	parent::Load ( 'NO_WORKING_DAY' ); 
  	while ( is_array ( $this->Fields ) ) {
  		$noWorkingDays[ $this->Fields['LEX_KEY'] ] = $this->Fields['LEX_VALUE'] ;
  		parent::next();
  	}
  	$row['noWorkingDays'] = $noWorkingDays;
  	
  	return $row;
  }

  function Save ($ini1, $end1, $ini2, $end2, $noWorkingDays )
  {
  	//save the first period
  	parent::Load ( 'WORK_PERIOD', 'FIRST' );
  	$this->Fields['LEX_TOPIC']   = 'WORK_PERIOD';
  	$this->Fields['LEX_KEY']     = 'FIRST';
  	$this->Fields['LEX_VALUE']   = $ini1;
  	$this->Fields['LEX_CAPTION'] = $end1;
  	parent::Save();

  	//save the second period
  	parent::Load ( 'WORK_PERIOD', 'SECOND' );
  	$this->Fields['LEX_TOPIC']   = 'WORK_PERIOD';
  	$this->Fields['LEX_KEY']     = 'SECOND';
  	$this->Fields['LEX_VALUE']   = $ini2;
  	$this->Fields['LEX_CAPTION'] = $end2;
  	parent::Save();

  	//save non working days
  	for ( $i = 0; $i <= 6 ; $i++) {
  		parent::Load ( 'NO_WORKING_DAY', $i );
  		$this->Fields['LEX_TOPIC']   = 'NO_WORKING_DAY';
  		$this->Fields['LEX_KEY']     = $i;
  		$this->Fields['LEX_VALUE']   = $noWorkingDays[$i];
  		$this->Fields['LEX_CAPTION'] = $noWorkingDays[$i];
  		$res = parent::Save();
  	}
  	
  }
  
}
?>