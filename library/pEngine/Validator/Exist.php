<?php

/**
 * Simple exists validator.
 * Checks uniquness of value in this field of this model.
 *
 * @author Vladimir Loginov
 */
class pEngine_Validator_Exist extends Zend_Validate_Abstract
{
	const MSG_EXIST = 'msgExist';

	public $_table;
	public $_field;

	protected $_messageTemplates = array(
		self::MSG_EXIST => "'%value%' already exist. Try another value."
		);

	public function  __construct($_table, $_field)
	{
		$this->_table = $_table;
		$this->_field = $_field;
	}

	protected function exists($value)
	{
		$count = Doctrine_Query::create()
			->from($this->_table)
			->where($this->_field.' = ?', $value)
			->count();

		return $count;
	}

	public function isValid($value)
    {
        $this->_setValue($value);
        if($this->exists($value)){
            $this->_error(self::MSG_EXIST);
            return false;
        }
        return true;
    }
}
