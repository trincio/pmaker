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
G::LoadSystem ( 'ymlDomain');
G::LoadSystem ( 'ymlTestCases');
G::LoadSystem ( 'unitTest');
class testTools
{
  function importDB( $host, $user, $password, $database, $importFile )
  {
    exec("mysql -h " . $host . " --user=" . $user . " --password=" . $password . " $database < $importFile");
  }
  function importLocalDB( $importFile )
  {
    self::importDB( DB_HOST, DB_USER, DB_PASS, DB_NAME, $importFile );
  }
  function callMethod( $methodFile, $GET, $POST, $SESSION )
  {
    //TODO $_SERVER
    self::arrayDelete($_GET);
    self::arrayDelete($_POST);
    self::arrayDelete($_SESSION);
    self::arrayAppend($_GET,$GET);
    self::arrayAppend($_POST,$POST);
    self::arrayAppend($_SESSION,$SESSION);
    include( PATH_CORE . 'methods/' . $methodFile );
  }
  function arrayAppend( &$to , $appendFrom )
  {
    foreach($appendFrom as $appendItem) $to[]=$appendItem;
    return true;
  }
  function arrayDelete( &$array )
  {
    foreach($array as $key => $value) unset($array[$key]);
    return true;
  }
  //@@
  function replaceVariables( $Fields, $ExternalVariables=array() )
  {
    //TODO: Verify dependencies between fields
    foreach($Fields as $key => $field)
    {
      if (is_string($field))
      {
        $mergedValues = G::array_merges( $Fields, $ExternalVariables );
        $Fields[$key] = G::ReplaceDataField( $field, $mergedValues );
      }
    }
    return $Fields;
  }
  // EXTRA TOOLS
  function findValue( $value, &$obj )
  {
    if (is_array($obj))
    {
      foreach($obj as $key => $val )
      {
        if ($res=self::findValue( $value , $obj[$key] ))
        {
          if ($res==true) return $key;
          else return $key . '.' . $res;
        }
      }
      return false;
    }
    elseif (is_object($obj))
    {
      foreach($obj as $key => $val )
      {
        if ($res=self::findValue( $value , $obj->$key ))
        {
          if ($res==true) return $key;
          else return $key . '.' . $res;
        }
      }
      return false;
    }
    else
    {
      return $obj==$value;
    }
  }
}
/* Some extra global functions */
function domain($location)
{
  global $testDomain;
  $result = $testDomain->get($location);
  if (count($result)==0) trigger_error("'$location' is an empty domain.", E_USER_WARNING);
  return $result;
}
?>