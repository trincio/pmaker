<?php
/**
 * DepartmentMapBuilder.php
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
 * This class adds structure of 'DEPARTMENT' table to 'workflow' DatabaseMap object.
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
class DepartmentMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'classes.model.map.DepartmentMapBuilder';

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

		$tMap = $this->dbMap->addTable('DEPARTMENT');
		$tMap->setPhpName('Department');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('DEP_UID', 'DepUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('DEP_PARENT', 'DepParent', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('DEP_MANAGER', 'DepManager', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('DEP_LOCATION', 'DepLocation', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addColumn('DEP_STATUS', 'DepStatus', 'string', CreoleTypes::CHAR, true, 1);

		$tMap->addColumn('DEP_TYPE', 'DepType', 'string', CreoleTypes::VARCHAR, true, 5);

		$tMap->addColumn('DEP_REF_CODE', 'DepRefCode', 'string', CreoleTypes::VARCHAR, true, 10);

		$tMap->addValidator('DEP_TYPE', 'validValues', 'propel.validator.ValidValuesValidator', 'INTER', 'Please select a valid department type.');

	} // doBuild()

} // DepartmentMapBuilder
