<?php
/**
 * RouteMapBuilder.php
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
 * This class adds structure of 'ROUTE' table to 'workflow' DatabaseMap object.
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
class RouteMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'classes.model.map.RouteMapBuilder';

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

		$tMap = $this->dbMap->addTable('ROUTE');
		$tMap->setPhpName('Route');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('ROU_UID', 'RouUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('ROU_PARENT', 'RouParent', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('TAS_UID', 'TasUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('ROU_NEXT_TASK', 'RouNextTask', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('ROU_CASE', 'RouCase', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addColumn('ROU_TYPE', 'RouType', 'string', CreoleTypes::VARCHAR, true, 25);

		$tMap->addColumn('ROU_CONDITION', 'RouCondition', 'string', CreoleTypes::VARCHAR, true, 255);

		$tMap->addColumn('ROU_TO_LAST_USER', 'RouToLastUser', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('ROU_OPTIONAL', 'RouOptional', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('ROU_SEND_EMAIL', 'RouSendEmail', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('ROU_SOURCEANCHOR', 'RouSourceanchor', 'int', CreoleTypes::INTEGER, false, null);

		$tMap->addColumn('ROU_TARGETANCHOR', 'RouTargetanchor', 'int', CreoleTypes::INTEGER, false, null);

		$tMap->addValidator('ROU_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Route UID can be no larger than 32 in size');

		$tMap->addValidator('ROU_UID', 'required', 'propel.validator.RequiredValidator', '', 'Route UID is required.');

		$tMap->addValidator('PRO_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Process UID can be no larger than 32 in size');

		$tMap->addValidator('PRO_UID', 'required', 'propel.validator.RequiredValidator', '', 'Process UID is required.');

		$tMap->addValidator('TAS_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Task UID can be no larger than 32 in size');

		$tMap->addValidator('TAS_UID', 'required', 'propel.validator.RequiredValidator', '', 'Task UID is required.');

		$tMap->addValidator('ROU_NEXT_TASK', 'required', 'propel.validator.RequiredValidator', '', 'Next Task UID is required.');

		$tMap->addValidator('ROU_TYPE', 'validValues', 'propel.validator.ValidValuesValidator', 'SEQUENTIAL|EVALUATE|SELECT|PARALLEL|PARALLEL-BY-EVALUATION|SEC-JOIN', 'Please select a valid Route Type.');

		$tMap->addValidator('ROU_TYPE', 'required', 'propel.validator.RequiredValidator', '', 'Route type is required.');

		$tMap->addValidator('ROU_TO_LAST_USER', 'validValues', 'propel.validator.ValidValuesValidator', 'FALSE|TRUE', 'Please select a valid value for ROU_TO_LAST_USER .');

		$tMap->addValidator('ROU_OPTIONAL', 'validValues', 'propel.validator.ValidValuesValidator', 'FALSE|TRUE', 'Please select a valid value for ROU_OPTIONAL .');

		$tMap->addValidator('ROU_SEND_EMAIL', 'validValues', 'propel.validator.ValidValuesValidator', 'FALSE|TRUE', 'Please select a valid value for ROU_SEND_EMAIL.');

	} // doBuild()

} // RouteMapBuilder
