<?php
/**
 * InputDocumentMapBuilder.php
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
 * This class adds structure of 'INPUT_DOCUMENT' table to 'workflow' DatabaseMap object.
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
class InputDocumentMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'classes.model.map.InputDocumentMapBuilder';

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

		$tMap = $this->dbMap->addTable('INPUT_DOCUMENT');
		$tMap->setPhpName('InputDocument');

		$tMap->setUseIdGenerator(false);

		$tMap->addPrimaryKey('INP_DOC_UID', 'InpDocUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

		$tMap->addColumn('INP_DOC_FORM_NEEDED', 'InpDocFormNeeded', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('INP_DOC_ORIGINAL', 'InpDocOriginal', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addColumn('INP_DOC_PUBLISHED', 'InpDocPublished', 'string', CreoleTypes::VARCHAR, true, 20);

		$tMap->addValidator('INP_DOC_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Input Document UID can be no larger than 32 in size');

		$tMap->addValidator('INP_DOC_UID', 'required', 'propel.validator.RequiredValidator', '', 'Input Document UID is required.');

		$tMap->addValidator('PRO_UID', 'maxLength', 'propel.validator.MaxLengthValidator', '32', 'Process UID can be no larger than 32 in size');

		$tMap->addValidator('PRO_UID', 'required', 'propel.validator.RequiredValidator', '', 'Process UID is required.');

		$tMap->addValidator('INP_DOC_FORM_NEEDED', 'validValues', 'propel.validator.ValidValuesValidator', 'VIRTUAL|REAL|VREAL', 'Please select a valid document format.');

		$tMap->addValidator('INP_DOC_FORM_NEEDED', 'required', 'propel.validator.RequiredValidator', '', 'Document format is required.');

		$tMap->addValidator('INP_DOC_ORIGINAL', 'validValues', 'propel.validator.ValidValuesValidator', 'COPY|ORIGINAL|COPYLEGAL|FINAL', 'Please select a valid document format type.');

		$tMap->addValidator('INP_DOC_ORIGINAL', 'required', 'propel.validator.RequiredValidator', '', 'Document format type is required.');

		$tMap->addValidator('INP_DOC_PUBLISHED', 'validValues', 'propel.validator.ValidValuesValidator', 'PUBLIC|PRIVATE', 'Please select a valid document access.');

		$tMap->addValidator('INP_DOC_PUBLISHED', 'required', 'propel.validator.RequiredValidator', '', 'Document access is required.');

	} // doBuild()

} // InputDocumentMapBuilder
