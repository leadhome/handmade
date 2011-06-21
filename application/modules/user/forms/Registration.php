<?php
class User_Form_Registration extends Zend_Form {
	public function init() {
		$this->setAction('/user/index/registration')
	         ->setMethod('post')
             ->setAttrib('id', 'user_form_registration');
				
		//Email
        $email = new Zend_Form_Element_Text('email');
		$email->setLabel('E-mail:')
			  ->setRequired(true)
	          ->setAttrib('id', 'user_form_registration_email')
	          ->addValidator(new Inc_Validator_UserEmail())			 
			  ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addFilter('StringToLower');
		
		//Пароль
		$password = new Zend_Form_Element_Password('password');
        $password->setLabel('Пароль:')
                 ->setRequired(true)
				 ->setDescription('Пароль должен состоять не менее чем из 6<br/>символов, но не более чем из 20')
				 ->setAttrib('id', 'user_form_registration_password')
                 ->addValidator(new Zend_Validate_StringLength(6, 20));
		
        //Потверждение пароля
        $password_confirm = new Zend_Form_Element_Password('password_confirm');
        $password_confirm->setLabel('Потверждение пароля:')
						 ->setRequired(true)				 
		                 ->setAttrib('id', 'user_form_registration_password_confirm')
					     ->addValidator(new Inc_Validator_EqualValues('password'))
                         ->setAllowEmpty(false);		 
        
		//Submit
		$submit = new Zend_Form_Element_Submit('submit_validator');
		$submit->setLabel('Регистрация')
			   ->setAttrib('id', 'user_form_registration_submit');
		
		//Decorator
		$this->addElements(array($email, $password, $password_confirm, $submit))
			 ->addDecorator('FormElements')
			 ->addDecorator(array('table' => 'HtmlTag'), array('tag' => 'table', 'class' => 'registration'))
			 ->addDecorator('Form')
		     ->addElementPrefixPath('Inc_Decorator',  'Inc/Decorator/', 'decorator')
			 ->setElementDecorators(array('ElementDecorator'));
	}
}
