<?php
class Inc_Validator_EqualValues extends Zend_Validate_Abstract
{
    const NOT_EQUAL = 'notEqual';
    protected $_messageTemplates = array(
        self::NOT_EQUAL => 'Пароль и потверждение пароля не совпадают'
    );
    protected $_contextKey;
    
    public function __construct($key) {
        $this->_contextKey = $key;
    }
    
    public function isValid($value, $context = null) {
        if (is_array($context)) {
            if (isset($context[$this->_contextKey]) && ($value === $context[$this->_contextKey])) {
                return true;
            }
        }
        if ($value === $context) {
            return true;
        }
        $this->_error(self::NOT_EQUAL);
        return false;
    }
}