<?php
/**
 * class.organizationalChart.php
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

class XmlForm_Field_OrganizationalChart extends Xmlform_Field
{
  var $borderStyle = '1px solid black';
  var $fontStyle   = '10px';
  var $style       = 'overflow:scroll;';
  //TODO: es isset no false
  var $withoutLabel = true;
  var $contextmenu  ='';
  function attachEvents() {
    $script = <<<ASCRIPT
      var element = document.getElementById("{$this->name}");
      var divs = element.getElementsByTagName("DIV");
      for(var r=0; r< divs.length ; r++ ){
        if ({$this->contextmenu}) {$this->contextmenu}(divs[r]);
      }
ASCRIPT;
    eval('$script="'.addcslashes( $script , "\\\"" ).'";');
    return $script;
  }
  function renderGrid( $nodes ){
    return $this->printArray( $this->printNodeToArray( $nodes ) ); 
  }
  function printArray( $array ) {
    $html = '<div id="'.$this->name.'" style="' . $this->style .
    '"><table border="0px" cellpadding="0" cellspacing="0">';
    ksort( $array );
    $firstRow = true;$countDown = sizeof($array);
    foreach( $array as $row => $v ) {
      $countDown--;
      $lastRow = $countDown===0;
      ksort( $v );
      $vlines = '<tr>';
      $vlines2 = '<tr>';
      $boxes = '<tr>';
      $position = 0;
      $printInterSpaceLine = false;
      foreach( $v as $col => $aCell ) {
        $boxes .= $this->printSpace( $col - $position );
        $cell = $aCell['text'];
        //Prints the node box
        $boxes .= '<td align="center" valign="top" colspan="'.$aCell['cols'].
          '" style="height:100%;"><div id="'.$this->name.'Node['.$aCell['id'].']'.
          '" style="cursor:default;height:100%;'.
          ';padding:0px;margin:0px 10px;font:'.$this->fontStyle.
          ';"><table style="border:'.$this->borderStyle.';height:100%;"><tr><td>'.
          $cell . '</td></tr></table></div></td>';
        if ($printInterSpaceLine) {
          $vlines .= $this->printHline( $col - $position );
        } else {
          $vlines .= $this->printSpace( $col - $position );
        }
        $vlines2 .= $this->printSpace( $col - $position );
        if ($aCell['isTerminal']) {
          $vlines2 .= '<td colspan="'.$aCell['cols'].'" style="border:0px"/>';
        } else {
          $vlines2 .= '<td align="right" colspan="'.$aCell['cols'].'">'. '<span style="display:block;border-left:2px solid black;width:50%;">&nbsp;</span>' . '</td>';
        }
        switch ($aCell['position']) {
          case 'left':
            $vlines .= '<td align="right" colspan="'.$aCell['cols'].'">'. '<span style="display:block;border-left:2px solid black;border-top:2px solid black;width:50%;">&nbsp;</span>' . '</td>';
            $printInterSpaceLine = true;
            break;
          case 'middle':
            $vlines .= '<td align="right" colspan="'.$aCell['cols'].'">'. '<span style="display:block;border-top:2px solid black;width:100%;"><span style="display:block;border-left:2px solid black;width:50%;">&nbsp;</span></span>' . '</td>';
            break;
          case 'unique':
            $vlines .= '<td align="right" colspan="'.$aCell['cols'].'">'. '<span style="display:block;border-left:2px solid black;width:50%;">&nbsp;</span>' . '</td>';
            break;
          case 'right':
            $vlines .= '<td align="left" colspan="'.$aCell['cols'].'">'. '<span style="display:block;border-right:2px solid black;border-top:2px solid black;width:50%;">&nbsp;</span>' . '</td>';
            $printInterSpaceLine = false;
            break;
        }
        $position = $col + $aCell['cols'];
      }
      $boxes .= '</tr>';
      $vlines .= '</tr>';
      $vlines2 .= '</tr>';
      if (!$firstRow) $html .= $vlines; 
      $html .= $boxes;
      //if (!$lastRow) 
      $html .= $vlines2;
      $firstRow = false;
    }
    $html .= '</table></div>';
    return $html;
  }
  function printNodeToArray( $node, $array = array() , $row = 0, $offset = 0 ) {
    if (is_array( $node['children'] )) {
      if (!isset($array[$row])) $array[$row] = array();
      if (!is_array($array[$row])) $array[$row] = array();
      $isFirst = true; $countDown = sizeof( $node['children'] );
      $isUnique = ($countDown===1);
      foreach($node['children'] as $r => $subnode ) {
        $v = $subnode['value'];
        $cols = $this->getCols( $node['children'][$r]['children'] );
        $col  = $offset;
        $countDown--;
        $text = $v;
        $isTerminal = sizeof($subnode['children'])===0;
        $isLast = ($countDown===0);
        $position = $isUnique ? 'unique' : ($isFirst ? 'left' : ( $isLast ? 'right': 'middle' ));
        $array[$row][$col] = array ( 'position'=> $position , 'id'=> $subnode['name'], 'text' => $text , 'isTerminal' => $isTerminal , 'cols' => $cols , 'isUnique' => $isUnique );
        $array = $this->printNodeToArray( $node['children'][$r], $array, $row+1, $offset );
        $offset += $cols;
        $isFirst = false;
      }
      return $array;
    } else {
      return $array;
    }
  }
  function getCols( $node ) {
    if (is_array( $node )) {
      $cols = 0;
      foreach($node as $r => $v ) {
        $cols += isset($node[$r]['children'])? 
          $this->getCols( $node[$r]['children'] ) :
          2;
      }
      return ($cols<=0)? 2 : $cols;
    } else {
      return 2;
    }
  }
  function printSpace( $cols ) {
    if ($cols>0) {
      return '<td colspan="'.$cols.'"/>';
    } else {
      return '';
    }
  }
  function printHLine( $cols ) {
    if ($cols>0) {
      return '<td colspan="'.$cols.'"><span style="border-top:2px solid black;width:100%;">&nbsp;</span></td>';
    } else {
      return '';
    }
  }
}

?>