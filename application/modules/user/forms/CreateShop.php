<?php
class User_Form_CreateShop extends Zend_Form {
	public function init() {
		$helper = new Inc_Helper_ConvertArray();
		$this->setAction('/user/shop/createshop')
	         ->setMethod('post')
             ->setAttrib('id', 'user_form_createshop');
		
		//Название магазина
        $title = new Zend_Form_Element_Text('title');
		$title->setLabel('Название магазина:')
				 ->setRequired(true)
	             ->setAttrib('id', $this->getAttrib('id').'_title')
	             ->setAttrib('class', 'input_create_shop')
				 ->addValidator(new Zend_Validate_NotEmpty(),array('breakChainOnFailure' => true))
				 ->addValidator(new Inc_Validator_CheckUnique(array('table'=>'User_Model_ShopTable','field'=>'title','error'=>'Магазин с таким именем уже существует')))				 
			     ->addFilter('StripTags')
			     ->addFilter('StringTrim')
				 ->setDecorators(array('ViewHelper'));
				 
		//Домен
        $domain = new Zend_Form_Element_Text('domain');
		$domain->setLabel('Введите ваш домен вашего магазина:')
				 ->setRequired(true)
	             ->setAttrib('id', $this->getAttrib('id').'_domain')
				 ->setAttrib('class', 'input_create_shop')
				 ->addValidator(new Zend_Validate_NotEmpty(),array('breakChainOnFailure' => true))
				 ->addValidator(new Inc_Validator_CheckUnique(array('table'=>'User_Model_ShopTable','field'=>'domain','error'=>'Магазин с таким доменом уже существует')))			 
			     ->addFilter('StripTags')
			     ->addFilter('StringTrim')
				 ->setDecorators(array('ViewHelper'));
		
		//Способы оплаты
		$payment_lists = User_Model_PaymentTable::getInstance()->findAll()->toArray();
		$payments = new Zend_Form_Element_MultiCheckbox('payments');

		$payments->setMultiOptions($helper->getArray($payment_lists,array('fields'=>array('title'),'key'=>'payment_id')))
				 ->setLabel('Выберите способ оплаты для вашего магазина:')
				 ->setRequired(true)
				 ->setAttrib('id', $this->getAttrib('id').'_payments')
				 ->setValue($helper->getArray($payment_lists,array('fields'=>array('payment_id'))))
				 ->addFilter('StripTags')
			     ->addFilter('StringTrim')
				 ->setDecorators(array('ViewHelper'));
				 
		//Дополнительная информация о способах оплаты
		$additional_payment_condition = new Zend_Form_Element_Textarea('additional_payment_condition');
		$additional_payment_condition->setLabel('Дополнительная информация')
									 ->setAttrib('id',$this->getAttrib('id').'_additional_payment_condition')
									 ->setAttrib('cols','2')
									 ->setAttrib('rows','3')
									 ->addFilter('StripTags')
									 ->addFilter('StringTrim')
									 ->setDecorators(array('ViewHelper'));
		
		//Способы доставки
		$delivery_lists = User_Model_DeliveryTable::getInstance()->findAll()->toArray();
		$delivery = new Zend_Form_Element_MultiCheckbox('delivery');

		$delivery->setMultiOptions($helper->getArray($delivery_lists,array('fields'=>array('title'),'key'=>'delivery_id')))
				 ->setLabel('Выберите способ доставки для вашего магазина:')
				 ->setRequired(true)
				 ->setAttrib('id', $this->getAttrib('id').'_delivery')
				 ->setValue($helper->getArray($delivery_lists,array('fields'=>array('delivery_id'))))
				 ->addFilter('StripTags')
			     ->addFilter('StringTrim')
				 ->setDecorators(array('ViewHelper'));
				 
		//Дополнительная информация о способах доставки
		$additional_delivery_condition = new Zend_Form_Element_Textarea('additional_delivery_condition');
		$additional_delivery_condition->setLabel('Дополнительная информация')
									  ->setAttrib('id',$this->getAttrib('id').'_additional_delivery_condition')
									  ->setAttrib('cols','2')
									  ->setAttrib('rows','3')
									  ->addFilter('StripTags')
									  ->addFilter('StringTrim')
									  ->setDecorators(array('ViewHelper'));
		
		//Информация о магазине
		$about = new Zend_Form_Element_Textarea('about');
		$about->setLabel('Информация о магазине')
			  ->setAttrib('id',$this->getAttrib('id').'_about')
			  ->setAttrib('cols','2')
			  ->setAttrib('rows','3')
			  ->addFilter('StripTags')
			  ->addFilter('StringTrim')
			  ->setDecorators(array('ViewHelper'));
									  
		//Информация об условиях возврата
		$return = new Zend_Form_Element_Textarea('return');
		$return->setLabel('Укажите условия возврата')
			   ->setAttrib('id',$this->getAttrib('id').'_return')
			   ->setAttrib('cols','2')
			   ->setAttrib('rows','3')
			   ->addFilter('StripTags')
			   ->addFilter('StringTrim')
			   ->setDecorators(array('ViewHelper'));
			  
			  
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Создать')
			   ->setAttrib('id', $this->getAttrib('id').'_submit')
			   ->setDecorators(array('ViewHelper'));
		
		
		$this->addElements(array(
									$title, 
									$domain,
									$payments,
									$delivery,
									$additional_payment_condition,
									$additional_delivery_condition,
									$about,
									$return,
									$submit,
								));
	}
}
