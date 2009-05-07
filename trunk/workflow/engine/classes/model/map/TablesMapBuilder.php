<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'TABLES' table to 'propel' DatabaseMap object.
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
class TablesMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'classes.model.map.TablesMapBuilder';

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
		$this->dbMap = Propel::getDatabaseMap('propel');

		$tMap = $this->dbMap->addTable('TABLES');
		$tMap->setPhpName('Tables');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('TAB_UID', 'TabUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('TAB_NAME', 'TabName', 'string', CreoleTypes::VARCHAR, true, 60);

		$tMap->addColumn('TAB_CLASS_NAME', 'TabClassName', 'string', CreoleTypes::VARCHAR, true, 100);

		$tMap->addColumn('TAB_DESCRIPTION', 'TabDescription', 'string', CreoleTypes::LONGVARCHAR, true, null);

		$tMap->addColumn('TAB_SDW_LOG_INSERT', 'TabSdwLogInsert', 'int', CreoleTypes::TINYINT, true, null);

		$tMap->addColumn('TAB_SDW_LOG_UPDATE', 'TabSdwLogUpdate', 'int', CreoleTypes::TINYINT, true, null);

		$tMap->addColumn('TAB_SDW_LOG_DELETE', 'TabSdwLogDelete', 'int', CreoleTypes::TINYINT, true, null);

		$tMap->addColumn('TAB_SDW_LOG_SELECT', 'TabSdwLogSelect', 'int', CreoleTypes::TINYINT, true, null);

		$tMap->addColumn('TAB_SDW_MAX_LENGTH', 'TabSdwMaxLength', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addColumn('TAB_SDW_AUTO_DELETE', 'TabSdwAutoDelete', 'int', CreoleTypes::TINYINT, true, null);

		$tMap->addColumn('TAB_PLG_UID', 'TabPlgUid', 'string', CreoleTypes::VARCHAR, true, 32);

	} // doBuild()

} // TablesMapBuilder
