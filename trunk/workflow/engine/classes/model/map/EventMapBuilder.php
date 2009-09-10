<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'EVENT' table to 'workflow' DatabaseMap object.
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
class EventMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'classes.model.map.EventMapBuilder';

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

		$tMap = $this->dbMap->addTable('EVENT');
		$tMap->setPhpName('Event');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('EVN_UID', 'EvnUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('EVN_RELATED_TO', 'EvnRelatedTo', 'string', CreoleTypes::VARCHAR, false, 10);

		$tMap->addColumn('TAS_UID', 'TasUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('EVN_TAS_UID_FROM', 'EvnTasUidFrom', 'string', CreoleTypes::VARCHAR, false, 32);

		$tMap->addColumn('EVN_TAS_UID_TO', 'EvnTasUidTo', 'string', CreoleTypes::VARCHAR, false, 32);

		$tMap->addColumn('EVN_TAS_STIMATED_DURATION', 'EvnTasStimatedDuration', 'double', CreoleTypes::DOUBLE, false, null);

		$tMap->addColumn('EVN_WHEN', 'EvnWhen', 'double', CreoleTypes::DOUBLE, true, null);

		$tMap->addColumn('EVN_MAX_ATTEMPTS', 'EvnMaxAttempts', 'int', CreoleTypes::TINYINT, true, null);

		$tMap->addColumn('EVN_ACTION', 'EvnAction', 'string', CreoleTypes::VARCHAR, true, 50);

		$tMap->addColumn('EVN_TEMPLATE', 'EvnTemplate', 'string', CreoleTypes::VARCHAR, false, 100);

		$tMap->addColumn('EVN_DIGEST', 'EvnDigest', 'int', CreoleTypes::TINYINT, false, null);

		$tMap->addColumn('TRI_UID', 'TriUid', 'string', CreoleTypes::VARCHAR, false, 32);

	} // doBuild()

} // EventMapBuilder
