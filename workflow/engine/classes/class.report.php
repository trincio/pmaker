<?php
/**
 * class.report.php
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


/**
 * Report - Report class
 * @package ProcessMaker
 * @author Everth S. Berrios Morales
 * @copyright 2008 COLOSA
 */

class Report {
	function generatedReport1()
	{
		require_once 'classes/model/AppDelegation.php';
		require_once 'classes/model/Application.php';
		$oCriteria = new Criteria('workflow');
		$del = DBAdapter::getStringDelimiter();
    $oCriteria->addSelectColumn(AppDelegationPeer::PRO_UID);
    $oCriteria->addAsColumn("TOTALDUR", "SUM(".AppDelegationPeer::DEL_DURATION.")");
    $oCriteria->addAsColumn("PROMEDIO", "AVG(".AppDelegationPeer::DEL_DURATION.")");
    $oCriteria->addAsColumn('PRO_TITLE', 'C1.CON_VALUE' );
    $oCriteria->addAlias("C1",  'CONTENT');
    $proTitleConds = array();
    $proTitleConds[] = array( AppDelegationPeer::PRO_UID , 'C1.CON_ID' );
    $proTitleConds[] = array( 'C1.CON_CATEGORY' , $del . 'PRO_TITLE' . $del );
    $proTitleConds[] = array( 'C1.CON_LANG' ,     $del . SYS_LANG . $del );
    $oCriteria->addJoinMC($proTitleConds ,    Criteria::LEFT_JOIN);
    $oCriteria->addGroupByColumn(AppDelegationPeer::PRO_UID);

    $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    $aProcess[] = array('PRO_UID'   => 'char',
      	                'PRO_TITLE' => 'char',
      	                'CANTCASES' => 'integer',
      	                'TOTALDUR'  => 'float',
      	                'PROMEDIO'  => 'float');
    while ($aRow = $oDataset->getRow()) {
      	$oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn(ApplicationPeer::PRO_UID);
        //$oCriteria->addAsColumn("CANTCASES", "COUNT(".ApplicationPeer::PRO_UID.")");
        $oCriteria->add(ApplicationPeer::PRO_UID,     $aRow['PRO_UID']);
        //$oCriteria->addGroupByColumn(ApplicationPeer::PRO_UID);

      	$aProcess[] = array('PRO_UID'   => $aRow['PRO_UID'],
      	                    'PRO_TITLE' => $aRow['PRO_TITLE'],
      	                    'CANTCASES' => ApplicationPeer::doCount($oCriteria),
      	                    'TOTALDUR'  => $aRow['TOTALDUR'],
      	                    'PROMEDIO'  => $aRow['PROMEDIO']);
      	$oDataset->next();
     }

    global $_DBArray;
    $_DBArray['reports']  = $aProcess;
    $_SESSION['_DBArray'] = $_DBArray;
    G::LoadClass('ArrayPeer');
    $oCriteria = new Criteria('dbarray');
    $oCriteria->setDBArrayTable('reports');

    return $oCriteria;
	}

	function descriptionReport1($PRO_UID)
	{
		require_once 'classes/model/AppDelegation.php';
		require_once 'classes/model/Task.php';
		require_once 'classes/model/Content.php';

		$oCriteria = new Criteria('workflow');
		$del = DBAdapter::getStringDelimiter();
		$oCriteria->addSelectColumn(AppDelegationPeer::PRO_UID);
		$oCriteria->addAsColumn("TOTALDUR", "SUM(".AppDelegationPeer::DEL_DURATION.")");
		$oCriteria->addAsColumn("PROMEDIO", "AVG(".AppDelegationPeer::DEL_DURATION.")");

		$oCriteria->addJoin(AppDelegationPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN);

		$oCriteria->addAsColumn('TAS_TITLE', 'C.CON_VALUE');
		$oCriteria->addAlias("C", 'CONTENT');

		$proContentConds = array();
		$proContentConds[] = array(AppDelegationPeer::TAS_UID, 'C.CON_ID');
		$proContentConds[] = array('C.CON_CATEGORY', $del . 'TAS_TITLE' . $del);
		$proContentConds[] = array('C.CON_LANG',     $del . SYS_LANG . $del);
		$oCriteria->addJoinMC($proContentConds,      Criteria::LEFT_JOIN);

		$oCriteria->add(AppDelegationPeer::PRO_UID, $PRO_UID);

		$oCriteria->addGroupByColumn(AppDelegationPeer::PRO_UID);
		$oCriteria->addGroupByColumn('C.CON_VALUE');

		return $oCriteria;
	}

