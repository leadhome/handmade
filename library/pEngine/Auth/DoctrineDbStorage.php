<?php
/**
 * Library Of Shared Code (LOSC)
 *
 * LICENSE
 *
 * This source file is subject to the New BSD License that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   LOSC Framework
 * @package    Session
 * @subpackage SaveHandlers
 * @copyright  Copyright (c) 2008 Robin Skoglund (http://robinsk.net/)
 * @license    http://creativecommons.org/licenses/BSD/  New BSD License
 */

/**
 * Session save handler using Doctrine for persistent storage
 *
 * @category   LOSC Framework
 * @package    Session
 * @subpackage SaveHandlers
 * @copyright  Copyright (c) 2008 Robin Skoglund (http://robinsk.net/)
 * @license    http://creativecommons.org/licenses/BSD/  New BSD License
 */
class pEngine_Auth_DoctrineDbStorage implements Zend_Session_SaveHandler_Interface
{
    const ID_COLUMN         = 'idColumn';
    const MODIFIED_COLUMN   = 'modifiedColumn';
    const LIFETIME_COLUMN   = 'lifetimeColumn';
    const DATA_COLUMN       = 'dataColumn';
	const USER_ID_COLUMN    = 'user_id';

    const LIFETIME          = 'lifetime';
    const OVERRIDE_LIFETIME = 'overrideLifetime';

    /**
     * Table/model name
     *
     * @var string
     */
    protected $_name;

    /**
     * Table instance
     *
     * @var Doctrine_Table
     */
    protected $_table;

    /**
     * Session table session id column
     *
     * @var array
     */
    protected $_idColumn = 'session_id';

	/**
     * Session table user id column
     *
     * @var array
     */
    protected $_userIdColumn = 'user_id';

    /**
     * Session table last modification time column
     *
     * @var string
     */
    protected $_modifiedColumn = 'modified';

    /**
     * Session table lifetime column
     *
     * @var string
     */
    protected $_lifetimeColumn = 'lifetime';

    /**
     * Session table data column
     *
     * @var string
     */
    protected $_dataColumn = 'data';

    /**
     * Session lifetime
     *
     * @var int
     */
    protected $_lifetime = false;

    /**
     * Whether or not the lifetime of an existing session should be overridden
     *
     * @var boolean
     */
    protected $_overrideLifetime = false;

    /**
     * Contains cached records
     *
     * @see _getRecord()
     * @var array
     */
    protected $_recordCache = array();

    /**
     * Constructor
     *
     * $config is an instance of Zend_Config or an array of key/value pairs,
     * containing configuration options for Losc_Session_SaveHandler_Doctrine:
     *
     * name             => (string) Name of session record class
     * idColumn         => (string) [optional] Session table session id column.
     *                     Defaults to 'session_id'.
	 * useridColumn		=> (int) [optional] Session table user id column.
     *                     Defaults to 'user_id'.
     * modifiedColumn   => (string) [optional] Session table last modification
     *                     time column. Defaults to 'modified'.
     * lifetimeColumn   => (string) [optional] Session table lifetime column.
     *                     Defaults to 'lifetime'.
     * dataColumn       => (string) [optional] Session table data column.
     *                     Defaults to 'data'.
     * lifetime         => (integer) [optional] Session lifetime. Defaults to
     *                     ini_get('session.gc_maxlifetime')
     * overrideLifetime => (boolean) [optional] Whether or not the lifetime of
     *                     an existing session should be overridden. Default
     *                     is false.
     *
     * @param  Zend_Config|array $config  user-provided configuration
     * @throws Zend_Session_SaveHandler_Exception
     */
    public function __construct($config)
    {
        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        } else if (!is_array($config)) {
            $msg = '$config must be an instance of Zend_Config or array of '
                 . 'key/value pairs containing configuration options for '
                 . __CLASS__;
            throw new Zend_Session_SaveHandler_Exception($msg);
        }

