<?php
class Inc_Validator_Auth extends Zend_Validate_Abstract {
    const NOT_AUTH = 'notAuth';    
    protected $_messageTemplates = array(
        self::NOT_AUTH => 'Неверно введены имя пользователя или пароль'
    );
	protected $_contextKey;
    
    public function __construct($key) {
        $this->_contextKey = $key;
    }
    
    public function isValid($value, $context = null) {
		if (is_array($context)) {
			$email = $context[$this->_contextKey];
        } else $email = $context;
		
		if(!$email) {
			return false;
		}
		
		$user = User_Model_UserTable::getInstance()->getUser($email, $value);
		if($user) {
			return true;
		} 
		$this->_error(self::NOT_AUTH);
        return false;
    }	
}