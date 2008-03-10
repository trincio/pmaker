<?php
/**
 * tableTest.php
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
 * Created on 03/01/2008
 *
 * @author David Callizaya <davidsantos@colosa.com>
 */

G::LoadClass("dates");
G::LoadClass("configuration");
$test=new dates();
print("Set SkipEveryYear = true<br/>");
$test->setSkipEveryYear(true);
print("skipDayOfWeek(1=Sunday)<br/>");
$test->skipDayOfWeek(1);
print("skipDayOfWeek(7=Saturday)<br/>");
$test->skipDayOfWeek(7);
print("skipDate('28-01-2007')<br/>");
//$test->skipDate("28-01-2007");
print("skipDateRange('29-01-2007','31-01-2007')<br/>");
$test->skipDateRange("29-01-2007","31-01-2007");
print("====================================<br/>");
$start="22-01-2008";
$days=5;
$test->calculateDate=0;
$result = $test->calculateDate($start,$days);
print(sprintf("Start Date: %s<br/>Days Additioned: %s<br/>Result: %s", $start, $days, date('d-m-Y',$result) . "(timestamp=$result)" ));

$conf=new Configurations();
$holidays=$test;
$conf->saveObject($holidays,'ProcessMaker','holidays');
//var_dump($conf->loadObject('ProcessMaker','holidays'));


require_once ( "classes/model/HolidayPeer.php" );
require_once ( "classes/model/TaskPeer.php" );
$h=HolidayPeer::doSelect(new Criteria());
var_dump($h[0]->getHldDate());

$task=TaskPeer::retrieveByPK("947961211CDA4D");
var_dump($task->getTasDuration(),$task->getTasTimeUnit());

var_dump(strtotime("2008-05-05 12:00:01"));
//

die;
$G_PUBLISH = new Publisher;
$G_PUBLISH->publisherId='dynaformEditor';
$G_HEADER->setTitle(G::LoadTranslation('ID_DYNAFORM_EDITOR'));

?>