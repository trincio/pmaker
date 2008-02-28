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
require_once("classes/model/Trigger.php");
$className='Trigger';
$classKey='TriUid';
$content=array
(
  array
  (
    'field'=>'TRI_TITLE',
    'camel'=>'TriTitle',
    'key'=>$classKey
  ),
  array
  (
    'field'=>'TRI_DESCRIPTION',
    'camel'=>'TriDescription',
    'key'=>$classKey
  )
);
///////////////////// START TEMPLATE ///////////////////////////////////////////
foreach($content as $r)
{
$field=$r['field'];
$plain=strtolower($field);
$camel=$r['camel'];
$key=$r['key'];
$prop='  /**
   * This value goes in the content table
   * @var        string
   */
  protected $'.$plain.' = \'\';
  /**
   * Get the '.$plain.' column value.
   * @return     string
   */
  public function get'.$camel.'()
  {
	  if ( $this->get'.$key.'() == "" ) {
      throw ( new Exception( "Error in get'.$camel.', the get'.$key.'() can\'t be blank") );
	  }
    $lang = defined ( \'SYS_LANG\') ? SYS_LANG : \'en\';
    $this->'.$plain.' = Content::load ( \''.$field.'\', \'\', $this->get'.$key.'(), $lang );
    return $this->'.$plain.';
  }
  /**
   * Set the '.$plain.' column value.
   * 
   * @param      string $v new value
   * @return     void
   */
  public function set'.$camel.'($v)
  {
	  if ( $this->get'.$key.'() == "" ) {
      throw ( new Exception( "Error in set'.$camel.', the get'.$key.'() can\'t be blank") );
	  }
    $v=isset($v)?((string)$v):\'\';
    $lang = defined ( \'SYS_LANG\') ? SYS_LANG : \'en\';
    if ($this->'.$plain.' !== $v || $v==="") {
      $this->'.$plain.' = $v;
      $res = Content::addContent( \''.$field.'\', \'\', $this->get'.$key.'(), $lang, $this->'.$plain.' );
    }
  }
';
print(nl2br(str_replace(' ','&nbsp;',$prop)));
}
//print nl2br(print_r(get_class_methods('trigger'),1));
$mets=get_class_methods($className);
$tab='&nbsp;&nbsp;';
print($tab.'function create($aData)<br/>');
print($tab.'{<br/>');
print($tab.$tab.'$con = Propel::getConnection(ApplicationPeer::DATABASE_NAME);<br/>');
print($tab.$tab.'try<br/>');
print($tab.$tab.'{<br/>');
print($tab.$tab.$tab.'$con->begin();<br/>');
foreach($mets as $met)
{
  if ($met=='hydrate')break;
  if (substr($met,0,3)=='set' && !isContent($met))
  {
    $default=(substr($met,3)==$classKey)?'G::generateUniqueID()':'""';
    print($tab.$tab.$tab.'$this->set'.$met.'('.$default.');<br/>');
  }
}
print($tab.$tab.$tab.'if($this->validate())<br/>');
print($tab.$tab.$tab.'{<br/>');
$updateContentFields='';
$removeContentFields='';
$loadContentFields='';
foreach($content as $r)
{
  print($tab.$tab.$tab.$tab.'$this->set'.$r['camel'].'("");<br/>');
  $updateContentFields.=$tab.$tab.$tab.$tab.'if (array_key_exists("'.$r['field'].'", $fields)) $this->set'.$r['camel'].'($fields["'.$r['field'].'"]);'."\n";
  $removeContentFields.=$tab.$tab.$tab.'Content::removeContent("'.$r['field'].'", "", $this->get'.$classKey.'());'."\n";
  $loadContentFields.=$tab.$tab.$tab.'$aFields["'.$r['field'].'"] = $this->get'.$r['camel'].'();'."\n";
}
print($tab.$tab.$tab.$tab.'$this->save();<br/>');
print($tab.$tab.$tab.'}<br/>');
print($tab.$tab.$tab.'else<br/>');
print($tab.$tab.$tab.'{<br/>');
print($tab.$tab.$tab.$tab.'trow(new Exception("Failed Validation in class ".get_class($this)."."));<br/>');
print($tab.$tab.$tab.'}<br/>');
print($tab.$tab.$tab.'$con->commit();<br/>');
print($tab.$tab.'}<br/>');
print($tab.$tab.'catch(Exception $e)<br/>');
print($tab.$tab.'{<br/>');
print($tab.$tab.$tab.'$con->rollback();<br/>');
print($tab.$tab.$tab.'trow($e);<br/>');
print($tab.$tab.'}<br/>');
print($tab.'}<br/>');

$loadCode='  public function load($'.$classKey.')
  {
    try {
      $oRow = '.$className.'Peer::retrieveByPK( $'.$classKey.' );
      if (!is_null($oRow))
      {
        $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
        $this->fromArray($aFields);
'.$loadContentFields.'        return $aFields;
      }
      else {
        throw( new Exception( "This row doesn\'t exists!" ));
      }
    }
    catch (Exception $oError) {
      throw($oError);
    }
  }
';
print(nl2br(str_replace(' ','&nbsp;',$loadCode)));
$updateCode=
'  function update($fields)
  {
    $con = Propel::getConnection(ApplicationPeer::DATABASE_NAME);
    try
    {
      $con->begin();
      $this->load($fields);
      $this->fromArray($fields,BasePeer::TYPE_FIELDNAME);
      if($this->validate())
      {
'.$updateContentFields.'        $this->save();
      }
      else
      {
        trow(new Exception("Failed Validation in class ".get_class($this)."."));
      }
      $con->commit();
    }
    catch(Exception $e)
    {
      $con->rollback();
      trow($e);
    }
  }
';
print(nl2br(str_replace(' ','&nbsp;',$updateCode)));

$removeCode=
'  function remove($fields)
  {
    $con = Propel::getConnection(ApplicationPeer::DATABASE_NAME);
    try
    {
      $con->begin();
      $this->fromArray($fields,BasePeer::TYPE_FIELDNAME);
'.$updateContentFields.
'      $this->delete();
      $con->commit();
    }
    catch(Exception $e)
    {
      $con->rollback();
      trow($e);
    }
  }
';
print(nl2br(str_replace(' ','&nbsp;',$removeCode)));


function isContent($name)
{
  global $content;
  foreach($content as $r)
  {
    if ('set'.$r['camel']==$name) return true;
  }
  return false;
}






//////////////////////////////////////////////
die;

$dbc=new DBConnection( "192.168.0.10", "fluid", "fluid2000", "report_pacena" , 'mysql');
$ses=new DBSession($dbc);
$res=$ses->execute('SELECT *
FROM `Sheet 1`
LEFT JOIN `Database` ON ( `Database`.MENU_NOMBRE = `Sheet 1`.MEN ) ');
ob_start();
$title=true;
while($row=$res->read())
{
  if ($title)
  {
    $first=true;
    foreach($row as $col => $val)
    {
      if (!$first) print',';
      print '"'.$col.'"';
      $first=false;
    }
    print "\n";
  }
  $first=true;
  foreach($row as $col)
  {
    if (!$first) print',';
    print '"'.$col.'"';
    $first=false;
  }
  print "\n";
$title=false;
}
$txt=ob_get_contents();ob_end_clean();
$fp=fopen(PATH_HTML . 'pacena.txt','w');
fwrite($fp,$txt);
fclose($fp);

return ;

G::LoadClass( "application" );
G::LoadClass( "appDelegation" );
//G::LoadClass( "task" );
require_once ( "model/Task.php" );
require_once ( "model/Application.php" );
require_once ( "model/AppDelegation.php" );
G::LoadClass( "plugin" );

die;
if (($RBAC_Response=$RBAC->userCanAccess("PM_CASES"))!=1) return $RBAC_Response;
G::LoadClass('derivation');
G::LoadClass('task');


$dbc=new DBConnection();
$ses=new DBSession($dbc);
$res=$ses->execute('SELECT D.*,R.* FROM APP_DELEGATION D RIGHT JOIN ROUTE R ON (R.TAS_UID=D.TAS_UID)');
$row=$res->Read();

$der=new Derivation($dbc);
global $tmpTask;
$tmpTask=new Task($dbc);
/*DERIVACION SIMPLE*/
/*
var_dump($der->isOpen('84718B68EC2996','94717B8EE4F322'));
$currentDerivation=array(
  'APP_UID'=>'84718B68EC2996',
  'DEL_INDEX'=>'1',
  'APP_STATUS'=>'8-)'
  );
$nextTasks=array(
    array(
    'TAS_UID'=>'44717B946AB7B9',
    'USR_UID'=>'446F8411F383A7',
    'TAS_DERIVATION'=>'NORMAL',
    'DEL_PRIORITY'=>'1',
    ),
  );
$der->derivate($currentDerivation,$nextTasks);
var_dump($der->isOpen('84718B68EC2996','94717B8EE4F322'));
*/

/************************************************************/
/**                                                        **/
/**                SIMULACION DE UN PROCESO                **/
/**                                                        **/
/************************************************************/
///////// INITIAL VALUES /////////
//Especifique la applicacion (APP_UID) que desea ejecutar
$APP_UID='2471935ADCA64E';
if (isset($_GET['APP_UID'])) $APP_UID=$_GET['APP_UID'];
$APP_STATUS='TO_DO';
//////////////////////
$TAS_DERIVATION='NORMAL';
$DEL_PRIORITY='1';
//////////////////////////////////////////////////////////////
$time_to_live=10;
for($r=0;$r<$time_to_live;$r++)
{
  /****  GETS ALL THE OPEN THREADS  ****/
  $sql="SELECT TAS_UID,USR_UID,DEL_INDEX FROM APP_DELEGATION WHERE APP_UID='$APP_UID' AND DEL_THREAD_STATUS='OPEN'";
  $res=$ses->Execute($sql);
  $threads=array();
  while ($thread=$res->Read())
  {
    $threads[]=$thread;
  }
  ECHO('<BR/><BR/><U><B>CICLO: </B></U>');var_dump($r+1);
  /****  RUNS ALL THE THREADS  ****/
  foreach($threads as $thread)
  {
      /*CURRENT DELEGATION*/
      $TAS_UID=$thread['TAS_UID'];
      $USR_UID=$thread['USR_UID'];
      $DEL_INDEX=$thread['DEL_INDEX'];
      
      $currentDerivation=array(
        'APP_UID'=>$APP_UID,
        'DEL_INDEX'=>$DEL_INDEX,
        'APP_STATUS'=>$APP_STATUS
        );
      
      /*NEXT DELEGATIONS*/
      $sql="SELECT ROU_NEXT_TASK FROM ROUTE WHERE TAS_UID='$TAS_UID'";
      $res=$ses->Execute($sql);
      $nextTasks=array();
      $sNextTasks='';
      while($ROU_NEXT_TASK=$res->Read())
      {
        $nextTask=array(
          'TAS_UID'=>$ROU_NEXT_TASK['ROU_NEXT_TASK'],
          'USR_UID'=>$USR_UID,
          'TAS_DERIVATION'=>$TAS_DERIVATION,
          'DEL_PRIORITY'=>$DEL_PRIORITY,
          );
        $tmpTask->load($nextTask['TAS_UID']);
        $sNextTasks.=($sNextTasks==='')?'':',';
        $sNextTasks.=$tmpTask->Fields['TAS_TITLE'];
        $nextTasks[]=$nextTask;
      }
      /*EXECUTE DERIVATION*/
      $tmpTask->load($TAS_UID);
      $tasTitle=$tmpTask->Fields['TAS_TITLE'];
      ECHO('<BR/><B>ANTES: </B>');var_dump("(".getCurrThreads($APP_UID)."):".$tasTitle."->($sNextTasks)",$der->isOpen($APP_UID,$TAS_UID));
      $der->derivate($currentDerivation,$nextTasks);
      ECHO('<BR/><B>DESPUES: </B>');var_dump('('.getCurrThreads($APP_UID).')',$der->isOpen('84718B68EC2996','94717B8EE4F322'));
  }
  if (count($threads)==0) break;
}
function getCurrThreads($APP_UID)
{
  global $tmpTask;
  $dbc=new DBConnection();
  $ses=new DBSession($dbc);
  /****  GETS ALL THE OPEN THREADS  ****/
  $sql="SELECT TAS_UID,USR_UID,DEL_INDEX FROM APP_DELEGATION WHERE APP_UID='$APP_UID' AND DEL_THREAD_STATUS='OPEN'";
  $res=$ses->Execute($sql);
  $threads='';
  while ($thread=$res->Read())
  {
    $threads.=($threads==='')?'':',';
    $tmpTask->load($thread['TAS_UID']);
    $threads.=$tmpTask->Fields['TAS_TITLE'];
  }
  return $threads;
}
?>