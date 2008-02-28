<?php
/*
 * Created on 11-02-2008
 *
 * @author David Callizaya <davidsantos@colosa.com>
 */
G::LoadSystem("webResource");
class dvServices extends WebResource
{
  function get_session_vars()
  {
    $cur=array_keys($_SESSION);
    $res='';
    foreach($cur as $key)
    {
      $res.='* '.$key.'<br/>';
    }
    return $res;
  }
  function get_session_xmlforms()
  {
    $cur=array_keys($_SESSION);
    $res='';
    $colors=array('white','#EEFFFF');
    $colori=0;$count=0;
    //Get xmlforms in session
    foreach($cur as $key)
    {
      $res.='<div style="background-color:'.$colors[$colori].';">';
        $xml=G::getUIDName($key,'');
        if (strpos($xml,'.xml')!==false)
        {
          $res.='<i>FORM:</i>  '.$xml;
          $colori=$colori ^ 1;
          $count++;
        }
      $res.='</div>';
    }
    //Get pagedTable in session
    foreach($cur as $key)
    {
      $res.='<div style="background-color:'.$colors[$colori].';">';
      if (substr($key,0,11)==="pagedTable[")
      {
        $xml=G::getUIDName(substr($key,11,-1),'');
        $res.='<i>TABLE:</i> '.$xml;
        $colori=$colori ^ 1;
        $count++;
      }
      $res.='</div>';
    }
    return array("count"=>$count,"html"=>$res);
  }
}
$o=new dvServices($_SERVER['REQUEST_URI'],$_POST);
//av.buenos aires maxparedes
//tienda viva.
//122

?>