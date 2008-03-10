<?php
/**
 * class.authentication.php
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

require_once('Net/LDAP.php');

class authenticationSource  extends DBTable
{
  var $vRow = array();

 function SetTo( $objConnection )
 {
  parent::SetTo( $objConnection, "AUTHENTICATION_SOURCES", array("AUT_UID"));
 }

 function log ( $text ) {
 	//comment out if you want to save the security log
/*
  $f =fopen ( '/shared/security.log', "a+" );
  fwrite ( $f, date("Y-m-d h:i:s") . " $text \n" );
  fclose ($f);
*/

  $this->vlog[] = date('H:i:s ') . $text;
 }
 
  //to create the tables and fields needed for LDAP
  //missing error validation... :(
  function verifyStructures( )  {
    //get mysql version
    $sql = "SHOW VARIABLES LIKE 'VERSION'";
    $dset = $this->_dbses->Execute( $sql );
    $row = $dset->Read();
    ereg("([0-9.]*)", $row['Value'], $regs);
    $version = $regs[0];

    //verify if exists table AUTHENTICATION_SOURCES
    $sql = "SHOW TABLES like 'AUTHENTICATION_SOURCES' ";
    $dset = $this->_dbses->Execute( $sql );
    $row = $dset->Read();


    //if not exists create the table
    if ( ! is_array ( $row) )  {
      $sql = "CREATE TABLE `AUTHENTICATION_SOURCES` (
           `AUT_UID` int(11) NOT NULL auto_increment,
           `AUT_NAME` varchar(50) NOT NULL default '',
           `AUT_NAMESPACE` varchar(255) NOT NULL default '',
           `AUT_PROVIDER` varchar(20) NOT NULL default 'Active Directory',
           `AUT_SERVER_NAME` varchar(50) NOT NULL default '',
           `AUT_PORT` int(11) NOT NULL default '389',
           `AUT_ENABLED_TLS` int(11) NOT NULL default '0',
           `AUT_BASE_DN` varchar(96) NOT NULL default '',
           `AUT_SEARCH_USER` varchar(96) NOT NULL default '',
           `AUT_SEARCH_PASSWORD` varchar(32) NOT NULL default '',
           `AUT_SEARCH_ATTRIBUTES` varchar(96) NOT NULL default '',
           `AUT_OBJECT_CLASSES` varchar(96) NOT NULL default '',
           PRIMARY KEY  (`AUT_UID`) )";
      if ( $version >= "4.1.20" ) $sql .= " ENGINE=MyISAM DEFAULT CHARSET=utf8 ";
      $sql .= ";  ";
      $this->_dbses->Execute( $sql );
    }

    //verify if exists new fields of USERS
    $sql = "SHOW COLUMNS FROM USERS LIKE  'USR_USE_LDAP' ";
    $dset = $this->_dbses->Execute( $sql );
    $row = $dset->Read();
    //if not exists create the table
    if ( ! is_array ( $row) )  {
      $sql = "ALTER TABLE `USERS` ADD `USR_USE_LDAP` ENUM( 'N', 'Y' ) DEFAULT 'N'  NOT NULL AFTER `USR_USERNAME` ,
              ADD `USR_LDAP_DN` VARCHAR( 255 ) NOT NULL AFTER `USR_USE_LDAP` ,
              ADD `USR_LDAP_SOURCE` INT NOT NULL AFTER `USR_LDAP_DN` ;";
      $this->_dbses->Execute( $sql );
    }

    return 1;
  }

  function newSource( $frm)  {
    $dset = $this->_dbses->Execute( "SELECT max(AUT_UID)+1 as MAX FROM AUTHENTICATION_SOURCES" );
    $row = $dset->Read();
    $index = ( $row['MAX'] == "" ? "1" :  $row['MAX'] );

    $this->Fields['AUT_UID']               = $index;
    $this->Fields['AUT_NAME']              = $frm['AUT_NAME'];
    $this->Fields['AUT_NAMESPACE']         = $frm['AUT_NAMESPACE'];
    $this->Fields['AUT_PROVIDER']          = $frm['AUT_PROVIDER'];
    $this->Fields['AUT_SERVER_NAME']       = $frm['AUT_SERVER_NAME'];
    $this->Fields['AUT_PORT']              = $frm['AUT_PORT'];
    $this->Fields['AUT_ENABLED_TLS']       = $frm['AUT_ENABLED_TLS'];
    $this->Fields['AUT_BASE_DN']           = $frm['AUT_BASE_DN'];
    $this->Fields['AUT_SEARCH_USER']       = $frm['AUT_SEARCH_USER'];
    $this->Fields['AUT_SEARCH_PASSWORD']   = $frm['AUT_SEARCH_PASSWORD'];
    $this->Fields['AUT_SEARCH_ATTRIBUTES'] = $frm['AUT_SEARCH_ATTRIBUTES'];
    $this->Fields['AUT_OBJECT_CLASSES']    = $frm['AUT_OBJECT_CLASSES'];
    $this->is_new = true;
    $this->Save();

    return $index;
  }

  function editSource( $index, $frm)  {

    $this->Fields['AUT_UID']               = $index;
    $this->Fields['AUT_NAME']              = $frm['AUT_NAME'];
    $this->Fields['AUT_NAMESPACE']         = $frm['AUT_NAMESPACE'];
    $this->Fields['AUT_PROVIDER']          = $frm['AUT_PROVIDER'];
    $this->Fields['AUT_SERVER_NAME']       = $frm['AUT_SERVER_NAME'];
    $this->Fields['AUT_PORT']              = $frm['AUT_PORT'];
    $this->Fields['AUT_ENABLED_TLS']       = $frm['AUT_ENABLED_TLS'];
    $this->Fields['AUT_BASE_DN']           = $frm['AUT_BASE_DN'];
    $this->Fields['AUT_SEARCH_USER']       = $frm['AUT_SEARCH_USER'];
    $this->Fields['AUT_SEARCH_PASSWORD']   = $frm['AUT_SEARCH_PASSWORD'];
    $this->Fields['AUT_SEARCH_ATTRIBUTES'] = $frm['AUT_SEARCH_ATTRIBUTES'];
    $this->Fields['AUT_OBJECT_CLASSES']    = $frm['AUT_OBJECT_CLASSES'];
    $this->is_new = false;
    $this->Save();

    return $index;
  }

  function removeSource( $index)  {
    $this->_dbses->Execute( "delete FROM AUTHENTICATION_SOURCES where AUT_UID = $index " );
    return 1;
  }

  function verifyPassword ( $userId, $strUser , $strPass, $index ) {
    $this->Load ( $index );

    $rAuth = $this->Fields;
    $log = array();
    $this->log ( "Authentication Source: " . $rAuth['AUT_NAME'] );
    $this->log ( "Provider:  " . $rAuth['AUT_PROVIDER']);
    $this->log ( "Server:  " . $rAuth['AUT_SERVER_NAME'] . ':' . $rAuth['AUT_PORT'] );
    $this->log ( "TLS:  " . $rAuth['AUT_ENABLED_TLS']  );
    $this->log ( "Base DN:  " . $rAuth['AUT_BASE_DN']  );
    $this->log ( "Search User:  " . $rAuth['AUT_SEARCH_USER']);
    $this->log ( "Search Attributes:   " . $rAuth['AUT_SEARCH_ATTRIBUTES']);
    $this->log ( "Object Classes: " . $rAuth['AUT_OBJECT_CLASSES']);
    $rootDn = $rAuth['AUT_BASE_DN'];
    $config = array(
            'dn' => $rAuth['AUT_SEARCH_USER'],
            'password' => $rAuth['AUT_SEARCH_PASSWORD'],
            'host' => $rAuth['AUT_SERVER_NAME'],
            'base' => $rAuth['AUT_BASE_DN'],
            'options' => array('LDAP_OPT_REFERRALS' => 0),
            'tls' => $rAuth['AUT_ENABLED_TLS'] ,
            'port'=> $rAuth['AUT_PORT']
    );

    $oLdap =& Net_LDAP::connect($config);
    if (PEAR::isError($oLdap)) {
      $this->log ( $oLdap->message );
      return -5;
    }
    $this->log ( "Binding sucessful" );

    $dset = $this->_dbses->Execute( "SELECT * FROM USERS where UID = $userId " );
    $row  = $dset->Read ();
    
    for ( $i = 2, $asterisk = ''; $i < strlen($strPass); $asterisk .= '*', $i++ );
    $fakePass = substr ( $strPass,0,2 ) . $asterisk;
    $this->log ( "user id: " . $row['USR_USERNAME'] );
    $this->log ( "user: $strUser " );
    $this->log ( "password    : $fakePass " );

    $res = $oLdap->reBind($strUser, $strPass );

    if (PEAR::isError($res)) {
      $this->log ( 'rebind: ' . $res->message );
      $return -2;
    }
    if ($res === true) {
      $this->log ( 'sucessful login, id = ' . $userId );
      return $userId;
    }

    return -5;
  }

  function testSource ( $index ) {
    $this->Load ( $index );

    $rAuth = $this->Fields;
    $log = array();
    $log[] = date('H:i:s ') . "Authentication Source: " . $rAuth['AUT_NAME'];
    $log[] = date('H:i:s ') . "Provider:  " . $rAuth['AUT_PROVIDER'];
    $log[] = date('H:i:s ') . "Server:  " . $rAuth['AUT_SERVER_NAME'] . ':' . $rAuth['AUT_PORT'] ;
    $log[] = date('H:i:s ') . "TLS:  " . $rAuth['AUT_ENABLED_TLS']  ;
    $log[] = date('H:i:s ') . "Base DN:  " . $rAuth['AUT_BASE_DN']  ;
    $log[] = date('H:i:s ') . "Search User:  " . $rAuth['AUT_SEARCH_USER'];
    $log[] = date('H:i:s ') . "Search Attributes:   " . $rAuth['AUT_SEARCH_ATTRIBUTES'];
    $log[] = date('H:i:s ') . "Object Classes: " . $rAuth['AUT_OBJECT_CLASSES'];
    $rootDn = $rAuth['AUT_BASE_DN'];
    $config = array(
            'dn' => $rAuth['AUT_SEARCH_USER'],
            'password' => $rAuth['AUT_SEARCH_PASSWORD'],
            'host' => $rAuth['AUT_SERVER_NAME'],
            'base' => $rAuth['AUT_BASE_DN'],
            'options' => array('LDAP_OPT_REFERRALS' => 0),
            'tls' => $rAuth['AUT_ENABLED_TLS'] ,
            'port'=> $rAuth['AUT_PORT']
    );

    $oLdap =& Net_LDAP::connect($config);
    if (PEAR::isError($oLdap)) {
      $log[] = date('H:i:s') . ' ' . $oLdap->message;
      return $log;
    }

    $log[] = date('H:i:s ') . "Binding sucessful, now showing first 10 users ";

    if ( $rAuth['AUT_PROVIDER'] == 'Active Directory' )
      //active directory
      $aAttributes = array ("cn", "samaccountname", "givenname", "sn", "userprincipalname");
    else
      //ldap
      $aAttributes = array ("cn", "dn", "givenname", "sn", "mail",);


    $sFilter = '(&(|(objectClass=user)(objectClass=inetOrgPerson)(objectClass=posixAccount))(|(cn=*)(mail=*)(sAMAccountName=*)))';
    $aParams = array(
      'scope' => 'sub',
      'sizelimit' => 1000, 
      'attributes' => $aAttributes,  //array('cn', 'dn', 'samaccountname'),
    );


    $oResult = $oLdap->search($rootDn, $sFilter, $aParams);

    if (PEAR::isError($oResult)) {
      $log[] = date('H:i:s') . ' ' . $oResult->message;
      return $log;
    }
    $i = 0;
    foreach($oResult->entries() as $oEntry) {
      $aAttr = $oEntry->attributes();
      if ($i == 10 ) continue;
      $log[] = date('H:i:s ') . ++$i . ' ' . $oEntry->dn();
    }


    $log[] = date('H:i:s ') . " Testing sucessful";
    return $log;
  }

  function searchUsers( $index , $searchText) {
    $this->Load ( $index );

    $rAuth = $this->Fields;
    $rows = array();
    $rootDn = $rAuth['AUT_BASE_DN'];
    $config = array(
            'dn' => $rAuth['AUT_SEARCH_USER'],
            'password' => $rAuth['AUT_SEARCH_PASSWORD'],
            'host' => $rAuth['AUT_SERVER_NAME'],
            'base' => $rAuth['AUT_BASE_DN'],
            'options' => array('LDAP_OPT_REFERRALS' => 0),
            'tls' => $rAuth['AUT_ENABLED_TLS'] ,
            'port'=> $rAuth['AUT_PORT']
    );

    $oLdap =& Net_LDAP::connect($config);
    if (PEAR::isError($oLdap)) {
      $data['codError'] = 1;
      $data['rows'] = date('H:i:s') . ' ' . $oLdap->message;
      return $data;
    }

    if ( $rAuth['AUT_PROVIDER']  == 'LDAP' )
      //ldap
      $aAttributes = array ("cn", "uid", "givenname", "sn", "mail", "mobile");
    else
      //Active Directory
      $aAttributes = array ("cn", "samaccountname", "givenname", "sn", "userprincipalname", "telephonenumber");

    if ( substr ( $searchText , -1 ) != '*' ) $searchText .= '*';
    $sFilter = "(&(|(objectClass=user)(objectClass=inetOrgPerson)(objectClass=posixAccount))(|(cn=$searchText)(mail=$searchText)(sAMAccountName=$searchText)))";
    $aParams = array(
      'scope' => 'sub',
      'attributes' => $aAttributes,
    );

    $oResult = $oLdap->search($rootDn, $sFilter, $aParams);
    if (PEAR::isError($oResult)) {
      $data['codError'] = 2;
      $data['rows'] = date('H:i:s') . ' ' .  $oLdap->message;
      return $data;
    }
    $i = 0;
    foreach($oResult->entries() as $oEntry) {
      $aAttr = $oEntry->attributes();
      $row['dn']   = $oEntry->dn();
      $row['attr'] = $oEntry->attributes();;
      if ( $rAuth['AUT_PROVIDER']  == 'Active Directory' ) {
        $row['attr']['uid']  = $row['attr']['sAMAccountName'];
        $row['attr']['mail'] = $row['attr']['userPrincipalName'];
//
//      print_r ($data)
      }

      $data['rows'][] = $row;
    }
//    sAMAccountName userPrincipalName

    $data['codError'] = 0;
    return $data;
  }

}
?>