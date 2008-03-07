<?php
/**
 * methodsPermissions.php
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
  G::LoadClass('tree');
  global $G_HEADER;
  $G_HEADER->addScriptFile('/js/common/tree/tree.js');
  $G_HEADER->addScriptFile('/js/common/core/webResource.js');
  $tree = new Tree();

  reView(PATH_TRUNK,$tree);
  print( $tree->render() );

  function reView($path,&$tree)
  {
    $tree->name  = $path;
    $tree->value = $path . '&nbsp;'.
      setHeader('Set Header','setDirHeader("'.$path.'",this);'). 
      selectPermissions('Set Permission','setDirPermission("'.$path.'",this);'). 
      selectPermissions('Remove Permission','removeDirPermission("'.$path.'",this);');
    $tree->contracted = true;
    foreach(glob($path.'*',GLOB_MARK) as $file)
    {
      if (is_dir($file)) 
      {
        reView($file,$tree->addChild($file, $file ));
      }
      elseif (substr($file,-4,4)==='.php')
      {
        $nodeFile=&$tree->addChild
          (
            $file, $file . '&nbsp;'.
            selectPermissions('Set Permission','setPermission("'.$file.'",this);'). 
            selectPermissions('Remove Permission','removePermission("'.$file.'",this);')
          );
        $nodeFile->addChild("View Permissions",'<div style="cursor:hand;" onclick="loadFile(&quot;'.htmlentities($file,ENT_QUOTES,'UTF-8').'&quot;,this.nextSibling);">View Permissions</div><div id="divPerms['.htmlentities($file,ENT_QUOTES,'UTF-8').']"></div>');
        $nodeFile->addChild("Add Line",'<a href="#" onclick="addPermission(&quot;'.htmlentities($file,ENT_QUOTES,'UTF-8').'&quot;,this.nextSibling.nextSibling);return false;">Add Permission</a><br/><input style="width:100%;"/>');
        $nodeFile->contracted = true;
      }
    }
  }
  function selectPermissions($label,$onchange)
  {
    return '<select onchange="'.htmlentities($onchange,ENT_QUOTES,'UTF-8').'">'.
      '<option>'.$label.'</option>'.
      '<option value="PM_LOGIN">PM_LOGIN</option>'.
      '<option value="PM_USERS">PM_USERS</option>'.
      '<option value="PM_CASES">PM_CASES</option>'.
      '<option value="PM_FACTORY">PM_FACTORY</option>'.
      '<option value="PM_SETUP">PM_SETUP</option>'.
      '</select>';
  }
  function setHeader($label,$onchange)
  {
    return '<input type="button" onclick="'.htmlentities($onchange,ENT_QUOTES,'UTF-8').'" value="set header"/>';
  }
?>
<textarea id="headerText" cols="80" rows="25"></textarea>
<script>
  var headerText=document.getElementById("headerText");
  var phpFile = WebResource('methodsPermissions_Ajax');
  function loadFile(file,div)
  {
    div.innerHTML=phpFile.get_permissions(file);
  }
  function switchViewEdit(txt,inp)
  {
    showHideElement(txt);
    showHideElement(inp);
    inp.focus();
    var file=inp.name.split('?')[0];
    var row=parseInt(inp.name.split('?')[1]);
    //phpFile.modify_line(file,row,inp.value);
  }
  function switchEditView(txt,inp)
  {
    showHideElement(txt);
    showHideElement(inp);
    var file=inp.name.split('?')[0];
    var row=parseInt(inp.name.split('?')[1]);
    var res=phpFile.modify_line(file,row,inp.value);
    txt.innerHTML=res[0];
    inp.value=res[1];
  }
  function addPermission(file,inp)
  {
    var res=phpFile.add_permission(file,inp.value);
    document.getElementById('divPerms['+file+']').innerHTML=res;
  }
  function removeLine(btn)
  {
    var file=btn.name.split('?')[0];
    var row=parseInt(btn.name.split('?')[1]);
    var res=phpFile.remove_line(file,row);
    document.getElementById('divPerms['+file+']').innerHTML=res;
  }
  function setDirPermission(file,inp)
  {
    var res=phpFile.set_path_permission(file,inp.value);
    inp.selectedIndex=0;
  }
  function setDirHeader(file,inp)
  {
    var res=phpFile.set_path_header(file,headerText.value);
    inp.selectedIndex=0;
  }
  function removeDirPermission(file,inp)
  {
    var res=phpFile.remove_path_permission(file,headerText.value);
    inp.selectedIndex=0;
  }
  function setPermission(file,inp)
  {
    var res=phpFile.set_permission(file,inp.value);
    document.getElementById('divPerms['+file+']').innerHTML=res;
    inp.selectedIndex=0;
  }
  function removePermission(file,inp)
  {
    var res=phpFile.remove_permission(file,inp.value);
    document.getElementById('divPerms['+file+']').innerHTML=res;
    inp.selectedIndex=0;
  }
</script>