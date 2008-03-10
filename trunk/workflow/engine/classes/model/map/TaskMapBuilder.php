<?php
/**
 * TaskMapBuilder.php
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

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'TASK' table to 'workflow' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    classes.model.map
 */
class TaskMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'classes.model.map.TaskMapBuilder';

	/**
	 * The database map.
	 */
	private $dbMap;

	/**
	 * Tells us if this DatabaseMapBuilder is built so that we
	 * don't have to re-build it every time.
	 *
	 * @return     boolean true if this DatabaseMapBuilder is built, false otherwise.
	 */
	public function isBuilt()
	{
		return ($this->dbMap !== null);
	}

	/**
	 * Gets the databasemap this map builder built.
	 *
	 * @return     the databasemap
	 */
	public function getDatabaseMap()
	{
		return $this->dbMap;
	}

	/**
	 * The doBuild() method builds the DatabaseMap
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function doBuild()
	{
		$this->dbMap = Propel::getDatabaseMap('workflow');

		$tMap = $this->dbMap->addTable('TASK');
		$tMap->setPhpName('Task');

		$tMap->setUseIdGenerator(false);

		$tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addPrimaryKey('TAS_UID', 'TasUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('TAS_TYPE', 'TasType', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('TAS_DURATION', 'TasDuration', 'double', CreoleTypes::DOUBLE, true, null);

		$tMap->addColumn('TAS_DELAY_TYPE', 'TasDelayType', 'string', CreoleTypes::VARCHAR, true, 30);

		$tMap->addColumn('TAS_TEMPORIZER', 'TasTemporizer', 'double', CreoleTypes::DOUBLE, true, null);

		$tMap->addColumn('TAS_TYPE_DAY', 'TasTypeDay', 'string', CreoleTypes::CHAR, true, 1);

		$tMap->addColumn('TAS_TIMEUNIT', 'TasTimeunit', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('TAS_ALERT', 'TasAlert', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('TAS_PRIORITY_VARIABLE', 'TasPriorityVariable', 'string', CreoleTypes::VARCHAR, true, 100);

		$tMap->addColumn('TAS_ASSIGN_TYPE', 'TasAssignType', 'string', CreoleTypes::VARCHAR, true, 30);

		$tMap->addColumn('TAS_ASSIGN_VARIABLE', 'TasAssignVariable', 'string', CreoleTypes::VARCHAR, true, 100);

		$tMap->addColumn('TAS_ASSIGN_LOCATION', 'TasAssignLocation', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('TAS_ASSIGN_LOCATION_ADHOC', 'TasAssignLocationAdhoc', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('TAS_TRANSFER_FLY', 'TasTransferFly', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('TAS_LAST_ASSIGNED', 'TasLastAssigned', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('TAS_USER', 'TasUser', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('TAS_CAN_UPLOAD', 'TasCanUpload', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('TAS_VIEW_UPLOAD', 'TasViewUpload', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('TAS_VIEW_ADDITIONAL_DOCUMENTATION', 'TasViewAdditionalDocumentation', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('TAS_CAN_CANCEL', 'TasCanCancel', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('TAS_OWNER_APP', 'TasOwnerApp', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('STG_UID', 'StgUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('TAS_CAN_PAUSE', 'TasCanPause', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('TAS_CAN_SEND_MESSAGE', 'TasCanSendMessage', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('TAS_CAN_DELETE_DOCS', 'TasCanDeleteDocs', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('TAS_SELF_SERVICE', 'TasSelfService', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('TAS_START', 'TasStart', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('TAS_TO_LAST_USER', 'TasToLastUser', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('TAS_SEND_LAST_EMAIL', 'TasSendLastEmail', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('TAS_DERIVATION', 'TasDerivation', 'string', CreoleTypes::VARCHAR, true, 100);

		$tMap->addColumn('TAS_POSX', 'TasPosx', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addColumn('TAS_POSY', 'TasPosy', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addColumn('TAS_COLOR', 'TasColor', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addValidator('TAS_TYPE', 'validValues', 'propel.validator.ValidValuesValidator', 'NORMAL|ADHOC', 'Please select a valid value for TAS_TYPE.');

		$tMap->addValidator('TAS_TIMEUNIT', 'validValues', 'propel.validator.ValidValuesValidator', 'MINUTES|HOURS|DAYS|WEEKS|MONTHS', 'Please select a valid value for TAS_TIMEUNIT.');

		$tMap->addValidator('TAS_ALERT', 'validValues', 'propel.validator.ValidValuesValidator', 'TRUE|FALSE', 'Please select a valid value for TAS_ALERT.');

		$tMap->addValidator('TAS_ASSIGN_TYPE', 'validValues', 'propel.validator.ValidValuesValidator', 'BALANCED|MANUAL|EVALUATE|SELFSERVICE', 'Please select a valid value for TAS_ASSIGN_TYPE.');

		$tMap->addValidator('TAS_ASSIGN_LOCATION', 'validValues', 'propel.validator.ValidValuesValidator', 'TRUE|FALSE', 'Please select a valid value for TAS_ASSIGN_LOCATION.');

		$tMap->addValidator('TAS_ASSIGN_LOCATION_ADHOC', 'validValues', 'propel.validator.ValidValuesValidator', 'TRUE|FALSE', 'Please select a valid value for TAS_ASSIGN_LOCATION_ADHOC.');

		$tMap->addValidator('TAS_TRANSFER_FLY', 'validValues', 'propel.validator.ValidValuesValidator', 'TRUE|FALSE', 'Please select a valid value for TAS_TRANSFER_FLY.');

		$tMap->addValidator('TAS_CAN_UPLOAD', 'validValues', 'propel.validator.ValidValuesValidator', 'TRUE|FALSE', 'Please select a valid value for TAS_CAN_UPLOAD.');

		$tMap->addValidator('TAS_VIEW_UPLOAD', 'validValues', 'propel.validator.ValidValuesValidator', 'TRUE|FALSE', 'Please select a valid value for TAS_VIEW_UPLOAD.');

		$tMap->addValidator('TAS_VIEW_ADDITIONAL_DOCUMENTATION', 'validValues', 'propel.validator.ValidValuesValidator', 'TRUE|FALSE', 'Please select a valid value for TAS_VIEW_ADDITIONAL_DOCUMENTATION.');

		$tMap->addValidator('TAS_CAN_CANCEL', 'validValues', 'propel.validator.ValidValuesValidator', 'TRUE|FALSE', 'Please select a valid value for TAS_CAN_CANCEL.');

		$tMap->addValidator('TAS_CAN_PAUSE', 'validValues', 'propel.validator.ValidValuesValidator', 'TRUE|FALSE', 'Please select a valid value for TAS_CAN_PAUSE.');

		$tMap->addValidator('TAS_CAN_SEND_MESSAGE', 'validValues', 'propel.validator.ValidValuesValidator', 'TRUE|FALSE', 'Please select a valid value for TAS_CAN_SEND_MESSAGE.');

		$tMap->addValidator('TAS_CAN_DELETE_DOCS', 'validValues', 'propel.validator.ValidValuesValidator', 'TRUE|VIEW|FALSE', 'Please select a valid value for TAS_CAN_DELETE_DOCS.');

		$tMap->addValidator('TAS_SELF_SERVICE', 'validValues', 'propel.validator.ValidValuesValidator', 'TRUE|FALSE', 'Please select a valid value for TAS_SELF_SERVICE.');

		$tMap->addValidator('TAS_START', 'validValues', 'propel.validator.ValidValuesValidator', 'TRUE|FALSE', 'Please select a valid value for TAS_START.');

		$tMap->addValidator('TAS_TO_LAST_USER', 'validValues', 'propel.validator.ValidValuesValidator', 'TRUE|FALSE', 'Please select a valid value for TAS_TO_LAST_USER.');

		$tMap->addValidator('TAS_SEND_LAST_EMAIL', 'validValues', 'propel.validator.ValidValuesValidator', 'TRUE|FALSE', 'Please select a valid value for TAS_SEND_LAST_EMAIL.');

		$tMap->addValidator('TAS_DERIVATION', 'validValues', 'propel.validator.ValidValuesValidator', 'NORMAL|FAST|AUTOMATIC', 'Please select a valid value for TAS_DERIVATION.');

	} // doBuild()

} // TaskMapBuilder