	function generatedReport2()
	{
		require_once 'classes/model/AppDelegation.php';
		require_once 'classes/model/Application.php';
		$oCriteria = new Criteria('workflow');
		$del = DBAdapter::getStringDelimiter();
    $oCriteria->addSelectColumn(AppDelegationPeer::PRO_UID);
    $oCriteria->addAsColumn('PRO_TITLE', 'C1.CON_VALUE' );
    $oCriteria->addAlias("C1",  'CONTENT');
    $proTitleConds = array();
    $proTitleConds[] = array( AppDelegationPeer::PRO_UID , 'C1.CON_ID' );
    $proTitleConds[] = array( 'C1.CON_CATEGORY' , $del . 'PRO_TITLE' . $del );
    $proTitleConds[] = array( 'C1.CON_LANG' ,     $del . SYS_LANG . $del );
    $oCriteria->addJoinMC($proTitleConds ,    Criteria::LEFT_JOIN);
    $oCriteria->addGroupByColumn(AppDelegationPeer::PRO_UID);

    $oDataset = AppDelegationPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();

    $lastmonth = mktime(0, 0, 0, date("m")-1  , date("d"), date("Y"));
    $month = date( 'Y-m-d' , $lastmonth );

    $lastday = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));
    $day = date( 'Y-m-d' , $lastday );

    $aProcess[] = array('PRO_UID'   => 'char',
      	                'PRO_TITLE' => 'char',
      	                'CANTCASES' => 'integer',
      	                'CASELASTMONTH' => 'integer',
      	                'CASELASTDAY' => 'integer'
      	                );

    while ($aRow = $oDataset->getRow()) {
      	$oCriteria2 = new Criteria('workflow');
      	$oCriteria2->addSelectColumn(ApplicationPeer::PRO_UID);
        $oCriteria2->addAsColumn("CANTCASES", "COUNT(*)");
        $oCriteria2->add(ApplicationPeer::PRO_UID, $aRow['PRO_UID']);
        $oCriteria2->addGroupByColumn(ApplicationPeer::PRO_UID);
        $oDataset2 = AppDelegationPeer::doSelectRS($oCriteria2);
        $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset2->next();
        $aRow2 = $oDataset2->getRow();
        $cant = $aRow2['CANTCASES'];

      	$oCriteria2 = new Criteria('workflow');
      	$oCriteria2->addSelectColumn(ApplicationPeer::PRO_UID);
        $oCriteria2->addAsColumn("CANTCASES", "COUNT(*)");
        $oCriteria2->add(ApplicationPeer::PRO_UID, $aRow['PRO_UID']);
        $oCriteria2->add(ApplicationPeer::APP_INIT_DATE, $month, Criteria::GREATER_EQUAL);
        $oCriteria2->addGroupByColumn(ApplicationPeer::PRO_UID);
        $oDataset2 = AppDelegationPeer::doSelectRS($oCriteria2);
        $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset2->next();
        $aRow2 = $oDataset2->getRow();
        $cant1 = $aRow2['CANTCASES'];

        $oCriteria2 = new Criteria('workflow');
      	$oCriteria2->addSelectColumn(ApplicationPeer::PRO_UID);
        $oCriteria2->addAsColumn("CANTCASES", "COUNT(*)");
        $oCriteria2->add(ApplicationPeer::PRO_UID, $aRow['PRO_UID']);
        $oCriteria2->add(ApplicationPeer::APP_INIT_DATE, $day, Criteria::GREATER_EQUAL);
        $oCriteria2->addGroupByColumn(ApplicationPeer::PRO_UID);
        $oDataset2 = AppDelegationPeer::doSelectRS($oCriteria2);
        $oDataset2->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset2->next();
        $aRow2 = $oDataset2->getRow();
        $cant2 = $aRow2['CANTCASES'];

      	$aProcess[] = array('PRO_UID'   => $aRow['PRO_UID'],
      	                    'PRO_TITLE' => $aRow['PRO_TITLE'],
      	                    'CANTCASES' => $cant,
      	                    'CASELASTMONTH' => $cant1,
      	                    'CASELASTDAY' => $cant2
      	                   );
      	$oDataset->next();
     }

    global $_DBArray;
    $_DBArray['reports']  = $aProcess;
    $_SESSION['_DBArray'] = $_DBArray;
    G::LoadClass('ArrayPeer');
    $oCriteria = new Criteria('dbarray');
    $oCriteria->setDBArrayTable('reports');
    return $oCriteria;
	}
}



