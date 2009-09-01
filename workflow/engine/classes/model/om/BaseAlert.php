<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/AlertPeer.php';

/**
 * Base class that represents a row from the 'ALERT' table.
 *
 * 
 *
 * @package    classes.model.om
 */
abstract class BaseAlert extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        AlertPeer
	 */
	protected static $peer;


	/**
	 * The value for the alt_uid field.
	 * @var        string
	 */
	protected $alt_uid = '';


	/**
	 * The value for the pro_uid field.
	 * @var        string
	 */
	protected $pro_uid = '';


	/**
	 * The value for the tas_initial field.
	 * @var        string
	 */
	protected $tas_initial = '';


	/**
	 * The value for the tas_final field.
	 * @var        string
	 */
	protected $tas_final = '';


	/**
	 * The value for the alt_type field.
	 * @var        string
	 */
	protected $alt_type = '';


	/**
	 * The value for the alt_days field.
	 * @var        double
	 */
	protected $alt_days = 1;


	/**
	 * The value for the alt_max_attempts field.
	 * @var        int
	 */
	protected $alt_max_attempts = 3;


	/**
	 * The value for the alt_template field.
	 * @var        string
	 */
	protected $alt_template = '';


	/**
	 * The value for the alt_digest field.
	 * @var        int
	 */
	protected $alt_digest = 1;


	/**
	 * The value for the tri_uid field.
	 * @var        string
	 */
	protected $tri_uid = '';

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
	 * Get the [alt_uid] column value.
	 * 
	 * @return     string
	 */
	public function getAltUid()
	{

		return $this->alt_uid;
	}

	/**
	 * Get the [pro_uid] column value.
	 * 
	 * @return     string
	 */
	public function getProUid()
	{

		return $this->pro_uid;
	}

	/**
	 * Get the [tas_initial] column value.
	 * 
	 * @return     string
	 */
	public function getTasInitial()
	{

		return $this->tas_initial;
	}

	/**
	 * Get the [tas_final] column value.
	 * 
	 * @return     string
	 */
	public function getTasFinal()
	{

		return $this->tas_final;
	}

	/**
	 * Get the [alt_type] column value.
	 * 
	 * @return     string
	 */
	public function getAltType()
	{

		return $this->alt_type;
	}

	/**
	 * Get the [alt_days] column value.
	 * 
	 * @return     double
	 */
	public function getAltDays()
	{

		return $this->alt_days;
	}

	/**
	 * Get the [alt_max_attempts] column value.
	 * 
	 * @return     int
	 */
	public function getAltMaxAttempts()
	{

		return $this->alt_max_attempts;
	}

	/**
	 * Get the [alt_template] column value.
	 * 
	 * @return     string
	 */
	public function getAltTemplate()
	{

		return $this->alt_template;
	}

	/**
	 * Get the [alt_digest] column value.
	 * 
	 * @return     int
	 */
	public function getAltDigest()
	{

		return $this->alt_digest;
	}

	/**
	 * Get the [tri_uid] column value.
	 * 
	 * @return     string
	 */
	public function getTriUid()
	{

		return $this->tri_uid;
	}

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
			$this->modifiedColumns[] = AlertPeer::ALT_UID;
		}

	} // setAltUid()

	/**
	 * Set the value of [pro_uid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setProUid($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->pro_uid !== $v || $v === '') {
			$this->pro_uid = $v;
			$this->modifiedColumns[] = AlertPeer::PRO_UID;
		}

	} // setProUid()

	/**
	 * Set the value of [tas_initial] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setTasInitial($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->tas_initial !== $v || $v === '') {
			$this->tas_initial = $v;
			$this->modifiedColumns[] = AlertPeer::TAS_INITIAL;
		}

	} // setTasInitial()

	/**
	 * Set the value of [tas_final] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setTasFinal($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->tas_final !== $v || $v === '') {
			$this->tas_final = $v;
			$this->modifiedColumns[] = AlertPeer::TAS_FINAL;
		}

	} // setTasFinal()

	/**
	 * Set the value of [alt_type] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setAltType($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->alt_type !== $v || $v === '') {
			$this->alt_type = $v;
			$this->modifiedColumns[] = AlertPeer::ALT_TYPE;
		}

	} // setAltType()

	/**
	 * Set the value of [alt_days] column.
	 * 
	 * @param      double $v new value
	 * @return     void
	 */
	public function setAltDays($v)
	{

		if ($this->alt_days !== $v || $v === 1) {
			$this->alt_days = $v;
			$this->modifiedColumns[] = AlertPeer::ALT_DAYS;
		}

	} // setAltDays()

	/**
	 * Set the value of [alt_max_attempts] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setAltMaxAttempts($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->alt_max_attempts !== $v || $v === 3) {
			$this->alt_max_attempts = $v;
			$this->modifiedColumns[] = AlertPeer::ALT_MAX_ATTEMPTS;
		}

	} // setAltMaxAttempts()

	/**
	 * Set the value of [alt_template] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setAltTemplate($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->alt_template !== $v || $v === '') {
			$this->alt_template = $v;
			$this->modifiedColumns[] = AlertPeer::ALT_TEMPLATE;
		}

	} // setAltTemplate()

	/**
	 * Set the value of [alt_digest] column.
	 * 
	 * @param      int $v new value
	 * @return     void
	 */
	public function setAltDigest($v)
	{

		// Since the native PHP type for this column is integer,
		// we will cast the input value to an int (if it is not).
		if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->alt_digest !== $v || $v === 1) {
			$this->alt_digest = $v;
			$this->modifiedColumns[] = AlertPeer::ALT_DIGEST;
		}

	} // setAltDigest()

	/**
	 * Set the value of [tri_uid] column.
	 * 
	 * @param      string $v new value
	 * @return     void
	 */
	public function setTriUid($v)
	{

		// Since the native PHP type for this column is string,
		// we will cast the input to a string (if it is not).
		if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->tri_uid !== $v || $v === '') {
			$this->tri_uid = $v;
			$this->modifiedColumns[] = AlertPeer::TRI_UID;
		}

	} // setTriUid()

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

			$this->alt_uid = $rs->getString($startcol + 0);

			$this->pro_uid = $rs->getString($startcol + 1);

			$this->tas_initial = $rs->getString($startcol + 2);

			$this->tas_final = $rs->getString($startcol + 3);

			$this->alt_type = $rs->getString($startcol + 4);

			$this->alt_days = $rs->getFloat($startcol + 5);

			$this->alt_max_attempts = $rs->getInt($startcol + 6);

			$this->alt_template = $rs->getString($startcol + 7);

			$this->alt_digest = $rs->getInt($startcol + 8);

			$this->tri_uid = $rs->getString($startcol + 9);

			$this->resetModified();

			$this->setNew(false);

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 10; // 10 = AlertPeer::NUM_COLUMNS - AlertPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Alert object", $e);
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
			$con = Propel::getConnection(AlertPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			AlertPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(AlertPeer::DATABASE_NAME);
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
					$pk = AlertPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += AlertPeer::doUpdate($this, $con);
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


			if (($retval = AlertPeer::doValidate($this, $columns)) !== true) {
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
		$pos = AlertPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getAltUid();
				break;
			case 1:
				return $this->getProUid();
				break;
			case 2:
				return $this->getTasInitial();
				break;
			case 3:
				return $this->getTasFinal();
				break;
			case 4:
				return $this->getAltType();
				break;
			case 5:
				return $this->getAltDays();
				break;
			case 6:
				return $this->getAltMaxAttempts();
				break;
			case 7:
				return $this->getAltTemplate();
				break;
			case 8:
				return $this->getAltDigest();
				break;
			case 9:
				return $this->getTriUid();
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
		$keys = AlertPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getAltUid(),
			$keys[1] => $this->getProUid(),
			$keys[2] => $this->getTasInitial(),
			$keys[3] => $this->getTasFinal(),
			$keys[4] => $this->getAltType(),
			$keys[5] => $this->getAltDays(),
			$keys[6] => $this->getAltMaxAttempts(),
			$keys[7] => $this->getAltTemplate(),
			$keys[8] => $this->getAltDigest(),
			$keys[9] => $this->getTriUid(),
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
		$pos = AlertPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setAltUid($value);
				break;
			case 1:
				$this->setProUid($value);
				break;
			case 2:
				$this->setTasInitial($value);
				break;
			case 3:
				$this->setTasFinal($value);
				break;
			case 4:
				$this->setAltType($value);
				break;
			case 5:
				$this->setAltDays($value);
				break;
			case 6:
				$this->setAltMaxAttempts($value);
				break;
			case 7:
				$this->setAltTemplate($value);
				break;
			case 8:
				$this->setAltDigest($value);
				break;
			case 9:
				$this->setTriUid($value);
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
		$keys = AlertPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setAltUid($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setProUid($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setTasInitial($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setTasFinal($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setAltType($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setAltDays($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setAltMaxAttempts($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setAltTemplate($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setAltDigest($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setTriUid($arr[$keys[9]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(AlertPeer::DATABASE_NAME);

		if ($this->isColumnModified(AlertPeer::ALT_UID)) $criteria->add(AlertPeer::ALT_UID, $this->alt_uid);
		if ($this->isColumnModified(AlertPeer::PRO_UID)) $criteria->add(AlertPeer::PRO_UID, $this->pro_uid);
		if ($this->isColumnModified(AlertPeer::TAS_INITIAL)) $criteria->add(AlertPeer::TAS_INITIAL, $this->tas_initial);
		if ($this->isColumnModified(AlertPeer::TAS_FINAL)) $criteria->add(AlertPeer::TAS_FINAL, $this->tas_final);
		if ($this->isColumnModified(AlertPeer::ALT_TYPE)) $criteria->add(AlertPeer::ALT_TYPE, $this->alt_type);
		if ($this->isColumnModified(AlertPeer::ALT_DAYS)) $criteria->add(AlertPeer::ALT_DAYS, $this->alt_days);
		if ($this->isColumnModified(AlertPeer::ALT_MAX_ATTEMPTS)) $criteria->add(AlertPeer::ALT_MAX_ATTEMPTS, $this->alt_max_attempts);
		if ($this->isColumnModified(AlertPeer::ALT_TEMPLATE)) $criteria->add(AlertPeer::ALT_TEMPLATE, $this->alt_template);
		if ($this->isColumnModified(AlertPeer::ALT_DIGEST)) $criteria->add(AlertPeer::ALT_DIGEST, $this->alt_digest);
		if ($this->isColumnModified(AlertPeer::TRI_UID)) $criteria->add(AlertPeer::TRI_UID, $this->tri_uid);

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
		$criteria = new Criteria(AlertPeer::DATABASE_NAME);

		$criteria->add(AlertPeer::ALT_UID, $this->alt_uid);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     string
	 */
	public function getPrimaryKey()
	{
		return $this->getAltUid();
	}

	/**
	 * Generic method to set the primary key (alt_uid column).
	 *
	 * @param      string $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setAltUid($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of Alert (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setProUid($this->pro_uid);

		$copyObj->setTasInitial($this->tas_initial);

		$copyObj->setTasFinal($this->tas_final);

		$copyObj->setAltType($this->alt_type);

		$copyObj->setAltDays($this->alt_days);

		$copyObj->setAltMaxAttempts($this->alt_max_attempts);

		$copyObj->setAltTemplate($this->alt_template);

		$copyObj->setAltDigest($this->alt_digest);

		$copyObj->setTriUid($this->tri_uid);


		$copyObj->setNew(true);

		$copyObj->setAltUid(''); // this is a pkey column, so set to default value

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
	 * @return     Alert Clone of current object.
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
	 * @return     AlertPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new AlertPeer();
		}
		return self::$peer;
	}

} // BaseAlert
