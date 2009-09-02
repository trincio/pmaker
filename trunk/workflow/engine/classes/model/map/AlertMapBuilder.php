<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'ALERT' table to 'workflow' DatabaseMap object.
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
class AlertMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'classes.model.map.AlertMapBuilder';

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

		$tMap = $this->dbMap->addTable('ALERT');
		$tMap->setPhpName('Alert');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('ALT_UID', 'AltUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('TAS_INITIAL', 'TasInitial', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('TAS_FINAL', 'TasFinal', 'string', CreoleTypes::VARCHAR, false, 32);

		$tMap->addColumn('ALT_TAS_DURATION', 'AltTasDuration', 'double', CreoleTypes::DOUBLE, false, null);

		$tMap->addColumn('ALT_TYPE', 'AltType', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('ALT_DAYS', 'AltDays', 'double', CreoleTypes::DOUBLE, false, null);

		$tMap->addColumn('ALT_MAX_ATTEMPTS', 'AltMaxAttempts', 'int', CreoleTypes::TINYINT, true, null);

		$tMap->addColumn('ALT_TEMPLATE', 'AltTemplate', 'string', CreoleTypes::VARCHAR, true, 100);

		$tMap->addColumn('ALT_DIGEST', 'AltDigest', 'int', CreoleTypes::TINYINT, true, null);

		$tMap->addColumn('TRI_UID', 'TriUid', 'string', CreoleTypes::VARCHAR, true, 32);

	} // doBuild()

} // AlertMapBuilder
