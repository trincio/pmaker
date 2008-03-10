<?php
/**
 * class.workPeriod.php
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