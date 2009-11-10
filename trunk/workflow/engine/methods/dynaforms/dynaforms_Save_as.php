<?php

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
		//return 0;
		}
		catch(Exception $e)
		{
			return (array) $e;
		}
?>