<?php
/**
 * class.testTools.php
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