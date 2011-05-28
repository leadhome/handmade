<?

class pEngine_Validator_Db_Record extends Zend_Validate_Db_Abstract
{
	protected $_id = null;
	/**
	* Returns the number of lines found in the database, in which the field value is equal to $value
	* @param string $value 
	* @return int
	*/
	protected function _query($value)
	{
		if ($this->_id) {
			$check = Doctrine_Query::create()
				->addFrom($this->_table)
				->addWhere($this->_field.' = ?', $value)
				->addWhere('id != ?', $this->_id)
				->count();
		} else {
			$check = Doctrine_Query::create()
				->addFrom($this->_table)
				->addWhere($this->_field.' = ?', $value)
				->count();
		}
		return $check;
	}
	
	
	/**
	* Verifies the existence of rows in the database, which value is $value
	* @param string $value 
	* @return boolean
	*/
    public function isValid($value)
    {
        $valid = true;
		
        $result = $this->_query($value);
        if ($result>0) {
            $valid = false;
            $this->_error(self::ERROR_RECORD_FOUND);
        } 
        return $valid;
    }
	public function __construct($options)
    {
		
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (func_num_args() > 1) {
            $options       = func_get_args();
            $temp['table'] = array_shift($options);
            $temp['field'] = array_shift($options);
			$temp['id'] = array_shift($options);
            if (!empty($options)) {
                $temp['exclude'] = array_shift($options);
            }

            if (!empty($options)) {
                $temp['adapter'] = array_shift($options);
            }

            $options = $temp;
        }

        if (!array_key_exists('table', $options) && !array_key_exists('schema', $options)) {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception('Table or Schema option missing!');
        }

        if (!array_key_exists('field', $options)) {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception('Field option missing!');
        }

        if (array_key_exists('adapter', $options)) {
            $this->setAdapter($options['adapter']);
        }

        if (array_key_exists('exclude', $options)) {
            $this->setExclude($options['exclude']);
        }

        $this->setField($options['field']);
        if (array_key_exists('table', $options)) {
            $this->setTable($options['table']);
        }

        if (array_key_exists('schema', $options)) {
            $this->setSchema($options['schema']);
        }
		if (array_key_exists('id', $options)) {
            $this->_id=($options['id']);
        }
    }
}