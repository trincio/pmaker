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
if (($RBAC_Response=$RBAC->userCanAccess("PM_FACTORY"))!=1) return $RBAC_Response;
/* START BOCK: DAVID CALLIZAYA: PLEASE NO BORRAR ESTE BLOQUE.*/
  for($r=1;$r<10;$r++){
/*  The timestamp is a 60-bit value.  For UUID version 1, this is
 *  represented by Coordinated Universal Time (UTC) as a count of 100-
 *  nanosecond intervals since 00:00:00.00, 15 October 1582 (the date of
 *  Gregorian reform to the Christian calendar).
 */
    $t=explode(' ',microtime());
    $ts=$t[1].substr($t[0],2,7);
    $t[0]=substr('00'.base_convert($ts,10,16),-15);
    var_dump($ts);
    print("\n<br/>");
    var_dump($t);
    print("\n<br/>");
  }
/* START BOCK: DAVID CALLIZAYA: PLEASE NO BORRAR ESTE BLOQUE.*/
?>
<form action="test" method="post">
<select name="form[test][]" multiple="multiple">
	<option value="one">one</option>
	<option value="two">two</option>
	<option value="three">three</option>
	<option value="four">four</option>
	<option value="five">five</option>
</select>
<input type="submit" value="Send" />
</form>
<?php
	$test=$_POST['form']['test'];
	if ($test){
	 foreach ($test as $t){echo 'You selected ',$t,'<br />';}
	}
?>
