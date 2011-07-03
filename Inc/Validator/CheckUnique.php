<?php

// class Inc_Validator_CheckUniqueField extends Zend_Validate_Abstract
class Inc_Validator_CheckUnique extends Zend_Validate_Abstract
{
	protected $_table = '';
	protected $_field = '';
	
	const DB_ = 'Duplicate email';
	const EMAIL_IS_NOT_CORRECT = 'Email is not correct';

	protected $_messageTemplates = array(
		self::EMAIL_EXISTS => 'Пользователь с данным email уже зарегистрирован.',
		self::EMAIL_IS_NOT_CORRECT => 'Email введен не корректно'
	);
	public function __construct($params = array()) {
		$this->_table = $params['table'];
		$this->_field = 'findOneBy'.ucfirst($params['field']);
		// $this->_error = $params['error'];
    }
	public function isValid($value) {
		$value = 'Республика Алтай';
		$this->_setValue($value);
		$field = $this->_field;
		$table = call_user_func(array($this->_table,'getInstance'));
		$row = $table->$field($value);
		if($row) {
			echo 'no';
		} else {
			echo 'yes';
		}
		die();
		// echo $this->_field;
		echo $value;
		// echo $table->$field($value);
		echo '<pre>';
			print_r($table->$field($value)->toArray());
		echo '</pre>';
		// $this->_field = 'findOneByTitle';
		// $table = call_user_func(array($this->_table,'getInstance'));
		// $field = call_user_func(array($table,$this->_field));
		// $table->$field($value);
		
		// echo '<pre>';
			// print_r(call_user_func(array($field,toArray)));
		// echo '</pre>';
		// $this->_table::getInstance()->findOneByTitle($value);
		// echo $this->_table;
		// echo $this->_field;
		// echo $this->_error;
		die();
		
/*		
		// проверка на коррекность
		$s=filter_var($value, FILTER_VALIDATE_EMAIL);
		if(!empty($s)==false) {
			$this->_error(self::EMAIL_IS_NOT_CORRECT);
			 $this->_countError++;
		} else {
			// проверка на дубликат
			if($this->_unique==true) {
				$user = User_Model_UserTable::getInstance()->findOneByEmail($value);
				if($user) {
					$this->_error(self::EMAIL_EXISTS);
					$this->_countError++;
				}
			}
		}
		
		if($this->_countError==0) {
			return true;
		} else {
			return false;
		}*/
	}
}
