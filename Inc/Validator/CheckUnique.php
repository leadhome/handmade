<?php
class Inc_Validator_CheckUnique extends Zend_Validate_Abstract {
	protected $_table = '';
	protected $_field = '';
	
	const ERROR_MESSAGE = 'Error message';
	
	protected $_messageTemplates = array(
		self::ERROR_MESSAGE => ''
	);
	public function __construct($params = array()) {
		$this->_table = $params['table'];
		$this->_field = 'findOneBy'.ucfirst($params['field']);
		$this->_messageTemplates = array(self::ERROR_MESSAGE => $params['error']);
    }
	public function isValid($value) {
		$this->_setValue($value);
		$field = $this->_field;
		$table = call_user_func(array($this->_table,'getInstance'));
		$row = $table->$field($value);

		if($row) {
			$this->_error(self::ERROR_MESSAGE);
			return false;
		} else {
			return true;
		}
	}
}
