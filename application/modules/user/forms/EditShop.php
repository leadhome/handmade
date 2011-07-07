<?php
class User_Form_EditShop extends Zend_Form {
	public function init() {
		$this->setAction('/user/shop/editshop')
	         ->setMethod('post')
             ->setAttrib('id', 'user_form_editshop');
		
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
		$payment_lists = User_Model_PaymentTable::getInstance()->findAll();
		$payments = new Zend_Form_Element_MultiCheckbox('payments');
		$payments->setMultiOptions($payment_lists->toKeyValueArray('payment_id', 'title'))
				 ->setLabel('Выберите способ оплаты для вашего магазина:')
				 ->setRequired(true)
				 ->setAttrib('id', $this->getAttrib('id').'_payments')
				 ->setValue($payment_lists->toKeyValueArray('title', 'payment_id'))
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
		$delivery_lists = User_Model_DeliveryTable::getInstance()->findAll();
		$delivery = new Zend_Form_Element_MultiCheckbox('delivery');

		$delivery->setMultiOptions($delivery_lists->toKeyValueArray('delivery_id', 'title'))
				 ->setLabel('Выберите способ доставки для вашего магазина:')
				 ->setRequired(true)
				 ->setAttrib('id', $this->getAttrib('id').'_delivery')
				 ->setValue($delivery_lists->toKeyValueArray('title', 'delivery_id'))
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
			  ->setRequired(true)
			  ->setAttrib('id',$this->getAttrib('id').'_about')
			  ->setAttrib('cols','2')
			  ->setAttrib('rows','3')
			  ->addFilter('StripTags')
			  ->addFilter('StringTrim')
			  ->setDecorators(array('ViewHelper'));
									  
		//Информация об условиях возврата
		$return = new Zend_Form_Element_Textarea('return');
		$return->setLabel('Укажите условия возврата')
		       ->setRequired(true)
			   ->setAttrib('id',$this->getAttrib('id').'_return')
			   ->setAttrib('cols','2')
			   ->setAttrib('rows','3')
			   ->addFilter('StripTags')
			   ->addFilter('StringTrim')
			   ->setDecorators(array('ViewHelper'));
			  
			  
		$submit = new Zend_Form_Element_Submit('submit_validator');
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
