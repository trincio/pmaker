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
if (($RBAC_Response=$RBAC->userCanAccess("PM_SETUP"))!=1) return $RBAC_Response;
  Header("Content-type: image/jpeg");

	G::LoadClass( "workPeriod" );

	$dbc = new DBConnection;
	$ses = new DBSession( $dbc );
	$obj = new workPeriod( $dbc );
	
	$row = $obj->Load ();

	$initPeriod1     = $row['initPeriod1']/ 60;
	$endPeriod1      = $row['endPeriod1'] / 60;
	$initPeriod2     = $row['initPeriod2']/ 60;
	$endPeriod2      = $row['endPeriod2'] / 60;
	$noWorkingDays   = $row['noWorkingDays'];

  $cant = 7;

  $w = 660;
  $h = $cant*18 + 20;
  $im= ImageCreate($w, $h);
  $width = $w;
  $height = $h;
  $center_x = intval($width / 2);
  $center_y = intval($height / 2);
 
  $bgcolor = ImageColorAllocate($im, 250, 250, 255);
  $plomo   = ImageColorAllocate($im, 220, 220, 220);
  $orange  = ImageColorAllocate($im, 255, 64, 64);
  $gris    = ImageColorAllocate($im, 150, 150, 155);
  $white   = ImageColorAllocate($im, 255, 255, 255);
  $red     = ImageColorAllocate($im, 255, 0, 0);
  $brown   = ImageColorAllocate($im, 160, 80, 0);
  $black   = ImageColorAllocate($im,   0,0,0);
  ImageFilledRectangle($im, 0, 0, $width-1, $height-1, $bgcolor);
  ImageRectangle      ($im, 0, 0, $width-1, $height-1, $black);


  $x = 10; $y = 20;
  $x1 = 78;
  $x2 = $x1 + 2*6;

  $weekday[0] = 'Sunday'; 
  $weekday[1] = 'Monday'; 
  $weekday[2] = 'Tuesday'; 
  $weekday[3] = 'Wednesday'; 
  $weekday[4] = 'Thursday'; 
  $weekday[5] = 'Friday'; 
  $weekday[6] = 'Saturday'; 
 
  for ( $day = 0; $day < count ($weekday ); $day ++ ) {
    ImageString($im, 2, $x, $y, $weekday[$day], $black);
    for ($i = 0; $i < 24*6; $i++) {
      ImageRectangle($im, $x1+$i* 4, $y, $x1+ ($i+1)*4, $y+12, $plomo );
    	  if ( $i >= $initPeriod1 * 6 && $i < $endPeriod2 * 6 && ( $i < $endPeriod1 * 6 || $i >= $initPeriod2 * 6 ) ) 
    	    $color = $orange;
    	  else
    	    $color = $white; 
			if ( isset ( $noWorkingDays[ $day ]) && $noWorkingDays[ $day ] ) $color = $white;
 	    ImageFillToBorder($im, $x1+$i*4+1, $y+1, $plomo, $color );
    }

    $y+=18;
  }

  $y = 20;  
  for ($i = 0; $i <= 24; $i++) {
    ImageLine($im, $x1+$i* 4*6, $y-5,    $x1+ $i*4*6, $y-5+18*$cant, $gris);
  	if ( $i < 24 ) {
      ImageLine($im, $x2+$i* 4*6, $y-5,    $x2+ $i*4*6, $y-5+18*$cant, $plomo);
      ImageString($im, 1, $x1+$i* 4*6, $y-10, $i, $black);
    }
  }
      //ImageString($im, 2, 5, 5, $initPeriod1*6 . ", $endPeriod1, $initPeriod2, $endPeriod2 ", $black);

  ImageJpeg($im);
?>