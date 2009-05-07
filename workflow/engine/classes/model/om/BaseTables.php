<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/TablesPeer.php';

/**
 * Base class that represents a row from the 'TABLES' table.
 *
 * 
 *
 * @package    classes.model.om
 */
abstract class BaseTables extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        TablesPeer
	 */
	protected static $peer;


	/**
	 * The value for the tab_uid field.
	 * @var        string
	 */
	protected $tab_uid = '';


	/**
	 * The value for the tab_name field.
	 * @var        string
	 */
	protected $tab_name = '';


	/**
	 * The value for the tab_class_name field.
	 * @var        string
	 */
	protected $tab_class_name = '';


	/**
	 * The value for the tab_description field.
	 * @var        string
	 */
	protected $tab_description;


	/**
	 * The value for the tab_sdw_log_insert field.
	 * @var        int
	 */
	protected $tab_sdw_log_insert = 1;


	/**
	 * The value for the tab_sdw_log_update field.
	 * @var        int
	 */
	protected $tab_sdw_log_update = 1;


	/**
	 * The value for the tab_sdw_log_delete field.
	 * @var        int
	 */
	protected $tab_sdw_log_delete = 1;


	/**
	 * The value for the tab_sdw_log_select field.
	 * @var        int
	 */
	protected $tab_sdw_log_select = 0;


	/**
	 * The value for the tab_sdw_max_length field.
	 * @var        int
	 */
	protected $tab_sdw_max_length = -1;


	/**
	 * The value for the tab_sdw_auto_delete field.
	 * @var        int
	 */
	protected $tab_sdw_auto_delete = 0;


	/**
	 * The value for the tab_plg_uid field.
	 * @var        string
	 */
	protected $tab_plg_uid = '';

	/**
	 * Flag to prevent endless save loop, if this object is referenced
	 * by another object which falls in this transaction.
	 * @var        boolean
	 */
	protected $alreadyInSave = false;

	/**
	 * Flag to prevent endless validation loop, if this object is referenced
	 * by another object which falls in this transaction.
	 * @var        boolean
	 */
	protected $alreadyInValidation = false;

	/**
	 * Get the [tab_uid] column value.
	 * 
	 * @return     string
	 */
	public function getTabUid()
	{

		return $this->tab_uid;
	}

	/**
	 * Get the [tab_name] column value.
	 * 
	 * @return     string
	 */
	public function getTabName()
	{

		return $this->tab_name;
	}

	/**
	 * Get the [tab_class_name] column value.
	 * 
	 * @return     string
	 */
	public function getTabClassName()
	{

		return $this->tab_class_name;
	}

	/**
	 * Get the [tab_description] column value.
	 * 
	 * @return     string
	 */
	public function getTabDescription()
	{

		return $this->tab_description;
	}

	/**
	 * Get the [tab_sdw_log_insert] column value.
	 * 
	 * @return     int
	 */
	public function getTabSdwLogInsert()
	{

		return $this->tab_sdw_log_insert;
	}

	/**
	 * Get the [tab_sdw_log_update] column value.
	 * 
	 * @return     int
	 */
	public function getTabSdwLogUpdate()
	{

		return $this->tab_sdw_log_update;
	}

	/**
	 * Get the [tab_sdw_log_delete] column value.
	 * 
	 * @return     int
	 */
	public function getTabSdwLogDelete()
	{

		return $this->tab_sdw_log_delete;
	}

	/**
	 * Get the [tab_sdw_log_select] column value.
	 * 
	 * @return     int
	 */
	public function getTabSdwLogSelect()
	{

		return $this->tab_sdw_log_select;
	}

	/**
	 * Get the [tab_sdw_max_length] column value.
	 * 
	 * @return     int
	 */
	public function getTabSdwMaxLength()
	{

		return $this->tab_sdw_max_length;
	}

	/**
	 * Get the [tab_sdw_auto_delete] column value.
	 * 
	 * @return     int
	 */
	public function getTabSdwAutoDelete()
	{

		return $this->tab_sdw_auto_delete;
	}

	/**
	 * Get the [tab_plg_uid] column value.
	 * 
	 * @return     string
	 */
	public function getTabPlgUid()
	{

		return $this->tab_plg_uid;
	}

	/**
	 * Set the value of [tab_uid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setTabUid($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->tab_uid !== $v || $v === '') {
			$this->tab_uid = $v;
			$this->modifiedColumns[] = TablesPeer::TAB_UID;
		}

	} // setTabUid()

	/**
	 * Set the value of [tab_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setTabName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->tab_name !== $v || $v === '') {
			$this->tab_name = $v;
			$this->modifiedColumns[] = TablesPeer::TAB_NAME;
		}

	} // setTabName()

	/**
	 * Set the value of [tab_class_name] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setTabClassName($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->tab_class_name !== $v || $v === '') {
			$this->tab_class_name = $v;
			$this->modifiedColumns[] = TablesPeer::TAB_CLASS_NAME;
		}

	} // setTabClassName()

	/**
	 * Set the value of [tab_description] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setTabDescription($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->tab_description !== $v) {
			$this->tab_description = $v;
			$this->modifiedColumns[] = TablesPeer::TAB_DESCRIPTION;
		}

	} // setTabDescription()

	/**
	 * Set the value of [tab_sdw_log_insert] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setTabSdwLogInsert($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->tab_sdw_log_insert !== $v || $v === 1) {
			$this->tab_sdw_log_insert = $v;
			$this->modifiedColumns[] = TablesPeer::TAB_SDW_LOG_INSERT;
		}

	} // setTabSdwLogInsert()

	/**
	 * Set the value of [tab_sdw_log_update] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setTabSdwLogUpdate($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->tab_sdw_log_update !== $v || $v === 1) {
			$this->tab_sdw_log_update = $v;
			$this->modifiedColumns[] = TablesPeer::TAB_SDW_LOG_UPDATE;
		}

	} // setTabSdwLogUpdate()

	/**
	 * Set the value of [tab_sdw_log_delete] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setTabSdwLogDelete($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->tab_sdw_log_delete !== $v || $v === 1) {
			$this->tab_sdw_log_delete = $v;
			$this->modifiedColumns[] = TablesPeer::TAB_SDW_LOG_DELETE;
		}

	} // setTabSdwLogDelete()

	/**
	 * Set the value of [tab_sdw_log_select] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setTabSdwLogSelect($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->tab_sdw_log_select !== $v || $v === 0) {
			$this->tab_sdw_log_select = $v;
			$this->modifiedColumns[] = TablesPeer::TAB_SDW_LOG_SELECT;
		}

	} // setTabSdwLogSelect()

	/**
	 * Set the value of [tab_sdw_max_length] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setTabSdwMaxLength($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->tab_sdw_max_length !== $v || $v === -1) {
			$this->tab_sdw_max_length = $v;
			$this->modifiedColumns[] = TablesPeer::TAB_SDW_MAX_LENGTH;
		}

	} // setTabSdwMaxLength()

	/**
	 * Set the value of [tab_sdw_auto_delete] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setTabSdwAutoDelete($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->tab_sdw_auto_delete !== $v || $v === 0) {
			$this->tab_sdw_auto_delete = $v;
			$this->modifiedColumns[] = TablesPeer::TAB_SDW_AUTO_DELETE;
		}

	} // setTabSdwAutoDelete()

	/**
	 * Set the value of [tab_plg_uid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setTabPlgUid($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->tab_plg_uid !== $v || $v === '') {
			$this->tab_plg_uid = $v;
			$this->modifiedColumns[] = TablesPeer::TAB_PLG_UID;
		}

	} // setTabPlgUid()

	/**
	 * Hydrates (populates) the object variables with values from the database resultset.
	 *
	 * An offset (1-based "start column") is specified so that objects can be hydrated
	 * with a subset of the columns in the resultset rows.  This is needed, for example,
	 * for results of JOIN queries where the resultset row includes columns from two or
	 * more tables.
	 *
	 * @param      ResultSet $rs The ResultSet class with cursor advanced to desired record pos.
	 * @param      int $startcol 1-based offset column which indicates which restultset column to start with.
	 * @return     int next starting column
	 * @throws     PropelException  - Any caught Exception will be rewrapped as a PropelException.
	 */
	public function hydrate(ResultSet $rs, $startcol = 1)
	{
		try {

			$this->tab_uid = $rs->getString($startcol + 0);

			$this->tab_name = $rs->getString($startcol + 1);

			$this->tab_class_name = $rs->getString($startcol + 2);

			$this->tab_description = $rs->getString($startcol + 3);

			$this->tab_sdw_log_insert = $rs->getInt($startcol + 4);

			$this->tab_sdw_log_update = $rs->getInt($startcol + 5);

			$this->tab_sdw_log_delete = $rs->getInt($startcol + 6);

			$this->tab_sdw_log_select = $rs->getInt($startcol + 7);

			$this->tab_sdw_max_length = $rs->getInt($startcol + 8);

			$this->tab_sdw_auto_delete = $rs->getInt($startcol + 9);

			$this->tab_plg_uid = $rs->getString($startcol + 10);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 11; // 11 = TablesPeer::NUM_COLUMNS - TablesPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Tables object", $e);
		}
	}

	/**
	 * Removes this object from datastore and sets delete attribute.
	 *
	 * @param      Connection $con
	 * @return     void
	 * @throws     PropelException
	 * @see        BaseObject::setDeleted()
	 * @see        BaseObject::isDeleted()
	 */
	public function delete($con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("This object has already been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(TablesPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			TablesPeer::doDelete($this, $con);
			$this->setDeleted(true);
			$con->commit();
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Stores the object in the database.  If the object is new,
	 * it inserts it; otherwise an update is performed.  This method
	 * wraps the doSave() worker method in a transaction.
	 *
	 * @param      Connection $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        doSave()
	 */
	public function save($con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("You cannot save an object that has been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(TablesPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			$affectedRows = $this->doSave($con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	/**
	 * Stores the object in the database.
	 *
	 * If the object is new, it inserts it; otherwise an update is performed.
	 * All related objects are also updated in this method.
	 *
	 * @param      Connection $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        save()
	 */
	protected function doSave($con)
	{
		$affectedRows = 0; // initialize var to track total num of affected rows
		if (!$this->alreadyInSave) {
			$this->alreadyInSave = true;


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = TablesPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += TablesPeer::doUpdate($this, $con);
				}
				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			$this->alreadyInSave = false;
		}
		return $affectedRows;
	} // doSave()

	/**
	 * Array of ValidationFailed objects.
	 * @var        array ValidationFailed[]
	 */
	protected $validationFailures = array();

	/**
	 * Gets any ValidationFailed objects that resulted from last call to validate().
	 *
	 *
	 * @return     array ValidationFailed[]
	 * @see        validate()
	 */
	public function getValidationFailures()
	{
		return $this->validationFailures;
	}

	/**
	 * Validates the objects modified field values and all objects related to this table.
	 *
	 * If $columns is either a column name or an array of column names
	 * only those columns are validated.
	 *
	 * @param      mixed $columns Column name or an array of column names.
	 * @return     boolean Whether all columns pass validation.
	 * @see        doValidate()
	 * @see        getValidationFailures()
	 */
	public function validate($columns = null)
	{
		$res = $this->doValidate($columns);
		if ($res === true) {
			$this->validationFailures = array();
			return true;
		} else {
			$this->validationFailures = $res;
			return false;
		}
	}

	/**
	 * This function performs the validation work for complex object models.
	 *
	 * In addition to checking the current object, all related objects will
	 * also be validated.  If all pass then <code>true</code> is returned; otherwise
	 * an aggreagated array of ValidationFailed objects will be returned.
	 *
	 * @param      array $columns Array of column names to validate.
	 * @return     mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objets otherwise.
	 */
	protected function doValidate($columns = null)
	{
		if (!$this->alreadyInValidation) {
			$this->alreadyInValidation = true;
			$retval = null;

			$failureMap = array();


			if (($retval = TablesPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}



			$this->alreadyInValidation = false;
		}

		return (!empty($failureMap) ? $failureMap : true);
	}

	/**
	 * Retrieves a field from the object by name passed in as a string.
	 *
	 * @param      string $name name
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants TYPE_PHPNAME,
	 *                     TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
	 * @return     mixed Value of field.
	 */
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = TablesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->getByPosition($pos);
	}

	/**
	 * Retrieves a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @return     mixed Value of field at $pos
	 */
	public function getByPosition($pos)
	{
		switch($pos) {
			case 0:
				return $this->getTabUid();
				break;
			case 1:
				return $this->getTabName();
				break;
			case 2:
				return $this->getTabClassName();
				break;
			case 3:
				return $this->getTabDescription();
				break;
			case 4:
				return $this->getTabSdwLogInsert();
				break;
			case 5:
				return $this->getTabSdwLogUpdate();
				break;
			case 6:
				return $this->getTabSdwLogDelete();
				break;
			case 7:
				return $this->getTabSdwLogSelect();
				break;
			case 8:
				return $this->getTabSdwMaxLength();
				break;
			case 9:
				return $this->getTabSdwAutoDelete();
				break;
			case 10:
				return $this->getTabPlgUid();
				break;
			default:
				return null;
				break;
		} // switch()
	}

	/**
	 * Exports the object as an array.
	 *
	 * You can specify the key type of the array by passing one of the class
	 * type constants.
	 *
	 * @param      string $keyType One of the class type constants TYPE_PHPNAME,
	 *                        TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
	 * @return     an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = TablesPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getTabUid(),
			$keys[1] => $this->getTabName(),
			$keys[2] => $this->getTabClassName(),
			$keys[3] => $this->getTabDescription(),
			$keys[4] => $this->getTabSdwLogInsert(),
			$keys[5] => $this->getTabSdwLogUpdate(),
			$keys[6] => $this->getTabSdwLogDelete(),
			$keys[7] => $this->getTabSdwLogSelect(),
			$keys[8] => $this->getTabSdwMaxLength(),
			$keys[9] => $this->getTabSdwAutoDelete(),
			$keys[10] => $this->getTabPlgUid(),
		);
		return $result;
	}

	/**
	 * Sets a field from the object by name passed in as a string.
	 *
	 * @param      string $name peer name
	 * @param      mixed $value field value
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants TYPE_PHPNAME,
	 *                     TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
	 * @return     void
	 */
	public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = TablesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->setByPosition($pos, $value);
	}

	/**
	 * Sets a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @param      mixed $value field value
	 * @return     void
	 */
	public function setByPosition($pos, $value)
	{
		switch($pos) {
			case 0:
				$this->setTabUid($value);
				break;
			case 1:
				$this->setTabName($value);
				break;
			case 2:
				$this->setTabClassName($value);
				break;
			case 3:
				$this->setTabDescription($value);
				break;
			case 4:
				$this->setTabSdwLogInsert($value);
				break;
			case 5:
				$this->setTabSdwLogUpdate($value);
				break;
			case 6:
				$this->setTabSdwLogDelete($value);
				break;
			case 7:
				$this->setTabSdwLogSelect($value);
				break;
			case 8:
				$this->setTabSdwMaxLength($value);
				break;
			case 9:
				$this->setTabSdwAutoDelete($value);
				break;
			case 10:
				$this->setTabPlgUid($value);
				break;
		} // switch()
	}

	/**
	 * Populates the object using an array.
	 *
	 * This is particularly useful when populating an object from one of the
	 * request arrays (e.g. $_POST).  This method goes through the column
	 * names, checking to see whether a matching key exists in populated
	 * array. If so the setByName() method is called for that column.
	 *
	 * You can specify the key type of the array by additionally passing one
	 * of the class type constants TYPE_PHPNAME, TYPE_COLNAME, TYPE_FIELDNAME,
	 * TYPE_NUM. The default key type is the column's phpname (e.g. 'authorId')
	 *
	 * @param      array  $arr     An array to populate the object from.
	 * @param      string $keyType The type of keys the array uses.
	 * @return     void
	 */
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = TablesPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setTabUid($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setTabName($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setTabClassName($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setTabDescription($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setTabSdwLogInsert($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setTabSdwLogUpdate($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setTabSdwLogDelete($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setTabSdwLogSelect($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setTabSdwMaxLength($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setTabSdwAutoDelete($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setTabPlgUid($arr[$keys[10]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(TablesPeer::DATABASE_NAME);

		if ($this->isColumnModified(TablesPeer::TAB_UID)) $criteria->add(TablesPeer::TAB_UID, $this->tab_uid);
		if ($this->isColumnModified(TablesPeer::TAB_NAME)) $criteria->add(TablesPeer::TAB_NAME, $this->tab_name);
		if ($this->isColumnModified(TablesPeer::TAB_CLASS_NAME)) $criteria->add(TablesPeer::TAB_CLASS_NAME, $this->tab_class_name);
		if ($this->isColumnModified(TablesPeer::TAB_DESCRIPTION)) $criteria->add(TablesPeer::TAB_DESCRIPTION, $this->tab_description);
		if ($this->isColumnModified(TablesPeer::TAB_SDW_LOG_INSERT)) $criteria->add(TablesPeer::TAB_SDW_LOG_INSERT, $this->tab_sdw_log_insert);
		if ($this->isColumnModified(TablesPeer::TAB_SDW_LOG_UPDATE)) $criteria->add(TablesPeer::TAB_SDW_LOG_UPDATE, $this->tab_sdw_log_update);
		if ($this->isColumnModified(TablesPeer::TAB_SDW_LOG_DELETE)) $criteria->add(TablesPeer::TAB_SDW_LOG_DELETE, $this->tab_sdw_log_delete);
		if ($this->isColumnModified(TablesPeer::TAB_SDW_LOG_SELECT)) $criteria->add(TablesPeer::TAB_SDW_LOG_SELECT, $this->tab_sdw_log_select);
		if ($this->isColumnModified(TablesPeer::TAB_SDW_MAX_LENGTH)) $criteria->add(TablesPeer::TAB_SDW_MAX_LENGTH, $this->tab_sdw_max_length);
		if ($this->isColumnModified(TablesPeer::TAB_SDW_AUTO_DELETE)) $criteria->add(TablesPeer::TAB_SDW_AUTO_DELETE, $this->tab_sdw_auto_delete);
		if ($this->isColumnModified(TablesPeer::TAB_PLG_UID)) $criteria->add(TablesPeer::TAB_PLG_UID, $this->tab_plg_uid);

		return $criteria;
	}

	/**
	 * Builds a Criteria object containing the primary key for this object.
	 *
	 * Unlike buildCriteria() this method includes the primary key values regardless
	 * of whether or not they have been modified.
	 *
	 * @return     Criteria The Criteria object containing value(s) for primary key(s).
	 */
	public function buildPkeyCriteria()
	{
		$criteria = new Criteria(TablesPeer::DATABASE_NAME);

		$criteria->add(TablesPeer::TAB_UID, $this->tab_uid);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     string
	 */
	public function getPrimaryKey()
	{
		return $this->getTabUid();
	}

	/**
	 * Generic method to set the primary key (tab_uid column).
	 *
	 * @param      string $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setTabUid($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of Tables (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setTabName($this->tab_name);

		$copyObj->setTabClassName($this->tab_class_name);

		$copyObj->setTabDescription($this->tab_description);

		$copyObj->setTabSdwLogInsert($this->tab_sdw_log_insert);

		$copyObj->setTabSdwLogUpdate($this->tab_sdw_log_update);

		$copyObj->setTabSdwLogDelete($this->tab_sdw_log_delete);

		$copyObj->setTabSdwLogSelect($this->tab_sdw_log_select);

		$copyObj->setTabSdwMaxLength($this->tab_sdw_max_length);

		$copyObj->setTabSdwAutoDelete($this->tab_sdw_auto_delete);

		$copyObj->setTabPlgUid($this->tab_plg_uid);


		$copyObj->setNew(true);

		$copyObj->setTabUid(''); // this is a pkey column, so set to default value

	}

	/**
	 * Makes a copy of this object that will be inserted as a new row in table when saved.
	 * It creates a new object filling in the simple attributes, but skipping any primary
	 * keys that are defined for the table.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @return     Tables Clone of current object.
	 * @throws     PropelException
	 */
	public function copy($deepCopy = false)
	{
		// we use get_class(), because this might be a subclass
		$clazz = get_class($this);
		$copyObj = new $clazz();
		$this->copyInto($copyObj, $deepCopy);
		return $copyObj;
	}

	/**
	 * Returns a peer instance associated with this om.
	 *
	 * Since Peer classes are not to have any instance attributes, this method returns the
	 * same instance for all member of this class. The method could therefore
	 * be static, but this would prevent one from overriding the behavior.
	 *
	 * @return     TablesPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new TablesPeer();
		}
		return self::$peer;
	}

} // BaseTables
