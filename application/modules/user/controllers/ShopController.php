<?php
class User_ShopController extends Zend_Controller_Action {
    public function init() {
        /* Initialize action controller here */
		$this->_helper->layout->setLayout('default');
		$this->_helper->AjaxContext()->addActionContext('ajaxuniquefield', 'json')->initContext('json');
		$this->_helper->AjaxContext()->addActionContext('create', 'json')->initContext('json');
		$this->_helper->AjaxContext()->addActionContext('edit', 'json')->initContext('json');
    }

    public function indexAction() {		
        // action body
    }
	//проверка на уникальность домена и названия магазина
	public function ajaxuniquefieldAction() {		
		$field = $this->getRequest()->field;
		$value = $this->getRequest()->value;
		if($field!='title' && $field!='domain') $this->view->error = 1;
		$validator = new Zend_Validate();
		if($field=='domain') $validator->addValidator(new Zend_Validate_Regex(array('pattern' => '/^[a-z0-9-]{1,10}$/i')),array('breakChainOnFailure' => true));
		$validator->addValidator(new Inc_Validator_CheckUnique(array('table'=>'User_Model_ShopTable','field'=>$field,'error'=>'Магазин с таким именем уже существует')));
		if ($validator->isValid($value)) $this->view->error = 0;
		else $this->view->error = $validator->getMessages();
	}
	//Редактирования магазина
	public function editAction() {
		$auth = Zend_Auth::getInstance();
		if(!$auth->hasIdentity()) {
			//редирект на авторизацию
			$this->_redirect("/user/index/login");
		}
		if(!User_Model_ShopTable::getInstance()->getShop($auth->getIdentity()->user_id)) $this->_redirect("/user/shop/edit");
		$this->view->headTitle('Редактирование магазина');
		$shop = User_Model_ShopTable::getInstance()->getShop($auth->getIdentity()->user_id);
		$data = $shop->toArray();
		$edit_shop_form = new User_Form_EditShop();
		foreach($shop->PaymentShop as $payment) {
			$data['payments'][] = $payment->payment_id;
		}
		foreach($shop->DeliveryShop as $delivery) {
			$data['delivery'][] = $delivery->delivery_id;
			$price_delivery[$delivery->delivery_id] =  $delivery->price_delivery;
		}
		$this->view->price_delivery = $price_delivery;
		$edit_shop_form->populate($data);
		$this->view->form = $edit_shop_form;
		// $this->view->form->populate = $shop ;
		// die();
	}
	//Создание магазина
    public function createAction() {
		$auth = Zend_Auth::getInstance();
		if(!$auth->hasIdentity()) {
			//редирект на авторизацию
			$this->_redirect("/user/index/login");
		}
		if(User_Model_ShopTable::getInstance()->getShop($auth->getIdentity()->user_id)) $this->_redirect("/user/shop/edit");
		
		$this->view->headTitle('Создание магазина');
		$create_shop_form = new User_Form_CreateShop();
		
		if($this->getRequest()->isPost()) {
			$form_data = $this->getRequest()->getPost();
			if($create_shop_form->isValid($form_data)) {
				$this->view->error = 0;
				if (!$this->getRequest()->isXmlHttpRequest()) {
					// запись в БД
					$auth = Zend_Auth::getInstance();
					$shop = User_Model_ShopTable::getInstance()->getRecord();				
					$shop->user_id = $auth->getIdentity()->user_id;
					$shop->title = $this->getRequest()->title;
					$shop->domain = $this->getRequest()->domain;
					$shop->about = $this->getRequest()->about;
					$shop->additional_payment_condition = $this->getRequest()->additional_payment_condition;
					$shop->additional_delivery_condition = $this->getRequest()->additional_delivery_condition;
					$shop->return = $this->getRequest()->return;
					$i = 0;
					foreach($this->getRequest()->delivery as $key=>$delivery) {
						$shop->DeliveryShop[$i]->delivery_id = $delivery;
						$shop->DeliveryShop[$i]->price_delivery = $this->getRequest()->price_delivery[$delivery];
						$i++;
					}
					$i = 0;
					foreach($this->getRequest()->payments as $key=>$payment) {
						$shop->PaymentShop[$i]->payment_id = $payment;
						$i++;
					}
					$shop->save();
					$this->_redirect("/");					
                }
			} else {
				if ($this->getRequest()->isXmlHttpRequest()) {
					$this->view->error = $create_shop_form->getMessages();
				} else {
					$this->view->form = $create_shop_form;
				}
			}
		} else {
			$this->view->form = $create_shop_form;
		}
    }
}

