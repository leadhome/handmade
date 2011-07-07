<?php
class User_ShopController extends Zend_Controller_Action {
    public function init() {
        /* Initialize action controller here */
		$this->_helper->layout->setLayout('default');
		$this->_helper->AjaxContext()->addActionContext('ajaxuniquefield', 'json')->initContext('json');
		$this->_helper->AjaxContext()->addActionContext('editshop', 'json')->initContext('json');
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
	

    public function editshopAction() {
		$auth = Zend_Auth::getInstance();
		if(!$auth->hasIdentity()) {
			//редирект на авторизацию
			$this->_redirect("/user/index/login");
		}
		$this->view->headTitle('Создание магазина');
		$edit_shop_form = new User_Form_EditShop();
		
		if($this->getRequest()->isPost()) {
			$form_data = $this->getRequest()->getPost();
			if($edit_shop_form->isValid($form_data)) {        
				$this->view->error = 0;
				if (!$this->getRequest()->isXmlHttpRequest()) {
					// запись в БД
					// $user = User_Model_UserTable::getInstance()->getRecord();
					// $user->email = $form_data["email"];
					// $user->password = md5($form_data["password"]);
					// $user->group_id = 1;
					// $user->tarif_id = 1;
					// $user->rating = 0;
					// $user->summ = 0;
					// $user->save();
					
                }
			} else {
				if ($this->getRequest()->isXmlHttpRequest()) {
					$this->view->error = $edit_shop_form->getMessages();
				} else {
					$this->view->form = $edit_shop_form;
				}
			}
		} else {
			$this->view->form = $edit_shop_form;
		}
    }
}

