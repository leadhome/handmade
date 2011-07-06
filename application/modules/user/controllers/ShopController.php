<?php
class User_ShopController extends Zend_Controller_Action {
    public function init() {
        /* Initialize action controller here */
		$this->_helper->layout->setLayout('default');
		$this->_helper->AjaxContext()->addActionContext('ajaxuniquefield', 'json')->initContext('json');
    }

    public function indexAction() {		
        // action body
    }
	
	public function ajaxuniquefieldAction() {		
		$field = $this->getRequest()->field;
		$value = $this->getRequest()->value;
	
		if($field!='title' && $field!='domain') $this->view->error = 1;
		
		$validator = new Zend_Validate();
		$validator->addValidator(new Inc_Validator_CheckUnique(array('table'=>'User_Model_ShopTable','field'=>$field,'error'=>'Магазин с таким именем уже существует')));
		if ($validator->isValid($value)) $this->view->error = 0;
		else $this->view->error = $validator->getMessages();
		// $row = User_Model_ShopTable::getInstance()->findByDql($field.' = ? ',$value)->toArray();
	}
	

    public function createshopAction() {
		$auth = Zend_Auth::getInstance();
		if(!$auth->hasIdentity()) {
			//редирект на авторизацию
			$this->_redirect("/user/index/login");
		}
		$this->view->headTitle('Создание магазина');
		$create_shop_form = new User_Form_CreateShop();
		
		if($this->getRequest()->isPost()) {
			$form_data = $this->getRequest()->getPost();
			if($create_shop_form->isValid($form_data)) {  
			
			}
		}
		// if($_POST) {
			// echo '<pre>';
				// print_r($_POST);
			// echo '</pre>';
			// die();
			
		// }
		$this->view->form = $create_shop_form;
		
		
		
    }
}

