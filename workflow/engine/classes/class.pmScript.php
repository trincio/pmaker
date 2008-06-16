<?php
/**
 * class.pmScript.php
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
////////////////////////////////////////////////////
// Execute and evaluate PMScripts
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * PMScript - PMScript class
 * @package ProcessMaker
 * @author Julio Cesar Laura Avendaño
 * @copyright 2007 COLOSA
 */

class PMScript
{
	/**
   * Original fields
   */
  var $aOriginalFields = array();

  /**
   * Fields to use
   */
  var $aFields = array();

  /**
   * Script
   */
  var $sScript = '';

  /**
   * Error has happened?
   */
  var $bError = false;

	/*
   * Set the fields to use
   * @param string $sScript
	 * @return void
	 */
  function setFields($aFields = array())
  {
  	if (!is_array($aFields))
  	{
  		$aFields = array();
  	}
  	$this->aOriginalFields = $this->aFields = $aFields;
	}

	/*
   * Set the current script
   * @param string $sScript
	 * @return void
	 */
  function setScript($sScript = '')
  {
  	//validate?
  	$this->sScript = $sScript;
	}

	/*
   * Verify the syntax
   * @param string $sScript
	 * @return boolean

	*/
	function validSyntax($sScript)
	{
		//
		return true;
	}

