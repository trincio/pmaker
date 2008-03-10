<?php
/**
 * LanguageMapBuilder.php
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
 * This class adds structure of 'LANGUAGE' table to 'workflow' DatabaseMap object.
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
class LanguageMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'classes.model.map.LanguageMapBuilder';

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

		$tMap = $this->dbMap->addTable('LANGUAGE');
		$tMap->setPhpName('Language');

		$tMap->setUseIdGenerator(false);

		$tMap->addColumn('LAN_ID', 'LanId', 'string', CreoleTypes::VARCHAR, true, 4);

		$tMap->addColumn('LAN_NAME', 'LanName', 'string', CreoleTypes::VARCHAR, true, 30);

		$tMap->addColumn('LAN_NATIVE_NAME', 'LanNativeName', 'string', CreoleTypes::VARCHAR, true, 30);

		$tMap->addColumn('LAN_DIRECTION', 'LanDirection', 'string', CreoleTypes::CHAR, true, 1);

		$tMap->addColumn('LAN_WEIGHT', 'LanWeight', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addColumn('LAN_ENABLED', 'LanEnabled', 'string', CreoleTypes::CHAR, true, 1);

		$tMap->addColumn('LAN_CALENDAR', 'LanCalendar', 'string', CreoleTypes::VARCHAR, true, 30);

		$tMap->addValidator('LAN_DIRECTION', 'validValues', 'propel.validator.ValidValuesValidator', 'L|R', 'Please select a valid Language Direccion.');

		$tMap->addValidator('LAN_DIRECTION', 'required', 'propel.validator.RequiredValidator', '', 'Document access is required.');

		$tMap->addValidator('LAN_ENABLED', 'validValues', 'propel.validator.ValidValuesValidator', '1|0', 'Please select a valid Language Direccion.');

		$tMap->addValidator('LAN_ENABLED', 'required', 'propel.validator.RequiredValidator', '', 'Document access is required.');

	} // doBuild()

} // LanguageMapBuilder
