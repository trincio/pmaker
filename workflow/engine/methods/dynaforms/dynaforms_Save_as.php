<?php
/**
 * dynaforms_Save_as.php
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
    require_once('classes/model/Dynaform.php');
    
		try
		{ 
			$con = Propel::getConnection( DynaformPeer::DATABASE_NAME );
		  $frm=$_POST['form'];
		  $PRO_UID=$frm['PRO_UID'];
		  $DYN_UID=$frm['DYN_UID'];
			
			$dynaform = new dynaform;
			/*Save Register*/
		  	
				$dynUid  = ( G::generateUniqueID() );
				
				$dynaform->setDynUid          ( $dynUid );
      	$dynaform->setProUid          ( $PRO_UID );
      	$dynaform->setDynType         ( 'xmlform' );
      	$dynaform->setDynFilename     ( $PRO_UID . PATH_SEP . $dynUid );

        $con->begin();
        $res = $dynaform->save();
        $dynaform->setDynTitle (  $frm['DYN_TITLENEW'] );
        $dynaform->setDynDescription ((!$frm['DYN_DESCRIPTIONNEW'])?'Default Dynaform Description':$frm['DYN_DESCRIPTIONNEW']);
				
        //$con->commit();
        
        $hd =fopen(PATH_DYNAFORM . $PRO_UID . '/' . $DYN_UID . '.xml',"r");	
			  $hd1=fopen(PATH_DYNAFORM . $PRO_UID . '/' . $dynUid . '.xml' ,"w");	
			  
			  if($hd){
					while(!feof($hd)){
					$line=fgets($hd,4096);
					fwrite($hd1,str_replace($DYN_UID,$dynUid,$line));
					}
				}
				
				fclose($hd);
			  fclose($hd1);
		
		}
		catch(Exception $e)
		{
			return (array) $e;
		}
?>