	/*
   * Execute the current script
	 * @return void
	 */
  function execute()
  {
  	$sScript = "try {\n";
  	$iAux    = 0;
  	$bEqual  = false;
  	$iOcurrences = preg_match_all('/\@(?:([\@\%\#\?\$\=])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*(?:[\\\\][\w\W])?)*)\))((?:\s*\[[\'"]?\w+[\'"]?\])+)?/', $this->sScript, $aMatch, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
  	if ($iOcurrences)
  	{
  		for($i = 0; $i < $iOcurrences; $i++)
			{
				$sAux = substr($this->sScript, $iAux, $aMatch[0][$i][1] - $iAux);
				if (!$bEqual)
				{
					if (strpos($sAux, '=') !== false)
					{
						$bEqual = true;
					}
				}
				if ($bEqual)
				{
					if (strpos($sAux, ';') !== false)
					{
						$bEqual = false;
					}
				}
				if ($bEqual) {
				  if (!isset($aMatch[5][$i][0])) {
			      eval("if (!isset(\$this->aFields['" . $aMatch[2][$i][0] . "'])) { \$this->aFields['" . $aMatch[2][$i][0] . "'] = null; }");
			    }
			    else {
			      eval("if (!isset(\$this->aFields" . $aMatch[5][$i][0] . ")) { \$this->aFields" . $aMatch[5][$i][0] . " = null; }");
			    }
				}
				$sScript .= $sAux;
				$iAux     = $aMatch[0][$i][1] + strlen($aMatch[0][$i][0]);
				switch ($aMatch[1][$i][0])
				{
					case '@':
					  if ($bEqual)
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "pmToString(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
					  	}
					  	else {
					  	  $sScript .= "pmToString(\$this->aFields" . $aMatch[5][$i][0] . ")";
					  	}
					  }
					  else
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					  	}
					  	else {
					  	  $sScript .= "\$this->aFields" . $aMatch[5][$i][0];
					  	}
					  }
					break;
					case '%':
					  if ($bEqual)
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "pmToInteger(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
              }
					  	else {
					  	  $sScript .= "pmToInteger(\$this->aFields" . $aMatch[5][$i][0] . ")";
					  	}
					  }
					  else
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					  	}
					  	else {
					  	  $sScript .= "\$this->aFields" . $aMatch[5][$i][0];
					  	}
					  }
					break;
					case '#':
					  if ($bEqual)
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "pmToFloat(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
					  	}
					  	else {
					  	  $sScript .= "pmToFloat(\$this->aFields" . $aMatch[5][$i][0] . ")";
					  	}
					  }
					  else
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					  	}
					  	else {
					  	  $sScript .= "\$this->aFields" . $aMatch[5][$i][0];
					  	}
					  }
					break;
					case '?':
					  if ($bEqual)
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "pmToUrl(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
					  	}
					  	else {
					  	  $sScript .= "pmToUrl(\$this->aFields" . $aMatch[5][$i][0] . ")";
					  	}
					  }
					  else
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					  	}
					  	else {
					  	  $sScript .= "\$this->aFields" . $aMatch[5][$i][0];
					  	}
					  }
					break;
					case '$':
					  if ($bEqual)
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "pmSqlEscape(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
					  	}
					  	else {
					  	  $sScript .= "pmSqlEscape(\$this->aFields" . $aMatch[5][$i][0] . ")";
					  	}
					  }
					  else
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					    }
					  	else {
					  	  $sScript .= "\$this->aFields" . $aMatch[5][$i][0];
					  	}
					  }
					break;
					case '=':
					  if ($bEqual)
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					  	}
					  	else {
					  	  $sScript .= "\$this->aFields" . $aMatch[5][$i][0];
					  	}
					  }
					  else
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					    }
					  	else {
					  	  $sScript .= "\$this->aFields" . $aMatch[5][$i][0];
					  	}
					  }
					break;
				}
			}
  	}
  	$sScript .= substr($this->sScript, $iAux);
  	$sScript .= "\n} catch (Exception \$oException) {\n  \$this->aFields['__ERROR__'] = \$oException->getMessage();\n}";
  	if ($this->validSyntax($sScript))
  	{
  		$this->bError = false;
		  eval($sScript);
	  }
	  else
	  {
	  	$this->bError = true;
	  }
	}

	/*
   * Evaluate the current script
	 * @return void
	 */
  function evaluate()
  {
  	$bResult = null;
  	$sScript = '';
  	$iAux    = 0;
  	$bEqual  = false;
  	$iOcurrences = preg_match_all('/\@(?:([\@\%\#\?\$\=])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*(?:[\\\\][\w\W])?)*)\))((?:\s*\[[\'"]?\w+[\'"]?\])+)?/', $this->sScript, $aMatch, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
  	if ($iOcurrences)
  	{
  		for($i = 0; $i < $iOcurrences; $i++)
			{
				if (!isset($this->aFields[$aMatch[2][$i][0]]))
				{
					$this->aFields[$aMatch[2][$i][0]] = '';
				}
				$sAux = substr($this->sScript, $iAux, $aMatch[0][$i][1] - $iAux);
				if (!$bEqual)
				{
					if (strpos($sAux, '=') !== false)
					{
						$bEqual = true;
					}
				}
				if ($bEqual)
				{
					if (strpos($sAux, ';') !== false)
					{
						$bEqual = false;
					}
				}
				$sScript .= $sAux;
				$iAux     = $aMatch[0][$i][1] + strlen($aMatch[0][$i][0]);
				switch ($aMatch[1][$i][0])
				{
					case '@':
					  if ($bEqual)
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "pmToString(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
					  	}
					  	else {
					  	  $sScript .= "pmToString(\$this->aFields" . $aMatch[5][$i][0] . ")";
					  	}
					  }
					  else
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					  	}
					  	else {
					  	  $sScript .= "\$this->aFields" . $aMatch[5][$i][0];
					  	}
					  }
					break;
					case '%':
					  if ($bEqual)
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "pmToInteger(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
              }
					  	else {
					  	  $sScript .= "pmToInteger(\$this->aFields" . $aMatch[5][$i][0] . ")";
					  	}
					  }
					  else
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					  	}
					  	else {
					  	  $sScript .= "\$this->aFields" . $aMatch[5][$i][0];
					  	}
					  }
					break;
					case '#':
					  if ($bEqual)
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "pmToFloat(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
					  	}
					  	else {
					  	  $sScript .= "pmToFloat(\$this->aFields" . $aMatch[5][$i][0] . ")";
					  	}
					  }
					  else
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					  	}
					  	else {
					  	  $sScript .= "\$this->aFields" . $aMatch[5][$i][0];
					  	}
					  }
					break;
					case '?':
					  if ($bEqual)
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "pmToUrl(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
					  	}
					  	else {
					  	  $sScript .= "pmToUrl(\$this->aFields" . $aMatch[5][$i][0] . ")";
					  	}
					  }
					  else
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					  	}
					  	else {
					  	  $sScript .= "\$this->aFields" . $aMatch[5][$i][0];
					  	}
					  }
					break;
					case '$':
					  if ($bEqual)
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "pmSqlEscape(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
					  	}
					  	else {
					  	  $sScript .= "pmSqlEscape(\$this->aFields" . $aMatch[5][$i][0] . ")";
					  	}
					  }
					  else
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					    }
					  	else {
					  	  $sScript .= "\$this->aFields" . $aMatch[5][$i][0];
					  	}
					  }
					break;
					case '=':
					  if ($bEqual)
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					  	}
					  	else {
					  	  $sScript .= "\$this->aFields" . $aMatch[5][$i][0];
					  	}
					  }
					  else
					  {
					    if (!isset($aMatch[5][$i][0])) {
					  	  $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					    }
					  	else {
					  	  $sScript .= "\$this->aFields" . $aMatch[5][$i][0];
					  	}
					  }
					break;
				}
			}
  	}
  	$sScript .= substr($this->sScript, $iAux);
  	$sScript  = '$bResult = ' . $sScript . ';';
  	if ($this->validSyntax($sScript))
  	{
  		$this->bError = false;
		  eval($sScript);
	  }
	  else
	  {
	  	$this->bError = true;
	  }
		return $bResult;
	}
}

//Start - Private functions

/*
 * Convert to string
 * @param variant $vValue
 * @return string
 */
function pmToString($vValue)
{
	return (string)$vValue;
}

/*
 * Convert to integer
 * @param variant $vValue
 * @return integer
 */
function pmToInteger($vValue)
{
	return (int)$vValue;
}

/*
 * Convert to float
 * @param variant $vValue
 * @return float
 */
function pmToFloat($vValue)
{
	return (float)$vValue;
}

/*
 * Convert to Url
 * @param variant $vValue
 * @return url
 */
function pmToUrl($vValue)
{
	return urlencode($vValue);
}

/*
 * Convert to data base escaped string
 * @param variant $vValue
 * @return string
 */
function pmSqlEscape($vValue)
{
	return G::sqlEscape($vValue);
}

//End - Private functions

//Start - Custom functions
G::LoadClass('pmFunctions');
//End - Custom functions
?>