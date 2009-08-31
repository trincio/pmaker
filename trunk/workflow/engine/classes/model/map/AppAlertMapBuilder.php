<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'APP_ALERT' table to 'workflow' DatabaseMap object.
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
class AppAlertMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'classes.model.map.AppAlertMapBuilder';

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

		$tMap = $this->dbMap->addTable('APP_ALERT');
		$tMap->setPhpName('AppAlert');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('APP_UID', 'AppUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addPrimaryKey('DEL_INDEX', 'DelIndex', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addColumn('ALT_UID', 'AltUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('APP_ALT_ACTION_DATE', 'AppAltActionDate', 'int', CreoleTypes::TIMESTAMP, true, null);

		$tMap->addColumn('APP_ALT_ATTEMPTS', 'AppAltAttempts', 'int', CreoleTypes::TINYINT, true, null);

		$tMap->addColumn('APP_ALT_LAST_EXECUTION_DATE', 'AppAltLastExecutionDate', 'int', CreoleTypes::TIMESTAMP, false, null);

		$tMap->addColumn('APP_ALT_STATUS', 'AppAltStatus', 'string', CreoleTypes::VARCHAR, true, 10);

	} // doBuild()

} // AppAlertMapBuilder