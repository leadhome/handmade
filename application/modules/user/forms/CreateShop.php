<?php
class User_Form_CreateShop extends Zend_Form {
	public function init() {
		$this->setAction('user/shop/createshop')
	         ->setMethod('post')
             ->setAttrib('id', 'user_form_createshop');
				
		//Название магазина
        $shopname = new Zend_Form_Element_Text('shopname');
		$shopname->setLabel('Название магазина:')
				 ->setRequired(true)
	             ->setAttrib('id', 'user_form_createshop_shopname')
	             ->addValidator(new Inc_Validator_ShopName())			 
			     ->addFilter('StripTags')
			     ->addFilter('StringTrim');
		
		$payments = User_Model_PaymentTable::getInstance()->findAll();
		
		
		$payments = new Zend_Form_Element_MultiCheckbox('payments');
		$payments->setMultiOptions(User_Model_PaymentTable::getInstance()->fetchAll())
				 ->setRequired(true)
				 ->setLabel('Выберите способ оплаты для вашего магазина:');
		
		//Decorator
		$this->addElements(array($shopname, $payments));
	}
}
