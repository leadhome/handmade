<?php
class User_IndexController extends Zend_Controller_Action {
    public function init() {
        /* Initialize action controller here */
		$this->_helper->layout->setLayout('default');
		$this->_helper->AjaxContext()->addActionContext('registration', 'json')->initContext('json');
		$this->_helper->AjaxContext()->addActionContext('login', 'json')->initContext('json');
    }

    public function indexAction() {		
		// action body
    }
	
	
	//Регистрация
    public function registrationAction(){
		$this->view->headTitle('Регистрация');
		$auth = Zend_Auth::getInstance();
		
		if($auth->hasIdentity()) {
			//редирект на редактирование профиля
			$this->_redirect("/");
		}
		
		$registration_form = new User_Form_Registration();		
	
		if($this->getRequest()->isPost()) {
			$form_data = $this->getRequest()->getPost();
			if($registration_form->isValid($form_data)) {        
				$this->view->error = 0;
				if (!$this->getRequest()->isXmlHttpRequest()) {
					// запись в БД
					$user = User_Model_UserTable::getInstance()->getRecord();
					$user->email = $form_data["email"];
					$user->password = md5($form_data["password"]);
					$user->group_id = 1;
					$user->tarif_id = 1;
					$user->rating = 0;
					$user->summ = 0;
					$user->save();
					
					// Отправка письма
					$mail = new Zend_Mail('UTF-8');
					$mail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);
					$mail->addTo($form_data["email"], '');
					$mail->setSubject('Быстрая регистрация на портале'.$this->getRequest()->getHttpHost());
					$mail->setBodyHtml('<html>
											<head>
												<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
											</head>
											<body>
												Здравствуйте!<br/></br>
												Текст.<br/><br/>
											</body>
										</html>');
					$mail->send();
					
					// Авторизация пользователя
					$adapter = new Inc_Auth_UserAdapter($form_data["email"], $form_data["password"]); 
					$result = $auth->authenticate($adapter);
					if($result->isValid()) {
						// редирект на страницу с который пользователь пришел
						$this->_redirect('/');
					}
                }
			} else {
				if ($this->getRequest()->isXmlHttpRequest()) {
					$this->view->error = $registration_form->getMessages();
				} else {
					$this->view->form = $registration_form;
				}
			}
		} else {
			$this->view->form = $registration_form;
		}		
    }

	
	//Авторизация
    public function loginAction() {
		$this->view->headTitle('Авторизация');
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()) {
			//редирект на редактирование профиля
			$this->_redirect("/");
		}
        
		$auth_form = new User_Form_Login();
		
		if($this->getRequest()->isPost()) {
			$form_data = $this->getRequest()->getPost();
			if($auth_form->isValid($form_data)) {
				$this->view->error = 0;
				if (!$this->getRequest()->isXmlHttpRequest()) {
					$adapter = new Inc_Auth_UserAdapter($form_data["email"], $form_data["password"]); 
					$result = $auth->authenticate($adapter);
					if($result->isValid()) {
						// редирект на страницу с который пользователь пришел
						$this->_redirect('/');
					}
				}				
			} else {
				if ($this->getRequest()->isXmlHttpRequest()) {
					$this->view->error = $auth_form->getMessages();
				} else {
					$this->view->form = $auth_form;
				}
			}
		} else {
			$this->view->form = $auth_form;
		}
    }
	
	
	//Выход
    public function logoutAction() {
		Zend_Auth::getInstance()->clearIdentity();
		// редирект на страницу с который пользователь пришел
		$this->_redirect('/');
    }
}

