<?php

class Inc_Validator_ShopName extends Zend_Validate_Abstract
{
	const EMAIL_EXISTS = 'Duplicate email';
	const EMAIL_IS_NOT_CORRECT = 'Email is not correct';

	protected $_messageTemplates = array(
		self::EMAIL_EXISTS => 'Пользователь с данным email уже зарегистрирован.',
		self::EMAIL_IS_NOT_CORRECT => 'Email введен не корректно'
	);
	public function __construct($unique=true) {
        $this->_unique = $unique;
        $this->_countError = 0;
    }
	public function isValid($value) {
		$this->_setValue($value);		
		//проверка на коррекность
		$s=filter_var($value, FILTER_VALIDATE_EMAIL);
		if(!empty($s)==false) {
			$this->_error(self::EMAIL_IS_NOT_CORRECT);
			 $this->_countError++;
		} else {
			//проверка на дубликат
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
		}
	}
}
