<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'OUTPUT_DOCUMENT' table to 'workflow' DatabaseMap object.
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
class OutputDocumentMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'classes.model.map.OutputDocumentMapBuilder';

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

		$tMap = $this->dbMap->addTable('OUTPUT_DOCUMENT');
		$tMap->setPhpName('OutputDocument');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('OUT_DOC_UID', 'OutDocUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('OUT_DOC_LANDSCAPE', 'OutDocLandscape', 'int', CreoleTypes::TINYINT, true, null);

		$tMap->addColumn('OUT_DOC_GENERATE', 'OutDocGenerate', 'string', CreoleTypes::VARCHAR, true, 10);

		$tMap->addColumn('OUT_DOC_TYPE', 'OutDocType', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('OUT_DOC_CURRENT_REVISION', 'OutDocCurrentRevision', 'int', CreoleTypes::INTEGER, false, null);

		$tMap->addColumn('OUT_DOC_FIELD_MAPPING', 'OutDocFieldMapping', 'string', CreoleTypes::LONGVARCHAR, true, null);

		$tMap->addValidator('OUT_DOC_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Output Document UID can be no larger than 32 in size');

		$tMap->addValidator('OUT_DOC_UID', 'required', 'propel.validator.RequiredValidator', '', 'Output Document UID is required.');

		$tMap->addValidator('PRO_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Process UID can be no larger than 32 in size');

		$tMap->addValidator('PRO_UID', 'required', 'propel.validator.RequiredValidator', '', 'Process UID is required.');

		$tMap->addValidator('OUT_DOC_GENERATE', 'validValues', 'propel.validator.ValidValuesValidator', 'BOTH|DOC|PDF', 'Please select a outputdocument.');

		$tMap->addValidator('OUT_DOC_TYPE', 'validValues', 'propel.validator.ValidValuesValidator', 'HTML|ITEXT|JRXML|ACROFORM', 'Please select a valid Output Document Type.');

	} // doBuild()

} // OutputDocumentMapBuilder
