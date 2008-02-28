<?php
/*
 * Created on 20/12/2007
 *
 * Test code.
 */
/*
require_once("classes/model/Configuration.php");
$className='Configuration';
$classKey='CfgUid, $ObjUid, $ProUid, $UsrUid, $AppUid';
$content=array
();
/*
require_once("classes/model/Step.php");
$className='Step';
$classKey='StepUid';
$content=array
();
*/

require_once("classes/model/StepTrigger.php");
$className='StepTrigger';
$classKey='StepUid, $TasUid, $TriUid, $StType';
$content=array
();

/*
require_once("classes/model/Task.php");
$className='Task';
$classKey='TasUid';
$content=array
(
  array
  (
    'field'=>'TAS_TITLE',
    'camel'=>'TasTitle',
    'key'=>$classKey
  ),
  array
  (
    'field'=>'TAS_DESCRIPTION',
    'camel'=>'TasDescription',
    'key'=>$classKey
  ),
  array
  (
    'field'=>'TAS_DEF_TITLE',
    'camel'=>'TasDefTitle',
    'key'=>$classKey
  ),
  array
  (
    'field'=>'TAS_DEF_DESCRIPTION',
    'camel'=>'TasDefDescription',
    'key'=>$classKey
  ),
  array
  (
    'field'=>'TAS_DEF_PROC_CODE',
    'camel'=>'TasDefProcCode',
    'key'=>$classKey
  ),
  array
  (
    'field'=>'TAS_DEF_MESSAGE',
    'camel'=>'TasDefMessage',
    'key'=>$classKey
  ),
);

/*
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
*/
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
      return $res;
    }
    return 0;
  }
';
print(nl2br(str_replace(' ','&nbsp;',$prop)));
}
//print nl2br(print_r(get_class_methods('trigger'),1));
$mets=get_class_methods($className);
$tab='&nbsp;&nbsp;';
print($tab.'function create($aData)<br/>');
print($tab.'{<br/>');
print($tab.$tab.'$con = Propel::getConnection('.$className.'Peer::DATABASE_NAME);<br/>');
print($tab.$tab.'try<br/>');
print($tab.$tab.'{<br/>');
print($tab.$tab.$tab.'$con->begin();<br/>');
foreach($mets as $met)
{
  if ($met=='hydrate')break;
  if (substr($met,0,3)=='set' && !isContent($met))
  {
    $default=(substr($met,3)==$classKey)?'G::generateUniqueID()':'""';
    print($tab.$tab.$tab.'$this->'.$met.'('.$default.');<br/>');
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
  $updateContentFields.=$tab.$tab.$tab.$tab.'if (array_key_exists("'.$r['field'].'", $fields)) $contentResult+=$this->set'.$r['camel'].'($fields["'.$r['field'].'"]);'."\n";
  $removeContentFields.=$tab.$tab.$tab.'Content::removeContent("'.$r['field'].'", "", $this->get'.$classKey.'());'."\n";
  $loadContentFields.=$tab.$tab.$tab.$tab.'$this->set'.$r['camel'].'($aFields["'.$r['field'].'"]=$this->get'.$r['camel'].'());'."\n";
}
print($tab.$tab.$tab.$tab.'$result=$this->save();<br/>');
print($tab.$tab.$tab.$tab.'$con->commit();<br/>');
print($tab.$tab.$tab.$tab.'return $result;<br/>');
print($tab.$tab.$tab.'}<br/>');
print($tab.$tab.$tab.'else<br/>');
print($tab.$tab.$tab.'{<br/>');
print($tab.$tab.$tab.$tab.'$con->rollback();<br/>');
print($tab.$tab.$tab.$tab.'throw(new Exception("Failed Validation in class ".get_class($this)."."));<br/>');
print($tab.$tab.$tab.'}<br/>');
print($tab.$tab.'}<br/>');
print($tab.$tab.'catch(Exception $e)<br/>');
print($tab.$tab.'{<br/>');
print($tab.$tab.$tab.'$con->rollback();<br/>');
print($tab.$tab.$tab.'throw($e);<br/>');
print($tab.$tab.'}<br/>');
print($tab.'}<br/>');

$loadCode='  public function load($'.$classKey.')
  {
    try {
      $oRow = '.$className.'Peer::retrieveByPK( $'.$classKey.' );
      if (!is_null($oRow))
      {
        $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
        $this->fromArray($aFields,BasePeer::TYPE_FIELDNAME);
        $this->setNew(false);
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
    $con = Propel::getConnection('.$className.'Peer::DATABASE_NAME);
    try
    {
      $con->begin();
      $this->load($fields);
      $this->fromArray($fields,BasePeer::TYPE_FIELDNAME);
      if($this->validate())
      {
		$contentResult=0;
'.$updateContentFields.'        $result=$this->save();
        $result=($result==0)?($contentResult>0?1:0):$result;
        $con->commit();
        return $result;
      }
      else
      {
        $con->rollback();
        throw(new Exception("Failed Validation in class ".get_class($this)."."));
      }
    }
    catch(Exception $e)
    {
      $con->rollback();
      throw($e);
    }
  }
';
print(nl2br(str_replace(' ','&nbsp;',$updateCode)));

$removeCode=
'  function remove($'.$classKey.')
  {
    $con = Propel::getConnection('.$className.'Peer::DATABASE_NAME);
    try
    {
      $con->begin();
'.splitKey($classKey,'      \$this->set{$key}(\${$key});
').$removeContentFields.
'      $result=$this->delete();
      $con->commit();
      return $result;
    }
    catch(Exception $e)
    {
      $con->rollback();
      throw($e);
    }
  }
';
print(nl2br(str_replace(' ','&nbsp;',$removeCode)));

function splitKey($keys,$template)
{
  $aKeys=explode(',',$keys);
  $result='';
  foreach($aKeys as $key)
  {
    $key=trim($key);
    while (substr($key,0,1)==='$') $key=substr($key,1);
    $result.=eval('return "'.$template.'";');
  }
  return $result;
}
function isContent($name)
{
  global $content;
  foreach($content as $r)
  {
    if ('set'.$r['camel']==$name) return true;
  }
  return false;
}


?>