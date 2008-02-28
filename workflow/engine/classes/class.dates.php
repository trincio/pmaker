<?php
/*
 * Created on 21/01/2008
 *
 * @author David Callizaya <davidsantos@colosa.com>
 */
require_once ( "classes/model/HolidayPeer.php" );
class dates
{
	private $holidays  = array();
	private $weekends  = array();
	private $range     = array();
	private $skipEveryYear=true;
	private $calendarDays = false;
	/*
	 * USER FUNCTIONS
	 */
	/*
	 * Calculate $sInitDate + $iDaysCount, skipping non laborable days.
	 * Input: Any valid strtotime function type input. 
	 * Returns: Integer timestamp of the result.
	 * Warning: It will hangs if there is no possible days to count as
	 * "laborable".
	 */
	function calculateDate( $sInitDate , $iDaysCount , $UsrUid = NULL , $ProUid = NULL ,$TasUid =NULL )
	{
		list( $this->holidays, $this->weekends ) = $this->prepareInformation( $UsrUid , $ProUid , $TasUid );
		$iEndDate = $this->addDays( strtotime($sInitDate) , $iDaysCount );
		$iEndDate = $this->addHours( $iEndDate , $iDaysCount );
		return $iEndDate;
	}
	/*
	 * Configuration functions
	 */
	function prepareInformation( $UsrUid = NULL , $ProUid = NULL , $TasUid =NULL )
	{
		if (isset($TasUid))
		{
			$task = TaskPeer::retrieveByPK( $TasUid );
			$this->calendarDays=($task->getTasTypeDay()==2);
		}
		$aoHolidays=HolidayPeer::doSelect(new Criteria());
		$holidays=array();
		foreach($aoHolidays as $holiday) $holidays[]=strtotime($holiday->getHldDate());
		$weekends = array(1,7);
		return array( $holidays , $weekends );
	}
	/*
	 * Set to repeat for every year all dates defined in $this->holiday 
	 */
	function setSkipEveryYear( $bSkipEveryYear )
	{
		$this->skipEveryYear = $bSkipEveryYear===true;
	}
	/*
	 * Add a single date to holidays
	 */
	function addHoliday( $sDate )
	{
		if ($date=strtotime( $sDate )) $this->holidays[]=self::truncateTime($date);
		else throw new Exception("Invalid date: $sDate."); 
	}
	/*
	 * Set all the holidays
	 * $aDate must be an array of (strtotime type) dates
	 */
	function setHolidays( $aDates )
	{
		foreach($aDates as $sDate) $this->holidays = $aDates;
	}
	/*
	 * Set all the weekends
	 * $aWeekends must be an array of integers [1,7]
	 * 1=Sunday
	 * 7=Saturday 
	 */
	function setWeekends( $aWeekends )
	{
		$this->weekends = $aWeekends;
	}
	/*
	 * Add one day of week to the weekends list 
	 * $aWeekends must be an array of integers [1,7]
	 * 1=Sunday
	 * 7=Saturday 
	 */
	function skipDayOfWeek( $iDayNumber )
	{
		if ($iDayNumber<1 || $iDayNumber>7) throw new Exception("The day of week must be a number from 1 to 7.");
		$this->weekends[]=$iDayNumber;
	}
	/*
	 * Add a range of non working dates  
	 * $sDateA must be a (strtotime type) dates
	 * $sDateB must be a (strtotime type) dates
	 */
	function addNonWorkingRange( $sDateA , $sDateB )
	{
		if ($date=strtotime( $sDateA )) $iDateA=self::truncateTime($date);
		else throw new Exception("Invalid date: $sDateA."); 
		if ($date=strtotime( $sDateB )) $iDateB=self::truncateTime($date);
		else throw new Exception("Invalid date: $sDateB.");
		if ($iDateA>$iDateB) { $s=$iDateA;$iDateA=$iDateB;$iDateB=$s; };  
		$this->range[]=array( $iDateA , $iDateB );
	}
	/*
	 * PRIVATE UTILITARY FUNCTIONS
	 */
	private function addDays( $sInitDate , $iDaysCount )
	{
		$aTime = $this->getTime( $sInitDate );
		$iInitDate=self::truncateTime( $sInitDate );
		$iEndDate=$iInitDate;
		$aList = $this->holidays;
		$lastYear = 0;
		for($r=1; $r <= $iDaysCount ; $r++)
		{
			$iEndDate = strtotime( "+1 day", $iEndDate );
			$thisYear = idate('Y',$iEndDate); 
			$dayOfWeek = idate('w',$iEndDate)+1; 
			if ($this->skipEveryYear && $lastYear != $thisYear ) 
			{
				$aList =  $this->listForYear($thisYear);
				$lastYear = $thisYear;
			}
			if ($this->calendarDays) $r=$r;
			elseif (array_search($dayOfWeek,$this->weekends)!==false) $r--; 
			elseif (array_search($iEndDate,$aList)!==false) $r--;
			elseif ($this->inRange($iEndDate)) $r--;
		}
		return $this->setTime( $iEndDate , $aTime );
	}
	private function addHours( $sInitDate , $iTimeCount )
	{
		return $sInitDate;
	} 
	/* $iDate = valid timestamp
	 * Returns: true if it is within any of the ranges defined.
	 */
	private function inRange( $iDate )
	{
		$aRange = $this->range;
		$iYear = idate( 'Y', $iDate );
		foreach($aRange as $key => $rang)
		{
			if ($this->skipEveryYear) 
			{
				$deltaYears = idate( 'Y', $rang[1] ) - idate( 'Y', $rang[0] );
				$rang[0]=self::changeYear( $rang[0] , $iYear );
				$rang[1]=self::changeYear( $rang[1] , $iYear + $deltaYears );
			}
			if (($iDate>=$rang[0]) && ($iDate<=$rang[1])) return true;
		}
		return false;
	}
	private function truncateTime( $iDate )
	{
		return mktime(0,0,0,idate('m',$iDate),idate('d',$iDate),idate('Y',$iDate));
	} 
	private function getTime( $iDate )
	{
		return array(idate('H',$iDate),idate('m',$iDate),idate('s',$iDate));
	} 
	private function setTime( $iDate , $aTime )
	{
		return mktime($aTime[0],$aTime[1],$aTime[2],idate('m',$iDate),idate('d',$iDate),idate('Y',$iDate));
	} 
	/* Returns an array with all the dates of $this->skip['List'] with its
	 * year changed to $iYear.
	 * Warning: Don't know what to do if change a 29-02-2004 to 29-02-2005
	 *          the last one does not exists.
	 */
	private function listForYear( $iYear )
	{
		$aList = $this->holidays;
		foreach($aList as $k => $v) 
		{
			$aList[$k] = self::changeYear( $v , $iYear );
		}
		return $aList;
	}
	private function changeYear( $iDate , $iYear )
	{
		if ($delta = ( $iYear - idate('Y',$iDate) ) )
		{
			 $iDate = strtotime( "$delta year" , $iDate );
		}
		return $iDate;
	}
}
?>