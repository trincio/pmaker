<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'SHADOW_TABLE' table to 'workflow' DatabaseMap object.
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
class ShadowTableMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'classes.model.map.ShadowTableMapBuilder';

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

		$tMap = $this->dbMap->addTable('SHADOW_TABLE');
		$tMap->setPhpName('ShadowTable');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('SHD_UID', 'ShdUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('ADD_TAB_UID', 'AddTabUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('SHD_ACTION', 'ShdAction', 'string', CreoleTypes::VARCHAR, true, 10);

		$tMap->addColumn('SHD_DETAILS', 'ShdDetails', 'string', CreoleTypes::LONGVARCHAR, true, null);

		$tMap->addColumn('USR_UID', 'UsrUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('APP_UID', 'AppUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('SHD_DATE', 'ShdDate', 'int', CreoleTypes::TIMESTAMP, false, null);

	} // doBuild()

} // ShadowTableMapBuilder
