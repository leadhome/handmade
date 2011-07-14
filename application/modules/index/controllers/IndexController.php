<?php

class Index_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
		$this->_helper->AjaxContext()->addActionContext('upload', 'json')->initContext('json');
    }

    public function indexAction()
    {
        // action body
		
    }
	public function headerAction() {
		
		$auth = Zend_Auth::getInstance();
		$locationUser = new Zend_Session_Namespace('locationUser');
		
		//определение местоположения пользователя
		if(!$auth->hasIdentity()) {
			if(!$locationUser->city_id) {
				$geoIp = Inc_Geo_Ip::getInstance();
				$infoIp = $geoIp->getInfo($_SERVER['REMOTE_ADDR']);
				$user = User_Model_CityTable::getInstance()->getCity($infoIp);
				
				foreach($user as $key=>$value) {
					$locationUser->$key = $value;
				}
			}
		} 
		$this->view->user = $auth->getIdentity();
		$this->view->locationUser = $locationUser;
		$this->view->cart = new Zend_Session_Namespace('cart');
	}
	public function uploadAction() {
		if (!$this->getRequest()->isXmlHttpRequest()) throw new Zend_Controller_Action_Exception('Доступ к upload только через ajax');
		$upload = new Inc_Upload();
		ini_set('display_errors', 1);
		
		header('Pragma: no-cache');
		header('Cache-Control: private, no-cache');
		header('Content-Disposition: inline; filename="files.json"');

		switch ($_SERVER['REQUEST_METHOD']) {
			case 'HEAD':
			case 'GET':
				echo $this->_helper->json($upload->get());
				break;
			case 'POST':
				echo $this->_helper->json($upload->post());
				break;
			case 'DELETE':
				echo $this->_helper->json($upload->delete());
				break;
			default:
				header('HTTP/1.0 405 Method Not Allowed');
		}
	}
}

