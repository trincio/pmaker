<?php
/**
 * class.dates.php
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
/*
 * Created on 21/01/2008
 *
 * @author David Callizaya <davidsantos@colosa.com>
 */
 
  require_once ( "classes/model/TaskPeer.php" );
  require_once ( "classes/model/HolidayPeer.php" );
  
class dates {
  private $holidays      = array();
  private $weekends      = array();
  private $range         = array();
  private $skipEveryYear = true;
  private $calendarDays  = false;  //by default we are using working days
  private $hoursPerDay   = 8;      //you should change this

  /*
   * Calculate $sInitDate + $iDaysCount, skipping non laborable days.
   * Input: Any valid strtotime function type input.
   * Returns: Integer timestamp of the result.
   * Warning: It will hangs if there is no possible days to count as
   * "laborable".
   */
  function calculateDate( $sInitDate, $iDuration, $sTimeUnit, $iTypeDay, $UsrUid = NULL, $ProUid = NULL, $TasUid =NULL ) {
  	//load in class variables the config of working days, holidays etc..
    $this->prepareInformation( $UsrUid , $ProUid , $TasUid );
    
    $iHours = 0; $iDays = 0;
    //convert the $iDuration and $sTimeUnit in hours and days, take in mind 8 hours = 1 day. and then we will have similar for 5 days = 1 weekends
    if ( strtolower ( $sTimeUnit ) == 'hours' ) {
      $iAux = intval(abs($iDuration)); 
      $iHours = $iAux % $this->hoursPerDay;
      $iDays  = intval( $iAux / $this->hoursPerDay );
    }
    if ( strtolower ( $sTimeUnit ) == 'days' ) {
      $iAux = intval(abs($iDuration * $this->hoursPerDay)); 
      $iHours = $iAux % 8;
      $iDays  = intval( $iAux / 8 );
    }
    $addSign = ( $iDuration >= 0 ) ? '+' : '-';
    
    $iInitDate = strtotime( $sInitDate );

    if ( $iTypeDay == 1 ) { // working days
      // if there are days calculate the days,
      $iEndDate = $this->addDays( $iInitDate , $iDays, $addSign );
      // if there are hours calculate the hours, and probably add a day if the quantity of hours for last day > 8 hours
      $iEndDate = $this->addHours( $iEndDate , $iHours, $addSign );
    }
    else { // $task->getTasTypeDay() == 2 // calendar days
      $iEndDate = strtotime( $addSign . $iDays  . ' days '  , $iInitDate );
      $iEndDate = strtotime( $addSign . $iHours . ' hours ' , $iEndDate );
    }

    return $iEndDate;
  }

  function calculateDuration( $sInitDate, $sEndDate = '', $UsrUid = NULL, $ProUid = NULL, $TasUid = NULL) {
    list( $this->holidays, $this->weekends ) = $this->prepareInformation($UsrUid, $ProUid, $TasUid);
    if ((string)$sEndDate == '') {
      $sEndDate = date('Y-m-d H:i:s');
    }
    if (strtotime($sInitDate) > strtotime($sEndDate)) {
      $sAux      = $sInitDate;
      $sInitDate = $sEndDate;
      $sEndDate  = $sAux;
    }
    $aAux1       = explode(' ', $sInitDate);
    $aAux2       = explode(' ', $sEndDate);
    $aInitDate   = explode('-', $aAux1[0]);
    $aEndDate    = explode('-', $aAux2[0]);
    $i           = 1;
    $iWorkedDays = 0;
    $bFinished   = false;
    $fHours1     = 0.0;
    $fHours2     = 0.0;
    if (count($aInitDate) != 3) {
      $aInitDate = array(0, 0, 0);
    }
    if (count($aEndDate) != 3) {
      $aEndDate = array(0, 0, 0);
    }
    if ($aInitDate !== $aEndDate) {
      while (!$bFinished && ($i < 10000)) {
        $sAux = date('Y-m-d', mktime(0, 0, 0, $aInitDate[1], $aInitDate[2] + $i, $aInitDate[0]));
        if ($sAux != implode('-', $aEndDate)) {
          if (!in_array($sAux, $this->holidays)) {
            if (!in_array(date('w', mktime(0, 0, 0, $aInitDate[1], $aInitDate[2] + $i, $aInitDate[0])), $this->weekends)) {
              $iWorkedDays++;
            }
          }
          $i++;
        }
        else {
          $bFinished = true;
        }
      }
      if (isset($aAux1[1])) {
        $aAux1[1] = explode(':', $aAux1[1]);
        $fHours1 = 24 - ($aAux1[1][0] + ($aAux1[1][1] / 60) + ($aAux1[1][2] / 3600));
      }
      if (isset($aAux2[1])) {
        $aAux2[1] = explode(':', $aAux2[1]);
        $fHours2 = $aAux2[1][0] + ($aAux2[1][1] / 60) + ($aAux2[1][2] / 3600);
      }
      $fDuration = ($iWorkedDays * 24) + $fHours1 + $fHours2;
    }
    else {
      $fDuration = (strtotime($sEndDate) - strtotime($sInitDate)) / 3600;
    }
    return $fDuration;
  }
  
  /*
   * Configuration functions
   */
  function prepareInformation( $UsrUid = NULL , $ProUid = NULL , $TasUid =NULL )
  {
    // setup calendarDays according the task
    if (isset($TasUid))
    {
      $task = TaskPeer::retrieveByPK( $TasUid );
      if (!is_null($task)) {
        $this->calendarDays = ($task->getTasTypeDay()==2);
      }
    }

    //get an array with all holidays.
    $aoHolidays=HolidayPeer::doSelect(new Criteria());
    $holidays=array();
    foreach($aoHolidays as $holiday) 
      $holidays[] = strtotime($holiday->getHldDate());
      
    // by default the weekdays are from monday to friday  
    $this->weekends = array(0,6);
    $this->holidays = $holidays;
    return ;
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
  private function addDays( $iInitDate , $iDaysCount, $addSign = '+' )
  {
    $iEndDate  = $iInitDate;
    $aList     = $this->holidays;
    for( $r=1; $r <= $iDaysCount ; $r++) {
      $iEndDate  = strtotime( $addSign . "1 day", $iEndDate );
      $dayOfWeek = idate('w',$iEndDate); //now sunday=0
      if ( array_search( $dayOfWeek, $this->weekends )!== false ) $r--; //continue loop, but we are adding one more day.
    }
    return $iEndDate;
  }
  
  private function addHours( $sInitDate , $iHoursCount, $addSign = '+' )
  {
    $iEndDate  = strtotime( $addSign . $iHoursCount ." hours", $sInitDate );
    return $iEndDate;
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