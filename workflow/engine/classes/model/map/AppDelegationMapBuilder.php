<?php
/**
 * AppDelegationMapBuilder.php
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
 * This class adds structure of 'APP_DELEGATION' table to 'workflow' DatabaseMap object.
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
class AppDelegationMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'classes.model.map.AppDelegationMapBuilder';

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

		$tMap = $this->dbMap->addTable('APP_DELEGATION');
		$tMap->setPhpName('AppDelegation');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('APP_UID', 'AppUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addPrimaryKey('DEL_INDEX', 'DelIndex', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addColumn('DEL_PREVIOUS', 'DelPrevious', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('TAS_UID', 'TasUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('USR_UID', 'UsrUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('DEL_TYPE', 'DelType', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('DEL_THREAD', 'DelThread', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addColumn('DEL_THREAD_STATUS', 'DelThreadStatus', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('DEL_PRIORITY', 'DelPriority', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('DEL_DELEGATE_DATE', 'DelDelegateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

		$tMap->addColumn('DEL_INIT_DATE', 'DelInitDate', 'int', CreoleTypes::TIMESTAMP, false, null);

		$tMap->addColumn('DEL_TASK_DUE_DATE', 'DelTaskDueDate', 'int', CreoleTypes::TIMESTAMP, false, null);

		$tMap->addColumn('DEL_FINISH_DATE', 'DelFinishDate', 'int', CreoleTypes::TIMESTAMP, false, null);

		$tMap->addValidator('DEL_TYPE', 'validValues', 'propel.validator.ValidValuesValidator', 'NORMAL|PARALLEL', 'Please select a valid status.');

		$tMap->addValidator('DEL_PRIORITY', 'validValues', 'propel.validator.ValidValuesValidator', 'HIGH|NORMAL|LOW', 'Please select a valid Priority.');

		$tMap->addValidator('DEL_THREAD_STATUS', 'validValues', 'propel.validator.ValidValuesValidator', 'CLOSED|OPEN', 'Please select a valid status.');

	} // doBuild()

} // AppDelegationMapBuilder
