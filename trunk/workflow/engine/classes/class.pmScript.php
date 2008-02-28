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
  	$sScript = '';
  	$iAux    = 0;
  	$bEqual  = false;
  	$iOcurrences = preg_match_all('/\@(?:([\@\%\#\?\$])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*(?:[\\\\][\w\W])?)*)\))/', $this->sScript, $aMatch, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
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
				$sScript .= $sAux;
				$iAux     = $aMatch[0][$i][1] + strlen($aMatch[0][$i][0]);
				switch ($aMatch[1][$i][0])
				{
					case '@':
					  if ($bEqual)
					  {
					  	$sScript .= "pmToString(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
					  }
					  else
					  {
					  	$sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					  }
					break;
					case '%':
					  if ($bEqual)
					  {
					  	$sScript .= "pmToInteger(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
					  }
					  else
					  {
					  	$sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					  }
					break;
					case '#':
					  if ($bEqual)
					  {
					  	$sScript .= "pmToFloat(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
					  }
					  else
					  {
					  	$sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					  }
					break;
					case '?':
					  if ($bEqual)
					  {
					  	$sScript .= "pmToUrl(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
					  }
					  else
					  {
					  	$sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					  }
					break;
					case '$':
					  if ($bEqual)
					  {
					  	$sScript .= "pmSqlEscape(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
					  }
					  else
					  {
					  	$sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					  }
					break;
				}
			}
  	}
  	$sScript .= substr($this->sScript, $iAux);
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
  	$iOcurrences = preg_match_all('/\@(?:([\@\%\#\?\$])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*(?:[\\\\][\w\W])?)*)\))/', $this->sScript, $aMatch, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
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
					  	$sScript .= "pmToString(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
					  }
					  else
					  {
					  	$sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					  }
					break;
					case '%':
					  if ($bEqual)
					  {
					  	$sScript .= "pmToInteger(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
					  }
					  else
					  {
					  	$sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					  }
					break;
					case '#':
					  if ($bEqual)
					  {
					  	$sScript .= "pmToFloat(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
					  }
					  else
					  {
					  	$sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					  }
					break;
					case '?':
					  if ($bEqual)
					  {
					  	$sScript .= "pmToUrl(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
					  }
					  else
					  {
					  	$sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
					  }
					break;
					case '$':
					  if ($bEqual)
					  {
					  	$sScript .= "pmSqlEscape(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
					  }
					  else
					  {
					  	$sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
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
//Available in the next versión
//End - Custom functions
?>