        // set options
        foreach ($config as $key => $value) {
            switch ($key) {
                case 'name':
                    $this->_name = (string) $value;
                    break;
                case self::ID_COLUMN:
                    $this->_idColumn = (string) $value;
                    break;
				case self::USER_ID_COLUMN:
					$this->_userIdColumn = (string) $value;
					break;
                case self::MODIFIED_COLUMN:
                    $this->_modifiedColumn = (string) $value;
                    break;
                case self::LIFETIME_COLUMN:
                    $this->_lifetimeColumn = (string) $value;
                    break;
                case self::DATA_COLUMN:
                    $this->_dataColumn = (string) $value;
                    break;
                case self::LIFETIME:
                    $this->setLifetime($value);
                    break;
                case self::OVERRIDE_LIFETIME:
                    $this->setOverrideLifetime($value);
                    break;
                default:
                    break;
            }
        }

        $this->_checkRequiredColumns();

        $this->_table = Doctrine::getTable($this->_name);
    }

    /**
     * Destructor
     *
     * @return void
     */
    public function __destruct()
    {
        Zend_Session::writeClose();
    }

    /**
     * Sets session lifetime and optionally whether or not the lifetime of an
     * existing session should be overridden
     *
     * $lifetime === false resets lifetime to session.gc_maxlifetime
     *
     * @param int  $lifetime         new lifetime
     * @param bool $overrideLifetime [optional] whether the lifetime of an
     *                               existing session should be overridden
     * @return void
     * @throws Zend_Session_SaveHandler_Exception  if $lifetime is less than 0
     */
    public function setLifetime($lifetime, $overrideLifetime = null)
    {
        if ($lifetime < 0) {
            $msg = sprintf('$lifetime is less than 0 (%s)', $lifetime);
            throw new Zend_Session_SaveHandler_Exception($msg);
        } else if (empty($lifetime)) {
            $this->_lifetime = (int) ini_get('session.gc_maxlifetime');
        } else {
            $this->_lifetime = (int) $lifetime;
        }


        if ($overrideLifetime != null) {
            $this->setOverrideLifetime($overrideLifetime);
        }

        return $this;
    }

    /**
     * Retrieves session lifetime
     *
     * @return int
     */
    public function getLifetime()
    {
        if (!$this->_lifetime) {
            $this->setLifetime(null);
        }

        return $this->_lifetime;
    }

    /**
     * Sets whether the lifetime of an existing session should be overridden
     *
     * @param  bool $overrideLifetime  override existing session lifetime
     * @return void
     */
    public function setOverrideLifetime($overrideLifetime)
    {
        $this->_overrideLifetime = (boolean) $overrideLifetime;
    }

    /**
     * Retrieve whether the lifetime of an existing session should be overridden
     *
     * @return bool  override existing session lifetime
     */
    public function getOverrideLifetime()
    {
        return $this->_overrideLifetime;
    }

    /**
     * Opens Session
     *
     * @param  string $save_path  ignored
     * @param  string $name       ignored
     * @return bool               always returns true
     */
    public function open($save_path, $name)
    {
        return true;
    }

    /**
     * Closes session
     *
     * @return boolean  always returns true
     */
    public function close()
    {
        return true;
    }

    /**
     * Reads session data for the given id
     *
     * @param  string $id  session id
     * @return string      serialized session data
     */
    public function read($id)
    {
        // try to retrieve the record with the given id
        if ($s = $this->_getRecord($id)) {
            if ($this->_getExpirationTime($s) > time()) {
                // get data
                return $s->{$this->_dataColumn};
            } else {
                // delete session if expired
                $s->delete();
                unset($this->_recordCache[$id]);
            }
        }

        return '';
    }

    /**
     * Writes session data for the given id
     *
     * @param  string $id    session id
     * @param  string $data  serialized data to write
     * @return bool          indicating success
     */
    public function write($id, $data)
    {
        // try to get session record
        if ($s = $this->_getRecord($id)) {
            // session record exists: update lifetime
            $lifetime = $this->_getLifetime($s);
            if ($s->get($this->_lifetimeColumn) != $lifetime) {
                $s->set($this->_lifetimeColumn, $lifetime);
            }
        } else {
            // session record does not exist: create new
            $s = new $this->_name();
            $s->set($this->_idColumn, $id);
            $s->set($this->_lifetimeColumn, $this->getLifetime());
        }
		$s->set($this->_userIdColumn, Zend_Auth::getInstance()->getIdentity()->id);
        // update modified and data
        $s->set($this->_modifiedColumn, time());
        $s->set($this->_dataColumn, $data);
        
		
//        if (Zend_Auth::getInstance()->hasIdentity())
		return $s->trySave();
    }

    /**
     * Destroys session with the given id
     *
     * @param  string $id  session id
     * @return bool        indicating success
     */
    public function destroy($id)
    {
        if ($record = $this->_getRecord($id)) {
            $record->delete();
            unset($this->_recordCache[$id]);
            return true;
        }

        return false;
    }

    /**
     * Garbage Collection method
     *
     * @param  int  $maxlifetime  ignored
     * @return true
     */
    public function gc($maxlifetime)
    {
        // get connection from table
        $conn = $this->_table->getConnection();

        // where clause: delete expired records
        $where = $conn->quoteIdentifier($this->_modifiedColumn) . ' + '
               . $conn->quoteIdentifier($this->_lifetimeColumn) . ' < '
               . $conn->quote(time());

        // execute query
        $deleted = Doctrine_Query::create()
                   ->delete()
                   ->from($this->_name)
                   ->where($where)
                   ->execute();

        // clear record cache
        $this->_recordCache = array();

        return true;
    }

    /**
     * Check for required session table columns
     *
     * @return void
     * @throws Zend_Session_SaveHandler_Exception
     */
    protected function _checkRequiredColumns()
    {
        $col = false;

        if ($this->_idColumn === null) {
            $key = self::ID_COLUMN;
            $col = 'session id';
        } else if ($this->_modifiedColumn === null) {
            $key = self::MODIFIED_COLUMN;
            $col = 'last modification time';
        } else if ($this->_lifetimeColumn === null) {
            $key = self::LIFETIME_COLUMN;
            $col = 'lifetime';
        } else if ($this->_dataColumn === null) {
            $key = self::DATA_COLUMN;
            $col = 'data';
        }

        if ($col) {
            $msg = "Configuration must define '%s' which '
                 . 'names the session table %s column";
            $msg = sprintf($msg, $key, $col);
            throw new Zend_Session_SaveHandler_Exception($msg);
        }
    }

    /**
     * Retrieves session lifetime for the given record
     *
     * @param  Doctrine_Record $record  record to retrieve lifetime from
     * @return int                      record lifetime
     */
    protected function _getLifetime(Doctrine_Record $record)
    {
        if (!$this->_overrideLifetime) {
            return (int) $record->get($this->_lifetimeColumn);
        }

        return $this->getLifetime();
    }


    /**
     * Retrieves session expiration time for the given record
     *
     * @param Doctrine_Record $record  doctrine record
     * @return int
     */
    protected function _getExpirationTime(Doctrine_Record $record)
    {
        return (int) $record->get($this->_modifiedColumn) +
               $this->_getLifetime($record);
    }

    /**
     * Retrieves the record matching $id, or false if it doesn't exist
     *
     * @param  string                $id  session id
     * @return Doctrine_Record|false
     */
    protected function _getRecord($id)
    {
        // check local record cache first
        if (isset($this->_recordCache[$id])) {
            return $this->_recordCache[$id];
        }

        // create query
        $query = Doctrine_Query::create()
                  ->from($this->_name)
                  ->addWhere($this->_idColumn . ' = ?', array($id));

        // force preDqlSelect to be called even if use_dql_callbacks is false
        if (!Doctrine_Manager::getInstance()->getAttribute('use_dql_callbacks')) {
            $record = $this->_table->getRecordInstance();
            if (method_exists($record, 'preDqlSelect')) {
                $record->preDqlSelect($query, array(
                    'table' => $this->_table,
                    'map'   => array()
                ), $this->_name);
            }
        }

        // good to go, execute query
        $record = $query->execute();

        if (count($record)) {
            // record found, store it in locale cache
            $this->_recordCache[$id] = $record[0];
            return $record[0];
        } else {
            // no record found
            return false;
        }
    }
}
?>