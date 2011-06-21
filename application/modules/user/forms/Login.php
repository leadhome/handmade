<?php
class User_Form_Login extends Zend_Form {
	public function init() {
		$this->setAction('/user/index/login')
	         ->setMethod('post')
             ->setAttrib('id', 'user_form_auth');
		
		//Email
        $email = new Zend_Form_Element_Text('email');
		$email->setLabel('E-mail:')
			  ->setRequired(true)
	          ->setAttrib('id', 'user_form_auth_email')
	          ->addValidator(new Inc_Validator_UserEmail(false))			 
			  ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addFilter('StringToLower');
		
		//Пароль
		$password = new Zend_Form_Element_Password('password');
        $password->setLabel('Пароль:')
                 ->setRequired(true)
				 ->setAttrib('id', 'user_form_auth_password')
                 ->addValidator(new Zend_Validate_NotEmpty(),array('breakChainOnFailure' => true))
				 ->addValidator(new Inc_Validator_Auth('email'));
			
		//Submit
		$submit = new Zend_Form_Element_Submit('submit_validator');
		$submit->setLabel('Войти')
			   ->setAttrib('id', 'user_form_auth_submit');
		
		//Decorator
		$this->addElements(array($email, $password, $submit))
			 ->addDecorator('FormElements')
			 ->addDecorator(array('table' => 'HtmlTag'), array('tag' => 'table', 'class' => 'auth'))
			 ->addDecorator('Form')
		     ->addElementPrefixPath('Inc_Decorator',  'Inc/Decorator/', 'decorator')
			 ->setElementDecorators(array('ElementDecorator'));
	}
}
