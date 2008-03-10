<?php
/**
 * Triggers.php
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

require_once 'classes/model/Content.php';
require_once 'classes/model/om/BaseTriggers.php';


/**
 * Skeleton subclass for representing a row from the 'TRIGGER' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class Triggers extends BaseTriggers {
  /**
   * This value goes in the content table
   * @var        string
   */
  protected $tri_title = '';
  /**
   * Get the tri_title column value.
   * @return     string
   */
  public function getTriTitle()
  {
    if ( $this->getTriUid() == "" ) {
      throw ( new Exception( "Error in getTriTitle, the getTriUid() can't be blank") );
    }
    $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
    $this->tri_title = Content::load ( 'TRI_TITLE', '', $this->getTriUid(), $lang );
    return $this->tri_title;
  }
  /**
   * Set the tri_title column value.
   * 
   * @param      string $v new value
   * @return     void
   */
  public function setTriTitle($v)
  {
    if ( $this->getTriUid() == "" ) {
      throw ( new Exception( "Error in setTriTitle, the getTriUid() can't be blank") );
    }
    $v=isset($v)?((string)$v):'';
    $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
    if ($this->tri_title !== $v || $v==="") {
      $this->tri_title = $v;
      $res = Content::addContent( 'TRI_TITLE', '', $this->getTriUid(), $lang, $this->tri_title );
      return $res;
    }
    return 0;
  }
  /**
   * This value goes in the content table
   * @var        string
   */
  protected $tri_description = '';
  /**
   * Get the tri_description column value.
   * @return     string
   */
  public function getTriDescription()
  {
    if ( $this->getTriUid() == "" ) {
      throw ( new Exception( "Error in getTriDescription, the getTriUid() can't be blank") );
    }
    $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
    $this->tri_description = Content::load ( 'TRI_DESCRIPTION', '', $this->getTriUid(), $lang );
    return $this->tri_description;
  }
  /**
   * Set the tri_description column value.
   * 
   * @param      string $v new value
   * @return     void
   */
  public function setTriDescription($v)
  {
    if ( $this->getTriUid() == "" ) {
      throw ( new Exception( "Error in setTriDescription, the getTriUid() can't be blank") );
    }
    $v=isset($v)?((string)$v):'';
    $lang = defined ( 'SYS_LANG') ? SYS_LANG : 'en';
    if ($this->tri_description !== $v || $v==="") {
      $this->tri_description = $v;
      $res = Content::addContent( 'TRI_DESCRIPTION', '', $this->getTriUid(), $lang, $this->tri_description );
      return $res;
    }
    return 0;
  }
  public function load($TriUid)
  {
  	try {
  	  $oRow = TriggersPeer::retrieveByPK( $TriUid );
  	  if (!is_null($oRow))
  	  {
  	    $aFields = $oRow->toArray(BasePeer::TYPE_FIELDNAME);
        $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME);
        $this->setNew(false);
  	    $this->setTriTitle($aFields['TRI_TITLE']=$this->getTriTitle());
        $this->setTriDescription($aFields['TRI_DESCRIPTION']=$this->getTriDescription());
  	    return $aFields;
      }
      else {
        throw( new Exception( "This row doesn't exists!" ));
      }
    }
    catch (Exception $oError) {
    	throw($oError);
    }
  }
  public function create($aData)
  {
    $con = Propel::getConnection(TriggersPeer::DATABASE_NAME);
    try
    {
      $con->begin();
      if ( !isset ( $aData['TRI_UID'] ) ) 
        $this->setTriUid(G::generateUniqueID());
      else
        $this->setTriUid($aData['TRI_UID'] );
        
      $this->setProUid($aData['PRO_UID']);
      $this->setTriType("SCRIPT");

      if ( !isset ( $aData['TRI_WEBBOT'] ) ) 
        $this->setTriWebbot("");
      else
        $this->setTriWebbot( $aData['TRI_WEBBOT'] );
        
      if($this->validate())
      {
        if ( !isset ( $aData['TRI_TITLE'] ) ) 
          $this->setTriTitle("");
        else
          $this->setTriTitle( $aData['TRI_TITLE'] );
          
        if ( !isset ( $aData['TRI_DESCRIPTION'] ) ) 
          $this->setTriDescription("");
        else
          $this->setTriDescription( $aData['TRI_DESCRIPTION'] );
        
        $result=$this->save();
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
  public function update($fields)
  {
    $con = Propel::getConnection(TriggersPeer::DATABASE_NAME);
    try
    {
      $con->begin();
      $this->load($fields['TRI_UID']);
      $this->fromArray($fields,BasePeer::TYPE_FIELDNAME);
      if($this->validate())
      {
        $contentResult=0;
        if (array_key_exists("TRI_TITLE", $fields)) $contentResult+=$this->setTriTitle($fields["TRI_TITLE"]);
        if (array_key_exists("TRI_DESCRIPTION", $fields)) $contentResult+=$this->setTriDescription($fields["TRI_DESCRIPTION"]);
        $result=$this->save();
        $result=($result==0)?($contentResult>0?1:0):$result;
        $con->commit();
        return $result;
      }
      else
      {
        $con->rollback();
        $validationE=new Exception("Failed Validation in class ".get_class($this).".");
        $validationE->aValidationFailures = $this->getValidationFailures();
        throw($validationE);
      }
    }
    catch(Exception $e)
    {
      $con->rollback();
      throw($e);
    }
  }
  public function remove($TriUid)
  {
    $con = Propel::getConnection(TriggersPeer::DATABASE_NAME);
    try
    {
      $con->begin();  
      $this->setTriUid($TriUid);
      Content::removeContent( 'TRI_TITLE', '', $this->getTriUid());
      Content::removeContent( 'TRI_DESCRIPTION', '', $this->getTriUid());
      $result=$this->delete();
      $con->commit();
      return $result;
    }
    catch(Exception $e)
    {
      $con->rollback();
      throw($e);
    }
  }
  
	/**
	 * verify if Trigger row specified in [sUid] exists.
	 *
	 * @param      string $sUid   the uid of the Prolication
	 */

  function TriggerExists ( $sUid ) {
  	$con = Propel::getConnection(TriggersPeer::DATABASE_NAME);
    try {
      $oObj = TriggersPeer::retrieveByPk( $sUid );
  	  if ( get_class ($oObj) == 'Triggers' ) {
  	    return true;
  	  }
      else {
        return false;
      }
    }
    catch (Exception $oError) {
    	throw($oError);
    }
  }  
} // Trigger

?>