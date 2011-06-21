<?php
class User_ShopController extends Zend_Controller_Action {
    public function init() {
        /* Initialize action controller here */
		$this->_helper->layout->setLayout('default');
    }

    public function indexAction() {		
        // action body
		//$this->_helper->AjaxContext()->addActionContext('ajax-handler', 'json')->registrationContext('json');
    }
	
	
	//Регистрация
    public function createshopAction() {
		$auth = Zend_Auth::getInstance();
		if(!$auth->hasIdentity()) {
			//редирект на авторизацию
			$this->_redirect("/user/index/login");
		}
		$this->view->headTitle('Создание магазина');
		$create_shop_form = new User_Form_CreateShop();
		
		
		$this->view->form = $create_shop_form;
		
		
		
    }
}

