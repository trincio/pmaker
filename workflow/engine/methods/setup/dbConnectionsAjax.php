<?php
/**
 * upgrade.php
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
switch ($_POST['action']) {
    case 'showConnections':
        require_once 'classes/model/DbSource.php';
        $oDBSource = new DbSource();
        $oCriteria = $oDBSource->getCriteriaDBSList($_POST['PRO_UID']);
        global $G_PUBLISH;
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent('propeltable', 'paged-table', 'setup/dbConnections', $oCriteria);
        $G_HEADER->clearScripts();
        G::RenderPage('publish', 'raw');
        break;
    case 'newOrEditDBConnection':
        if ($_POST['DBS_UID'] != '') {
            $aFields = $_POST;
        } else {
            $aFields = $_POST;
        }
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent('xmlform', 'xmlform', 'setup/dbConnections_Edit', '', $aFields);
        $G_HEADER->clearScripts();
        G::RenderPage('publish', 'raw');
        break;

    case 'saveConnection':
    	
    	$pro_uid = $_POST['pro_uid'];
        $type = $_POST['type'];
        $server = $_POST['server'];
        $db_name = $_POST['db_name'];
        $user = $_POST['user'];
        $passwd = ($_POST['passwd'] == 'none') ? "":$_POST['passwd'];
        $port = ($_POST['port'] == 'none') ? "":$_POST['port'];
        
        require_once 'classes/model/DbSource.php';
        $oDBSource = new DbSource();
		//$oDBSource->setDbsUid();
		$oDBSource->setProUid($pro_uid);
		$oDBSource->setDbsType($type);
		$oDBSource->setDbsServer($server);
		$oDBSource->setDbsDatabaseName($db_name);
		$oDBSource->setDbsUsername($user);
		$oDBSource->setDbsPassword($passwd);
		$oDBSource->setDbsPort($port);
		 
		$oDBSource->save();
		echo "<center><font color=blue><b>The Server configuration wass added successfully!</b><font></center>";    
        break;

    case 'showTestConnection':

        $G_PUBLISH = new Publisher;
        $G_PUBLISH->AddContent('view', 'setup/dbConnections');
        G::RenderPage('publish', 'raw');
        break;

    case 'testConnection':
        sleep(0);
        $teststep = $_POST['teststep'];
        $type = $_POST['type'];
        $server = $_POST['server'];
        $db_name = $_POST['db_name'];
        $user = $_POST['user'];
        $passwd = ($_POST['passwd'] == 'none') ? "":$_POST['passwd'];
        $port = ($_POST['port'] == 'none') ? "":$_POST['port'];

        require_once 'classes/class.net.php';
        G::LoadClass('NET');
        $db_server = new NET($server);

        switch ($teststep) {
            case 'resolv':
                if ($db_server->getErrno() == 0) {
                    print ('true');
                } else {
                    print ('false');
                }
                break;

            case 'checkport':
                $db_server->scannPort($port);
                if ($db_server->getErrno() == 0) {
                    print ('true');
                } else {
                    print ('false');
                }
                break;

            case 'mysql_ping':
                $db_server->mysql_ping($user, $passwd);
                if ($db_server->getErrno() == 0) {
                    print ('true');
                } else {
                    print ('false');
                }
                break;

            case 'mysql_testDataBase':
                $db_server->mysql_testDataBase($user, $passwd, $db_name);
                if ($db_server->getErrno() == 0) {
                    print ('true');
                } else {
                    print ('false');
                }
                break;

            default:
                echo 'default';
        }


        break;
}
