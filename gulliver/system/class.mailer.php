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
G::LoadThirdParty('phpmailer','class.phpmailer');
/* Send emails using the class "PHPMailer"
 *  Email server configuration constants:
 *    MAIL_MAILER     mail/smtp
 *    MAIL_HOST       email.server.address
 *    MAIL_SMTPAUTH   true/false
 *    MAIL_USERNAME   Email Username (smtp)
 *    MAIL_PASSWORD   Email Password (smtp)
 *    MAIL_TIMEOUT    Email Timeout  (smtp)
 *    MAIL_CHARSET    Email Charset  "utf-8"
 *    MAIL_ENCODING   Email Encoding "base64"
 *  Other required configuration constants:
 *    PATH_HTMLMAIL   Email templates path
 * @author David Callizaya <davidsantos@colosa.com>
 */

class mailer
{
  function instanceMailer()
  {
    $mailer = new PHPMailer;
    $mailer->PluginDir=PATH_THIRDPARTY.'phpmailer/';
    //DEFAULT CONFIGURATION
    $mailer->Mailer='mail';
    $mailer->Host = "";
    $mailer->SMTPAuth = false;
    $mailer->Username = ""; 
    $mailer->Password = "";
    $mailer->Timeout=30;
    $mailer->CharSet ='utf-8';
    $mailer->Encoding='base64';
    if (defined('MAIL_MAILER'))   $mailer->Mailer = MAIL_MAILER;
    if (defined('MAIL_HOST'))     $mailer->Host = MAIL_HOST;          
    if (defined('MAIL_SMTPAUTH')) $mailer->SMTPAuth = MAIL_SMTPAUTH;   
    if (defined('MAIL_USERNAME')) $mailer->Username = MAIL_USERNAME;      
    if (defined('MAIL_PASSWORD')) $mailer->Password = MAIL_PASSWORD;      
    if (defined('MAIL_TIMEOUT'))  $mailer->Timeout = MAIL_TIMEOUT;         
    if (defined('MAIL_CHARSET'))  $mailer->CharSet = MAIL_CHARSET;   
    if (defined('MAIL_ENCODING')) $mailer->Encoding= MAIL_ENCODING;  
    return $mailer;
  }
  /* ARPA INTERNET TEXT MESSAGES
   * Returns an array with the "name" and "email" of an ARPA type 
   * email $address.
   * @author David Callizaya
   */
  function arpaEMAIL($address)
  {
    $arpa=array();
    preg_match("/([^<>]*)(?:\<([^<>]*)\>)?/",$address,$matches);
    $isEmail=preg_match("/\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i",$matches[1]);
      $arpa['email'] = ((isset($matches[2]))&&($matches[2]!=''))?$matches[2]:(($isEmail)?$matches[1]:'');
      $arpa['name'] = ($isEmail)?'':$matches[1];
      return $arpa;
  }
  function sendTemplate( $from = "", $target = "", $cc = "", $bcc="" ,$subject = "", $Fields = array(), $templateFile = "empty.html", $attachs=array(), $plainText=false, $returnContent = false )
  {
    //Read the content of the TemplateFile
    $fp=fopen(PATH_HTMLMAIL . $templateFile,"r");
    $content=fread($fp,filesize(PATH_HTMLMAIL . $templateFile));
    fclose($fp);
    //Replace the @@Fields with the $Fields array.
    $content=mailer::replaceFields($Fields,$content);
    //Compatibility  with class.Application
    if ($attachs==='FALSE') return $content;
    //Create the alternative body (text only)
    //$h2t =& new html2text($content);
    $text = '';//$h2t->get_text();
    //Prepate el phpmailer
    $mailer=mailer::instanceMailer();
      $arpa = mailer::arpaEMAIL($from);
      $mailer->From = $arpa['email']==''?$mailer->defaultEMail:$arpa['email'];
      $mailer->FromName = $arpa['name'];
      $arpa = mailer::arpaEMAIL($target);
    $mailer->AddAddress($arpa['email'],$arpa['name']);
    $mailer->AddCC($cc);
    $mailer->AddBCC($bcc);
      $mailer->Subject = $subject;
    if ($plainText) $content = $text;
    if ($content==='') $content='empty';
    $mailer->Body = $content;
    //$mailer->AltBody = $text;
    $mailer->isHTML(!$plainText);
    //Attach the required files
    if (is_array($attachs))
      if (sizeof($attachs)>0)
        foreach($attachs as $aFile)
          $mailer->AddAttachment($aFile,basename($aFile));
    //Send the e-mail.
    for($r=1;$r<=4;$r++)
    {
      $result=$mailer->Send();
      if ($result) break;
    }
    //unset($h2t);
    if ($result && $returnContent) return $content;
    return $result;
  }
  function sendHtml( $from = "", $target = "", $cc = "", $bcc="" ,$subject = "", $Fields = array(), $content = "", $attachs=array(), $plainText=false, $returnContent = false )
  {
    //Replace the @@Fields with the $Fields array.
    $content=mailer::replaceFields($Fields,$content);
    //Create the alternative body (text only)
    //$h2t =& new html2text($content);
    $text = '';//$h2t->get_text();
    //Prepate el phpmailer
    $mailer=mailer::instanceMailer();
      $arpa = mailer::arpaEMAIL($from);
      $mailer->From = $arpa['email']==''?$mailer->defaultEMail:$arpa['email'];
      $mailer->FromName = $arpa['name'];
      $arpa = mailer::arpaEMAIL($target);
    $mailer->AddAddress($arpa['email'],$arpa['name']);
    $mailer->AddCC($cc);
    $mailer->AddBCC($bcc);
    $mailer->Subject = $subject;
    if ($plainText) $content = $text;
    if ($content==='') $content='empty';
    $mailer->Body = $content;
    //$mailer->AltBody = $text;
    $mailer->isHTML(!$plainText);
    //Attach the required files
    if (is_array($attachs))
      if (sizeof($attachs)>0)
        foreach($attachs as $aFile)
          $mailer->AddAttachment($aFile,basename($aFile));
    //Send the e-mail.
    for($r=1;$r<=4;$r++)
    {
      $result=$mailer->Send();
      if ($result) break;
    }
    //unset($h2t);
    if ($result && $returnContent) return $content;
    return $result;
  }
  function sendText( $from = "", $target = "", $cc = "", $bcc="" ,$subject = "", $content = "", $attachs=array())
  {
    //Prepate el phpmailer
    $mailer=mailer::instanceMailer();
      $arpa = mailer::arpaEMAIL($from);
      $mailer->From = $arpa['email']==''?$mailer->defaultEMail:$arpa['email'];
      $mailer->FromName = $arpa['name'];
      $arpa = mailer::arpaEMAIL($target);
    $mailer->AddAddress($arpa['email'],$arpa['name']);
    $mailer->AddCC($cc);
    $mailer->AddBCC($bcc);
    $mailer->Subject = $subject;
    if ($content==='') $content='empty';
    $mailer->Body = $content;
    $mailer->AltBody = $content;
    $mailer->isHTML(false);
    //Attach the required files
    if (sizeof($attachs)>0)
      foreach($attachs as $aFile)
        $mailer->AddAttachment($aFile,basename($aFile));
    //Send the e-mail.
    for($r=1;$r<=4;$r++)
    {
      $result=$mailer->Send();
      if ($result) break;
    }
    return $result;
  }

  function replaceFields($Fields = array(), $content = "")
  {
    return G::replaceDataField( $content , $Fields );
  }
  function html2text()
  {
    //$h2t =& new html2text($content);
    //return $h2t->get_text();
  }
}
?>