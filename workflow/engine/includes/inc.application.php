<?php

class App
{
  function ForceLogin()
  {
    global $HTTP_SESSION_VARS;
    global $G_MAIN_MENU;
    global $G_SUB_MENU;
    if( $HTTP_SESSION_VARS['LOGGED_IN'] == false)
    {
      header( "location: /sys/" . SYS_LANG . "/" . SYS_SKIN . "/login/login.html" ); 
      die();
    }
    else
    {
      $cmptype = $HTTP_SESSION_VARS['USER_TYPE'];
      switch( $cmptype )
      {
      case 'BUYER':
        $G_MAIN_MENU = "buyer";
        $G_SUB_MENU  = "";
        break;
      case 'PROVIDER':
        $G_MAIN_MENU = "provider";
        $G_SUB_MENU  = "";
        break;
      case 'REINSURANCE':
        $G_MAIN_MENU = "reinsurance";
        $G_SUB_MENU  = "";
        break;
      case 'ADMIN':
        $G_MAIN_MENU = "admin";
        $G_SUB_MENU  = "";
        break;
      case '':
        header( "location: /sys/" . SYS_LANG . "/" . SYS_SKIN . "/login/login.html" ); 
        die();
	break;
      default:
        $G_MAIN_MENU = "default";
	$G_SUB_MENU  = "";
        break;
      }
    }
  }
  
  function GetPartnerStatus()
  {
    global $HTTP_SESSION_VARS;
    $slipid = $HTTP_SESSION_VARS['CURRENT_SLIP'];
    $partnerid = $HTTP_SESSION_VARS['CURRENT_PARTNER'];
    
    $mdbc = new DBConnection();
    G::LoadClass( "slip" );
    $slip = new Slip;
    $slip->SetTo( $mdbc );
    $slip->Load( $slipid );
    $partner = $slip->GetPartner( $partnerid );
    
    $res = $partner->Fields['SLP_PARTNER_STATUS'];
    unset( $partner );
    unset( $slip );
    unset( $mdbc );
    return $res;
  }
  
  function SetPartnerStatus( $intStatus = 0 )
  {
    global $HTTP_SESSION_VARS;
    $slipid = $HTTP_SESSION_VARS['CURRENT_SLIP'];
    $partnerid = $HTTP_SESSION_VARS['CURRENT_PARTNER'];
    
    $mdbc = new DBConnection();
    G::LoadClass( "slip" );
    $slip = new Slip;
    $slip->SetTo( $mdbc );
    $slip->Load( $slipid );
    $partner = $slip->GetPartner( $partnerid );
    
    $partner->Fields = NULL;
    $partner->Fields['UID_SLIP'] = $slipid;
    $partner->Fields['UID_REINSURANCE'] = $partnerid;
    $partner->Fields['SLP_PARTNER_STATUS'] = $intStatus;
    $partner->Fields['SLP_PARTNER_UPDATED'] = G::CurDate();
    $partner->Save();
        
    unset( $partner );
    unset( $slip );
    unset( $mdbc );
  }

}

?>