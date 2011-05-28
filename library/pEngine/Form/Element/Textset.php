<?php

require_once 'Zend/Form/Element/Xhtml.php';

/** @see Zend_Filter */
require_once 'Zend/Filter.php';

/** @see Zend_Form */
require_once 'Zend/Form.php';

/** @see Zend_Validate_Interface */
require_once 'Zend/Validate/Interface.php';

/** @see Zend_Validate_Abstract */
require_once 'Zend/Validate/Abstract.php';

/**
 * Textset form element.
 *
 * This will render a set of text fields.
 * The values and names of theese fields put in parameter array.
 */

class pEngine_Form_Element_Textset extends Zend_Form_Element_Xhtml
{
	/**
	 * Default form view helper to use for rendering
	 * @var string
	 */
	public $helper = 'formTextset';

	public $name;

	/**
	 * Массив обязательных полей
	 *
	 * @example $_required_fields[0] = 1;
	 * @var array int
	 */
	protected $_required_fields;

	/**
	 * Массив валидаторов для всех полей
	 *
	 * $_validators[field][] = validator
	 * @var array
	 */
	protected $_validators;

	public function __construct($name, array $req_fields = null) 
	{
		$this->name = $name;
		if(isset($req_fields)) {
			$this->_required_fields = $req_fields;
		} else {
			$this->_required_fields = array();
		}

		parent::__construct($name);
	}

	/**
	 * Добавить валидатор к полю $index
	 *
	 * @param int $index
	 * @param validator
	 */
	public function addValidatorForField($index, $validator)
	{
		if ($validator instanceof Zend_Validate_Interface) {
			$name = get_class($validator);
		} elseif(is_string($validator)) {
			$name = $validator;
			$validator = array(
					'validator' => $validator,
					'breakChainOnFailure' => true,
					'options'             => array(),
					);
		} else {
			require_once 'Zend/Form/Exception.php';
			throw new Zend_Form_Exception('Invalid validator provided to addValidator; must be string or Zend_Validate_Interface');
		}
		$this->_validators[$index][$name] = $validator;

		return $this;
	}

	/**
	 * Установка обязательных полей
	 * @param array int
	 */
	public function setRequiredFields(array $fields)
	{
		if(isset($fields) && is_array($fields)) {
			foreach($fields as $f) {
				$this->setRequiredField($f);
			}
		}
		return $this;
	}

	/**
	 * Установка одного поля
	 * @param int
	 */
	public function setRequiredField($index)
	{
		$this->_required_fields[$index] = true;
		return $this;
	}

	/**
	 * Проверка, обязательное ли поле
	 *
	 * @param int
	 */
	public function isRequiredField($index)
	{
		if(isset($this->_required_fields[$index])) {
			return $this->_required_fields[$index] == true;
		} else {
			return false;
		}
	}

	/**
	 * Получить валидаторы для нужного поля
	 *
	 * @param int
	 * @return array
	 */
	public function getValidatorsForField($index)
	{
		$validators = array();
		if(isset($this->_validators[$index]) && is_array($this->_validators[$index])) {
			foreach ($this->_validators[$index] as $key => $value) {
				if ($value instanceof Zend_Validate_Interface) {
					$validators[$key] = $value;
					continue;
				}
				$validator = $this->_loadValidator($value);
				$validators[get_class($validator)] = $validator;
			}
		}
		return $validators;
	}

	/**
	 * Retrieve a single validator by name
	 *
	 * @param  string $name
	 * @return Zend_Validate_Interface|false False if not found, validator otherwise
	 */
	public function getValidatorForField($index, $name)
	{
		if (!isset($this->_validators[$name])) {
			$len = strlen($name);
			foreach ($this->_validators as $localName => $validator) {
				if ($len > strlen($localName)) {
					continue;
				}
				if (0 === substr_compare($localName, $name, -$len, $len, true)) {
					if (is_array($validator)) {
						return $this->_loadValidator($validator);
					}
					return $validator;
				}
			}
			return false;
		}

		if (is_array($this->_validators[$name])) {
			return $this->_loadValidator($this->_validators[$name]);
		}

		return $this->_validators[$name];
	}

	/**
	 * Проверка валидности данных
	 */
	public function isValid($values, $context = null)
	{
		if(is_array($values)) {
			foreach($values as $key => $val) {
				if((!isset($val) || empty($val)) && !$this->isRequiredField($key)) {
					return true;
				}

				if($this->isRequiredField($key) && !$this->getValidatorForField($key, 'NotEmpty')) {
					$not_empty = new Zend_Validate_NotEmpty();
					if(!isset($this->_validators[$key])) {
						$this->_validators[$key] = array();
					}
					array_unshift($this->_validators[$key], $not_empty);
				}

				if (Zend_Validate_Abstract::hasDefaultTranslator() &&
						!Zend_Form::hasDefaultTranslator())
				{
					$translator = Zend_Validate_Abstract::getDefaultTranslator();
					if ($this->hasTranslator()) {
						$translator = $this->getTranslator();
					}
				} else {
					$translator = $this->getTranslator();
				}

				$result = true;
				$validators = $this->getValidatorsForField($key);
				foreach($validators as $key => $validator) {
					if (method_exists($validator, 'setTranslator')) {
						if (method_exists($validator, 'hasTranslator')) {
							if (!$validator->hasTranslator()) {
								$validator->setTranslator($translator);
							}
						} else {
							$validator->setTranslator($translator);
						}
					}

					if (method_exists($validator, 'setDisableTranslator')) {
						$validator->setDisableTranslator($this->translatorIsDisabled());
					}

					if($validator->isValid($val, $context)) {
						continue;
					} else {
						$result = false;
						$this->_messages = array_merge($this->_messages, $validator->getMessages());
						$this->markAsError();
					}
					break;
				}	

				if($result == false) {
					break;
				}
			}
		}

		return $result;
	}
}
