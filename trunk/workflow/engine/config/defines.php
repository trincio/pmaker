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

//***************** URL KEY *********************************************
  define("URL_KEY", 'c0l0s40pt1mu59r1m3' );

//************ Other definitions  **************
  //web service timeout
  define( 'TIMEOUT_RESPONSE', 100 );
  //to login like workflow system
  define( 'APPLICATION_CODE', 'ProcessMaker' );

  define ( 'MAIN_POFILE', 'processmaker');
  define ( 'PO_SYSTEM_VERSION',  'PM 4.0.1');

  $G_CONTENT = NULL;
  $G_MESSAGE = "";
  $G_MESSAGE_TYPE = "info";
  $G_MENU_SELECTED = -1;
  $G_MAIN_MENU = "default";

  //remove this, when migrate to Propel
  define ( 'PEAR_DATABASE', 'mysql');
  define ( 'ENABLE_ENCRYPT', 'no' );
  define('DB_ERROR_BACKTRACE', TRUE);

//************ Environment definitions  **************
  define ( 'G_PRO_ENV',  'PRODUCTION' );
  define ( 'G_DEV_ENV',  'DEVELOPMENT' );
  define ( 'G_TEST_ENV', 'TEST' );

///************TimeZone Set***************//

  if (version_compare(phpversion(), "5.1.0", ">=")) {
    date_default_timezone_set("America/La_Paz");
  }
  else {
    // you're not
  }

/*
 * Number of files per folder at PATH_UPLOAD (cases documents)  
 */
 define( 'APPLICATION_DOCUMENTS_PER_FOLDER', 1000 );