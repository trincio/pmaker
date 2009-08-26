<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/AppAlertPeer.php';

/**
 * Base class that represents a row from the 'APP_ALERT' table.
 *
 * 
 *
 * @package    classes.model.om
 */
abstract class BaseAppAlert extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        AppAlertPeer
	 */
	protected static $peer;


	/**
	 * The value for the app_uid field.
	 * @var        string
	 */
	protected $app_uid = '';


	/**
	 * The value for the del_index field.
	 * @var        int
	 */
	protected $del_index = 0;


	/**
	 * The value for the alt_uid field.
	 * @var        string
	 */
	protected $alt_uid = '';


	/**
	 * The value for the app_alt_action_date field.
	 * @var        int
	 */
	protected $app_alt_action_date;


	/**
	 * The value for the app_alt_attempts field.
	 * @var        int
	 */
	protected $app_alt_attempts = 0;


	/**
	 * The value for the app_alt_last_execution_date field.
	 * @var        int
	 */
	protected $app_alt_last_execution_date;


	/**
	 * The value for the app_alt_status field.
	 * @var        string
	 */
	protected $app_alt_status = 'OPEN';

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
	 * Get the [app_uid] column value.
	 * 
	 * @return     string
	 */
	public function getAppUid()
	{

		return $this->app_uid;
	}

	/**
	 * Get the [del_index] column value.
	 * 
	 * @return     int
	 */
	public function getDelIndex()
	{

		return $this->del_index;
	}

	/**
	 * Get the [alt_uid] column value.
	 * 
	 * @return     string
	 */
	public function getAltUid()
	{

		return $this->alt_uid;
	}

	/**
	 * Get the [optionally formatted] [app_alt_action_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getAppAltActionDate($format = 'Y-m-d H:i:s')
	{

		if ($this->app_alt_action_date === null || $this->app_alt_action_date === '') {
			return null;
		} elseif (!is_int($this->app_alt_action_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->app_alt_action_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [app_alt_action_date] as date/time value: " . var_export($this->app_alt_action_date, true));
			}
		} else {
			$ts = $this->app_alt_action_date;
		}
		if ($format === null) {
			return $ts;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $ts);
		} else {
			return date($format, $ts);
		}
	}

	/**
	 * Get the [app_alt_attempts] column value.
	 * 
	 * @return     int
	 */
	public function getAppAltAttempts()
	{

		return $this->app_alt_attempts;
	}

	/**
	 * Get the [optionally formatted] [app_alt_last_execution_date] column value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the integer unix timestamp will be returned.
	 * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
	 * @throws     PropelException - if unable to convert the date/time to timestamp.
	 */
	public function getAppAltLastExecutionDate($format = 'Y-m-d H:i:s')
	{

		if ($this->app_alt_last_execution_date === null || $this->app_alt_last_execution_date === '') {
			return null;
		} elseif (!is_int($this->app_alt_last_execution_date)) {
			// a non-timestamp value was set externally, so we convert it
			$ts = strtotime($this->app_alt_last_execution_date);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse value of [app_alt_last_execution_date] as date/time value: " . var_export($this->app_alt_last_execution_date, true));
			}
		} else {
			$ts = $this->app_alt_last_execution_date;
		}
		if ($format === null) {
			return $ts;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $ts);
		} else {
			return date($format, $ts);
		}
	}

	/**
	 * Get the [app_alt_status] column value.
	 * 
	 * @return     string
	 */
	public function getAppAltStatus()
	{

		return $this->app_alt_status;
	}

	/**
	 * Set the value of [app_uid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setAppUid($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->app_uid !== $v || $v === '') {
			$this->app_uid = $v;
			$this->modifiedColumns[] = AppAlertPeer::APP_UID;
		}

	} // setAppUid()

	/**
	 * Set the value of [del_index] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setDelIndex($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->del_index !== $v || $v === 0) {
			$this->del_index = $v;
			$this->modifiedColumns[] = AppAlertPeer::DEL_INDEX;
		}

	} // setDelIndex()

	/**
	 * Set the value of [alt_uid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setAltUid($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->alt_uid !== $v || $v === '') {
			$this->alt_uid = $v;
			$this->modifiedColumns[] = AppAlertPeer::ALT_UID;
		}

	} // setAltUid()

	/**
	 * Set the value of [app_alt_action_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setAppAltActionDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [app_alt_action_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->app_alt_action_date !== $ts) {
			$this->app_alt_action_date = $ts;
			$this->modifiedColumns[] = AppAlertPeer::APP_ALT_ACTION_DATE;
		}

	} // setAppAltActionDate()

	/**
	 * Set the value of [app_alt_attempts] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setAppAltAttempts($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->app_alt_attempts !== $v || $v === 0) {
			$this->app_alt_attempts = $v;
			$this->modifiedColumns[] = AppAlertPeer::APP_ALT_ATTEMPTS;
		}

	} // setAppAltAttempts()

	/**
	 * Set the value of [app_alt_last_execution_date] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setAppAltLastExecutionDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
				throw new PropelException("Unable to parse date/time value for [app_alt_last_execution_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->app_alt_last_execution_date !== $ts) {
			$this->app_alt_last_execution_date = $ts;
			$this->modifiedColumns[] = AppAlertPeer::APP_ALT_LAST_EXECUTION_DATE;
		}

	} // setAppAltLastExecutionDate()

	/**
	 * Set the value of [app_alt_status] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setAppAltStatus($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->app_alt_status !== $v || $v === 'OPEN') {
			$this->app_alt_status = $v;
			$this->modifiedColumns[] = AppAlertPeer::APP_ALT_STATUS;
		}

	} // setAppAltStatus()

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

			$this->app_uid = $rs->getString($startcol + 0);

			$this->del_index = $rs->getInt($startcol + 1);

			$this->alt_uid = $rs->getString($startcol + 2);

			$this->app_alt_action_date = $rs->getTimestamp($startcol + 3, null);

			$this->app_alt_attempts = $rs->getInt($startcol + 4);

			$this->app_alt_last_execution_date = $rs->getTimestamp($startcol + 5, null);

			$this->app_alt_status = $rs->getString($startcol + 6);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 7; // 7 = AppAlertPeer::NUM_COLUMNS - AppAlertPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating AppAlert object", $e);
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
			$con = Propel::getConnection(AppAlertPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			AppAlertPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(AppAlertPeer::DATABASE_NAME);
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
					$pk = AppAlertPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += AppAlertPeer::doUpdate($this, $con);
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


			if (($retval = AppAlertPeer::doValidate($this, $columns)) !== true) {
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
		$pos = AppAlertPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getAppUid();
				break;
			case 1:
				return $this->getDelIndex();
				break;
			case 2:
				return $this->getAltUid();
				break;
			case 3:
				return $this->getAppAltActionDate();
				break;
			case 4:
				return $this->getAppAltAttempts();
				break;
			case 5:
				return $this->getAppAltLastExecutionDate();
				break;
			case 6:
				return $this->getAppAltStatus();
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
		$keys = AppAlertPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getAppUid(),
			$keys[1] => $this->getDelIndex(),
			$keys[2] => $this->getAltUid(),
			$keys[3] => $this->getAppAltActionDate(),
			$keys[4] => $this->getAppAltAttempts(),
			$keys[5] => $this->getAppAltLastExecutionDate(),
			$keys[6] => $this->getAppAltStatus(),
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
		$pos = AppAlertPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setAppUid($value);
				break;
			case 1:
				$this->setDelIndex($value);
				break;
			case 2:
				$this->setAltUid($value);
				break;
			case 3:
				$this->setAppAltActionDate($value);
				break;
			case 4:
				$this->setAppAltAttempts($value);
				break;
			case 5:
				$this->setAppAltLastExecutionDate($value);
				break;
			case 6:
				$this->setAppAltStatus($value);
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
		$keys = AppAlertPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setAppUid($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDelIndex($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setAltUid($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setAppAltActionDate($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setAppAltAttempts($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setAppAltLastExecutionDate($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setAppAltStatus($arr[$keys[6]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(AppAlertPeer::DATABASE_NAME);

		if ($this->isColumnModified(AppAlertPeer::APP_UID)) $criteria->add(AppAlertPeer::APP_UID, $this->app_uid);
		if ($this->isColumnModified(AppAlertPeer::DEL_INDEX)) $criteria->add(AppAlertPeer::DEL_INDEX, $this->del_index);
		if ($this->isColumnModified(AppAlertPeer::ALT_UID)) $criteria->add(AppAlertPeer::ALT_UID, $this->alt_uid);
		if ($this->isColumnModified(AppAlertPeer::APP_ALT_ACTION_DATE)) $criteria->add(AppAlertPeer::APP_ALT_ACTION_DATE, $this->app_alt_action_date);
		if ($this->isColumnModified(AppAlertPeer::APP_ALT_ATTEMPTS)) $criteria->add(AppAlertPeer::APP_ALT_ATTEMPTS, $this->app_alt_attempts);
		if ($this->isColumnModified(AppAlertPeer::APP_ALT_LAST_EXECUTION_DATE)) $criteria->add(AppAlertPeer::APP_ALT_LAST_EXECUTION_DATE, $this->app_alt_last_execution_date);
		if ($this->isColumnModified(AppAlertPeer::APP_ALT_STATUS)) $criteria->add(AppAlertPeer::APP_ALT_STATUS, $this->app_alt_status);

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
		$criteria = new Criteria(AppAlertPeer::DATABASE_NAME);

		$criteria->add(AppAlertPeer::APP_UID, $this->app_uid);
		$criteria->add(AppAlertPeer::DEL_INDEX, $this->del_index);

		return $criteria;
	}

	/**
	 * Returns the composite primary key for this object.
	 * The array elements will be in same order as specified in XML.
	 * @return     array
	 */
	public function getPrimaryKey()
	{
		$pks = array();

		$pks[0] = $this->getAppUid();

		$pks[1] = $this->getDelIndex();

		return $pks;
	}

	/**
	 * Set the [composite] primary key.
	 *
	 * @param      array $keys The elements of the composite key (order must match the order in XML file).
	 * @return     void
	 */
	public function setPrimaryKey($keys)
	{

		$this->setAppUid($keys[0]);

		$this->setDelIndex($keys[1]);

	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of AppAlert (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setAltUid($this->alt_uid);

		$copyObj->setAppAltActionDate($this->app_alt_action_date);

		$copyObj->setAppAltAttempts($this->app_alt_attempts);

		$copyObj->setAppAltLastExecutionDate($this->app_alt_last_execution_date);

		$copyObj->setAppAltStatus($this->app_alt_status);


		$copyObj->setNew(true);

		$copyObj->setAppUid(''); // this is a pkey column, so set to default value

		$copyObj->setDelIndex('0'); // this is a pkey column, so set to default value

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
	 * @return     AppAlert Clone of current object.
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
	 * @return     AppAlertPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new AppAlertPeer();
		}
		return self::$peer;
	}

} // BaseAppAlert
